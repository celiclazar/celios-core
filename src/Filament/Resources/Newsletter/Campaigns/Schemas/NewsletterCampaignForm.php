<?php

namespace Celios\Core\Filament\Resources\Newsletter\Campaigns\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class NewsletterCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        $locales = config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']);

        return $schema
            ->columns(1)
            ->components([
                Section::make('Campaign Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('Internal Campaign Title')
                                ->required()
                                ->placeholder('e.g. Summer Promo 2026')
                                ->columnSpan(1),

                            Select::make('target_locales')
                                ->label('Target Audience Locales')
                                ->multiple()
                                ->options([
                                    'all' => 'All Locales (Everyone)',
                                    'sr' => 'Srpski (sr)',
                                    'en' => 'English (en)',
                                    'it' => 'Italiano (it)',
                                ])
                                ->default(['all'])
                                ->required()
                                ->columnSpan(1),

                            DateTimePicker::make('scheduled_at')
                                ->label('Schedule For (Optional)')
                                ->helperText('Leave empty to send manually when ready.')
                                ->columnSpan(1),

                            Select::make('status')
                                ->label(__('fields.status'))
                                ->options([
                                    'draft' => __('newsletter.campaign_status_draft'),
                                    'scheduled' => __('newsletter.campaign_status_scheduled'),
                                    'sending' => __('newsletter.campaign_status_sending'),
                                    'sent' => __('newsletter.campaign_status_sent'),
                                    'cancelled' => __('newsletter.campaign_status_cancelled'),
                                ])
                                ->default('draft')
                                ->disabled(fn ($record) => in_array($record?->status, ['sending', 'sent']))
                                ->columnSpan(1),
                        ]),
                    ]),

                Section::make('Campaign Content & Localization')
                    ->description('Available merge tags: {{first_name}}, {{last_name}}, {{name}}, {{email}}, {{unsubscribe_url}}')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Tabs::make('campaign_content_tabs')
                            ->tabs(
                                collect($locales)->map(function (string $label, string $lang) {
                                    return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                        ->id($lang)
                                        ->schema([
                                            TextInput::make("subject.{$lang}")
                                                ->label(__('fields.title') . ' / Email Subject (' . strtoupper($lang) . ')')
                                                ->required($lang === config('locales.default', 'sr'))
                                                ->placeholder('Exciting updates from Celios CMS!'),

                                            TextInput::make("preview_text.{$lang}")
                                                ->label('Inbox Preview Text (' . strtoupper($lang) . ')')
                                                ->placeholder('Brief teaser shown in the inbox summary snippet'),

                                            RichEditor::make("content.{$lang}")
                                                ->label('Email Body (' . strtoupper($lang) . ')')
                                                ->required($lang === config('locales.default', 'sr'))
                                                ->toolbarButtons([
                                                    'bold',
                                                    'italic',
                                                    'underline',
                                                    'strike',
                                                    'link',
                                                    'h2',
                                                    'h3',
                                                    'bulletList',
                                                    'orderedList',
                                                    'blockquote',
                                                ]),
                                        ]);
                                })->toArray()
                            ),
                    ]),
            ]);
    }
}
