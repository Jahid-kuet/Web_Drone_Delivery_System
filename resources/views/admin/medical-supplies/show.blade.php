@extends('layouts.app')

@section('title', 'View Medical Supply')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-home"></i> Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.medical-supplies.index') }}" class="text-gray-600 hover:text-gray-900">Medical Supplies</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Details</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- READ: Display detailed information about medical supply --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">
                        <i class="fas fa-pills mr-2"></i>{{ $supply->name }}
                    </h2>
                    <p class="text-blue-100 mt-1">READ: Viewing supply details from database</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.medical-supplies.edit', $supply) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('admin.medical-supplies.destroy', $supply) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">SKU</label>
                    <p class="text-lg text-gray-900 mt-1">{{ $supply->sku }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Category</label>
                    <p class="text-lg text-gray-900 mt-1">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg">
                            {{ str_replace('_', ' ', ucwords($supply->category)) }}
                        </span>
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Quantity</label>
                    <p class="text-lg text-gray-900 mt-1">{{ number_format($supply->quantity) }} {{ $supply->unit }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Unit Price</label>
                    <p class="text-lg text-gray-900 mt-1">${{ number_format($supply->unit_price, 2) }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Minimum Stock Level</label>
                    <p class="text-lg text-gray-900 mt-1">{{ number_format($supply->min_stock_level ?? 0) }} {{ $supply->unit }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Status</label>
                    <p class="text-lg mt-1">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            {{ $supply->status === 'in_stock' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $supply->status === 'low_stock' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $supply->status === 'out_of_stock' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($supply->status)) }}
                        </span>
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Expiry Date</label>
                    <p class="text-lg text-gray-900 mt-1">
                        @if($supply->expiry_date)
                            {{ \Carbon\Carbon::parse($supply->expiry_date)->format('F d, Y') }}
                        @else
                            <span class="text-gray-400">Not specified</span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Storage Conditions</label>
                    <p class="text-lg text-gray-900 mt-1">{{ $supply->storage_conditions ?? 'Standard conditions' }}</p>
                </div>
            </div>

            @if($supply->description)
                <div class="mt-6">
                    <label class="text-sm font-medium text-gray-500">Description</label>
                    <p class="text-gray-900 mt-2 p-4 bg-gray-50 rounded-lg">{{ $supply->description }}</p>
                </div>
            @endif

            <div class="mt-6 pt-6 border-t grid grid-cols-2 gap-4 text-sm text-gray-500">
                <div>
                    <i class="fas fa-clock mr-1"></i> Created: {{ $supply->created_at->format('M d, Y h:i A') }}
                </div>
                <div>
                    <i class="fas fa-edit mr-1"></i> Last Updated: {{ $supply->updated_at->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Related Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>Supply Statistics
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Value</p>
                <p class="text-2xl font-bold text-blue-600">${{ number_format($supply->quantity * $supply->unit_price, 2) }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stock Status</p>
                <p class="text-2xl font-bold text-green-600">
                    @if($supply->quantity > ($supply->min_stock_level ?? 0))
                        Healthy
                    @else
                        Low
                    @endif
                </p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Requests</p>
                <p class="text-2xl font-bold text-purple-600">{{ $supply->deliveryRequests()->count() }}</p>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('admin.medical-supplies.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>
</div>
@endsection
