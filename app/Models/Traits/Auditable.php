<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Trait Auditable
 * 
 * Automatically logs model actions (create, update, delete) to audit_logs table
 */
trait Auditable
{
    /**
     * Boot the auditable trait for a model
     */
    public static function bootAuditable(): void
    {
        // Log on creation
        static::created(function ($model) {
            $model->auditAction('created', null, $model->getAuditableAttributes());
        });

        // Log on update
        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();

            // Remove timestamps if not tracking them
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $model->auditAction('updated', $original, $changes);
            }
        });

        // Log on deletion
        static::deleted(function ($model) {
            $model->auditAction('deleted', $model->getAuditableAttributes(), null);
        });
    }

    /**
     * Create an audit log entry
     */
    protected function auditAction(string $action, ?array $oldValues = null, ?array $newValues = null): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'action' => $action,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'severity' => $this->getAuditSeverity($action),
        ]);
    }

    /**
     * Get attributes to audit (override in model if needed)
     */
    protected function getAuditableAttributes(): array
    {
        $hidden = $this->getHidden();
        return collect($this->getAttributes())
            ->except(array_merge($hidden, ['password', 'remember_token']))
            ->toArray();
    }

    /**
     * Determine audit severity based on action
     */
    protected function getAuditSeverity(string $action): string
    {
        return match ($action) {
            'deleted' => 'warning',
            'created' => 'info',
            'updated' => 'info',
            default => 'info',
        };
    }

    /**
     * Get audit logs for this model
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Get the latest audit log
     */
    public function latestAudit()
    {
        return $this->morphOne(AuditLog::class, 'auditable')->latestOfMany();
    }
}
