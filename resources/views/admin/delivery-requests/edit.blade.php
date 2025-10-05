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
            <form action="{{ route('admin.delivery-requests.update', $request) }}" method="POST">
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
                                <option value="{{ $hospital->id }}" {{ old('hospital_id', $request->hospital_id) == $hospital->id ? 'selected' : '' }}>
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
                                <option value="{{ $supply->id }}" {{ old('medical_supply_id', $request->medical_supply_id) == $supply->id ? 'selected' : '' }}>
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
                        <label class="block text-gray-700 font-medium mb-2">Quantity *</label>
                        <input type="number" name="quantity" value="{{ old('quantity', $request->quantity) }}" min="1" required
                            class="w-full px-4 py-2 border @error('quantity') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                        @error('quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Urgency Level -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Urgency Level *</label>
                        <select name="urgency_level" required
                            class="w-full px-4 py-2 border @error('urgency_level') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Urgency</option>
                            <option value="normal" {{ old('urgency_level', $request->urgency_level) === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="urgent" {{ old('urgency_level', $request->urgency_level) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="emergency" {{ old('urgency_level', $request->urgency_level) === 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                        @error('urgency_level')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Required By -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Required By *</label>
                        <input type="datetime-local" name="required_by" value="{{ old('required_by', $request->required_by) }}" required
                            class="w-full px-4 py-2 border @error('required_by') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                        @error('required_by')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Status *</label>
                        <select name="status" required
                            class="w-full px-4 py-2 border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="pending" {{ old('status', $request->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $request->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="processing" {{ old('status', $request->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="delivered" {{ old('status', $request->status) === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ old('status', $request->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Notes</label>
                    <textarea name="notes" rows="4"
                        class="w-full px-4 py-2 border @error('notes') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500"
                        placeholder="Additional information...">{{ old('notes', $request->notes) }}</textarea>
                    @error('notes')
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
