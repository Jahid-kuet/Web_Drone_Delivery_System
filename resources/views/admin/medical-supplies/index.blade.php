@extends('layouts.app')

@section('title', 'Medical Supplies')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Medical Supplies</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-pills mr-2 text-blue-600"></i>Medical Supplies
            </h1>
            <p class="text-gray-600 mt-1">Manage your medical supply inventory</p>
        </div>
        <div class="mt-4 md:mt-0">
            {{-- INSERT: Create new medical supply --}}
            <a href="{{ route('admin.medical-supplies.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>
                Add New Supply
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.medical-supplies.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- READ: Filter and search supplies --}}
            <div>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search supplies..." 
                    value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <option value="medication" {{ request('category') === 'medication' ? 'selected' : '' }}>Medication</option>
                    <option value="blood_products" {{ request('category') === 'blood_products' ? 'selected' : '' }}>Blood Products</option>
                    <option value="vaccines" {{ request('category') === 'vaccines' ? 'selected' : '' }}>Vaccines</option>
                    <option value="surgical" {{ request('category') === 'surgical' ? 'selected' : '' }}>Surgical</option>
                    <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.medical-supplies.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-center transition">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- READ: Display all medical supplies from database --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($supplies->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supply Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($supplies as $supply)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-pills text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $supply->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $supply->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                        {{ str_replace('_', ' ', ucwords($supply->category)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ number_format($supply->quantity) }} {{ $supply->unit }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    ${{ number_format($supply->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $supply->status === 'in_stock' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $supply->status === 'low_stock' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $supply->status === 'out_of_stock' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ str_replace('_', ' ', ucfirst($supply->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($supply->expiry_date)
                                        {{ \Carbon\Carbon::parse($supply->expiry_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                    {{-- READ: View supply details --}}
                                    <a href="{{ route('admin.medical-supplies.show', $supply) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- UPDATE: Edit supply --}}
                                    <a href="{{ route('admin.medical-supplies.edit', $supply) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- DELETE: Remove supply --}}
                                    <form action="{{ route('admin.medical-supplies.destroy', $supply) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this supply?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $supplies->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-pills text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">No medical supplies found</p>
                <a href="{{ route('admin.medical-supplies.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Supply
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
