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
        $supportedLocales = function_exists('active_locale_codes')
            ? active_locale_codes()
            : array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']));

        $locale = $request->segment(1);

        if (in_array($locale, $supportedLocales, true)) {
            App::setLocale($locale);
        } else {
            $defaultLocale = function_exists('default_locale')
                ? default_locale()
                : config('locales.default', config('app.fallback_locale', 'sr'));
            App::setLocale($defaultLocale);
        }

        return $next($request);
    }
}
