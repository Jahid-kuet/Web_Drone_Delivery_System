@extends('layouts.app')

@section('title', 'Operator Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Drone Operator Dashboard</h1>
        <p class="text-gray-600 mt-1">Manage your assigned deliveries and monitor drone status</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Assigned Deliveries</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-tasks text-4xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">In Flight</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-helicopter text-4xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Completed Today</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-check-circle text-4xl text-purple-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Flight Hours</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">0h</h3>
                </div>
                <i class="fas fa-clock text-4xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    <!-- Active Deliveries -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Active Deliveries</h2>
        <div class="text-center py-12">
            <i class="fas fa-helicopter text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No active deliveries</p>
            <p class="text-gray-400 text-sm mt-2">Assigned deliveries will appear here</p>
        </div>
    </div>

    <!-- Drone Status -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Drones</h2>
        <div class="text-center py-12">
            <i class="fas fa-drone text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No drones assigned</p>
        </div>
    </div>
</div>
@endsection
