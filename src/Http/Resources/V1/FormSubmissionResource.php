<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FormSubmission
 */
class FormSubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'data' => $this->data ?? [],
            'files' => $this->files ?? [],
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'is_read' => (bool) $this->is_read,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
