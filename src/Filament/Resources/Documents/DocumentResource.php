<?php

namespace Celios\Core\Filament\Resources\Documents;

use Celios\Core\Filament\Resources\Documents\Pages\CreateDocument;
use Celios\Core\Filament\Resources\Documents\Pages\EditDocument;
use Celios\Core\Filament\Resources\Documents\Pages\ListDocuments;
use Celios\Core\Filament\Resources\Documents\Schemas\DocumentForm;
use Celios\Core\Filament\Resources\Documents\Tables\DocumentsTable;
use Celios\Core\Models\Document;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'documents';

    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_content');
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.documents');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.documents');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.document_single');
    }
}
