@extends('layouts.app')
@section('title', 'Hospitals')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span><span class="text-gray-900">Hospitals</span>
@endsection
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div><h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-hospital mr-2 text-purple-600"></i>Hospitals</h1>
            <p class="text-gray-600 mt-1">Manage hospital network</p></div>
        <div class="mt-4 md:mt-0">
            {{-- INSERT: Create new hospital --}}
            <a href="{{ route('admin.hospitals.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Add New Hospital</a></div></div>
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.hospitals.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- READ: Filter and search hospitals --}}
            <div><input type="text" name="search" placeholder="Search hospitals..." value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></div>
            <div><select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="government" {{ request('type') === 'government' ? 'selected' : '' }}>Government</option>
                    <option value="private" {{ request('type') === 'private' ? 'selected' : '' }}>Private</option>
                    <option value="specialist" {{ request('type') === 'specialist' ? 'selected' : '' }}>Specialist</option></select></div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('admin.hospitals.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-center transition"><i class="fas fa-redo"></i> Reset</a></div>
        </form></div>
    {{-- READ: Display all hospitals from database --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($hospitals->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hospital</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th></tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($hospitals as $hospital)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-hospital text-purple-600"></i></div>
                                        <div><div class="font-medium text-gray-900">{{ $hospital->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $hospital->registration_number }}</div></div></div></td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>{{ $hospital->contact_person }}</div>
                                    <div class="text-gray-500">{{ $hospital->phone }}</div></td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($hospital->address, 30) }}</td>
                                <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">{{ ucfirst($hospital->type) }}</span></td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                    {{-- READ: View hospital details --}}
                                    <a href="{{ route('admin.hospitals.show', $hospital) }}" class="text-blue-600 hover:text-blue-900" title="View"><i class="fas fa-eye"></i></a>
                                    {{-- UPDATE: Edit hospital --}}
                                    <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit"><i class="fas fa-edit"></i></a>
                                    {{-- DELETE: Remove hospital --}}
                                    <form action="{{ route('admin.hospitals.destroy', $hospital) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form></td></tr>
                        @endforeach
                    </tbody></table></div>
            <div class="px-6 py-4 border-t border-gray-200">{{ $hospitals->links() }}</div>
        @else
            <div class="text-center py-12"><i class="fas fa-hospital text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">No hospitals found</p>
                <a href="{{ route('admin.hospitals.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Add Your First Hospital</a></div>
        @endif
    </div></div>
@endsection
