@extends('layouts.app')

@section('title', 'Drones')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Drones</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-drone mr-2 text-green-600"></i>Drone Fleet
            </h1>
            <p class="text-gray-600 mt-1">Manage your drone delivery fleet</p>
        </div>
        <div class="mt-4 md:mt-0">
            {{-- INSERT: Create new drone --}}
            <a href="{{ route('admin.drones.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>
                Add New Drone
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.drones.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- READ: Filter and search drones --}}
            <div>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search drones..." 
                    value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>In Use</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="charging" {{ request('status') === 'charging' ? 'selected' : '' }}>Charging</option>
                    <option value="retired" {{ request('status') === 'retired' ? 'selected' : '' }}>Retired</option>
                </select>
            </div>
            <div>
                <select name="condition" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">All Conditions</option>
                    <option value="excellent" {{ request('condition') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                    <option value="good" {{ request('condition') === 'good' ? 'selected' : '' }}>Good</option>
                    <option value="fair" {{ request('condition') === 'fair' ? 'selected' : '' }}>Fair</option>
                    <option value="poor" {{ request('condition') === 'poor' ? 'selected' : '' }}>Poor</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.drones.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-center transition">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- READ: Display all drones from database --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($drones->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drone Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Battery</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Flights</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($drones as $drone)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-drone text-green-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $drone->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $drone->serial_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $drone->model }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2 mr-2" style="width: 100px;">
                                            <div class="h-2 rounded-full {{ $drone->battery_level >= 75 ? 'bg-green-600' : ($drone->battery_level >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                                 style="width: {{ $drone->battery_level }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $drone->battery_level }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $drone->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $drone->status === 'in_use' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $drone->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $drone->status === 'charging' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $drone->status === 'retired' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($drone->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                        {{ ucfirst($drone->condition) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ number_format($drone->total_flight_time ?? 0) }} hrs
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                    {{-- READ: View drone details --}}
                                    <a href="{{ route('admin.drones.show', $drone) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- UPDATE: Edit drone --}}
                                    <a href="{{ route('admin.drones.edit', $drone) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- DELETE: Remove drone --}}
                                    <form action="{{ route('admin.drones.destroy', $drone) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this drone?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $drones->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-drone text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">No drones found</p>
                <a href="{{ route('admin.drones.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Drone
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
