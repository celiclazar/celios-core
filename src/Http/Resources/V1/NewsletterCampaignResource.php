<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\NewsletterCampaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin NewsletterCampaign
 */
class NewsletterCampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'subject' => $this->getTranslation('subject', $locale, false) ?: $this->subject,
            'preview_text' => $this->getTranslation('preview_text', $locale, false) ?: $this->preview_text,
            'content' => $this->getTranslation('content', $locale, false) ?: $this->content,
            'target_locales' => $this->target_locales,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'sent_at' => $this->sent_at?->toIso8601String(),
            'recipients_count' => $this->recipients_count ?? 0,
            'sent_count' => $this->sent_count ?? 0,
            'open_count' => $this->open_count ?? 0,
            'click_count' => $this->click_count ?? 0,
            'bounced_count' => $this->bounced_count ?? 0,
            'open_rate' => $this->open_rate,
            'click_rate' => $this->click_rate,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
