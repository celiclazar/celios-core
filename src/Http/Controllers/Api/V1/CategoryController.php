<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\CategoryResource;
use Celios\Core\Http\Resources\V1\PostResource;
use Celios\Core\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * List all visible categories with post counts.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->visible()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('order')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Show category details along with its published posts.
     */
    public function show(Request $request, string $slugOrId): JsonResponse
    {
        $locale = app()->getLocale();
        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $category = Category::query()
            ->visible()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->where(function ($query) use ($slugOrId, $locale) {
                $query->where("slug->{$locale}", $slugOrId)
                    ->orWhere('id', is_numeric($slugOrId) ? (int) $slugOrId : 0);
            })
            ->firstOrFail();

        $posts = $category->posts()
            ->published()
            ->with(['author', 'featuredImage'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'category' => new CategoryResource($category),
            'posts' => PostResource::collection($posts)->response()->getData(true),
        ]);
    }
}
