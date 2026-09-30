<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    protected $fillable = [
        'category_id',
        'author_id',
        'featured_image_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'is_published',
        'published_at',
        'views_count',
    ];

    public array $translatable = [
        'title',
        'slug',
        'excerpt',
        'body',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeRecent($query, int $limit = 6)
    {
        return $query->published()
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit($limit);
    }

    public function getReadingTimeAttribute(): int
    {
        $locale = app()->getLocale();
        $text = strip_tags($this->getTranslation('body', $locale, false) ?: '');
        $wordCount = str_word_count($text);
        return max(1, (int) ceil($wordCount / 200));
    }

    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslation('slug', $locale, false);

        if (blank($slug)) {
            $defaultLocale = config('locales.default', 'sr');
            $slug = $this->getTranslation('slug', $defaultLocale, false);
        }

        return filled($slug)
            ? url("/{$locale}/blog/" . ltrim($slug, '/'))
            : url("/{$locale}/blog");
    }
}
