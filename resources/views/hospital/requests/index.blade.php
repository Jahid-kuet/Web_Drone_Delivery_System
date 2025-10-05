@extends('layouts.app')

@section('title', 'My Delivery Requests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Delivery Requests</h1>
            <p class="text-gray-600 mt-1">Manage your medical supply delivery requests</p>
        </div>
        <a href="{{ route('hospital.requests.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>New Request
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" placeholder="Search by request number..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Priority</option>
                    <option value="emergency">Emergency</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div>
                <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Requests List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="text-center py-12">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No delivery requests found</p>
            <a href="{{ route('hospital.requests.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                Create your first request
            </a>
        </div>
    </div>
</div>
@endsection
