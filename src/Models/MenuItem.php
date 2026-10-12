<?php

namespace Celios\Core\Models;

use Celios\Core\Services\MenuResolverService;
use Celios\Core\Traits\HasLocalizedDisplay;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use HasLocalizedDisplay, HasTranslations;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'order',
        'linkable_type',
        'linkable_id',
        'title',
        'custom_url',
        'target',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public array $translatable = [
        'custom_url',
        'title',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function getUrlAttribute(): string
    {
        $locale = app()->getLocale();

        // Ako je izabrana CMS stranica / model
        if ($this->linkable) {
            if (method_exists($this->linkable, 'getUrl')) {
                return $this->linkable->getUrl($locale);
            }

            $slug = $this->linkable->getTranslation('slug', $locale, false);
            if (blank($slug)) {
                $defaultLocale = config('locales.default', 'sr');
                $slug = $this->linkable->getTranslation('slug', $defaultLocale, false);
            }

            return filled($slug) ? url("/{$locale}/".ltrim($slug, '/')) : url("/{$locale}");
        }

        // Ako nije izabrana stranica, vraća formatirani custom_url
        $customUrl = $this->getTranslation('custom_url', $locale, false);
        if (blank($customUrl)) {
            $defaultLocale = config('locales.default', 'sr');
            $customUrl = $this->getTranslation('custom_url', $defaultLocale, false);
        }

        if (blank($customUrl)) {
            return '#';
        }

        return app(MenuResolverService::class)->formatCustomUrl($customUrl, $locale);
    }

    public function displayTitle(?string $locale = null, string $fallback = ''): string
    {
        $title = $this->displayTranslatedAttribute('title', $locale, '');
        if (filled($title)) {
            return $title;
        }

        if ($this->linkable && method_exists($this->linkable, 'displayTitle')) {
            return $this->linkable->displayTitle($locale, $fallback);
        }

        return $fallback ?: __('fields.no_title');
    }

    protected static function booted(): void
    {
        static::saved(function (MenuItem $item) {
            $menu = $item->menu ?? ($item->menu_id ? Menu::find($item->menu_id) : null);
            if ($menu) {
                app(MenuResolverService::class)->clearCache($menu->key);
            }
        });

        static::deleted(function (MenuItem $item) {
            $menu = $item->menu ?? ($item->menu_id ? Menu::find($item->menu_id) : null);
            if ($menu) {
                app(MenuResolverService::class)->clearCache($menu->key);
            }
        });
    }
}
