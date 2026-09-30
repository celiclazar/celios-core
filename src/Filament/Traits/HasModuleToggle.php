<?php

namespace Celios\Core\Filament\Traits;

use Celios\Core\Services\ModuleManager;

trait HasModuleToggle
{
    /**
     * Determine if the resource/page can be accessed.
     * Denies access if the associated module is disabled.
     */
    public static function canAccess(): bool
    {
        if (defined('static::MODULE_KEY') && ! ModuleManager::isEnabled(static::MODULE_KEY)) {
            return false;
        }

        return parent::canAccess();
    }

    /**
     * Determine if the resource/page navigation item should be registered in sidebar.
     * Hides navigation if the associated module is disabled.
     */
    public static function shouldRegisterNavigation(): bool
    {
        if (defined('static::MODULE_KEY') && ! ModuleManager::isEnabled(static::MODULE_KEY)) {
            return false;
        }

        return parent::shouldRegisterNavigation();
    }
}
