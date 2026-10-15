<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Models\Section;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;

class SectionReferenceBlock
{
    public static function make(): Block
    {
        return Block::make('section_reference')
            ->label(function (?array $state) {
                $base = __('blocks.section_reference_title');
                if (! empty($state['section_id'])) {
                    try {
                        $sec = Section::find($state['section_id']);
                        if ($sec) {
                            return "{$base}: {$sec->name} ({$sec->layout})";
                        }
                    } catch (\Throwable $e) {
                        // Table might not exist in early tests
                    }
                }

                return $base;
            })
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->schema([
                Select::make('section_id')
                    ->label('Izaberite sekciju iz biblioteke')
                    ->options(function () {
                        try {
                            return Section::where('is_active', true)->pluck('name', 'id')->toArray();
                        } catch (\Throwable $e) {
                            return [];
                        }
                    })
                    ->searchable()
                    ->required()
                    ->helperText('Ova sekcija se preuzima iz biblioteke sekcija i automatski renderuje sa svim podešenim stilovima i kolonama.'),
            ]);
    }
}
