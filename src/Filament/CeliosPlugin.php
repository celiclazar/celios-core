<?php

namespace Celios\Core\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Navigation\NavigationGroup;

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

        $panel->navigationGroups($navigationGroups)
            ->discoverResources(in: __DIR__ . '/Resources', for: 'Celios\\Core\\Filament\\Resources')
            ->discoverPages(in: __DIR__ . '/Pages', for: 'Celios\\Core\\Filament\\Pages')
            ->discoverWidgets(in: __DIR__ . '/Widgets', for: 'Celios\\Core\\Filament\\Widgets');
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
