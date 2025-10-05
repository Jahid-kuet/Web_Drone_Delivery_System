@extends('layouts.app')
@section('title', 'Add Hospital')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.hospitals.index') }}" class="text-gray-600 hover:text-gray-900">Hospitals</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Add New</span>
@endsection
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-hospital mr-2"></i>Add New Hospital</h2>
            <p class="text-purple-100 mt-1">INSERT: Create a new hospital record in database</p>
        </div>
        {{-- INSERT: Form to create new hospital --}}
        <form action="{{ route('admin.hospitals.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label for="name" class="block text-sm font-medium text-gray-700 mb-2">Hospital Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., City General Hospital">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">Registration Number <span class="text-red-500">*</span></label>
                    <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., HOSP-2023-001">
                    @error('registration_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Dr. John Doe">
                    @error('contact_person')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="+1234567890">
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="contact@hospital.com">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select Type</option>
                        <option value="government" {{ old('type') === 'government' ? 'selected' : '' }}>Government</option>
                        <option value="private" {{ old('type') === 'private' ? 'selected' : '' }}>Private</option>
                        <option value="specialist" {{ old('type') === 'specialist' ? 'selected' : '' }}>Specialist</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div><label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                <textarea name="address" id="address" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Enter full address">{{ old('address') }}</textarea>
                @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                    <input type="number" name="latitude" id="latitude" value="{{ old('latitude') }}" step="0.000001" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., 40.712776">
                    @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                    <input type="number" name="longitude" id="longitude" value="{{ old('longitude') }}" step="0.000001" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., -74.005974">
                    @error('longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.hospitals.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Save Hospital</button>
            </div>
        </form>
    </div>
</div>
@endsection
