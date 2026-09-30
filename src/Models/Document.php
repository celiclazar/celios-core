<?php

namespace Celios\Core\Models;

use Celios\Core\Enums\DocumentAccessLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Document extends Model
{
    use HasTranslations;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'version',
        'access_level',
        'allowed_roles',
        'downloads_count',
        'is_published',
        'published_at',
        'uploaded_by',
    ];

    public array $translatable = [
        'title',
        'slug',
        'description',
    ];

    protected $casts = [
        'access_level' => DocumentAccessLevel::class,
        'allowed_roles' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'file_size' => 'integer',
        'downloads_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Document $document) {
            if ($document->file_path) {
                $disk = \Illuminate\Support\Facades\Storage::disk('documents');
                if ($disk->exists($document->file_path)) {
                    if (blank($document->file_size) || $document->file_size === 0) {
                        $document->file_size = $disk->size($document->file_path);
                    }
                    if (blank($document->file_type)) {
                        $document->file_type = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                    }
                    if (blank($document->file_name)) {
                        $document->file_name = basename($document->file_path);
                    }
                }
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DocumentDownloadLog::class);
    }

    public function canAccess(?User $user): bool
    {
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return true;
        }

        // If category is internal, treat as internal access
        if ($this->category && $this->category->is_internal) {
            return $user && ($user->can('View:Document') || $user->hasRole(['super_admin', 'admin', 'panel_user']));
        }

        return match ($this->access_level) {
            DocumentAccessLevel::PUBLIC => true,
            DocumentAccessLevel::AUTHENTICATED => $user !== null,
            DocumentAccessLevel::ROLES => $user !== null && !empty($this->allowed_roles) && $user->hasAnyRole($this->allowed_roles),
            DocumentAccessLevel::INTERNAL => $user !== null && ($user->can('View:Document') || $user->hasRole(['super_admin', 'admin', 'panel_user'])),
        };
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    public function scopePublicOnly($query)
    {
        return $query->where('access_level', DocumentAccessLevel::PUBLIC->value)
            ->where(function ($q) {
                $q->whereNull('category_id')
                  ->orWhereHas('category', fn ($cat) => $cat->where('is_internal', false));
            });
    }

    public function scopeVisibleToUser($query, ?User $user)
    {
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            // Public documents from non-internal categories
            $q->where(function ($sub) {
                $sub->where('access_level', DocumentAccessLevel::PUBLIC->value)
                    ->where(function ($c) {
                        $c->whereNull('category_id')
                          ->orWhereHas('category', fn ($cat) => $cat->where('is_internal', false));
                    });
            });

            if ($user) {
                // Authenticated level
                $q->orWhere(function ($sub) {
                    $sub->where('access_level', DocumentAccessLevel::AUTHENTICATED->value)
                        ->where(function ($c) {
                            $c->whereNull('category_id')
                              ->orWhereHas('category', fn ($cat) => $cat->where('is_internal', false));
                        });
                });

                // Roles level
                if (method_exists($user, 'roles')) {
                    $userRoles = $user->roles()->pluck('name')->toArray();
                    foreach ($userRoles as $role) {
                        $q->orWhere(function ($sub) use ($role) {
                            $sub->where('access_level', DocumentAccessLevel::ROLES->value)
                                ->whereJsonContains('allowed_roles', $role);
                        });
                    }
                }

                // Internal level if staff/admin
                if ($user->can('View:Document') || $user->hasRole(['super_admin', 'admin', 'panel_user'])) {
                    $q->orWhere('access_level', DocumentAccessLevel::INTERNAL->value)
                      ->orWhereHas('category', fn ($cat) => $cat->where('is_internal', true));
                }
            }
        });
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        if ($bytes > 0) {
            return $bytes . ' B';
        }
        return '0 B';
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

        return $title ?: ($this->file_name ?: __('fields.no_title'));
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

    public function getDownloadUrl(?string $locale = null): string
    {
        $targetLocale = $locale ?? app()->getLocale();
        $slug = $this->getLocalizedSlug($targetLocale);

        return filled($slug)
            ? url("/{$targetLocale}/documents/{$slug}/download")
            : url("/{$targetLocale}/documents");
    }

    public function getPreviewUrl(?string $locale = null): string
    {
        $targetLocale = $locale ?? app()->getLocale();
        $slug = $this->getLocalizedSlug($targetLocale);

        return filled($slug)
            ? url("/{$targetLocale}/documents/{$slug}/preview")
            : url("/{$targetLocale}/documents");
    }
}
