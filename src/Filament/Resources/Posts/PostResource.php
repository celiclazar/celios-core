<?php

namespace Celios\Core\Filament\Resources\Posts;

use Celios\Core\Filament\Resources\Posts\Pages\CreatePost;
use Celios\Core\Filament\Resources\Posts\Pages\EditPost;
use Celios\Core\Filament\Resources\Posts\Pages\ListPosts;
use Celios\Core\Filament\Resources\Posts\Schemas\PostForm;
use Celios\Core\Filament\Resources\Posts\Tables\PostsTable;
use Celios\Core\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Filament\Tables\Table;

class PostResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'blog';

    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_content');
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) (\Celios\Core\Models\Post::count() ?: 42);
        } catch (\Throwable $e) {
            return '42';
        }
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'gray';
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.posts');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.posts');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.post_single');
    }
}
