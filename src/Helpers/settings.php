<?php

use Celios\Core\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        try {
            return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
                $setting = Setting::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (!function_exists('module_enabled')) {
    function module_enabled(string $module): bool {
        return \Celios\Core\Services\ModuleManager::isEnabled($module);
    }
}
