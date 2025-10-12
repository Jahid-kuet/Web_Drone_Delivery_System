@extends('layouts.app')

@section('title', 'Add Drone')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.drones.index') }}" class="text-gray-600 hover:text-gray-900">Drones</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Add New</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-drone mr-2"></i>Add New Drone
            </h2>
            <p class="text-green-100 mt-1">INSERT: Create a new drone record in database</p>
        </div>

        {{-- INSERT: Form to create new drone --}}
        <form action="{{ route('admin.drones.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Drone Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Drone Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="e.g., Falcon-001"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Model -->
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                        Model <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="model" 
                        id="model" 
                        value="{{ old('model') }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="e.g., DJI Matrice 300"
                    >
                    @error('model')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Serial Number -->
                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Serial Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="serial_number" 
                        id="serial_number" 
                        value="{{ old('serial_number') }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="e.g., SN-20231001-001"
                    >
                    @error('serial_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Battery Level -->
                <div>
                    <label for="battery_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Battery Level (%) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="battery_level" 
                        id="battery_level" 
                        value="{{ old('battery_level', 100) }}" 
                        required
                        min="0"
                        max="100"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    @error('battery_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Payload -->
                <div>
                    <label for="max_payload_kg" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Payload (kg) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="max_payload_kg" 
                        id="max_payload_kg" 
                        value="{{ old('max_payload_kg') }}" 
                        required
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="5.0"
                    >
                    @error('max_payload_kg')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Range -->
                <div>
                    <label for="max_range_km" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Range (km) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="max_range_km" 
                        id="max_range_km" 
                        value="{{ old('max_range_km') }}" 
                        required
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="30.0"
                    >
                    @error('max_range_km')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="status" 
                        id="status" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="assigned" {{ old('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_flight" {{ old('status') === 'in_flight' ? 'selected' : '' }}>In Flight</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="charging" {{ old('status') === 'charging' ? 'selected' : '' }}>Charging</option>
                        <option value="offline" {{ old('status') === 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="emergency" {{ old('status') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Condition -->
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                        Condition <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="condition" 
                        id="condition" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option value="excellent" {{ old('condition') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('condition') === 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition') === 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition') === 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                    @error('condition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Flight Time -->
                <div>
                    <label for="total_flight_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Flight Time (hours)
                    </label>
                    <input 
                        type="number" 
                        name="total_flight_time" 
                        id="total_flight_time" 
                        value="{{ old('total_flight_time', 0) }}" 
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    @error('total_flight_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Maintenance Date -->
                <div>
                    <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Last Maintenance Date
                    </label>
                    <input 
                        type="date" 
                        name="last_maintenance_date" 
                        id="last_maintenance_date" 
                        value="{{ old('last_maintenance_date') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    @error('last_maintenance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Specifications -->
            <div class="col-span-2 bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-cog mr-2"></i>Equipment & Specifications
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Camera -->
                    <div>
                        <label for="has_camera" class="flex items-center space-x-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="has_camera" 
                                id="has_camera" 
                                value="1"
                                {{ old('has_camera') ? 'checked' : '' }}
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                            >
                            <span class="text-sm font-medium text-gray-700">Has Camera</span>
                        </label>
                    </div>

                    <!-- Temperature Control -->
                    <div>
                        <label for="has_temperature_control" class="flex items-center space-x-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="has_temperature_control" 
                                id="has_temperature_control" 
                                value="1"
                                {{ old('has_temperature_control') ? 'checked' : '' }}
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                            >
                            <span class="text-sm font-medium text-gray-700">Has Temperature Control</span>
                        </label>
                    </div>

                    <!-- Emergency Parachute -->
                    <div>
                        <label for="has_emergency_parachute" class="flex items-center space-x-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="has_emergency_parachute" 
                                id="has_emergency_parachute" 
                                value="1"
                                {{ old('has_emergency_parachute') ? 'checked' : '' }}
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                            >
                            <span class="text-sm font-medium text-gray-700">Has Emergency Parachute</span>
                        </label>
                    </div>

                    <!-- Firmware Version -->
                    <div>
                        <label for="firmware_version" class="block text-sm font-medium text-gray-700 mb-2">
                            Firmware Version
                        </label>
                        <input 
                            type="text" 
                            name="firmware_version" 
                            id="firmware_version" 
                            value="{{ old('firmware_version') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="v2.5.1"
                        >
                        @error('firmware_version')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sensors (comma-separated) -->
                    <div class="col-span-2">
                        <label for="sensors_input" class="block text-sm font-medium text-gray-700 mb-2">
                            Sensors (comma-separated)
                        </label>
                        <input 
                            type="text" 
                            name="sensors_input" 
                            id="sensors_input" 
                            value="{{ old('sensors_input') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="GPS, Lidar, Ultrasonic, Barometer, Gyroscope"
                        >
                        <p class="mt-1 text-xs text-gray-500">Enter sensor names separated by commas (e.g., GPS, Lidar, Camera)</p>
                        @error('sensors_input')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Temperature Range -->
                    <div>
                        <label for="temperature_min_celsius" class="block text-sm font-medium text-gray-700 mb-2">
                            Min Temperature (°C)
                        </label>
                        <input 
                            type="number" 
                            name="temperature_min_celsius" 
                            id="temperature_min_celsius" 
                            value="{{ old('temperature_min_celsius') }}"
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="-20.0"
                        >
                        @error('temperature_min_celsius')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="temperature_max_celsius" class="block text-sm font-medium text-gray-700 mb-2">
                            Max Temperature (°C)
                        </label>
                        <input 
                            type="number" 
                            name="temperature_max_celsius" 
                            id="temperature_max_celsius" 
                            value="{{ old('temperature_max_celsius') }}"
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="50.0"
                        >
                        @error('temperature_max_celsius')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.drones.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Save Drone
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
