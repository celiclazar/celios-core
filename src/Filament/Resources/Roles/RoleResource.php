<?php

declare(strict_types=1);

namespace Celios\Core\Filament\Resources\Roles;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as BaseRoleResource;
use Celios\Core\Filament\Resources\Roles\Pages\CreateRole;
use Celios\Core\Filament\Resources\Roles\Pages\EditRole;
use Celios\Core\Filament\Resources\Roles\Pages\ListRoles;
use Celios\Core\Filament\Resources\Roles\Pages\ViewRole;
use Illuminate\Database\Eloquent\Builder;
use Override;

class RoleResource extends BaseRoleResource
{
    #[Override]
    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make()
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule): \Illuminate\Validation\Rules\Unique => \BezhanSalleh\FilamentShield\Support\Utils::isTenancyEnabled() ? $rule->where(\BezhanSalleh\FilamentShield\Support\Utils::getTenantModelForeignKey(), \Filament\Facades\Filament::getTenant()?->id) : $rule
                                    )
                                    ->rules([\Illuminate\Validation\Rule::notIn(['super_admin', 'super-admin'])])
                                    ->disabled(fn (?\Illuminate\Database\Eloquent\Model $record): bool => in_array($record?->name, ['admin', 'panel_user', 'super_admin', 'super-admin'], true))
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255),

                                \Filament\Forms\Components\TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(\BezhanSalleh\FilamentShield\Support\Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),

                                \Filament\Forms\Components\Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    ->default(\Filament\Facades\Filament::getTenant()?->id)
                                    ->options(fn (): array => in_array(\BezhanSalleh\FilamentShield\Support\Utils::getTenantModel(), [null, '', '0'], true) ? [] : \BezhanSalleh\FilamentShield\Support\Utils::getTenantModel()::pluck('name', 'id')->toArray())
                                    ->visible(fn (): bool => static::shield()->isCentralApp() && \BezhanSalleh\FilamentShield\Support\Utils::isTenancyEnabled())
                                    ->dehydrated(fn (): bool => static::shield()->isCentralApp() && \BezhanSalleh\FilamentShield\Support\Utils::isTenancyEnabled()),
                                static::getSelectAllFormComponent(),

                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                static::getShieldFormComponents(),
            ]);
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && ! ($user->hasRole('super_admin') || $user->hasRole('super-admin'))) {
            $query->whereNotIn('name', ['super_admin', 'super-admin']);
        }

        return $query;
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
