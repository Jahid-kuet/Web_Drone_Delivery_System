@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
        <p class="text-gray-600 mt-1">{{ $user->email }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">User Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-sm text-gray-600">Name</p><p class="text-gray-900 font-medium">{{ $user->name }}</p></div>
                    <div><p class="text-sm text-gray-600">Email</p><p class="text-gray-900">{{ $user->email }}</p></div>
                    <div><p class="text-sm text-gray-600">Phone</p><p class="text-gray-900">{{ $user->phone ?? 'N/A' }}</p></div>
                    <div><p class="text-sm text-gray-600">Status</p>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>
                    </div>
                    <div><p class="text-sm text-gray-600">Created</p><p class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</p></div>
                    <div><p class="text-sm text-gray-600">Last Login</p><p class="text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</p></div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Roles</h2>
                <div class="space-y-2">
                    @forelse($user->roles ?? [] as $role)
                    <span class="block px-3 py-2 bg-purple-100 text-purple-800 rounded">{{ $role->name }}</span>
                    @empty
                    <p class="text-gray-500">No roles assigned</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex space-x-2">
        <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Edit</a>
        <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">Back to List</a>
    </div>
</div>
@endsection
