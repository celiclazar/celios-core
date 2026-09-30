<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class PortfolioShowcaseBlock
{
    public static function make(): Block
    {
        return Block::make('portfolio_showcase')
            ->label(fn (?array $state) => BlockLabelHelper::withCount(__('blocks.portfolio_showcase_title') ?? 'Portfolio Showcase', $state, 'projects', 'projekat', 'projekata'))
            ->icon('heroicon-o-briefcase')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("section_tag.{$lang}")
                                ->label('Tag sekcije (' . strtoupper($lang) . ')')
                                ->default('Realizovani Projekti'),

                            TextInput::make("section_title.{$lang}")
                                ->label('Naslov sekcije (' . strtoupper($lang) . ')')
                                ->required($lang === 'sr')
                                ->default('Izrađeno u Celios CMS-u'),

                            Textarea::make("section_desc.{$lang}")
                                ->label('Opis sekcije (' . strtoupper($lang) . ')')
                                ->rows(2)
                                ->default('Odabrani digitalni prostori, portfoliji i publikacije lansirani na našoj platformi.'),

                            TextInput::make("count_label.{$lang}")
                                ->label('Oznaka broja projekata (' . strtoupper($lang) . ')')
                                ->default('Prikazano: 4 od 28 projekata'),
                        ])
                    ),

                Repeater::make('projects')
                    ->label('Projekti u portfoliju')
                    ->schema([
                        TextInput::make('image_url')
                            ->label('URL slike projekta')
                            ->default('https://lh3.googleusercontent.com/aida-public/AB6AXuCKpBc5Pvv1WS3DmXJljzhXspNg4afU4UIzaQAWGNSH62ErCfJNghI2WNVAE4g93YO0d1ZbSNRu0b-Uy81LbBj6EOGVbrIaBD6AXAyqCnkDFbB2fZwAYhOTeikhsnpvvzGEouSDa8Nn34wdOYdxak32Xl-IjLOF0Sh9VGn_GQZfrE-rhFcWrM--n6yKAd3UGfjW9I8alYLTh08xUT8Mj-0kXUCsHNH0cvtdbnlfOCNtptUuIMW3nx0S')
                            ->required(),

                        TextInput::make('client_year')
                            ->label('Klijent i godina (npr. Studio Forma • 2024)')
                            ->default('Studio Forma • 2024'),

                        TextInput::make('category')
                            ->label('Kategorija (npr. Arhitektura, Kultura, Fintech)')
                            ->default('Arhitektura'),

                        TextInput::make('languages_badge')
                            ->label('Oznaka jezika (npr. SR / EN)')
                            ->default('SR / EN'),

                        Tabs::make(fn () => __('blocks.translations'))
                            ->tabs(
                                TranslatableTabs::make(fn ($lang) => [
                                    TextInput::make("title.{$lang}")
                                        ->label('Naslov projekta (' . strtoupper($lang) . ')')
                                        ->required($lang === 'sr'),

                                    Textarea::make("description.{$lang}")
                                        ->label('Kratak opis (' . strtoupper($lang) . ')')
                                        ->rows(2),

                                    TextInput::make("case_study_note.{$lang}")
                                        ->label('Poruka pri kliku na Case Study (' . strtoupper($lang) . ')')
                                        ->default('100/100 Core Web Vitals i 0.2s prelaz stranica.'),
                                ])
                            ),

                        TextInput::make('project_url')
                            ->label('URL stranice projekta / Case Study (npr. /sr/studio-forma ili ostaviti za auto-generisanje)')
                            ->placeholder('/sr/studio-forma'),

                        TextInput::make('tags')
                            ->label('Tehnološki tagovi (razdvojeni zarezom, npr: Laravel 11, Filament v3, Alpine.js)')
                            ->default('Laravel 11, Filament v3, Alpine.js'),
                    ])
                    ->collapsible()
                    ->defaultItems(4),
            ]);
    }
}
