@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Role</h1>
        <p class="text-gray-600 mt-1">Define a new role with specific permissions</p>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-red-500">*</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('slug') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">e.g., hospital_admin, drone_operator</p>
                @error('slug')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                <input type="number" name="level" value="{{ old('level', 1) }}" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Higher level = more authority</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach(['view', 'create', 'edit', 'delete', 'approve', 'manage'] as $permission)
                <label class="flex items-center">
                    <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">{{ ucfirst($permission) }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between items-center pt-6 border-t">
            <a href="{{ route('admin.roles.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-check mr-2"></i>Create Role</button>
        </div>
    </form>
</div>
@endsection
