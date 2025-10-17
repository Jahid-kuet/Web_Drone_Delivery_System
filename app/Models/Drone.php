<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Drone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'serial_number',
        'registration_number',
        'status',
        'assigned_operator_id',
        'type',
        'max_payload_kg',
        'max_range_km',
        'max_altitude_m',
        'max_speed_kmh',
        'battery_life_minutes',
        'current_battery_level',
        'gps_coordinates',
        'current_altitude_m',
        'current_speed_kmh',
        'firmware_version',
        'sensors',
        'has_camera',
        'has_temperature_control',
        'has_emergency_parachute',
        'temperature_min_celsius',
        'temperature_max_celsius',
        'operator_license_required',
        'last_maintenance_date',
        'next_maintenance_due',
        'total_flight_hours',
        'total_deliveries',
        'flight_restrictions',
        'insurance_policy_number',
        'insurance_expiry_date',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'gps_coordinates' => 'array',
        'sensors' => 'array',
        'flight_restrictions' => 'array',
        'metadata' => 'array',
        'max_payload_kg' => 'decimal:3',
        'max_range_km' => 'decimal:2',
        'max_altitude_m' => 'decimal:2',
        'max_speed_kmh' => 'decimal:2',
        'current_battery_level' => 'decimal:2',
        'current_altitude_m' => 'decimal:2',
        'current_speed_kmh' => 'decimal:2',
        'temperature_min_celsius' => 'decimal:2',
        'temperature_max_celsius' => 'decimal:2',
        'has_camera' => 'boolean',
        'has_temperature_control' => 'boolean',
        'has_emergency_parachute' => 'boolean',
        'is_active' => 'boolean',
        'last_maintenance_date' => 'date',
        'next_maintenance_due' => 'date',
        'insurance_expiry_date' => 'date'
    ];

    protected $dates = ['deleted_at'];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_FLIGHT = 'in_flight';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_CHARGING = 'charging';
    const STATUS_OFFLINE = 'offline';
    const STATUS_EMERGENCY = 'emergency';

    // Type constants
    const TYPE_MEDICAL_TRANSPORT = 'medical_transport';
    const TYPE_EMERGENCY_RESPONSE = 'emergency_response';
    const TYPE_BLOOD_DELIVERY = 'blood_delivery';
    const TYPE_PHARMACEUTICAL = 'pharmaceutical';
    const TYPE_MULTI_PURPOSE = 'multi_purpose';

    /**
     * Get the operator assigned to this drone
     */
    public function assignedOperator()
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }

    /**
     * Get all assignments for this drone
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(DroneAssignment::class);
    }

    /**
     * Get current active assignment
     */
    public function currentAssignment(): HasOne
    {
        return $this->hasOne(DroneAssignment::class)->where('assignment_status', 'in_progress');
    }

    /**
     * Get all deliveries for this drone
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get current active delivery
     */
    public function currentDelivery(): HasOne
    {
        return $this->hasOne(Delivery::class)->whereIn('status', [
            'scheduled', 'preparing', 'loaded', 'departed', 'in_transit', 'approaching_destination'
        ]);
    }

    /**
     * Get tracking records for this drone
     */
    public function trackingRecords(): HasMany
    {
        return $this->hasMany(DeliveryTracking::class);
    }

    /**
     * Get recent tracking records
     */
    public function recentTracking(): HasMany
    {
        return $this->trackingRecords()->orderBy('recorded_at', 'desc')->limit(10);
    }

    /**
     * Scope for available drones
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE)
                    ->where('is_active', true)
                    ->where('current_battery_level', '>=', 20);
    }

    /**
     * Scope for active drones
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for drones in flight
     */
    public function scopeInFlight($query)
    {
        return $query->where('status', self::STATUS_IN_FLIGHT);
    }

    /**
     * Scope for drones by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for drones needing maintenance
     */
    public function scopeNeedsMaintenance($query)
    {
        return $query->where('next_maintenance_due', '<=', now()->addDays(7));
    }

    /**
     * Scope for drones with low battery
     */
    public function scopeLowBattery($query, $threshold = 20)
    {
        return $query->where('current_battery_level', '<', $threshold);
    }

    /**
     * Check if drone is available for assignment
     */
    public function isAvailableForAssignment(): bool
    {
        return $this->status === self::STATUS_AVAILABLE &&
               $this->is_active &&
               $this->current_battery_level >= 20 &&
               !$this->needsMaintenanceUrgently();
    }

    /**
     * Check if drone needs maintenance urgently
     */
    public function needsMaintenanceUrgently(): bool
    {
        return $this->next_maintenance_due && 
               $this->next_maintenance_due->isPast();
    }

    /**
     * Check if drone needs maintenance soon
     */
    public function needsMaintenanceSoon($days = 7): bool
    {
        return $this->next_maintenance_due && 
               $this->next_maintenance_due->isBetween(now(), now()->addDays($days));
    }

    /**
     * Check if drone has low battery
     */
    public function hasLowBattery($threshold = 20): bool
    {
        return $this->current_battery_level < $threshold;
    }

    /**
     * Check if drone is currently in flight
     */
    public function isInFlight(): bool
    {
        return $this->status === self::STATUS_IN_FLIGHT;
    }

    /**
     * Check if drone can carry payload
     */
    public function canCarryPayload($weight): bool
    {
        return $this->max_payload_kg >= $weight;
    }

    /**
     * Check if drone can reach distance
     */
    public function canReachDistance($distance): bool
    {
        $efficiency = 0.8; // Battery efficiency factor
        return ($this->max_range_km * $efficiency) >= $distance;
    }

    /**
     * Get battery status color
     */
    public function getBatteryStatusColorAttribute(): string
    {
        if ($this->current_battery_level >= 80) return 'success';
        if ($this->current_battery_level >= 50) return 'warning';
        if ($this->current_battery_level >= 20) return 'danger';
        return 'critical';
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get maintenance status
     */
    public function getMaintenanceStatusAttribute(): string
    {
        if ($this->needsMaintenanceUrgently()) return 'Overdue';
        if ($this->needsMaintenanceSoon()) return 'Due Soon';
        return 'Current';
    }

    /**
     * Get flight time in hours
     */
    public function getFlightTimeHoursAttribute(): string
    {
        return number_format($this->total_flight_hours, 1) . ' hrs';
    }

    /**
     * Get efficiency rating
     */
    public function getEfficiencyRatingAttribute(): float
    {
        if ($this->total_deliveries === 0) return 0;
        return ($this->total_deliveries / max($this->total_flight_hours, 1)) * 10;
    }

    /**
     * Update drone position
     */
    public function updatePosition($latitude, $longitude, $altitude = null, $speed = null): bool
    {
        $this->gps_coordinates = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'updated_at' => now()->toISOString()
        ];

        if ($altitude !== null) {
            $this->current_altitude_m = $altitude;
        }

        if ($speed !== null) {
            $this->current_speed_kmh = $speed;
        }

        return $this->save();
    }

    /**
     * Update battery level
     */
    public function updateBatteryLevel($level): bool
    {
        $this->current_battery_level = max(0, min(100, $level));
        
        // Auto-update status based on battery level
        if ($this->current_battery_level < 10 && $this->status === self::STATUS_IN_FLIGHT) {
            $this->status = self::STATUS_EMERGENCY;
        } elseif ($this->current_battery_level < 20 && $this->status === self::STATUS_AVAILABLE) {
            $this->status = self::STATUS_CHARGING;
        }

        return $this->save();
    }

    /**
     * Assign to delivery
     */
    public function assignToDelivery(Delivery $delivery): bool
    {
        if (!$this->isAvailableForAssignment()) {
            return false;
        }

        $this->status = self::STATUS_ASSIGNED;
        return $this->save();
    }

    /**
     * Mark as in flight
     */
    public function takeOff(): bool
    {
        $this->status = self::STATUS_IN_FLIGHT;
        return $this->save();
    }

    /**
     * Mark as landed/available
     */
    public function land(): bool
    {
        $this->status = self::STATUS_AVAILABLE;
        $this->increment('total_deliveries');
        return $this->save();
    }

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_FLIGHT => 'In Flight',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_CHARGING => 'Charging',
            self::STATUS_OFFLINE => 'Offline',
            self::STATUS_EMERGENCY => 'Emergency'
        ];
    }

    /**
     * Get drone types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_MEDICAL_TRANSPORT => 'Medical Transport',
            self::TYPE_EMERGENCY_RESPONSE => 'Emergency Response',
            self::TYPE_BLOOD_DELIVERY => 'Blood Delivery',
            self::TYPE_PHARMACEUTICAL => 'Pharmaceutical',
            self::TYPE_MULTI_PURPOSE => 'Multi Purpose'
        ];
    }
}
