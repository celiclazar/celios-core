<?php

namespace Celios\Core\Filament\Resources\Activities;

use Celios\Core\Filament\Resources\Activities\Pages\ListActivities;
use Celios\Core\Filament\Resources\Activities\Pages\ViewActivity;
use Celios\Core\Filament\Resources\Activities\Schemas\ActivityForm;
use Celios\Core\Filament\Resources\Activities\Schemas\ActivityInfolist;
use Celios\Core\Filament\Resources\Activities\Tables\ActivitiesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'activity_log';

    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    protected static ?string $recordTitleAttribute = 'Activity';

    public static function form(Schema $schema): Schema
    {
        return ActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
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
            'index' => ListActivities::route('/'),
            //'create' => CreateActivity::route('/create'),
            'view' => ViewActivity::route('/{record}'),
            //'edit' => EditActivity::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.activities');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.activities');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.activity_single');
    }
}
