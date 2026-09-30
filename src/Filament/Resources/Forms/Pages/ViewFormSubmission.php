<?php

namespace Celios\Core\Filament\Resources\Forms\Pages;

use Celios\Core\Filament\Resources\Contacts\ContactResource;
use Celios\Core\Filament\Resources\Forms\FormSubmissionResource;
use Celios\Core\Models\Contact;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewFormSubmission extends ViewRecord
{
    protected static string $resource = FormSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('convert_to_lead')
                ->label(__('crm.convert_to_lead'))
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->visible(fn () => module_enabled('crm') && ! Contact::where('form_submission_id', $this->record->id)->exists())
                ->modalHeading(__('crm.convert_to_lead_heading'))
                ->modalDescription(__('crm.convert_to_lead_description'))
                ->fillForm(function (): array {
                    $record = $this->record;
                    $data = $record->data ?? [];
                    $name = null;
                    $email = null;
                    $phone = null;
                    $company = null;
                    $notes = [];

                    foreach ($data as $key => $value) {
                        $k = strtolower(str_replace(['-', '_'], '', $key));
                        $valStr = is_array($value) ? implode(', ', $value) : (string) $value;

                        if (!$name && (str_contains($k, 'name') || str_contains($k, 'ime'))) {
                            $name = $valStr;
                        } elseif (!$email && (str_contains($k, 'email') || str_contains($k, 'mail'))) {
                            $email = $valStr;
                        } elseif (!$phone && (str_contains($k, 'phone') || str_contains($k, 'tel') || str_contains($k, 'telefon'))) {
                            $phone = $valStr;
                        } elseif (!$company && (str_contains($k, 'company') || str_contains($k, 'kompanija') || str_contains($k, 'firma'))) {
                            $company = $valStr;
                        } else {
                            $notes[] = str($key)->headline() . ': ' . $valStr;
                        }
                    }

                    return [
                        'name' => $name ?: 'Lead #' . $record->id,
                        'email' => $email,
                        'phone' => $phone,
                        'company' => $company,
                        'stage' => 'lead',
                        'source' => 'form_submission',
                        'lead_value' => null,
                        'notes' => !empty($notes) ? implode("\n", $notes) : null,
                    ];
                })
                ->form([
                    TextInput::make('name')
                        ->label(__('crm.name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('company')
                        ->label(__('crm.company'))
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label(__('crm.email'))
                        ->email()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label(__('crm.phone'))
                        ->tel()
                        ->maxLength(50),
                    Select::make('stage')
                        ->label(__('crm.stage'))
                        ->options(Contact::getStages())
                        ->default('lead')
                        ->required(),
                    TextInput::make('lead_value')
                        ->label(__('crm.lead_value'))
                        ->numeric()
                        ->prefix('€'),
                    Select::make('assigned_to_user_id')
                        ->label(__('crm.assigned_to'))
                        ->relationship('assignedUser', 'name')
                        ->searchable()
                        ->preload(),
                    Textarea::make('notes')
                        ->label(__('crm.notes'))
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $record = $this->record;
                    $contact = Contact::create([
                        'name' => $data['name'],
                        'company' => $data['company'] ?? null,
                        'email' => $data['email'] ?? null,
                        'phone' => $data['phone'] ?? null,
                        'stage' => $data['stage'] ?? 'lead',
                        'source' => 'form_submission',
                        'lead_value' => $data['lead_value'] ?? null,
                        'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
                        'form_submission_id' => $record->id,
                        'notes' => $data['notes'] ?? null,
                    ]);

                    if (! $record->is_read) {
                        $record->update(['is_read' => true]);
                    }

                    if (! empty($data['notes'])) {
                        $contact->interactions()->create([
                            'user_id' => auth()->id(),
                            'type' => 'note',
                            'content' => "Inquiry details from Form #{$record->form_id} Submission #{$record->id}:\n" . $data['notes'],
                        ]);
                    }

                    Notification::make()
                        ->title(__('crm.converted_to_lead_success'))
                        ->success()
                        ->send();
                }),

            Action::make('view_lead')
                ->label(__('crm.contact_single'))
                ->icon('heroicon-o-user-check')
                ->color('success')
                ->visible(fn () => module_enabled('crm') && Contact::where('form_submission_id', $this->record->id)->exists())
                ->url(function (): ?string {
                    $contact = Contact::where('form_submission_id', $this->record->id)->first();
                    return $contact ? ContactResource::getUrl('edit', ['record' => $contact]) : null;
                }),

            Action::make('toggle_read')
                ->label(fn () => $this->record->is_read ? __('forms.mark_as_unread') : __('forms.mark_as_read'))
                ->icon(fn () => $this->record->is_read ? 'heroicon-o-envelope' : 'heroicon-o-check-circle')
                ->color(fn () => $this->record->is_read ? 'warning' : 'success')
                ->action(function () {
                    $this->record->update(['is_read' => !$this->record->is_read]);
                    $this->record->refresh();
                }),

            DeleteAction::make(),
        ];
    }
}
