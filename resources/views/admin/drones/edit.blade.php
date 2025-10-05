@extends('layouts.app')

@section('title', 'Edit Drone')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.drones.index') }}" class="text-gray-600 hover:text-gray-900">Drones</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Edit</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>Edit Drone
            </h2>
            <p class="text-yellow-100 mt-1">UPDATE: Modify existing drone data in database</p>
        </div>

        {{-- UPDATE: Form to edit drone --}}
        <form action="{{ route('admin.drones.update', $drone) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Drone Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $drone->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model <span class="text-red-500">*</span></label>
                    <input type="text" name="model" id="model" value="{{ old('model', $drone->model) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">Serial Number <span class="text-red-500">*</span></label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $drone->serial_number) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('serial_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="battery_level" class="block text-sm font-medium text-gray-700 mb-2">Battery Level (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="battery_level" id="battery_level" value="{{ old('battery_level', $drone->battery_level) }}" required min="0" max="100"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('battery_level')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="max_payload_kg" class="block text-sm font-medium text-gray-700 mb-2">Max Payload (kg) <span class="text-red-500">*</span></label>
                    <input type="number" name="max_payload_kg" id="max_payload_kg" value="{{ old('max_payload_kg', $drone->max_payload_kg) }}" required min="0" step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('max_payload_kg')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="max_range_km" class="block text-sm font-medium text-gray-700 mb-2">Max Range (km) <span class="text-red-500">*</span></label>
                    <input type="number" name="max_range_km" id="max_range_km" value="{{ old('max_range_km', $drone->max_range_km) }}" required min="0" step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('max_range_km')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="available" {{ old('status', $drone->status) === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ old('status', $drone->status) === 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="maintenance" {{ old('status', $drone->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="charging" {{ old('status', $drone->status) === 'charging' ? 'selected' : '' }}>Charging</option>
                        <option value="retired" {{ old('status', $drone->status) === 'retired' ? 'selected' : '' }}>Retired</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">Condition <span class="text-red-500">*</span></label>
                    <select name="condition" id="condition" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="excellent" {{ old('condition', $drone->condition) === 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('condition', $drone->condition) === 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition', $drone->condition) === 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition', $drone->condition) === 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                    @error('condition')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="total_flight_time" class="block text-sm font-medium text-gray-700 mb-2">Total Flight Time (hours)</label>
                    <input type="number" name="total_flight_time" id="total_flight_time" value="{{ old('total_flight_time', $drone->total_flight_time) }}" min="0" step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('total_flight_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-2">Last Maintenance Date</label>
                    <input type="date" name="last_maintenance_date" id="last_maintenance_date" value="{{ old('last_maintenance_date', $drone->last_maintenance_date) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('last_maintenance_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="specifications" class="block text-sm font-medium text-gray-700 mb-2">Specifications (JSON)</label>
                <textarea name="specifications" id="specifications" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent font-mono text-sm">{{ old('specifications', $drone->specifications) }}</textarea>
                @error('specifications')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.drones.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Update Drone
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
