@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-bar text-orange-600 mr-2"></i>
            Reports & Analytics
        </h1>
        <p class="text-gray-600">Generate and export comprehensive reports for your drone delivery system</p>
    </div>

    <!-- Report Generation Forms -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Delivery Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shipping-fast text-blue-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Delivery Reports</h3>
            </div>
            <form method="GET" action="{{ route('admin.reports.delivery') }}">
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                        <select name="period" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ now()->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hospital (Optional)</label>
                        <select name="hospital_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Hospitals</option>
                            @foreach($hospitals ?? [] as $hospital)
                                <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-file-alt mr-2"></i>Generate Report
                </button>
            </form>
        </div>

        <!-- Drone Performance Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-drone text-green-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Drone Performance</h3>
            </div>
            <form method="GET" action="{{ route('admin.reports.drone') }}">
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ now()->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Drone (Optional)</label>
                        <select name="drone_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">All Drones</option>
                            @foreach($drones ?? [] as $drone)
                                <option value="{{ $drone->id }}">{{ $drone->name }} ({{ $drone->model }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-alt mr-2"></i>Generate Report
                </button>
            </form>
        </div>

        <!-- Hospital Activity Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-hospital text-purple-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Hospital Activity</h3>
            </div>
            <form method="GET" action="{{ route('admin.reports.hospital') }}">
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ now()->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <button type="submit" class="w-full mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-file-alt mr-2"></i>Generate Report
                </button>
            </form>
        </div>
    </div>

    <!-- Planned Features Grid -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Upcoming Features
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
            <div class="flex items-start">
                <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                <span>PDF Export for all reports</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                <span>Excel Export with charts</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                <span>Scheduled report email delivery</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                <span>Custom report templates</span>
            </div>
        </div>
    </div>

    <!-- Removed rest of old content, keeping only the active report forms above -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" style="display: none;">
        <!-- Old content hidden -->
        <!-- Delivery Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shipping-fast text-blue-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Delivery Reports</h3>
            </div>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Delivery performance metrics</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>On-time delivery rates</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Failed delivery analysis</span>
                </li>
            </ul>
        </div>

        <!-- Drone Performance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-helicopter text-green-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Drone Performance</h3>
            </div>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Flight hours and utilization</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Maintenance history</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Battery performance trends</span>
                </li>
            </ul>
        </div>

        <!-- Medical Supply Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-pills text-purple-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Supply Reports</h3>
            </div>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Stock level analysis</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Expiry tracking</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Supply demand forecasting</span>
                </li>
            </ul>
        </div>

        <!-- Hospital Analytics -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-hospital text-red-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Hospital Analytics</h3>
            </div>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Request patterns</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Service level metrics</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Geographic distribution</span>
                </li>
            </ul>
        </div>

        <!-- Financial Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Financial Reports</h3>
            </div>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Delivery cost analysis</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Revenue reports</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Cost optimization insights</span>
                </li>
            </ul>
        </div>

        <!-- Custom Reports -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-cog text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Custom Reports</h3>
            </div>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Report builder interface</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Scheduled reports</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-600 mt-1 mr-2"></i>
                    <span>Export to PDF/Excel</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Quick Stats Preview -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-chart-line text-orange-600 mr-2"></i>
            Quick Statistics
        </h2>
        <p class="text-gray-600 mb-4">In the meantime, view your dashboard for real-time statistics:</p>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
            <i class="fas fa-tachometer-alt mr-2"></i>
            Go to Dashboard
        </a>
    </div>
</div>
@endsection
