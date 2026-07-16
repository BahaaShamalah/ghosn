<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'width',
        'height',
        'thumbnail_path',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function url(): string
    {
        if ($this->disk === 'public') {
            return '/storage/'.ltrim(str_replace('\\', '/', $this->path), '/');
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    public function thumbnailUrl(): ?string
    {
        if (! $this->thumbnail_path) {
            return $this->isImage() ? $this->url() : null;
        }

        if ($this->disk === 'public') {
            return '/storage/'.ltrim(str_replace('\\', '/', $this->thumbnail_path), '/');
        }

        return Storage::disk($this->disk)->url($this->thumbnail_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isSvg(): bool
    {
        return $this->mime_type === 'image/svg+xml';
    }

    public function humanSize(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPickerArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url(),
            'thumbnail_url' => $this->thumbnailUrl() ?? $this->url(),
            'original_filename' => $this->original_filename,
            'mime_type' => $this->mime_type,
            'is_image' => $this->isImage(),
            'is_video' => str_starts_with($this->mime_type, 'video/'),
        ];
    }
}
