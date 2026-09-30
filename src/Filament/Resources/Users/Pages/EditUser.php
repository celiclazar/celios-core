<?php

namespace Celios\Core\Filament\Resources\Users\Pages;

use Celios\Core\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label(fn () => __('actions.delete')),
        ];
    }
}
