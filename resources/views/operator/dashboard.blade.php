@extends('layouts.app')

@section('title', 'Operator Dashboard')

@section('breadcrumb')
    <i class="fas fa-helicopter mr-2"></i> Operator Dashboard
@endsection

@section('content')
<div class="space-y-6">
    {{-- Welcome header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-3xl font-bold mb-2">Welcome, {{ Auth::user()->name }}! 🚁</h1>
        <p class="text-indigo-100">Drone Operator - Manage your flight assignments and deliveries</p>
    </div>

    {{-- Quick stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Assigned Deliveries</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['assigned_deliveries'] }}</h3>
                    <p class="text-xs text-blue-600 mt-2">
                        <i class="fas fa-tasks"></i> Total assignments
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">In Flight</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['in_flight'] }}</h3>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-helicopter"></i> Active now
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-drone text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Completed Today</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['completed_today'] }}</h3>
                    <p class="text-xs text-purple-600 mt-2">
                        <i class="fas fa-check-circle"></i> Successfully delivered
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box-check text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Flight Hours Today</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['flight_hours'], 1) }}h</h3>
                    <p class="text-xs text-yellow-600 mt-2">
                        <i class="fas fa-clock"></i> Total time
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-stopwatch text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('operator.deliveries.index') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-shipping-fast text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">My Deliveries</h3>
                    <p class="text-sm text-gray-600">View assigned deliveries</p>
                </div>
            </a>

            <a href="#" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-drone text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">My Drones</h3>
                    <p class="text-sm text-gray-600">Manage your drones</p>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-user-circle text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Profile</h3>
                    <p class="text-sm text-gray-600">View your profile</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Today's deliveries and drone status --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Today's delivery schedule --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Today's Deliveries</h2>
                <a href="{{ route('operator.deliveries.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                @forelse($todayDeliveries as $delivery)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $delivery->tracking_number }}
                                </span>
                                <span class="ml-2 px-2 py-1 text-xs font-semibold rounded 
                                    @if($delivery->deliveryRequest->priority === 'emergency') bg-red-100 text-red-800
                                    @elseif($delivery->deliveryRequest->priority === 'high') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($delivery->deliveryRequest->priority ?? 'Normal') }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-hospital mr-1"></i>
                                {{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                ETA: {{ $delivery->estimated_arrival_time ? $delivery->estimated_arrival_time->format('h:i A') : 'Pending' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($delivery->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($delivery->status === 'in_transit') bg-blue-100 text-blue-800
                                @elseif($delivery->status === 'delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($delivery->status)) }}
                            </span>
                            @if($delivery->status === 'pending' || $delivery->status === 'in_transit')
                                <a href="{{ route('operator.deliveries.show', $delivery) }}" class="block text-xs text-blue-600 hover:text-blue-800 mt-1">
                                    Manage <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-check text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No deliveries scheduled for today</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Drone status --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">My Drones</h2>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                @forelse($drones as $drone)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <i class="fas fa-drone text-blue-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $drone->name }}
                                </span>
                            </div>
                            <div class="flex items-center mt-1">
                                <div class="flex-1">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $drone->current_battery_level > 50 ? 'bg-green-500' : ($drone->current_battery_level > 20 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                             style="width: {{ $drone->current_battery_level }}%"></div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 ml-2">{{ $drone->current_battery_level }}%</span>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($drone->status === 'available') bg-green-100 text-green-800
                                @elseif($drone->status === 'in_flight') bg-blue-100 text-blue-800
                                @elseif($drone->status === 'maintenance') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($drone->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-drone text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No drones assigned yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Battery alerts --}}
    @if($lowBatteryDrones->isNotEmpty())
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-battery-quarter text-yellow-600 text-2xl mr-4 mt-1"></i>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-yellow-900 mb-2">Low Battery Alert</h3>
                <p class="text-sm text-yellow-700 mb-3">The following drones need charging:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($lowBatteryDrones as $drone)
                        <div class="bg-white rounded p-3 border border-yellow-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">{{ $drone->name }}</span>
                                <span class="text-sm font-bold text-yellow-600">{{ $drone->current_battery_level }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
