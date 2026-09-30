<?php

namespace Celios\Core\Filament\Resources\Forms\Tables;

use Celios\Core\Filament\Resources\Contacts\ContactResource;
use Celios\Core\Models\Contact;
use Celios\Core\Models\Form;
use Celios\Core\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class FormSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.title')
                    ->label(__('forms.form_single'))
                    ->state(fn (FormSubmission $record): string => $record->form
                        ? ($record->form->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                        : '-'
                    )
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('summary')
                    ->label(__('forms.submission_details'))
                    ->state(function (FormSubmission $record): string {
                        $data = $record->data ?? [];
                        $snippets = [];
                        foreach ($data as $key => $val) {
                            if (is_array($val)) {
                                $val = implode(', ', $val);
                            }
                            $snippets[] = str($key)->headline() . ': ' . str($val)->limit(30);
                            if (count($snippets) >= 2) {
                                break;
                            }
                        }
                        return !empty($snippets) ? implode(' | ', $snippets) : '-';
                    })
                    ->wrap()
                    ->limit(80),

                IconColumn::make('has_files')
                    ->label(__('forms.files_attached'))
                    ->state(fn (FormSubmission $record): bool => !empty($record->files))
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('')
                    ->alignCenter(),

                IconColumn::make('is_lead')
                    ->label(__('crm.crm'))
                    ->boolean()
                    ->trueIcon('heroicon-o-user-plus')
                    ->falseIcon('')
                    ->trueColor('primary')
                    ->state(fn (FormSubmission $record): bool => Contact::where('form_submission_id', $record->id)->exists())
                    ->visible(fn (): bool => module_enabled('crm'))
                    ->alignCenter(),

                IconColumn::make('is_read')
                    ->label(__('forms.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('forms.submitted_at'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('form_id')
                    ->label(__('forms.form_single'))
                    ->options(function () {
                        $locale = app()->getLocale();
                        $user = auth()->user();

                        return Form::query()
                            ->get()
                            ->filter(fn (Form $f) => $user ? $f->canUserViewSubmissions($user) : true)
                            ->mapWithKeys(fn (Form $f) => [
                                $f->id => $f->getTranslation('title', $locale, true) ?: $f->slug,
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_read')
                    ->label(__('forms.status'))
                    ->placeholder(__('fields.all') ?? 'All')
                    ->trueLabel(__('forms.read'))
                    ->falseLabel(__('forms.unread')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->after(function (FormSubmission $record) {
                        if (!$record->is_read) {
                            $record->update(['is_read' => true]);
                        }
                    }),

                Action::make('convert_to_lead')
                    ->label(__('crm.convert_to_lead'))
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (FormSubmission $record) => module_enabled('crm') && ! Contact::where('form_submission_id', $record->id)->exists())
                    ->modalHeading(__('crm.convert_to_lead_heading'))
                    ->modalDescription(__('crm.convert_to_lead_description'))
                    ->fillForm(function (FormSubmission $record): array {
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
                    ->action(function (FormSubmission $record, array $data): void {
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
                    ->visible(fn (FormSubmission $record) => module_enabled('crm') && Contact::where('form_submission_id', $record->id)->exists())
                    ->url(function (FormSubmission $record): ?string {
                        $contact = Contact::where('form_submission_id', $record->id)->first();
                        return $contact ? ContactResource::getUrl('edit', ['record' => $contact]) : null;
                    }),

                Action::make('toggle_read')
                    ->label(fn (FormSubmission $record) => $record->is_read ? __('forms.mark_as_unread') : __('forms.mark_as_read'))
                    ->icon(fn (FormSubmission $record) => $record->is_read ? 'heroicon-o-envelope' : 'heroicon-o-check-circle')
                    ->color(fn (FormSubmission $record) => $record->is_read ? 'warning' : 'success')
                    ->action(fn (FormSubmission $record) => $record->update(['is_read' => !$record->is_read])),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_as_read')
                        ->label(__('forms.mark_as_read'))
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true])),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
