<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\MenuItemResource;
use Celios\Core\Http\Resources\V1\MenuResource;
use Celios\Core\Models\Menu;
use Celios\Core\Models\MenuItem;
use Celios\Core\Services\MenuResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function __construct(
        protected MenuResolverService $menuResolver
    ) {}

    /**
     * List all registered navigation menus.
     */
    public function index(): AnonymousResourceCollection
    {
        $menus = Menu::query()
            ->withCount('menuItems')
            ->orderBy('name')
            ->get();

        return MenuResource::collection($menus);
    }

    /**
     * Get resolved navigation menu tree by key.
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

    /**
     * Create a new navigation menu (Admin).
     */
    public function store(Request $request): MenuResource
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100', 'unique:menus,key'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $menu = Menu::create([
            'key' => strtolower($validated['key']),
            'name' => $validated['name'],
        ]);

        return new MenuResource($menu);
    }

    /**
     * Update a navigation menu (Admin).
     */
    public function update(Request $request, Menu $menu): MenuResource
    {
        $validated = $request->validate([
            'key' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('menus', 'key')->ignore($menu->id)],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        if (isset($validated['key'])) {
            $validated['key'] = strtolower($validated['key']);
        }

        $menu->update($validated);
        $this->menuResolver->clearCache($menu->key);

        return new MenuResource($menu);
    }

    /**
     * Delete a navigation menu (Admin).
     */
    public function destroy(Menu $menu): JsonResponse
    {
        $key = $menu->key;
        $menu->delete();
        $this->menuResolver->clearCache($key);

        return response()->json([
            'message' => 'Menu deleted successfully.',
        ]);
    }

    /**
     * Add a menu item to a menu (Admin).
     */
    public function storeItem(Request $request, Menu $menu): MenuItemResource
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'order' => ['integer'],
            'title' => ['required', 'array'],
            'custom_url' => ['nullable', 'array'],
            'linkable_type' => ['nullable', 'string'],
            'linkable_id' => ['nullable', 'integer'],
            'target' => ['nullable', 'string', 'in:_self,_blank'],
            'is_visible' => ['boolean'],
        ]);

        $item = $menu->menuItems()->create($validated);

        return new MenuItemResource($item);
    }

    /**
     * Update a menu item (Admin).
     */
    public function updateItem(Request $request, Menu $menu, MenuItem $item): MenuItemResource
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'order' => ['integer'],
            'title' => ['sometimes', 'required', 'array'],
            'custom_url' => ['nullable', 'array'],
            'linkable_type' => ['nullable', 'string'],
            'linkable_id' => ['nullable', 'integer'],
            'target' => ['nullable', 'string', 'in:_self,_blank'],
            'is_visible' => ['boolean'],
        ]);

        $item->update($validated);

        return new MenuItemResource($item);
    }

    /**
     * Delete a menu item (Admin).
     */
    public function destroyItem(Menu $menu, MenuItem $item): JsonResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $item->delete();

        return response()->json([
            'message' => 'Menu item deleted successfully.',
        ]);
    }
}
