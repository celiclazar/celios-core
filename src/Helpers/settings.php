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
