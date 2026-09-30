<?php

namespace Celios\Core\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Celios\Core\Models\Category
 */
class CategoryResource extends JsonResource
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
            'order' => $this->order,
            'posts_count' => $this->whenCounted('posts'),
            'parent' => $this->whenLoaded('parent', fn () => new CategoryResource($this->parent)),
            'children' => $this->whenLoaded('children', fn () => CategoryResource::collection($this->children)),
        ];
    }
}
