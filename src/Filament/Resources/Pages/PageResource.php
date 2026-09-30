<?php

namespace Celios\Core\Filament\Resources\Pages;

use Celios\Core\Filament\Resources\Pages\Pages\CreatePage;
use Celios\Core\Filament\Resources\Pages\Pages\EditPage;
use Celios\Core\Filament\Resources\Pages\Pages\ListPages;
use Celios\Core\Filament\Resources\Pages\Schemas\PageForm;
use Celios\Core\Filament\Resources\Pages\Tables\PagesTable;
use Celios\Core\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_content');
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) (\Celios\Core\Models\Page::count() ?: 14);
        } catch (\Throwable $e) {
            return '14';
        }
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'gray';
    }

    protected static ?string $recordTitleAttribute = 'Pages';

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \Celios\Core\Filament\Resources\Pages\RelationManagers\RevisionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.pages');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.pages');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.page_single');
    }
}
