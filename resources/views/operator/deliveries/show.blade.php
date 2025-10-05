@extends('layouts.app')

@section('title', 'Delivery Control Panel')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('operator.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Deliveries
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Delivery Control Panel</h1>
        <p class="text-gray-600 mt-1">Manage and monitor this delivery</p>
    </div>

    <!-- Delivery Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tracking #: DR-2024-0001</h2>
                <p class="text-gray-600 mt-1">Assigned to you</p>
            </div>
            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-medium">Pending</span>
        </div>
    </div>

    <!-- Pre-Flight Checklist -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6" x-data="{ checklist: [] }">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Pre-Flight Checklist</h3>
        <div class="space-y-3">
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Battery fully charged (100%)</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Weather conditions acceptable</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Cargo secured properly</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">GPS signal strong</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Communication systems operational</span>
            </label>
        </div>
    </div>

    <!-- Delivery Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Hospital</p>
                    <p class="text-gray-900 font-medium">Hospital Name</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Scheduled Departure</p>
                    <p class="text-gray-900">Not scheduled</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estimated Arrival</p>
                    <p class="text-gray-900">Not calculated</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Drone Status</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Drone Model</p>
                    <p class="text-gray-900 font-medium">Model Name</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Battery Level</p>
                    <p class="text-gray-900">100%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Available</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-wrap gap-4">
            <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex-1">
                <i class="fas fa-play mr-2"></i>Start Delivery
            </button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg flex-1">
                <i class="fas fa-times mr-2"></i>Cancel Delivery
            </button>
            <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg flex-1">
                <i class="fas fa-exclamation-triangle mr-2"></i>Report Issue
            </button>
        </div>
    </div>
</div>
@endsection
