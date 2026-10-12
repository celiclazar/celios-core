<?php

namespace Celios\Core\Filament\Pages;

use BackedEnum;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Celios\Core\Models\Setting;
use Celios\Core\Services\Newsletter\NewsletterMailerService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class NewsletterSettingsPage extends Page
{
    use HasModuleToggle;

    public const MODULE_KEY = 'newsletter';

    protected static ?string $slug = 'newsletter/settings';

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.newsletter-settings';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_marketing');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.newsletter_settings');
    }

    public function getTitle(): string
    {
        return __('sidebar.newsletter_settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'newsletter_mailer_type' => setting('newsletter_mailer_type', 'app_default'),
            'newsletter_smtp_host' => setting('newsletter_smtp_host', '127.0.0.1'),
            'newsletter_smtp_port' => setting('newsletter_smtp_port', 587),
            'newsletter_smtp_username' => setting('newsletter_smtp_username', ''),
            'newsletter_smtp_password' => setting('newsletter_smtp_password', ''),
            'newsletter_smtp_encryption' => setting('newsletter_smtp_encryption', 'tls'),
            'newsletter_rate_limit' => (int) setting('newsletter_rate_limit', 0),
            'newsletter_from_email' => setting('newsletter_from_email', config('mail.from.address', '')),
            'newsletter_from_name' => setting('newsletter_from_name', config('mail.from.name', '')),
            'newsletter_double_opt_in' => (bool) setting('newsletter_double_opt_in', true),
            'newsletter_company_address' => setting('newsletter_company_address', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make(__('newsletter.settings_section_transport'))
                    ->description(__('newsletter.settings_section_transport_desc'))
                    ->schema([
                        Select::make('newsletter_mailer_type')
                            ->label(__('newsletter.settings_mailer_type'))
                            ->options([
                                'app_default' => __('newsletter.settings_mailer_app_default'),
                                'custom_smtp' => __('newsletter.settings_mailer_custom_smtp'),
                                'ses' => __('newsletter.settings_mailer_ses'),
                                'resend' => __('newsletter.settings_mailer_resend'),
                                'mailgun' => __('newsletter.settings_mailer_mailgun'),
                            ])
                            ->required()
                            ->live(),

                        Grid::make(3)
                            ->visible(fn ($get) => $get('newsletter_mailer_type') === 'custom_smtp')
                            ->schema([
                                TextInput::make('newsletter_smtp_host')
                                    ->label(__('newsletter.settings_smtp_host'))
                                    ->placeholder('mail.yourdomain.com')
                                    ->columnSpan(2),

                                TextInput::make('newsletter_smtp_port')
                                    ->label(__('newsletter.settings_smtp_port'))
                                    ->numeric()
                                    ->placeholder('587 or 465')
                                    ->columnSpan(1),

                                TextInput::make('newsletter_smtp_username')
                                    ->label(__('newsletter.settings_smtp_username'))
                                    ->placeholder('newsletter@yourdomain.com')
                                    ->columnSpan(1),

                                TextInput::make('newsletter_smtp_password')
                                    ->label(__('newsletter.settings_smtp_password'))
                                    ->password()
                                    ->revealable()
                                    ->columnSpan(1),

                                Select::make('newsletter_smtp_encryption')
                                    ->label(__('newsletter.settings_smtp_encryption'))
                                    ->options([
                                        'tls' => __('newsletter.settings_encryption_tls'),
                                        'ssl' => __('newsletter.settings_encryption_ssl'),
                                        'null' => __('newsletter.settings_encryption_none'),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make(__('newsletter.settings_section_rate_limit'))
                    ->description(__('newsletter.settings_section_rate_limit_desc'))
                    ->schema([
                        TextInput::make('newsletter_rate_limit')
                            ->label(__('newsletter.settings_rate_limit'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('newsletter.settings_rate_limit_helper')),
                    ]),

                Section::make(__('newsletter.settings_section_sender'))
                    ->description(__('newsletter.settings_section_sender_desc'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('newsletter_from_email')
                                ->label(__('newsletter.settings_from_email'))
                                ->email()
                                ->placeholder('newsletter@yourdomain.com'),

                            TextInput::make('newsletter_from_name')
                                ->label(__('newsletter.settings_from_name'))
                                ->placeholder('Celios CMS Updates'),
                        ]),
                    ]),

                Section::make(__('newsletter.settings_section_compliance'))
                    ->schema([
                        Toggle::make('newsletter_double_opt_in')
                            ->label(__('newsletter.settings_double_opt_in'))
                            ->helperText(__('newsletter.settings_double_opt_in_helper'))
                            ->default(true),

                        Textarea::make('newsletter_company_address')
                            ->label(__('newsletter.settings_company_address'))
                            ->placeholder("Celios CMS Inc.\n123 Business Boulevard, Belgrade, Serbia")
                            ->rows(3)
                            ->helperText(__('newsletter.settings_company_address_helper')),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label(__('newsletter.settings_test_connection'))
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->form([
                    TextInput::make('test_email')
                        ->label(__('newsletter.settings_recipient_email'))
                        ->email()
                        ->default(fn () => auth()->user()?->email)
                        ->required(),
                ])
                ->action(function (array $data, NewsletterMailerService $mailerService) {
                    try {
                        $mailerService->testConnection($data['test_email']);
                        Notification::make()
                            ->title(__('newsletter.test_connection_success'))
                            ->body(__('newsletter.settings_test_delivered', ['email' => $data['test_email']]))
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title(__('newsletter.settings_test_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function save(): void
    {
        $formData = $this->form->getState();

        foreach ($formData as $key => $value) {
            $val = is_bool($value) ? ($value ? '1' : '0') : (string) ($value ?? '');
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'type' => 'text']
            );
            Cache::forget("setting.{$key}");
        }

        Notification::make()
            ->title(__('newsletter.settings_saved_success'))
            ->success()
            ->send();
    }
}
