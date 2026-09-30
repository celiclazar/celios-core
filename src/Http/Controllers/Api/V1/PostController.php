<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\PostResource;
use Celios\Core\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * List published posts with search, category filtering, and pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $locale = app()->getLocale();
        $search = $request->input('search');
        $categorySlug = $request->input('category');
        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $query = Post::query()
            ->published()
            ->with(['category', 'author', 'featuredImage'])
            ->when($search, function ($q, $search) use ($locale) {
                $q->where(function ($sub) use ($search, $locale) {
                    $sub->where("title->{$locale}", 'like', "%{$search}%")
                        ->orWhere("excerpt->{$locale}", 'like', "%{$search}%")
                        ->orWhere("body->{$locale}", 'like', "%{$search}%");
                });
            })
            ->when($categorySlug, function ($q, $categorySlug) use ($locale) {
                $q->whereHas('category', function ($sub) use ($categorySlug, $locale) {
                    $sub->where("slug->{$locale}", $categorySlug);
                });
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC');

        $posts = $query->paginate($perPage)->withQueryString();

        return PostResource::collection($posts);
    }

    /**
     * Show single post by slug or ID.
     */
    public function show(string $slugOrId): PostResource
    {
        $locale = app()->getLocale();

        $post = Post::query()
            ->published()
            ->with(['category', 'author', 'featuredImage'])
            ->where(function ($query) use ($slugOrId, $locale) {
                $query->where("slug->{$locale}", $slugOrId)
                    ->orWhere('id', is_numeric($slugOrId) ? (int) $slugOrId : 0);
            })
            ->firstOrFail();

        // Increment views count silently
        $post->increment('views_count');

        return new PostResource($post);
    }
}
