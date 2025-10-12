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
<div class="space-y-4 sm:space-y-6 p-2 sm:p-0">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center">
                <!-- Realistic Drone SVG Icon -->
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <!-- Propellers -->
                    <circle cx="5" cy="5" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                    <circle cx="19" cy="5" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                    <circle cx="5" cy="19" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                    <circle cx="19" cy="19" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                    <!-- Arms -->
                    <line x1="8" y1="8" x2="5" y2="5" stroke-width="2"/>
                    <line x1="16" y1="8" x2="19" y2="5" stroke-width="2"/>
                    <line x1="8" y1="16" x2="5" y2="19" stroke-width="2"/>
                    <line x1="16" y1="16" x2="19" y2="19" stroke-width="2"/>
                    <!-- Central body -->
                    <rect x="9" y="9" width="6" height="6" rx="1.5" fill="currentColor" stroke-width="0"/>
                    <!-- Camera -->
                    <circle cx="12" cy="15" r="1.5" fill="currentColor" opacity="0.7"/>
                    <line x1="12" y1="15" x2="12" y2="17" stroke-width="1.5"/>
                </svg>
                <span class="truncate">Drone Fleet</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Manage your drone delivery fleet</p>
        </div>
        <div class="w-full md:w-auto">
            {{-- INSERT: Create new drone --}}
            <a href="{{ route('admin.drones.create') }}" class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>
                Add New Drone
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
        <form method="GET" action="{{ route('admin.drones.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
            <div class="overflow-x-auto responsive-table">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drone Info</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Model</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Battery</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Condition</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Total Flights</th>
                            <th class="px-3 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($drones as $drone)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0">
                                            <i class="fas fa-drone text-green-600 text-sm sm:text-base"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 text-sm truncate">{{ $drone->name }}</div>
                                            <div class="text-xs text-gray-500 truncate">{{ $drone->serial_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-sm text-gray-900 hidden sm:table-cell">{{ $drone->model }}</td>
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-16 sm:w-24 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="h-2 rounded-full {{ $drone->battery_level >= 75 ? 'bg-green-600' : ($drone->battery_level >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                                 style="width: {{ $drone->battery_level }}%"></div>
                                        </div>
                                        <span class="text-xs sm:text-sm text-gray-900">{{ $drone->battery_level }}%</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap
                                        {{ $drone->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $drone->status === 'in_use' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $drone->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $drone->status === 'charging' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $drone->status === 'retired' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($drone->status) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded whitespace-nowrap">
                                        {{ ucfirst($drone->condition) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-900 hidden lg:table-cell">
                                    {{ number_format($drone->total_flight_time ?? 0) }} hrs
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
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
