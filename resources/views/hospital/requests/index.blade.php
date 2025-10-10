@extends('layouts.app')

@section('title', 'My Delivery Requests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <a href="{{ route('hospital.dashboard') }}" class="hover:text-teal-600 transition">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-gray-900">Delivery Requests</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Delivery Requests</h1>
                    <p class="text-gray-600 mt-2">Manage your medical supply delivery requests</p>
                </div>
                <a href="{{ route('hospital.requests.create') }}" 
                    class="px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-700 hover:to-blue-700 text-white rounded-lg shadow-lg transition flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i> New Request
                </a>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('hospital.requests.index') }}" class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Request number..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                        <option value="">All Priority</option>
                        <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('hospital.requests.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>

        {{-- Requests List --}}
        @forelse($requests as $request)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4 hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $request->request_number }}</h3>
                            
                            {{-- Status Badge --}}
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusIcons = [
                                    'pending' => 'fa-clock',
                                    'approved' => 'fa-check-circle',
                                    'rejected' => 'fa-times-circle',
                                    'cancelled' => 'fa-ban',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800' }}">
                                <i class="fas {{ $statusIcons[$request->status] ?? 'fa-question' }} mr-1"></i>
                                {{ ucfirst($request->status) }}
                            </span>

                            {{-- Priority Badge --}}
                            @php
                                $priorityColors = [
                                    'emergency' => 'bg-red-500 text-white',
                                    'high' => 'bg-orange-500 text-white',
                                    'medium' => 'bg-yellow-500 text-white',
                                    'low' => 'bg-green-500 text-white',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $priorityColors[$request->priority] ?? 'bg-gray-500 text-white' }}">
                                {{ ucfirst($request->priority) }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-3">{{ $request->description }}</p>
                        
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-2 text-teal-600"></i>
                                <span>{{ $request->requested_date }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2 text-teal-600"></i>
                                <span>{{ $request->requestedBy->name }}</span>
                            </div>
                            @if($request->contact_person)
                            <div class="flex items-center">
                                <i class="fas fa-phone mr-2 text-teal-600"></i>
                                <span>{{ $request->contact_person }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $request->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                {{-- Medical Supplies Summary --}}
                @php
                    $supplies = json_decode($request->medical_supplies, true);
                @endphp
                @if($supplies && count($supplies) > 0)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-box-open text-purple-600 mr-2"></i>
                        Medical Supplies ({{ count($supplies) }} items)
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($supplies as $supply)
                        <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs">
                            Qty: {{ $supply['quantity'] ?? 'N/A' }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t">
                    <a href="#" 
                        class="text-teal-600 hover:text-teal-800 text-sm font-medium transition flex items-center">
                        <i class="fas fa-eye mr-2"></i> View Details
                    </a>
                    @if($request->status == 'pending')
                    <button onclick="if(confirm('Cancel this request?')) { /* Add cancel logic */ }"
                        class="text-red-600 hover:text-red-800 text-sm font-medium transition flex items-center">
                        <i class="fas fa-times-circle mr-2"></i> Cancel Request
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clipboard-list text-5xl text-teal-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Delivery Requests Yet</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first delivery request</p>
                <a href="{{ route('hospital.requests.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-700 hover:to-blue-700 text-white rounded-lg shadow-lg transition">
                    <i class="fas fa-plus-circle mr-2"></i> Create First Request
                </a>
            </div>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($requests->hasPages())
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
