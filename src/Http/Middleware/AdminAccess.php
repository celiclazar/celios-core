<?php

namespace Celios\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if (auth()->user()->hasAnyRole(['super_admin', 'super-admin', 'admin', 'panel_user'])) {
            return $next($request);
        }

        abort(403, 'Unauthorized access to admin area.');
    }
}
