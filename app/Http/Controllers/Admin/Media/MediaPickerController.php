<?php

namespace App\Http\Controllers\Admin\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Media\StoreMediaRequest;
use App\Models\Media;
use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MediaPickerController extends Controller
{
    public function __construct(
        private readonly MediaService $media,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = trim($request->string('q')->toString());
        $type = $request->string('type', 'image')->toString();

        $query = Media::query()->latest();

        if ($type === 'video') {
            $query->where('mime_type', 'like', 'video/%');
        } elseif ($type === 'image' || $request->boolean('images_only', true)) {
            $query->where('mime_type', 'like', 'image/%');
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('original_filename', 'like', "%{$search}%")
                    ->orWhere('filename', 'like', "%{$search}%");
            });
        }

        $items = $query
            ->limit(60)
            ->get()
            ->map(fn (Media $medium) => $medium->toPickerArray())
            ->values();

        return response()->json(['data' => $items]);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        try {
            $uploaded = $this->media->upload($request->file('file'));
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['media' => $uploaded->toPickerArray()], 201);
    }
}
