<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FormSubmission extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'form_id',
        'data',
        'files',
        'ip_address',
        'user_agent',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'files' => 'array',
        'is_read' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['is_read'])
            ->logOnlyDirty()
            ->useLogName('Form Submissions')
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'submission_received',
                'updated' => $this->is_read ? 'marked_as_read' : 'marked_as_unread',
                'deleted' => 'deleted',
                default => $eventName,
            });
    }

    public function displayTitle(?string $locale = null): string
    {
        $formTitle = $this->form ? $this->form->displayTitle($locale) : __('forms.form_single');

        return "{$formTitle} #{$this->id}";
    }
}
