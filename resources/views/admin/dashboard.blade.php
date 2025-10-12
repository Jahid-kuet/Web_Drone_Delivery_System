@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <i class="fas fa-home mr-2"></i> Dashboard
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-blue-100">Here's what's happening with your drone delivery system today.</p>
    </div>

    {{-- READ: Statistics cards showing key metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Deliveries -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Deliveries</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_deliveries'] ?? 0 }}</h3>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-arrow-up"></i> +12% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Active Drones -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Drones</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['active_drones'] ?? 0 }}</h3>
                    <p class="text-xs text-gray-600 mt-2">
                        <i class="fas fa-drone"></i> {{ $stats['total_drones'] ?? 0 }} total drones
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-drone text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Requests</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['pending_requests'] ?? 0 }}</h3>
                    <p class="text-xs text-yellow-600 mt-2">
                        <i class="fas fa-clock"></i> Awaiting assignment
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Hospitals -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Registered Hospitals</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_hospitals'] ?? 0 }}</h3>
                    <p class="text-xs text-purple-600 mt-2">
                        <i class="fas fa-hospital"></i> Active network
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hospital text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- READ: Recent delivery requests --}}
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Recent Delivery Requests
                </h3>
            </div>
            <div class="p-6">
                @if(isset($recent_requests) && $recent_requests->count() > 0)
                    <div class="space-y-4">
                        @foreach($recent_requests as $request)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $request->hospital->name ?? 'N/A' }}</h4>
                                    <p class="text-sm text-gray-600">{{ $request->supply->name ?? 'N/A' }} - {{ $request->quantity }} units</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-clock"></i> {{ $request->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>No recent delivery requests</p>
                    </div>
                @endif
                <a href="{{ route('admin.delivery-requests.index') }}" class="block mt-4 text-center text-blue-600 hover:text-blue-800 font-medium">
                    View All Requests <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- READ: Active deliveries status --}}
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-shipping-fast mr-2 text-green-600"></i>Active Deliveries
                </h3>
            </div>
            <div class="p-6">
                @if(isset($active_deliveries) && $active_deliveries->count() > 0)
                    <div class="space-y-4">
                        @foreach($active_deliveries as $delivery)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">Tracking: {{ $delivery->tracking_number }}</h4>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-drone"></i> {{ $delivery->drone->model ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-map-marker-alt"></i> {{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        {{ $delivery->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ str_replace('_', ' ', ucfirst($delivery->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-box text-4xl mb-3"></i>
                        <p>No active deliveries</p>
                    </div>
                @endif
                <a href="{{ route('admin.deliveries.index') }}" class="block mt-4 text-center text-blue-600 hover:text-blue-800 font-medium">
                    View All Deliveries <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- READ: Drone fleet status overview --}}
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-drone mr-2 text-purple-600"></i>Drone Fleet Status
            </h3>
        </div>
        <div class="p-6">
            @if(isset($drone_status) && count($drone_status) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($drone_status as $status => $count)
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold mb-1
                                {{ $status === 'available' ? 'text-green-600' : '' }}
                                {{ $status === 'in_use' ? 'text-blue-600' : '' }}
                                {{ $status === 'maintenance' ? 'text-yellow-600' : '' }}
                                {{ $status === 'charging' ? 'text-orange-600' : '' }}">
                                {{ $count }}
                            </div>
                            <div class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-drone text-4xl mb-3"></i>
                    <p>No drone data available</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Analytics Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Deliveries by Status (Pie Chart) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie mr-2 text-blue-600"></i>Deliveries by Status
            </h3>
            <canvas id="statusChart" class="w-full" style="max-height: 300px;"></canvas>
        </div>

        {{-- Delivery Success Rate (Doughnut Chart) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-check-circle mr-2 text-green-600"></i>Success Rate
            </h3>
            <canvas id="successRateChart" class="w-full" style="max-height: 300px;"></canvas>
            <div class="text-center mt-4">
                <p class="text-3xl font-bold text-green-600">
                    @php
                        $total = ($stats['completed_deliveries'] ?? 0) + ($stats['failed_deliveries'] ?? 0);
                        $rate = $total > 0 ? round((($stats['completed_deliveries'] ?? 0) / $total) * 100, 1) : 0;
                    @endphp
                    {{ $rate }}%
                </p>
                <p class="text-sm text-gray-600">Overall Success Rate</p>
            </div>
        </div>
    </div>

    {{-- Delivery Trends (Line Chart) --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-line mr-2 text-indigo-600"></i>Delivery Trends (Last 30 Days)
        </h3>
        <canvas id="trendsChart" class="w-full" style="max-height: 300px;"></canvas>
    </div>

    {{-- Hospital Usage & Drone Utilization --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Hospital Usage (Bar Chart) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-hospital mr-2 text-purple-600"></i>Hospital Usage
            </h3>
            <canvas id="hospitalUsageChart" class="w-full" style="max-height: 250px;"></canvas>
        </div>

        {{-- Drone Utilization (Bar Chart) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-drone mr-2 text-teal-600"></i>Drone Utilization
            </h3>
            <canvas id="droneUtilizationChart" class="w-full" style="max-height: 250px;"></canvas>
        </div>
    </div>

    {{-- Quick Actions Section --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.delivery-requests.create') }}" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <i class="fas fa-plus-circle text-3xl text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">New Request</span>
            </a>
            <a href="{{ route('admin.drones.create') }}" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <i class="fas fa-drone text-3xl text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Add Drone</span>
            </a>
            <a href="{{ route('admin.hospitals.create') }}" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <i class="fas fa-hospital text-3xl text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Add Hospital</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="flex flex-col items-center justify-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <i class="fas fa-chart-bar text-3xl text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">View Reports</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Status Distribution Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: @json($charts['status_distribution']['labels'] ?? []),
            datasets: [{
                data: @json($charts['status_distribution']['data'] ?? []),
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Success Rate Doughnut Chart
    const successCtx = document.getElementById('successRateChart').getContext('2d');
    const completed = {{ $stats['completed_deliveries'] ?? 0 }};
    const failed = {{ $stats['failed_deliveries'] ?? 0 }};
    new Chart(successCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Failed'],
            datasets: [{
                data: [completed, failed],
                backgroundColor: ['#10B981', '#EF4444'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Delivery Trends Line Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: @json($charts['deliveries_chart']['labels'] ?? []),
            datasets: [
                {
                    label: 'Total Deliveries',
                    data: @json($charts['deliveries_chart']['datasets'][0]['data'] ?? []),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Completed',
                    data: @json($charts['deliveries_chart']['datasets'][1]['data'] ?? []),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Failed',
                    data: @json($charts['deliveries_chart']['datasets'][2]['data'] ?? []),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Hospital Usage Bar Chart
    const hospitalCtx = document.getElementById('hospitalUsageChart').getContext('2d');
    new Chart(hospitalCtx, {
        type: 'bar',
        data: {
            labels: @json($charts['hospital_usage']['labels'] ?? []),
            datasets: [{
                label: 'Deliveries',
                data: @json($charts['hospital_usage']['data'] ?? []),
                backgroundColor: '#8B5CF6',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Drone Utilization Bar Chart
    const droneCtx = document.getElementById('droneUtilizationChart').getContext('2d');
    new Chart(droneCtx, {
        type: 'bar',
        data: {
            labels: @json($charts['drone_utilization']['labels'] ?? []),
            datasets: [{
                label: 'Utilization %',
                data: @json($charts['drone_utilization']['data'] ?? []),
                backgroundColor: '#14B8A6',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endpush
