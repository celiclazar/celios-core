<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'locale',
        'status',
        'verification_token',
        'verified_at',
        'unsubscribe_token',
        'unsubscribed_at',
        'ip_address',
        'signup_source',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = Str::random(40);
            }
            if (empty($subscriber->verification_token)) {
                $subscriber->verification_token = Str::random(40);
            }
        });
    }

    public function campaignLogs(): HasMany
    {
        return $this->hasMany(NewsletterCampaignLog::class, 'subscriber_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForLocale($query, ?string $locale = null)
    {
        if ($locale && $locale !== 'all') {
            return $query->where('locale', $locale);
        }
        return $query;
    }

    public function markVerified(): bool
    {
        return $this->update([
            'status' => 'active',
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    public function markUnsubscribed(): bool
    {
        return $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function markBounced(): bool
    {
        return $this->update([
            'status' => 'bounced',
        ]);
    }

    public function getFullNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        return $name ?: $this->email;
    }
}
