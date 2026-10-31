<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\MediaResource;
use Celios\Core\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * List media library items with pagination (Admin).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = $request->input('search');
        $perPage = min(max($request->integer('per_page', 20), 1), 50);

        $media = Media::query()
            ->when($search, fn ($q, $search) => $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('alt', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            }))
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return MediaResource::collection($media);
    }

    /**
     * Upload a new file into the media library (Admin).
     */
    public function store(Request $request): MediaResource
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480'],
            'alt' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'caption' => ['nullable', 'string'],
        ]);

        $file = $request->file('file');
        $disk = config('curator.disk', 'public');
        $directory = config('curator.directory', 'media');

        $path = $file->store($directory, $disk);
        $dimensions = @getimagesize($file->getRealPath());

        $media = Media::create([
            'disk' => $disk,
            'directory' => $directory,
            'visibility' => 'public',
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => $path,
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'size' => $file->getSize(),
            'type' => $file->getMimeType(),
            'ext' => strtolower($file->getClientOriginalExtension()),
            'alt' => $validated['alt'] ?? null,
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'caption' => $validated['caption'] ?? null,
        ]);

        return new MediaResource($media);
    }

    /**
     * Get single media item details (Admin).
     */
    public function show(Media $media): MediaResource
    {
        return new MediaResource($media);
    }

    /**
     * Delete a media item (Admin).
     */
    public function destroy(Media $media): JsonResponse
    {
        if ($media->path && $media->disk) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();

        return response()->json([
            'message' => 'Media deleted successfully.',
        ]);
    }
}
