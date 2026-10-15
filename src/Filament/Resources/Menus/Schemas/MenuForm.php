<?php

namespace Celios\Core\Filament\Resources\Menus\Schemas;

use Celios\Core\Models\Menu;
use Celios\Core\Services\ThemeManager;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('menus.general_settings') ?? 'General Menu Details')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.name') ?? 'Menu Name')
                            ->placeholder('e.g. Main Header Navigation')
                            ->helperText('A recognizable name for this menu in the admin dashboard.')
                            ->required(),

                        CheckboxList::make('theme_locations')
                            ->label(__('menus.display_locations') ?? 'Theme Display Locations')
                            ->helperText('Select which theme navigation slots should display this menu on the frontend.')
                            ->options(function () {
                                return app(ThemeManager::class)->getMenuLocations();
                            })
                            ->afterStateHydrated(function ($component, ?Menu $record) {
                                if ($record) {
                                    $component->state($record->getAssignedLocations());
                                }
                            })
                            ->dehydrated(false),
                    ]),
            ]);
    }
}

