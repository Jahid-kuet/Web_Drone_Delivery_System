@extends('layouts.app')

@section('title', 'Edit Medical Supply')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.medical-supplies.index') }}" class="text-gray-600 hover:text-gray-900">Medical Supplies</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Edit</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>Edit Medical Supply
            </h2>
            <p class="text-yellow-100 mt-1">UPDATE: Modify existing medical supply data in database</p>
        </div>

        {{-- UPDATE: Form to edit medical supply --}}
        <form action="{{ route('admin.medical-supplies.update', $supply) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supply Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Supply Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $supply->name) }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code" 
                        value="{{ old('code', $supply->code) }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="category" 
                        id="category" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Select Category</option>
                        <option value="blood_products" {{ old('category', $supply->category) === 'blood_products' ? 'selected' : '' }}>Blood Products</option>
                        <option value="medicines" {{ old('category', $supply->category) === 'medicines' ? 'selected' : '' }}>Medicines</option>
                        <option value="vaccines" {{ old('category', $supply->category) === 'vaccines' ? 'selected' : '' }}>Vaccines</option>
                        <option value="surgical_instruments" {{ old('category', $supply->category) === 'surgical_instruments' ? 'selected' : '' }}>Surgical Instruments</option>
                        <option value="emergency_supplies" {{ old('category', $supply->category) === 'emergency_supplies' ? 'selected' : '' }}>Emergency Supplies</option>
                        <option value="diagnostic_kits" {{ old('category', $supply->category) === 'diagnostic_kits' ? 'selected' : '' }}>Diagnostic Kits</option>
                        <option value="medical_devices" {{ old('category', $supply->category) === 'medical_devices' ? 'selected' : '' }}>Medical Devices</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="type" 
                        id="type" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Select Type</option>
                        <option value="liquid" {{ old('type', $supply->type) === 'liquid' ? 'selected' : '' }}>Liquid</option>
                        <option value="solid" {{ old('type', $supply->type) === 'solid' ? 'selected' : '' }}>Solid</option>
                        <option value="fragile" {{ old('type', $supply->type) === 'fragile' ? 'selected' : '' }}>Fragile</option>
                        <option value="temperature_sensitive" {{ old('type', $supply->type) === 'temperature_sensitive' ? 'selected' : '' }}>Temperature Sensitive</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity Available -->
                <div>
                    <label for="quantity_available" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity Available <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="quantity_available" 
                        id="quantity_available" 
                        value="{{ old('quantity_available', $supply->quantity_available) }}" 
                        required
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('quantity_available')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Weight (kg) -->
                <div>
                    <label for="weight_kg" class="block text-sm font-medium text-gray-700 mb-2">
                        Weight (kg) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="weight_kg" 
                        id="weight_kg" 
                        value="{{ old('weight_kg', $supply->weight_kg) }}" 
                        required
                        min="0"
                        step="0.001"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('weight_kg')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit Price -->
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Price ($)
                    </label>
                    <input 
                        type="number" 
                        name="unit_price" 
                        id="unit_price" 
                        value="{{ old('unit_price', $supply->unit_price) }}" 
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('unit_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Stock Level -->
                <div>
                    <label for="minimum_stock_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Stock Level <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="minimum_stock_level" 
                        id="minimum_stock_level" 
                        value="{{ old('minimum_stock_level', $supply->minimum_stock_level) }}" 
                        required
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('minimum_stock_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Volume (ml) -->
                <div>
                    <label for="volume_ml" class="block text-sm font-medium text-gray-700 mb-2">
                        Volume (ml)
                    </label>
                    <input 
                        type="number" 
                        name="volume_ml" 
                        id="volume_ml" 
                        value="{{ old('volume_ml', $supply->volume_ml) }}" 
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('volume_ml')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Manufacturer -->
                <div>
                    <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">
                        Manufacturer
                    </label>
                    <input 
                        type="text" 
                        name="manufacturer" 
                        id="manufacturer" 
                        value="{{ old('manufacturer', $supply->manufacturer) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('manufacturer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batch Number -->
                <div>
                    <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Batch Number
                    </label>
                    <input 
                        type="text" 
                        name="batch_number" 
                        id="batch_number" 
                        value="{{ old('batch_number', $supply->batch_number) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('batch_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expiry Date -->
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Expiry Date
                    </label>
                    <input 
                        type="date" 
                        name="expiry_date" 
                        id="expiry_date" 
                        value="{{ old('expiry_date', $supply->expiry_date) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('expiry_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >{{ old('description', $supply->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Storage Conditions -->
            <div>
                <label for="storage_conditions" class="block text-sm font-medium text-gray-700 mb-2">
                    Storage Conditions
                </label>
                <input 
                    type="text" 
                    name="storage_conditions" 
                    id="storage_conditions" 
                    value="{{ old('storage_conditions', $supply->storage_conditions) }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                @error('storage_conditions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.medical-supplies.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Update Supply
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
