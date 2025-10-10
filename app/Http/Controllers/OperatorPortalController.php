<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Drone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorPortalController extends Controller
{
    /**
     * Display the operator dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Statistics
        $stats = [
            'assigned_deliveries' => Delivery::where('assigned_pilot_id', $user->id)
                ->whereNotIn('status', ['completed', 'failed', 'cancelled'])
                ->count(),
            'in_flight' => Delivery::where('assigned_pilot_id', $user->id)
                ->where('status', 'in_transit')
                ->count(),
            'completed_today' => Delivery::where('assigned_pilot_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('delivery_completed_time', today())
                ->count(),
            'flight_hours' => $this->calculateFlightHours($user->id),
        ];

        // Today's deliveries
        $todayDeliveries = Delivery::where('assigned_pilot_id', $user->id)
            ->whereDate('scheduled_departure_time', today())
            ->with(['deliveryRequest.hospital', 'drone'])
            ->orderBy('scheduled_departure_time', 'asc')
            ->get();

        // Assigned drones (through active deliveries)
        $drones = Drone::whereHas('assignments', function($q) use ($user) {
            $q->whereHas('delivery', function($d) use ($user) {
                $d->where('assigned_pilot_id', $user->id)
                  ->whereIn('status', ['pending', 'in_transit']);
            });
        })->get();

        // Low battery drones (only for assigned drones)
        $lowBatteryDrones = Drone::whereHas('assignments', function($q) use ($user) {
            $q->whereHas('delivery', function($d) use ($user) {
                $d->where('assigned_pilot_id', $user->id)
                  ->whereIn('status', ['pending', 'in_transit']);
            });
        })
        ->where('current_battery_level', '<', 30)
        ->get();

        return view('operator.dashboard', compact('stats', 'todayDeliveries', 'drones', 'lowBatteryDrones'));
    }

    /**
     * Display drones assigned to this operator
     */
    public function dronesIndex()
    {
        $user = Auth::user();

        // Get drones through active assignments
        $drones = Drone::whereHas('assignments', function($q) use ($user) {
            $q->whereHas('delivery', function($d) use ($user) {
                $d->where('assigned_pilot_id', $user->id)
                  ->whereIn('status', ['pending', 'in_transit']);
            });
        })->with(['assignments.delivery'])->get();

        // Stats
        $stats = [
            'available' => $drones->where('status', 'available')->count(),
            'in_flight' => $drones->where('status', 'in_flight')->count(),
        ];

        return view('operator.drones.index', compact('drones', 'stats'));
    }

    /**
     * Display deliveries assigned to this operator
     */
    public function deliveriesIndex(Request $request)
    {
        $user = Auth::user();

        $query = Delivery::where('assigned_pilot_id', $user->id)
            ->with(['deliveryRequest.hospital', 'drone']);

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sort by status priority
        $query->orderByRaw("
            CASE status
                WHEN 'in_transit' THEN 1
                WHEN 'pending' THEN 2
                WHEN 'delivered' THEN 3
                WHEN 'completed' THEN 4
                ELSE 5
            END
        ")->orderBy('scheduled_departure_time', 'asc');

        $deliveries = $query->paginate(15);

        // Stats for filter tabs
        $stats = [
            'total' => Delivery::where('assigned_pilot_id', $user->id)->count(),
            'pending' => Delivery::where('assigned_pilot_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'in_transit' => Delivery::where('assigned_pilot_id', $user->id)
                ->where('status', 'in_transit')
                ->count(),
            'delivered' => Delivery::where('assigned_pilot_id', $user->id)
                ->where('status', 'delivered')
                ->count(),
        ];

        return view('operator.deliveries.index', compact('deliveries', 'stats'));
    }

    /**
     * Display a specific delivery control panel
     */
    public function deliveriesShow($id)
    {
        $user = Auth::user();

        $delivery = Delivery::where('id', $id)
            ->where('assigned_pilot_id', $user->id)
            ->with(['deliveryRequest.hospital', 'drone', 'assignedPilot'])
            ->firstOrFail();

        return view('operator.deliveries.show', compact('delivery'));
    }

    /**
     * Start a delivery
     */
    public function startDelivery(Request $request, $id)
    {
        $user = Auth::user();

        $delivery = Delivery::where('id', $id)
            ->where('assigned_pilot_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $delivery->update([
            'status' => 'in_transit',
            'actual_departure_time' => now(),
        ]);

        // Update drone status
        if ($delivery->drone) {
            $delivery->drone->update(['status' => 'in_flight']);
        }

        return redirect()->back()->with('success', 'Delivery started successfully!');
    }

    /**
     * Mark delivery as delivered
     */
    public function markAsDelivered(Request $request, $id)
    {
        $user = Auth::user();

        $delivery = Delivery::where('id', $id)
            ->where('assigned_pilot_id', $user->id)
            ->where('status', 'in_transit')
            ->firstOrFail();

        $delivery->update([
            'status' => 'delivered',
            'actual_arrival_time' => now(),
        ]);

        // Update drone status
        if ($delivery->drone) {
            $delivery->drone->update(['status' => 'available']);
        }

        return redirect()->back()->with('success', 'Delivery marked as delivered! Awaiting confirmation.');
    }

    /**
     * Cancel a delivery
     */
    public function cancelDelivery(Request $request, $id)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $delivery = Delivery::where('id', $id)
            ->where('assigned_pilot_id', $user->id)
            ->whereIn('status', ['pending', 'in_transit'])
            ->firstOrFail();

        $delivery->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        // Update drone status
        if ($delivery->drone) {
            $delivery->drone->update(['status' => 'available']);
        }

        return redirect()->route('operator.deliveries.index')
            ->with('success', 'Delivery cancelled.');
    }

    /**
     * Report an incident
     */
    public function reportIncident(Request $request, $id)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'incident_type' => 'required|string',
            'incident_description' => 'required|string',
            'incident_severity' => 'required|in:low,medium,high,critical',
        ]);

        $delivery = Delivery::where('id', $id)
            ->where('assigned_pilot_id', $user->id)
            ->firstOrFail();

        $incidents = json_decode($delivery->incidents, true) ?? [];
        $incidents[] = array_merge($validated, [
            'reported_at' => now()->toISOString(),
            'reported_by' => $user->name,
        ]);

        $delivery->update([
            'incidents' => json_encode($incidents),
        ]);

        return redirect()->back()->with('success', 'Incident reported successfully.');
    }

    /**
     * Calculate total flight hours for an operator
     */
    private function calculateFlightHours($userId)
    {
        $deliveries = Delivery::where('assigned_pilot_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('actual_departure_time')
            ->whereNotNull('actual_arrival_time')
            ->get();

        $totalMinutes = 0;
        foreach ($deliveries as $delivery) {
            if ($delivery->actual_departure_time && $delivery->actual_arrival_time) {
                $totalMinutes += $delivery->actual_departure_time->diffInMinutes($delivery->actual_arrival_time);
            }
        }

        return round($totalMinutes / 60, 1);
    }
}
