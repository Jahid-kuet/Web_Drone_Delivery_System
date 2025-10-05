@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Roles & Permissions</h1>
            <p class="text-gray-600 mt-1">Manage user roles and their permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>Create Role
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles ?? [] as $role)
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $role->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $role->slug }}</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                    Level {{ $role->level ?? 0 }}
                </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-4">{{ $role->description }}</p>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Permissions ({{ count($role->permissions ?? []) }})</p>
                <div class="flex flex-wrap gap-1">
                    @forelse(($role->permissions ?? [])->take(3) as $permission)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $permission->name }}</span>
                    @empty
                    <span class="text-gray-400 text-xs">No permissions</span>
                    @endforelse
                    @if(count($role->permissions ?? []) > 3)
                    <span class="text-gray-500 text-xs">+{{ count($role->permissions) - 3 }} more</span>
                    @endif
                </div>
            </div>
            
            <div class="flex justify-between items-center pt-4 border-t">
                <span class="text-sm text-gray-500">{{ $role->users_count ?? 0 }} users</span>
                <div class="space-x-2">
                    <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-green-600 hover:text-green-900"><i class="fas fa-edit"></i></a>
                    @if(($role->users_count ?? 0) == 0)
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Delete this role?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <i class="fas fa-user-shield text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No roles found</p>
            <a href="{{ route('admin.roles.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Create your first role</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
