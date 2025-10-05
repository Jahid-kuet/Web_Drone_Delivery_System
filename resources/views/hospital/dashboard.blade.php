@extends('layouts.app')

@section('title', 'Hospital Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Hospital Dashboard</h1>
        <p class="text-gray-600 mt-1">Manage your delivery requests and track medical supplies</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Pending Requests</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-clock text-4xl text-yellow-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Active Deliveries</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-shipping-fast text-4xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Completed Today</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-check-circle text-4xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Emergency Requests</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a href="{{ route('hospital.requests.create') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6 hover:from-blue-600 hover:to-blue-700 transition">
            <i class="fas fa-plus-circle text-3xl mb-3"></i>
            <h3 class="text-xl font-semibold">New Delivery Request</h3>
            <p class="text-blue-100 mt-2">Request medical supplies delivery</p>
        </a>

        <a href="{{ route('hospital.deliveries.index') }}" class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6 hover:from-green-600 hover:to-green-700 transition">
            <i class="fas fa-map-marked-alt text-3xl mb-3"></i>
            <h3 class="text-xl font-semibold">Track Deliveries</h3>
            <p class="text-green-100 mt-2">Monitor active deliveries in real-time</p>
        </a>
    </div>

    <!-- Recent Requests -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Requests</h2>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No delivery requests yet</p>
            <a href="{{ route('hospital.requests.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Create your first request</a>
        </div>
    </div>
</div>
@endsection
