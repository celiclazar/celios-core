<?php

namespace Celios\Core\Filament\Pages;

use Celios\Core\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
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

class CookieSettingsPage extends Page
{
    protected static ?string $slug = 'settings/cookies';

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-shield-check';

    protected string $view = 'filament.pages.cookie-settings';

    protected static ?int $navigationSort = 11;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.cookie_settings');
    }

    public function getTitle(): string
    {
        return __('cookies.title');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->hasRole('admin') || $user->can('page_CookieSettingsPage');
    }

    public function mount(): void
    {
        $this->form->fill([
            'cookie_consent_enabled' => (bool) setting('cookie_consent_enabled', '1'),
            'cookie_banner_position' => setting('cookie_banner_position', 'bottom_banner'),
            'cookie_consent_expiry_days' => (int) setting('cookie_consent_expiry_days', 365),
            'cookie_privacy_url' => setting('cookie_privacy_url', ''),
            'cookie_banner_title' => setting('cookie_banner_title', ''),
            'cookie_banner_description' => setting('cookie_banner_description', ''),
            'cookie_cat_analytics_enabled' => (bool) setting('cookie_cat_analytics_enabled', '1'),
            'cookie_analytics_scripts' => setting('cookie_analytics_scripts', ''),
            'cookie_cat_marketing_enabled' => (bool) setting('cookie_cat_marketing_enabled', '1'),
            'cookie_marketing_scripts' => setting('cookie_marketing_scripts', ''),
            'cookie_cat_functional_enabled' => (bool) setting('cookie_cat_functional_enabled', '1'),
            'cookie_functional_scripts' => setting('cookie_functional_scripts', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make(__('cookies.section_general'))
                    ->description(__('cookies.section_general_desc'))
                    ->schema([
                        Toggle::make('cookie_consent_enabled')
                            ->label(__('cookies.enabled'))
                            ->helperText(__('cookies.enabled_help'))
                            ->default(true),

                        Grid::make(3)->schema([
                            Select::make('cookie_banner_position')
                                ->label(__('cookies.position'))
                                ->options([
                                    'bottom_banner' => __('cookies.position_bottom_banner'),
                                    'bottom_right' => __('cookies.position_bottom_right'),
                                    'bottom_left' => __('cookies.position_bottom_left'),
                                    'center_modal' => __('cookies.position_center_modal'),
                                ])
                                ->default('bottom_banner')
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('cookie_consent_expiry_days')
                                ->label(__('cookies.expiry_days'))
                                ->numeric()
                                ->default(365)
                                ->helperText(__('cookies.expiry_days_help'))
                                ->columnSpan(1),

                            TextInput::make('cookie_privacy_url')
                                ->label(__('cookies.privacy_url'))
                                ->placeholder('/politika-privatnosti or https://example.com/privacy')
                                ->helperText(__('cookies.privacy_url_help'))
                                ->columnSpan(1),
                        ]),
                    ]),

                Section::make(__('cookies.section_content'))
                    ->description(__('cookies.section_content_desc'))
                    ->schema([
                        TextInput::make('cookie_banner_title')
                            ->label(__('cookies.banner_title'))
                            ->placeholder(__('cookies.banner_title_default'))
                            ->helperText('Leave blank to use the default localized title.'),

                        Textarea::make('cookie_banner_description')
                            ->label(__('cookies.banner_description'))
                            ->placeholder(__('cookies.banner_description_default'))
                            ->rows(3)
                            ->helperText('Leave blank to use the default localized message.'),
                    ]),

                Section::make(__('cookies.section_categories'))
                    ->description(__('cookies.section_categories_desc'))
                    ->schema([
                        // Necessary section notice
                        Placeholder::make('necessary_info')
                            ->label(__('cookies.cat_necessary'))
                            ->content(__('cookies.cat_necessary_desc') . ' — (' . __('cookies.cat_necessary_always_active') . ')'),

                        // Analytics
                        Toggle::make('cookie_cat_analytics_enabled')
                            ->label(__('cookies.cat_analytics_enabled'))
                            ->helperText(__('cookies.cat_analytics_desc'))
                            ->default(true)
                            ->live(),

                        Textarea::make('cookie_analytics_scripts')
                            ->label(__('cookies.analytics_head_scripts'))
                            ->placeholder("<script>\n  // Google Analytics / GA4 / Plausible snippet\n  console.log('Analytics loaded');\n</script>")
                            ->rows(5)
                            ->helperText(__('cookies.analytics_head_scripts_help'))
                            ->visible(fn ($get) => (bool) $get('cookie_cat_analytics_enabled')),

                        // Marketing
                        Toggle::make('cookie_cat_marketing_enabled')
                            ->label(__('cookies.cat_marketing_enabled'))
                            ->helperText(__('cookies.cat_marketing_desc'))
                            ->default(true)
                            ->live(),

                        Textarea::make('cookie_marketing_scripts')
                            ->label(__('cookies.marketing_head_scripts'))
                            ->placeholder("<script>\n  // Meta Pixel / Google Ads snippet\n  console.log('Marketing loaded');\n</script>")
                            ->rows(5)
                            ->helperText(__('cookies.marketing_head_scripts_help'))
                            ->visible(fn ($get) => (bool) $get('cookie_cat_marketing_enabled')),

                        // Functional
                        Toggle::make('cookie_cat_functional_enabled')
                            ->label(__('cookies.cat_functional_enabled'))
                            ->helperText(__('cookies.cat_functional_desc'))
                            ->default(true)
                            ->live(),

                        Textarea::make('cookie_functional_scripts')
                            ->label(__('cookies.functional_scripts'))
                            ->placeholder("<script>\n  // Functional widgets / chat snippets\n</script>")
                            ->rows(4)
                            ->helperText(__('cookies.functional_scripts_help'))
                            ->visible(fn ($get) => (bool) $get('cookie_cat_functional_enabled')),
                    ]),
            ]);
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
            ->title(__('cookies.settings_saved'))
            ->body(__('cookies.settings_saved_body'))
            ->success()
            ->send();
    }
}
