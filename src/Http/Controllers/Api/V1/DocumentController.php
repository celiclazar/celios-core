<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\DocumentResource;
use Celios\Core\Models\Document;
use Celios\Core\Models\DocumentDownloadLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * List published documents accessible by current user or guest.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $locale = app()->getLocale();
        $search = $request->input('search');
        $categorySlug = $request->input('category');
        $perPage = min(max($request->integer('per_page', 12), 1), 50);

        $query = Document::query()
            ->published()
            ->visibleToUser($request->user('sanctum'))
            ->with('category')
            ->when($search, function ($q, $search) use ($locale) {
                $q->where(function ($sub) use ($search, $locale) {
                    $sub->where("title->{$locale}", 'like', "%{$search}%")
                        ->orWhere("description->{$locale}", 'like', "%{$search}%")
                        ->orWhere('file_name', 'like', "%{$search}%");
                });
            })
            ->when($categorySlug, function ($q, $categorySlug) use ($locale) {
                $q->whereHas('category', function ($sub) use ($categorySlug, $locale) {
                    $sub->where("slug->{$locale}", $categorySlug);
                });
            })
            ->orderBy('created_at', 'desc');

        $documents = $query->paginate($perPage)->withQueryString();

        return DocumentResource::collection($documents);
    }

    /**
     * Show document metadata.
     */
    public function show(Request $request, string $slugOrId): DocumentResource
    {
        $locale = app()->getLocale();
        $user = $request->user('sanctum');

        $document = Document::query()
            ->published()
            ->visibleToUser($user)
            ->with('category')
            ->where(function ($query) use ($slugOrId, $locale) {
                $query->where("slug->{$locale}", $slugOrId)
                    ->orWhere('id', is_numeric($slugOrId) ? (int) $slugOrId : 0);
            })
            ->firstOrFail();

        return new DocumentResource($document);
    }

    /**
     * Download the file and track download analytics.
     */
    public function download(Request $request, Document $document): StreamedResponse|JsonResponse
    {
        $user = $request->user('sanctum');

        if (! $document->is_published || ! $document->canAccess($user)) {
            return response()->json([
                'message' => 'Unauthorized or document unavailable.',
            ], 403);
        }

        $disk = Storage::disk('documents');
        if (! $disk->exists($document->file_path)) {
            return response()->json([
                'message' => 'File not found on storage disk.',
            ], 404);
        }

        // Increment downloads count
        $document->increment('downloads_count');

        // Audit log
        DocumentDownloadLog::create([
            'document_id' => $document->id,
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'locale' => app()->getLocale(),
            'downloaded_at' => now(),
        ]);

        $fileName = $document->file_name ?: basename($document->file_path);

        return $disk->download($document->file_path, $fileName);
    }

    /**
     * Stream inline preview of the document file.
     */
    public function preview(Request $request, Document $document): StreamedResponse|JsonResponse
    {
        $user = $request->user('sanctum');

        if (! $document->is_published || ! $document->canAccess($user)) {
            return response()->json([
                'message' => 'Unauthorized or document unavailable.',
            ], 403);
        }

        $disk = Storage::disk('documents');
        if (! $disk->exists($document->file_path)) {
            return response()->json([
                'message' => 'File not found on storage disk.',
            ], 404);
        }

        $fileName = $document->file_name ?: basename($document->file_path);

        return $disk->response($document->file_path, $fileName, [
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Upload and create a new document (Admin).
     */
    public function store(Request $request): DocumentResource
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:document_categories,id'],
            'title' => ['required', 'array'],
            'slug' => ['required', 'array'],
            'description' => ['nullable', 'array'],
            'file' => ['required_without:file_path', 'file', 'max:51200'],
            'file_path' => ['required_without:file', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'access_level' => ['nullable', 'string', 'in:public,authenticated,role_based'],
            'allowed_roles' => ['nullable', 'array'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $validated['file_path'] = $uploaded->store('uploads/' . date('Y/m'), 'documents');
            $validated['file_name'] = $uploaded->getClientOriginalName();
            $validated['file_size'] = $uploaded->getSize();
            $validated['file_type'] = strtolower($uploaded->getClientOriginalExtension());
        }

        unset($validated['file']);
        $validated['uploaded_by'] = $request->user()?->id;

        $document = Document::create($validated);

        return new DocumentResource($document->load('category'));
    }

    /**
     * Update an existing document (Admin).
     */
    public function update(Request $request, Document $document): DocumentResource
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:document_categories,id'],
            'title' => ['sometimes', 'required', 'array'],
            'slug' => ['sometimes', 'required', 'array'],
            'description' => ['nullable', 'array'],
            'version' => ['nullable', 'string', 'max:50'],
            'access_level' => ['nullable', 'string', 'in:public,authenticated,role_based'],
            'allowed_roles' => ['nullable', 'array'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $document->update($validated);

        return new DocumentResource($document->load('category'));
    }

    /**
     * Delete a document (Admin).
     */
    public function destroy(Document $document): JsonResponse
    {
        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully.',
        ]);
    }
}
