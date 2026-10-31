<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Media
 */
class MediaResource extends JsonResource
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
            'name' => $this->name,
            'path' => $this->path,
            'url' => $this->url ? url($this->url) : null,
            'type' => $this->type,
            'ext' => $this->ext,
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'alt' => $this->alt,
            'title' => $this->title,
            'description' => $this->description,
            'caption' => $this->caption,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
