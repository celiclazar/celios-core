<?php

namespace Celios\Core\Filament\Blocks;

use Celios\Core\Filament\Helpers\BlockLabelHelper;
use Celios\Core\Models\Category;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;

class PostsBlock
{
    public static function make(): Block
    {
        return Block::make('posts_block')
            ->label(fn (?array $state) => BlockLabelHelper::make(__('blog.latest_posts_title'), $state, ['heading', 'subtitle']))
            ->icon('heroicon-o-newspaper')
            ->schema([
                Tabs::make('translations')
                    ->tabs(
                        collect(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                            ->map(function ($label, $lang) {
                                return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                    ->schema([
                                        TextInput::make("heading.{$lang}")
                                            ->label(__('fields.heading') . ' (' . strtoupper($lang) . ')')
                                            ->placeholder(__('blog.latest_posts')),

                                        TextInput::make("subtitle.{$lang}")
                                            ->label(__('fields.subtitle') . ' (' . strtoupper($lang) . ')'),
                                    ]);
                            })
                            ->values()
                            ->all()
                    ),

                Grid::make(2)
                    ->schema([
                        Select::make('category_id')
                            ->label(__('blog.category'))
                            ->placeholder(__('blog.all_categories'))
                            ->options(function (): array {
                                $locale = app()->getLocale();
                                return Category::query()
                                    ->get()
                                    ->mapWithKeys(fn (Category $cat) => [
                                        $cat->id => $cat->getTranslation('title', $locale, true) ?: __('fields.no_title'),
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->nullable(),

                        Select::make('limit')
                            ->label(__('blog.post_limit'))
                            ->options([
                                3 => '3',
                                6 => '6',
                                9 => '9',
                                12 => '12',
                            ])
                            ->default(3),

                        Select::make('layout')
                            ->label(__('blog.layout'))
                            ->options([
                                'grid_3' => __('blog.layout_grid_3'),
                                'grid_2' => __('blog.layout_grid_2'),
                                'list' => __('blog.layout_list'),
                            ])
                            ->default('grid_3'),

                        Toggle::make('show_excerpt')
                            ->label(__('blog.show_excerpt'))
                            ->default(true),

                        Toggle::make('show_date')
                            ->label(__('blog.show_date'))
                            ->default(true),

                        Toggle::make('show_author')
                            ->label(__('blog.show_author'))
                            ->default(true),
                    ]),
            ]);
    }
}
