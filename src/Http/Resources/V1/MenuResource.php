<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Menu
 */
class MenuResource extends JsonResource
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
            'key' => $this->key,
            'name' => $this->name,
            'items_count' => $this->whenCounted('menuItems'),
            'items' => $this->whenLoaded('menuItems', fn () => MenuItemResource::collection($this->menuItems)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
