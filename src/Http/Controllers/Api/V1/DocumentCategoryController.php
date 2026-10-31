<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\DocumentCategoryResource;
use Celios\Core\Http\Resources\V1\DocumentResource;
use Celios\Core\Models\DocumentCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentCategoryController extends Controller
{
    /**
     * List active document categories tree.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = DocumentCategory::query()
            ->active()
            ->public()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->active()->public()])
            ->withCount(['documents' => fn ($q) => $q->published()])
            ->orderBy('order')
            ->get();

        return DocumentCategoryResource::collection($categories);
    }

    /**
     * Show a specific document category and its paginated documents.
     */
    public function show(Request $request, string $slugOrId): JsonResponse
    {
        $locale = app()->getLocale();
        $defaultLocale = config('locales.default', 'sr');
        $perPage = min(max($request->integer('per_page', 12), 1), 50);

        $category = DocumentCategory::query()
            ->active()
            ->withCount(['documents' => fn ($q) => $q->published()])
            ->where(function ($q) use ($slugOrId, $locale, $defaultLocale) {
                $q->where("slug->{$locale}", $slugOrId)
                    ->orWhere("slug->{$defaultLocale}", $slugOrId)
                    ->orWhere('id', is_numeric($slugOrId) ? (int) $slugOrId : 0);
            })
            ->firstOrFail();

        $user = $request->user('sanctum');

        if ($category->is_internal) {
            if (! $user || ! ($user->can('View:Document') || $user->hasAnyRole(['super_admin', 'super-admin', 'admin', 'panel_user']))) {
                return response()->json([
                    'message' => 'Unauthorized access to internal document category.',
                ], 403);
            }
        }

        $documents = $category->documents()
            ->published()
            ->visibleToUser($user)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'category' => new DocumentCategoryResource($category),
            'documents' => DocumentResource::collection($documents)->response()->getData(true),
        ]);
    }

    /**
     * Create a new document category (Admin).
     */
    public function store(Request $request): DocumentCategoryResource
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:document_categories,id'],
            'title' => ['required', 'array'],
            'slug' => ['required', 'array'],
            'description' => ['nullable', 'array'],
            'icon' => ['nullable', 'string', 'max:100'],
            'order' => ['integer'],
            'is_active' => ['boolean'],
            'is_internal' => ['boolean'],
        ]);

        $category = DocumentCategory::create($validated);

        return new DocumentCategoryResource($category);
    }

    /**
     * Update an existing document category (Admin).
     */
    public function update(Request $request, DocumentCategory $documentCategory): DocumentCategoryResource
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:document_categories,id'],
            'title' => ['sometimes', 'required', 'array'],
            'slug' => ['sometimes', 'required', 'array'],
            'description' => ['nullable', 'array'],
            'icon' => ['nullable', 'string', 'max:100'],
            'order' => ['integer'],
            'is_active' => ['boolean'],
            'is_internal' => ['boolean'],
        ]);

        $documentCategory->update($validated);

        return new DocumentCategoryResource($documentCategory);
    }

    /**
     * Delete a document category (Admin).
     */
    public function destroy(DocumentCategory $documentCategory): JsonResponse
    {
        $documentCategory->delete();

        return response()->json([
            'message' => 'Document category deleted successfully.',
        ]);
    }
}
