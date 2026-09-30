<?php

namespace Celios\Core\Filament\Resources\Newsletter\Subscribers\Pages;

use Celios\Core\Filament\Resources\Newsletter\Subscribers\NewsletterSubscriberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletterSubscriber extends CreateRecord
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
