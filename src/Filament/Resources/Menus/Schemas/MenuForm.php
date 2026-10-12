<?php

namespace Celios\Core\Filament\Resources\Menus\Schemas;

use Celios\Core\Models\Menu;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label(__('fields.key') ?? 'Menu Key / Location')
                    ->placeholder('e.g. header, footer, sidebar')
                    ->helperText(function (?Menu $record): string {
                        if ($record?->isSystem()) {
                            return 'System menu key (protected, cannot be changed).';
                        }
                        if ($record) {
                            return 'Menu key is locked after creation to protect theme bindings.';
                        }

                        return 'Unique identifier key used in theme templates (e.g. header, footer).';
                    })
                    ->datalist(['header', 'footer', 'sidebar', 'mobile'])
                    ->dehydrateStateUsing(fn (?string $state) => Str::slug((string) $state, '_'))
                    ->disabled(fn (?Menu $record) => $record !== null)
                    ->dehydrated(fn (?Menu $record) => $record === null)
                    ->unique(Menu::class, 'key', ignoreRecord: true)
                    ->required(),

                TextInput::make('name')
                    ->label(__('fields.name') ?? 'Menu Name')
                    ->placeholder('e.g. Main Header Navigation')
                    ->required(),
            ]);
    }
}
