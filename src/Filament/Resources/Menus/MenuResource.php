<?php

namespace Celios\Core\Filament\Resources\Menus;

use Celios\Core\Filament\Resources\Menus\Pages\CreateMenu;
use Celios\Core\Filament\Resources\Menus\Pages\EditMenu;
use Celios\Core\Filament\Resources\Menus\Pages\ListMenus;
use Celios\Core\Filament\Resources\Menus\Pages\ManageMenuLinks;
use Celios\Core\Filament\Resources\Menus\Schemas\MenuForm;
use Celios\Core\Filament\Resources\Menus\Tables\MenusTable;
use Celios\Core\Models\Menu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_content');
    }

    protected static ?string $recordTitleAttribute = 'Menus';

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
            'manage' => ManageMenuLinks::route('/{record}/manage'),
            ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.menus');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.menus');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.menu_single');
    }
}
