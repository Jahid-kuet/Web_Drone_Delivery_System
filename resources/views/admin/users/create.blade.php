@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
        <p class="text-gray-600 mt-1">Add a new user to the system</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Roles <span class="text-red-500">*</span></label>
                <select name="roles[]" multiple required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('roles') border-red-500 @enderror" size="4">
                    @foreach($roles ?? [] as $role)
                    <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple roles</p>
                @error('roles')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="">Select Status</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @error('status')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hospital</label>
                <select name="hospital_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('hospital_id') border-red-500 @enderror">
                    <option value="">Select Hospital (Optional)</option>
                    @foreach($hospitals ?? [] as $hospital)
                    <option value="{{ $hospital->id }}" {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>
                        {{ $hospital->name }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Required for hospital staff and admins</p>
                @error('hospital_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Additional Fields Section -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information (Optional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                    <input type="text" name="state_province" value="{{ old('state_province') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Postal/Zip Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" value="{{ old('country') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">License Expiry Date</label>
                    <input type="date" name="license_expiry_date" value="{{ old('license_expiry_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center pt-6 border-t">
            <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-check mr-2"></i>Create User</button>
        </div>
    </form>
</div>
@endsection
