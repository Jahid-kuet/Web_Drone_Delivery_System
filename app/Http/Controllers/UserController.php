<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'hospital']);
        
        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        // Role filter
        if ($role = $request->input('role')) {
            $query->withRole($role);
        }
        
        // Hospital filter
        if ($hospitalId = $request->input('hospital_id')) {
            $query->where('hospital_id', $hospitalId);
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $users = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'drone_pilots' => User::dronePilots()->count(),
            'expiring_licenses' => User::expiringLicenses()->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        $hospitals = Hospital::active()->get();
        
        return view('admin.users.create', compact('roles', 'hospitals'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'hospital_id' => 'nullable|exists:hospitals,id',
            'license_expiry_date' => 'nullable|date|after:today',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive,suspended',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'profile_photo' => 'nullable|image|max:2048',
        ]);
        
        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        // Handle profile photo
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        
        // Create user
        $roles = $validated['roles'];
        unset($validated['roles']);
        
        $user = User::create($validated);
        
        // Assign roles
        $user->roles()->attach($roles);
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load([
            'roles',
            'hospital',
            'deliveryRequests' => function ($query) {
                $query->latest()->limit(10);
            },
            'assignedDeliveries' => function ($query) {
                $query->latest()->limit(10);
            },
            'auditLogs' => function ($query) {
                $query->latest()->limit(20);
            },
        ]);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $hospitals = Hospital::active()->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'hospitals'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'hospital_id' => 'nullable|exists:hospitals,id',
            'license_expiry_date' => 'nullable|date',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive,suspended',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'profile_photo' => 'nullable|image|max:2048',
        ]);
        
        // Hash password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        // Handle profile photo
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        
        // Update user
        $roles = $validated['roles'];
        unset($validated['roles']);
        
        $user->update($validated);
        
        // Sync roles
        $user->roles()->sync($roles);
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Check if user has active deliveries
        $activeDeliveries = $user->assignedDeliveries()
            ->whereIn('status', ['pending', 'in_transit', 'picked_up'])
            ->count();
        
        if ($activeDeliveries > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with active deliveries!');
        }
        
        // Delete profile photo
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Suspend a user
     */
    public function suspend(Request $request, User $user)
    {
        $validated = $request->validate([
            'suspension_reason' => 'required|string|max:500',
        ]);
        
        $user->update([
            'status' => 'suspended',
        ]);
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User suspended successfully!');
    }

    /**
     * Activate a user
     */
    public function activate(User $user)
    {
        $user->update([
            'status' => 'active',
        ]);
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User activated successfully!');
    }
}
