<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class AuthorBioBlock
{
    public static function make(): Block
    {
        return Block::make('author_bio')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.author_bio_title') ?? 'Author Bio & Story', $state, ['author_name', 'section_heading', 'author_role']))
            ->icon('heroicon-o-user')
            ->schema([
                TextInput::make('photo_url')
                    ->label('URL fotografije autora')
                    ->default('https://lh3.googleusercontent.com/aida/AEtjO1UU1cY31xFr14rSP0jI718ew9dhGfi6yJolDgQ_8AIxsjp2a3xkkN6yOKqbhVCLm0dflK0rbwxIw3y1Z866K6Nnu8E0eUQUC7wTrParVz4K6uNsjTNloRRLDvMJaFbG9Wtz3v1K-6UsZOdqx9YI109M5KZNCOMbqwQvnTCvlLhCE8WY-IO3670lKXgfkus3QJFiycEkhWCroXmDOw65fEE9MReqRXyrq_fuVbY9ijLVZRASocFv2L1cxg0')
                    ->required(),

                TextInput::make('author_name')
                    ->label('Ime autora')
                    ->default('Filip V.')
                    ->required(),

                TextInput::make('author_role')
                    ->label('Uloga / Titula')
                    ->default('Full-stack Developer & UI Arhitekta'),

                TextInput::make('author_location')
                    ->label('Lokacija')
                    ->default('Beograd, Srbija • Remote & Consulting'),

                TextInput::make('author_email')
                    ->label('Email adresa')
                    ->default('filip@celios.io'),

                TextInput::make('github_url')
                    ->label('GitHub profil URL')
                    ->default('#'),

                TextInput::make('linkedin_url')
                    ->label('LinkedIn profil URL')
                    ->default('#'),

                TextInput::make('twitter_url')
                    ->label('X / Twitter profil URL')
                    ->default('#'),

                TextInput::make('tech_stack')
                    ->label('Tehnološki stack (razdvojeno zarezom)')
                    ->default('Laravel 11, Filament v3, Tailwind CSS, Alpine.js, Livewire 3, PostgreSQL / Redis'),

                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("section_tag.{$lang}")
                                ->label('Tag sekcije (' . strtoupper($lang) . ')')
                                ->default('O Autoru & Motivacija'),

                            TextInput::make("section_heading.{$lang}")
                                ->label('Naslov sekcije (' . strtoupper($lang) . ')')
                                ->required($lang === 'sr')
                                ->default('Zašto sam napravio Celios CMS?'),

                            Textarea::make("story_paragraph_1.{$lang}")
                                ->label('Priča - Pasus 1 (' . strtoupper($lang) . ')')
                                ->rows(3)
                                ->default('Kao full-stack inženjer i dizajner sa dugogodišnjim radom na custom web projektima, konstantno sam se susretao sa istom dilemom: WordPress je bio pretrpan nesigurnim dodacima i teškim za održavanje, dok su pure headless rešenja klijentima delovala previše apstraktno i hladno.'),

                            Textarea::make("story_paragraph_2.{$lang}")
                                ->label('Priča - Pasus 2 (' . strtoupper($lang) . ')')
                                ->rows(3)
                                ->default('Celios CMS je rođen iz potrebe za čistom, beskompromisnom platformom. Spojio sam eleganciju i reaktivnost Filament v3 administracije sa robusnim Laravel backendom i čistim Tailwind komponentama. Rezultat je CMS u kome klijenti intuitivno uređuju višejezične sadržaje i slažu vizuelne blokove, a developeri imaju 100% čist Blade i PHP kod bez crnih kutija.'),
                        ])
                    ),
            ]);
    }
}
