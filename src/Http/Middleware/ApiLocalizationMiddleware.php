<?php

namespace Celios\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ApiLocalizationMiddleware
{
    /**
     * Handle an incoming request.
     * Sets the application locale based on the 'Accept-Language' header or '?lang=' query parameter.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = function_exists('active_locale_codes')
            ? active_locale_codes()
            : array_keys(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']));

        // 1. Check for query parameter (?lang=en or ?locale=en)
        $locale = $request->query('lang', $request->query('locale'));

        // 2. If not in query, check Accept-Language header (e.g. "en-US,en;q=0.9,sr;q=0.8")
        if (! $locale) {
            $header = $request->header('Accept-Language');
            if ($header) {
                // Parse primary language tag
                $primary = strtolower(substr(trim(explode(',', $header)[0]), 0, 2));
                if (in_array($primary, $supportedLocales, true)) {
                    $locale = $primary;
                }
            }
        }

        // 3. Fallback to default locale if not supported
        if ($locale && in_array($locale, $supportedLocales, true)) {
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
