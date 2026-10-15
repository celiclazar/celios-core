<?php

namespace Celios\Core\Services;

use Celios\Core\Filament\Blocks\AuthorBioBlock;
use Celios\Core\Filament\Blocks\ButtonBlock;
use Celios\Core\Filament\Blocks\ColorSystemBlock;
use Celios\Core\Filament\Blocks\CTABlock;
use Celios\Core\Filament\Blocks\CtaSandboxBlock;
use Celios\Core\Filament\Blocks\DocumentsBlock;
use Celios\Core\Filament\Blocks\FAQBlock;
use Celios\Core\Filament\Blocks\FeaturesBlock;
use Celios\Core\Filament\Blocks\FeaturesCardsBlock;
use Celios\Core\Filament\Blocks\FormBlock;
use Celios\Core\Filament\Blocks\GalleryBlock;
use Celios\Core\Filament\Blocks\HeroBlock;
use Celios\Core\Filament\Blocks\HeroShowcaseBlock;
use Celios\Core\Filament\Blocks\ImageBlock;
use Celios\Core\Filament\Blocks\InteractiveShowcaseBlock;
use Celios\Core\Filament\Blocks\NewsletterBlock;
use Celios\Core\Filament\Blocks\PortfolioShowcaseBlock;
use Celios\Core\Filament\Blocks\PostsBlock;
use Celios\Core\Filament\Blocks\SectionBlock;
use Celios\Core\Filament\Blocks\SectionReferenceBlock;
use Celios\Core\Filament\Blocks\SliderBlock;
use Celios\Core\Filament\Blocks\StatsBlock;
use Celios\Core\Filament\Blocks\TextBlock;
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

                if ($setting && ! empty($setting->value)) {
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
            if ($setting && ! empty($setting->value)) {
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
            SectionBlock::make(),
            SectionReferenceBlock::make(),
            HeroBlock::make(),
            HeroShowcaseBlock::make(),
            FeaturesCardsBlock::make(),
            InteractiveShowcaseBlock::make(),
            PortfolioShowcaseBlock::make(),
            AuthorBioBlock::make(),
            CtaSandboxBlock::make(),
            ColorSystemBlock::make(),
            TextBlock::make(),
            ButtonBlock::make(),
            ImageBlock::make(),
            GalleryBlock::make(),
            CTABlock::make(),
            FeaturesBlock::make(),
            FAQBlock::make(),
            StatsBlock::make(),
            SliderBlock::make(),
        ];

        if (self::isEnabled('blog') && class_exists(PostsBlock::class)) {
            $blocks[] = PostsBlock::make();
        }

        if (self::isEnabled('documents') && class_exists(DocumentsBlock::class)) {
            $blocks[] = DocumentsBlock::make();
        }

        if (self::isEnabled('forms') && class_exists(FormBlock::class)) {
            $blocks[] = FormBlock::make();
        }

        if (self::isEnabled('newsletter') && class_exists(NewsletterBlock::class)) {
            $blocks[] = NewsletterBlock::make();
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
