<?php

namespace Celios\Core\Filament\Resources\Forms\Schemas;

use Celios\Core\Models\FormSubmission;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('forms.submission_details'))
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('form.title')
                                    ->label(__('forms.form_single'))
                                    ->state(fn (FormSubmission $record): string => $record->form
                                        ? ($record->form->getTranslation('title', app()->getLocale(), true) ?: __('fields.no_title'))
                                        : '-'
                                    )
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('created_at')
                                    ->label(__('forms.submitted_at'))
                                    ->dateTime('d.m.Y H:i:s'),

                                IconEntry::make('is_read')
                                    ->label(__('forms.status'))
                                    ->boolean(),

                                TextEntry::make('ip_address')
                                    ->label(__('forms.ip_address'))
                                    ->placeholder('-'),

                                TextEntry::make('user_agent')
                                    ->label(__('forms.user_agent'))
                                    ->columnSpan(2)
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make(__('forms.submission_details'))
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        KeyValueEntry::make('data')
                            ->label(__('forms.form_fields'))
                            ->keyLabel(__('forms.field_label'))
                            ->valueLabel(__('forms.submission_details'))
                            ->getStateUsing(function (FormSubmission $record) {
                                $data = $record->data ?? [];
                                $fields = $record->form?->fields ?? [];
                                $locale = app()->getLocale();

                                $formatted = [];
                                foreach ($data as $key => $val) {
                                    // Try to find the human readable label for the field
                                    $fieldDef = collect($fields)->firstWhere('key', $key);
                                    $label = $key;
                                    if ($fieldDef && isset($fieldDef['label'])) {
                                        $label = is_array($fieldDef['label'])
                                            ? ($fieldDef['label'][$locale] ?? $fieldDef['label']['sr'] ?? $fieldDef['label']['en'] ?? $key)
                                            : (string)$fieldDef['label'];
                                    }

                                    if (is_array($val)) {
                                        $val = implode(', ', $val);
                                    }

                                    $formatted[$label] = (string)($val ?? '');
                                }

                                return $formatted;
                            }),
                    ]),

                Section::make(__('forms.files_attached'))
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->visible(fn (FormSubmission $record) => !empty($record->files))
                    ->schema([
                        TextEntry::make('files')
                            ->label(__('forms.files_attached'))
                            ->html()
                            ->state(function (FormSubmission $record) {
                                $files = $record->files ?? [];
                                if (empty($files)) {
                                    return '-';
                                }

                                $html = '<ul class="space-y-2">';
                                foreach ($files as $fieldKey => $path) {
                                    $url = asset('storage/' . $path);
                                    $filename = basename($path);
                                    $html .= sprintf(
                                        '<li><a href="%s" target="_blank" download class="inline-flex items-center space-x-2 text-primary-600 hover:underline font-medium"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> <span>%s: <strong>%s</strong></span></a></li>',
                                        e($url),
                                        e(str($fieldKey)->headline()),
                                        e($filename)
                                    );
                                }
                                $html .= '</ul>';

                                return $html;
                            }),
                    ]),

                Section::make(__('sidebar.activities'))
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('activities')
                            ->label(__('fields.activity_timestamp'))
                            ->html()
                            ->state(function (FormSubmission $record) {
                                $activities = \Spatie\Activitylog\Models\Activity::forSubject($record)->latest()->get();
                                if ($activities->isEmpty()) {
                                    return '<p class="text-sm text-gray-500">' . __('fields.no_previous_data') . '</p>';
                                }

                                $html = '<div class="space-y-2">';
                                foreach ($activities as $act) {
                                    $actionLabel = match ($act->description) {
                                        'marked_as_read' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">' . __('forms.mark_as_read') . '</span>',
                                        'marked_as_unread' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">' . __('forms.mark_as_unread') . '</span>',
                                        'submission_received' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">' . __('forms.submission_single') . '</span>',
                                        default => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">' . e(str($act->description)->headline()) . '</span>',
                                    };

                                    $causerName = $act->causer ? $act->causer->name : __('fields.system');
                                    $dateStr = $act->created_at->format('d.m.Y H:i:s');

                                    $html .= sprintf(
                                        '<div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100 text-sm">
                                            <div class="flex items-center space-x-3">
                                                %s
                                                <span class="text-gray-700 font-medium">%s: <strong class="text-gray-900">%s</strong></span>
                                            </div>
                                            <span class="text-xs text-gray-400 font-mono">%s</span>
                                        </div>',
                                        $actionLabel,
                                        e(__('fields.user_responsible')),
                                        e($causerName),
                                        e($dateStr)
                                    );
                                }
                                $html .= '</div>';

                                return $html;
                            }),
                    ]),
            ]);
    }
}
