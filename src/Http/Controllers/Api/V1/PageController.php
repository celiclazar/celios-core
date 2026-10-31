<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Enums\PageType;
use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\PageResource;
use Celios\Core\Http\Resources\V1\PageRevisionResource;
use Celios\Core\Models\Page;
use Celios\Core\Models\PageRevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    /**
     * List all visible CMS pages.
     */
    public function index(): AnonymousResourceCollection
    {
        $pages = Page::query()
            ->where('is_visible', true)
            ->orderBy('id')
            ->get();

        return PageResource::collection($pages);
    }

    /**
     * Get the designated Homepage directly.
     */
    public function home(): PageResource
    {
        $locale = app()->getLocale();

        $page = Page::query()
            ->where('is_visible', true)
            ->where(function ($query) use ($locale) {
                $query->where('type', PageType::HOME->value)
                    ->orWhere("slug->{$locale}", 'home');
            })
            ->firstOrFail();

        return new PageResource($page);
    }

    /**
     * Show published page by slug or ID.
     */
    public function show(string $slug): PageResource
    {
        $locale = app()->getLocale();

        $page = Page::query()
            ->where('is_visible', true)
            ->where(function ($query) use ($slug, $locale) {
                $query->where("slug->{$locale}", $slug)
                    ->orWhere('slug', $slug)
                    ->orWhere('type', $slug)
                    ->orWhere('id', is_numeric($slug) ? (int) $slug : 0);
            })
            ->firstOrFail();

        return new PageResource($page);
    }

    /**
     * Create a new CMS page (Admin).
     */
    public function store(Request $request): PageResource
    {
        $validated = $request->validate([
            'title' => ['required', 'array'],
            'slug' => ['required', 'array'],
            'type' => ['nullable', 'string'],
            'content' => ['nullable', 'array'],
            'draft_content' => ['nullable', 'array'],
            'is_visible' => ['boolean'],
        ]);

        $page = Page::create($validated);

        return new PageResource($page);
    }

    /**
     * Update an existing CMS page (Admin).
     */
    public function update(Request $request, Page $page): PageResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'array'],
            'slug' => ['sometimes', 'required', 'array'],
            'type' => ['nullable', 'string'],
            'content' => ['nullable', 'array'],
            'draft_content' => ['nullable', 'array'],
            'is_visible' => ['boolean'],
        ]);

        $page->update($validated);

        return new PageResource($page);
    }

    /**
     * Delete a CMS page (Admin).
     */
    public function destroy(Page $page): JsonResponse
    {
        if ($page->isSystem()) {
            return response()->json([
                'message' => 'System and legal pages cannot be deleted.',
            ], 422);
        }

        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully.',
        ]);
    }

    /**
     * Save visual builder draft blocks for a page (Admin).
     */
    public function saveDraft(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'blocks' => ['present', 'array'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $page->update([
            'draft_content' => $validated['blocks'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully.',
            'updated_at' => $page->fresh()->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Publish draft content to live page and create revision snapshot (Admin).
     */
    public function publish(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'blocks' => ['nullable', 'array'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if (isset($validated['blocks'])) {
            $page->draft_content = $validated['blocks'];
            $page->save();
        }

        $published = $page->publish(
            userId: $request->user()?->id,
            note: $validated['note'] ?? 'Published via API'
        );

        return response()->json([
            'success' => $published,
            'message' => $published ? 'Page successfully published.' : 'No draft content to publish.',
            'page' => new PageResource($page->fresh()),
        ]);
    }

    /**
     * List revision history for a page (Admin).
     */
    public function revisions(Page $page): AnonymousResourceCollection
    {
        $revisions = $page->revisions()->with('user')->get();

        return PageRevisionResource::collection($revisions);
    }

    /**
     * Restore a specific page revision into draft_content (Admin).
     */
    public function restoreRevision(Page $page, PageRevision $revision): JsonResponse
    {
        abort_unless($revision->page_id === $page->id, 404);

        $page->restoreRevision($revision);

        return response()->json([
            'message' => 'Revision restored to draft successfully.',
            'page' => new PageResource($page->fresh()),
        ]);
    }
}
