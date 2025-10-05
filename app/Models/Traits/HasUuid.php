<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasUuid
 * 
 * Automatically generates UUIDs for models
 */
trait HasUuid
{
    /**
     * Boot the trait
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key name
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope: Find by UUID
     */
    public function scopeByUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }
}
