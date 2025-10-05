@extends('layouts.app')

@section('title', 'Create Delivery Request')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">New Delivery Request</h1>
        <p class="text-gray-600 mt-1">Request medical supplies delivery to your hospital</p>
    </div>

    <form action="{{ route('hospital.requests.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Request Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Request Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('priority') border-red-500 @enderror">
                        <option value="">Select Priority</option>
                        <option value="emergency" class="text-red-600">Emergency - Immediate</option>
                        <option value="high" class="text-orange-600">High - Within 1 hour</option>
                        <option value="medium" class="text-yellow-600">Medium - Within 3 hours</option>
                        <option value="low">Low - Within 24 hours</option>
                    </select>
                    @error('priority')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Request Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" 
                              placeholder="Describe the medical supplies needed and the reason for the request...">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Requested Delivery Date <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="requested_date" required 
                           value="{{ old('requested_date', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('requested_date') border-red-500 @enderror">
                    @error('requested_date')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', auth()->user()->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', auth()->user()->phone) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Medical Supplies -->
        <div class="bg-white rounded-lg shadow-sm p-6" x-data="{ supplies: [{ supply_id: '', quantity: '' }] }">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Medical Supplies</h2>
            
            <template x-for="(supply, index) in supplies" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medical Supply <span class="text-red-500">*</span></label>
                        <select :name="'supplies[' + index + '][supply_id]'" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Medical Supply</option>
                            <!-- Medical supplies will be loaded from controller -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="number" :name="'supplies[' + index + '][quantity]'" required min="1" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" @click="supplies.splice(index, 1)" x-show="supplies.length > 1"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="supplies.push({ supply_id: '', quantity: '' })"
                    class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-plus-circle mr-2"></i>Add Another Supply
            </button>
        </div>

        <!-- Special Instructions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Special Instructions</h2>
            <textarea name="special_instructions" rows="4" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                      placeholder="Any special handling requirements, storage conditions, or delivery notes...">{{ old('special_instructions') }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center">
            <a href="{{ route('hospital.requests.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg">
                <i class="fas fa-paper-plane mr-2"></i>Submit Request
            </button>
        </div>
    </form>
</div>
@endsection
