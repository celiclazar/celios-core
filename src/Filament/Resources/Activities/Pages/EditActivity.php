<?php

namespace Celios\Core\Filament\Resources\Activities\Pages;

use Celios\Core\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label(fn () => __('actions.view')),
            DeleteAction::make()->label(fn () => __('actions.delete')),
        ];
    }
}
