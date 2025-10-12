@extends('layouts.app')
@section('title', 'Edit Delivery Request')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-clipboard-list text-orange-600 mr-2"></i>Edit Delivery Request
                </h1>
                <p class="text-gray-600 mt-1">Update delivery request details</p>
            </div>
            <a href="{{ route('admin.delivery-requests.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        {{-- UPDATE: Modify existing delivery request in database --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('admin.delivery-requests.update', $deliveryRequest) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Hospital -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Hospital *</label>
                        <select name="hospital_id" required
                            class="w-full px-4 py-2 border @error('hospital_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Hospital</option>
                            @foreach($hospitals as $hospital)
                                <option value="{{ $hospital->id }}" {{ old('hospital_id', $deliveryRequest->hospital_id) == $hospital->id ? 'selected' : '' }}>
                                    {{ $hospital->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hospital_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Medical Supply -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Medical Supply *</label>
                        <select name="medical_supply_id" required
                            class="w-full px-4 py-2 border @error('medical_supply_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Supply</option>
                            @foreach($medicalSupplies as $supply)
                                <option value="{{ $supply->id }}" {{ old('medical_supply_id', $deliveryRequest->medical_supply_id) == $supply->id ? 'selected' : '' }}>
                                    {{ $supply->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('medical_supply_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Quantity Requested *</label>
                        <input type="number" name="quantity_requested" value="{{ old('quantity_requested', $deliveryRequest->quantity_requested) }}" min="1" required
                            class="w-full px-4 py-2 border @error('quantity_requested') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                        @error('quantity_requested')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Priority *</label>
                        <select name="priority" required
                            class="w-full px-4 py-2 border @error('priority') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority', $deliveryRequest->priority) === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $deliveryRequest->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $deliveryRequest->priority) === 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('priority', $deliveryRequest->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="emergency" {{ old('priority', $deliveryRequest->priority) === 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                        @error('priority')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Required By Date -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Required By Date *</label>
                        <input type="datetime-local" name="required_by_date" value="{{ old('required_by_date', $deliveryRequest->required_by_date ? $deliveryRequest->required_by_date->format('Y-m-d\TH:i') : '') }}" required
                            class="w-full px-4 py-2 border @error('required_by_date') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                        @error('required_by_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Temperature Control -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Requires Temperature Control *</label>
                        <select name="requires_temperature_control" required
                            class="w-full px-4 py-2 border @error('requires_temperature_control') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="0" {{ old('requires_temperature_control', $deliveryRequest->requires_temperature_control) == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('requires_temperature_control', $deliveryRequest->requires_temperature_control) == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('requires_temperature_control')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Temperature Range (optional) -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Temperature Range</label>
                        <input type="text" name="temperature_range" value="{{ old('temperature_range', $deliveryRequest->temperature_range) }}" placeholder="e.g., 2-8°C"
                            class="w-full px-4 py-2 border @error('temperature_range') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                        @error('temperature_range')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Delivery Notes -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Delivery Notes</label>
                        <textarea name="delivery_notes" rows="3"
                            class="w-full px-4 py-2 border @error('delivery_notes') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="Special delivery instructions...">{{ old('delivery_notes', $deliveryRequest->delivery_notes) }}</textarea>
                        @error('delivery_notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Special Instructions -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Special Instructions</label>
                        <textarea name="special_instructions" rows="3"
                            class="w-full px-4 py-2 border @error('special_instructions') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="Additional handling requirements...">{{ old('special_instructions', $deliveryRequest->special_instructions) }}</textarea>
                        @error('special_instructions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.delivery-requests.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Update Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
