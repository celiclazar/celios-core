<?php

namespace Celios\Core\Filament\Resources\Forms;

use Celios\Core\Filament\Resources\Forms\Pages\ListFormSubmissions;
use Celios\Core\Filament\Resources\Forms\Pages\ViewFormSubmission;
use Celios\Core\Filament\Resources\Forms\Schemas\FormSubmissionInfolist;
use Celios\Core\Filament\Resources\Forms\Tables\FormSubmissionsTable;
use Celios\Core\Models\FormSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Illuminate\Database\Eloquent\Builder;

class FormSubmissionResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'forms';

    protected static ?string $model = FormSubmission::class;

    protected static ?string $slug = 'form-submissions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_marketing');
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $count = static::getEloquentQuery()
            ->where('is_read', false)
            ->count();

        return $count > 0 ? (string)$count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->with('form')
            ->when($user && !$user->hasRole('super_admin'), function ($query) use ($user) {
                $query->whereHas('form', function ($q) use ($user) {
                    $userRoles = $user->roles->pluck('name')->toArray();
                    $q->where(function ($sub) use ($userRoles) {
                        $sub->whereNull('authorized_roles')
                            ->orWhere('authorized_roles', '[]')
                            ->orWhere('authorized_roles', '');
                        foreach ($userRoles as $role) {
                            $sub->orWhereJsonContains('authorized_roles', $role);
                        }
                    });
                });
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return FormSubmissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormSubmissionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormSubmissions::route('/'),
            'view' => ViewFormSubmission::route('/{record}'),
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

    public static function getNavigationLabel(): string
    {
        return __('forms.submissions');
    }

    public static function getPluralModelLabel(): string
    {
        return __('forms.submissions');
    }

    public static function getModelLabel(): string
    {
        return __('forms.submission_single');
    }
}
