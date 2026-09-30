<?php

namespace Celios\Core\Filament\Pages;

use Celios\Core\Models\Setting;
use Celios\Core\Services\Newsletter\NewsletterMailerService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Celios\Core\Filament\Traits\HasModuleToggle;
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
                Section::make('Mailing Transport & Hosting Flexibility')
                    ->description('Choose whether to send newsletters via your hosting server / custom SMTP or via a dedicated cloud service (Amazon SES, Resend, etc.).')
                    ->schema([
                        Select::make('newsletter_mailer_type')
                            ->label('Mail Transport Provider')
                            ->options([
                                'app_default' => 'Application Default (uses Laravel .env configuration)',
                                'custom_smtp' => 'Custom SMTP (Hosting Server / cPanel / Plesk)',
                                'ses' => 'Amazon SES (AWS)',
                                'resend' => 'Resend (API/SMTP)',
                                'mailgun' => 'Mailgun',
                            ])
                            ->required()
                            ->live(),

                        Grid::make(3)
                            ->visible(fn ($get) => $get('newsletter_mailer_type') === 'custom_smtp')
                            ->schema([
                                TextInput::make('newsletter_smtp_host')
                                    ->label('SMTP Host')
                                    ->placeholder('mail.yourdomain.com')
                                    ->columnSpan(2),

                                TextInput::make('newsletter_smtp_port')
                                    ->label('SMTP Port')
                                    ->numeric()
                                    ->placeholder('587 or 465')
                                    ->columnSpan(1),

                                TextInput::make('newsletter_smtp_username')
                                    ->label('SMTP Username')
                                    ->placeholder('newsletter@yourdomain.com')
                                    ->columnSpan(1),

                                TextInput::make('newsletter_smtp_password')
                                    ->label('SMTP Password')
                                    ->password()
                                    ->revealable()
                                    ->columnSpan(1),

                                Select::make('newsletter_smtp_encryption')
                                    ->label('Encryption')
                                    ->options([
                                        'tls' => 'TLS (STARTTLS)',
                                        'ssl' => 'SSL',
                                        'null' => 'None',
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Sending Rate Limiting & Throttling')
                    ->description('Protect your hosting server or email provider from being rate-limited or blacklisted.')
                    ->schema([
                        TextInput::make('newsletter_rate_limit')
                            ->label('Max Emails Per Minute')
                            ->numeric()
                            ->default(0)
                            ->helperText('Recommended for shared hosting / cPanel: 20-30 emails/min. Set to 0 for unlimited / fast sending (Amazon SES, Resend, VPS).'),
                    ]),

                Section::make('Sender Identity')
                    ->description('Default from address and display name for all outgoing newsletters.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('newsletter_from_email')
                                ->label('Sender Email Address')
                                ->email()
                                ->placeholder('newsletter@yourdomain.com'),

                            TextInput::make('newsletter_from_name')
                                ->label('Sender Display Name')
                                ->placeholder('Celios CMS Updates'),
                        ]),
                    ]),

                Section::make('Compliance & Legal (GDPR & CAN-SPAM)')
                    ->schema([
                        Toggle::make('newsletter_double_opt_in')
                            ->label('Enable Double Opt-In (Verification Email)')
                            ->helperText('When enabled, subscribers must click a verification link sent to their inbox before receiving campaigns (strongly recommended for GDPR).')
                            ->default(true),

                        Textarea::make('newsletter_company_address')
                            ->label('Physical Postal Address')
                            ->placeholder("Celios CMS Inc.\n123 Business Boulevard, Belgrade, Serbia")
                            ->rows(3)
                            ->helperText('Physical postal address shown in the footer of all marketing emails (legally required by CAN-SPAM and international standards).'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Test Mailer Connection')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->form([
                    TextInput::make('test_email')
                        ->label('Recipient Email')
                        ->email()
                        ->default(fn () => auth()->user()?->email)
                        ->required(),
                ])
                ->action(function (array $data, NewsletterMailerService $mailerService) {
                    try {
                        $mailerService->testConnection($data['test_email']);
                        Notification::make()
                            ->title(__('newsletter.test_connection_success'))
                            ->body('A test email was delivered to ' . $data['test_email'])
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Mailer Connection Failed')
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
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }
}
