<?php

namespace Celios\Core\Filament\Helpers;

class BlockLabelHelper
{
    /**
     * Resolves a localized preview string from block state and appends it to the base label.
     *
     * Example output: "Hero sekcija: Dobrodošli na platformu"
     */
    public static function make(
        string $baseLabel,
        ?array $state = null,
        array $fieldKeys = ['heading', 'title', 'name', 'badge_text', 'section_tag', 'body', 'caption'],
        int $maxLength = 50
    ): string {
        if (empty($state)) {
            return $baseLabel;
        }

        $previewText = null;

        foreach ($fieldKeys as $key) {
            if (empty($state[$key])) {
                continue;
            }

            $val = $state[$key];

            if (is_array($val)) {
                $locale = app()->getLocale();
                $previewText = $val[$locale] 
                    ?? $val['sr'] 
                    ?? $val['en'] 
                    ?? $val['it'] 
                    ?? collect($val)->first(fn ($item) => !empty($item) && is_string($item));
            } elseif (is_string($val)) {
                $previewText = $val;
            }

            if (!empty($previewText)) {
                break;
            }
        }

        if (empty($previewText)) {
            return $baseLabel;
        }

        $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags((string) $previewText)));

        if (empty($cleanText)) {
            return $baseLabel;
        }

        $truncated = mb_strlen($cleanText) > $maxLength
            ? mb_substr($cleanText, 0, $maxLength) . '...'
            : $cleanText;

        return "{$baseLabel}: {$truncated}";
    }

    /**
     * Appends an item count or summary to the base label.
     *
     * Example output: "Često postavljana pitanja (4 pitanja)"
     */
    public static function withCount(
        string $baseLabel,
        ?array $state = null,
        string $arrayKey = 'items',
        string $singularUnit = 'stavka',
        string $pluralUnit = 'stavki'
    ): string {
        if (empty($state)) {
            return $baseLabel;
        }

        // Try heading/title first if available
        $headingPreview = null;
        foreach (['heading', 'title', 'name'] as $key) {
            if (!empty($state[$key])) {
                $val = $state[$key];
                $locale = app()->getLocale();
                $headingPreview = is_array($val) ? ($val[$locale] ?? $val['sr'] ?? $val['en'] ?? null) : $val;
                if (!empty($headingPreview)) {
                    break;
                }
            }
        }

        $items = $state[$arrayKey] ?? [];
        $count = is_array($items) ? count($items) : 0;
        $countText = $count === 1 ? "1 {$singularUnit}" : "{$count} {$pluralUnit}";

        if (!empty($headingPreview)) {
            $cleanHeading = trim(preg_replace('/\s+/', ' ', strip_tags((string) $headingPreview)));
            $truncated = mb_strlen($cleanHeading) > 35 ? mb_substr($cleanHeading, 0, 35) . '...' : $cleanHeading;
            return "{$baseLabel}: {$truncated} ({$countText})";
        }

        if ($count > 0) {
            return "{$baseLabel} ({$countText})";
        }

        return $baseLabel;
    }
}
