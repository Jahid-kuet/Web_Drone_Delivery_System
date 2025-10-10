<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryRequest;
use App\Models\Drone;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display reports index
     */
    public function index()
    {
        $hospitals = Hospital::where('is_active', true)->get();
        $drones = Drone::all();
        
        return view('admin.reports.index', compact('hospitals', 'drones'));
    }

    /**
     * Generate delivery report
     */
    public function deliveryReport(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:daily,weekly,monthly',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'hospital_id' => 'nullable|exists:hospitals,id',
            'status' => 'nullable|string',
        ]);

        $query = Delivery::with(['deliveryRequest.hospital', 'drone']);

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply hospital filter
        if ($request->filled('hospital_id')) {
            $query->whereHas('deliveryRequest', function($q) use ($request) {
                $q->where('hospital_id', $request->hospital_id);
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deliveries = $query->latest()->paginate(20);
        
        $hospital = null;
        if ($request->filled('hospital_id')) {
            $hospital = Hospital::find($request->hospital_id);
        }

        // Calculate statistics
        $stats = [
            'total' => $deliveries->total(),
            'completed' => Delivery::where('status', 'completed')
                ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
                ->count(),
            'in_transit' => Delivery::where('status', 'in_transit')
                ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
                ->count(),
            'failed' => Delivery::where('status', 'failed')
                ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
                ->count(),
        ];

        return view('admin.reports.delivery', compact('deliveries', 'stats', 'hospital'));
    }

    /**
     * Generate drone performance report
     */
    public function droneReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'drone_id' => 'nullable|exists:drones,id',
        ]);

        $query = Drone::withCount(['deliveries' => function($q) use ($request) {
            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }
        }]);

        if ($request->filled('drone_id')) {
            $query->where('id', $request->drone_id);
        }

        $drones = $query->paginate(20);
        
        $selectedDrone = null;
        if ($request->filled('drone_id')) {
            $selectedDrone = Drone::find($request->drone_id);
        }

        return view('admin.reports.drone', compact('drones', 'selectedDrone'));
    }

    /**
     * Generate hospital activity report
     */
    public function hospitalReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $hospitals = Hospital::withCount(['deliveryRequests' => function($q) use ($request) {
            if ($request->filled('date_from')) {
                $q->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->whereDate('created_at', '<=', $request->date_to);
            }
        }])->paginate(20);

        return view('admin.reports.hospital', compact('hospitals'));
    }
}
