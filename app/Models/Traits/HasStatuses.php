<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasStatuses
 * 
 * Provides common status management functionality for models with status fields
 */
trait HasStatuses
{
    /**
     * Get the status field name (override in model if different)
     */
    protected function getStatusField(): string
    {
        return 'status';
    }

    /**
     * Get allowed status values (must be defined in model)
     */
    abstract public function getAllowedStatuses(): array;

    /**
     * Update the status
     */
    public function updateStatus(string $status, ?string $notes = null): bool
    {
        if (!$this->isValidStatus($status)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        $field = $this->getStatusField();
        $this->{$field} = $status;

        if ($notes && method_exists($this, 'addStatusNote')) {
            $this->addStatusNote($status, $notes);
        }

        return $this->save();
    }

    /**
     * Check if a status is valid
     */
    public function isValidStatus(string $status): bool
    {
        return in_array($status, $this->getAllowedStatuses());
    }

    /**
     * Check if current status matches given status
     */
    public function hasStatus(string $status): bool
    {
        $field = $this->getStatusField();
        return $this->{$field} === $status;
    }

    /**
     * Check if current status is one of the given statuses
     */
    public function hasAnyStatus(array $statuses): bool
    {
        $field = $this->getStatusField();
        return in_array($this->{$field}, $statuses);
    }

    /**
     * Get status label (human-readable)
     */
    public function getStatusLabelAttribute(): string
    {
        $field = $this->getStatusField();
        return ucwords(str_replace('_', ' ', $this->{$field}));
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        $field = $this->getStatusField();
        $status = $this->{$field};

        return match ($status) {
            'active', 'available', 'completed', 'delivered', 'approved' => 'green',
            'pending', 'processing', 'in_transit', 'awaiting_pickup' => 'blue',
            'maintenance', 'on_hold', 'awaiting_approval' => 'yellow',
            'inactive', 'unavailable', 'cancelled', 'rejected', 'failed' => 'red',
            'emergency', 'critical', 'urgent' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope: Filter by status
     */
    public function scopeWithStatus(Builder $query, string|array $status): Builder
    {
        $field = $this->getStatusField();
        
        if (is_array($status)) {
            return $query->whereIn($field, $status);
        }

        return $query->where($field, $status);
    }

    /**
     * Scope: Exclude status
     */
    public function scopeWithoutStatus(Builder $query, string|array $status): Builder
    {
        $field = $this->getStatusField();
        
        if (is_array($status)) {
            return $query->whereNotIn($field, $status);
        }

        return $query->where($field, '!=', $status);
    }

    /**
     * Get status history (if model tracks it)
     */
    public function getStatusHistory(): array
    {
        if (!method_exists($this, 'auditLogs')) {
            return [];
        }

        $field = $this->getStatusField();

        return $this->auditLogs()
            ->where(function ($query) use ($field) {
                $query->whereRaw("JSON_EXTRACT(new_values, '$.{$field}') IS NOT NULL")
                    ->orWhereRaw("JSON_EXTRACT(old_values, '$.{$field}') IS NOT NULL");
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) use ($field) {
                $newValues = json_decode($log->new_values, true);
                $oldValues = json_decode($log->old_values, true);

                return [
                    'from' => $oldValues[$field] ?? null,
                    'to' => $newValues[$field] ?? null,
                    'changed_at' => $log->created_at,
                    'changed_by' => $log->user_id,
                ];
            })
            ->toArray();
    }
}
