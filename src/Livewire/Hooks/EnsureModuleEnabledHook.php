<?php

namespace Celios\Core\Livewire\Hooks;

use Celios\Core\Services\ModuleManager;
use Celios\Core\Services\Shield\CeliosShield;
use Filament\Pages\Page as FilamentPage;
use Livewire\ComponentHook;

class EnsureModuleEnabledHook extends ComponentHook
{
    /**
     * Intercept Livewire component booting.
     * Aborts with a 404 response if the component is a Filament Page/Resource Page
     * belonging to a disabled CMS module.
     */
    public function boot(): void
    {
        if (! ($this->component instanceof FilamentPage)) {
            return;
        }

        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('super-admin'))) {
            return;
        }

        $componentClass = get_class($this->component);
        $targetClass = method_exists($this->component, 'getResource')
            ? $this->component::getResource()
            : $componentClass;

        if ($targetClass) {
            if (defined("{$targetClass}::MODULE_KEY")) {
                $moduleKey = constant("{$targetClass}::MODULE_KEY");
                if (! ModuleManager::isEnabled($moduleKey)) {
                    abort(404, "Module [{$moduleKey}] is currently disabled.");
                }
            } elseif (! CeliosShield::isEntityEnabled($targetClass)) {
                abort(404, "Module for [{$targetClass}] is currently disabled.");
            }
        }
    }
}
