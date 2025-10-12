@extends('layouts.public')
@section('title', 'Track Your Delivery')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-map-marker-alt text-5xl text-blue-600"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Track Your Delivery</h1>
            <p class="text-gray-600">Enter your tracking number to see real-time delivery status</p>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-xl p-6 mb-8">
            <form action="{{ route('tracking.search') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                {{-- READ: Search for delivery by tracking number --}}
                <input 
                    type="text" 
                    name="tracking_number" 
                    placeholder="Enter tracking number (e.g., TRK-20231015-0001)" 
                    value="{{ request('tracking_number') }}"
                    required
                    class="flex-1 px-6 py-4 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500 focus:outline-none text-lg"
                >
                <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition shadow-lg whitespace-nowrap">
                    <i class="fas fa-search mr-2"></i>Track
                </button>
            </form>
            
            @if(session('error'))
                <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
        </div>

        @if(isset($delivery))
            {{-- READ: Display delivery tracking information --}}
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h2 class="text-2xl font-bold text-white">
                            <i class="fas fa-shipping-fast mr-2"></i>Delivery Status
                        </h2>
                        <p class="text-blue-100 mt-1">{{ $delivery->tracking_number }}</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Current Status -->
                        <div class="text-center py-4">
                            <span class="inline-block px-6 py-3 text-xl font-bold rounded-full
                                {{ $delivery->status === 'preparing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $delivery->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $delivery->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $delivery->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                <i class="fas 
                                    {{ $delivery->status === 'preparing' ? 'fa-box' : '' }}
                                    {{ $delivery->status === 'in_transit' ? 'fa-plane' : '' }}
                                    {{ $delivery->status === 'delivered' ? 'fa-check-circle' : '' }}
                                    {{ $delivery->status === 'cancelled' ? 'fa-times-circle' : '' }}
                                    {{ $delivery->status === 'failed' ? 'fa-exclamation-circle' : '' }}
                                    mr-2"></i>
                                {{ str_replace('_', ' ', ucwords($delivery->status)) }}
                            </span>
                        </div>

                        <!-- Timeline -->
                        <div class="relative">
                            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            
                            <div class="space-y-6">
                                <!-- Preparing -->
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0 
                                        {{ in_array($delivery->status, ['preparing', 'in_transit', 'delivered']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                        <i class="fas fa-box text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Preparing</h3>
                                        <p class="text-sm text-gray-600">Package is being prepared for delivery</p>
                                        @if($delivery->created_at)
                                            <p class="text-xs text-gray-500 mt-1">{{ $delivery->created_at->format('M d, Y h:i A') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- In Transit -->
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0
                                        {{ in_array($delivery->status, ['in_transit', 'delivered']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                        <i class="fas fa-drone text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">In Transit</h3>
                                        <p class="text-sm text-gray-600">Drone is on the way to destination</p>
                                        @if($delivery->pickup_time)
                                            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($delivery->pickup_time)->format('M d, Y h:i A') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Delivered -->
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0
                                        {{ $delivery->status === 'delivered' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Delivered</h3>
                                        <p class="text-sm text-gray-600">Package has been delivered successfully</p>
                                        @if($delivery->delivery_time)
                                            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($delivery->delivery_time)->format('M d, Y h:i A') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Destination</label>
                                <p class="text-lg text-gray-900 mt-1">{{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Drone</label>
                                <p class="text-lg text-gray-900 mt-1">{{ $delivery->drone->name ?? 'Not Assigned' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Estimated Delivery</label>
                                <p class="text-lg text-gray-900 mt-1">
                                    @if($delivery->estimated_delivery_time)
                                        {{ \Carbon\Carbon::parse($delivery->estimated_delivery_time)->format('M d, Y h:i A') }}
                                    @else
                                        Calculating...
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Distance</label>
                                <p class="text-lg text-gray-900 mt-1">{{ number_format($delivery->distance_km ?? 0, 2) }} km</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Placeholder -->
                <div class="bg-white rounded-lg shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-map-marked-alt mr-2 text-blue-600"></i>Live Location
                    </h3>
                    <div class="bg-gray-100 rounded-lg flex items-center justify-center h-64">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-map text-4xl mb-3"></i>
                            <p>Map integration coming soon</p>
                            <p class="text-sm mt-1">Current location: {{ $delivery->current_location ?? 'Updating...' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request('tracking_number'))
            <!-- No Results -->
            <div class="bg-white rounded-lg shadow-xl p-12 text-center">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No Delivery Found</h3>
                <p class="text-gray-600 mb-6">The tracking number "{{ request('tracking_number') }}" was not found in our system.</p>
                <p class="text-sm text-gray-500">Please check the tracking number and try again.</p>
            </div>
        @endif

        <!-- Help Section -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-question-circle mr-2 text-blue-600"></i>Need Help?
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                    <i class="fas fa-phone mr-2 text-blue-600"></i>
                    <span class="font-medium">Call:</span> +1 (555) 123-4567
                </div>
                <div>
                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                    <span class="font-medium">Email:</span> support@dronedelivery.com
                </div>
                <div>
                    <i class="fas fa-clock mr-2 text-blue-600"></i>
                    <span class="font-medium">Hours:</span> 24/7 Support
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
