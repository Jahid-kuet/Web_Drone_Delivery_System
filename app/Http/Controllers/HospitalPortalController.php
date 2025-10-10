<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryRequest;
use App\Models\Hospital;
use App\Models\MedicalSupply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HospitalPortalController extends Controller
{
    /**
     * Display the hospital dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('home')->with('error', 'You are not associated with any hospital.');
        }

        // Statistics
        $stats = [
            'pending_requests' => DeliveryRequest::where('hospital_id', $hospital->id)
                ->where('status', 'pending')
                ->count(),
            'active_deliveries' => Delivery::whereHas('deliveryRequest', function ($query) use ($hospital) {
                $query->where('hospital_id', $hospital->id);
            })->whereIn('status', ['pending', 'in_transit'])->count(),
            'completed_today' => Delivery::whereHas('deliveryRequest', function ($query) use ($hospital) {
                $query->where('hospital_id', $hospital->id);
            })->where('status', 'completed')
                ->whereDate('delivery_completed_time', today())
                ->count(),
            'emergency_requests' => DeliveryRequest::where('hospital_id', $hospital->id)
                ->where('priority', 'emergency')
                ->whereIn('status', ['pending', 'approved'])
                ->count(),
        ];

        // Recent requests
        $recentRequests = DeliveryRequest::where('hospital_id', $hospital->id)
            ->with(['requestedBy', 'hospital'])
            ->latest()
            ->take(10)
            ->get();

        // Active deliveries
        $activeDeliveries = Delivery::whereHas('deliveryRequest', function ($query) use ($hospital) {
            $query->where('hospital_id', $hospital->id);
        })->whereIn('status', ['pending', 'in_transit'])
            ->with(['drone', 'deliveryRequest'])
            ->latest()
            ->take(5)
            ->get();

        // Low stock supplies (optional, if tracking inventory)
        $lowStockSupplies = MedicalSupply::where('quantity_available', '<', 10)
            ->orderBy('quantity_available', 'asc')
            ->take(6)
            ->get();

        return view('hospital.dashboard', compact('stats', 'recentRequests', 'activeDeliveries', 'hospital', 'lowStockSupplies'));
    }

    /**
     * Display delivery requests list
     */
    public function requestsIndex(Request $request)
    {
        $user = Auth::user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('home')->with('error', 'You are not associated with any hospital.');
        }

        $query = DeliveryRequest::where('hospital_id', $hospital->id)
            ->with(['requestedBy', 'hospital']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where('request_number', 'like', "%{$search}%");
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Priority filter
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $requests = $query->latest()->paginate(15);

        return view('hospital.requests.index', compact('requests', 'hospital'));
    }

    /**
     * Show the form for creating a new delivery request
     */
    public function requestsCreate()
    {
        $user = Auth::user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('home')->with('error', 'You are not associated with any hospital.');
        }

        $medicalSupplies = MedicalSupply::where('is_active', true)
            ->where('quantity_available', '>', 0)
            ->orderBy('name')
            ->get();

        return view('hospital.requests.create', compact('hospital', 'medicalSupplies'));
    }

    /**
     * Store a new delivery request
     */
    public function requestsStore(Request $request)
    {
        $user = Auth::user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('home')->with('error', 'You are not associated with any hospital.');
        }

        $validated = $request->validate([
            'priority' => 'required|in:emergency,high,medium,low',
            'description' => 'required|string',
            'requested_date' => 'required|date',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'supplies' => 'required|array|min:1',
            'supplies.*.supply_id' => 'required|exists:medical_supplies,id',
            'supplies.*.quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
        ]);

        // Generate request number
        $requestNumber = 'REQ-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Calculate total weight from supplies
        $totalWeight = 0;
        $totalVolume = 0;
        foreach ($validated['supplies'] as $supply) {
            $medicalSupply = MedicalSupply::find($supply['supply_id']);
            if ($medicalSupply) {
                $totalWeight += $medicalSupply->weight_kg * $supply['quantity'];
                $totalVolume += ($medicalSupply->volume_ml ?? 0) * $supply['quantity'];
            }
        }

        // Map priority to urgency_level
        $urgencyMap = [
            'low' => 'routine',
            'medium' => 'routine',
            'high' => 'urgent',
            'emergency' => 'emergency',
        ];
        $urgencyLevel = $urgencyMap[$validated['priority']] ?? 'routine';

        // Create delivery request
        $deliveryRequest = DeliveryRequest::create([
            'request_number' => $requestNumber,
            'hospital_id' => $hospital->id,
            'requested_by_user_id' => $user->id,
            'priority' => $validated['priority'],
            'urgency_level' => $urgencyLevel,
            'description' => $validated['description'],
            'requested_delivery_time' => $validated['requested_date'],
            'total_weight_kg' => $totalWeight,
            'total_volume_ml' => $totalVolume > 0 ? $totalVolume : null,
            'status' => 'pending',
            'medical_supplies' => json_encode($validated['supplies']),
            'delivery_location' => json_encode([
                'latitude' => $hospital->latitude,
                'longitude' => $hospital->longitude,
                'address' => $hospital->address,
            ]),
            'special_instructions' => $validated['special_instructions'],
            'contact_person' => $validated['contact_person'] ?? $user->name,
            'contact_phone' => $validated['contact_phone'] ?? $user->phone,
        ]);

        return redirect()->route('hospital.requests.index')
            ->with('success', 'Delivery request created successfully! Request Number: ' . $requestNumber);
    }

    /**
     * Display deliveries to this hospital
     */
    public function deliveriesIndex(Request $request)
    {
        $user = Auth::user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('home')->with('error', 'You are not associated with any hospital.');
        }

        $query = Delivery::whereHas('deliveryRequest', function ($q) use ($hospital) {
            $q->where('hospital_id', $hospital->id);
        })->with(['deliveryRequest', 'drone', 'assignedPilot']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where('tracking_number', 'like', "%{$search}%");
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $deliveries = $query->latest()->paginate(15);

        return view('hospital.deliveries.index', compact('deliveries', 'hospital'));
    }

    /**
     * Display delivery history with advanced filters
     */
    public function deliveryHistory(Request $request)
    {
        $user = Auth::user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('home')->with('error', 'You are not associated with any hospital.');
        }

        $query = Delivery::whereHas('deliveryRequest', function ($q) use ($hospital) {
            $q->where('hospital_id', $hospital->id);
        })->with(['deliveryRequest', 'drone', 'assignedPilot']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where('tracking_number', 'like', "%{$search}%");
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Date range filter
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Priority filter (from delivery request)
        if ($priority = $request->input('priority')) {
            $query->whereHas('deliveryRequest', function ($q) use ($priority) {
                $q->where('priority', $priority);
            });
        }

        $deliveries = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => Delivery::whereHas('deliveryRequest', function ($q) use ($hospital) {
                $q->where('hospital_id', $hospital->id);
            })->count(),
            'delivered' => Delivery::whereHas('deliveryRequest', function ($q) use ($hospital) {
                $q->where('hospital_id', $hospital->id);
            })->where('status', 'delivered')->count(),
            'cancelled' => Delivery::whereHas('deliveryRequest', function ($q) use ($hospital) {
                $q->where('hospital_id', $hospital->id);
            })->where('status', 'cancelled')->count(),
            'in_transit' => Delivery::whereHas('deliveryRequest', function ($q) use ($hospital) {
                $q->where('hospital_id', $hospital->id);
            })->where('status', 'in_transit')->count(),
        ];

        return view('hospital.history', compact('deliveries', 'hospital', 'stats'));
    }

    /**
     * Get notifications for AJAX request
     */
    public function getNotifications()
    {
        $user = Auth::user();
        
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'read_at' => $notification->read_at,
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Display all notifications page
     */
    public function notificationsIndex()
    {
        $user = Auth::user();
        $hospital = $user->hospital;

        if (!$hospital) {
            return redirect()->route('home')->with('error', 'You are not associated with any hospital.');
        }

        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('hospital.notifications.index', compact('notifications', 'hospital'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($notificationId)
    {
        $notification = \App\Models\Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        \App\Models\Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}

