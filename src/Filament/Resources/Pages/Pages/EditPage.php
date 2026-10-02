<?php

namespace Celios\Core\Filament\Resources\Pages\Pages;

use Celios\Core\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected $listeners = [
        'refreshDraftContent' => 'handleRefreshDraftContent',
    ];

    public function handleRefreshDraftContent(): void
    {
        $this->fillForm();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (empty($data['draft_content']) && ! empty($this->getRecord()->content)) {
            $data['draft_content'] = $this->getRecord()->content;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('visual_builder')
                ->label('Visual Designer (Beta)')
                ->icon('heroicon-o-paint-brush')
                ->color('primary')
                ->visible(fn () => \Illuminate\Support\Facades\Route::has('admin.visual_builder.edit'))
                ->url(fn () => \Illuminate\Support\Facades\Route::has('admin.visual_builder.edit')
                    ? route('admin.visual_builder.edit', ['page' => $this->getRecord()->id])
                    : null),

            Action::make('publish')
                ->label(fn() => __('actions.publish'))
                ->icon('heroicon-m-cloud-arrow-up')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(fn () => empty($this->getRecord()->draft_content))
                ->action(function () {
                    /** @var \Celios\Core\Models\Page $record */
                    $record = $this->getRecord();
                    $record->publish(auth()->id());

                    $this->refreshFormData(['draft_content']);

                    Notification::make()
                        ->title(fn () => __('notifications.page_launched'))
                        ->success()
                        ->send();
                }),

            Action::make('save_snapshot')
                ->label(fn () => __('actions.save_snapshot'))
                ->icon('heroicon-o-camera')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\TextInput::make('note')
                        ->label(fn () => __('fields.snapshot_note'))
                        ->placeholder(fn () => __('fields.snapshot_note_placeholder'))
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    /** @var \Celios\Core\Models\Page $record */
                    $record = $this->getRecord();
                    $record->createRevision(
                        userId: auth()->id(),
                        note: ! empty($data['note']) ? $data['note'] : __('notifications.manual_snapshot')
                    );

                    Notification::make()
                        ->title(fn () => __('notifications.snapshot_created'))
                        ->success()
                        ->send();
                }),

            Action::make('preview')
                ->label(fn () => __('actions.preview_draft'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->openUrlInNewTab()
                ->url(function () {
                    $record = $this->getRecord();
                    $locale = app()->getLocale();
                    $slug = $record->getTranslation('slug', $locale);
                    return url("/{$locale}/{$slug}?preview=true");
                }),

            DeleteAction::make()
                ->label(fn () => __('actions.delete'))
                ->hidden(fn () => $this->getRecord()->isSystem()),
        ];
    }
}
