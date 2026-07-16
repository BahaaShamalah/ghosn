<?php

namespace App\Http\Controllers\Admin\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Media\StoreMediaRequest;
use App\Models\Media;
use App\Services\Media\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService $media,
    ) {}

    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $search = $request->string('q')->trim()->toString();

        $query = Media::query()->latest();

        if ($type === 'image') {
            $query->where('mime_type', 'like', 'image/%');
        } elseif ($type === 'video') {
            $query->where('mime_type', 'like', 'video/%');
        } elseif ($type === 'document') {
            $query->whereIn('mime_type', ['application/pdf']);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('original_filename', 'like', "%{$search}%")
                    ->orWhere('filename', 'like', "%{$search}%");
            });
        }

        return view('admin.media.index', [
            'mediaItems' => $query->paginate(24)->withQueryString(),
            'filters' => [
                'type' => $type,
                'q' => $search,
            ],
            'maxUploadMb' => round(config('media.max_upload_kb', 10240) / 1024, 1),
            'allowedTypes' => implode(', ', $this->media->allowedExtensions()),
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        try {
            $this->media->upload($request->file('file'));
        } catch (RuntimeException $exception) {
            return back()->withErrors(['file' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.media.index')
            ->with('status', __('admin.media.uploaded'));
    }

    public function destroy(Media $medium): RedirectResponse
    {
        try {
            $this->media->delete($medium);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['media' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.media.index')
            ->with('status', __('admin.media.deleted'));
    }
}
