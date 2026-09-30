<?php

namespace Celios\Core\Filament\Pages;

use Celios\Core\Services\Sitemap\SitemapGenerator;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class SitemapManager extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    protected string $view = 'filament.pages.sitemap-manager';

    public array $stats = [];

    public function mount(SitemapGenerator $generator): void
    {
        $this->loadStats($generator);
    }

    public function loadStats(?SitemapGenerator $generator = null): void
    {
        $generator = $generator ?: app(SitemapGenerator::class);
        $this->stats = $generator->getStats();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSitemap')
                ->label('Regenerate Sitemap')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function (SitemapGenerator $generator) {
                    $generator->writeToFile();
                    $this->loadStats($generator);

                    Notification::make()
                        ->title('Sitemap regenerated successfully!')
                        ->body("Indexed {$this->stats['total_urls']} URLs to {$this->stats['static_path']}")
                        ->success()
                        ->send();
                }),

            Action::make('clearCache')
                ->label('Clear Cache')
                ->icon('heroicon-o-trash')
                ->color('gray')
                ->action(function (SitemapGenerator $generator) {
                    $generator->clearCache();
                    $this->loadStats($generator);

                    Notification::make()
                        ->title('Sitemap cache cleared.')
                        ->success()
                        ->send();
                }),

            Action::make('viewXml')
                ->label('View sitemap.xml')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->openUrlInNewTab()
                ->url(fn (): string => url('/sitemap.xml')),

            Action::make('viewRobots')
                ->label('View robots.txt')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->openUrlInNewTab()
                ->url(fn (): string => url('/robots.txt')),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (): Collection {
                $urls = app(SitemapGenerator::class)->getUrls();
                return $urls->map(function ($item, $index) {
                    $item['id'] = $index + 1;
                    return $item;
                });
            })
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'page' => 'success',
                        'post' => 'info',
                        'category' => 'warning',
                        'static' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                TextColumn::make('title')
                    ->label('Title / Section')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('loc')
                    ->label('URL')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('URL copied to clipboard')
                    ->url(fn (array $record): string => $record['loc'])
                    ->openUrlInNewTab()
                    ->color('primary'),

                TextColumn::make('locale')
                    ->label('Locale')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?: '-')),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->alignCenter(),

                TextColumn::make('changefreq')
                    ->label('Change Frequency')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('lastmod')
                    ->label('Last Modified')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.sitemap_manager', [], 'en') !== 'sidebar.sitemap_manager'
            ? __('sidebar.sitemap_manager')
            : 'Sitemap';
    }

    public function getTitle(): string
    {
        return __('sidebar.sitemap_manager', [], 'en') !== 'sidebar.sitemap_manager'
            ? __('sidebar.sitemap_manager')
            : 'Sitemap Manager';
    }
}
