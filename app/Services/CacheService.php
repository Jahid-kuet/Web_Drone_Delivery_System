<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;
use App\Models\Drone;
use App\Models\Hospital;
use App\Models\MedicalSupply;

/**
 * Caching service for frequently accessed data
 * Implements cache-aside pattern with configurable TTL
 */
class CacheService
{
    /**
     * Cache TTL configurations (in seconds)
     */
    const TTL_SHORT = 300;      // 5 minutes - frequently changing data
    const TTL_MEDIUM = 1800;    // 30 minutes - moderate changes
    const TTL_LONG = 3600;      // 1 hour - stable data
    const TTL_VERY_LONG = 86400; // 24 hours - rarely changing data

    /**
     * Cache key prefixes
     */
    const PREFIX_STATS = 'stats:';
    const PREFIX_DELIVERY = 'delivery:';
    const PREFIX_DRONE = 'drone:';
    const PREFIX_HOSPITAL = 'hospital:';
    const PREFIX_SUPPLY = 'supply:';
    const PREFIX_USER = 'user:';

    /**
     * Get dashboard statistics with caching
     */
    public function getDashboardStats(): array
    {
        return Cache::remember(self::PREFIX_STATS . 'dashboard', self::TTL_SHORT, function () {
            return [
                'total_deliveries' => Delivery::count(),
                'active_deliveries' => Delivery::whereIn('status', ['pending', 'assigned', 'in_transit', 'approaching_destination', 'landed'])->count(),
                'completed_deliveries' => Delivery::where('status', 'delivered')->count(),
                'active_drones' => Drone::where('status', 'active')->count(),
                'total_hospitals' => Hospital::where('is_active', true)->count(),
                'low_stock_supplies' => $this->getLowStockCount(),
                'pending_requests' => DB::table('delivery_requests')->where('status', 'pending')->count(),
            ];
        });
    }

    /**
     * Get delivery by tracking number with caching
     */
    public function getDeliveryByTracking(string $trackingNumber): ?Delivery
    {
        $cacheKey = self::PREFIX_DELIVERY . 'tracking:' . $trackingNumber;
        
        return Cache::remember($cacheKey, self::TTL_SHORT, function () use ($trackingNumber) {
            return Delivery::with(['drone', 'hospital', 'deliveryRequest', 'trackingRecords'])
                ->where('tracking_number', $trackingNumber)
                ->first();
        });
    }

