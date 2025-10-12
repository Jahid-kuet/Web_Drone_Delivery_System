@extends('layouts.app')

@section('title', 'Delivery Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-4">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <a href="{{ route('admin.delivery-requests.index') }}" class="hover:text-blue-600">Delivery Requests</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-gray-900">#{{ $deliveryRequest->request_number ?? 'N/A' }}</span>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Request #{{ $deliveryRequest->request_number ?? 'N/A' }}</h1>
                <p class="text-gray-600 mt-1">{{ $deliveryRequest->hospital->name ?? 'N/A' }}</p>
            </div>
            <div class="flex space-x-2">
                @if($deliveryRequest->status == 'pending')
                <form action="{{ route('admin.delivery-requests.approve', $deliveryRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-check mr-2"></i>Approve
                    </button>
                </form>
                <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg"
                        onclick="document.getElementById('rejectModal').classList.remove('hidden')">
                    <i class="fas fa-times mr-2"></i>Reject
                </button>
                @endif
                @if($deliveryRequest->status == 'approved')
                <a href="{{ route('admin.deliveries.create', ['request_id' => $deliveryRequest->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-helicopter mr-2"></i>Create Delivery
                </a>
                @endif
                <a href="{{ route('admin.delivery-requests.edit', $deliveryRequest) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Request Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Request Number</p>
                        <p class="text-gray-900 font-medium">#{{ $deliveryRequest->request_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                            $color = $statusColors[$deliveryRequest->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $color }}">
                            {{ ucfirst($deliveryRequest->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Priority</p>
                        @php
                            $priorityColors = [
                                'emergency' => 'bg-red-100 text-red-800',
                                'urgent' => 'bg-orange-100 text-orange-800',
                                'normal' => 'bg-blue-100 text-blue-800',
                                'low' => 'bg-gray-100 text-gray-800',
                            ];
                            $pColor = $priorityColors[$deliveryRequest->priority] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $pColor }}">
                            {{ ucfirst($deliveryRequest->priority) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Required By</p>
                        <p class="text-gray-900">{{ $deliveryRequest->required_by_date ? $deliveryRequest->required_by_date->format('M d, Y H:i') : 'ASAP' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Created Date</p>
                        <p class="text-gray-900">{{ $deliveryRequest->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Requested By</p>
                        <p class="text-gray-900">{{ $deliveryRequest->requestedBy->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Medical Supplies -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Medical Supplies</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-sm font-medium text-gray-700">Item</th>
                                <th class="text-center py-2 text-sm font-medium text-gray-700">Quantity</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-700">Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $supplies = is_array($deliveryRequest->medical_supplies ?? null)
                                    ? $deliveryRequest->medical_supplies
                                    : (is_string($deliveryRequest->medical_supplies ?? null)
                                        ? json_decode($deliveryRequest->medical_supplies, true)
                                        : []);
                            @endphp
                            @if(!empty($supplies))
                                @foreach($supplies as $item)
                                    @php
                                        $name = is_array($item) ? ($item['name'] ?? 'N/A') : (is_string($item) ? $item : 'N/A');
                                        $qty = is_array($item) ? ($item['quantity'] ?? ($item['quantity_requested'] ?? 0)) : 0;
                                        $code = is_array($item) ? ($item['code'] ?? '') : '';
                                    @endphp
                                    <tr class="border-b">
                                        <td class="py-3">
                                            <p class="text-gray-900 font-medium">{{ $name }}</p>
                                            <p class="text-sm text-gray-500">{{ $code }}</p>
                                        </td>
                                        <td class="text-center py-3 text-gray-900">{{ $qty }}</td>
                                        <td class="text-right py-3 text-gray-900">{{ $code }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-gray-500">No items specified</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            @if($deliveryRequest->special_requirements || $deliveryRequest->delivery_instructions || $deliveryRequest->rejection_reason)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes & Instructions</h2>
                @if($deliveryRequest->special_requirements)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-1">Special Requirements</h3>
                    <p class="text-gray-600">{{ $deliveryRequest->special_requirements }}</p>
                </div>
                @endif
                @if($deliveryRequest->delivery_instructions)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-1">Delivery Instructions</h3>
                    <p class="text-gray-600">{{ $deliveryRequest->delivery_instructions }}</p>
                </div>
                @endif
                @if($deliveryRequest->rejection_reason)
                <div>
                    <h3 class="text-sm font-medium text-red-700 mb-1">Rejection Reason</h3>
                    <p class="text-red-600">{{ $deliveryRequest->rejection_reason }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Hospital Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Hospital</h2>
                @if($deliveryRequest->hospital)
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="text-gray-900 font-medium">{{ $deliveryRequest->hospital->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="text-gray-900 text-sm">{{ $deliveryRequest->hospital->address }}</p>
                        <p class="text-gray-900 text-sm">{{ $deliveryRequest->hospital->city }}, {{ $deliveryRequest->hospital->state }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Contact</p>
                        <p class="text-gray-900 text-sm">{{ $deliveryRequest->hospital->phone }}</p>
                        <p class="text-gray-900 text-sm">{{ $deliveryRequest->hospital->email }}</p>
                    </div>
                </div>
                @else
                <p class="text-gray-500">No hospital assigned</p>
                @endif
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Timeline</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-plus text-blue-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Request Created</p>
                            <p class="text-xs text-gray-500">{{ $deliveryRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @if($deliveryRequest->approved_at)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Approved</p>
                            <p class="text-xs text-gray-500">{{ $deliveryRequest->approved_at->format('M d, Y H:i') }}</p>
                            <p class="text-xs text-gray-500">By: {{ $deliveryRequest->approvedBy->name ?? 'System' }}</p>
                        </div>
                    </div>
                    @endif
                    @if($deliveryRequest->rejected_at)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-times text-red-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Rejected</p>
                            <p class="text-xs text-gray-500">{{ $deliveryRequest->rejected_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Related Delivery -->
            @if($deliveryRequest->delivery)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Related Delivery</h2>
                <div class="space-y-2">
                    <p class="text-gray-900 font-medium">{{ $deliveryRequest->delivery->delivery_number }}</p>
                    <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">
                        {{ ucfirst($deliveryRequest->delivery->status) }}
                    </span>
                    <a href="{{ route('admin.deliveries.show', $deliveryRequest->delivery) }}" class="block text-blue-600 hover:text-blue-800 text-sm mt-2">
                        View Delivery Details →
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject Delivery Request</h3>
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.delivery-requests.reject', $deliveryRequest) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason <span class="text-red-600">*</span>
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" 
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                              required placeholder="Please provide a reason for rejecting this request...">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-times mr-2"></i>Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
