@extends('layouts.app')

@section('title', 'View Drone')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.drones.index') }}" class="text-gray-600 hover:text-gray-900">Drones</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Details</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- READ: Display detailed information about drone --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white"><i class="fas fa-drone mr-2"></i>{{ $drone->name }}</h2>
                    <p class="text-green-100 mt-1">READ: Viewing drone details from database</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.drones.edit', $drone) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('admin.drones.destroy', $drone) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Serial Number</label>
                    <p class="text-lg text-gray-900 mt-1">{{ $drone->serial_number }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Model</label>
                    <p class="text-lg text-gray-900 mt-1">{{ $drone->model }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Battery Level</label>
                    <div class="mt-1">
                        <div class="flex items-center">
                            <div class="w-full bg-gray-200 rounded-full h-3 mr-2">
                                <div class="h-3 rounded-full {{ $drone->battery_level >= 75 ? 'bg-green-600' : ($drone->battery_level >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                     style="width: {{ $drone->battery_level }}%"></div>
                            </div>
                            <span class="text-lg font-semibold text-gray-900">{{ $drone->battery_level }}%</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            {{ $drone->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $drone->status === 'in_use' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $drone->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $drone->status === 'charging' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $drone->status === 'retired' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst($drone->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Condition</label>
                    <p class="text-lg text-gray-900 mt-1">{{ ucfirst($drone->condition) }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Max Payload</label>
                    <p class="text-lg text-gray-900 mt-1">{{ $drone->max_payload_kg }} kg</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Max Range</label>
                    <p class="text-lg text-gray-900 mt-1">{{ $drone->max_range_km }} km</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Total Flight Time</label>
                    <p class="text-lg text-gray-900 mt-1">{{ number_format($drone->total_flight_time ?? 0, 2) }} hours</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Last Maintenance</label>
                    <p class="text-lg text-gray-900 mt-1">
                        @if($drone->last_maintenance_date)
                            {{ \Carbon\Carbon::parse($drone->last_maintenance_date)->format('F d, Y') }}
                        @else
                            <span class="text-gray-400">Not recorded</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Current Operator</label>
                    <p class="text-lg text-gray-900 mt-1">
                        @if($drone->operator)
                            {{ $drone->operator->name }}
                        @else
                            <span class="text-gray-400">Unassigned</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($drone->specifications)
                <div class="mt-6">
                    <label class="text-sm font-medium text-gray-500">Specifications</label>
                    <pre class="mt-2 p-4 bg-gray-50 rounded-lg text-sm overflow-x-auto">{{ json_encode(json_decode($drone->specifications), JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif

            <div class="mt-6 pt-6 border-t grid grid-cols-2 gap-4 text-sm text-gray-500">
                <div><i class="fas fa-clock mr-1"></i> Created: {{ $drone->created_at->format('M d, Y h:i A') }}</div>
                <div><i class="fas fa-edit mr-1"></i> Last Updated: {{ $drone->updated_at->format('M d, Y h:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Delivery History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-history mr-2 text-blue-600"></i>Delivery History
        </h3>
        @if($drone->deliveries && $drone->deliveries->count() > 0)
            <div class="space-y-3">
                @foreach($drone->deliveries->take(5) as $delivery)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $delivery->tracking_number }}</p>
                            <p class="text-sm text-gray-600">{{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                            {{ ucfirst($delivery->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No delivery history</p>
        @endif
    </div>

    <div class="flex justify-between">
        <a href="{{ route('admin.drones.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>
</div>
@endsection
