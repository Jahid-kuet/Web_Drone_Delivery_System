<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'hospital']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();
        $hospitals = Hospital::where('is_active', true)->get();

        return view('admin.users.index', compact('users', 'roles', 'hospitals'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,slug'],
            'hospital_id' => ['nullable', 'exists:hospitals,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'hospital_id' => $validated['hospital_id'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'email_verified_at' => now(),
        ]);

        // Assign role
        $role = Role::where('slug', $validated['role'])->first();
        $user->roles()->attach($role->id);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'exists:roles,slug'],
            'hospital_id' => ['nullable', 'exists:hospitals,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'hospital_id' => $validated['hospital_id'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
        ]);

        // Update role
        $role = Role::where('slug', $validated['role'])->first();
        $user->roles()->sync([$role->id]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'status' => $user->status
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password reset successfully!');
    }
}
