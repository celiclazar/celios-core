<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\SectionSchemaHelper;
use Filament\Forms\Components\Builder\Block;

class SectionBlock
{
    public static function make(): Block
    {
        return Block::make('custom_section')
            ->label(function (?array $state) {
                $layout = $state['layout'] ?? '2_cols';
                $layoutLabel = match ($layout) {
                    '1_col' => '1 kolona',
                    '2_cols' => '2 kolone',
                    '3_cols' => '3 kolone',
                    '4_cols' => '4 kolone',
                    '1_2_cols' => '1/3 + 2/3',
                    '2_1_cols' => '2/3 + 1/3',
                    default => $layout,
                };

                $headerTitle = null;
                if (! empty($state['header']['title'])) {
                    $val = $state['header']['title'];
                    $locale = app()->getLocale();
                    $headerTitle = is_array($val) ? ($val[$locale] ?? $val['sr'] ?? $val['en'] ?? null) : $val;
                }

                $prefix = __('blocks.custom_section_title');
                if ($headerTitle) {
                    return "{$prefix} ({$layoutLabel}): {$headerTitle}";
                }

                return "{$prefix} ({$layoutLabel})";
            })
            ->icon('heroicon-o-view-columns')
            ->schema(SectionSchemaHelper::getSchema(includeNameFields: false));
    }
}
