@extends('layouts.app')

@section('title', 'Delivery History')

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
                <span class="text-gray-900">Delivery History</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Delivery History</h1>
                    <p class="text-gray-600 mt-2">Complete history of all deliveries to your hospital</p>
                </div>
                <a href="{{ route('hospital.requests.create') }}" 
                    class="px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-700 hover:to-blue-700 text-white rounded-lg shadow-lg transition flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i> New Request
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Deliveries</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Delivered</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['delivered'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $stats['total'] > 0 ? round(($stats['delivered'] / $stats['total']) * 100, 1) : 0 }}% success rate
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">In Transit</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['in_transit'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Cancelled</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Advanced Filters --}}
        <form method="GET" action="{{ route('hospital.history') }}" class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                        <option value="">All Priority</option>
                        <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition flex items-center">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="{{ route('hospital.history') }}"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition flex items-center">
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
                <button type="button" onclick="window.print()"
                    class="ml-auto px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center">
                    <i class="fas fa-print mr-2"></i> Print Report
                </button>
            </div>
        </form>

        {{-- Deliveries Table --}}
        @forelse($deliveries as $delivery)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4 hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
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

                            {{-- Priority Badge --}}
                            @if($delivery->deliveryRequest)
                            @php
                                $priorityColors = [
                                    'emergency' => 'bg-red-500 text-white',
                                    'high' => 'bg-orange-500 text-white',
                                    'medium' => 'bg-yellow-500 text-white',
                                    'low' => 'bg-green-500 text-white',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $priorityColors[$delivery->deliveryRequest->priority] ?? 'bg-gray-500 text-white' }}">
                                {{ ucfirst($delivery->deliveryRequest->priority) }} Priority
                            </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-gray-600 mb-4">
                            @if($delivery->drone)
                            <div class="flex items-center">
                                <i class="fas fa-drone mr-2 text-teal-600"></i>
                                <span>{{ $delivery->drone->name }}</span>
                            </div>
                            @endif
                            @if($delivery->assignedPilot)
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2 text-teal-600"></i>
                                <span>{{ $delivery->assignedPilot->name }}</span>
                            </div>
                            @endif
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-2 text-teal-600"></i>
                                <span>{{ $delivery->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2 text-teal-600"></i>
                                <span>{{ $delivery->created_at->format('h:i A') }}</span>
                            </div>
                        </div>

                        {{-- Delivery Timeline --}}
                        @if($delivery->status == 'delivered')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center text-green-700">
                                    <i class="fas fa-check-double mr-2"></i>
                                    <span class="font-medium">Delivered Successfully</span>
                                </div>
                                @if($delivery->actual_arrival_time)
                                <span class="text-green-600">
                                    {{ \Carbon\Carbon::parse($delivery->actual_arrival_time)->format('M d, Y h:i A') }}
                                </span>
                                @endif
                            </div>
                            @if($delivery->actual_departure_time && $delivery->actual_arrival_time)
                            @php
                                $departureTime = \Carbon\Carbon::parse($delivery->actual_departure_time);
                                $arrivalTime = \Carbon\Carbon::parse($delivery->actual_arrival_time);
                                $duration = $departureTime->diffInMinutes($arrivalTime);
                            @endphp
                            <div class="mt-2 text-xs text-green-600">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                Delivery time: {{ $duration }} minutes
                            </div>
                            @endif
                        </div>
                        @elseif($delivery->status == 'cancelled')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center text-red-700">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span class="font-medium">Delivery Cancelled</span>
                            </div>
                            @if($delivery->cancellation_reason)
                            <p class="mt-2 text-sm text-red-600">{{ $delivery->cancellation_reason }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t">
                    <a href="#" 
                        class="text-teal-600 hover:text-teal-800 text-sm font-medium transition flex items-center">
                        <i class="fas fa-eye mr-2"></i> View Full Details
                    </a>
                    @if($delivery->status == 'delivered')
                    <button onclick="window.print()"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium transition flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i> Generate Receipt
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-history text-5xl text-teal-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Delivery History</h3>
                <p class="text-gray-600 mb-6">Start by requesting a delivery to see your history here</p>
                <a href="{{ route('hospital.requests.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-700 hover:to-blue-700 text-white rounded-lg shadow-lg transition">
                    <i class="fas fa-plus-circle mr-2"></i> Create First Request
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
