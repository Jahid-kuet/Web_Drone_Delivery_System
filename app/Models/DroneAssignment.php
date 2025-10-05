<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DroneAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'drone_id',
        'delivery_id',
        'assigned_by_user_id',
        'assignment_status',
        'assigned_at',
        'accepted_at',
        'started_at',
        'completed_at',
        'assignment_notes',
        'pilot_notes',
        'rejection_reason',
        'completion_notes',
        'estimated_duration_minutes',
        'actual_duration_minutes',
        'estimated_distance_km',
        'actual_distance_km',
        'estimated_battery_usage',
        'actual_battery_usage',
        'pre_flight_checklist',
        'post_flight_report',
        'priority_override',
        'metadata'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_duration_minutes' => 'decimal:2',
        'actual_duration_minutes' => 'decimal:2',
        'estimated_distance_km' => 'decimal:3',
        'actual_distance_km' => 'decimal:3',
        'estimated_battery_usage' => 'decimal:2',
        'actual_battery_usage' => 'decimal:2',
        'pre_flight_checklist' => 'array',
        'post_flight_report' => 'array',
        'metadata' => 'array'
    ];

    protected $dates = ['deleted_at'];

    // Assignment status constants
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

    /**
     * Get the assigned drone
     */
    public function drone(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    /**
     * Get the delivery
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the user who made the assignment
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('assignment_status', [
            self::STATUS_ASSIGNED,
            self::STATUS_ACCEPTED,
            self::STATUS_IN_PROGRESS
        ]);
    }

    /**
     * Scope for completed assignments
     */
    public function scopeCompleted($query)
    {
        return $query->where('assignment_status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for pending assignments
     */
    public function scopePending($query)
    {
        return $query->where('assignment_status', self::STATUS_ASSIGNED);
    }

    /**
     * Accept the assignment
     */
    public function accept($notes = null): bool
    {
        $this->assignment_status = self::STATUS_ACCEPTED;
        $this->accepted_at = now();
        $this->pilot_notes = $notes;
        
        return $this->save();
    }

    /**
     * Reject the assignment
     */
    public function reject($reason): bool
    {
        $this->assignment_status = self::STATUS_REJECTED;
        $this->rejection_reason = $reason;
        
        // Free up the drone
        if ($this->drone) {
            $this->drone->status = Drone::STATUS_AVAILABLE;
            $this->drone->save();
        }
        
        return $this->save();
    }

    /**
     * Start the assignment
     */
    public function start(): bool
    {
        $this->assignment_status = self::STATUS_IN_PROGRESS;
        $this->started_at = now();
        
        return $this->save();
    }

    /**
     * Complete the assignment
     */
    public function complete($notes = null, $postFlightReport = null): bool
    {
        $this->assignment_status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->completion_notes = $notes;
        $this->post_flight_report = $postFlightReport;
        
        // Calculate actual metrics
        if ($this->started_at) {
            $this->actual_duration_minutes = $this->started_at->diffInMinutes($this->completed_at);
        }
        
        return $this->save();
    }

    /**
     * Cancel the assignment
     */
    public function cancel($reason = null): bool
    {
        $this->assignment_status = self::STATUS_CANCELLED;
        $this->rejection_reason = $reason;
        
        // Free up the drone
        if ($this->drone) {
            $this->drone->status = Drone::STATUS_AVAILABLE;
            $this->drone->save();
        }
        
        return $this->save();
    }

    /**
     * Check if assignment is active
     */
    public function isActive(): bool
    {
        return in_array($this->assignment_status, [
            self::STATUS_ASSIGNED,
            self::STATUS_ACCEPTED,
            self::STATUS_IN_PROGRESS
        ]);
    }

    /**
     * Check if assignment is completed
     */
    public function isCompleted(): bool
    {
        return $this->assignment_status === self::STATUS_COMPLETED;
    }

    /**
     * Get assignment duration
     */
    public function getDurationAttribute(): ?float
    {
        if ($this->actual_duration_minutes) {
            return $this->actual_duration_minutes;
        }
        
        if ($this->started_at && $this->assignment_status === self::STATUS_IN_PROGRESS) {
            return $this->started_at->diffInMinutes(now());
        }
        
        return $this->estimated_duration_minutes;
    }

    /**
     * Get efficiency rating
     */
    public function getEfficiencyRatingAttribute(): ?float
    {
        if (!$this->estimated_duration_minutes || !$this->actual_duration_minutes) {
            return null;
        }
        
        $efficiency = ($this->estimated_duration_minutes / $this->actual_duration_minutes) * 100;
        return min(100, max(0, $efficiency));
    }

    /**
     * Get battery efficiency
     */
    public function getBatteryEfficiencyAttribute(): ?float
    {
        if (!$this->estimated_battery_usage || !$this->actual_battery_usage) {
            return null;
        }
        
        $efficiency = ($this->estimated_battery_usage / $this->actual_battery_usage) * 100;
        return min(100, max(0, $efficiency));
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->assignment_status));
    }

    /**
     * Get assignment statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_FAILED => 'Failed'
        ];
    }
}
