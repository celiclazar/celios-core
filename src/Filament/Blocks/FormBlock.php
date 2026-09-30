<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Models\Form;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;

class FormBlock
{
    public static function make(): Block
    {
        return Block::make('form_block')
            ->label(function (?array $state): string {
                $base = __('blocks.form_block_title');
                if (!empty($state['form_id'])) {
                    $form = Form::find($state['form_id']);
                    if ($form) {
                        $locale = app()->getLocale();
                        $title = $form->getTranslation('title', $locale, true) ?: $form->slug;
                        return "{$base}: {$title}";
                    }
                }
                return $base;
            })
            ->icon('heroicon-o-clipboard-document-check')
            ->schema([
                Select::make('form_id')
                    ->label(fn () => __('forms.select_form'))
                    ->options(function () {
                        $locale = app()->getLocale();
                        return Form::query()
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn (Form $f) => [
                                $f->id => $f->getTranslation('title', $locale, true) ?: $f->slug,
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Grid::make(2)
                    ->schema([
                        Toggle::make('show_title')
                            ->label(fn () => __('forms.show_title'))
                            ->default(true),

                        Toggle::make('show_description')
                            ->label(fn () => __('forms.show_description'))
                            ->default(true),
                    ]),
            ]);
    }
}
