@extends('layouts.app')

@section('title', 'Send Notification')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Send Notification</h1>
        <p class="text-gray-600 mt-1">Send a notification to selected users</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.notifications.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                placeholder="Enter notification title">
            @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Message <span class="text-red-500">*</span></label>
            <textarea name="message" rows="5" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('message') border-red-500 @enderror"
                placeholder="Enter notification message">{{ old('message') }}</textarea>
            @error('message')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
            <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                <option value="">Select Type</option>
                <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>Info</option>
                <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="error" {{ old('type') === 'error' ? 'selected' : '' }}>Error</option>
            </select>
            @error('type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Recipients <span class="text-red-500">*</span></label>
            
            <div class="mb-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Select All Users</span>
                </label>
            </div>

            <div class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto @error('recipients') border-red-500 @enderror">
                @php
                    $oldRecipients = old('recipients', []);
                @endphp
                @foreach($users as $user)
                <div class="mb-2">
                    <label class="inline-flex items-center hover:bg-gray-50 p-2 rounded cursor-pointer w-full">
                        <input type="checkbox" name="recipients[]" value="{{ $user->id }}" 
                            class="recipient-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ in_array($user->id, $oldRecipients) ? 'checked' : '' }}>
                        <span class="ml-3 text-sm">
                            <span class="font-medium text-gray-900">{{ $user->name }}</span>
                            <span class="text-gray-500">({{ $user->email }})</span>
                            @if($user->roles->isNotEmpty())
                            <span class="ml-2">
                                @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $role->name }}
                                </span>
                                @endforeach
                            </span>
                            @endif
                        </span>
                    </label>
                </div>
                @endforeach
            </div>
            @error('recipients')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            @error('recipients.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-between items-center pt-4 border-t">
            <a href="{{ route('admin.notifications.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Send Notification
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.recipient-checkbox');
    
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                selectAll.checked = false;
            } else {
                selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            }
        });
    });
});
</script>
@endsection
