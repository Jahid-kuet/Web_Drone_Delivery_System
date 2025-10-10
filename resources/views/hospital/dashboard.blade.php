@extends('layouts.app')

@section('title', 'Hospital Dashboard')

@section('breadcrumb')
    <i class="fas fa-hospital mr-2"></i> Hospital Dashboard
@endsection

@section('content')
<div class="space-y-6">
    {{-- Welcome header --}}
    <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-3xl font-bold mb-2">Welcome, {{ Auth::user()->name }}! 🏥</h1>
        <p class="text-teal-100">{{ $hospital->name }} - Manage your medical supply delivery requests</p>
    </div>

    {{-- Quick stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Requests</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['pending_requests'] }}</h3>
                    <p class="text-xs text-yellow-600 mt-2">
                        <i class="fas fa-clock"></i> Awaiting approval
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Deliveries</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['active_deliveries'] }}</h3>
                    <p class="text-xs text-blue-600 mt-2">
                        <i class="fas fa-shipping-fast"></i> In progress
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-drone text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Completed Today</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['completed_today'] }}</h3>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-check-circle"></i> Successfully delivered
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box-check text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Emergency Requests</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['emergency_requests'] }}</h3>
                    <p class="text-xs text-red-600 mt-2">
                        <i class="fas fa-exclamation-triangle"></i> High priority
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-ambulance text-2xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('hospital.requests.create') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">New Request</h3>
                    <p class="text-sm text-gray-600">Create delivery request</p>
                </div>
            </a>

            <a href="{{ route('hospital.requests.index') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-clipboard-list text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">My Requests</h3>
                    <p class="text-sm text-gray-600">View all requests</p>
                </div>
            </a>

            <a href="{{ route('hospital.deliveries.index') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition group">
                <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-shipping-fast text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Track Deliveries</h3>
                    <p class="text-sm text-gray-600">View active deliveries</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent requests and deliveries side by side --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent requests --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Requests</h2>
                <a href="{{ route('hospital.requests.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                @forelse($recentRequests as $request)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded 
                                    @if($request->priority === 'emergency') bg-red-100 text-red-800
                                    @elseif($request->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($request->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($request->priority) }}
                                </span>
                                <span class="ml-2 text-sm font-medium text-gray-900">
                                    {{ $request->supply_name }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $request->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($request->status === 'approved') bg-green-100 text-green-800
                            @elseif($request->status === 'assigned') bg-blue-100 text-blue-800
                            @elseif($request->status === 'completed') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No recent requests</p>
                        <a href="{{ route('hospital.requests.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                            Create your first request
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Active deliveries --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Active Deliveries</h2>
                <a href="{{ route('hospital.deliveries.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                @forelse($activeDeliveries as $delivery)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <i class="fas fa-drone text-blue-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $delivery->tracking_number }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                ETA: {{ $delivery->estimated_arrival_time ? $delivery->estimated_arrival_time->format('h:i A') : 'Calculating...' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($delivery->status === 'pending') bg-gray-100 text-gray-800
                                @elseif($delivery->status === 'in_transit') bg-blue-100 text-blue-800
                                @elseif($delivery->status === 'delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($delivery->status)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-drone text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No active deliveries</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Low inventory alert --}}
    @if(isset($lowStockSupplies) && $lowStockSupplies->isNotEmpty())
    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-4 mt-1"></i>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-red-900 mb-2">Low Inventory Alert</h3>
                <p class="text-sm text-red-700 mb-3">The following medical supplies are running low:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($lowStockSupplies as $supply)
                        <div class="bg-white rounded p-3 border border-red-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">{{ $supply->name }}</span>
                                <span class="text-sm font-bold text-red-600">{{ $supply->quantity_available }} left</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('hospital.inventory') }}" class="mt-3 inline-block text-sm text-red-700 hover:text-red-900 font-medium">
                    View Inventory <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
