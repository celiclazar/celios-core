<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class CtaSandboxBlock
{
    public static function make(): Block
    {
        return Block::make('cta_sandbox')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.cta_sandbox_title') ?? 'CTA Sandbox Banner', $state, ['heading', 'status_pill', 'description']))
            ->icon('heroicon-o-command-line')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("status_pill.{$lang}")
                                ->label('Status bedž (' . strtoupper($lang) . ')')
                                ->default('Trenutno Aktivan: Celios Sandbox Environment'),

                            TextInput::make("heading.{$lang}")
                                ->label('Naslov banera (' . strtoupper($lang) . ')')
                                ->required($lang === 'sr')
                                ->default('Isprobajte Celios Admin u testnom režimu'),

                            Textarea::make("description.{$lang}")
                                ->label('Opis banera (' . strtoupper($lang) . ')')
                                ->rows(3)
                                ->default('Istražite upravljanje sadržajem, višejezičnost i modularne blokove u realnom Filament v3 okruženju. Bez instalacije, bez kreditne kartice.'),

                            TextInput::make("button_text.{$lang}")
                                ->label('Tekst na dugmetu (' . strtoupper($lang) . ')')
                                ->default('Uđi u Admin Kontrolnu Tablu (Test Demo)'),

                            TextInput::make("footer_note_1.{$lang}")
                                ->label('Donja napomena 1 (' . strtoupper($lang) . ')')
                                ->default('Demo nalog unapred popunjen'),

                            TextInput::make("footer_note_2.{$lang}")
                                ->label('Donja napomena 2 (' . strtoupper($lang) . ')')
                                ->default('Reset baze svakih 60 minuta'),
                        ])
                    ),

                TextInput::make('admin_url')
                    ->label('Direktan URL ka adminu (ili ostaviti prazno za modal)')
                    ->default('/admin'),
            ]);
    }
}
