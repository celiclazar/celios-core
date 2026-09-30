<?php

namespace Celios\Core\Filament\Pages;

use Celios\Core\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class ThemeSettingsPage extends Page
{
    protected static ?string $slug = 'settings/themes';

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-paint-brush';

    protected string $view = 'filament.pages.theme-settings';

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.theme_settings');
    }

    public function getTitle(): string
    {
        return __('sidebar.theme_settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'admin_theme' => setting('admin_theme', 'ocean'),
            'frontend_theme' => setting('frontend_theme', 'ocean'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make('Admin Panel Theme')
                    ->description('Customize the visual appearance of your administration dashboard. Changes apply instantly.')
                    ->schema([
                        Radio::make('admin_theme')
                            ->label('Select Active Admin Theme')
                            ->options([
                                'ocean' => 'Ocean Blue — Corporate blue accents with clean slate surfaces (Default)',
                                'emerald' => 'Emerald Forest — Vivid emerald & mint accents with organic surfaces',
                                'midnight' => 'Midnight Obsidian — Ultra-modern violet & amber accents with deep slate/obsidian contrast',
                            ])
                            ->default('ocean')
                            ->required(),
                    ]),

                Section::make('Frontend Website Theme')
                    ->description('Customize the visual styling for the public website (Home, Blog, Documents, etc.) independently.')
                    ->schema([
                        Radio::make('frontend_theme')
                            ->label('Select Active Frontend Theme')
                            ->options([
                                'ocean' => 'Ocean Blue — Original brand styling with deep corporate navy and cobalt primary (Default)',
                                'emerald' => 'Emerald Forest — Modern fresh green tone with mint surfaces and sky secondary accents',
                                'midnight' => 'Midnight Obsidian — Sleek dark aesthetic with indigo/violet primary and zinc surfaces',
                            ])
                            ->default('ocean')
                            ->required(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $formData = $this->form->getState();

        foreach ($formData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) ($value ?? 'ocean'), 'type' => 'text']
            );
            Cache::forget("setting.{$key}");
        }

        Notification::make()
            ->title('Theme settings saved successfully!')
            ->body('Admin and frontend themes have been updated.')
            ->success()
            ->send();

        // Refresh the page to immediately reflect the new admin theme colors & tokens
        $this->redirect(static::getUrl());
    }
}
