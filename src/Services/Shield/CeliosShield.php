<?php

declare(strict_types=1);

namespace Celios\Core\Services\Shield;

use BezhanSalleh\FilamentShield\FilamentShield;
use Celios\Core\Services\ModuleManager;
use Illuminate\Support\Collection;

class CeliosShield extends FilamentShield
{
    public function discoverResources(): Collection
    {
        return parent::discoverResources()->filter(function (string $resource): bool {
            return self::isEntityEnabled($resource);
        });
    }

    public function discoverPages(): Collection
    {
        return parent::discoverPages()->filter(function (string $page): bool {
            return self::isEntityEnabled($page);
        });
    }

    public function discoverWidgets(): Collection
    {
        return parent::discoverWidgets()->filter(function (string $widget): bool {
            return self::isEntityEnabled($widget);
        });
    }

    public function getEntitiesPermissions(): ?array
    {
        return collect($this->getAllResourcePermissionsWithLabels())->keys()
            ->merge(collect($this->getPages())->flatMap(fn (array $page): array => array_keys($page['permissions'] ?? [])))
            ->merge(collect($this->getWidgets())->flatMap(fn (array $widget): array => array_keys($widget['permissions'] ?? [])))
            ->merge(collect($this->getCustomPermissions())->keys())
            ->values()
            ->flatten()
            ->unique()
            ->toArray();
    }

    public static function isEntityEnabled(string $class): bool
    {
        // Check if class defines MODULE_KEY constant
        if (defined("{$class}::MODULE_KEY")) {
            return ModuleManager::isEnabled($class::MODULE_KEY);
        }

        // Check namespace-based conventions for known modular packages
        if (str_starts_with($class, 'Modules\\Appointments\\')) {
            return ModuleManager::isEnabled('appointments');
        }

        if (str_starts_with($class, 'Modules\\Crm\\')) {
            return ModuleManager::isEnabled('crm');
        }

        if (str_starts_with($class, 'Modules\\Payment\\')) {
            return ModuleManager::isEnabled('payment');
        }

        if (str_starts_with($class, 'Celios\\Core\\Filament\\Widgets\\Queue\\')) {
            return ModuleManager::isEnabled('queue_manager');
        }

        // Check against config('modules.modules')
        $modules = config('modules.modules', []);
        foreach ($modules as $moduleKey => $moduleConfig) {
            $resources = $moduleConfig['resources'] ?? [];
            $pages = $moduleConfig['pages'] ?? [];
            $widgets = $moduleConfig['widgets'] ?? [];

            if (in_array($class, $resources, true) || in_array($class, $pages, true) || in_array($class, $widgets, true)) {
                return ModuleManager::isEnabled($moduleKey);
            }
        }

        return true;
    }
}
