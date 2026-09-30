<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NewsletterCampaignLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'status',
        'tracking_token',
        'opened_at',
        'clicked_at',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (NewsletterCampaignLog $log) {
            if (empty($log->tracking_token)) {
                $log->tracking_token = Str::random(40);
            }
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class, 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscriber::class, 'subscriber_id');
    }
}
