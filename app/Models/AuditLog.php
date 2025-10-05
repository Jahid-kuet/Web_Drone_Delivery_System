<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_identifier',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id',
        'severity',
        'description',
        'context',
        'batch_id',
        'metadata'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'context' => 'array',
        'metadata' => 'array'
    ];

    // Action constants
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_RESTORED = 'restored';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_APPROVED = 'approved';
    const ACTION_REJECTED = 'rejected';
    const ACTION_ASSIGNED = 'assigned';
    const ACTION_CANCELLED = 'cancelled';

    // Severity constants
    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_ERROR = 'error';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the audited model (polymorphic)
     */
    public function auditable()
    {
        if ($this->model_type && $this->model_id) {
            return $this->morphTo('auditable', 'model_type', 'model_id');
        }
        return null;
    }

    /**
     * Scope by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope by model
     */
    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId !== null) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope by batch
     */
    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for critical logs
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }

    /**
     * Get changes summary
     */
    public function getChangesSummaryAttribute(): array
    {
        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                if (isset($this->old_values[$key]) && $this->old_values[$key] !== $newValue) {
                    $changes[$key] = [
                        'old' => $this->old_values[$key],
                        'new' => $newValue
                    ];
                }
            }
        }
        
        return $changes;
    }

    /**
     * Get severity color
     */
    public function getSeverityColorAttribute(): string
    {
        switch ($this->severity) {
            case self::SEVERITY_CRITICAL:
                return 'danger';
            case self::SEVERITY_ERROR:
                return 'warning';
            case self::SEVERITY_WARNING:
                return 'info';
            default:
                return 'secondary';
        }
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Log an action
     */
    public static function logAction($action, $modelType, $modelId, $data = []): self
    {
        return static::create(array_merge([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'severity' => self::SEVERITY_INFO
        ], $data));
    }

    /**
     * Get actions array
     */
    public static function getActions(): array
    {
        return [
            self::ACTION_CREATED => 'Created',
            self::ACTION_UPDATED => 'Updated',
            self::ACTION_DELETED => 'Deleted',
            self::ACTION_RESTORED => 'Restored',
            self::ACTION_LOGIN => 'Login',
            self::ACTION_LOGOUT => 'Logout',
            self::ACTION_APPROVED => 'Approved',
            self::ACTION_REJECTED => 'Rejected',
            self::ACTION_ASSIGNED => 'Assigned',
            self::ACTION_CANCELLED => 'Cancelled'
        ];
    }

    /**
     * Get severities array
     */
    public static function getSeverities(): array
    {
        return [
            self::SEVERITY_INFO => 'Info',
            self::SEVERITY_WARNING => 'Warning',
            self::SEVERITY_ERROR => 'Error',
            self::SEVERITY_CRITICAL => 'Critical'
        ];
    }
}
