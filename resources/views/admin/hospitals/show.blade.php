@extends('layouts.app')
@section('title', 'Hospital Details')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-hospital text-purple-600 mr-2"></i>{{ $hospital->name }}
            </h1>
            <p class="text-gray-600 mt-1">Hospital Details and Statistics</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.hospitals.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('admin.hospitals.destroy', $hospital) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this hospital?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </form>
        </div>
    </div>

    {{-- READ: Display hospital information from database --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Details -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-purple-600 mr-2"></i>Basic Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Registration Number</label>
                        <p class="text-lg text-gray-900">{{ $hospital->registration_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Type</label>
                        <p class="text-lg">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $hospital->type === 'government' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $hospital->type === 'private' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $hospital->type === 'specialist' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ ucfirst($hospital->type) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Contact Person</label>
                        <p class="text-lg text-gray-900">{{ $hospital->contact_person }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Phone</label>
                        <p class="text-lg text-gray-900">
                            <i class="fas fa-phone text-purple-600 mr-2"></i>{{ $hospital->phone }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-lg text-gray-900">
                            <i class="fas fa-envelope text-purple-600 mr-2"></i>{{ $hospital->email }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Registered</label>
                        <p class="text-lg text-gray-900">{{ $hospital->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>Location
                </h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Address</label>
                        <p class="text-lg text-gray-900">{{ $hospital->address }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Latitude</label>
                            <p class="text-lg text-gray-900">{{ $hospital->latitude }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Longitude</label>
                            <p class="text-lg text-gray-900">{{ $hospital->longitude }}</p>
                        </div>
                    </div>
                    <!-- Map Placeholder -->
                    <div class="mt-4 bg-gray-100 rounded-lg h-64 flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-map text-4xl mb-2"></i>
                            <p>Map integration coming soon</p>
                            <p class="text-sm mt-1">{{ $hospital->latitude }}, {{ $hospital->longitude }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Statistics -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-purple-600 mr-2"></i>Delivery Statistics
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-box text-2xl text-blue-600 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-900">{{ $hospital->deliveryRequests()->count() }}</p>
                        <p class="text-sm text-gray-600">Total Requests</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-check-circle text-2xl text-green-600 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-900">{{ $hospital->deliveryRequests()->where('status', 'delivered')->count() }}</p>
                        <p class="text-sm text-gray-600">Completed</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <i class="fas fa-clock text-2xl text-yellow-600 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-900">{{ $hospital->deliveryRequests()->where('status', 'pending')->count() }}</p>
                        <p class="text-sm text-gray-600">Pending</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-600 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-900">{{ $hospital->deliveryRequests()->where('urgency_level', 'emergency')->count() }}</p>
                        <p class="text-sm text-gray-600">Emergency</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="#" class="block w-full px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg transition text-center">
                        <i class="fas fa-plus-circle mr-2"></i>New Request
                    </a>
                    <a href="#" class="block w-full px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition text-center">
                        <i class="fas fa-history mr-2"></i>View History
                    </a>
                    <a href="#" class="block w-full px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition text-center">
                        <i class="fas fa-file-export mr-2"></i>Export Report
                    </a>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Requests</h3>
                <div class="space-y-3">
                    @forelse($hospital->deliveryRequests()->latest()->take(5)->get() as $request)
                        <div class="border-l-4 border-purple-500 pl-3 py-2">
                            <p class="text-sm font-medium text-gray-900">{{ $request->medicalSupply->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full mt-1
                                {{ $request->urgency_level === 'emergency' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $request->urgency_level === 'urgent' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $request->urgency_level === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($request->urgency_level) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">No recent requests</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DELETE: Remove hospital from database (form in header) --}}
@endsection
