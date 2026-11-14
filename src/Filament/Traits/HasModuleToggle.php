<?php

namespace Celios\Core\Filament\Traits;

use Celios\Core\Services\ModuleManager;

trait HasModuleToggle
{
    /**
     * Determine if the resource/page can be accessed.
     * Denies access if the associated module is disabled, or if required permission is missing.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('super-admin'))) {
            return parent::canAccess();
        }

        if (defined('static::MODULE_KEY') && ! ModuleManager::isEnabled(static::MODULE_KEY)) {
            return false;
        }

        // For Filament Pages (not Resources)
        if (is_subclass_of(static::class, \Filament\Pages\Page::class)) {
            $baseName = class_basename(static::class);
            $permission = "View:{$baseName}";
            $legacyPermission = "page_{$baseName}";

            if (! ($user->can($permission) || $user->can($legacyPermission))) {
                return false;
            }
        }

        return parent::canAccess();
    }

    /**
     * Determine if the resource/page navigation item should be registered in sidebar.
     * Hides navigation if the associated module is disabled, or if user cannot access the item.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('super-admin'))) {
            return parent::shouldRegisterNavigation();
        }

        if (defined('static::MODULE_KEY') && ! ModuleManager::isEnabled(static::MODULE_KEY)) {
            return false;
        }

        if (is_subclass_of(static::class, \Filament\Pages\Page::class)) {
            return static::canAccess() && parent::shouldRegisterNavigation();
        }

        return parent::shouldRegisterNavigation();
    }
}
