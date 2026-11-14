<?php

namespace Celios\Core\Http\Middleware;

use Celios\Core\Services\ModuleManager;
use Celios\Core\Services\Shield\CeliosShield;
use Closure;
use Filament\Resources\Pages\Page as FilamentResourcePage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFilamentModuleEnabled
{
    /**
     * Handle an incoming request to the Filament admin panel.
     * Aborts with a 404 response if the targeted Resource or Page belongs to a disabled CMS module.
     * SuperAdmins bypass this restriction and retain access to everything.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // SuperAdmin bypasses all module restrictions
        if ($user && method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('super-admin'))) {
            return $next($request);
        }

        $route = $request->route();
        if (! $route) {
            return $next($request);
        }

        $controller = $route->getController();
        $controllerClass = null;

        if (is_object($controller)) {
            $controllerClass = get_class($controller);
        } elseif (is_string($controller)) {
            $controllerClass = $controller;
        } else {
            $action = $route->getAction('controller');
            if (is_string($action)) {
                $controllerClass = explode('@', $action)[0];
            }
        }

        if (! $controllerClass || ! class_exists($controllerClass)) {
            return $next($request);
        }

        // Resolve target class (Resource if Resource Page, otherwise Page class)
        $targetClass = null;
        if (is_subclass_of($controllerClass, FilamentResourcePage::class) && method_exists($controllerClass, 'getResource')) {
            $targetClass = $controllerClass::getResource();
        } elseif (is_subclass_of($controllerClass, \Filament\Pages\Page::class)) {
            $targetClass = $controllerClass;
        }

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

        return $next($request);
    }
}
