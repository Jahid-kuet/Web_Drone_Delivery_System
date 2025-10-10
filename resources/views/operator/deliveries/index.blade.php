@extends('layouts.app')

@section('title', 'My Deliveries')

@section('breadcrumb')
    <i class="fas fa-shipping-fast mr-2"></i> My Deliveries
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header with filters --}}
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Deliveries</h1>
                <p class="text-gray-600 mt-1">Manage your assigned delivery tasks</p>
            </div>
            
            {{-- Filter tabs --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('operator.deliveries.index') }}" 
                   class="px-4 py-2 rounded-lg font-medium transition {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-list mr-2"></i>All
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ !request('status') ? 'bg-indigo-500' : 'bg-gray-300' }}">{{ $stats['total'] }}</span>
                </a>
                <a href="{{ route('operator.deliveries.index', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-lg font-medium transition {{ request('status') == 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-clock mr-2"></i>Pending
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ request('status') == 'pending' ? 'bg-yellow-500' : 'bg-gray-300' }}">{{ $stats['pending'] }}</span>
                </a>
                <a href="{{ route('operator.deliveries.index', ['status' => 'in_transit']) }}" 
                   class="px-4 py-2 rounded-lg font-medium transition {{ request('status') == 'in_transit' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-plane-departure mr-2"></i>In Flight
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ request('status') == 'in_transit' ? 'bg-blue-500' : 'bg-gray-300' }}">{{ $stats['in_transit'] }}</span>
                </a>
                <a href="{{ route('operator.deliveries.index', ['status' => 'delivered']) }}" 
                   class="px-4 py-2 rounded-lg font-medium transition {{ request('status') == 'delivered' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-check-circle mr-2"></i>Delivered
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ request('status') == 'delivered' ? 'bg-green-500' : 'bg-gray-300' }}">{{ $stats['delivered'] }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Deliveries grid --}}
    @forelse($deliveries as $delivery)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-all duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    {{-- Left: Delivery info --}}
                    <div class="flex-1 space-y-3">
                        <div class="flex items-start gap-4">
                            {{-- Status icon --}}
                            <div class="flex-shrink-0">
                                @if($delivery->status === 'pending')
                                    <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-clock text-2xl text-yellow-600"></i>
                                    </div>
                                @elseif($delivery->status === 'in_transit')
                                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center animate-pulse">
                                        <i class="fas fa-plane-departure text-2xl text-blue-600"></i>
                                    </div>
                                @elseif($delivery->status === 'delivered')
                                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                                    </div>
                                @else
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-box text-2xl text-gray-600"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Delivery details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $delivery->delivery_number }}</h3>
                                    
                                    {{-- Status badge --}}
                                    @if($delivery->status === 'pending')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        </span>
                                    @elseif($delivery->status === 'in_transit')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-plane-departure mr-1"></i>In Flight
                                        </span>
                                    @elseif($delivery->status === 'delivered')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Delivered
                                        </span>
                                    @elseif($delivery->status === 'completed')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check-double mr-1"></i>Completed
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($delivery->status) }}
                                        </span>
                                    @endif

                                    {{-- Priority badge --}}
                                    @if($delivery->deliveryRequest && $delivery->deliveryRequest->priority === 'emergency')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 animate-pulse">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>EMERGENCY
                                        </span>
                                    @elseif($delivery->deliveryRequest && $delivery->deliveryRequest->priority === 'high')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            <i class="fas fa-arrow-up mr-1"></i>High Priority
                                        </span>
                                    @endif
                                </div>

                                {{-- Hospital --}}
                                <p class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-hospital text-teal-600 mr-2"></i>
                                    <span class="font-medium">{{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}</span>
                                </p>

                                {{-- Drone --}}
                                @if($delivery->drone)
                                <p class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-drone text-indigo-600 mr-2"></i>
                                    <span class="font-medium">{{ $delivery->drone->name }}</span>
                                    <span class="text-gray-500 ml-2">Battery: {{ $delivery->drone->current_battery_level }}%</span>
                                </p>
                                @endif

                                {{-- Schedule --}}
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                                    <span class="font-medium">Scheduled:</span> {{ $delivery->scheduled_departure_time->format('M d, Y H:i') }}
                                    @if($delivery->estimated_arrival_time)
                                        <span class="text-gray-500 ml-2">ETA: {{ $delivery->estimated_arrival_time->format('H:i') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex lg:flex-col gap-2">
                        <a href="{{ route('operator.deliveries.show', $delivery) }}" 
                           class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow hover:shadow-lg flex items-center justify-center gap-2 group">
                            <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                            <span>View Details</span>
                        </a>

                        @if($delivery->status === 'pending')
                            <button onclick="startDelivery({{ $delivery->id }})"
                                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-200 shadow hover:shadow-lg flex items-center justify-center gap-2 group">
                                <i class="fas fa-rocket group-hover:scale-110 transition-transform"></i>
                                <span>Start Delivery</span>
                            </button>
                        @elseif($delivery->status === 'in_transit')
                            <button onclick="completeDelivery({{ $delivery->id }})"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-200 shadow hover:shadow-lg flex items-center justify-center gap-2 group">
                                <i class="fas fa-check group-hover:scale-110 transition-transform"></i>
                                <span>Mark Delivered</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-inbox text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Deliveries Found</h3>
            <p class="text-gray-600">You don't have any {{ request('status') ? request('status') : '' }} deliveries assigned yet.</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($deliveries->hasPages())
        <div class="bg-white rounded-lg shadow p-4">
            {{ $deliveries->links() }}
        </div>
    @endif
</div>

{{-- Start Delivery Modal --}}
<div id="startModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto mb-4">
                <i class="fas fa-rocket text-3xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Start Delivery?</h3>
            <p class="text-gray-600 text-center mb-6">Are you ready to begin this delivery? This will mark the departure time and set the drone in flight.</p>
            
            <form id="startForm" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('startModal')"
                            class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-rocket mr-2"></i>Start
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Complete Delivery Modal --}}
<div id="completeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4">
                <i class="fas fa-check-circle text-3xl text-blue-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Mark as Delivered?</h3>
            <p class="text-gray-600 text-center mb-6">Confirm that the delivery has been successfully completed and received.</p>
            
            <form id="completeForm" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('completeModal')"
                            class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-check mr-2"></i>Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function startDelivery(id) {
    const modal = document.getElementById('startModal');
    const form = document.getElementById('startForm');
    form.action = `/operator/deliveries/${id}/start`;
    modal.classList.remove('hidden');
}

function completeDelivery(id) {
    const modal = document.getElementById('completeModal');
    const form = document.getElementById('completeForm');
    form.action = `/operator/deliveries/${id}/mark-delivered`;
    modal.classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal on outside click
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>
@endpush
@endsection
