@extends('layouts.app')

@section('title', 'Delivery Details - ' . ($delivery->delivery_number ?? 'N/A'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-4">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <a href="{{ route('admin.deliveries.index') }}" class="hover:text-blue-600">Deliveries</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-gray-900">{{ $delivery->delivery_number ?? 'N/A' }}</span>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Delivery #{{ $delivery->delivery_number ?? 'N/A' }}</h1>
                <p class="text-gray-600 mt-1">Created {{ $delivery->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex space-x-2">
                @if($delivery->status == 'scheduled')
                <form action="{{ route('admin.deliveries.start', $delivery) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-play mr-2"></i>Start Delivery
                    </button>
                </form>
                @endif
                @if(in_array($delivery->status, ['departed', 'in_transit']))
                <form action="{{ route('admin.deliveries.mark-delivered', $delivery) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-check mr-2"></i>Mark as Delivered
                    </button>
                </form>
                @endif
                <a href="{{ route('admin.deliveries.tracking', $delivery) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-map-marked-alt mr-2"></i>Live Tracking
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Delivery Status</h2>
                @php
                    $statusColors = [
                        'scheduled' => 'bg-gray-100 text-gray-800',
                        'preparing' => 'bg-blue-100 text-blue-800',
                        'departed' => 'bg-purple-100 text-purple-800',
                        'in_transit' => 'bg-yellow-100 text-yellow-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'cancelled' => 'bg-gray-100 text-gray-800',
                    ];
                    $color = $statusColors[$delivery->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-4 py-2 inline-flex text-lg leading-5 font-semibold rounded-full {{ $color }}">
                    {{ ucwords(str_replace('_', ' ', $delivery->status)) }}
                </span>
                
                <!-- Timeline -->
                <div class="mt-6 space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $delivery->created_at ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Delivery Created</p>
                            <p class="text-sm text-gray-500">{{ $delivery->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $delivery->actual_departure_time ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                <i class="fas {{ $delivery->actual_departure_time ? 'fa-check' : 'fa-clock' }} text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Departed</p>
                            <p class="text-sm text-gray-500">{{ $delivery->actual_departure_time ? $delivery->actual_departure_time->format('M d, Y H:i') : 'Not yet departed' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $delivery->actual_arrival_time ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                <i class="fas {{ $delivery->actual_arrival_time ? 'fa-check' : 'fa-clock' }} text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Delivered</p>
                            <p class="text-sm text-gray-500">{{ $delivery->actual_arrival_time ? $delivery->actual_arrival_time->format('M d, Y H:i') : 'Not yet delivered' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $delivery->delivery_completed_time ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                <i class="fas {{ $delivery->delivery_completed_time ? 'fa-check' : 'fa-clock' }} text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Completed</p>
                            <p class="text-sm text-gray-500">{{ $delivery->delivery_completed_time ? $delivery->delivery_completed_time->format('M d, Y H:i') : 'Not yet completed' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Route Information</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Origin</h3>
                        <p class="text-gray-900">Distribution Center</p>
                        <p class="text-sm text-gray-500">{{ $delivery->pickup_coordinates ? json_encode($delivery->pickup_coordinates) : 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Destination</h3>
                        <p class="text-gray-900">{{ $delivery->hospital->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $delivery->hospital->address ?? '' }}, {{ $delivery->hospital->city ?? '' }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Distance</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $delivery->total_distance_km ?? '0' }} km</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Distance Remaining</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $delivery->distance_remaining_km ?? '0' }} km</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ETA</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $delivery->estimated_time_remaining_minutes ?? '0' }} min</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($delivery->special_handling_notes || $delivery->pilot_notes || $delivery->delivery_notes)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes</h2>
                @if($delivery->special_handling_notes)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-1">Special Handling Notes</h3>
                    <p class="text-gray-600">{{ $delivery->special_handling_notes }}</p>
                </div>
                @endif
                @if($delivery->pilot_notes)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-1">Pilot Notes</h3>
                    <p class="text-gray-600">{{ $delivery->pilot_notes }}</p>
                </div>
                @endif
                @if($delivery->delivery_notes)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-1">Delivery Notes</h3>
                    <p class="text-gray-600">{{ $delivery->delivery_notes }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Drone Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Drone Details</h2>
                @if($delivery->drone)
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Drone</p>
                        <p class="text-gray-900 font-medium">{{ $delivery->drone->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Model</p>
                        <p class="text-gray-900">{{ $delivery->drone->model }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Battery Level</p>
                        <p class="text-gray-900">{{ $delivery->fuel_battery_level_current ?? $delivery->drone->battery_level }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-2 py-1 text-xs font-medium rounded {{ $delivery->drone->status == 'available' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($delivery->drone->status) }}
                        </span>
                    </div>
                </div>
                @else
                <p class="text-gray-500">No drone assigned</p>
                @endif
            </div>

            <!-- Hospital Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Hospital Details</h2>
                @if($delivery->hospital)
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="text-gray-900 font-medium">{{ $delivery->hospital->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="text-gray-900">{{ $delivery->hospital->address }}</p>
                        <p class="text-gray-900">{{ $delivery->hospital->city }}, {{ $delivery->hospital->state }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Contact</p>
                        <p class="text-gray-900">{{ $delivery->hospital->phone }}</p>
                        <p class="text-gray-900">{{ $delivery->hospital->email }}</p>
                    </div>
                </div>
                @else
                <p class="text-gray-500">No hospital information</p>
                @endif
            </div>

            <!-- Cargo Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Cargo Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Total Weight</p>
                        <p class="text-gray-900 font-medium">{{ $delivery->total_cargo_weight_kg ?? '0' }} kg</p>
                    </div>
                    @if($delivery->cargo_manifest)
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Manifest</p>
                        <ul class="text-sm text-gray-900 space-y-1">
                            @foreach($delivery->cargo_manifest as $item)
                            <li>• {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if($delivery->requires_return_trip)
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-undo mr-2"></i>Requires Return Trip
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
