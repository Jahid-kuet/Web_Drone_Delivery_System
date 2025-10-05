<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasPriority
 * 
 * Provides priority management for models with priority fields
 */
trait HasPriority
{
    /**
     * Priority constants
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';
    public const PRIORITY_EMERGENCY = 'emergency';

    /**
     * Get the priority field name
     */
    protected function getPriorityField(): string
    {
        return 'priority';
    }

    /**
     * Get all priority levels
     */
    public static function getPriorityLevels(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_NORMAL,
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT,
            self::PRIORITY_EMERGENCY,
        ];
    }

    /**
     * Get priority weight for sorting
     */
    public function getPriorityWeight(): int
    {
        $field = $this->getPriorityField();
        
        return match ($this->{$field}) {
            self::PRIORITY_EMERGENCY => 5,
            self::PRIORITY_URGENT => 4,
            self::PRIORITY_HIGH => 3,
            self::PRIORITY_NORMAL => 2,
            self::PRIORITY_LOW => 1,
            default => 0,
        };
    }

    /**
     * Check if priority is emergency
     */
    public function isEmergency(): bool
    {
        $field = $this->getPriorityField();
        return $this->{$field} === self::PRIORITY_EMERGENCY;
    }

    /**
     * Check if priority is urgent or higher
     */
    public function isUrgent(): bool
    {
        $field = $this->getPriorityField();
        return in_array($this->{$field}, [self::PRIORITY_URGENT, self::PRIORITY_EMERGENCY]);
    }

    /**
     * Check if priority is high or higher
     */
    public function isHighPriority(): bool
    {
        $field = $this->getPriorityField();
        return in_array($this->{$field}, [
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT,
            self::PRIORITY_EMERGENCY
        ]);
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        $field = $this->getPriorityField();
        
        return match ($this->{$field}) {
            self::PRIORITY_EMERGENCY => 'red',
            self::PRIORITY_URGENT => 'orange',
            self::PRIORITY_HIGH => 'yellow',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_LOW => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        $field = $this->getPriorityField();
        return ucfirst($this->{$field});
    }

    /**
     * Scope: Filter by priority
     */
    public function scopeWithPriority(Builder $query, string|array $priority): Builder
    {
        $field = $this->getPriorityField();
        
        if (is_array($priority)) {
            return $query->whereIn($field, $priority);
        }

        return $query->where($field, $priority);
    }

    /**
     * Scope: High priority items
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        $field = $this->getPriorityField();
        
        return $query->whereIn($field, [
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT,
            self::PRIORITY_EMERGENCY
        ]);
    }

    /**
     * Scope: Emergency items
     */
    public function scopeEmergency(Builder $query): Builder
    {
        $field = $this->getPriorityField();
        return $query->where($field, self::PRIORITY_EMERGENCY);
    }

    /**
     * Scope: Order by priority (highest first)
     */
    public function scopeOrderByPriority(Builder $query, string $direction = 'desc'): Builder
    {
        $field = $this->getPriorityField();
        
        // Custom ordering by priority weight
        return $query->orderByRaw("
            CASE {$field}
                WHEN 'emergency' THEN 5
                WHEN 'urgent' THEN 4
                WHEN 'high' THEN 3
                WHEN 'normal' THEN 2
                WHEN 'low' THEN 1
                ELSE 0
            END {$direction}
        ");
    }
}
