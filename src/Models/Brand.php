<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'headline',
        'description',
        'copyright_text',
        'primary_domain',
        'domains',
        'logo_light_id',
        'logo_dark_id',
        'favicon_id',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'tax_id',
        'social_links',
        'default_locale',
        'supported_locales',
        'default_currency',
        'supported_currencies',
        'currency_symbol_position',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'domains' => 'array',
            'social_links' => 'array',
            'supported_locales' => 'array',
            'supported_currencies' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Brand $brand) {
            // If this brand is marked as default, ensure all other brands are not default
            if ($brand->is_default) {
                static::where('id', '!=', $brand->id ?? 0)->update(['is_default' => false]);
            }
        });

        static::saved(function () {
            Cache::flush(); // Flush relevant brand cache
        });

        static::deleted(function () {
            Cache::flush();
        });
    }

    public function logoLight(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_light_id', 'id');
    }

    public function logoDark(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_dark_id', 'id');
    }

    public function faviconMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'favicon_id', 'id');
    }

    public function hasCustomLogo(): bool
    {
        return ! empty($this->logo_light_id) || ! empty($this->logo_dark_id);
    }

    public function hasCustomFavicon(): bool
    {
        return ! empty($this->favicon_id);
    }

    public function getLogoLightUrlAttribute(): string
    {
        if ($this->logoLight && ! empty($this->logoLight->url)) {
            return $this->logoLight->url;
        }

        if ($this->logoDark && ! empty($this->logoDark->url)) {
            return $this->logoDark->url;
        }

        return file_exists(public_path('images/light-logo.png')) ? asset('images/light-logo.png') : '';
    }

    public function getLogoDarkUrlAttribute(): string
    {
        if ($this->logoDark && ! empty($this->logoDark->url)) {
            return $this->logoDark->url;
        }

        if ($this->logoLight && ! empty($this->logoLight->url)) {
            return $this->logoLight->url;
        }

        return file_exists(public_path('images/dark-logo.png')) ? asset('images/dark-logo.png') : '';
    }

    public function getFaviconUrlAttribute(): string
    {
        if ($this->faviconMedia && ! empty($this->faviconMedia->url)) {
            return $this->faviconMedia->url;
        }

        return file_exists(public_path('favicon.ico')) ? asset('favicon.ico') : '';
    }

    /**
     * Resolve the current brand by domain or fallback to the default brand.
     */
    public static function current(?string $domain = null): ?self
    {
        $domain = $domain ?: (app()->runningInConsole() ? null : request()?->getHost());
        $cacheKey = 'brand_current_id_'.($domain ?: 'default');

        try {
            $brandId = Cache::remember($cacheKey, 3600, function () use ($domain) {
                if ($domain) {
                    $matchedId = static::query()
                        ->where('is_active', true)
                        ->where(function ($query) use ($domain) {
                            $query->where('primary_domain', $domain)
                                ->orWhereJsonContains('domains', $domain);
                        })
                        ->value('id');

                    if ($matchedId) {
                        return $matchedId;
                    }
                }

                return static::query()
                    ->where('is_default', true)
                    ->where('is_active', true)
                    ->value('id')
                    ?? static::query()->where('is_active', true)->value('id');
            });

            if ($brandId) {
                $brand = static::with(['logoLight', 'logoDark', 'faviconMedia'])->find($brandId);
                if ($brand) {
                    return $brand;
                }
            }
        } catch (\Throwable $e) {
            // Fallback directly to database
        }

        return static::with(['logoLight', 'logoDark', 'faviconMedia'])
            ->where('is_default', true)
            ->where('is_active', true)
            ->first()
            ?? static::with(['logoLight', 'logoDark', 'faviconMedia'])->where('is_active', true)->first();
    }
}
