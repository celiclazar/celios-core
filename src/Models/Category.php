<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'description',
        'order',
        'is_visible',
    ];

    public array $translatable = [
        'title',
        'slug',
        'description',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public static function getRoutePrefix(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'sr' => 'kategorije',
            'it' => 'categorie',
            default => 'categories',
        };
    }

    public function getNestedSlug(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslation('slug', $locale, false);

        if (blank($slug)) {
            $defaultLocale = config('locales.default', 'sr');
            $slug = $this->getTranslation('slug', $defaultLocale, false);
        }

        if ($this->parent_id) {
            $parent = $this->relationLoaded('parent') ? $this->parent : $this->parent()->first();
            $parentSlug = $parent?->getNestedSlug($locale);
            if (filled($parentSlug)) {
                return "{$parentSlug}/" . ltrim($slug, '/');
            }
        }

        return $slug ?? '';
    }

    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slugPath = $this->getNestedSlug($locale);
        $prefix = static::getRoutePrefix($locale);

        return filled($slugPath)
            ? url("/{$locale}/{$prefix}/" . ltrim($slugPath, '/'))
            : url("/{$locale}/blog");
    }
}
