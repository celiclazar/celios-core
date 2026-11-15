<?php

namespace Celios\Core\Filament\Helpers;

use Filament\Schemas\Components\Tabs;

class TranslatableTabs
{
    public static function make(callable $schemaBuilder): array
    {
        $locales = function_exists('active_locales')
            ? active_locales()
            : config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']);

        return collect($locales)
            ->map(function ($label, $lang) use ($schemaBuilder) {
                return Tabs\Tab::make(strtoupper($lang)." ({$label})")
                    ->id($lang)
                    ->schema($schemaBuilder($lang));
            })
            ->values()
            ->toArray();
    }
}
