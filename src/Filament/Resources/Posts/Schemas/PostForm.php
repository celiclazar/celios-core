<?php

namespace Celios\Core\Filament\Resources\Posts\Schemas;

use Celios\Core\Models\Category;
use Celios\Core\Models\User;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('fields.translations'))
                    ->icon('heroicon-o-language')
                    ->columnSpanFull()
                    ->schema([
                        Tabs::make('translations_tabs')
                            ->tabs(
                                collect(config('locales.available', ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano']))
                                    ->map(function (string $label, string $lang) {
                                        return Tabs\Tab::make(strtoupper($lang) . " ({$label})")
                                            ->id($lang)
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make("title.{$lang}")
                                                            ->label(__('fields.title') . ' (' . strtoupper($lang) . ')')
                                                            ->required($lang === config('locales.default', 'sr'))
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, callable $set) use ($lang) {
                                                                $set("slug.{$lang}", str($state)->slug());
                                                            }),

                                                        TextInput::make("slug.{$lang}")
                                                            ->label(__('fields.url_slug') . ' (' . strtoupper($lang) . ')')
                                                            ->required($lang === config('locales.default', 'sr'))
                                                            ->unique(
                                                                table: 'posts',
                                                                column: "slug->{$lang}",
                                                                ignoreRecord: true
                                                            ),
                                                    ]),

                                                Textarea::make("excerpt.{$lang}")
                                                    ->label(__('blog.excerpt') . ' (' . strtoupper($lang) . ')')
                                                    ->rows(3),

                                                RichEditor::make("body.{$lang}")
                                                    ->label(__('blog.body') . ' (' . strtoupper($lang) . ')')
                                                    ->toolbarButtons([
                                                        'attachFiles',
                                                        'blockquote',
                                                        'bold',
                                                        'bulletList',
                                                        'codeBlock',
                                                        'h2',
                                                        'h3',
                                                        'italic',
                                                        'link',
                                                        'orderedList',
                                                        'redo',
                                                        'strike',
                                                        'underline',
                                                        'undo',
                                                    ]),
                                            ]);
                                    })
                                    ->values()
                                    ->all()
                            ),
                    ]),

                Section::make(__('blog.post_single') . ' ' . __('fields.general_settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label(__('blog.category'))
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
                                    ->preload()
                                    ->nullable(),

                                Select::make('author_id')
                                    ->label(__('blog.author'))
                                    ->options(User::query()->pluck('name', 'id')->all())
                                    ->default(fn () => auth()->id())
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                DateTimePicker::make('published_at')
                                    ->label(__('blog.published_at'))
                                    ->default(now()),

                                Toggle::make('is_published')
                                    ->label(__('blog.is_published'))
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        CuratorPicker::make('featured_image_id')
                            ->label(__('blog.featured_image'))
                            ->buttonLabel(__('fields.choose_picture'))
                            ->directory('blog/featured')
                            ->relationship('featuredImage', 'id')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
