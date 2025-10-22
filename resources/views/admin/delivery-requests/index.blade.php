@extends('layouts.app')
@section('title', 'Delivery Requests')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-clipboard-list text-orange-600 mr-2"></i>Delivery Requests
            </h1>
            <p class="text-gray-600 mt-1">Manage all delivery requests</p>
        </div>
        @if(!auth()->user()->hasRoleSlug('admin') && !auth()->user()->hasRoleSlug('super_admin'))
            {{-- Only hospital staff can create delivery requests --}}
            <a href="{{ route('admin.delivery-requests.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>New Request
            </a>
        @else
            {{-- Admins can only view and manage requests --}}
            <div class="bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Admin View:</strong> Only hospital staff can create delivery requests
                </p>
            </div>
        @endif
    </div>

    {{-- READ: Fetch and display delivery requests from database --}}
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.delivery-requests.index') }}" class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Search requests..." value="{{ request('search') }}"
                class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="urgency" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="">All Urgency</option>
                <option value="emergency" {{ request('urgency') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                <option value="urgent" {{ request('urgency') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="normal" {{ request('urgency') === 'normal' ? 'selected' : '' }}>Normal</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.delivery-requests.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-orange-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Hospital</th>
                        <th class="px-6 py-3 text-left">Supply</th>
                        <th class="px-6 py-3 text-left">Quantity</th>
                        <th class="px-6 py-3 text-left">Urgency</th>
                        <th class="px-6 py-3 text-left">Required By</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">#{{ $request->id }}</td>
                            <td class="px-6 py-4">{{ $request->destination_hospital_name ?? ($request->hospital->name ?? 'N/A') }}</td>
                            <td class="px-6 py-4">
                                @if(is_array($request->medical_supplies) && count($request->medical_supplies) > 0)
                                    {{ count($request->medical_supplies) }} item(s)
                                @else
                                    {{ $request->medicalSupply->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(is_array($request->medical_supplies) && count($request->medical_supplies) > 0)
                                    @php
                                        $totalQty = collect($request->medical_supplies)->sum('quantity');
                                    @endphp
                                    {{ $totalQty }} units
                                @else
                                    {{ $request->quantity ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $request->urgency_level === 'emergency' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $request->urgency_level === 'urgent' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $request->urgency_level === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    <i class="fas 
                                        {{ $request->urgency_level === 'emergency' ? 'fa-exclamation-triangle' : '' }}
                                        {{ $request->urgency_level === 'urgent' ? 'fa-exclamation-circle' : '' }}
                                        {{ $request->urgency_level === 'normal' ? 'fa-info-circle' : '' }}
                                        mr-1"></i>
                                    {{ ucfirst($request->urgency_level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $request->requested_delivery_time ? \Carbon\Carbon::parse($request->requested_delivery_time)->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $request->status === 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $request->status === 'processing' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $request->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $request->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.delivery-requests.show', $request) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.delivery-requests.edit', $request) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.delivery-requests.destroy', $request) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">No delivery requests found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            {{ $requests->links() }}
        </div>
    </div>
</div>

{{-- DELETE: Remove delivery request from database (in table actions) --}}
@endsection
