@extends('layouts.app')

@section('title', 'My Drones')

@section('breadcrumb')
    <i class="fas fa-drone mr-2"></i> My Drones
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Drones</h1>
                <p class="text-gray-600 mt-1">Drones currently assigned to you</p>
            </div>
            
            <div class="flex gap-2">
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg font-medium">
                    <i class="fas fa-check-circle mr-2"></i>{{ $stats['available'] }} Available
                </span>
                <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-medium">
                    <i class="fas fa-plane-departure mr-2"></i>{{ $stats['in_flight'] }} In Flight
                </span>
            </div>
        </div>
    </div>

    {{-- Drones grid --}}
    @forelse($drones as $drone)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-all duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    {{-- Left: Drone info --}}
                    <div class="flex-1">
                        <div class="flex items-start gap-4">
                            {{-- Drone icon --}}
                            <div class="flex-shrink-0">
                                @if($drone->status === 'available')
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-drone text-3xl text-green-600"></i>
                                    </div>
                                @elseif($drone->status === 'in_flight')
                                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center animate-pulse">
                                        <i class="fas fa-drone text-3xl text-blue-600"></i>
                                    </div>
                                @elseif($drone->status === 'charging')
                                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-battery-half text-3xl text-yellow-600"></i>
                                    </div>
                                @elseif($drone->status === 'maintenance')
                                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-wrench text-3xl text-red-600"></i>
                                    </div>
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-drone text-3xl text-gray-600"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Drone details --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $drone->name }}</h3>
                                    
                                    {{-- Status badge --}}
                                    @if($drone->status === 'available')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Available
                                        </span>
                                    @elseif($drone->status === 'in_flight')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-plane-departure mr-1"></i>In Flight
                                        </span>
                                    @elseif($drone->status === 'charging')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-battery-half mr-1"></i>Charging
                                        </span>
                                    @elseif($drone->status === 'maintenance')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-wrench mr-1"></i>Maintenance
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($drone->status) }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-600 mb-2">
                                    <i class="fas fa-tag text-indigo-600 mr-2"></i>
                                    <span class="font-medium">Model:</span> {{ $drone->model }}
                                </p>

                                <p class="text-sm text-gray-600 mb-2">
                                    <i class="fas fa-barcode text-indigo-600 mr-2"></i>
                                    <span class="font-medium">Serial:</span> {{ $drone->serial_number }}
                                </p>

                                {{-- Battery Level --}}
                                <div class="mb-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fas fa-battery-full text-indigo-600"></i>
                                        <span class="text-sm font-medium text-gray-700">Battery Level</span>
                                        <span class="text-sm font-bold {{ $drone->current_battery_level > 50 ? 'text-green-600' : ($drone->current_battery_level > 20 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $drone->current_battery_level }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full {{ $drone->current_battery_level > 50 ? 'bg-green-500' : ($drone->current_battery_level > 20 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                             style="width: {{ $drone->current_battery_level }}%"></div>
                                    </div>
                                </div>

                                {{-- Specs --}}
                                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-weight-hanging text-gray-400 mr-1"></i>
                                        Max Payload: <span class="font-medium text-gray-900">{{ $drone->max_payload_kg }} kg</span>
                                    </span>
                                    <span class="text-gray-600">
                                        <i class="fas fa-route text-gray-400 mr-1"></i>
                                        Range: <span class="font-medium text-gray-900">{{ $drone->max_range_km }} km</span>
                                    </span>
                                    <span class="text-gray-600">
                                        <i class="fas fa-tachometer-alt text-gray-400 mr-1"></i>
                                        Speed: <span class="font-medium text-gray-900">{{ $drone->max_speed_kmh }} km/h</span>
                                    </span>
                                </div>

                                {{-- Current assignment --}}
                                @php
                                    $currentDelivery = $drone->assignments()
                                        ->whereHas('delivery', function($q) {
                                            $q->whereIn('status', ['pending', 'in_transit']);
                                        })
                                        ->with('delivery.deliveryRequest.hospital')
                                        ->first();
                                @endphp

                                @if($currentDelivery && $currentDelivery->delivery)
                                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <p class="text-sm font-semibold text-blue-900 mb-1">
                                            <i class="fas fa-shipping-fast mr-1"></i>Current Assignment
                                        </p>
                                        <p class="text-sm text-blue-800">
                                            {{ $currentDelivery->delivery->delivery_number }} - 
                                            {{ $currentDelivery->delivery->deliveryRequest->hospital->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right: Stats --}}
                    <div class="lg:w-64 space-y-3">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Total Deliveries</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $drone->total_deliveries ?? 0 }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Flight Hours</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $drone->total_flight_hours ?? 0 }}h</p>
                        </div>
                        @if($drone->last_maintenance_date)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Last Maintenance</p>
                            <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($drone->last_maintenance_date)->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-drone text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Drones Assigned</h3>
            <p class="text-gray-600">You don't have any drones currently assigned to you.</p>
        </div>
    @endforelse
</div>
@endsection
