@extends('layouts.app')

@section('title', 'My Deliveries')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Deliveries</h1>
        <p class="text-gray-600 mt-1">Manage your assigned deliveries</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Ready to Start</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-play-circle text-3xl text-blue-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">In Progress</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-helicopter text-3xl text-green-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Awaiting Confirmation</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" placeholder="Search by tracking number..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_transit">In Transit</option>
                    <option value="delivered">Delivered</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Deliveries List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="text-center py-12">
            <i class="fas fa-helicopter text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No assigned deliveries</p>
            <p class="text-gray-400 text-sm mt-2">Your assigned deliveries will appear here</p>
        </div>
    </div>
</div>
@endsection
