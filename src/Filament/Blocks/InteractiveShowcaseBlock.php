<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class InteractiveShowcaseBlock
{
    public static function make(): Block
    {
        return Block::make('interactive_showcase')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.interactive_showcase_title') ?? 'Interactive Layout Showcase', $state, ['section_title', 'section_tag']))
            ->icon('heroicon-o-cursor-arrow-rays')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("section_tag.{$lang}")
                                ->label('Tag sekcije (' . strtoupper($lang) . ')')
                                ->default('Fleksibilni Graditelj Stranica'),

                            TextInput::make("section_title.{$lang}")
                                ->label('Naslov sekcije (' . strtoupper($lang) . ')')
                                ->required($lang === 'sr')
                                ->default('Modularni Blokovi u Akciji'),

                            Textarea::make("section_desc.{$lang}")
                                ->label('Opis sekcije (' . strtoupper($lang) . ')')
                                ->rows(2)
                                ->default('Odaberite stil prikaza i testirajte ponašanje sekcija na radnoj površini sa dinamičkom dvojezičnom proverom.'),

                            TextInput::make("split_tag.{$lang}")
                                ->label('Split Tab - Oznaka (' . strtoupper($lang) . ')')
                                ->default('Block: Architecture & Space Showcase'),

                            TextInput::make("split_title.{$lang}")
                                ->label('Split Tab - Naslov (' . strtoupper($lang) . ')')
                                ->default('Precizna estetika za moderne kreativne studije.'),

                            Textarea::make("split_desc.{$lang}")
                                ->label('Split Tab - Opis (' . strtoupper($lang) . ')')
                                ->rows(2)
                                ->default('Svi blokovi poseduju direktne opcije konfiguracije kroz Filament panel: izmene margina, tipografskih razmera, relacija sa slikama iz biblioteke i SEO meta oznakama na nivou fragmenta.'),

                            TextInput::make("split_btn_text.{$lang}")
                                ->label('Split Tab - Dugme (' . strtoupper($lang) . ')')
                                ->default('Pogledaj Arhivu Radova'),

                            TextInput::make("split_project_title.{$lang}")
                                ->label('Split Tab - Naziv projekta (' . strtoupper($lang) . ')')
                                ->default('Atelje Beograd • Monolit 24'),

                            TextInput::make("bento_1_title.{$lang}")
                                ->label('Bento Tab - Naslov 1 (' . strtoupper($lang) . ')')
                                ->default('Interaktivni Digitalni Arhiv'),

                            Textarea::make("bento_1_desc.{$lang}")
                                ->label('Bento Tab - Opis 1 (' . strtoupper($lang) . ')')
                                ->rows(2)
                                ->default('Strukturirana baza eksponata sa više od 4,000 unosa, asinhronim pretraživanjem i automatskim generisanjem OpenGraph slika.'),

                            TextInput::make("case_1_title.{$lang}")
                                ->label('Case Study 1 - Naslov (' . strtoupper($lang) . ')')
                                ->default('Nordic Furniture Lab — Digitalni Salon 2024'),

                            Textarea::make("case_1_desc.{$lang}")
                                ->label('Case Study 1 - Opis (' . strtoupper($lang) . ')')
                                ->rows(2)
                                ->default('Kako smo postigli 300% brži odziv kataloga sa preko 1,200 3D modela koristeći Celios blokove.'),

                            TextInput::make("case_2_title.{$lang}")
                                ->label('Case Study 2 - Naslov (' . strtoupper($lang) . ')')
                                ->default('Kvantum Branding Hub — Multi-tenant sajt'),

                            Textarea::make("case_2_desc.{$lang}")
                                ->label('Case Study 2 - Opis (' . strtoupper($lang) . ')')
                                ->rows(2)
                                ->default('Jedinstvena CMS instanca koja u realnom vremenu opslužuje 6 nezavisnih portfolio domena.'),
                        ])
                    ),

                TextInput::make('split_image_url')
                    ->label('Split Tab - URL Slike')
                    ->default('https://lh3.googleusercontent.com/aida-public/AB6AXuDkf9ewQzU8QrKp-qRL56pWjwkvGuD7ubhnCzQG2lQ3cXK8OeVT2DiWye2s7qUBbEpzkdwuSKViMomNK6UMezxrtp0V39vILCEAlop2DbKYEqJhjp5zH41YDtd6mFFXMGXsdp7MhUTccnlIq819Veu-nQUBPgVw1lWsbzSPjVOsKqVbJF6iXKQhmc4fGdGIhNsaWVSfUgRTRzaL2HS4cv1TOla65PFv9nAEPAdAkskWyrbgn4Lmux9V'),

                TextInput::make('bento_1_image_url')
                    ->label('Bento Tab - URL Slike 1')
                    ->default('https://lh3.googleusercontent.com/aida-public/AB6AXuBgPZGNZyyWgfr6P7GSDV7tl2GrapFX1nw2u_tHypcpspgbcZ7cXXyhWfwFkPOzgDJ10E_ECXgCihBN5WucWOorYScmJjV_ORJHQrGhm9g4cVHk1w14lK1OgZqYl6M_kCTa7yMuxAIHT_AMuGLYN1V-95dGLW52HftxK3siFl1V7uvSnFJEn4Q9fZOuRXA06WWKyQ7dI3NsP8kgNKV8Zv5cxsQjVnS3L4Da-uwr97ZCnOu3h3pFn0xh'),

                TextInput::make('case_1_image_url')
                    ->label('Case Study 1 - URL Slike')
                    ->default('https://lh3.googleusercontent.com/aida-public/AB6AXuDlIynnFgBMnTnaqqupl5wwvbzkGlFY-9ZPFJw3V-F0tVuEu3dgff_ECLLyurfGYcG0FRqQGv7i2zQD5LB3Wbn7OY6ceDL_PyhHGAmRWcQiR1exm15bvFPVhtm53ldv5cuExy3IhO7eMW81ixd3VkFlk9CukXirLsNLDk281T2foV93db1sySxIIVBJXwLzEuzhAeShK3rp9ILkiorP91QMkV_A0Vx8vDkUDMqD56-MqGuHQXIzxMJJ'),

                TextInput::make('case_2_image_url')
                    ->label('Case Study 2 - URL Slike')
                    ->default('https://lh3.googleusercontent.com/aida-public/AB6AXuAPgH8jA1mFcORzMpMwVjE_Hrc4c2bY62pDD2tjTxmRJPajDsbiRtUJX_kuw0bPWe72862DGNwQxE-0WXeTpEUserPwCLTYob1DRbKt1Vz6RcuRd-gjzyCL4UtA8xgiIgUr0P5Rzre6n_YAKbn7su0ayJj1Lf7ChkvPgKSbaOnRRGa7B-MhuwEcw4bYv5oXaNHCuI0rvbEtCDgs3gWAzPEBf7oI9PVDbO42v8O1gvm6a1YhwBlqHv83'),
            ]);
    }
}
