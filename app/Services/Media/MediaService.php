<?php

namespace App\Services\Media;

use App\Models\Campaign;
use App\Models\Media;
use App\Models\PageSectionBlock;
use App\Models\PageSection;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MediaService
{
    /**
     * @return array<int, string>
     */
    public function allowedExtensions(): array
    {
        return array_keys(config('media.allowed_mimes', []));
    }

    /**
     * @return array<int, string>
     */
    public function allowedMimeTypes(): array
    {
        return array_values(array_unique(config('media.allowed_mimes', [])));
    }

    public function maxUploadBytes(): int
    {
        return (int) config('media.max_upload_kb', 10240) * 1024;
    }

    public function upload(UploadedFile $file): Media
    {
        $this->assertAllowed($file);

        $disk = config('media.disk', 'public');
        $directory = config('media.directory', 'media');
        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid()->toString().'.'.$extension;
        $path = $file->storeAs($directory, $storedName, $disk);

        [$width, $height] = $this->resolveDimensions($file, $file->getMimeType() ?? '');

        $media = Media::query()->create([
            'disk' => $disk,
            'path' => $path,
            'filename' => $storedName,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize() ?: 0,
            'width' => $width,
            'height' => $height,
            'user_id' => Auth::id(),
        ]);

        if ($media->isImage() && ! $media->isSvg()) {
            $thumbnail = $this->createThumbnail($media);

            if ($thumbnail) {
                $media->update(['thumbnail_path' => $thumbnail]);
            }
        }

        return $media->fresh();
    }

    public function delete(Media $media): void
    {
        if ($this->isInUse($media)) {
            throw new RuntimeException(__('admin.media.in_use'));
        }

        $disk = Storage::disk($media->disk);

        if ($disk->exists($media->path)) {
            $disk->delete($media->path);
        }

        if ($media->thumbnail_path && $disk->exists($media->thumbnail_path)) {
            $disk->delete($media->thumbnail_path);
        }

        $media->delete();
    }

    public function isInUse(Media $media): bool
    {
        $logoKeys = ['site.logo_media_id', 'site.favicon_media_id'];

        foreach ($logoKeys as $key) {
            $value = Setting::query()->where('key', $key)->value('value');

            if ((int) $value === $media->id) {
                return true;
            }
        }

        return PageSectionBlock::query()
            ->where(function ($query) use ($media) {
                $query->where('content->media_id', $media->id)
                    ->orWhere('content->en->media_id', $media->id)
                    ->orWhere('content->ar->media_id', $media->id);
            })
            ->exists()
            || PageSection::query()
                ->where(function ($query) use ($media) {
                    $query->where('settings->content->background_media_id', $media->id)
                        ->orWhere('settings->content->image_media_id', $media->id)
                        ->orWhere('settings->content->video_media_id', $media->id)
                        ->orWhere('settings->content->video_cover_media_id', $media->id);
                })
                ->exists()
            || Campaign::query()
                ->where('featured_image_media_id', $media->id)
                ->orWhere('video_media_id', $media->id)
                ->exists();
    }

    private function assertAllowed(UploadedFile $file): void
    {
        $mime = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, $this->allowedExtensions(), true)) {
            throw new RuntimeException(__('admin.media.invalid_type'));
        }

        if ($mime && ! in_array($mime, $this->allowedMimeTypes(), true)) {
            throw new RuntimeException(__('admin.media.invalid_mime'));
        }

        if (($file->getSize() ?: 0) > $this->maxUploadBytes()) {
            throw new RuntimeException(__('admin.media.too_large'));
        }
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function resolveDimensions(UploadedFile $file, string $mime): array
    {
        if (! str_starts_with($mime, 'image/') || $mime === 'image/svg+xml') {
            return [null, null];
        }

        $size = @getimagesize($file->getRealPath());

        if ($size === false) {
            return [null, null];
        }

        return [$size[0] ?? null, $size[1] ?? null];
    }

    private function createThumbnail(Media $media): ?string
    {
        if (! extension_loaded('gd') || ! config('media.thumbnail.enabled', true)) {
            return null;
        }

        $disk = Storage::disk($media->disk);
        $sourcePath = $disk->path($media->path);

        if (! is_readable($sourcePath)) {
            return null;
        }

        $image = match ($media->mime_type) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default => false,
        };

        if ($image === false) {
            return null;
        }

        $srcWidth = imagesx($image);
        $srcHeight = imagesy($image);
        $maxWidth = (int) config('media.thumbnail.max_width', 400);
        $maxHeight = (int) config('media.thumbnail.max_height', 400);
        $ratio = min($maxWidth / max($srcWidth, 1), $maxHeight / max($srcHeight, 1), 1);
        $targetWidth = (int) max(1, round($srcWidth * $ratio));
        $targetHeight = (int) max(1, round($srcHeight * $ratio));

        $thumb = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($media->mime_type === 'image/png') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcWidth, $srcHeight);

        $thumbDir = config('media.thumbnail.directory', 'media/thumbnails');
        $thumbName = 'thumb_'.$media->filename;
        $relativePath = $thumbDir.'/'.$thumbName;
        $absolutePath = $disk->path($relativePath);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        $saved = match ($media->mime_type) {
            'image/jpeg' => imagejpeg($thumb, $absolutePath, 85),
            'image/png' => imagepng($thumb, $absolutePath),
            'image/webp' => function_exists('imagewebp') ? imagewebp($thumb, $absolutePath, 85) : false,
            default => false,
        };

        imagedestroy($image);
        imagedestroy($thumb);

        return $saved ? $relativePath : null;
    }
}
