<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MenuItem
 */
class MenuItemResource extends JsonResource
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
            'menu_id' => $this->menu_id,
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'title' => $this->getTranslation('title', $locale, false) ?: $this->title,
            'url' => $this->url,
            'custom_url' => $this->getTranslation('custom_url', $locale, false) ?: $this->custom_url,
            'target' => $this->target ?? '_self',
            'is_visible' => (bool) $this->is_visible,
            'children' => $this->whenLoaded('children', fn () => MenuItemResource::collection($this->children)),
        ];
    }
}
