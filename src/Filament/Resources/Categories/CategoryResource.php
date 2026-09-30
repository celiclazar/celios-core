<?php

namespace Celios\Core\Filament\Resources\Categories;

use Celios\Core\Filament\Resources\Categories\Pages\CreateCategory;
use Celios\Core\Filament\Resources\Categories\Pages\EditCategory;
use Celios\Core\Filament\Resources\Categories\Pages\ListCategories;
use Celios\Core\Filament\Resources\Categories\Schemas\CategoryForm;
use Celios\Core\Filament\Resources\Categories\Tables\CategoriesTable;
use Celios\Core\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'blog';

    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_content');
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.categories');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.categories');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.category_single');
    }
}
