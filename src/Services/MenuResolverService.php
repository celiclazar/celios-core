<?php

namespace Celios\Core\Services;

use Celios\Core\Models\Menu;
use Celios\Core\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MenuResolverService
{
    /**
     * Vraća meni iz keša (ili baze ako keš ne postoji) u vidu stabla (sa ugnježdenim 'children')
     */
    public function getMenu(string $key, ?string $locale = null): Collection
    {
        $key = Str::lower($key);
        $locale = $locale ?? App::getLocale();
        $cacheKey = "navigation_menu_{$key}_{$locale}";

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return collect($this->buildTree($cached));
            }
        }

        $menu = Menu::query()
            ->where(function ($query) use ($key) {
                $wrappedKey = $query->getGrammar()->wrap('key');
                $query->where('key', $key)
                    ->orWhereRaw("LOWER({$wrappedKey}) = ?", [$key]);
            })
            ->with(['menuItems' => function ($query) {
                $query->where('is_visible', true)
                    ->with('linkable')
                    ->orderBy('order', 'asc');
            }])
            ->first();

        if (! $menu) {
            return collect([]);
        }

        $data = $menu->menuItems->map(function (MenuItem $item) use ($locale) {
            return [
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'target' => $item->target ?? '_self',
                'title' => $this->resolveTitle($item, $locale),
                'url' => $this->resolveUrl($item, $locale),
            ];
        })->toArray();

        Cache::put($cacheKey, $data, now()->addDay());

        return collect($this->buildTree($data));
    }

    /**
     * Resolves a menu assigned to a specific theme location/slot (e.g. 'primary', 'footer').
     * If no menu is explicitly assigned, falls back to legacy matching keys ('header', 'footer').
     */
    public function getMenuForLocation(string $location, ?string $locale = null): Collection
    {
        $location = Str::lower($location);
        $locale = $locale ?? App::getLocale();
        $cacheKey = "navigation_menu_location_{$location}_{$locale}";

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return collect($this->buildTree($cached));
            }
        }

        $locations = setting('theme_menu_locations', []);
        if (is_string($locations)) {
            $locations = json_decode($locations, true) ?: [];
        }

        $menuId = $locations[$location] ?? null;
        $menu = null;

        if ($menuId) {
            $menu = Menu::where('id', $menuId)
                ->with(['menuItems' => function ($query) {
                    $query->where('is_visible', true)
                        ->with('linkable')
                        ->orderBy('order', 'asc');
                }])
                ->first();
        }

        // Backward compatibility fallback: check legacy system keys
        if (! $menu) {
            $fallbackKey = match ($location) {
                'primary', 'header_navigation' => 'header',
                default => $location,
            };

            return $this->getMenu($fallbackKey, $locale);
        }

        $data = $menu->menuItems->map(function (MenuItem $item) use ($locale) {
            return [
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'target' => $item->target ?? '_self',
                'title' => $this->resolveTitle($item, $locale),
                'url' => $this->resolveUrl($item, $locale),
            ];
        })->toArray();

        Cache::put($cacheKey, $data, now()->addDay());

        return collect($this->buildTree($data));
    }

    /**
     * Rekurzivno formira hijerarhijsko stablo stavki
     */
    public function buildTree(array $items, ?int $parentId = null): array
    {
        $branch = [];

        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {
                $children = $this->buildTree($items, $item['id']);
                $item['children'] = $children;
                $branch[] = $item;
            }
        }

        return $branch;
    }

    /**
     * Briše keš za određeni meni na svim jezicima
     */
    public function clearCache(string $key): void
    {
        $key = Str::lower($key);
        $availableLocales = array_unique(array_filter(array_merge(
            array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano'])),
            [config('locales.default', 'sr'), 'sr', 'en', 'it', App::getLocale()]
        )));

        foreach ($availableLocales as $locale) {
            Cache::forget("navigation_menu_{$key}_{$locale}");
        }

        Cache::forget("navigation_menu_{$key}");
    }

    /**
     * Briše keš za specifičnu lokaciju menija na svim jezicima
     */
    public function clearLocationCache(string $location): void
    {
        $location = Str::lower($location);
        $availableLocales = array_unique(array_filter(array_merge(
            array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano'])),
            [config('locales.default', 'sr'), 'sr', 'en', 'it', App::getLocale()]
        )));

        foreach ($availableLocales as $locale) {
            Cache::forget("navigation_menu_location_{$location}_{$locale}");
        }

        Cache::forget("navigation_menu_location_{$location}");
    }

    /**
     * Briše keš za sve poznate lokacije menija
     */
    public function clearAllLocationCaches(): void
    {
        $locations = ['primary', 'header', 'footer', 'footer_main', 'footer_legal', 'mobile', 'sidebar'];
        foreach ($locations as $loc) {
            $this->clearLocationCache($loc);
        }
    }

    private function resolveTitle(MenuItem $item, string $locale): string
    {
        return $item->displayTitle($locale);
    }

    private function resolveUrl(MenuItem $item, string $locale): string
    {
        if ($item->linkable) {
            if (method_exists($item->linkable, 'getUrl')) {
                return $item->linkable->getUrl($locale);
            }

            $slug = $item->linkable->getTranslation('slug', $locale, false);

            if (blank($slug)) {
                $defaultLocale = config('locales.default', 'sr');
                $slug = $item->linkable->getTranslation('slug', $defaultLocale, false);
            }

            return filled($slug) ? url("/{$locale}/".ltrim($slug, '/')) : url("/{$locale}");
        }

        $customUrl = $item->getTranslation('custom_url', $locale, false);

        if (blank($customUrl)) {
            $defaultLocale = config('locales.default', 'sr');
            $customUrl = $item->getTranslation('custom_url', $defaultLocale, false);
        }

        if (blank($customUrl)) {
            return '#';
        }

        return $this->formatCustomUrl($customUrl, $locale);
    }

    /**
     * Formats a custom URL into a safe, absolute or protocol-qualified URL
     * so that browsers never append it as a relative segment to current nested pages/categories.
     */
    public function formatCustomUrl(string $url, ?string $locale = null): string
    {
        $url = trim($url);

        if ($url === '' || $url === '#') {
            return '#';
        }

        // Anchor links within page (e.g. #contact)
        if (str_starts_with($url, '#')) {
            return $url;
        }

        // Block dangerous scripting and data action schemes
        if (preg_match('/^(javascript:|data:|vbscript:)/i', trim($url))) {
            return '#';
        }

        // Action schemes (mailto:, tel:)
        if (preg_match('/^(mailto:|tel:)/i', $url)) {
            return $url;
        }

        // Absolute protocol URLs (http://, https://, //)
        if (preg_match('/^(https?:)?\/\//i', $url)) {
            return $url;
        }

        // URLs starting with www.
        if (str_starts_with($url, 'www.')) {
            return 'https://'.$url;
        }

        // External domain names without protocol (e.g. example.com, google.com/test)
        if (preg_match('/^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}(\/.*)?$/', $url)) {
            return 'https://'.$url;
        }

        // Absolute path from root (e.g. /sr/contact or /about)
        if (str_starts_with($url, '/')) {
            return url($url);
        }

        // Check if relative path already begins with an available locale (e.g. "sr/kontakt")
        $locales = array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']));
        $segments = explode('/', $url);
        $firstSegment = strtolower($segments[0]);

        if (in_array($firstSegment, $locales)) {
            return url('/'.$url);
        }

        // Internal relative slug/path: prepend locale to create absolute URL
        $locale = $locale ?? app()->getLocale();

        return url("/{$locale}/".ltrim($url, '/'));
    }
}
