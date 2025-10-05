@extends('layouts.app')

@section('title', 'Role Details - ' . $role->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $role->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $role->slug }}</p>
        </div>
        <a href="{{ route('admin.roles.edit', $role) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-edit mr-2"></i>Edit Role
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Role Information</h2>
                <div class="space-y-3">
                    <div><p class="text-sm text-gray-600">Name</p><p class="text-gray-900 font-medium">{{ $role->name }}</p></div>
                    <div><p class="text-sm text-gray-600">Slug</p><p class="text-gray-900">{{ $role->slug }}</p></div>
                    <div><p class="text-sm text-gray-600">Description</p><p class="text-gray-900">{{ $role->description ?? 'No description' }}</p></div>
                    <div><p class="text-sm text-gray-600">Level</p><p class="text-gray-900">{{ $role->level ?? 1 }}</p></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Permissions ({{ count($role->permissions ?? []) }})</h2>
                <div class="grid grid-cols-2 gap-2">
                    @forelse($role->permissions ?? [] as $permission)
                    <div class="flex items-center px-3 py-2 bg-gray-50 rounded">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-sm">{{ $permission->name }}</span>
                    </div>
                    @empty
                    <p class="col-span-2 text-gray-500">No permissions assigned</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Users ({{ count($role->users ?? []) }})</h2>
                <div class="space-y-2">
                    @forelse(($role->users ?? [])->take(10) as $user)
                    <div class="flex items-center py-2">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-semibold">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500">No users assigned to this role</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.roles.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">Back to Roles</a>
    </div>
</div>
@endsection
