<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Filament\Helpers\TranslatableTabs;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;

class ColorSystemBlock
{
    public static function make(): Block
    {
        return Block::make('color_system')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blocks.color_system_title') ?? 'Color System Teaser', $state, ['heading', 'section_tag']))
            ->icon('heroicon-o-paint-brush')
            ->schema([
                Tabs::make(fn () => __('blocks.translations'))
                    ->tabs(
                        TranslatableTabs::make(fn ($lang) => [
                            TextInput::make("section_tag.{$lang}")
                                ->label('Tag sekcije (' . strtoupper($lang) . ')')
                                ->default('Adaptivni Kolor Sistem'),

                            TextInput::make("heading.{$lang}")
                                ->label('Naslov sekcije (' . strtoupper($lang) . ')')
                                ->required($lang === 'sr')
                                ->default('Fluidni Prelaz Između Svetlog i Tamnog Režima'),

                            Textarea::make("description.{$lang}")
                                ->label('Opis sekcije (' . strtoupper($lang) . ')')
                                ->rows(3)
                                ->default('Dizajneri nisu primorani na kompromise. Celios tokeni automatski mapiraju kontraste (surface, on-surface, primary i accente) tako da vaš portfolio zadržava identičan nivo elegancije i čitljivosti u bilo koje doba dana.'),

                            TextInput::make("chip_1.{$lang}")
                                ->label('Bedž 1 (' . strtoupper($lang) . ')')
                                ->default('Automatska detekcija OS postavki'),

                            TextInput::make("chip_2.{$lang}")
                                ->label('Bedž 2 (' . strtoupper($lang) . ')')
                                ->default('Tailwind Semantic Tokens'),

                            TextInput::make("footer_note.{$lang}")
                                ->label('Donja napomena (' . strtoupper($lang) . ')')
                                ->default('Generisano bez hardkodovanih boja • 100% Tailwind Semantic Classes'),
                        ])
                    ),
            ]);
    }
}
