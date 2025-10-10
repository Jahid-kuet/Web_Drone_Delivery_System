<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryTrackingController extends Controller
{
    /**
     * Track a delivery by tracking number
     */
    public function track($trackingNumber)
    {
        $delivery = Delivery::with([
            'request.hospital',
            'request.supply',
            'drone',
            'trackingRecords' => function ($query) {
                $query->latest()->limit(50);
            },
        ])->where('tracking_number', $trackingNumber)->first();
        
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'tracking_number' => $delivery->tracking_number,
                'status' => $delivery->status,
                'status_label' => $delivery->status_label,
                'progress' => $delivery->progress_percentage,
                'eta' => $delivery->eta,
                'hospital' => [
                    'name' => $delivery->request->hospital->name,
                    'address' => $delivery->request->hospital->full_address,
                    'coordinates' => [
                        'latitude' => $delivery->request->hospital->latitude,
                        'longitude' => $delivery->request->hospital->longitude,
                    ],
                ],
                'medical_supplies' => $delivery->request->medical_supplies,
                'drone' => [
                    'name' => $delivery->drone->name,
                    'model' => $delivery->drone->model,
                    'battery_level' => $delivery->drone->battery_level,
                ],
                'current_position' => [
                    'latitude' => $delivery->current_latitude,
                    'longitude' => $delivery->current_longitude,
                    'altitude' => $delivery->current_altitude,
                ],
                'timeline' => [
                    'created_at' => $delivery->created_at->toIso8601String(),
                    'pickup_time' => $delivery->pickup_time?->toIso8601String(),
                    'estimated_delivery_time' => $delivery->estimated_delivery_time->toIso8601String(),
                    'actual_delivery_time' => $delivery->actual_delivery_time?->toIso8601String(),
                ],
                'distance' => [
                    'total_km' => $delivery->distance_km,
                    'remaining_km' => $delivery->distance_remaining_km,
                ],
                'tracking_history' => $delivery->trackingRecords->map(function ($record) {
                    return [
                        'latitude' => $record->latitude,
                        'longitude' => $record->longitude,
                        'altitude' => $record->altitude,
                        'speed_kmh' => $record->speed_kmh,
                        'battery_level' => $record->battery_level,
                        'timestamp' => $record->created_at->toIso8601String(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get real-time position updates
     */
    public function realtimePosition($trackingNumber)
    {
        $delivery = Delivery::with(['drone', 'request.hospital'])
            ->where('tracking_number', $trackingNumber)
            ->first();
        
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found',
            ], 404);
        }
        
        $latestTracking = $delivery->trackingRecords()->latest()->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'position' => [
                    'latitude' => $delivery->current_latitude,
                    'longitude' => $delivery->current_longitude,
                    'altitude' => $delivery->current_altitude,
                ],
                'status' => $delivery->status,
                'progress' => $delivery->progress_percentage,
                'eta' => $delivery->eta,
                'speed_kmh' => $latestTracking->speed_kmh ?? 0,
                'heading' => $latestTracking->heading ?? 0,
                'battery_level' => $delivery->drone->battery_level,
                'distance_remaining_km' => $delivery->distance_remaining_km,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get active deliveries
     */
    public function activeDeliveries()
    {
        $deliveries = Delivery::with(['deliveryRequest.hospital', 'drone'])
            ->whereIn('status', ['pending', 'in_transit', 'picked_up'])
            ->get()
            ->map(function ($delivery) {
                return [
                    'tracking_number' => $delivery->tracking_number,
                    'status' => $delivery->status,
                    'hospital' => $delivery->deliveryRequest->hospital->name,
                    'drone' => $delivery->drone->name,
                    'progress' => $delivery->progress_percentage,
                    'eta' => $delivery->eta,
                    'position' => [
                        'latitude' => $delivery->current_latitude,
                        'longitude' => $delivery->current_longitude,
                    ],
                ];
            });
        
        return response()->json([
            'success' => true,
            'count' => $deliveries->count(),
            'data' => $deliveries,
        ]);
    }

    /**
     * Update delivery position (for drone/pilot apps)
     */
    public function updatePosition(Request $request, $trackingNumber)
    {
        $delivery = Delivery::where('tracking_number', $trackingNumber)->first();
        
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found',
            ], 404);
        }
        
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|numeric|min:0',
        ]);
        
        $delivery->updatePosition(
            $validated['latitude'],
            $validated['longitude'],
            $validated['altitude'] ?? null
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Position updated successfully',
            'data' => [
                'progress' => $delivery->progress_percentage,
                'eta' => $delivery->eta,
                'distance_remaining_km' => $delivery->distance_remaining_km,
            ],
        ]);
    }
}
