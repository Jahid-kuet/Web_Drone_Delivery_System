@extends('layouts.app')

@section('title', 'Track Delivery')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Track Your Delivery</h1>
            <p class="text-gray-600">Enter your tracking number to see real-time delivery status</p>
        </div>

        <!-- Tracking Form -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8 mb-8">
            <form action="{{ route('tracking.show', ['trackingNumber' => 'TRACK']) }}" method="GET" class="flex gap-4" onsubmit="event.preventDefault(); window.location.href = '{{ route('tracking.public') }}/' + document.getElementById('trackingNumber').value;">
                <input type="text" id="trackingNumber" name="tracking" placeholder="Enter tracking number (e.g., DEL-2024-001)" required 
                       class="flex-1 px-6 py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold transition">
                    <i class="fas fa-search mr-2"></i>Track
                </button>
            </form>
        </div>

        <!-- Info Cards -->
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-helicopter text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-sm">Medical supplies delivered by drone in minutes</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marked-alt text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Real-Time Tracking</h3>
                <p class="text-gray-600 text-sm">Track your delivery live on the map</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure & Safe</h3>
                <p class="text-gray-600 text-sm">Your medical supplies are handled with care</p>
            </div>
        </div>
    </div>
</div>
@endsection
