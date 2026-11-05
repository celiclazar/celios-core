<?php

namespace Celios\Core\Filament;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CeliosPlugin implements Plugin
{
    protected bool $hasContentGroup = true;

    protected bool $hasMarketingGroup = true;

    protected bool $hasPaymentsGroup = true;

    protected bool $hasSystemGroup = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament('celios');

        return $plugin;
    }

    public function getId(): string
    {
        return 'celios';
    }

    public function withContentGroup(bool $condition = true): static
    {
        $this->hasContentGroup = $condition;

        return $this;
    }

    public function withMarketingGroup(bool $condition = true): static
    {
        $this->hasMarketingGroup = $condition;

        return $this;
    }

    public function withPaymentsGroup(bool $condition = true): static
    {
        $this->hasPaymentsGroup = $condition;

        return $this;
    }

    public function withSystemGroup(bool $condition = true): static
    {
        $this->hasSystemGroup = $condition;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $navigationGroups = [];

        if ($this->hasContentGroup) {
            $navigationGroups[] = NavigationGroup::make(fn () => __('sidebar.group_content'))
                ->icon('heroicon-o-document-text')
                ->collapsible();
        }

        if ($this->hasMarketingGroup) {
            $navigationGroups[] = NavigationGroup::make(fn () => __('sidebar.group_marketing'))
                ->icon('heroicon-o-megaphone')
                ->collapsed();
        }

        if ($this->hasPaymentsGroup) {
            $navigationGroups[] = NavigationGroup::make(fn () => __('sidebar.group_payments'))
                ->icon('heroicon-o-credit-card')
                ->collapsed();
        }

        if ($this->hasSystemGroup) {
            $navigationGroups[] = NavigationGroup::make(fn () => __('sidebar.group_system'))
                ->icon('heroicon-o-shield-check')
                ->collapsed();
        }

        $navigationGroups[] = NavigationGroup::make(fn () => __('sidebar.group_appointments'))
            ->icon('heroicon-o-calendar-days')
            ->collapsible();

        $panel->navigationGroups($navigationGroups)
            ->discoverResources(in: __DIR__.'/Resources', for: 'Celios\\Core\\Filament\\Resources')
            ->discoverPages(in: __DIR__.'/Pages', for: 'Celios\\Core\\Filament\\Pages')
            ->discoverWidgets(in: __DIR__.'/Widgets', for: 'Celios\\Core\\Filament\\Widgets');

        $discoveredModules = [];

        foreach ([base_path('packages'), base_path('Modules'), base_path('vendor/celios')] as $parentPath) {
            if (! File::exists($parentPath)) {
                continue;
            }

            foreach (File::directories($parentPath) as $moduleDir) {
                $dirName = basename($moduleDir);
                if ($dirName === 'core') {
                    continue;
                }

                $studlyName = Str::studly($dirName);
                if (isset($discoveredModules[$studlyName])) {
                    continue;
                }
                $discoveredModules[$studlyName] = true;

                $resPath = $moduleDir.'/Filament/Resources';
                if (File::exists($resPath)) {
                    $panel->discoverResources(in: $resPath, for: "Modules\\{$studlyName}\\Filament\\Resources");
                }

                $pagesPath = $moduleDir.'/Filament/Pages';
                if (File::exists($pagesPath)) {
                    $panel->discoverPages(in: $pagesPath, for: "Modules\\{$studlyName}\\Filament\\Pages");
                }

                $widgetsPath = $moduleDir.'/Filament/Widgets';
                if (File::exists($widgetsPath)) {
                    $panel->discoverWidgets(in: $widgetsPath, for: "Modules\\{$studlyName}\\Filament\\Widgets");
                }
            }
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
