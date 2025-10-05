<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'delivery_request_id',
        'drone_id',
        'hospital_id',
        'assigned_pilot_id',
        'delivery_number',
        'status',
        'scheduled_departure_time',
        'actual_departure_time',
        'estimated_arrival_time',
        'actual_arrival_time',
        'delivery_completed_time',
        'pickup_coordinates',
        'delivery_coordinates',
        'current_coordinates',
        'current_altitude_m',
        'current_speed_kmh',
        'distance_remaining_km',
        'estimated_time_remaining_minutes',
        'total_distance_km',
        'route_waypoints',
        'weather_conditions',
        'fuel_battery_level_start',
        'fuel_battery_level_current',
        'fuel_battery_level_end',
        'cargo_manifest',
        'total_cargo_weight_kg',
        'special_handling_notes',
        'pilot_notes',
        'delivery_notes',
        'incidents',
        'delivery_confirmation_signature',
        'delivery_photos',
        'requires_return_trip',
        'return_cargo',
        'delivery_rating',
        'delivery_feedback',
        'delivery_cost',
        'metadata'
    ];

    protected $casts = [
        'pickup_coordinates' => 'array',
        'delivery_coordinates' => 'array',
        'current_coordinates' => 'array',
        'route_waypoints' => 'array',
        'weather_conditions' => 'array',
        'cargo_manifest' => 'array',
        'incidents' => 'array',
        'delivery_photos' => 'array',
        'return_cargo' => 'array',
        'metadata' => 'array',
        'scheduled_departure_time' => 'datetime',
        'actual_departure_time' => 'datetime',
        'estimated_arrival_time' => 'datetime',
        'actual_arrival_time' => 'datetime',
        'delivery_completed_time' => 'datetime',
        'current_altitude_m' => 'decimal:2',
        'current_speed_kmh' => 'decimal:2',
        'distance_remaining_km' => 'decimal:3',
        'total_distance_km' => 'decimal:3',
        'fuel_battery_level_start' => 'decimal:2',
        'fuel_battery_level_current' => 'decimal:2',
        'fuel_battery_level_end' => 'decimal:2',
        'total_cargo_weight_kg' => 'decimal:3',
        'delivery_cost' => 'decimal:2',
        'requires_return_trip' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_PREPARING = 'preparing';
    const STATUS_LOADED = 'loaded';
    const STATUS_DEPARTED = 'departed';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_APPROACHING_DESTINATION = 'approaching_destination';
    const STATUS_LANDED = 'landed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_RETURNING = 'returning';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EMERGENCY_LANDED = 'emergency_landed';

    /**
     * Get the delivery request
     */
    public function deliveryRequest(): BelongsTo
    {
        return $this->belongsTo(DeliveryRequest::class);
    }

    /**
     * Get the assigned drone
     */
    public function drone(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    /**
     * Get the destination hospital
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the assigned pilot
     */
    public function assignedPilot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_pilot_id');
    }

    /**
     * Get the drone assignment
     */
    public function droneAssignment(): HasOne
    {
        return $this->hasOne(DroneAssignment::class);
    }

    /**
     * Get tracking records
     */
    public function trackingRecords(): HasMany
    {
        return $this->hasMany(DeliveryTracking::class)->orderBy('recorded_at', 'desc');
    }

    /**
     * Get latest tracking record
     */
    public function latestTracking(): HasOne
    {
        return $this->hasOne(DeliveryTracking::class)->latestOfMany('recorded_at');
    }

    /**
     * Get delivery confirmation
     */
    public function confirmation(): HasOne
    {
        return $this->hasOne(DeliveryConfirmation::class);
    }

    /**
     * Scope for active deliveries
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope for in-progress deliveries
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [
            self::STATUS_DEPARTED,
            self::STATUS_IN_TRANSIT,
            self::STATUS_APPROACHING_DESTINATION
        ]);
    }

    /**
     * Scope for completed deliveries
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for emergency situations
     */
    public function scopeEmergency($query)
    {
        return $query->where('status', self::STATUS_EMERGENCY_LANDED)
                    ->orWhereHas('trackingRecords', function($q) {
                        $q->where('tracking_status', 'emergency');
                    });
    }

    /**
     * Check if delivery is active
     */
    public function isActive(): bool
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if delivery is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, [
            self::STATUS_DEPARTED,
            self::STATUS_IN_TRANSIT,
            self::STATUS_APPROACHING_DESTINATION
        ]);
    }

    /**
     * Check if delivery is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if delivery is overdue
     */
    public function isOverdue(): bool
    {
        return $this->estimated_arrival_time && 
               $this->estimated_arrival_time->isPast() &&
               !$this->isCompleted();
    }

    /**
     * Start the delivery
     */
    public function start(): bool
    {
        $this->status = self::STATUS_DEPARTED;
        $this->actual_departure_time = now();
        
        // Update drone status
        if ($this->drone) {
            $this->drone->takeOff();
        }
        
        return $this->save();
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(): bool
    {
        $this->status = self::STATUS_DELIVERED;
        $this->actual_arrival_time = now();
        $this->delivery_completed_time = now();
        
        return $this->save();
    }

    /**
     * Complete the delivery
     */
    public function complete(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        
        // Update drone status
        if ($this->drone) {
            $this->drone->land();
        }
        
        // Update delivery request status
        if ($this->deliveryRequest) {
            $this->deliveryRequest->status = DeliveryRequest::STATUS_DELIVERED;
            $this->deliveryRequest->save();
        }
        
        return $this->save();
    }

    /**
     * Update current position
     */
    public function updatePosition($latitude, $longitude, $altitude = null, $speed = null): bool
    {
        $this->current_coordinates = [
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
        
        // Calculate remaining distance
        if ($this->delivery_coordinates) {
            $this->distance_remaining_km = $this->calculateDistanceTo(
                $this->delivery_coordinates['latitude'],
                $this->delivery_coordinates['longitude']
            );
        }
        
        return $this->save();
    }

    /**
     * Calculate distance to coordinates
     */
    private function calculateDistanceTo($latitude, $longitude): float
    {
        if (!$this->current_coordinates) return 0;
        
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $currentLat = $this->current_coordinates['latitude'];
        $currentLon = $this->current_coordinates['longitude'];
        
        $latDelta = deg2rad($latitude - $currentLat);
        $lonDelta = deg2rad($longitude - $currentLon);
        
        $angle = sin($latDelta / 2) * sin($latDelta / 2) +
                cos(deg2rad($currentLat)) * cos(deg2rad($latitude)) *
                sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($angle), sqrt(1 - $angle));
        
        return $earthRadius * $c;
    }

    /**
     * Generate unique delivery number
     */
    public static function generateDeliveryNumber(): string
    {
        $prefix = 'DEL';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', now()->toDateString())->count() + 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if (!$this->total_distance_km || $this->total_distance_km == 0) return 0;
        
        $completed = $this->total_distance_km - ($this->distance_remaining_km ?? $this->total_distance_km);
        return ($completed / $this->total_distance_km) * 100;
    }

    /**
     * Get estimated time of arrival
     */
    public function getEtaAttribute(): ?Carbon
    {
        if ($this->estimated_time_remaining_minutes) {
            return now()->addMinutes($this->estimated_time_remaining_minutes);
        }
        
        return $this->estimated_arrival_time;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get delivery statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_PREPARING => 'Preparing',
            self::STATUS_LOADED => 'Loaded',
            self::STATUS_DEPARTED => 'Departed',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_APPROACHING_DESTINATION => 'Approaching Destination',
            self::STATUS_LANDED => 'Landed',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_RETURNING => 'Returning',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EMERGENCY_LANDED => 'Emergency Landed'
        ];
    }
}
