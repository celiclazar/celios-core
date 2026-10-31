<?php

namespace Celios\Core\Http\Resources\V1;

use Celios\Core\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Form
 */
class FormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $rawFields = is_array($this->fields) ? $this->fields : [];

        $localizedFields = array_map(function (array $field) use ($locale) {
            $label = $field['label'] ?? ($field['key'] ?? '');
            if (is_array($label)) {
                $label = $label[$locale] ?? $label['sr'] ?? $label['en'] ?? reset($label) ?: '';
            }

            $placeholder = $field['placeholder'] ?? null;
            if (is_array($placeholder)) {
                $placeholder = $placeholder[$locale] ?? $placeholder['sr'] ?? $placeholder['en'] ?? reset($placeholder);
            }

            return array_merge($field, [
                'label' => $label,
                'placeholder' => $placeholder,
            ]);
        }, $rawFields);

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale, false) ?: $this->title,
            'slug' => $this->slug,
            'description' => $this->getTranslation('description', $locale, false) ?: $this->description,
            'fields' => $localizedFields,
            'submit_button_text' => $this->getTranslation('submit_button_text', $locale, false) ?: $this->submit_button_text,
            'success_message' => $this->getTranslation('success_message', $locale, false) ?: $this->success_message,
            'redirect_type' => $this->redirect_type,
            'redirect_url' => $this->redirect_url,
            'is_active' => (bool) $this->is_active,
            'submissions_count' => $this->whenCounted('submissions'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
