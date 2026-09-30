<?php

namespace Celios\Core\Filament\Helpers;

use Celios\Core\Support\Locales;
use Filament\Schemas\Components\Tabs;

class TranslatableTabs
{
    public static function make(callable $schemaBuilder): array
    {
        return collect(Locales::all())
            ->map(function ($lang) use ($schemaBuilder) {
                return Tabs\Tab::make(strtoupper($lang))
                    ->schema($schemaBuilder($lang));
            })
            ->toArray();
    }
}
