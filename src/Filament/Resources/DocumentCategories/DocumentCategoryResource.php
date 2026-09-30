<?php

namespace Celios\Core\Filament\Resources\DocumentCategories;

use Celios\Core\Filament\Resources\DocumentCategories\Pages\CreateDocumentCategory;
use Celios\Core\Filament\Resources\DocumentCategories\Pages\EditDocumentCategory;
use Celios\Core\Filament\Resources\DocumentCategories\Pages\ListDocumentCategories;
use Celios\Core\Filament\Resources\DocumentCategories\Schemas\DocumentCategoryForm;
use Celios\Core\Filament\Resources\DocumentCategories\Tables\DocumentCategoriesTable;
use Celios\Core\Models\DocumentCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Filament\Tables\Table;

class DocumentCategoryResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'documents';

    protected static ?string $model = DocumentCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_content');
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentCategories::route('/'),
            'create' => CreateDocumentCategory::route('/create'),
            'edit' => EditDocumentCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.document_categories');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.document_categories');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.document_category_single');
    }
}
