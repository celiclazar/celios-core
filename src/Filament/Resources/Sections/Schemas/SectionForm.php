<?php

namespace Celios\Core\Filament\Resources\Sections\Schemas;

use Celios\Core\Filament\Helpers\SectionSchemaHelper;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(array_merge(
                SectionSchemaHelper::getSchema(includeNameFields: true),
                [
                    Toggle::make('is_active')
                        ->label('Aktivna sekcija')
                        ->default(true),
                ]
            ));
    }
}
