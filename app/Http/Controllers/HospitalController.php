<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HospitalController extends Controller
{
    /**
     * Display a listing of hospitals
     */
    public function index(Request $request)
    {
        $query = Hospital::query();
        
        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Type filter
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        
        // Location filter (within radius)
        if ($request->has('latitude') && $request->has('longitude') && $request->has('radius')) {
            $query->withinRadius(
                $request->input('latitude'),
                $request->input('longitude'),
                $request->input('radius')
            );
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $hospitals = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => Hospital::count(),
            'active' => Hospital::where('is_active', true)->count(),
            'inactive' => Hospital::where('is_active', false)->count(),
            'with_landing_pad' => Hospital::where('has_drone_landing_pad', true)->count(),
        ];
        
        return view('admin.hospitals.index', compact('hospitals', 'stats'));
    }

    /**
     * Show the form for creating a new hospital
     */
    public function create()
    {
        return view('admin.hospitals.create');
    }

    /**
     * Store a newly created hospital
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hospitals,code|max:50',
            'type' => 'required|string|in:hospital,clinic,health_center,pharmacy,other',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'operating_hours' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'has_drone_landing_pad' => 'required|boolean',
            'landing_pad_coordinates' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:100',
            'license_expiry_date' => 'nullable|date|after:today',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        
        $hospital = Hospital::create($validated);
        
        return redirect()->route('admin.hospitals.show', $hospital)
            ->with('success', 'Hospital created successfully!');
    }

    /**
     * Display the specified hospital
     */
    public function show(Hospital $hospital)
    {
        $hospital->load([
            'users',
            'deliveryRequests' => function ($query) {
                $query->latest()->limit(10);
            },
            'deliveries' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);
        
        // Get statistics
        $stats = [
            'total_requests' => $hospital->deliveryRequests()->count(),
            'pending_requests' => $hospital->deliveryRequests()->where('status', 'pending')->count(),
            'total_deliveries' => $hospital->deliveries()->count(),
            'completed_deliveries' => $hospital->deliveries()->where('status', 'completed')->count(),
            'active_users' => $hospital->users()->where('status', 'active')->count(),
        ];
        
        return view('admin.hospitals.show', compact('hospital', 'stats'));
    }

    /**
     * Show the form for editing the specified hospital
     */
    public function edit(Hospital $hospital)
    {
        return view('admin.hospitals.edit', compact('hospital'));
    }

    /**
     * Update the specified hospital
     */
    public function update(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:hospitals,code,' . $hospital->id,
            'type' => 'required|string|in:hospital,clinic,health_center,pharmacy,other',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'operating_hours' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'has_drone_landing_pad' => 'nullable|boolean',
            'landing_pad_coordinates' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:100',
            'license_expiry_date' => 'nullable|date',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        
        // Handle checkbox (checkboxes don't send anything when unchecked)
        $validated['has_drone_landing_pad'] = $request->has('has_drone_landing_pad');
        
        $hospital->update($validated);
        
        return redirect()->route('admin.hospitals.show', $hospital)
            ->with('success', 'Hospital updated successfully!');
    }

    /**
     * Remove the specified hospital
     */
    public function destroy(Hospital $hospital)
    {
        // Check if hospital has active deliveries
        $activeDeliveries = $hospital->deliveries()
            ->whereIn('status', ['pending', 'in_transit', 'picked_up'])
            ->count();
        
        if ($activeDeliveries > 0) {
            return redirect()->route('admin.hospitals.index')
                ->with('error', 'Cannot delete hospital with active deliveries!');
        }
        
        $hospital->delete();
        
        return redirect()->route('admin.hospitals.index')
            ->with('success', 'Hospital deleted successfully!');
    }

    /**
     * Get nearby hospitals (AJAX)
     */
    public function nearby(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1',
        ]);
        
        $radius = $validated['radius'] ?? 50; // Default 50km
        
        $hospitals = Hospital::active()
            ->withinRadius(
                $validated['latitude'],
                $validated['longitude'],
                $radius
            )
            ->orderByDistance(
                $validated['latitude'],
                $validated['longitude']
            )
            ->get()
            ->map(function ($hospital) use ($validated) {
                return [
                    'id' => $hospital->id,
                    'name' => $hospital->name,
                    'code' => $hospital->code,
                    'type' => $hospital->type,
                    'address' => $hospital->full_address,
                    'distance' => $hospital->distanceTo($validated['latitude'], $validated['longitude']),
                    'has_landing_pad' => $hospital->has_drone_landing_pad,
                    'is_open' => $hospital->isOpenNow(),
                    'phone' => $hospital->phone,
                ];
            });
        
        return response()->json([
            'count' => $hospitals->count(),
            'hospitals' => $hospitals,
        ]);
    }

    /**
     * Export hospitals list
     */
    public function export()
    {
        $hospitals = Hospital::all();
        
        $filename = 'hospitals-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($hospitals) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Code', 'Name', 'Type', 'City', 'State', 'Phone', 
                'Email', 'Status', 'Has Landing Pad', 'License Number'
            ]);
            
            foreach ($hospitals as $hospital) {
                fputcsv($file, [
                    $hospital->code,
                    $hospital->name,
                    $hospital->type,
                    $hospital->city,
                    $hospital->state,
                    $hospital->phone,
                    $hospital->email,
                    $hospital->status,
                    $hospital->has_drone_landing_pad ? 'Yes' : 'No',
                    $hospital->license_number,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
