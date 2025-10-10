@extends('layouts.app')

@section('title', 'Delivery Report')

@section('breadcrumb')
    <nav class="text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.reports') }}" class="text-blue-600 hover:text-blue-800">Reports</a>
        <span class="mx-2">/</span>
        <span class="text-gray-600">Delivery Report</span>
    </nav>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Report Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                    Delivery Report
                </h2>
                <p class="text-gray-600 mt-1">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Period:</span>
                    <span class="font-medium ml-2">{{ ucfirst(request('period', 'daily')) }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Date Range:</span>
                    <span class="font-medium ml-2">
                        {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} - 
                        {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                    </span>
                </div>
                @if(request('hospital_id'))
                <div>
                    <span class="text-gray-600">Hospital:</span>
                    <span class="font-medium ml-2">{{ $hospital->name ?? 'All Hospitals' }}</span>
                </div>
                @endif
                @if(request('status'))
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium ml-2">{{ ucwords(str_replace('_', ' ', request('status'))) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-blue-600 text-sm font-medium">Total Deliveries</div>
                <div class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-green-600 text-sm font-medium">Completed</div>
                <div class="text-2xl font-bold text-green-700">{{ $stats['completed'] }}</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-yellow-600 text-sm font-medium">In Transit</div>
                <div class="text-2xl font-bold text-yellow-700">{{ $stats['in_transit'] }}</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <div class="text-red-600 text-sm font-medium">Failed</div>
                <div class="text-2xl font-bold text-red-700">{{ $stats['failed'] }}</div>
            </div>
        </div>

        @if(isset($stats['avg_duration']))
        <div class="bg-purple-50 p-4 rounded-lg mb-6">
            <div class="text-purple-600 text-sm font-medium">Average Delivery Duration</div>
            <div class="text-2xl font-bold text-purple-700">{{ round($stats['avg_duration']) }} minutes</div>
        </div>
        @endif
    </div>

    <!-- Deliveries Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Delivery Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hospital</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deliveries as $delivery)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-blue-600">{{ $delivery->tracking_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($delivery->status === 'completed') bg-green-100 text-green-800
                                @elseif($delivery->status === 'failed') bg-red-100 text-red-800
                                @elseif($delivery->status === 'in_transit') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $delivery->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($delivery->deliveryRequest->priority === 'emergency') bg-red-100 text-red-800
                                @elseif($delivery->deliveryRequest->priority === 'high') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($delivery->deliveryRequest->priority ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $delivery->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $delivery->delivery_completed_time ? $delivery->delivery_completed_time->format('M d, Y H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p>No deliveries found for the selected criteria</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($deliveries->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $deliveries->links() }}
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
}
</style>
@endsection
