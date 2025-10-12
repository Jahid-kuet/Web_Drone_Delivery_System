@extends('layouts.app')
@section('title', 'Edit Hospital')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-hospital text-purple-600 mr-2"></i>Edit Hospital
                </h1>
                <p class="text-gray-600 mt-1">Update hospital information</p>
            </div>
            <a href="{{ route('admin.hospitals.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        {{-- UPDATE: Modify existing hospital data in the database --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('admin.hospitals.update', $hospital) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Hospital Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $hospital->name) }}" required
                        class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Hospital Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $hospital->code) }}" required
                        class="w-full px-4 py-2 border @error('code') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Hospital Type <span class="text-red-500">*</span></label>
                    <select name="type" required
                        class="w-full px-4 py-2 border @error('type') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select Type</option>
                        <option value="hospital" {{ old('type', $hospital->type) === 'hospital' ? 'selected' : '' }}>Hospital</option>
                        <option value="clinic" {{ old('type', $hospital->type) === 'clinic' ? 'selected' : '' }}>Clinic</option>
                        <option value="health_center" {{ old('type', $hospital->type) === 'health_center' ? 'selected' : '' }}>Health Center</option>
                        <option value="pharmacy" {{ old('type', $hospital->type) === 'pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                        <option value="other" {{ old('type', $hospital->type) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Contact Person <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $hospital->contact_person) }}" required
                        class="w-full px-4 py-2 border @error('contact_person') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('contact_person')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person Phone -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Contact Person Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_person_phone" value="{{ old('contact_person_phone', $hospital->contact_person_phone) }}" required
                        class="w-full px-4 py-2 border @error('contact_person_phone') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('contact_person_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone & Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $hospital->phone ?? $hospital->primary_phone) }}" required
                            class="w-full px-4 py-2 border @error('phone') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $hospital->email) }}" required
                            class="w-full px-4 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required
                        class="w-full px-4 py-2 border @error('address') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('address', $hospital->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City, State, Zip Code, Country -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" value="{{ old('city', $hospital->city) }}" required
                            class="w-full px-4 py-2 border @error('city') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">State/Province <span class="text-red-500">*</span></label>
                        <input type="text" name="state" value="{{ old('state', $hospital->state ?? $hospital->state_province) }}" required
                            class="w-full px-4 py-2 border @error('state') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('state')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Zip/Postal Code <span class="text-red-500">*</span></label>
                        <input type="text" name="zip_code" value="{{ old('zip_code', $hospital->zip_code ?? $hospital->postal_code) }}" required
                            class="w-full px-4 py-2 border @error('zip_code') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('zip_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Country <span class="text-red-500">*</span></label>
                        <input type="text" name="country" value="{{ old('country', $hospital->country) }}" required
                            class="w-full px-4 py-2 border @error('country') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('country')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Has Drone Landing Pad -->
                <div class="mb-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="has_drone_landing_pad" 
                            value="1"
                            {{ old('has_drone_landing_pad', $hospital->has_drone_landing_pad) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                        >
                        <span class="text-gray-700 font-medium">Has Drone Landing Pad <span class="text-red-500">*</span></span>
                    </label>
                    @error('has_drone_landing_pad')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                        class="w-full px-4 py-2 border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="active" {{ old('status', $hospital->status ?? ($hospital->is_active ? 'active' : 'inactive')) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $hospital->status ?? ($hospital->is_active ? 'active' : 'inactive')) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- GPS Coordinates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Latitude *</label>
                        <input type="number" step="0.000001" name="latitude" value="{{ old('latitude', $hospital->latitude) }}" required
                            class="w-full px-4 py-2 border @error('latitude') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('latitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Longitude *</label>
                        <input type="number" step="0.000001" name="longitude" value="{{ old('longitude', $hospital->longitude) }}" required
                            class="w-full px-4 py-2 border @error('longitude') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('longitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.hospitals.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Update Hospital
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
