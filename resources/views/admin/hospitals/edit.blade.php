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
                    <label class="block text-gray-700 font-medium mb-2">Hospital Name *</label>
                    <input type="text" name="name" value="{{ old('name', $hospital->name) }}" required
                        class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Registration Number -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Registration Number *</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number', $hospital->registration_number) }}" required
                        class="w-full px-4 py-2 border @error('registration_number') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('registration_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Contact Person *</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $hospital->contact_person) }}" required
                        class="w-full px-4 py-2 border @error('contact_person') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('contact_person')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone & Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Phone *</label>
                        <input type="text" name="phone" value="{{ old('phone', $hospital->phone) }}" required
                            class="w-full px-4 py-2 border @error('phone') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $hospital->email) }}" required
                            class="w-full px-4 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Type -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Hospital Type *</label>
                    <select name="type" required
                        class="w-full px-4 py-2 border @error('type') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select Type</option>
                        <option value="government" {{ old('type', $hospital->type) === 'government' ? 'selected' : '' }}>Government</option>
                        <option value="private" {{ old('type', $hospital->type) === 'private' ? 'selected' : '' }}>Private</option>
                        <option value="specialist" {{ old('type', $hospital->type) === 'specialist' ? 'selected' : '' }}>Specialist</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Address *</label>
                    <textarea name="address" rows="3" required
                        class="w-full px-4 py-2 border @error('address') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('address', $hospital->address) }}</textarea>
                    @error('address')
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
