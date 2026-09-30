<?php

namespace Celios\Core\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Celios\Core\Models\Document
 */
class DocumentResource extends JsonResource
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
            'title' => $this->getTranslation('title', $locale, false) ?: $this->title,
            'slug' => $this->getTranslation('slug', $locale, false) ?: $this->slug,
            'description' => $this->getTranslation('description', $locale, false) ?: $this->description,
            'file_name' => $this->file_name,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'version' => $this->version,
            'access_level' => $this->access_level?->value ?? $this->access_level,
            'downloads_count' => $this->downloads_count ?? 0,
            'download_url' => url("/api/v1/documents/{$this->id}/download"),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'name' => $this->category?->getTranslation('name', $locale, false) ?: $this->category?->name,
                'slug' => $this->category?->getTranslation('slug', $locale, false) ?: $this->category?->slug,
            ]),
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
