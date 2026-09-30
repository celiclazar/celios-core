<?php

namespace Celios\Core\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Celios\Core\Models\Post
 */
class PostResource extends JsonResource
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
            'excerpt' => $this->getTranslation('excerpt', $locale, false) ?: $this->excerpt,
            'body' => $this->getTranslation('body', $locale, false) ?: $this->body,
            'is_published' => (bool) $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'views_count' => $this->views_count ?? 0,
            'featured_image' => $this->whenLoaded('featuredImage', function () {
                if (! $this->featuredImage) {
                    return null;
                }
                return [
                    'id' => $this->featuredImage->id,
                    'name' => $this->featuredImage->name ?? null,
                    'url' => $this->featuredImage->url ? url($this->featuredImage->url) : null,
                    'alt' => $this->featuredImage->alt ?? null,
                ];
            }),
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'author' => $this->whenLoaded('author', fn () => [
                'id' => $this->author?->id,
                'name' => $this->author?->name,
                'avatar' => $this->author?->avatar ? url($this->author->avatar) : null,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
