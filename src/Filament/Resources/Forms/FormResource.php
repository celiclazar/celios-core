<?php

namespace Celios\Core\Filament\Resources\Forms;

use Celios\Core\Filament\Resources\Forms\Pages\CreateForm;
use Celios\Core\Filament\Resources\Forms\Pages\EditForm;
use Celios\Core\Filament\Resources\Forms\Pages\ListForms;
use Celios\Core\Filament\Resources\Forms\Schemas\FormForm;
use Celios\Core\Filament\Resources\Forms\Tables\FormsTable;
use Celios\Core\Models\Form;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Filament\Tables\Table;

class FormResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'forms';

    protected static ?string $model = Form::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_marketing');
    }

    public static function form(Schema $schema): Schema
    {
        return FormForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('forms.forms');
    }

    public static function getPluralModelLabel(): string
    {
        return __('forms.forms');
    }

    public static function getModelLabel(): string
    {
        return __('forms.form_single');
    }

    public static function getNavigationItemActiveRoutePattern(): string | array
    {
        return [
            static::getRouteBaseName() . '.index',
            static::getRouteBaseName() . '.create',
            static::getRouteBaseName() . '.edit',
            static::getRouteBaseName() . '.view',
        ];
    }
}
