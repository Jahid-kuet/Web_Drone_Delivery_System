@extends('layouts.app')

@section('title', 'Track Delivery - ' . $trackingNumber)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('tracking.public') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tracking
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Tracking: {{ $trackingNumber }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Map Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Live Tracking</h2>
                    <div id="map" class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-map text-6xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">Map will be loaded here</p>
                            <p class="text-gray-400 text-sm">Integration required: Google Maps or Leaflet.js</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Delivery Timeline</h2>
                    <div class="space-y-4" id="timeline">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-500 flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Order Placed</p>
                                <p class="text-sm text-gray-500">Your delivery request was created</p>
                                <p class="text-xs text-gray-400">Loading...</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">In Transit</p>
                                <p class="text-sm text-gray-500">Drone is on the way</p>
                                <p class="text-xs text-gray-400">Pending</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Delivered</p>
                                <p class="text-sm text-gray-500">Package delivered successfully</p>
                                <p class="text-xs text-gray-400">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Status</h2>
                    <div id="status-info">
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                            <p class="text-gray-500">Loading delivery information...</p>
                        </div>
                    </div>
                </div>

                <!-- ETA Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Estimated Arrival</h2>
                    <div id="eta-info" class="text-center">
                        <p class="text-4xl font-bold text-blue-600 mb-2">--:--</p>
                        <p class="text-gray-500">Calculating...</p>
                    </div>
                </div>

                <!-- Contact Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Need Help?</h2>
                    <div class="space-y-3">
                        <a href="tel:+1234567890" class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-phone mr-2"></i>
                            Call Support
                        </a>
                        <a href="mailto:support@dronedeliver.com" class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-envelope mr-2"></i>
                            Email Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fetch delivery tracking data
async function loadTrackingData() {
    try {
        const response = await fetch('/api/tracking/{{ $trackingNumber }}');
        const data = await response.json();
        
        // Update status
        document.getElementById('status-info').innerHTML = `
            <span class="px-4 py-2 inline-flex text-lg font-semibold rounded-full bg-blue-100 text-blue-800">
                ${data.status}
            </span>
            <p class="text-gray-600 mt-4">${data.description || 'Your delivery is being processed'}</p>
        `;
        
        // Update ETA
        if (data.eta) {
            document.getElementById('eta-info').innerHTML = `
                <p class="text-4xl font-bold text-blue-600 mb-2">${data.eta}</p>
                <p class="text-gray-500">minutes remaining</p>
            `;
        }
        
        // Update timeline (implement based on your data structure)
        
    } catch (error) {
        console.error('Error loading tracking data:', error);
        document.getElementById('status-info').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-2"></i>
                <p class="text-red-600">Unable to load tracking information</p>
                <p class="text-gray-500 text-sm mt-1">Please check your tracking number</p>
            </div>
        `;
    }
}

// Load data on page load
document.addEventListener('DOMContentLoaded', loadTrackingData);

// Refresh every 30 seconds
setInterval(loadTrackingData, 30000);
</script>
@endsection
