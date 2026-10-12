<?php

namespace Celios\Core\Traits;

trait HasLocalizedDisplay
{
    /**
     * Get the display title for the model with language fallback cascade:
     * 1. Requested/current locale
     * 2. Default app locale (config('locales.default', 'sr'))
     * 3. First available non-empty translation
     * 4. Fallback string / 'fields.no_title'
     */
    public function displayTitle(?string $locale = null, string $fallback = ''): string
    {
        return $this->displayTranslatedAttribute('title', $locale, $fallback ?: __('fields.no_title'));
    }

    /**
     * Get the display slug for the model with language fallback cascade.
     */
    public function displaySlug(?string $locale = null, string $fallback = ''): string
    {
        return $this->displayTranslatedAttribute('slug', $locale, $fallback);
    }

    /**
     * Resolve a translated attribute with cascade.
     */
    public function displayTranslatedAttribute(string $attribute, ?string $locale = null, string $fallback = ''): string
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = config('locales.default', 'sr');

        if (method_exists($this, 'getTranslation')) {
            $val = $this->getTranslation($attribute, $locale, false);
            if (filled($val)) {
                return (string) $val;
            }

            if ($locale !== $defaultLocale) {
                $fallbackVal = $this->getTranslation($attribute, $defaultLocale, false);
                if (filled($fallbackVal)) {
                    return (string) $fallbackVal;
                }
            }

            if (method_exists($this, 'getTranslations')) {
                $firstAvailable = collect($this->getTranslations($attribute))->filter(fn ($v) => filled($v))->first();
                if (filled($firstAvailable)) {
                    return (string) $firstAvailable;
                }
            }
        } elseif (isset($this->{$attribute}) && filled($this->{$attribute})) {
            return (string) $this->{$attribute};
        }

        return $fallback;
    }
}
