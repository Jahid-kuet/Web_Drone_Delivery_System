@extends('layouts.app')

@section('title', 'Deliveries')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Deliveries</h1>
            <p class="text-gray-600 mt-1">Manage and track all drone deliveries</p>
        </div>
        <a href="{{ route('admin.deliveries.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition">
            <i class="fas fa-plus mr-2"></i>
            New Delivery
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.deliveries.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tracking number..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="departed" {{ request('status') == 'departed' ? 'selected' : '' }}>Departed</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Deliveries</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $deliveries->total() }}</h3>
                </div>
                <i class="fas fa-truck text-4xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Completed</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $deliveries->where('status', 'completed')->count() }}</h3>
                </div>
                <i class="fas fa-check-circle text-4xl text-green-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">In Transit</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $deliveries->whereIn('status', ['departed', 'in_transit'])->count() }}</h3>
                </div>
                <i class="fas fa-shipping-fast text-4xl text-yellow-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Failed/Cancelled</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $deliveries->whereIn('status', ['failed', 'cancelled'])->count() }}</h3>
                </div>
                <i class="fas fa-exclamation-triangle text-4xl text-red-200"></i>
            </div>
        </div>
    </div>

    <!-- Deliveries Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hospital</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ETA</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deliveries as $delivery)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-barcode text-gray-400 mr-2"></i>
                                <span class="text-sm font-medium text-gray-900">{{ $delivery->delivery_number ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $delivery->hospital->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $delivery->hospital->city ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $delivery->drone->name ?? 'Unassigned' }}</div>
                            <div class="text-sm text-gray-500">{{ $delivery->drone->model ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ ucwords(str_replace('_', ' ', $delivery->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $delivery->actual_departure_time ? $delivery->actual_departure_time->format('M d, H:i') : ($delivery->scheduled_departure_time ? $delivery->scheduled_departure_time->format('M d, H:i') : 'Not Set') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $delivery->estimated_arrival_time ? $delivery->estimated_arrival_time->format('M d, H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.deliveries.tracking', $delivery) }}" class="text-green-600 hover:text-green-900 mr-3" title="Track">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>
                            @if(in_array($delivery->status, ['scheduled', 'preparing']))
                            <form action="{{ route('admin.deliveries.cancel', $delivery) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this delivery?');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Cancel">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No deliveries found</p>
                            <a href="{{ route('admin.deliveries.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Create your first delivery</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($deliveries->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $deliveries->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
