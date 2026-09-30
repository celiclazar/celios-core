<?php

namespace Celios\Core\Filament\Resources\Forms\Schemas;

use Celios\Core\Models\Form;
use Celios\Core\Models\Page;
use Celios\Core\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('fields.translations'))
                    ->icon('heroicon-o-language')
                    ->columnSpanFull()
                    ->schema([
                        Tabs::make('translations_tabs')
                            ->tabs(
                                collect(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                                    ->map(function (string $label, string $lang) {
                                        return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                            ->id($lang)
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make("title.{$lang}")
                                                            ->label(__('forms.form_title') . ' (' . strtoupper($lang) . ')')
                                                            ->required($lang === config('locales.default', 'sr'))
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, callable $set) use ($lang) {
                                                                if ($lang === config('locales.default', 'sr')) {
                                                                    $set('slug', str($state)->slug());
                                                                }
                                                            }),

                                                        TextInput::make('slug')
                                                            ->label(__('forms.form_slug'))
                                                            ->required()
                                                            ->unique(table: 'forms', column: 'slug', ignoreRecord: true)
                                                            ->visible($lang === config('locales.default', 'sr')),
                                                    ]),

                                                Textarea::make("description.{$lang}")
                                                    ->label(__('forms.form_description') . ' (' . strtoupper($lang) . ')')
                                                    ->rows(2),

                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make("submit_button_text.{$lang}")
                                                            ->label(__('forms.submit_button_text') . ' (' . strtoupper($lang) . ')')
                                                            ->placeholder(__('forms.submit')),

                                                        TextInput::make("success_message.{$lang}")
                                                            ->label(__('forms.success_message') . ' (' . strtoupper($lang) . ')')
                                                            ->placeholder(__('forms.submission_successful')),
                                                    ]),
                                            ]);
                                    })
                                    ->values()
                                    ->all()
                            ),
                    ]),

                Section::make(__('forms.form_fields'))
                    ->icon('heroicon-o-queue-list')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('fields')
                            ->label(__('forms.form_fields'))
                            ->itemLabel(fn (array $state): ?string => ($state['label']['sr'] ?? $state['label']['en'] ?? $state['label']['it'] ?? __('forms.field_label')))
                            ->collapsible()
                            ->collapsed(false)
                            ->cloneable()
                            ->reorderableWithButtons()
                            ->schema([
                                Hidden::make('key')
                                    ->default(fn () => 'field_' . Str::lower(Str::random(6)))
                                    ->dehydrated(),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('type')
                                            ->label(__('forms.field_type'))
                                            ->options([
                                                'text' => __('forms.type_text'),
                                                'email' => __('forms.type_email'),
                                                'tel' => __('forms.type_tel'),
                                                'number' => __('forms.type_number'),
                                                'textarea' => __('forms.type_textarea'),
                                                'select' => __('forms.type_select'),
                                                'radio' => __('forms.type_radio'),
                                                'checkbox' => __('forms.type_checkbox'),
                                                'file' => __('forms.type_file'),
                                                'date' => __('forms.type_date'),
                                            ])
                                            ->required()
                                            ->live(),

                                        Select::make('width')
                                            ->label(__('forms.field_width'))
                                            ->options([
                                                '12' => __('forms.width_12'),
                                                '6' => __('forms.width_6'),
                                                '4' => __('forms.width_4'),
                                                '8' => __('forms.width_8'),
                                            ])
                                            ->default('12'),
                                    ]),

                                Tabs::make('field_translations')
                                    ->tabs(
                                        collect(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                                            ->map(function (string $label, string $lang) {
                                                return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                TextInput::make("label.{$lang}")
                                                                    ->label(__('forms.field_label') . ' (' . strtoupper($lang) . ')')
                                                                    ->required($lang === config('locales.default', 'sr'))
                                                                    ->live(onBlur: true)
                                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($lang) {
                                                                        if ($lang === config('locales.default', 'sr') && filled($state)) {
                                                                            $currentKey = $get('key');
                                                                            if (blank($currentKey) || str_starts_with($currentKey, 'field_')) {
                                                                                $slug = (string) str($state)->slug('_');
                                                                                if (filled($slug)) {
                                                                                    $set('key', $slug . '_' . Str::lower(Str::random(4)));
                                                                                }
                                                                            }
                                                                        }
                                                                    }),

                                                                TextInput::make("placeholder.{$lang}")
                                                                    ->label(__('forms.field_placeholder') . ' (' . strtoupper($lang) . ')'),
                                                            ]),
                                                    ]);
                                            })
                                            ->values()
                                            ->all()
                                    ),

                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('required')
                                            ->label(__('forms.field_required'))
                                            ->default(false),

                                        TextInput::make('allowed_types')
                                            ->label(__('forms.field_allowed_types'))
                                            ->placeholder('pdf, doc, docx, jpg, png')
                                            ->default('pdf, doc, docx, jpg, png')
                                            ->visible(fn ($get) => $get('type') === 'file'),
                                    ]),

                                Textarea::make('options')
                                    ->label(__('forms.field_options'))
                                    ->helperText(__('forms.field_options_helper'))
                                    ->placeholder("Option 1\nOption 2\nOption 3")
                                    ->rows(3)
                                    ->visible(fn ($get) => in_array($get('type'), ['select', 'radio', 'checkbox'])),
                            ]),
                    ]),

                Section::make(__('forms.settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('recipient_user_ids')
                                    ->label(__('forms.recipient_users'))
                                    ->helperText(__('forms.recipient_users_helper'))
                                    ->multiple()
                                    ->options(function () {
                                        return User::query()
                                            ->where('status', 'active')
                                            ->get()
                                            ->mapWithKeys(fn (User $u) => [
                                                $u->id => "{$u->name} ({$u->email})",
                                            ])
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload(),

                                Toggle::make('send_confirmation_email')
                                    ->label(__('forms.send_confirmation_email'))
                                    ->helperText(__('forms.send_confirmation_email_helper'))
                                    ->default(false)
                                    ->live(),

                                Select::make('authorized_roles')
                                    ->label(__('forms.authorized_roles'))
                                    ->helperText(__('forms.authorized_roles_helper'))
                                    ->multiple()
                                    ->options(fn () => Role::query()->pluck('name', 'name')->all())
                                    ->preload(),

                                Toggle::make('is_active')
                                    ->label(__('forms.is_active'))
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        // Confirmation email content - ONLY visible when send_confirmation_email is TRUE
                        Section::make(__('forms.confirmation_email_subject'))
                            ->description(__('forms.send_confirmation_email_helper'))
                            ->icon('heroicon-o-envelope')
                            ->visible(fn ($get) => (bool) $get('send_confirmation_email'))
                            ->schema([
                                Tabs::make('confirmation_translations')
                                    ->tabs(
                                        collect(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                                            ->map(function (string $label, string $lang) {
                                                return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                                    ->schema([
                                                        Grid::make(1)
                                                            ->schema([
                                                                TextInput::make("confirmation_email_subject.{$lang}")
                                                                    ->label(__('forms.confirmation_email_subject') . ' (' . strtoupper($lang) . ')')
                                                                    ->placeholder(__('forms.confirmation_email_default_subject', ['form' => '...'])),

                                                                Textarea::make("confirmation_email_body.{$lang}")
                                                                    ->label(__('forms.confirmation_email_body') . ' (' . strtoupper($lang) . ')')
                                                                    ->placeholder(__('forms.confirmation_email_default_body', ['form' => '...']))
                                                                    ->rows(3),
                                                            ]),
                                                    ]);
                                            })
                                            ->values()
                                            ->all()
                                    ),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('redirect_type')
                                    ->label(__('forms.redirect_type'))
                                    ->options([
                                        'none' => __('forms.redirect_none'),
                                        'page' => __('forms.redirect_page'),
                                        'custom' => __('forms.redirect_custom'),
                                    ])
                                    ->default('none')
                                    ->required()
                                    ->live(),

                                Select::make('redirect_page_id')
                                    ->label(__('forms.redirect_select_page'))
                                    ->options(function () {
                                        $locale = app()->getLocale();
                                        return Page::query()
                                            ->get()
                                            ->mapWithKeys(fn (Page $p) => [
                                                $p->id => ($p->getTranslation('title', $locale, true) ?: $p->getTranslation('title', 'sr', true)) . ' (' . ($p->getTranslation('slug', $locale, true) ?: 'page') . ')',
                                            ])
                                            ->all();
                                    })
                                    ->visible(fn ($get) => $get('redirect_type') === 'page')
                                    ->required(fn ($get) => $get('redirect_type') === 'page')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('redirect_url')
                                    ->label(__('forms.redirect_custom_url'))
                                    ->helperText(__('forms.redirect_custom_url_helper'))
                                    ->placeholder('/hvala or https://example.com/thank-you')
                                    ->visible(fn ($get) => $get('redirect_type') === 'custom')
                                    ->required(fn ($get) => $get('redirect_type') === 'custom'),
                            ]),
                    ]),
            ]);
    }
}
