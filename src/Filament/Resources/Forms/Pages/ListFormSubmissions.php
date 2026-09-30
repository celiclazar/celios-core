<?php

namespace Celios\Core\Filament\Resources\Forms\Pages;

use Celios\Core\Filament\Resources\Forms\FormSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListFormSubmissions extends ListRecords
{
    protected static string $resource = FormSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
