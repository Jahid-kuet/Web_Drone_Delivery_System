@extends('layouts.app')

@section('title', 'Add Medical Supply')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.medical-supplies.index') }}" class="text-gray-600 hover:text-gray-900">Medical Supplies</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Add New</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-pills mr-2"></i>Add New Medical Supply
            </h2>
            <p class="text-blue-100 mt-1">INSERT: Create a new medical supply record in database</p>
        </div>

        {{-- INSERT: Form to create new medical supply --}}
        <form action="{{ route('admin.medical-supplies.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

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
                        value="{{ old('name') }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Paracetamol 500mg"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="sku" 
                        id="sku" 
                        value="{{ old('sku') }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., MED-001"
                    >
                    @error('sku')
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
                        <option value="medication" {{ old('category') === 'medication' ? 'selected' : '' }}>Medication</option>
                        <option value="blood_products" {{ old('category') === 'blood_products' ? 'selected' : '' }}>Blood Products</option>
                        <option value="vaccines" {{ old('category') === 'vaccines' ? 'selected' : '' }}>Vaccines</option>
                        <option value="surgical" {{ old('category') === 'surgical' ? 'selected' : '' }}>Surgical</option>
                        <option value="equipment" {{ old('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="unit" 
                        id="unit" 
                        value="{{ old('unit') }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., tablets, ml, units"
                    >
                    @error('unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="quantity" 
                        id="quantity" 
                        value="{{ old('quantity') }}" 
                        required
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="0"
                    >
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit Price -->
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Price ($) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="unit_price" 
                        id="unit_price" 
                        value="{{ old('unit_price') }}" 
                        required
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="0.00"
                    >
                    @error('unit_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Stock Level -->
                <div>
                    <label for="min_stock_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Stock Level
                    </label>
                    <input 
                        type="number" 
                        name="min_stock_level" 
                        id="min_stock_level" 
                        value="{{ old('min_stock_level') }}" 
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="10"
                    >
                    @error('min_stock_level')
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
                        value="{{ old('expiry_date') }}" 
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
                    placeholder="Enter detailed description..."
                >{{ old('description') }}</textarea>
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
                    value="{{ old('storage_conditions') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., Store at 2-8°C"
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
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Save Supply
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
