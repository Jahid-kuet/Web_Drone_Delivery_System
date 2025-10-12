<?php

namespace App\Http\Controllers;

use App\Models\Drone;
use App\Models\DroneAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DroneController extends Controller
{
    /**
     * Display a listing of drones
     */
    public function index(Request $request)
    {
        $query = Drone::query();
        
        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Availability filter
        if ($request->has('available_only')) {
            $query->available();
        }
        
        // Battery level filter
        if ($batteryLevel = $request->input('battery_level')) {
            switch ($batteryLevel) {
                case 'low':
                    $query->where('current_battery_level', '<', 30);
                    break;
                case 'medium':
                    $query->whereBetween('current_battery_level', [30, 70]);
                    break;
                case 'high':
                    $query->where('current_battery_level', '>', 70);
                    break;
            }
        }
        
        // Maintenance filter
        if ($request->has('needs_maintenance')) {
            $query->needsMaintenance();
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $drones = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => Drone::count(),
            'available' => Drone::where('status', 'available')->count(),
            'in_flight' => Drone::where('status', 'in_flight')->count(),
            'charging' => Drone::where('status', 'charging')->count(),
            'maintenance' => Drone::where('status', 'maintenance')->count(),
            'low_battery' => Drone::where('current_battery_level', '<', 30)->count(),
            'needs_maintenance' => Drone::needsMaintenance()->count(),
        ];
        
        return view('admin.drones.index', compact('drones', 'stats'));
    }

    /**
     * Show the form for creating a new drone
     */
    public function create()
    {
        return view('admin.drones.create');
    }

    /**
     * Store a newly created drone
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:drones,serial_number|max:100',
            'registration_number' => 'required|string|unique:drones,registration_number|max:100',
            'model' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'max_payload_kg' => 'required|numeric|min:0',
            'max_range_km' => 'required|numeric|min:0',
            'max_speed_kmh' => 'required|numeric|min:0',
            'battery_life_minutes' => 'required|integer|min:0',
            'current_battery_level' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:available,assigned,in_flight,maintenance,charging,offline,emergency',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after:last_maintenance_date',
            'total_flight_hours' => 'nullable|numeric|min:0',
            'total_deliveries' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            // Equipment & Specifications
            'has_camera' => 'nullable|boolean',
            'has_temperature_control' => 'nullable|boolean',
            'has_emergency_parachute' => 'nullable|boolean',
            'firmware_version' => 'nullable|string|max:50',
            'sensors_input' => 'nullable|string',
            'temperature_min_celsius' => 'nullable|numeric',
            'temperature_max_celsius' => 'nullable|numeric|gte:temperature_min_celsius',
        ]);
        
        // Convert sensors from comma-separated string to JSON array
        if (!empty($validated['sensors_input'])) {
            $sensors = array_map('trim', explode(',', $validated['sensors_input']));
            $validated['sensors'] = json_encode($sensors);
            unset($validated['sensors_input']);
        }
        
        // Convert checkboxes to boolean
        $validated['has_camera'] = $request->has('has_camera');
        $validated['has_temperature_control'] = $request->has('has_temperature_control');
        $validated['has_emergency_parachute'] = $request->has('has_emergency_parachute');
        
        // Set default values
        $validated['current_latitude'] = $validated['current_latitude'] ?? null;
        $validated['current_longitude'] = $validated['current_longitude'] ?? null;
        $validated['current_altitude'] = $validated['current_altitude'] ?? null;
        
        $drone = Drone::create($validated);
        
        return redirect()->route('admin.drones.show', $drone)
            ->with('success', 'Drone created successfully!');
    }

    /**
     * Display the specified drone
     */
    public function show(Drone $drone)
    {
        $drone->load([
            'assignments' => function ($query) {
                $query->latest()->limit(10);
            },
            'deliveries' => function ($query) {
                $query->latest()->limit(10);
            },
            // TODO: Uncomment when delivery_trackings table exists
            // 'trackingRecords' => function ($query) {
            //     $query->latest()->limit(20);
            // },
        ]);
        
        // Maintenance history not available without audit logs
        $maintenanceHistory = collect();
        
        // Get performance metrics
        $metrics = [
            'total_distance' => $drone->deliveries()->sum('total_distance_km'),
            'avg_efficiency' => 0, // efficiency_rating column doesn't exist in drone_assignments
            'success_rate' => $this->calculateSuccessRate($drone),
            'avg_battery_usage' => $drone->assignments()
                ->where('assignment_status', 'completed')
                ->whereNotNull('actual_battery_usage')
                ->avg('actual_battery_usage'),
        ];
        
        return view('admin.drones.show', compact('drone', 'maintenanceHistory', 'metrics'));
    }

    /**
     * Show the form for editing the specified drone
     */
    public function edit(Drone $drone)
    {
        return view('admin.drones.edit', compact('drone'));
    }

    /**
     * Update the specified drone
     */
    public function update(Request $request, Drone $drone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:100|unique:drones,serial_number,' . $drone->id,
            'registration_number' => 'required|string|max:100|unique:drones,registration_number,' . $drone->id,
            'model' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'max_payload_kg' => 'required|numeric|min:0',
            'max_range_km' => 'required|numeric|min:0',
            'max_speed_kmh' => 'required|numeric|min:0',
            'battery_life_minutes' => 'required|integer|min:0',
            'current_battery_level' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:available,assigned,in_flight,maintenance,charging,offline,emergency',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after:last_maintenance_date',
            'total_flight_hours' => 'nullable|numeric|min:0',
            'total_deliveries' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $drone->update($validated);
        
        return redirect()->route('admin.drones.show', $drone)
            ->with('success', 'Drone updated successfully!');
    }

    /**
     * Remove the specified drone
     */
    public function destroy(Drone $drone)
    {
        // Check if drone has active assignments
        $activeAssignments = $drone->assignments()
            ->whereIn('assignment_status', ['pending', 'accepted', 'in_progress'])
            ->count();
        
        if ($activeAssignments > 0) {
            return redirect()->route('admin.drones.index')
                ->with('error', 'Cannot delete drone with active assignments!');
        }
        
        $drone->delete();
        
        return redirect()->route('admin.drones.index')
            ->with('success', 'Drone deleted successfully!');
    }

    /**
     * Update drone status
     */
    public function updateStatus(Request $request, Drone $drone)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:available,assigned,in_flight,maintenance,charging,offline,emergency',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $drone->update(['status' => $validated['status']]);
        
        return redirect()->back()
            ->with('success', 'Drone status updated successfully!');
    }

    /**
     * Update drone battery level
     */
    public function updateBattery(Request $request, Drone $drone)
    {
        $validated = $request->validate([
            'battery_level' => 'required|integer|min:0|max:100',
        ]);
        
        $drone->updateBatteryLevel($validated['battery_level']);
        
        return response()->json([
            'success' => true,
            'battery_level' => $drone->battery_level,
            'battery_status' => $drone->battery_status,
        ]);
    }

    /**
     * Update drone position
     */
    public function updatePosition(Request $request, Drone $drone)
    {
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
            'position' => [
                'latitude' => $drone->current_latitude,
                'longitude' => $drone->current_longitude,
                'altitude' => $drone->current_altitude,
            ],
        ]);
    }

    /**
     * Record maintenance
     */
    public function recordMaintenance(Request $request, Drone $drone)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'performed_by' => 'required|string|max:255',
            'next_maintenance_date' => 'required|date|after:today',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update drone
            $drone->update([
                'last_maintenance_date' => now(),
                'next_maintenance_date' => $validated['next_maintenance_date'],
                'status' => 'available', // Make available after maintenance
            ]);
            
            // Log maintenance
            DB::table('drone_maintenance_logs')->insert([
                'drone_id' => $drone->id,
                'user_id' => auth()->id(),
                'maintenance_type' => $validated['maintenance_type'],
                'description' => $validated['description'],
                'cost' => $validated['cost'] ?? 0,
                'performed_by' => $validated['performed_by'],
                'created_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.drones.show', $drone)
                ->with('success', 'Maintenance recorded successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to record maintenance: ' . $e->getMessage());
        }
    }

    /**
     * Get available drones for assignment (AJAX)
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
                    'battery_level' => $drone->battery_level,
                    'battery_status' => $drone->battery_status,
                    'max_payload_kg' => $drone->max_payload_kg,
                    'max_range_km' => $drone->max_range_km,
                    'efficiency_rating' => $drone->efficiency_rating,
                ];
            })
            ->values();
        
        return response()->json([
            'count' => $drones->count(),
            'drones' => $drones,
        ]);
    }

    /**
     * Get drone real-time tracking data (AJAX)
     */
    public function tracking(Drone $drone)
    {
        $latestTracking = $drone->trackingRecords()->latest()->first();
        
        return response()->json([
            'drone' => [
                'id' => $drone->id,
                'name' => $drone->name,
                'status' => $drone->status,
                'battery_level' => $drone->battery_level,
                'position' => [
                    'latitude' => $drone->current_latitude,
                    'longitude' => $drone->current_longitude,
                    'altitude' => $drone->current_altitude,
                ],
            ],
            'tracking' => $latestTracking ? [
                'speed' => $latestTracking->speed_kmh,
                'heading' => $latestTracking->heading,
                'flight_mode' => $latestTracking->flight_mode,
                'signal_strength' => $latestTracking->signal_strength,
                'timestamp' => $latestTracking->created_at,
            ] : null,
        ]);
    }

    /**
     * Calculate drone success rate
     */
    private function calculateSuccessRate(Drone $drone): float
    {
        $total = $drone->assignments()
            ->whereIn('assignment_status', ['completed', 'cancelled', 'failed'])
            ->count();
        
        if ($total === 0) return 100.0;
        
        $completed = $drone->assignments()
            ->where('assignment_status', 'completed')
            ->count();
        
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Export drones list
     */
    public function export(Request $request)
    {
        $drones = Drone::all();
        
        $filename = 'drones-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($drones) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Name', 'Serial Number', 'Model', 'Status', 'Battery Level', 
                'Max Payload (kg)', 'Max Range (km)', 'Total Deliveries', 
                'Total Flight Hours', 'Next Maintenance'
            ]);
            
            // Data
            foreach ($drones as $drone) {
                fputcsv($file, [
                    $drone->name,
                    $drone->serial_number,
                    $drone->model,
                    $drone->status,
                    $drone->battery_level . '%',
                    $drone->max_payload_kg,
                    $drone->max_range_km,
                    $drone->total_deliveries,
                    $drone->total_flight_hours,
                    $drone->next_maintenance_date ? $drone->next_maintenance_date->format('Y-m-d') : 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
