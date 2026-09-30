<?php

namespace Celios\Core\Filament\Resources\Contacts\Pages;

use Celios\Core\Filament\Resources\Contacts\ContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}
