<?php

namespace Celios\Core\Models;

use Celios\Core\Enums\PageType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'content',
        'is_visible',
        'draft_content'
    ];

    public $translatable = [
        'title',
        'slug',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'type' => PageType::class,
        'content' => 'array',
        'is_visible' => 'boolean',
        'draft_content' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            if (! $page->type) {
                $page->type = PageType::STANDARD;
            }

            if ($page->isSystem()) {
                // Enforce standardized slugs for all supported locales
                $standardSlugs = $page->type->standardSlugs();
                $currentSlugs = $page->getTranslations('slug') ?: [];

                foreach ($standardSlugs as $locale => $stdSlug) {
                    $currentSlugs[$locale] = $stdSlug;
                }

                $page->setTranslations('slug', $currentSlugs);

                // Auto-fill empty titles with defaults
                $currentTitles = $page->getTranslations('title') ?: [];
                $defaultTitles = $page->type->defaultTitles();

                foreach ($defaultTitles as $locale => $defTitle) {
                    if (empty($currentTitles[$locale])) {
                        $currentTitles[$locale] = $defTitle;
                    }
                }

                $page->setTranslations('title', $currentTitles);
            }
        });

        static::deleting(function (Page $page) {
            if ($page->isSystem()) {
                throw new \RuntimeException('System and legal pages cannot be deleted.');
            }
        });
    }

    public function revisions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PageRevision::class)->latest('id');
    }

    public function createRevision(?int $userId = null, ?string $note = null, ?array $contentToSnapshot = null): PageRevision
    {
        $content = $contentToSnapshot ?? $this->draft_content ?? $this->content ?? [];

        $revision = $this->revisions()->create([
            'user_id' => $userId ?? auth()->id(),
            'title' => $this->getTranslations('title'),
            'content' => $content,
            'note' => $note,
        ]);

        $this->pruneRevisions(20);

        return $revision;
    }

    public function publish(?int $userId = null, ?string $note = null): bool
    {
        if (empty($this->draft_content)) {
            return false;
        }

        $this->createRevision(
            userId: $userId,
            note: $note ?: __('notifications.page_published_snapshot')
        );

        $this->update([
            'content' => $this->draft_content,
            'draft_content' => null,
        ]);

        return true;
    }

    public function restoreRevision(PageRevision|int $revision): bool
    {
        $rev = $revision instanceof PageRevision ? $revision : $this->revisions()->findOrFail($revision);

        return $this->update([
            'draft_content' => $rev->content,
        ]);
    }

    public function pruneRevisions(int $keep = 20): void
    {
        $revisionIdsToKeep = $this->revisions()
            ->reorder()
            ->latest('id')
            ->take($keep)
            ->pluck('id');

        $this->revisions()
            ->whereNotIn('id', $revisionIdsToKeep)
            ->delete();
    }

    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = config('locales.default', 'sr');

        if ($this->type === PageType::HOME) {
            return $locale === $defaultLocale ? url('/') : url("/{$locale}");
        }

        $slug = $this->getTranslation('slug', $locale, false);

        if (blank($slug)) {
            $slug = $this->getTranslation('slug', $defaultLocale, false);
        }

        if (blank($slug) || $slug === 'home') {
            return $locale === $defaultLocale ? url('/') : url("/{$locale}");
        }

        return url("/{$locale}/" . ltrim($slug, '/'));
    }

    public function isSystem(): bool
    {
        return $this->type instanceof PageType ? $this->type->isSystem() : false;
    }

    public function scopeOfType($query, PageType|string $type)
    {
        $val = $type instanceof PageType ? $type->value : $type;
        return $query->where('type', $val);
    }

    public function scopeSystem($query)
    {
        return $query->where('type', '!=', PageType::STANDARD->value);
    }

    public function scopeStandard($query)
    {
        return $query->where('type', PageType::STANDARD->value);
    }

    public static function findByType(PageType|string $type): ?self
    {
        $val = $type instanceof PageType ? $type->value : $type;
        return static::where('type', $val)->first();
    }

    public static function urlForType(PageType|string $type, ?string $locale = null): ?string
    {
        $pageType = $type instanceof PageType ? $type : PageType::tryFrom($type);
        if (! $pageType) {
            return null;
        }

        $page = static::findByType($pageType);
        if ($page) {
            return $page->getUrl($locale);
        }

        // Fallback if page not created yet in DB but has standardized slug
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = config('locales.default', 'sr');

        if ($pageType === PageType::HOME) {
            return $locale === $defaultLocale ? url('/') : url("/{$locale}");
        }

        $slug = $pageType->standardSlug($locale) ?? $pageType->standardSlug($defaultLocale);
        if ($slug) {
            return url("/{$locale}/" . ltrim($slug, '/'));
        }

        return null;
    }
}
