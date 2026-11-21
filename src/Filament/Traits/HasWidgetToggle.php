<?php

namespace Celios\Core\Filament\Traits;

use Celios\Core\Services\ModuleManager;

trait HasWidgetToggle
{
    /**
     * Determine if the widget can be viewed.
     * Denies visibility if the associated module is disabled, or if required permission is missing.
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('super-admin'))) {
            return parent::canView();
        }

        if (defined('static::MODULE_KEY') && ! ModuleManager::isEnabled(static::MODULE_KEY)) {
            return false;
        }

        if (defined('static::MODULE_KEY') && static::MODULE_KEY === 'queue_manager') {
            if ($user->can('View:QueueManager') || $user->can('page_QueueManager')) {
                return parent::canView();
            }
        }

        $baseName = class_basename(static::class);
        $permission = "View:{$baseName}";
        $legacyPermission = "widget_{$baseName}";

        if (! ($user->can($permission) || $user->can($legacyPermission))) {
            return false;
        }

        return parent::canView();
    }
}
