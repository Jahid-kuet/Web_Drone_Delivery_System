<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasGPSCoordinates;

class Hub extends Model
{
    use SoftDeletes, HasGPSCoordinates;

    protected $fillable = [
        'name',
        'code',
        'hub_type',
        'address',
        'city',
        'division',
        'district',
        'postal_code',
        'latitude',
        'longitude',
        'contact_person',
        'phone',
        'email',
        'operating_hours',
        'storage_capacity_cubic_meters',
        'has_cold_storage',
        'cold_storage_temp_min',
        'cold_storage_temp_max',
        'cold_storage_capacity_liters',
        'drone_charging_stations',
        'drone_parking_bays',
        'has_maintenance_facility',
        'has_weather_station',
        'is_active',
        'is_24_7',
        'notes',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'has_cold_storage' => 'boolean',
        'has_maintenance_facility' => 'boolean',
        'has_weather_station' => 'boolean',
        'is_active' => 'boolean',
        'is_24_7' => 'boolean',
        'cold_storage_temp_min' => 'decimal:2',
        'cold_storage_temp_max' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relationships
     */
    public function drones()
    {
        return $this->hasMany(Drone::class, 'home_hub_id');
    }

    public function currentDrones()
    {
        return $this->hasMany(Drone::class, 'current_hub_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'pickup_hub_id');
    }

    public function inventories()
    {
        return $this->hasMany(HubInventory::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithColdStorage($query)
    {
        return $query->where('has_cold_storage', true);
    }

    public function scopeInDivision($query, $division)
    {
        return $query->where('division', $division);
    }

    public function scopeOperating24_7($query)
    {
        return $query->where('is_24_7', true);
    }

    /**
     * Accessors
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->division} {$this->postal_code}, Bangladesh";
    }

    public function getAvailableChargingStationsAttribute(): int
    {
        $inUse = $this->currentDrones()
            ->where('status', 'charging')
            ->count();

        return max(0, $this->drone_charging_stations - $inUse);
    }

    public function getAvailableParkingBaysAttribute(): int
    {
        $occupied = $this->currentDrones()->count();
        return max(0, $this->drone_parking_bays - $occupied);
    }

    /**
     * Methods
     */
    public function availableDrones()
    {
        return $this->currentDrones()
            ->where('status', 'available')
            ->where('current_battery_level', '>=', 30);
    }

    public function getInventoryForSupply($medicalSupplyId)
    {
        return $this->inventories()
            ->where('medical_supply_id', $medicalSupplyId)
            ->first();
    }

    public function hasStock($medicalSupplyId, $quantity = 1): bool
    {
        $inventory = $this->getInventoryForSupply($medicalSupplyId);
        return $inventory && $inventory->quantity_available >= $quantity;
    }

    public function canAcceptDrone(): bool
    {
        return $this->is_active && $this->available_parking_bays > 0;
    }

    public function canChargeDrone(): bool
    {
        return $this->is_active && $this->available_charging_stations > 0;
    }

    /**
     * Find nearest hub to given coordinates
     */
    public static function findNearestTo(float $latitude, float $longitude, array $filters = [])
    {
        $query = self::active();

        // Apply filters
        if (isset($filters['with_cold_storage']) && $filters['with_cold_storage']) {
            $query->where('has_cold_storage', true);
        }

        if (isset($filters['division'])) {
            $query->where('division', $filters['division']);
        }

        if (isset($filters['has_maintenance'])) {
            $query->where('has_maintenance_facility', true);
        }

        return $query->get()
            ->sortBy(function ($hub) use ($latitude, $longitude) {
                return $hub->distanceTo($latitude, $longitude);
            })
            ->first();
    }

    /**
     * Find nearest hub for delivery
     */
    public static function findNearestHubForDelivery($hospitalLat, $hospitalLng, $requiresColdChain = false)
    {
        $query = self::active()
            ->whereHas('currentDrones', function ($q) {
                $q->where('status', 'available')
                  ->where('current_battery_level', '>=', 30);
            });

        if ($requiresColdChain) {
            $query->where('has_cold_storage', true);
        }

        return $query->get()
            ->sortBy(function ($hub) use ($hospitalLat, $hospitalLng) {
                return $hub->distanceTo($hospitalLat, $hospitalLng);
            })
            ->first();
    }

    /**
     * Get hub statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_drones' => $this->drones()->count(),
            'available_drones' => $this->availableDrones()->count(),
            'active_deliveries' => $this->deliveries()
                ->whereIn('status', ['preparing', 'in_transit', 'picked_up'])
                ->count(),
            'completed_deliveries_today' => $this->deliveries()
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->count(),
            'inventory_items' => $this->inventories()->count(),
            'low_stock_items' => $this->inventories()
                ->whereColumn('quantity_available', '<=', 'minimum_stock_level')
                ->count(),
            'available_parking' => $this->available_parking_bays,
            'available_charging' => $this->available_charging_stations,
        ];
    }
}
