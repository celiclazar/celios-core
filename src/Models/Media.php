<?php

namespace Celios\Core\Models;

use Awcodes\Curator\Models\Media as CuratorMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Media extends CuratorMedia
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    public function url(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if (empty($this->path)) {
                    return '';
                }

                if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
                    return $this->path;
                }

                return '/storage/' . ltrim($this->path, '/');
            }
        );
    }
}
