<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'primary_phone',
        'emergency_phone',
        'email',
        'website',
        'license_number',
        'license_expiry_date',
        'operating_hours',
        'emergency_hours',
        'specializations',
        'bed_capacity',
        'has_emergency_department',
        'has_blood_bank',
        'has_pharmacy',
        'has_laboratory',
        'has_helicopter_pad',
        'has_drone_landing_pad',
        'drone_landing_coordinates',
        'drone_landing_altitude_m',
        'delivery_preferences',
        'priority_level',
        'accepts_emergency_deliveries',
        'approved_supply_categories',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email',
        'backup_contact_name',
        'backup_contact_phone',
        'special_instructions',
        'is_active',
        'is_verified',
        'metadata'
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'emergency_hours' => 'array',
        'specializations' => 'array',
        'drone_landing_coordinates' => 'array',
        'delivery_preferences' => 'array',
        'approved_supply_categories' => 'array',
        'metadata' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'drone_landing_altitude_m' => 'decimal:2',
        'has_emergency_department' => 'boolean',
        'has_blood_bank' => 'boolean',
        'has_pharmacy' => 'boolean',
        'has_laboratory' => 'boolean',
        'has_helicopter_pad' => 'boolean',
        'has_drone_landing_pad' => 'boolean',
        'accepts_emergency_deliveries' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'license_expiry_date' => 'date'
    ];

    protected $dates = ['deleted_at'];

    // Type constants
    const TYPE_GENERAL_HOSPITAL = 'general_hospital';
    const TYPE_SPECIALIZED_HOSPITAL = 'specialized_hospital';
    const TYPE_CLINIC = 'clinic';
    const TYPE_EMERGENCY_CENTER = 'emergency_center';
    const TYPE_BLOOD_BANK = 'blood_bank';
    const TYPE_DIAGNOSTIC_CENTER = 'diagnostic_center';
    const TYPE_PHARMACY = 'pharmacy';
    const TYPE_RESEARCH_FACILITY = 'research_facility';

    /**
     * Get all users associated with this hospital
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all delivery requests from this hospital
     */
    public function deliveryRequests(): HasMany
    {
        return $this->hasMany(DeliveryRequest::class);
    }

    /**
     * Get all deliveries to this hospital
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get all delivery confirmations for this hospital
     */
    public function deliveryConfirmations(): HasMany
    {
        return $this->hasMany(DeliveryConfirmation::class);
    }

    /**
     * Scope for active hospitals
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified hospitals
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for hospitals by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for hospitals that accept emergency deliveries
     */
    public function scopeAcceptsEmergencyDeliveries($query)
    {
        return $query->where('accepts_emergency_deliveries', true);
    }

    /**
     * Scope for hospitals within radius
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm)
    {
        return $query->selectRaw(
            '*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
            [$latitude, $longitude, $latitude]
        )
        ->having('distance', '<', $radiusKm)
        ->orderBy('distance');
    }

    /**
     * Check if hospital has drone landing capability
     */
    public function hasDroneLandingCapability(): bool
    {
        return $this->has_drone_landing_pad && $this->drone_landing_coordinates;
    }

    /**
     * Check if hospital is open now
     */
    public function isOpenNow(): bool
    {
        if (!$this->operating_hours) return true; // Assume 24/7 if no hours set
        
        $now = now();
        $dayOfWeek = strtolower($now->format('l'));
        
        if (isset($this->operating_hours[$dayOfWeek])) {
            $hours = $this->operating_hours[$dayOfWeek];
            if ($hours['closed']) return false;
            
            $openTime = Carbon::createFromFormat('H:i', $hours['open']);
            $closeTime = Carbon::createFromFormat('H:i', $hours['close']);
            
            return $now->between($openTime, $closeTime);
        }
        
        return false;
    }

    /**
     * Check if hospital accepts emergency deliveries 24/7
     */
    public function acceptsEmergencyDeliveries(): bool
    {
        return $this->accepts_emergency_deliveries;
    }

    /**
     * Check if license is expired
     */
    public function isLicenseExpired(): bool
    {
        return $this->license_expiry_date && $this->license_expiry_date->isPast();
    }

    /**
     * Check if license is expiring soon
     */
    public function isLicenseExpiringSoon($days = 30): bool
    {
        return $this->license_expiry_date && 
               $this->license_expiry_date->isBetween(now(), now()->addDays($days));
    }

    /**
     * Calculate distance to another point
     */
    public function distanceTo($latitude, $longitude): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $latDelta = deg2rad($latitude - $this->latitude);
        $lonDelta = deg2rad($longitude - $this->longitude);
        
        $angle = sin($latDelta / 2) * sin($latDelta / 2) +
                cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
                sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($angle), sqrt(1 - $angle));
        
        return $earthRadius * $c;
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country
        ]));
    }

    /**
     * Get contact information
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'primary_phone' => $this->primary_phone,
            'emergency_phone' => $this->emergency_phone,
            'email' => $this->email,
            'contact_person' => $this->contact_person_name,
            'contact_phone' => $this->contact_person_phone,
            'contact_email' => $this->contact_person_email
        ];
    }

    /**
     * Get hospital types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_GENERAL_HOSPITAL => 'General Hospital',
            self::TYPE_SPECIALIZED_HOSPITAL => 'Specialized Hospital',
            self::TYPE_CLINIC => 'Clinic',
            self::TYPE_EMERGENCY_CENTER => 'Emergency Center',
            self::TYPE_BLOOD_BANK => 'Blood Bank',
            self::TYPE_DIAGNOSTIC_CENTER => 'Diagnostic Center',
            self::TYPE_PHARMACY => 'Pharmacy',
            self::TYPE_RESEARCH_FACILITY => 'Research Facility'
        ];
    }
}
