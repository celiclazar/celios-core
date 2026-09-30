<?php

namespace Celios\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['sr', 'en', 'it'];


        $locale = $request->segment(1);

        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        } else {
            // Fallback ako u URL-u nema jezika (npr. direktan dolazak na sajt "domain.com/o-nama")
            // Možeš preusmeriti na podrazumevani jezik (npr. 'sr') ili pročitati iz config-a
            $defaultLocale = config('app.fallback_locale', 'sr');
            App::setLocale($defaultLocale);
        }

        return $next($request);
    }
}
