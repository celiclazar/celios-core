<?php

use Celios\Core\Models\Setting;
use Celios\Core\Services\ModuleManager;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        try {
            return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
                $setting = Setting::where('key', $key)->first();

                return $setting ? $setting->value : $default;
            });
        } catch (Throwable $e) {
            return $default;
        }
    }
}

if (! function_exists('module_enabled')) {
    function module_enabled(string $module): bool
    {
        return ModuleManager::isEnabled($module);
    }
}

if (! function_exists('current_brand')) {
    function current_brand(?string $domain = null): ?\Celios\Core\Models\Brand
    {
        return \Celios\Core\Models\Brand::current($domain);
    }
}

if (! function_exists('active_locales')) {
    /**
     * Get associative array of active locales [code => Label] for current brand or global config.
     *
     * @return array<string, string>
     */
    function active_locales(): array
    {
        $all = config('locales.available', [
            'sr' => 'Srpski',
            'en' => 'English',
            'it' => 'Italiano',
        ]);

        try {
            $brand = current_brand();
            if ($brand && ! empty($brand->supported_locales) && is_array($brand->supported_locales)) {
                $filtered = array_intersect_key($all, array_flip($brand->supported_locales));
                if (! empty($filtered)) {
                    return $filtered;
                }
            }
        } catch (\Throwable $e) {
            // Fallback to all
        }

        return $all;
    }
}

if (! function_exists('active_locale_codes')) {
    /**
     * Get list of active locale codes (e.g. ['sr', 'en']).
     *
     * @return array<string>
     */
    function active_locale_codes(): array
    {
        return array_keys(active_locales());
    }
}

if (! function_exists('default_locale')) {
    /**
     * Get the default locale for the current brand or global config.
     */
    function default_locale(): string
    {
        try {
            $brand = current_brand();
            if ($brand && ! empty($brand->default_locale)) {
                return $brand->default_locale;
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return config('locales.default', 'sr');
    }
}

