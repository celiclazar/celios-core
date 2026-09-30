<?php

namespace Celios\Core\Filament\Resources\Newsletter\Campaigns;

use Celios\Core\Filament\Resources\Newsletter\Campaigns\Pages\CreateNewsletterCampaign;
use Celios\Core\Filament\Resources\Newsletter\Campaigns\Pages\EditNewsletterCampaign;
use Celios\Core\Filament\Resources\Newsletter\Campaigns\Pages\ListNewsletterCampaigns;
use Celios\Core\Filament\Resources\Newsletter\Campaigns\Pages\ViewNewsletterCampaign;
use Celios\Core\Filament\Resources\Newsletter\Campaigns\Schemas\NewsletterCampaignForm;
use Celios\Core\Filament\Resources\Newsletter\Campaigns\Tables\NewsletterCampaignsTable;
use Celios\Core\Models\NewsletterCampaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Filament\Tables\Table;

class NewsletterCampaignResource extends Resource
{
    use HasModuleToggle;

    public const MODULE_KEY = 'newsletter';

    protected static ?string $model = NewsletterCampaign::class;

    protected static ?string $slug = 'newsletter/campaigns';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_marketing');
    }

    public static function form(Schema $schema): Schema
    {
        return NewsletterCampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsletterCampaignsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletterCampaigns::route('/'),
            'create' => CreateNewsletterCampaign::route('/create'),
            'view' => ViewNewsletterCampaign::route('/{record}'),
            'edit' => EditNewsletterCampaign::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.campaigns');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.campaigns');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.campaign_single');
    }
}
