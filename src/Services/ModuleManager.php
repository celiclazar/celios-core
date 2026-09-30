<?php

namespace Celios\Core\Services;

use Celios\Core\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ModuleManager
{
    /**
     * Check if a specific module is currently enabled.
     */
    public static function isEnabled(string $moduleKey): bool
    {
        // Core features are always enabled
        if (in_array($moduleKey, ['core', 'pages', 'media', 'seo'], true)) {
            return true;
        }

        $currentPlan = self::getCurrentPlan();
        if ($currentPlan !== 'custom') {
            $planModules = config("modules.plans.{$currentPlan}.modules");
            if ($planModules !== null && in_array($moduleKey, $planModules, true)) {
                return true;
            }
        }

        $activeModules = self::getActiveModules();

        return in_array($moduleKey, $activeModules, true);
    }

    /**
     * Retrieve all active module keys.
     * Cached permanently until changed in settings.
     */
    public static function getActiveModules(): array
    {
        return Cache::rememberForever('cms_active_modules', function () {
            try {
                $setting = Setting::where('key', 'active_modules')->first();

                if ($setting && !empty($setting->value)) {
                    $decoded = is_array($setting->value)
                        ? $setting->value
                        : json_decode($setting->value, true);

                    if (is_array($decoded)) {
                        return $decoded;
                    }
                }
            } catch (\Throwable $e) {
                // In early migrations or tests before table exists, fallback safely
            }

            // Fallback default: use CMS_PLAN or unlock all modules (enterprise)
            $defaultPlan = env('CMS_PLAN', 'enterprise');
            $planModules = config("modules.plans.{$defaultPlan}.modules");

            if ($planModules !== null) {
                return $planModules;
            }

            return array_keys(config('modules.modules', []));
        });
    }

    /**
     * Get the current active pricing plan key.
     */
    public static function getCurrentPlan(): string
    {
        try {
            $setting = Setting::where('key', 'current_pricing_plan')->first();
            if ($setting && !empty($setting->value)) {
                return (string) $setting->value;
            }
        } catch (\Throwable $e) {
            // Fallback safely
        }

        return env('CMS_PLAN', 'enterprise');
    }

    /**
     * Apply a predefined pricing plan.
     */
    public static function setPlan(string $planKey): void
    {
        $plan = config("modules.plans.{$planKey}");

        if ($plan) {
            self::setActiveModules($plan['modules'], $planKey);
        }
    }

    /**
     * Update active modules list and bust cache.
     */
    public static function setActiveModules(array $modules, ?string $plan = 'custom'): void
    {
        $uniqueModules = array_values(array_unique(array_filter($modules)));

        Setting::updateOrCreate(
            ['key' => 'active_modules'],
            ['value' => json_encode($uniqueModules)]
        );

        if ($plan !== null) {
            Setting::updateOrCreate(
                ['key' => 'current_pricing_plan'],
                ['value' => $plan]
            );
        }

        self::flushCache();
    }

    /**
     * Flush all module-related cache tags / keys.
     */
    public static function flushCache(): void
    {
        Cache::forget('cms_active_modules');
        Cache::forget('setting.active_modules');
        Cache::forget('setting.current_pricing_plan');
    }

    /**
     * Returns an array of Page Builder block instances enabled for active modules.
     */
    public static function getAvailablePageBlocks(): array
    {
        $blocks = [
            \Celios\Core\Filament\Blocks\HeroBlock::make(),
            \Celios\Core\Filament\Blocks\HeroShowcaseBlock::make(),
            \Celios\Core\Filament\Blocks\FeaturesCardsBlock::make(),
            \Celios\Core\Filament\Blocks\InteractiveShowcaseBlock::make(),
            \Celios\Core\Filament\Blocks\PortfolioShowcaseBlock::make(),
            \Celios\Core\Filament\Blocks\AuthorBioBlock::make(),
            \Celios\Core\Filament\Blocks\CtaSandboxBlock::make(),
            \Celios\Core\Filament\Blocks\ColorSystemBlock::make(),
            \Celios\Core\Filament\Blocks\TextBlock::make(),
            \Celios\Core\Filament\Blocks\ImageBlock::make(),
            \Celios\Core\Filament\Blocks\GalleryBlock::make(),
            \Celios\Core\Filament\Blocks\CTABlock::make(),
            \Celios\Core\Filament\Blocks\FeaturesBlock::make(),
            \Celios\Core\Filament\Blocks\FAQBlock::make(),
            \Celios\Core\Filament\Blocks\StatsBlock::make(),
            \Celios\Core\Filament\Blocks\SliderBlock::make(),
        ];

        if (self::isEnabled('blog') && class_exists(\Celios\Core\Filament\Blocks\PostsBlock::class)) {
            $blocks[] = \Celios\Core\Filament\Blocks\PostsBlock::make();
        }

        if (self::isEnabled('documents') && class_exists(\Celios\Core\Filament\Blocks\DocumentsBlock::class)) {
            $blocks[] = \Celios\Core\Filament\Blocks\DocumentsBlock::make();
        }

        if (self::isEnabled('forms') && class_exists(\Celios\Core\Filament\Blocks\FormBlock::class)) {
            $blocks[] = \Celios\Core\Filament\Blocks\FormBlock::make();
        }

        if (self::isEnabled('newsletter') && class_exists(\Celios\Core\Filament\Blocks\NewsletterBlock::class)) {
            $blocks[] = \Celios\Core\Filament\Blocks\NewsletterBlock::make();
        }

        return $blocks;
    }

    /**
     * Get all module definitions.
     */
    public static function getAllModules(): array
    {
        return config('modules.modules', []);
    }

    /**
     * Get all plan definitions.
     */
    public static function getAllPlans(): array
    {
        return config('modules.plans', []);
    }
}
