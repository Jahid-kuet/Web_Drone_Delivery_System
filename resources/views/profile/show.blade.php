@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-600 mt-1">View and manage your account information</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Personal Information</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="text-gray-900 font-medium">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-gray-900">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="text-gray-900">{{ auth()->user()->phone ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Member Since</p>
                        <p class="text-gray-900">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-block">
                        <i class="fas fa-edit mr-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Avatar & Role -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Avatar</h2>
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-blue-500 flex items-center justify-center text-white text-4xl font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Roles</h2>
                <div class="space-y-2">
                    @forelse(auth()->user()->roles ?? [] as $role)
                    <span class="block px-3 py-2 bg-purple-100 text-purple-800 rounded text-center">
                        {{ $role->name }}
                    </span>
                    @empty
                    <p class="text-gray-500 text-sm">No roles assigned</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
