@extends('layouts.app')

@section('title', 'Track Deliveries')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Track Deliveries</h1>
        <p class="text-gray-600 mt-1">Monitor medical supply deliveries to your hospital</p>
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
                    <option value="failed">Failed</option>
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
            <i class="fas fa-shipping-fast text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No deliveries found</p>
            <p class="text-gray-400 text-sm mt-2">Deliveries to your hospital will appear here</p>
        </div>
    </div>
</div>
@endsection
