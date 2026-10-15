<?php

namespace Celios\Core\Filament\Resources\Sections;

use BackedEnum;
use Celios\Core\Filament\Resources\Sections\Pages\CreateSection;
use Celios\Core\Filament\Resources\Sections\Pages\EditSection;
use Celios\Core\Filament\Resources\Sections\Pages\ListSections;
use Celios\Core\Filament\Resources\Sections\Schemas\SectionForm;
use Celios\Core\Filament\Resources\Sections\Tables\SectionsTable;
use Celios\Core\Models\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_content');
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = Section::count();

            return $count > 0 ? (string) $count : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return SectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSections::route('/'),
            'create' => CreateSection::route('/create'),
            'edit' => EditSection::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.sections');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.sections');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.section_single');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof Section ? $record->name : null;
    }
}
