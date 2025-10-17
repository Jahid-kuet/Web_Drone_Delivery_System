@extends('layouts.app')

@section('title', 'Delivery Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <a href="{{ route('hospital.dashboard') }}" class="hover:text-teal-600 transition">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <a href="{{ route('hospital.deliveries.index') }}" class="hover:text-teal-600 transition">
                    Deliveries
                </a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-gray-900">{{ $delivery->tracking_number }}</span>
            </div>
            
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Delivery Details</h1>
                    <p class="text-gray-600 mt-2">Tracking Number: <span class="font-semibold text-teal-600">{{ $delivery->tracking_number }}</span></p>
                </div>
                <div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $delivery->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $delivery->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $delivery->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Delivery Information --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-teal-600 mr-2"></i>Delivery Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-600">Request Number</label>
                            <p class="font-semibold text-gray-900">#{{ $delivery->deliveryRequest->id }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Priority</label>
                            <p class="font-semibold text-gray-900">{{ ucfirst($delivery->deliveryRequest->priority ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Urgency Level</label>
                            <p class="font-semibold text-gray-900">{{ ucfirst($delivery->deliveryRequest->urgency_level ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Pickup Time</label>
                            <p class="font-semibold text-gray-900">
                                @if($delivery->pickup_time)
                                    {{ \Carbon\Carbon::parse($delivery->pickup_time)->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-gray-400">Not yet picked up</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Delivery Time</label>
                            <p class="font-semibold text-gray-900">
                                @if($delivery->delivery_time)
                                    {{ \Carbon\Carbon::parse($delivery->delivery_time)->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-gray-400">Not yet delivered</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Estimated Delivery</label>
                            <p class="font-semibold text-gray-900">
                                @if($delivery->estimated_delivery_time)
                                    {{ \Carbon\Carbon::parse($delivery->estimated_delivery_time)->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-gray-400">Calculating...</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Medical Supplies --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-boxes text-teal-600 mr-2"></i>Medical Supplies
                    </h2>
                    
                    @if(is_array($delivery->deliveryRequest->medical_supplies) && count($delivery->deliveryRequest->medical_supplies) > 0)
                        <div class="space-y-3">
                            @foreach($delivery->deliveryRequest->medical_supplies as $supply)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $supply['name'] ?? 'Unknown Supply' }}</p>
                                        <p class="text-sm text-gray-600">{{ $supply['category'] ?? '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">{{ $supply['quantity'] ?? 0 }} units</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No medical supplies information available.</p>
                    @endif
                </div>

                {{-- Delivery Locations --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-map-marker-alt text-teal-600 mr-2"></i>Locations
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <label class="text-sm text-blue-800 font-semibold">Pickup Location</label>
                            @if(is_array($delivery->deliveryRequest->pickup_location))
                                <p class="text-gray-900 mt-1">{{ $delivery->deliveryRequest->pickup_location['address'] ?? 'N/A' }}</p>
                            @else
                                <p class="text-gray-900 mt-1">{{ $delivery->deliveryRequest->pickup_location ?? 'N/A' }}</p>
                            @endif
                        </div>
                        
                        <div class="p-4 bg-green-50 rounded-lg">
                            <label class="text-sm text-green-800 font-semibold">Delivery Location</label>
                            @if(is_array($delivery->deliveryRequest->delivery_location))
                                <p class="text-gray-900 mt-1">{{ $delivery->deliveryRequest->delivery_location['address'] ?? 'N/A' }}</p>
                            @else
                                <p class="text-gray-900 mt-1">{{ $delivery->deliveryRequest->delivery_location ?? 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Special Instructions --}}
                @if($delivery->deliveryRequest->special_instructions)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>Special Instructions
                    </h2>
                    <p class="text-gray-700">{{ $delivery->deliveryRequest->special_instructions }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Drone Details --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-drone text-teal-600 mr-2"></i>Drone Details
                    </h2>
                    
                    @if($delivery->drone)
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">Model</label>
                                <p class="font-semibold text-gray-900">{{ $delivery->drone->model }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Registration</label>
                                <p class="font-semibold text-gray-900">{{ $delivery->drone->registration_number }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Status</label>
                                <p class="font-semibold text-gray-900">{{ ucfirst($delivery->drone->status) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No drone assigned yet.</p>
                    @endif
                </div>

                {{-- Pilot Details --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-user-pilot text-teal-600 mr-2"></i>Pilot Details
                    </h2>
                    
                    @if($delivery->assignedPilot)
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-600">Name</label>
                                <p class="font-semibold text-gray-900">{{ $delivery->assignedPilot->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Contact</label>
                                <p class="font-semibold text-gray-900">{{ $delivery->assignedPilot->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No pilot assigned yet.</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Actions</h2>
                    
                    <div class="space-y-2">
                        <a href="{{ route('hospital.deliveries.index') }}" class="block w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-center rounded-lg transition">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
