<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class DeliveryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'requested_by_user_id',
        'request_number',
        'priority',
        'status',
        'medical_supplies',
        'total_weight_kg',
        'total_volume_ml',
        'description',
        'urgency_level',
        'requested_delivery_time',
        'latest_acceptable_time',
        'pickup_location',
        'delivery_location',
        'special_instructions',
        'handling_requirements',
        'requires_signature',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'estimated_cost',
        'approved_by_user_id',
        'approved_at',
        'approval_notes',
        'cancellation_reason',
        'cancelled_at',
        'metadata'
    ];

    protected $casts = [
        'medical_supplies' => 'array',
        'pickup_location' => 'array',
        'delivery_location' => 'array',
        'handling_requirements' => 'array',
        'metadata' => 'array',
        'total_weight_kg' => 'decimal:3',
        'total_volume_ml' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'requires_signature' => 'boolean',
        'requested_delivery_time' => 'datetime',
        'latest_acceptable_time' => 'datetime',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    protected $dates = ['deleted_at'];

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_EMERGENCY = 'emergency';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    // Urgency level constants
    const URGENCY_ROUTINE = 'routine';
    const URGENCY_URGENT = 'urgent';
    const URGENCY_EMERGENCY = 'emergency';
    const URGENCY_LIFE_THREATENING = 'life_threatening';

    /**
     * Get the hospital that made this request
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the user who made this request
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * Get the user who approved this request
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /**
     * Get the delivery associated with this request
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    /**
     * Get all deliveries associated with this request (alias)
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get the medical supply for this request (if single supply)
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(MedicalSupply::class, 'medical_supply_id');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for high priority requests
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL, self::PRIORITY_EMERGENCY]);
    }

    /**
     * Scope for emergency requests
     */
    public function scopeEmergency($query)
    {
        return $query->where('urgency_level', self::URGENCY_EMERGENCY)
                    ->orWhere('urgency_level', self::URGENCY_LIFE_THREATENING);
    }

    /**
     * Scope for requests by hospital
     */
    public function scopeByHospital($query, $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    /**
     * Get the destination hospital name from delivery location payload.
     */
    public function getDestinationHospitalNameAttribute(): ?string
    {
        $location = $this->delivery_location;

        if (is_string($location)) {
            $decoded = json_decode($location, true);
            $location = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        if (is_array($location)) {
            return $location['hospital_name']
                ?? $location['name']
                ?? null;
        }

        return null;
    }

    /**
     * Scope for overdue requests
     */
    public function scopeOverdue($query)
    {
        return $query->where('latest_acceptable_time', '<', now())
                    ->whereNotIn('status', [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if request is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is emergency
     */
    public function isEmergency(): bool
    {
        return in_array($this->urgency_level, [self::URGENCY_EMERGENCY, self::URGENCY_LIFE_THREATENING]) ||
               in_array($this->priority, [self::PRIORITY_CRITICAL, self::PRIORITY_EMERGENCY]);
    }

    /**
     * Check if request is overdue
     */
    public function isOverdue(): bool
    {
        return $this->latest_acceptable_time && 
               $this->latest_acceptable_time->isPast() &&
               !in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    /**
     * Approve the request
     */
    public function approve(User $approver, $notes = null): bool
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by_user_id = $approver->id;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        
        return $this->save();
    }

    /**
     * Reject the request
     */
    public function reject($reason = null): bool
    {
        $this->status = self::STATUS_REJECTED;
        $this->cancellation_reason = $reason;
        $this->cancelled_at = now();
        
        return $this->save();
    }

    /**
     * Cancel the request
     */
    public function cancel($reason = null): bool
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancellation_reason = $reason;
        $this->cancelled_at = now();
        
        return $this->save();
    }

    /**
     * Generate unique request number
     */
    public static function generateRequestNumber(): string
    {
        $prefix = 'DR';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', now()->toDateString())->count() + 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute(): string
    {
        switch ($this->priority) {
            case self::PRIORITY_EMERGENCY:
                return 'red';
            case self::PRIORITY_CRITICAL:
                return 'orange';
            case self::PRIORITY_HIGH:
                return 'yellow';
            case self::PRIORITY_MEDIUM:
                return 'blue';
            default:
                return 'gray';
        }
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get time until delivery
     */
    public function getTimeUntilDeliveryAttribute(): string
    {
        if (!$this->requested_delivery_time) return 'Not specified';
        
        return $this->requested_delivery_time->diffForHumans();
    }

    /**
     * Get supply count
     */
    public function getSupplyCountAttribute(): int
    {
        return count($this->medical_supplies ?? []);
    }

    /**
     * Get priorities array
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_CRITICAL => 'Critical',
            self::PRIORITY_EMERGENCY => 'Emergency'
        ];
    }

    /**
     * Get statuses array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REJECTED => 'Rejected'
        ];
    }

    /**
     * Get urgency levels array
     */
    public static function getUrgencyLevels(): array
    {
        return [
            self::URGENCY_ROUTINE => 'Routine',
            self::URGENCY_URGENT => 'Urgent',
            self::URGENCY_EMERGENCY => 'Emergency',
            self::URGENCY_LIFE_THREATENING => 'Life Threatening'
        ];
    }
}
