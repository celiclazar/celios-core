<?php

namespace Celios\Core\Filament\Resources\Brands\Pages;

use Celios\Core\Filament\Resources\Brands\BrandResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record, $action) {
                    if ($record->is_default) {
                        Notification::make()
                            ->title(__('brands.cannot_delete_default_brand'))
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
