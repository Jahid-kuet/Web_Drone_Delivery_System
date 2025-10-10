<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRequest;
use App\Models\Hospital;
use App\Models\MedicalSupply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeliveryRequestController extends Controller
{
    /**
     * Display a listing of delivery requests
     */
    public function index(Request $request)
    {
        $query = DeliveryRequest::with(['hospital', 'requestedBy']);
        
        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhereHas('hospital', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }
        
        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Priority filter
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }
        
        // Hospital filter
        if ($hospitalId = $request->input('hospital_id')) {
            $query->where('hospital_id', $hospitalId);
        }
        
        // Date range filter
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->input('end_date'));
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $requests = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => DeliveryRequest::count(),
            'pending' => DeliveryRequest::where('status', 'pending')->count(),
            'approved' => DeliveryRequest::where('status', 'approved')->count(),
            'rejected' => DeliveryRequest::where('status', 'rejected')->count(),
            'emergency' => DeliveryRequest::where('priority', 'emergency')->count(),
            'overdue' => DeliveryRequest::overdue()->count(),
        ];
        
        return view('admin.delivery-requests.index', compact('requests', 'stats'));
    }

    /**
     * Show the form for creating a new delivery request
     */
    public function create()
    {
        $hospitals = Hospital::active()->get();
        $supplies = MedicalSupply::active()->get();
        
        return view('admin.delivery-requests.create', compact('hospitals', 'supplies'));
    }

    /**
     * Store a newly created delivery request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'medical_supply_id' => 'required|exists:medical_supplies,id',
            'quantity_requested' => 'required|integer|min:1',
            'priority' => 'required|string|in:low,normal,high,urgent,emergency',
            'required_by_date' => 'required|date|after:now',
            'delivery_notes' => 'nullable|string|max:1000',
            'special_instructions' => 'nullable|string|max:1000',
            'requires_temperature_control' => 'required|boolean',
            'temperature_range' => 'nullable|string|max:100',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Check supply availability
            $supply = MedicalSupply::find($validated['medical_supply_id']);
            if (!$supply->isAvailableForDelivery($validated['quantity_requested'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Insufficient stock for this supply!');
            }
            
            // Create delivery request
            $deliveryRequest = DeliveryRequest::create(array_merge($validated, [
                'requested_by_user_id' => Auth::id(),
                'status' => 'pending',
            ]));
            
            DB::commit();
            
            // Notify relevant users
            $this->notifyNewRequest($deliveryRequest);
            
            return redirect()->route('admin.delivery-requests.show', $deliveryRequest)
                ->with('success', 'Delivery request created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create delivery request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified delivery request
     */
    public function show(DeliveryRequest $deliveryRequest)
    {
        $deliveryRequest->load([
            'hospital',
            'requestedBy',
            'approvedBy',
            'delivery',
        ]);
        
        return view('admin.delivery-requests.show', compact('deliveryRequest'));
    }

    /**
     * Show the form for editing the specified delivery request
     */
    public function edit(DeliveryRequest $deliveryRequest)
    {
        // Only allow editing if pending
        if (!$deliveryRequest->isPending()) {
            return redirect()->route('admin.delivery-requests.show', $deliveryRequest)
                ->with('error', 'Can only edit pending requests!');
        }
        
        $hospitals = Hospital::active()->get();
        $supplies = MedicalSupply::active()->get();
        
        return view('admin.delivery-requests.edit', compact('deliveryRequest', 'hospitals', 'supplies'));
    }

    /**
     * Update the specified delivery request
     */
    public function update(Request $request, DeliveryRequest $deliveryRequest)
    {
        // Only allow updating if pending
        if (!$deliveryRequest->isPending()) {
            return redirect()->route('admin.delivery-requests.show', $deliveryRequest)
                ->with('error', 'Can only update pending requests!');
        }
        
        $validated = $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'medical_supply_id' => 'required|exists:medical_supplies,id',
            'quantity_requested' => 'required|integer|min:1',
            'priority' => 'required|string|in:low,normal,high,urgent,emergency',
            'required_by_date' => 'required|date|after:now',
            'delivery_notes' => 'nullable|string|max:1000',
            'special_instructions' => 'nullable|string|max:1000',
            'requires_temperature_control' => 'required|boolean',
            'temperature_range' => 'nullable|string|max:100',
        ]);
        
        $deliveryRequest->update($validated);
        
        return redirect()->route('admin.delivery-requests.show', $deliveryRequest)
            ->with('success', 'Delivery request updated successfully!');
    }

    /**
     * Approve a delivery request
     */
    public function approve(Request $request, DeliveryRequest $deliveryRequest)
    {
        if (!$deliveryRequest->isPending()) {
            return redirect()->back()
                ->with('error', 'Can only approve pending requests!');
        }
        
        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $deliveryRequest->approve($validated['approval_notes'] ?? null);
            
            DB::commit();
            
            // Notify relevant users
            $this->notifyRequestApproved($deliveryRequest);
            
            return redirect()->route('admin.delivery-requests.show', $deliveryRequest)
                ->with('success', 'Delivery request approved successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    /**
     * Reject a delivery request
     */
    public function reject(Request $request, DeliveryRequest $deliveryRequest)
    {
        if (!$deliveryRequest->isPending()) {
            return redirect()->back()
                ->with('error', 'Can only reject pending requests!');
        }
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $deliveryRequest->reject($validated['rejection_reason']);
            
            DB::commit();
            
            // Notify relevant users
            $this->notifyRequestRejected($deliveryRequest);
            
            return redirect()->route('admin.delivery-requests.show', $deliveryRequest)
                ->with('success', 'Delivery request rejected!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to reject request: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a delivery request
     */
    public function cancel(Request $request, DeliveryRequest $deliveryRequest)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $deliveryRequest->cancel($validated['cancellation_reason']);
            
            DB::commit();
            
            return redirect()->route('admin.delivery-requests.show', $deliveryRequest)
                ->with('success', 'Delivery request cancelled!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to cancel request: ' . $e->getMessage());
        }
    }

    /**
     * Delete a delivery request
     */
    public function destroy(DeliveryRequest $deliveryRequest)
    {
        // Check if request has an associated delivery
        if ($deliveryRequest->delivery) {
            return redirect()->back()
                ->with('error', 'Cannot delete request with associated delivery!');
        }
        
        // Only allow deletion of pending, rejected, or cancelled requests
        if (!in_array($deliveryRequest->status, ['pending', 'rejected', 'cancelled'])) {
            return redirect()->back()
                ->with('error', 'Can only delete pending, rejected, or cancelled requests!');
        }
        
        try {
            $deliveryRequest->delete();
            
            return redirect()->route('admin.delivery-requests.index')
                ->with('success', 'Delivery request deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete request: ' . $e->getMessage());
        }
    }

    /**
     * Get pending requests (AJAX)
     */
    public function pending()
    {
        $requests = DeliveryRequest::pending()
            ->with(['hospital'])
            ->orderByPriority('desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'request_number' => $request->request_number,
                    'hospital' => $request->hospital->name,
                    'medical_supplies' => $request->medical_supplies,
                    'priority' => $request->priority,
                    'required_by' => $request->required_by_date->format('Y-m-d H:i'),
                    'is_overdue' => $request->isOverdue(),
                    'created_at' => $request->created_at->diffForHumans(),
                ];
            });
        
        return response()->json([
            'count' => $requests->count(),
            'requests' => $requests,
        ]);
    }

    /**
     * Notify about new request
     */
    private function notifyNewRequest(DeliveryRequest $request)
    {
        // Implementation would send notifications to relevant users
        // Using notification system, email, SMS, etc.
    }

    /**
     * Notify about approved request
     */
    private function notifyRequestApproved(DeliveryRequest $request)
    {
        // Implementation would send notifications
    }

    /**
     * Notify about rejected request
     */
    private function notifyRequestRejected(DeliveryRequest $request)
    {
        // Implementation would send notifications
    }
}
