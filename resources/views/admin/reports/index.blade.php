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
        <p class="text-gray-600">Generate and view comprehensive reports for your drone delivery system</p>
    </div>

    <!-- Coming Soon Notice -->
    <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg p-8 text-center mb-8">
        <i class="fas fa-construction text-orange-600 text-5xl mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Reports Module Coming Soon!</h2>
        <p class="text-gray-600 mb-4">We're working on comprehensive reporting features for your drone delivery system.</p>
        <div class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-800 rounded-lg">
            <i class="fas fa-clock mr-2"></i>
            <span class="font-medium">Under Development</span>
        </div>
    </div>

    <!-- Planned Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
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
