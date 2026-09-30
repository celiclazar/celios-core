<?php

namespace Celios\Core\Filament\Resources\Posts\Pages;

use Celios\Core\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
