@extends('layouts.app')

@section('title', 'Track Deliveries')

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
                <span class="text-gray-900">Track Deliveries</span>
            </div>
            
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Track Deliveries</h1>
                <p class="text-gray-600 mt-2">Monitor real-time status of medical supply deliveries</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $deliveries->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ $deliveries->where('status', 'pending')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">In Transit</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $deliveries->where('status', 'in_transit')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Delivered</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $deliveries->where('status', 'delivered')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('hospital.deliveries.index') }}" class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tracking number..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div></div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('hospital.deliveries.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>

        {{-- Deliveries List --}}
        @forelse($deliveries as $delivery)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4 hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $delivery->tracking_number }}</h3>
                            
                            {{-- Status Badge --}}
                            @php
                                $statusConfig = [
                                    'pending' => ['color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock'],
                                    'in_transit' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-shipping-fast'],
                                    'delivered' => ['color' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle'],
                                    'cancelled' => ['color' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle'],
                                ];
                                $config = $statusConfig[$delivery->status] ?? ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question'];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $config['color'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                            </span>
                        </div>

                        {{-- Delivery Info --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-4">
                            @if($delivery->drone)
                            <div class="flex items-center">
                                <i class="fas fa-drone mr-2 text-teal-600"></i>
                                <span>{{ $delivery->drone->name }}</span>
                            </div>
                            @endif
                            @if($delivery->assignedPilot)
                            <div class="flex items-center">
                                <i class="fas fa-user-pilot mr-2 text-teal-600"></i>
                                <span>Pilot: {{ $delivery->assignedPilot->name }}</span>
                            </div>
                            @endif
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-2 text-teal-600"></i>
                                <span>{{ $delivery->scheduled_departure_time ? $delivery->scheduled_departure_time : 'Not scheduled' }}</span>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                {{-- Request Created --}}
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center mb-2">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                    <p class="text-xs font-medium text-gray-900">Requested</p>
                                    <p class="text-xs text-gray-500">{{ $delivery->created_at->format('H:i') }}</p>
                                </div>

                                <div class="flex-1 h-1 {{ $delivery->status != 'pending' ? 'bg-green-500' : 'bg-gray-300' }} -mt-8"></div>

                                {{-- Dispatched --}}
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-10 h-10 rounded-full {{ $delivery->actual_departure_time ? 'bg-green-500' : ($delivery->status == 'in_transit' ? 'bg-blue-500 animate-pulse' : 'bg-gray-300') }} flex items-center justify-center mb-2">
                                        <i class="fas {{ $delivery->actual_departure_time ? 'fa-check' : 'fa-plane-departure' }} text-white"></i>
                                    </div>
                                    <p class="text-xs font-medium text-gray-900">Dispatched</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $delivery->actual_departure_time ? \Carbon\Carbon::parse($delivery->actual_departure_time)->format('H:i') : '--:--' }}
                                    </p>
                                </div>

                                <div class="flex-1 h-1 {{ $delivery->status == 'delivered' ? 'bg-green-500' : 'bg-gray-300' }} -mt-8"></div>

                                {{-- Delivered --}}
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-10 h-10 rounded-full {{ $delivery->status == 'delivered' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                                        <i class="fas fa-box-open text-white"></i>
                                    </div>
                                    <p class="text-xs font-medium text-gray-900">Delivered</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $delivery->actual_arrival_time ? \Carbon\Carbon::parse($delivery->actual_arrival_time)->format('H:i') : '--:--' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t">
                    <a href="#" 
                        class="text-teal-600 hover:text-teal-800 text-sm font-medium transition flex items-center">
                        <i class="fas fa-eye mr-2"></i> View Details
                    </a>
                    @if($delivery->status == 'in_transit')
                    <span class="text-blue-600 text-sm font-medium flex items-center">
                        <i class="fas fa-location-dot mr-2 animate-pulse"></i> Live Tracking
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shipping-fast text-5xl text-teal-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Deliveries Yet</h3>
                <p class="text-gray-600 mb-6">Your medical supply deliveries will appear here once dispatched</p>
                <a href="{{ route('hospital.requests.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-700 hover:to-blue-700 text-white rounded-lg shadow-lg transition">
                    <i class="fas fa-plus-circle mr-2"></i> Request New Delivery
                </a>
            </div>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($deliveries->hasPages())
        <div class="mt-6">
            {{ $deliveries->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
