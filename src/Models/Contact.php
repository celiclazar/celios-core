<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contact extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'stage',
        'source',
        'lead_value',
        'assigned_to_user_id',
        'form_submission_id',
        'notes',
        'last_contacted_at',
    ];

    protected $casts = [
        'lead_value' => 'decimal:2',
        'last_contacted_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function formSubmission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(ContactNote::class)->latest();
    }

    public function contactNotes(): HasMany
    {
        return $this->hasMany(ContactNote::class)->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'stage', 'lead_value', 'assigned_to_user_id'])
            ->logOnlyDirty()
            ->useLogName('CRM')
            ->setDescriptionForEvent(fn (string $eventName) => "Contact {$this->name} was {$eventName}");
    }

    public static function getStages(): array
    {
        return [
            'lead' => 'New Lead',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'proposal' => 'Proposal Sent',
            'won' => 'Won / Closed',
            'lost' => 'Lost',
        ];
    }

    public static function getStageColors(): array
    {
        return [
            'lead' => 'info',
            'contacted' => 'primary',
            'qualified' => 'warning',
            'proposal' => 'secondary',
            'won' => 'success',
            'lost' => 'danger',
        ];
    }
}
