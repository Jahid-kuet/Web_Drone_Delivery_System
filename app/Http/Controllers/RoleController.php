<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:100',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hierarchy_level' => 'required|integer|min:1|max:10',
            'max_users' => 'nullable|integer|min:1',
            'is_system_role' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);
        
        $role = Role::create($validated);
        
        // Attach permissions
        if (!empty($permissions)) {
            $role->permissions()->attach($permissions);
        }
        
        return redirect()->route('admin.roles.show', $role)
            ->with('success', 'Role created successfully!');
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        if ($role->is_system_role) {
            return redirect()->route('admin.roles.show', $role)
                ->with('error', 'Cannot edit system roles!');
        }
        
        $permissions = Permission::all()->groupBy('module');
        $role->load('permissions');
        
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        if ($role->is_system_role) {
            return redirect()->route('admin.roles.show', $role)
                ->with('error', 'Cannot edit system roles!');
        }
        
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hierarchy_level' => 'required|integer|min:1|max:10',
            'max_users' => 'nullable|integer|min:1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);
        
        $role->update($validated);
        
        // Sync permissions
        $role->permissions()->sync($permissions);
        
        return redirect()->route('admin.roles.show', $role)
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        if ($role->is_system_role) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete system roles!');
        }
        
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role with assigned users!');
        }
        
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully!');
    }
}
