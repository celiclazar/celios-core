<?php

namespace Celios\Core\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->placeholder('Enter a unique key')
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
