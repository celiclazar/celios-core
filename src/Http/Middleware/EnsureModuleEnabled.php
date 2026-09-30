<?php

namespace Celios\Core\Http\Middleware;

use Celios\Core\Services\ModuleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /**
     * Handle an incoming request.
     * Aborts with a 404 response if the requested CMS module is disabled.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! ModuleManager::isEnabled($module)) {
            abort(404, "Module [{$module}] is currently disabled.");
        }

        return $next($request);
    }
}
