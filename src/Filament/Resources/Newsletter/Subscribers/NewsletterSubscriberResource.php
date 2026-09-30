<?php

namespace Celios\Core\Filament\Resources\Newsletter\Subscribers;

use Celios\Core\Filament\Resources\Newsletter\Subscribers\Pages\CreateNewsletterSubscriber;
use Celios\Core\Filament\Resources\Newsletter\Subscribers\Pages\EditNewsletterSubscriber;
use Celios\Core\Filament\Resources\Newsletter\Subscribers\Pages\ListNewsletterSubscribers;
use Celios\Core\Filament\Resources\Newsletter\Subscribers\Schemas\NewsletterSubscriberForm;
use Celios\Core\Filament\Resources\Newsletter\Subscribers\Tables\NewsletterSubscribersTable;
use Celios\Core\Models\NewsletterSubscriber;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'newsletter';

    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $slug = 'newsletter/subscribers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_marketing');
    }

    public static function form(Schema $schema): Schema
    {
        return NewsletterSubscriberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsletterSubscribersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletterSubscribers::route('/'),
            'create' => CreateNewsletterSubscriber::route('/create'),
            'edit' => EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.subscribers');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.subscribers');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.subscriber_single');
    }
}
