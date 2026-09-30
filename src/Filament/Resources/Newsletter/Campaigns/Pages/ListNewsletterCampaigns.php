<?php

namespace Celios\Core\Filament\Resources\Newsletter\Campaigns\Pages;

use Celios\Core\Filament\Resources\Newsletter\Campaigns\NewsletterCampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNewsletterCampaigns extends ListRecords
{
    protected static string $resource = NewsletterCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
