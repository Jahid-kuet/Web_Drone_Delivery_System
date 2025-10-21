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
            
            {{-- Display validation errors --}}
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-red-800 font-semibold mb-2">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside text-red-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            {{-- Basic Information --}}
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Hospital Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., Khulna Medical College Hospital">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Hospital Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., HOSP-KHL-001">
                        @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Hospital Type <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            <option value="general_hospital" {{ old('type') === 'general_hospital' ? 'selected' : '' }}>General Hospital</option>
                            <option value="specialized_hospital" {{ old('type') === 'specialized_hospital' ? 'selected' : '' }}>Specialized Hospital</option>
                            <option value="clinic" {{ old('type') === 'clinic' ? 'selected' : '' }}>Clinic</option>
                            <option value="emergency_center" {{ old('type') === 'emergency_center' ? 'selected' : '' }}>Emergency Center</option>
                            <option value="blood_bank" {{ old('type') === 'blood_bank' ? 'selected' : '' }}>Blood Bank</option>
                            <option value="diagnostic_center" {{ old('type') === 'diagnostic_center' ? 'selected' : '' }}>Diagnostic Center</option>
                            <option value="pharmacy" {{ old('type') === 'pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                            <option value="research_facility" {{ old('type') === 'research_facility' ? 'selected' : '' }}>Research Facility</option>
                        </select>
                        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="contact@hospital.com">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Address Information --}}
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Address Information</h3>
                <div class="space-y-4">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Street Address <span class="text-red-500">*</span></label>
                        <textarea name="address" id="address" rows="2" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Enter full street address">{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., Khulna">
                            @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Division <span class="text-red-500">*</span></label>
                            <input type="text" name="state" id="state" value="{{ old('state', 'Khulna') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., Khulna Division">
                            @error('state')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code <span class="text-red-500">*</span></label>
                            <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., 9100">
                            @error('zip_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                        <input type="text" name="country" id="country" value="{{ old('country', 'Bangladesh') }}" required readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600" placeholder="Bangladesh">
                        <p class="mt-1 text-xs text-gray-500">Service currently available in Bangladesh only</p>
                        @error('country')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- GPS Coordinates (Khulna Only) --}}
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>GPS Coordinates (Khulna Division Only)
                </h3>
                <p class="text-sm text-blue-700 mb-4">📍 Use coordinates within Khulna Division: Lat 21.5-23.5, Lng 88.5-90.5</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                        <input type="number" name="latitude" id="latitude" value="{{ old('latitude') }}" step="0.000001" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., 22.8456">
                        <p class="mt-1 text-xs text-gray-500">Valid range: 21.5 to 23.5</p>
                        @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                        <input type="number" name="longitude" id="longitude" value="{{ old('longitude') }}" step="0.000001" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., 89.5403">
                        <p class="mt-1 text-xs text-gray-500">Valid range: 88.5 to 90.5</p>
                        @error('longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Primary Phone <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="+880-41-761020">
                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Dr. John Doe">
                        @error('contact_person')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="contact_person_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Person Phone <span class="text-red-500">*</span></label>
                        <input type="tel" name="contact_person_phone" id="contact_person_phone" value="{{ old('contact_person_phone') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="+880-1700-000000">
                        @error('contact_person_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="operating_hours" class="block text-sm font-medium text-gray-700 mb-2">Operating Hours</label>
                        <input type="text" name="operating_hours" id="operating_hours" value="{{ old('operating_hours') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., 24/7 or 8:00 AM - 10:00 PM">
                        @error('operating_hours')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
                        <input type="tel" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="+880-1700-999999">
                        @error('emergency_contact')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Drone Delivery Settings --}}
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-drone text-green-600 mr-2"></i>Drone Delivery Settings
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="has_drone_landing_pad" class="block text-sm font-medium text-gray-700 mb-2">Has Drone Landing Pad? <span class="text-red-500">*</span></label>
                        <select name="has_drone_landing_pad" id="has_drone_landing_pad" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select Option</option>
                            <option value="1" {{ old('has_drone_landing_pad') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('has_drone_landing_pad') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('has_drone_landing_pad')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="landing_pad_coordinates" class="block text-sm font-medium text-gray-700 mb-2">Landing Pad GPS (Optional)</label>
                        <input type="text" name="landing_pad_coordinates" id="landing_pad_coordinates" value="{{ old('landing_pad_coordinates') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., 22.8456, 89.5403">
                        @error('landing_pad_coordinates')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- License & Status --}}
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">License & Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., LIC-BD-KHL-001">
                        @error('license_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="license_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">License Expiry Date</label>
                        <input type="date" name="license_expiry_date" id="license_expiry_date" value="{{ old('license_expiry_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('license_expiry_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }} selected>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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