    /**
     * Get available drones with caching
     */
    public function getAvailableDrones(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::PREFIX_DRONE . 'available', self::TTL_SHORT, function () {
            return Drone::where('status', 'active')
                ->where('battery_level', '>=', 30)
                ->whereNull('current_delivery_id')
                ->with('currentHub')
                ->get();
        });
    }

    /**
     * Get drone status with caching
     */
    public function getDroneStatus(int $droneId): ?Drone
    {
        $cacheKey = self::PREFIX_DRONE . 'status:' . $droneId;
        
        return Cache::remember($cacheKey, self::TTL_SHORT, function () use ($droneId) {
            return Drone::with(['currentHub', 'assignedOperator'])->find($droneId);
        });
    }

    /**
     * Get hospital details with caching
     */
    public function getHospital(int $hospitalId): ?Hospital
    {
        $cacheKey = self::PREFIX_HOSPITAL . $hospitalId;
        
        return Cache::remember($cacheKey, self::TTL_MEDIUM, function () use ($hospitalId) {
            return Hospital::with(['hub'])->find($hospitalId);
        });
    }

    /**
     * Get all active hospitals with caching
     */
    public function getActiveHospitals(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::PREFIX_HOSPITAL . 'active', self::TTL_MEDIUM, function () {
            return Hospital::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get medical supplies with low stock
     */
    public function getLowStockSupplies(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::PREFIX_SUPPLY . 'low_stock', self::TTL_MEDIUM, function () {
            return MedicalSupply::whereRaw('stock_quantity <= reorder_level')
                ->orderBy('stock_quantity', 'asc')
                ->get();
        });
    }

    /**
     * Get low stock count (helper)
     */
    protected function getLowStockCount(): int
    {
        return Cache::remember(self::PREFIX_SUPPLY . 'low_stock_count', self::TTL_MEDIUM, function () {
            return MedicalSupply::whereRaw('stock_quantity <= reorder_level')->count();
        });
    }

    /**
     * Get recent deliveries for a hospital
     */
    public function getHospitalRecentDeliveries(int $hospitalId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = self::PREFIX_HOSPITAL . $hospitalId . ':recent_deliveries:' . $limit;
        
        return Cache::remember($cacheKey, self::TTL_SHORT, function () use ($hospitalId, $limit) {
            return Delivery::where('hospital_id', $hospitalId)
                ->with(['drone', 'deliveryRequest'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get delivery statistics for date range
     */
    public function getDeliveryStats(string $startDate, string $endDate): array
    {
        $cacheKey = self::PREFIX_STATS . 'delivery:' . $startDate . ':' . $endDate;
        
        return Cache::remember($cacheKey, self::TTL_LONG, function () use ($startDate, $endDate) {
            return [
                'total' => Delivery::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed' => Delivery::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'delivered')->count(),
                'cancelled' => Delivery::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'cancelled')->count(),
                'in_progress' => Delivery::whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('status', ['pending', 'assigned', 'in_transit'])->count(),
                'by_priority' => Delivery::whereBetween('created_at', [$startDate, $endDate])
                    ->select('priority', DB::raw('count(*) as count'))
                    ->groupBy('priority')
                    ->pluck('count', 'priority')
                    ->toArray(),
            ];
        });
    }

    /**
     * Invalidate delivery cache when delivery is updated
     */
    public function invalidateDeliveryCache(Delivery $delivery): void
    {
        // Invalidate specific delivery caches
        Cache::forget(self::PREFIX_DELIVERY . 'tracking:' . $delivery->tracking_number);
        Cache::forget(self::PREFIX_DELIVERY . $delivery->id);
        
        // Invalidate related caches
        if ($delivery->hospital_id) {
            Cache::forget(self::PREFIX_HOSPITAL . $delivery->hospital_id . ':recent_deliveries:10');
        }
        
        // Invalidate dashboard stats
        Cache::forget(self::PREFIX_STATS . 'dashboard');
    }

    /**
     * Invalidate drone cache when drone is updated
     */
    public function invalidateDroneCache(Drone $drone): void
    {
        Cache::forget(self::PREFIX_DRONE . 'status:' . $drone->id);
        Cache::forget(self::PREFIX_DRONE . 'available');
        Cache::forget(self::PREFIX_STATS . 'dashboard');
    }

    /**
     * Invalidate hospital cache
     */
    public function invalidateHospitalCache(Hospital $hospital): void
    {
        Cache::forget(self::PREFIX_HOSPITAL . $hospital->id);
        Cache::forget(self::PREFIX_HOSPITAL . 'active');
        Cache::forget(self::PREFIX_STATS . 'dashboard');
    }

    /**
     * Invalidate supply cache
     */
    public function invalidateSupplyCache(MedicalSupply $supply): void
    {
        Cache::forget(self::PREFIX_SUPPLY . $supply->id);
        Cache::forget(self::PREFIX_SUPPLY . 'low_stock');
        Cache::forget(self::PREFIX_SUPPLY . 'low_stock_count');
        Cache::forget(self::PREFIX_STATS . 'dashboard');
    }

    /**
     * Clear all application caches
     */
    public function clearAll(): void
    {
        Cache::flush();
    }

    /**
     * Clear stats caches only
     */
    public function clearStats(): void
    {
        $keys = [
            self::PREFIX_STATS . 'dashboard',
            self::PREFIX_DRONE . 'available',
            self::PREFIX_HOSPITAL . 'active',
            self::PREFIX_SUPPLY . 'low_stock',
            self::PREFIX_SUPPLY . 'low_stock_count',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public function warmUp(): array
    {
        $warmed = [];

        try {
            // Warm up dashboard stats
            $this->getDashboardStats();
            $warmed[] = 'dashboard_stats';

            // Warm up available drones
            $this->getAvailableDrones();
            $warmed[] = 'available_drones';

            // Warm up active hospitals
            $this->getActiveHospitals();
            $warmed[] = 'active_hospitals';

            // Warm up low stock supplies
            $this->getLowStockSupplies();
            $warmed[] = 'low_stock_supplies';

            return [
                'success' => true,
                'warmed' => $warmed,
                'count' => count($warmed),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'warmed' => $warmed,
            ];
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'ttl_short' => self::TTL_SHORT . 's (' . (self::TTL_SHORT / 60) . 'min)',
            'ttl_medium' => self::TTL_MEDIUM . 's (' . (self::TTL_MEDIUM / 60) . 'min)',
            'ttl_long' => self::TTL_LONG . 's (' . (self::TTL_LONG / 60) . 'min)',
            'ttl_very_long' => self::TTL_VERY_LONG . 's (' . (self::TTL_VERY_LONG / 3600) . 'h)',
        ];
    }
}
