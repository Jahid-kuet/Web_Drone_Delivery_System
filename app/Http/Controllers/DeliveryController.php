<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryRequest;
use App\Models\Drone;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries
     */
    public function index(Request $request)
    {
        $query = Delivery::with(['request.hospital', 'drone', 'request.supply']);
        
        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('request', function ($q) use ($search) {
                        $q->where('request_number', 'like', "%{$search}%");
                    });
            });
        }
        
        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Date range filter
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->input('end_date'));
        }
        
        // Active deliveries filter
        if ($request->has('active_only')) {
            $query->whereIn('status', ['pending', 'in_transit', 'picked_up']);
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $deliveries = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => Delivery::count(),
            'active' => Delivery::whereIn('status', ['pending', 'in_transit', 'picked_up'])->count(),
            'completed' => Delivery::where('status', 'completed')->count(),
            'failed' => Delivery::where('status', 'failed')->count(),
            'today' => Delivery::whereDate('created_at', today())->count(),
        ];
        
        return view('admin.deliveries.index', compact('deliveries', 'stats'));
    }

    /**
     * Show the form for creating a new delivery
     */
    public function create()
    {
        $pendingRequests = DeliveryRequest::approved()
            ->whereDoesntHave('deliveries', function ($query) {
                $query->whereIn('status', ['pending', 'in_transit', 'picked_up', 'completed']);
            })
            ->get();
        
        $availableDrones = Drone::available()
            ->where('battery_level', '>=', 30)
            ->get();
        
        return view('admin.deliveries.create', compact('pendingRequests', 'availableDrones'));
    }

    /**
     * Store a newly created delivery
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_request_id' => 'required|exists:delivery_requests,id',
            'drone_id' => 'required|exists:drones,id',
            'estimated_delivery_time' => 'required|date|after:now',
            'assigned_pilot_id' => 'nullable|exists:users,id',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Verify delivery request is approved
            $deliveryRequest = DeliveryRequest::find($validated['delivery_request_id']);
            if (!$deliveryRequest->isApproved()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Delivery request must be approved first!');
            }
            
            // Verify drone availability
            $drone = Drone::find($validated['drone_id']);
            if (!$drone->isAvailableForAssignment()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected drone is not available!');
            }
            
            // Calculate distance
            $hospital = $deliveryRequest->hospital;
            $distance = $hospital->distanceTo($drone->current_latitude ?? 0, $drone->current_longitude ?? 0);
            
            // Create delivery
            $delivery = Delivery::create(array_merge($validated, [
                'status' => 'pending',
                'distance_km' => $distance,
            ]));
            
            // Assign drone
            $drone->assignToDelivery($delivery->id);
            
            DB::commit();
            
            return redirect()->route('admin.deliveries.show', $delivery)
                ->with('success', 'Delivery created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create delivery: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified delivery
     */
    public function show(Delivery $delivery)
    {
        $delivery->load([
            'request.hospital',
            'request.supply',
            'drone',
            'assignedPilot',
            'trackingRecords' => function ($query) {
                $query->latest();
            },
            'confirmation',
        ]);
        
        return view('admin.deliveries.show', compact('delivery'));
    }

    /**
     * Start a delivery
     */
    public function start(Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Can only start pending deliveries!');
        }
        
        DB::beginTransaction();
        
        try {
            $delivery->start();
            
            // Update drone status
            $delivery->drone->takeOff();
            
            DB::commit();
            
            return redirect()->route('admin.deliveries.show', $delivery)
                ->with('success', 'Delivery started successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to start delivery: ' . $e->getMessage());
        }
    }

    /**
     * Mark delivery as delivered
     */
    public function markAsDelivered(Delivery $delivery)
    {
        if (!in_array($delivery->status, ['in_transit', 'picked_up'])) {
            return redirect()->back()
                ->with('error', 'Invalid delivery status!');
        }
        
        DB::beginTransaction();
        
        try {
            $delivery->markAsDelivered();
            
            // Update drone
            $delivery->drone->land();
            
            DB::commit();
            
            return redirect()->route('admin.deliveries.show', $delivery)
                ->with('success', 'Delivery marked as delivered!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to mark delivery: ' . $e->getMessage());
        }
    }

    /**
     * Complete a delivery
     */
    public function complete(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== 'delivered') {
            return redirect()->back()
                ->with('error', 'Can only complete delivered deliveries!');
        }
        
        $validated = $request->validate([
            'completion_notes' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $delivery->complete($validated['completion_notes'] ?? null);
            
            // Reduce stock
            $delivery->request->supply->reduceStock($delivery->request->quantity_requested);
            
            DB::commit();
            
            return redirect()->route('admin.deliveries.show', $delivery)
                ->with('success', 'Delivery completed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to complete delivery: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a delivery
     */
    public function cancel(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $delivery->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now(),
            ]);
            
            // Release drone
            if ($delivery->drone) {
                $delivery->drone->update(['status' => 'available']);
            }
            
            DB::commit();
            
            return redirect()->route('admin.deliveries.show', $delivery)
                ->with('success', 'Delivery cancelled!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to cancel delivery: ' . $e->getMessage());
        }
    }

    /**
     * Get active deliveries (AJAX)
     */
    public function active()
    {
        $deliveries = Delivery::with(['request.hospital', 'drone'])
            ->whereIn('status', ['pending', 'in_transit', 'picked_up'])
            ->get()
            ->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'tracking_number' => $delivery->tracking_number,
                    'hospital' => $delivery->request->hospital->name,
                    'drone' => $delivery->drone->name,
                    'status' => $delivery->status,
                    'progress' => $delivery->progress_percentage,
                    'eta' => $delivery->eta,
                    'distance_remaining' => $delivery->distance_remaining_km,
                    'position' => [
                        'latitude' => $delivery->current_latitude,
                        'longitude' => $delivery->current_longitude,
                    ],
                ];
            });
        
        return response()->json([
            'count' => $deliveries->count(),
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Get delivery tracking data (AJAX)
     */
    public function tracking(Delivery $delivery)
    {
        $trackingData = [
            'delivery' => [
                'id' => $delivery->id,
                'tracking_number' => $delivery->tracking_number,
                'status' => $delivery->status,
                'progress' => $delivery->progress_percentage,
                'eta' => $delivery->eta,
                'distance_total' => $delivery->distance_km,
                'distance_remaining' => $delivery->distance_remaining_km,
            ],
            'drone' => [
                'name' => $delivery->drone->name,
                'battery_level' => $delivery->drone->battery_level,
                'position' => [
                    'latitude' => $delivery->current_latitude,
                    'longitude' => $delivery->current_longitude,
                    'altitude' => $delivery->current_altitude,
                ],
            ],
            'destination' => [
                'hospital' => $delivery->request->hospital->name,
                'latitude' => $delivery->request->hospital->latitude,
                'longitude' => $delivery->request->hospital->longitude,
            ],
            'timeline' => [
                'created_at' => $delivery->created_at,
                'pickup_time' => $delivery->pickup_time,
                'estimated_delivery_time' => $delivery->estimated_delivery_time,
                'actual_delivery_time' => $delivery->actual_delivery_time,
            ],
        ];
        
        return response()->json($trackingData);
    }

    /**
     * Update delivery position (AJAX)
     */
    public function updatePosition(Request $request, Delivery $delivery)
    {
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
            'position' => [
                'latitude' => $delivery->current_latitude,
                'longitude' => $delivery->current_longitude,
                'altitude' => $delivery->current_altitude,
            ],
            'progress' => $delivery->progress_percentage,
            'eta' => $delivery->eta,
        ]);
    }
}
