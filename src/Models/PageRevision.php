<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageRevision extends Model
{
    protected $fillable = [
        'page_id',
        'user_id',
        'title',
        'content',
        'note',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBlocksCountAttribute(): int
    {
        return is_array($this->content) ? count($this->content) : 0;
    }

    public function getBlockTypesAttribute(): array
    {
        if (! is_array($this->content)) {
            return [];
        }

        return collect($this->content)
            ->pluck('type')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function isCurrentLive(): bool
    {
        return $this->page && json_encode($this->content) === json_encode($this->page->content);
    }
}
