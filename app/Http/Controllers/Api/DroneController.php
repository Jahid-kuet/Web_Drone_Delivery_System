<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use Illuminate\Http\Request;

class DroneController extends Controller
{
    /**
     * Get all available drones
     */
    public function available(Request $request)
    {
        $payload = $request->input('payload', 0);
        $distance = $request->input('distance', 0);
        
        $drones = Drone::available()
            ->where('current_battery_level', '>=', 30)
            ->get()
            ->filter(function ($drone) use ($payload, $distance) {
                return $drone->canCarryPayload($payload) 
                    && $drone->canReachDistance($distance);
            })
            ->map(function ($drone) {
                return [
                    'id' => $drone->id,
                    'name' => $drone->name,
                    'model' => $drone->model,
                    'serial_number' => $drone->serial_number,
                    'battery_level' => $drone->battery_level,
                    'battery_status' => $drone->battery_status,
                    'max_payload_kg' => $drone->max_payload_kg,
                    'max_range_km' => $drone->max_range_km,
                    'max_speed_kmh' => $drone->max_speed_kmh,
                    'position' => [
                        'latitude' => $drone->current_latitude,
                        'longitude' => $drone->current_longitude,
                        'altitude' => $drone->current_altitude,
                    ],
                ];
            })
            ->values();
        
        return response()->json([
            'success' => true,
            'count' => $drones->count(),
            'data' => $drones,
        ]);
    }

    /**
     * Get drone status
     */
    public function status($droneId)
    {
        $drone = Drone::with(['assignments' => function ($query) {
            $query->where('status', 'in_progress')->latest();
        }])->find($droneId);
        
        if (!$drone) {
            return response()->json([
                'success' => false,
                'message' => 'Drone not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $drone->id,
                'name' => $drone->name,
                'status' => $drone->status,
                'battery_level' => $drone->battery_level,
                'battery_status' => $drone->battery_status,
                'position' => [
                    'latitude' => $drone->current_latitude,
                    'longitude' => $drone->current_longitude,
                    'altitude' => $drone->current_altitude,
                ],
                'statistics' => [
                    'total_deliveries' => $drone->total_deliveries,
                    'total_flight_hours' => $drone->total_flight_hours,
                    'efficiency_rating' => $drone->efficiency_rating,
                ],
                'maintenance' => [
                    'last_maintenance' => $drone->last_maintenance_date?->toDateString(),
                    'next_maintenance' => $drone->next_maintenance_date?->toDateString(),
                    'needs_maintenance' => $drone->needsMaintenance(),
                ],
                'current_assignment' => $drone->assignments->first() ? [
                    'delivery_id' => $drone->assignments->first()->delivery_id,
                    'status' => $drone->assignments->first()->status,
                    'started_at' => $drone->assignments->first()->started_at?->toIso8601String(),
                ] : null,
            ],
        ]);
    }

    /**
     * Update drone battery level
     */
    public function updateBattery(Request $request, $droneId)
    {
        $drone = Drone::find($droneId);
        
        if (!$drone) {
            return response()->json([
                'success' => false,
                'message' => 'Drone not found',
            ], 404);
        }
        
        $validated = $request->validate([
            'battery_level' => 'required|integer|min:0|max:100',
        ]);
        
        $drone->updateBatteryLevel($validated['battery_level']);
        
        return response()->json([
            'success' => true,
            'message' => 'Battery level updated',
            'data' => [
                'battery_level' => $drone->battery_level,
                'battery_status' => $drone->battery_status,
            ],
        ]);
    }

    /**
     * Update drone position
     */
    public function updatePosition(Request $request, $droneId)
    {
        $drone = Drone::find($droneId);
        
        if (!$drone) {
            return response()->json([
                'success' => false,
                'message' => 'Drone not found',
            ], 404);
        }
        
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|numeric|min:0',
        ]);
        
        $drone->updatePosition(
            $validated['latitude'],
            $validated['longitude'],
            $validated['altitude'] ?? null
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Position updated',
            'data' => [
                'latitude' => $drone->current_latitude,
                'longitude' => $drone->current_longitude,
                'altitude' => $drone->current_altitude,
            ],
        ]);
    }
}
