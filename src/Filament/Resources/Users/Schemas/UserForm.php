<?php

namespace Celios\Core\Filament\Resources\Users\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn () => __('fields.account_information'))
                    ->description(fn () => __('fields.account_information_desc'))
                    ->schema([
                        TextInput::make('name')
                            ->label(fn () => __('fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->label(fn () => __('fields.username'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(fn () => __('fields.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(fn () => __('fields.phone'))
                            ->tel()
                            ->maxLength(20),
                        CuratorPicker::make('avatar')
                            ->label(fn () => __('fields.user_avatar'))
                            ->buttonLabel(fn () => __('fields.choose_picture'))
                            ->directory('avatars')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('500')
                            ->imageResizeTargetHeight('500')
                            ->relationship('avatar', 'id')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(fn () => __('fields.security_permissions'))
                    ->description(fn () => __('fields.security_permissions_desc'))
                    ->schema([
                        TextInput::make('password')
                            ->label(fn () => __('fields.password'))
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Select::make('roles')
                            ->label(fn () => __('fields.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])->columns(2),

                Section::make(fn () => __('fields.status_activity'))
                    ->schema([
                        Toggle::make('status')
                            ->label(fn () => __('fields.status_active'))
                            ->trueValue('active')
                            ->falseValue('inactive')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default('active'),

                        DateTimePicker::make('email_verified_at')
                            ->label(fn () => __('fields.verified_at'))
                            ->disabled(),

                        DateTimePicker::make('last_login_at')
                            ->label(fn () => __('fields.last_login'))
                            ->disabled(),
                    ])->columns(3),
            ]);
    }
}
