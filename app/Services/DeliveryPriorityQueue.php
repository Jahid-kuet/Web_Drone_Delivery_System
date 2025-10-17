<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Drone;
use App\Models\Hub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryPriorityQueue
{
    /**
     * Priority scores for different delivery types
     */
    const PRIORITY_SCORES = [
        'emergency' => 100,
        'urgent' => 50,
        'normal' => 10,
    ];

    /**
     * Medical supply type priority multipliers
     */
    const SUPPLY_PRIORITY = [
        'blood' => 2.0,
        'plasma' => 2.0,
        'emergency_medicine' => 1.8,
        'vaccine' => 1.5,
        'surgical_supplies' => 1.3,
        'regular_medicine' => 1.0,
    ];

    /**
     * Calculate delivery priority score
     */
    public static function calculatePriority(Delivery $delivery): int
    {
        // Get priority from delivery_request
        $priorityLevel = $delivery->deliveryRequest->priority ?? 'medium';
        
        // Map priority levels to our scoring system
        $priorityMap = [
            'emergency' => 100,
            'critical' => 100,
            'high' => 50,
            'medium' => 10,
            'low' => 5,
        ];
        
        $baseScore = $priorityMap[$priorityLevel] ?? 10;
        
        // Apply supply type multiplier
        $supplyMultiplier = 1.0;
        if ($delivery->medicalSupply) {
            $supplyType = strtolower($delivery->medicalSupply->category ?? 'regular_medicine');
            $supplyMultiplier = self::SUPPLY_PRIORITY[$supplyType] ?? 1.0;
        }
        
        // Time factor: older requests get slight boost (max 20 points)
        $ageInHours = $delivery->created_at->diffInHours(now());
        $timeFactor = min($ageInHours * 2, 20);
        
        // Hospital urgency factor
        $hospitalFactor = 0;
        if ($delivery->hospital && $delivery->hospital->priority_level === 'high') {
            $hospitalFactor = 10;
        }
        
        // Calculate final score
        $finalScore = ($baseScore * $supplyMultiplier) + $timeFactor + $hospitalFactor;
        
        return (int) round($finalScore);
    }

    /**
     * Get pending deliveries sorted by priority
     */
    public static function getPendingDeliveries()
    {
        return Delivery::with(['hospital', 'medicalSupply', 'deliveryRequest'])
            ->where('status', 'pending')
            ->whereNull('drone_id')
            ->get()
            ->map(function ($delivery) {
                $delivery->priority_score = self::calculatePriority($delivery);
                $delivery->priority_level = $delivery->deliveryRequest->priority ?? 'medium';
                return $delivery;
            })
            ->sortByDesc('priority_score');
    }

    /**
     * Find best available drone for a delivery
     */
    public static function findBestDrone(Delivery $delivery): ?Drone
    {
        // Get the hub closest to the hospital
        $hub = Hub::where('is_active', true)
            ->where('status', 'operational')
            ->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance
            ", [
                $delivery->pickup_latitude ?? $delivery->hospital->latitude,
                $delivery->pickup_longitude ?? $delivery->hospital->longitude,
                $delivery->pickup_latitude ?? $delivery->hospital->latitude,
            ])
            ->orderBy('distance')
            ->first();

        if (!$hub) {
            Log::warning("No operational hub found for delivery #{$delivery->id}");
            return null;
        }

        // Find available drones at this hub
        $availableDrones = Drone::where('hub_id', $hub->id)
            ->where('status', 'available')
            ->where('is_active', true)
            ->whereRaw('battery_level >= ?', [30]) // Minimum 30% battery
            ->whereRaw('max_payload_kg >= ?', [$delivery->weight_kg ?? 5])
            ->with('maintenanceRecords')
            ->get()
            ->filter(function ($drone) {
                // Filter out drones with recent critical issues
                $recentIssues = $drone->maintenanceRecords()
                    ->where('severity', 'critical')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->where('status', '!=', 'completed')
                    ->count();
                return $recentIssues === 0;
            });

        if ($availableDrones->isEmpty()) {
            Log::warning("No available drones at hub #{$hub->id} for delivery #{$delivery->id}");
            return null;
        }

        // Select drone with highest battery and lowest flight hours (less wear)
        return $availableDrones->sortByDesc(function ($drone) {
            return ($drone->battery_level * 10) - ($drone->total_flight_hours ?? 0);
        })->first();
    }

    /**
     * Auto-assign deliveries to available drones
     */
    public static function autoAssignDeliveries(): array
    {
        $results = [
            'assigned' => 0,
            'failed' => 0,
            'skipped' => 0,
            'details' => [],
        ];

        // Get pending deliveries sorted by priority
        $pendingDeliveries = self::getPendingDeliveries();

        Log::info("Auto-assign: Processing {$pendingDeliveries->count()} pending deliveries");

        foreach ($pendingDeliveries as $delivery) {
            try {
                // Skip if already assigned
                if ($delivery->drone_id) {
                    $results['skipped']++;
                    continue;
                }

                // Find best drone
                $drone = self::findBestDrone($delivery);

                if (!$drone) {
                    $results['failed']++;
                    $results['details'][] = [
                        'delivery_id' => $delivery->id,
                        'status' => 'failed',
                        'reason' => 'No suitable drone available',
                        'priority_score' => $delivery->priority_score,
                    ];
                    continue;
                }

                // Assign drone to delivery
                DB::transaction(function () use ($delivery, $drone) {
                    $delivery->update([
                        'drone_id' => $drone->id,
                        'status' => 'assigned',
                        'assigned_at' => now(),
                    ]);

                    $drone->update([
                        'status' => 'assigned',
                        'current_delivery_id' => $delivery->id,
                    ]);

                    // Log assignment
                    Log::info("Assigned drone #{$drone->id} to delivery #{$delivery->id} (Priority: {$delivery->priority_score})");
                });

                $results['assigned']++;
                $results['details'][] = [
                    'delivery_id' => $delivery->id,
                    'drone_id' => $drone->id,
                    'status' => 'assigned',
                    'priority_score' => $delivery->priority_score,
                    'priority_level' => $delivery->deliveryRequest->priority ?? 'medium',
                ];

                // For emergency deliveries, trigger immediate notification
                $priority = $delivery->deliveryRequest->priority ?? 'medium';
                if (in_array($priority, ['emergency', 'critical'])) {
                    // TODO: Trigger SMS/Push notification to operator
                    Log::info("EMERGENCY: Delivery #{$delivery->id} (Priority: $priority) assigned to drone #{$drone->id}");
                }

            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'delivery_id' => $delivery->id,
                    'status' => 'error',
                    'reason' => $e->getMessage(),
                ];
                Log::error("Error assigning delivery #{$delivery->id}: {$e->getMessage()}");
            }
        }

        Log::info("Auto-assign completed: {$results['assigned']} assigned, {$results['failed']} failed, {$results['skipped']} skipped");

        return $results;
    }

    /**
     * Get priority queue status
     */
    public static function getQueueStatus(): array
    {
        $pending = Delivery::with('deliveryRequest')
            ->where('status', 'pending')
            ->whereNull('drone_id')
            ->get();

        $byPriority = [
            'emergency' => 0,
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
        ];

        $oldestWaitTime = null;

        foreach ($pending as $delivery) {
            $priority = $delivery->deliveryRequest->priority ?? 'medium';
            $byPriority[$priority] = ($byPriority[$priority] ?? 0) + 1;
            
            $waitTime = $delivery->created_at->diffInMinutes(now());
            if ($oldestWaitTime === null || $waitTime > $oldestWaitTime) {
                $oldestWaitTime = $waitTime;
            }
        }

        return [
            'total_pending' => $pending->count(),
            'by_priority' => $byPriority,
            'oldest_wait_minutes' => $oldestWaitTime ?? 0,
            'available_drones' => Drone::where('status', 'available')->count(),
        ];
    }

    /**
     * Check if any emergency deliveries are waiting too long
     */
    public static function checkEmergencyAlerts(): array
    {
        $alerts = [];

        // Emergency deliveries waiting more than 15 minutes
        $emergencies = Delivery::with(['hospital', 'medicalSupply', 'deliveryRequest'])
            ->where('status', 'pending')
            ->whereNull('drone_id')
            ->whereHas('deliveryRequest', function($q) {
                $q->whereIn('priority', ['emergency', 'critical']);
            })
            ->where('created_at', '<=', now()->subMinutes(15))
            ->get();

        foreach ($emergencies as $delivery) {
            $waitTime = $delivery->created_at->diffInMinutes(now());
            $priority = $delivery->deliveryRequest->priority ?? 'unknown';
            $alerts[] = [
                'type' => 'critical',
                'delivery_id' => $delivery->id,
                'hospital' => $delivery->hospital->name ?? 'Unknown',
                'supply' => $delivery->medicalSupply->name ?? 'Unknown',
                'wait_time_minutes' => $waitTime,
                'priority' => $priority,
                'message' => "CRITICAL: " . strtoupper($priority) . " delivery #{$delivery->id} waiting {$waitTime} minutes without assignment",
            ];
        }

        return $alerts;
    }
}
