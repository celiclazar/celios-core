<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\PageRevision;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PageRevision
 */
class PageRevisionResource extends JsonResource
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
            'page_id' => $this->page_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'content' => $this->content,
            'note' => $this->note,
            'blocks_count' => $this->blocks_count,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
