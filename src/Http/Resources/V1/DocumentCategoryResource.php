<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DocumentCategory
 */
class DocumentCategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'title' => $this->getTranslation('title', $locale, false) ?: $this->title,
            'slug' => $this->getTranslation('slug', $locale, false) ?: $this->slug,
            'description' => $this->getTranslation('description', $locale, false) ?: $this->description,
            'icon' => $this->icon,
            'order' => $this->order,
            'is_active' => (bool) $this->is_active,
            'is_internal' => (bool) $this->is_internal,
            'documents_count' => $this->whenCounted('documents'),
            'children' => $this->whenLoaded('children', fn () => DocumentCategoryResource::collection($this->children)),
        ];
    }
}
