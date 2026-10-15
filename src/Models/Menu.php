<?php

namespace Celios\Core\Models;

use Celios\Core\Services\MenuResolverService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    public const SYSTEM_KEYS = ['header', 'footer'];

    protected $fillable = ['key', 'name'];

    public function isSystem(): bool
    {
        return in_array(strtolower($this->key), self::SYSTEM_KEYS, true);
    }

    protected static function booted(): void
    {
        static::creating(function (Menu $menu) {
            if (blank($menu->key)) {
                $baseKey = \Illuminate\Support\Str::slug($menu->name ?? 'menu', '_');
                $baseKey = $baseKey ?: 'menu';
                $key = $baseKey;
                $counter = 1;
                while (static::where('key', $key)->exists()) {
                    $key = "{$baseKey}_{$counter}";
                    $counter++;
                }
                $menu->key = $key;
            }
        });

        static::saved(function (Menu $menu) {
            app(MenuResolverService::class)->clearCache($menu->key);
            if ($menu->isDirty('key') && $menu->getOriginal('key')) {
                app(MenuResolverService::class)->clearCache((string) $menu->getOriginal('key'));
            }
            app(MenuResolverService::class)->clearAllLocationCaches();
        });

        static::deleted(function (Menu $menu) {
            app(MenuResolverService::class)->clearCache($menu->key);
            app(MenuResolverService::class)->clearAllLocationCaches();
        });

        static::deleting(function (Menu $menu) {
            if ($menu->isSystem()) {
                throw new \InvalidArgumentException("System menu '{$menu->key}' cannot be deleted.");
            }
        });
    }

    /**
     * Returns an array of theme location keys currently mapped to this menu.
     */
    public function getAssignedLocations(): array
    {
        $locations = setting('theme_menu_locations', []);
        if (is_string($locations)) {
            $locations = json_decode($locations, true) ?: [];
        }

        $assigned = [];
        foreach ($locations as $locKey => $menuId) {
            if ((int) $menuId === (int) $this->id) {
                $assigned[] = $locKey;
            }
        }

        // Default legacy fallback if no explicit theme location setting is configured
        if (empty($locations)) {
            if ($this->key === 'header') {
                $assigned[] = 'primary';
            } elseif ($this->key === 'footer') {
                $assigned[] = 'footer';
            }
        }

        return $assigned;
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    public function rootMenuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('order');
    }
}
