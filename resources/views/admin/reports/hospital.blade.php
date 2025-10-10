@extends('layouts.app')

@section('title', 'Hospital Activity Report')

@section('breadcrumb')
    <nav class="text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.reports') }}" class="text-blue-600 hover:text-blue-800">Reports</a>
        <span class="mx-2">/</span>
        <span class="text-gray-600">Hospital Activity Report</span>
    </nav>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Report Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-hospital text-purple-600 mr-2"></i>
                    Hospital Activity Report
                </h2>
                <p class="text-gray-600 mt-1">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
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
            </div>
        </div>
    </div>

    <!-- Hospital Activity Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Hospital Activity Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hospital Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Requests</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Active</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hospitals as $hospital)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-hospital text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $hospital->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $hospital->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm text-gray-600">{{ $hospital->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucwords(str_replace('_', ' ', $hospital->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-800 font-bold text-lg">
                                {{ $hospital->delivery_requests_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $hospital->city }}, {{ $hospital->state_province }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($hospital->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $hospital->updated_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-hospital text-4xl text-gray-300 mb-3"></i>
                            <p>No hospitals found for the selected criteria</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($hospitals->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $hospitals->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-purple-50 p-6 rounded-lg">
            <div class="text-purple-600 text-sm font-medium">Total Hospitals</div>
            <div class="text-3xl font-bold text-purple-700">{{ $hospitals->total() }}</div>
        </div>
        <div class="bg-green-50 p-6 rounded-lg">
            <div class="text-green-600 text-sm font-medium">Active Hospitals</div>
            <div class="text-3xl font-bold text-green-700">
                {{ $hospitals->where('is_active', true)->count() }}
            </div>
        </div>
        <div class="bg-blue-50 p-6 rounded-lg">
            <div class="text-blue-600 text-sm font-medium">Total Requests</div>
            <div class="text-3xl font-bold text-blue-700">
                {{ $hospitals->sum('delivery_requests_count') }}
            </div>
        </div>
        <div class="bg-orange-50 p-6 rounded-lg">
            <div class="text-orange-600 text-sm font-medium">Avg Requests/Hospital</div>
            <div class="text-3xl font-bold text-orange-700">
                {{ $hospitals->count() > 0 ? round($hospitals->sum('delivery_requests_count') / $hospitals->count(), 1) : 0 }}
            </div>
        </div>
    </div>

    <!-- Top Performing Hospitals -->
    @if($hospitals->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-trophy text-yellow-500 mr-2"></i>
            Top 5 Most Active Hospitals
        </h3>
        <div class="space-y-3">
            @foreach($hospitals->sortByDesc('delivery_requests_count')->take(5) as $index => $hospital)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full 
                        @if($index === 0) bg-yellow-500
                        @elseif($index === 1) bg-gray-400
                        @elseif($index === 2) bg-orange-600
                        @else bg-gray-300
                        @endif
                        text-white flex items-center justify-center font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900">{{ $hospital->name }}</div>
                        <div class="text-sm text-gray-500">{{ $hospital->city }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-purple-600">{{ $hospital->delivery_requests_count }}</div>
                    <div class="text-xs text-gray-500">requests</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
}
</style>
@endsection
