<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class HeroShowcaseBlock
{
    public static function make(): Block
    {
        return Block::make('hero_showcase')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.hero_showcase_title') ?? 'Hero Showcase', $state, ['heading', 'badge_text', 'subtitle']))
            ->icon('heroicon-o-sparkles')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("badge_text.{$lang}")
                                ->label('Glavni bedž (' . strtoupper($lang) . ')')
                                ->default('Celios CMS • Laravel 11 + Filament v3'),

                            TextInput::make("badge_version.{$lang}")
                                ->label('Verzija (' . strtoupper($lang) . ')')
                                ->default('v3.4.2 Production Ready'),

                            TextInput::make("badge_build.{$lang}")
                                ->label('Build oznaka (' . strtoupper($lang) . ')')
                                ->default('build#8914-release'),

                            TextInput::make("heading.{$lang}")
                                ->label('Glavni naslov (' . strtoupper($lang) . ')')
                                ->required($lang === 'sr')
                                ->default('Modularni CMS kreiran za developere i dizajnere koji cene'),

                            TextInput::make("highlighted_text.{$lang}")
                                ->label('Podvučeni/Akcentovani tekst (' . strtoupper($lang) . ')')
                                ->default('brzinu, estetiku i kontrolu.'),

                            Textarea::make("subtitle.{$lang}")
                                ->label('Opis / Podnaslov (' . strtoupper($lang) . ')')
                                ->rows(3)
                                ->default('Potpuna sloboda u kreiranju modernih portfolio sajtova, digitalnih arhiva i višejezičnih publikacija bez sporih pluginova. Izgrađen na temeljima Laravel i Filament v3 ekosistema.'),

                            TextInput::make("button_primary_text.{$lang}")
                                ->label('Tekst primarnog dugmeta (' . strtoupper($lang) . ')')
                                ->default('Pokreni Test Mode (Admin Demo)'),

                            TextInput::make("button_secondary_text.{$lang}")
                                ->label('Tekst sekundarnog dugmeta (' . strtoupper($lang) . ')')
                                ->default('Pregledaj Mogućnosti'),

                            TextInput::make("button_tertiary_text.{$lang}")
                                ->label('Tekst tercijarnog dugmeta (' . strtoupper($lang) . ')')
                                ->default('Pogledaj Portfolio'),
                        ])
                    ),

                TextInput::make('button_primary_url')
                    ->label('URL primarnog dugmeta')
                    ->default('#test-mode-banner'),

                TextInput::make('button_secondary_url')
                    ->label('URL sekundarnog dugmeta')
                    ->default('#mogucnosti'),

                TextInput::make('button_tertiary_url')
                    ->label('URL tercijarnog dugmeta')
                    ->default('#portfolio'),

                Repeater::make('metrics')
                    ->label('Metrike / Benchmark kartice (4 preporučeno)')
                    ->schema([
                        TextInput::make('label')->label('Naziv / Opis (npr. Benchmark TTFB)')->required(),
                        TextInput::make('value')->label('Vrednost (npr. 32ms, 100% Native)')->required(),
                        TextInput::make('badge')->label('Bedž oznaka (npr. 99.8th perc, Green)'),
                        TextInput::make('note')->label('Napomena (npr. Laravel Octane + Redis keš)'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->defaultItems(4),
            ]);
    }
}
