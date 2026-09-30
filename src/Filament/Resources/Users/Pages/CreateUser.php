<?php

namespace Celios\Core\Filament\Resources\Users\Pages;

use Celios\Core\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
