<?php

namespace Celios\Core\Filament\Resources\Newsletter\Subscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterSubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('fields.general_info'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->label(__('fields.email'))
                                ->email()
                                ->required()
                                ->unique(table: 'newsletter_subscribers', column: 'email', ignoreRecord: true),

                            Select::make('locale')
                                ->label(__('fields.language'))
                                ->options(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                                ->default(config('locales.default', 'sr'))
                                ->required(),

                            TextInput::make('first_name')
                                ->label(__('fields.first_name')),

                            TextInput::make('last_name')
                                ->label(__('fields.last_name')),

                            Select::make('status')
                                ->label(__('fields.status'))
                                ->options([
                                    'pending' => __('newsletter.status_pending'),
                                    'active' => __('newsletter.status_active'),
                                    'unsubscribed' => __('newsletter.status_unsubscribed'),
                                    'bounced' => __('newsletter.status_bounced'),
                                ])
                                ->default('active')
                                ->required(),

                            TextInput::make('signup_source')
                                ->label('Signup Source')
                                ->default('admin_manual')
                                ->disabled(),
                        ]),
                    ]),

                Section::make('Consent & Audit Details')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('ip_address')
                                ->label('IP Address')
                                ->disabled(),

                            DateTimePicker::make('verified_at')
                                ->label('Verified At'),

                            DateTimePicker::make('unsubscribed_at')
                                ->label('Unsubscribed At'),

                            TextInput::make('unsubscribe_token')
                                ->label('Unsubscribe Token')
                                ->disabled(),
                        ]),
                    ]),
            ]);
    }
}
