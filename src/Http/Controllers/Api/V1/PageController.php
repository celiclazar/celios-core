<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\PageResource;
use Celios\Core\Models\Page;

class PageController extends Controller
{
    /**
     * Show published page by slug.
     */
    public function show(string $slug): PageResource
    {
        $locale = app()->getLocale();

        $page = Page::query()
            ->where('is_visible', true)
            ->where(function ($query) use ($slug, $locale) {
                $query->where("slug->{$locale}", $slug)
                    ->orWhere('slug', $slug)
                    ->orWhere('type', $slug);
            })
            ->firstOrFail();

        return new PageResource($page);
    }
}
