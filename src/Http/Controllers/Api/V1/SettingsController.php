<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Models\Setting;
use Celios\Core\Services\ModuleManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Get public site settings, theme configuration, locales, and active modules.
     */
    public function index(): JsonResponse
    {
        $publicSettings = Setting::query()
            ->whereNotIn('key', ['smtp_password', 'mail_password', 'stripe_secret', 'turnstile_secret_key', 'recaptcha_secret_key'])
            ->pluck('value', 'key')
            ->toArray();

        return response()->json([
            'site_name' => setting('site_name', config('app.name', 'Celios CMS')),
            'frontend_theme' => setting('frontend_theme', 'ocean'),
            'default_locale' => config('locales.default', 'sr'),
            'available_locales' => config('locales.available', [
                'sr' => 'Srpski',
                'en' => 'English',
                'it' => 'Italiano',
            ]),
            'active_modules' => ModuleManager::getActiveModules(),
            'current_plan' => ModuleManager::getCurrentPlan(),
            'settings' => $publicSettings,
        ]);
    }

    /**
     * Update key-value site settings (Admin).
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $storedValue = is_array($value) ? json_encode($value) : (string) ($value ?? '');
            Setting::updateOrCreate(
                ['key' => (string) $key],
                ['value' => $storedValue, 'type' => is_array($value) ? 'json' : 'text']
            );
            Cache::forget("setting.{$key}");
        }

        Cache::forget('cms_active_modules');

        return response()->json([
            'message' => 'Settings updated successfully.',
        ]);
    }
}
