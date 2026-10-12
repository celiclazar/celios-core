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
        static::saved(function (Menu $menu) {
            app(MenuResolverService::class)->clearCache($menu->key);
            if ($menu->isDirty('key') && $menu->getOriginal('key')) {
                app(MenuResolverService::class)->clearCache((string) $menu->getOriginal('key'));
            }
        });

        static::deleted(function (Menu $menu) {
            app(MenuResolverService::class)->clearCache($menu->key);
        });

        static::deleting(function (Menu $menu) {
            if ($menu->isSystem()) {
                throw new \InvalidArgumentException("System menu '{$menu->key}' cannot be deleted.");
            }
        });
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
