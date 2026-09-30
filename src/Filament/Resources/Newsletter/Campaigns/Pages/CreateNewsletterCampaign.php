<?php

namespace Celios\Core\Filament\Resources\Newsletter\Campaigns\Pages;

use Celios\Core\Filament\Resources\Newsletter\Campaigns\NewsletterCampaignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletterCampaign extends CreateRecord
{
    protected static string $resource = NewsletterCampaignResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
