<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class NewsletterCampaign extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'subject',
        'preview_text',
        'content',
        'target_locales',
        'status',
        'scheduled_at',
        'sent_at',
        'recipients_count',
        'sent_count',
        'open_count',
        'click_count',
        'bounced_count',
        'batch_id',
    ];

    public array $translatable = [
        'subject',
        'preview_text',
        'content',
    ];

    protected $casts = [
        'target_locales' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'recipients_count' => 'integer',
        'sent_count' => 'integer',
        'open_count' => 'integer',
        'click_count' => 'integer',
        'bounced_count' => 'integer',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterCampaignLog::class, 'campaign_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSending($query)
    {
        return $query->where('status', 'sending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count <= 0) {
            return 0.0;
        }
        return round(($this->open_count / $this->sent_count) * 100, 1);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->sent_count <= 0) {
            return 0.0;
        }
        return round(($this->click_count / $this->sent_count) * 100, 1);
    }
}
