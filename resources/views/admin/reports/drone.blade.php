@extends('layouts.app')

@section('title', 'Drone Performance Report')

@section('breadcrumb')
    <nav class="text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.reports') }}" class="text-blue-600 hover:text-blue-800">Reports</a>
        <span class="mx-2">/</span>
        <span class="text-gray-600">Drone Performance Report</span>
    </nav>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Report Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-drone text-green-600 mr-2"></i>
                    Drone Performance Report
                </h2>
                <p class="text-gray-600 mt-1">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="{{ route('admin.reports') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Report Filters Summary -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-700 mb-3">Report Filters:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Date Range:</span>
                    <span class="font-medium ml-2">
                        {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} - 
                        {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                    </span>
                </div>
                @if(request('drone_id'))
                <div>
                    <span class="text-gray-600">Drone:</span>
                    <span class="font-medium ml-2">{{ $selectedDrone->serial_number ?? 'All Drones' }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Drones Performance Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Drone Performance Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Deliveries</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Battery Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Flight Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Maintenance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($drones as $drone)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-green-600">{{ $drone->serial_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $drone->model }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($drone->status === 'available') bg-green-100 text-green-800
                                @elseif($drone->status === 'in_flight') bg-blue-100 text-blue-800
                                @elseif($drone->status === 'maintenance') bg-red-100 text-red-800
                                @elseif($drone->status === 'charging') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $drone->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-800 font-semibold">
                                {{ $drone->deliveries_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="h-2 rounded-full 
                                        @if($drone->current_battery_level >= 70) bg-green-500
                                        @elseif($drone->current_battery_level >= 30) bg-yellow-500
                                        @else bg-red-500
                                        @endif" 
                                        style="width: {{ $drone->current_battery_level }}%">
                                    </div>
                                </div>
                                <span class="text-sm font-medium">{{ $drone->current_battery_level }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $drone->total_flight_hours ? number_format($drone->total_flight_hours, 1) . ' hrs' : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $drone->last_maintenance_date ? $drone->last_maintenance_date->format('M d, Y') : 'Never' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-drone text-4xl text-gray-300 mb-3"></i>
                            <p>No drones found for the selected criteria</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($drones->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $drones->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-green-50 p-6 rounded-lg">
            <div class="text-green-600 text-sm font-medium">Total Drones</div>
            <div class="text-3xl font-bold text-green-700">{{ $drones->total() }}</div>
        </div>
        <div class="bg-blue-50 p-6 rounded-lg">
            <div class="text-blue-600 text-sm font-medium">In Flight</div>
            <div class="text-3xl font-bold text-blue-700">
                {{ $drones->where('status', 'in_flight')->count() }}
            </div>
        </div>
        <div class="bg-yellow-50 p-6 rounded-lg">
            <div class="text-yellow-600 text-sm font-medium">Charging</div>
            <div class="text-3xl font-bold text-yellow-700">
                {{ $drones->where('status', 'charging')->count() }}
            </div>
        </div>
        <div class="bg-red-50 p-6 rounded-lg">
            <div class="text-red-600 text-sm font-medium">Maintenance</div>
            <div class="text-3xl font-bold text-red-700">
                {{ $drones->where('status', 'maintenance')->count() }}
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
}
</style>
@endsection
