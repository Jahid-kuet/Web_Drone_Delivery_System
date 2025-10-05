<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DeliveryTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'drone_id',
        'latitude',
        'longitude',
        'altitude_m',
        'speed_kmh',
        'heading_degrees',
        'battery_level',
        'flight_mode',
        'tracking_status',
        'sensor_data',
        'weather_data',
        'signal_strength',
        'gps_lock',
        'satellites_visible',
        'system_alerts',
        'cargo_status',
        'estimated_arrival_time_minutes',
        'distance_to_destination_km',
        'notes',
        'recorded_at'
    ];

    protected $casts = [
        'sensor_data' => 'array',
        'weather_data' => 'array',
        'system_alerts' => 'array',
        'cargo_status' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude_m' => 'decimal:2',
        'speed_kmh' => 'decimal:2',
        'heading_degrees' => 'decimal:2',
        'battery_level' => 'decimal:2',
        'signal_strength' => 'decimal:2',
        'estimated_arrival_time_minutes' => 'decimal:2',
        'distance_to_destination_km' => 'decimal:3',
        'gps_lock' => 'boolean',
        'recorded_at' => 'datetime'
    ];

    // Flight mode constants
    const FLIGHT_MODE_MANUAL = 'manual';
    const FLIGHT_MODE_AUTOPILOT = 'autopilot';
    const FLIGHT_MODE_GPS_GUIDED = 'gps_guided';
    const FLIGHT_MODE_RETURN_TO_HOME = 'return_to_home';
    const FLIGHT_MODE_EMERGENCY = 'emergency';
    const FLIGHT_MODE_HOVERING = 'hovering';

    // Tracking status constants
    const STATUS_NORMAL = 'normal';
    const STATUS_WARNING = 'warning';
    const STATUS_CRITICAL = 'critical';
    const STATUS_EMERGENCY = 'emergency';
    const STATUS_OFFLINE = 'offline';

    /**
     * Get the delivery this tracking belongs to
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the drone being tracked
     */
    public function drone(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }

    /**
     * Scope for recent tracking records
     */
    public function scopeRecent($query, $minutes = 30)
    {
        return $query->where('recorded_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope for tracking by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('tracking_status', $status);
    }

    /**
     * Scope for emergency tracking
     */
    public function scopeEmergency($query)
    {
        return $query->where('tracking_status', self::STATUS_EMERGENCY)
                    ->orWhere('flight_mode', self::FLIGHT_MODE_EMERGENCY);
    }

    /**
     * Scope for low battery tracking
     */
    public function scopeLowBattery($query, $threshold = 20)
    {
        return $query->where('battery_level', '<', $threshold);
    }

    /**
     * Check if tracking indicates emergency
     */
    public function isEmergency(): bool
    {
        return $this->tracking_status === self::STATUS_EMERGENCY ||
               $this->flight_mode === self::FLIGHT_MODE_EMERGENCY ||
               $this->battery_level < 10;
    }

    /**
     * Check if tracking indicates warning
     */
    public function isWarning(): bool
    {
        return $this->tracking_status === self::STATUS_WARNING ||
               $this->battery_level < 20 ||
               !$this->gps_lock ||
               ($this->signal_strength && $this->signal_strength < 30);
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'altitude' => $this->altitude_m
        ];
    }

    /**
     * Get battery status color
     */
    public function getBatteryStatusColorAttribute(): string
    {
        if ($this->battery_level >= 60) return 'success';
        if ($this->battery_level >= 30) return 'warning';
        if ($this->battery_level >= 10) return 'danger';
        return 'critical';
    }

    /**
     * Get signal status
     */
    public function getSignalStatusAttribute(): string
    {
        if (!$this->signal_strength) return 'unknown';
        if ($this->signal_strength >= 70) return 'excellent';
        if ($this->signal_strength >= 50) return 'good';
        if ($this->signal_strength >= 30) return 'fair';
        return 'poor';
    }

    /**
     * Get formatted speed
     */
    public function getFormattedSpeedAttribute(): string
    {
        return number_format($this->speed_kmh, 1) . ' km/h';
    }

    /**
     * Get formatted altitude
     */
    public function getFormattedAltitudeAttribute(): string
    {
        return number_format($this->altitude_m, 1) . ' m';
    }

    /**
     * Get time since recorded
     */
    public function getTimeSinceRecordedAttribute(): string
    {
        return $this->recorded_at->diffForHumans();
    }

    /**
     * Get flight modes
     */
    public static function getFlightModes(): array
    {
        return [
            self::FLIGHT_MODE_MANUAL => 'Manual',
            self::FLIGHT_MODE_AUTOPILOT => 'Autopilot',
            self::FLIGHT_MODE_GPS_GUIDED => 'GPS Guided',
            self::FLIGHT_MODE_RETURN_TO_HOME => 'Return to Home',
            self::FLIGHT_MODE_EMERGENCY => 'Emergency',
            self::FLIGHT_MODE_HOVERING => 'Hovering'
        ];
    }

    /**
     * Get tracking statuses
     */
    public static function getTrackingStatuses(): array
    {
        return [
            self::STATUS_NORMAL => 'Normal',
            self::STATUS_WARNING => 'Warning',
            self::STATUS_CRITICAL => 'Critical',
            self::STATUS_EMERGENCY => 'Emergency',
            self::STATUS_OFFLINE => 'Offline'
        ];
    }
}
