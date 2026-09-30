<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class DocumentCategory extends Model
{
    use HasTranslations;

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
        'is_internal',
    ];

    public array $translatable = [
        'title',
        'slug',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_internal' => 'boolean',
        'order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id')->orderBy('order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getLocalizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $title = $this->getTranslation('title', $locale, false);

        if (blank($title)) {
            $defaultLocale = config('locales.default', 'sr');
            $title = $this->getTranslation('title', $defaultLocale, false);
        }

        if (blank($title)) {
            $all = $this->getTranslations('title');
            $title = !empty($all) ? (reset($all) ?: null) : null;
        }

        return $title ?: __('fields.no_title');
    }

    public function getLocalizedSlug(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslation('slug', $locale, false);

        if (blank($slug)) {
            $defaultLocale = config('locales.default', 'sr');
            $slug = $this->getTranslation('slug', $defaultLocale, false);
        }

        if (blank($slug)) {
            $all = $this->getTranslations('slug');
            $slug = !empty($all) ? (reset($all) ?: '') : '';
        }

        return $slug ?: '';
    }

    public function getUrl(?string $locale = null): string
    {
        $slug = $this->getLocalizedSlug($locale);

        return filled($slug)
            ? url("/" . ($locale ?? app()->getLocale()) . "/documents/category/" . ltrim($slug, '/'))
            : url("/" . ($locale ?? app()->getLocale()) . "/documents");
    }
}
