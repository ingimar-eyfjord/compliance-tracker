<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait and ensure a UUID is assigned before persisting.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            if (! $model->getKey()) {
                $model->setAttribute($model->getKeyName(), (string) Str::uuid());
            }
        });
    }

    /**
     * Initialize the trait by forcing string keys and disabling incrementing IDs.
     */
    protected function initializeHasUuid(): void
    {
        $this->setKeyType('string');
        $this->setIncrementing(false);
    }
}
