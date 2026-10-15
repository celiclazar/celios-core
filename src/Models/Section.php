<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'layout',
        'style',
        'header',
        'columns',
        'is_active',
    ];

    protected $casts = [
        'style' => 'array',
        'header' => 'array',
        'columns' => 'array',
        'is_active' => 'boolean',
    ];

    public function getGridClasses(): string
    {
        return match ($this->layout ?? '1_col') {
            '2_cols' => 'grid grid-cols-1 md:grid-cols-2',
            '3_cols' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
            '4_cols' => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
            '1_2_cols', '2_1_cols' => 'grid grid-cols-1 md:grid-cols-3',
            default => 'grid grid-cols-1',
        };
    }

    public function getColumnSpanClass(int $colIndex): string
    {
        $layout = $this->layout ?? '1_col';

        if ($layout === '1_2_cols') {
            return $colIndex === 0 ? 'md:col-span-1 w-full' : 'md:col-span-2 w-full';
        }

        if ($layout === '2_1_cols') {
            return $colIndex === 0 ? 'md:col-span-2 w-full' : 'md:col-span-1 w-full';
        }

        return 'w-full';
    }

    public function getBackgroundClasses(): string
    {
        $bg = $this->style['background'] ?? 'default';

        return match ($bg) {
            'surface_low' => 'bg-surface-container-low/70 border-y border-surface-container-high/40',
            'surface_lowest' => 'bg-surface-container-lowest shadow-sm border-y border-surface-container-high/30',
            'primary_tint' => 'bg-primary/[0.04] border-y border-primary/10',
            'dark' => 'bg-slate-900 text-white',
            'brand' => 'bg-primary text-on-primary',
            default => '',
        };
    }

    public function getPaddingClasses(): string
    {
        $pad = $this->style['padding_y'] ?? 'md';

        return match ($pad) {
            'none' => 'py-0',
            'sm' => 'py-6 sm:py-8',
            'lg' => 'py-16 sm:py-24',
            'xl' => 'py-20 sm:py-32',
            default => 'py-10 sm:py-16',
        };
    }

    public function getMaxWidthClasses(): string
    {
        $width = $this->style['max_width'] ?? 'default';

        return match ($width) {
            'narrow' => 'max-w-4xl',
            'full' => 'w-full max-w-none',
            default => 'max-w-7xl',
        };
    }

    public function getGapClasses(): string
    {
        $gap = $this->style['gap'] ?? 'md';

        return match ($gap) {
            'none' => 'gap-0',
            'sm' => 'gap-4 sm:gap-6',
            'lg' => 'gap-8 sm:gap-12',
            default => 'gap-6 sm:gap-8',
        };
    }

    public function getItemsAlignClasses(): string
    {
        $align = $this->style['items_align'] ?? 'top';

        return match ($align) {
            'center' => 'items-center',
            'bottom' => 'items-end',
            'stretch' => 'items-stretch',
            default => 'items-start',
        };
    }

    /**
     * Resolves localized header value.
     */
    public function getHeaderField(string $field): ?string
    {
        $header = $this->header ?? [];
        $val = $header[$field] ?? null;

        if (empty($val)) {
            return null;
        }

        if (is_array($val)) {
            $locale = app()->getLocale();

            return $val[$locale] ?? $val['sr'] ?? $val['en'] ?? $val['it'] ?? null;
        }

        return is_string($val) ? $val : null;
    }
}
