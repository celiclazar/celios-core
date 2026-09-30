<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Services\MenuResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        protected MenuResolverService $menuResolver
    ) {}

    /**
     * Get menu tree by key.
     */
    public function show(Request $request, string $key): JsonResponse
    {
        $locale = app()->getLocale();
        $items = $this->menuResolver->getMenu($key, $locale);

        return response()->json([
            'key' => strtolower($key),
            'locale' => $locale,
            'items' => $items,
        ]);
    }
}
