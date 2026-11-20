<?php

namespace Celios\Core\Services;

use Celios\Core\Contracts\ThemeInterface;
use Celios\Core\Models\Setting;
use Celios\Core\Themes\DefaultTheme;
use Celios\Core\Themes\JsonTheme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    /**
     * @var array<string, ThemeInterface>
     */
    protected array $registeredThemes = [];

    protected ?ThemeInterface $activeTheme = null;

    protected bool $booted = false;

    public function __construct()
    {
        // 1. Always register the default built-in theme
        $default = new DefaultTheme();
        $this->register($default);

        // 2. Discover local themes from themes/ directory in app root
        $this->discoverLocalThemes();
    }

    /**
     * Register a theme instance explicitly (e.g. from a package ServiceProvider).
     */
    public function register(ThemeInterface $theme): self
    {
        $this->registeredThemes[$theme->getId()] = $theme;

        return $this;
    }

    /**
     * Unregisters a theme instance by ID.
     */
    public function unregister(string $themeId): self
    {
        unset($this->registeredThemes[$themeId]);

        return $this;
    }

    /**
     * Discover all themes having a theme.json in base_path('themes').
     */
    public function discoverLocalThemes(): void
    {
        $themesDir = base_path('themes');

        if (! File::isDirectory($themesDir)) {
            return;
        }

        $directories = File::directories($themesDir);

        foreach ($directories as $dir) {
            if (File::exists($dir . '/theme.json')) {
                try {
                    $theme = new JsonTheme($dir);
                    $this->register($theme);
                } catch (\Throwable $e) {
                    // Ignore corrupted or invalid theme folders gracefully
                    report($e);
                }
            }
        }
    }

    /**
     * Returns all discovered themes.
     *
     * @return Collection<string, ThemeInterface>
     */
    public function getAvailableThemes(): Collection
    {
        return collect($this->registeredThemes);
    }

    /**
     * Returns the currently active theme.
     */
    public function getActiveTheme(): ThemeInterface
    {
        if ($this->activeTheme !== null) {
            return $this->activeTheme;
        }

        $activeId = (string) setting('active_theme', 'default');

        if (isset($this->registeredThemes[$activeId])) {
            $this->activeTheme = $this->registeredThemes[$activeId];
        } else {
            $this->activeTheme = $this->registeredThemes['default'] ?? new DefaultTheme();
        }

        return $this->activeTheme;
    }

    /**
     * Sets the active theme by ID.
     */
    public function setActiveTheme(string $themeId): bool
    {
        if (! isset($this->registeredThemes[$themeId])) {
            return false;
        }

        Setting::updateOrCreate(
            ['key' => 'active_theme'],
            ['value' => $themeId, 'type' => 'text']
        );

        Cache::forget('setting.active_theme');
        $this->activeTheme = $this->registeredThemes[$themeId];

        // Clear location and menu caches when changing active theme
        app(MenuResolverService::class)->clearAllLocationCaches();

        return true;
    }

    /**
     * Returns declared menu locations for the active theme.
     *
     * @return array<string, string>
     */
    public function getMenuLocations(): array
    {
        return $this->getActiveTheme()->getMenuLocations();
    }

    /**
     * Returns map of location => menu_id from settings.
     *
     * @return array<string, int>
     */
    public function getLocationAssignments(): array
    {
        $locations = setting('theme_menu_locations', []);
        if (is_string($locations)) {
            $locations = json_decode($locations, true) ?: [];
        }

        return (array) $locations;
    }

    /**
     * Assigns a menu ID to a specific location slot.
     */
    public function assignMenuToLocation(string $location, ?int $menuId): void
    {
        $locations = $this->getLocationAssignments();

        if ($menuId === null || $menuId <= 0) {
            unset($locations[$location]);
        } else {
            $locations[$location] = $menuId;
        }

        Setting::updateOrCreate(
            ['key' => 'theme_menu_locations'],
            ['value' => json_encode($locations), 'type' => 'json']
        );

        Cache::forget('setting.theme_menu_locations');
        app(MenuResolverService::class)->clearLocationCache($location);
    }

    /**
     * Bootstraps the active theme into Laravel's ViewFinder.
     * Prepends theme view path so theme templates take precedence over core fallbacks.
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;
        $activeTheme = $this->getActiveTheme();

        // 1. Call theme boot hook
        $activeTheme->boot();

        // 2. Prepend theme views path to Laravel ViewFinder
        $viewPath = $activeTheme->getViewPath();
        if ($viewPath && File::isDirectory($viewPath)) {
            View::prependLocation($viewPath);
        }
    }
}
