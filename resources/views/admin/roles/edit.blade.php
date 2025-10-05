@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Role</h1>
        <p class="text-gray-600 mt-1">Update role information and permissions</p>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $role->slug) }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                <p class="text-xs text-gray-500 mt-1">Slug cannot be changed</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $role->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                <input type="number" name="level" value="{{ old('level', $role->level) }}" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($allPermissions ?? [] as $permission)
                <label class="flex items-center">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                           {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between items-center pt-6 border-t">
            <a href="{{ route('admin.roles.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i>Update Role</button>
        </div>
    </form>
</div>
@endsection
