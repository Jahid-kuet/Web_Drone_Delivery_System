@extends('layouts.app')

@section('title', 'Delivery Details')

@section('breadcrumb')
    <a href="{{ route('operator.deliveries.index') }}" class="text-gray-600 hover:text-gray-900">
        <i class="fas fa-shipping-fast mr-2"></i>My Deliveries
    </a>
    <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
    <span>{{ $delivery->delivery_number }}</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header with status and actions --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $delivery->delivery_number }}</h1>
                <p class="text-indigo-100">
                    <i class="fas fa-hospital mr-2"></i>{{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}
                </p>
            </div>
            
            <div class="flex flex-col gap-2">
                {{-- Status badge --}}
                @if($delivery->status === 'pending')
                    <span class="px-4 py-2 bg-yellow-500 text-white rounded-lg font-semibold text-center">
                        <i class="fas fa-clock mr-2"></i>Pending
                    </span>
                @elseif($delivery->status === 'in_transit')
                    <span class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold text-center animate-pulse">
                        <i class="fas fa-plane-departure mr-2"></i>In Flight
                    </span>
                @elseif($delivery->status === 'delivered')
                    <span class="px-4 py-2 bg-green-500 text-white rounded-lg font-semibold text-center">
                        <i class="fas fa-check-circle mr-2"></i>Delivered
                    </span>
                @elseif($delivery->status === 'completed')
                    <span class="px-4 py-2 bg-emerald-500 text-white rounded-lg font-semibold text-center">
                        <i class="fas fa-check-double mr-2"></i>Completed
                    </span>
                @else
                    <span class="px-4 py-2 bg-gray-500 text-white rounded-lg font-semibold text-center">
                        {{ ucfirst($delivery->status) }}
                    </span>
                @endif

                {{-- Priority --}}
                @if($delivery->deliveryRequest && $delivery->deliveryRequest->priority === 'emergency')
                    <span class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold text-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>EMERGENCY
                    </span>
                @elseif($delivery->deliveryRequest && $delivery->deliveryRequest->priority === 'high')
                    <span class="px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold text-center">
                        <i class="fas fa-arrow-up mr-2"></i>High Priority
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Main action buttons --}}
    @if(in_array($delivery->status, ['pending', 'in_transit']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @if($delivery->status === 'pending')
            <button onclick="showStartModal()"
                    class="px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 group">
                <i class="fas fa-rocket text-2xl group-hover:scale-110 transition-transform"></i>
                <span>Start Delivery</span>
            </button>
        @endif

        @if($delivery->status === 'in_transit')
            <button onclick="showCompleteModal()"
                    class="px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 group">
                <i class="fas fa-check-circle text-2xl group-hover:scale-110 transition-transform"></i>
                <span>Mark Delivered</span>
            </button>
        @endif

        <button onclick="showIncidentModal()"
                class="px-6 py-4 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 group">
            <i class="fas fa-exclamation-triangle text-2xl group-hover:scale-110 transition-transform"></i>
            <span>Report Issue</span>
        </button>

        <button onclick="showCancelModal()"
                class="px-6 py-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 group">
            <i class="fas fa-times-circle text-2xl group-hover:scale-110 transition-transform"></i>
            <span>Cancel Delivery</span>
        </button>
    </div>
    @endif

    {{-- Delivery details grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Column 1: Delivery Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-box text-indigo-600 mr-2"></i>Delivery Information
            </h2>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Tracking Number</p>
                    <p class="text-gray-900 font-semibold">{{ $delivery->delivery_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Hospital</p>
                    <p class="text-gray-900 font-medium">{{ $delivery->deliveryRequest->hospital->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Scheduled Departure</p>
                    <p class="text-gray-900">{{ $delivery->scheduled_departure_time->format('M d, Y H:i') }}</p>
                </div>
                @if($delivery->actual_departure_time)
                <div>
                    <p class="text-sm text-gray-600">Actual Departure</p>
                    <p class="text-gray-900">{{ $delivery->actual_departure_time->format('M d, Y H:i') }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600">Estimated Arrival</p>
                    <p class="text-gray-900">{{ $delivery->estimated_arrival_time->format('H:i') }}</p>
                </div>
                @if($delivery->actual_arrival_time)
                <div>
                    <p class="text-sm text-gray-600">Actual Arrival</p>
                    <p class="text-gray-900">{{ $delivery->actual_arrival_time->format('M d, Y H:i') }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600">Distance</p>
                    <p class="text-gray-900">{{ number_format($delivery->total_distance_km, 2) }} km</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Priority</p>
                    @if($delivery->deliveryRequest->priority === 'emergency')
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Emergency</span>
                    @elseif($delivery->deliveryRequest->priority === 'high')
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">High</span>
                    @elseif($delivery->deliveryRequest->priority === 'medium')
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Medium</span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Normal</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Column 2: Drone Status --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-drone text-indigo-600 mr-2"></i>Drone Status
            </h2>
            @if($delivery->drone)
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Drone Name</p>
                    <p class="text-gray-900 font-semibold">{{ $delivery->drone->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Model</p>
                    <p class="text-gray-900">{{ $delivery->drone->model }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Battery Level</p>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $delivery->drone->current_battery_level > 50 ? 'bg-green-500' : ($delivery->drone->current_battery_level > 20 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $delivery->drone->current_battery_level }}%"></div>
                        </div>
                        <span class="font-semibold">{{ $delivery->drone->current_battery_level }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    @if($delivery->drone->status === 'available')
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                    @elseif($delivery->drone->status === 'in_flight')
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">In Flight</span>
                    @elseif($delivery->drone->status === 'charging')
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Charging</span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($delivery->drone->status) }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-600">Max Payload</p>
                    <p class="text-gray-900">{{ $delivery->drone->max_payload_kg }} kg</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Max Range</p>
                    <p class="text-gray-900">{{ $delivery->drone->max_range_km }} km</p>
                </div>
            </div>
            @else
            <p class="text-gray-500 text-center py-8">No drone assigned</p>
            @endif
        </div>

        {{-- Column 3: Timeline --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-clock text-indigo-600 mr-2"></i>Timeline
            </h2>
            <div class="space-y-4">
                {{-- Request Created --}}
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <div class="w-0.5 h-full bg-gray-300"></div>
                    </div>
                    <div class="pb-4">
                        <p class="font-semibold text-gray-900">Request Created</p>
                        <p class="text-sm text-gray-600">{{ $delivery->created_at->format('M d, H:i') }}</p>
                    </div>
                </div>

                {{-- Scheduled --}}
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 {{ $delivery->status !== 'pending' ? 'bg-green-500' : 'bg-gray-300' }} rounded-full"></div>
                        <div class="w-0.5 h-full bg-gray-300"></div>
                    </div>
                    <div class="pb-4">
                        <p class="font-semibold text-gray-900">Scheduled</p>
                        <p class="text-sm text-gray-600">{{ $delivery->scheduled_departure_time->format('M d, H:i') }}</p>
                    </div>
                </div>

                {{-- In Transit --}}
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 {{ in_array($delivery->status, ['in_transit', 'delivered', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full"></div>
                        <div class="w-0.5 h-full bg-gray-300"></div>
                    </div>
                    <div class="pb-4">
                        <p class="font-semibold text-gray-900">In Transit</p>
                        @if($delivery->actual_departure_time)
                            <p class="text-sm text-gray-600">{{ $delivery->actual_departure_time->format('M d, H:i') }}</p>
                        @else
                            <p class="text-sm text-gray-400">Not started</p>
                        @endif
                    </div>
                </div>

                {{-- Delivered --}}
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 {{ in_array($delivery->status, ['delivered', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full"></div>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Delivered</p>
                        @if($delivery->actual_arrival_time)
                            <p class="text-sm text-gray-600">{{ $delivery->actual_arrival_time->format('M d, H:i') }}</p>
                        @else
                            <p class="text-sm text-gray-400">Pending</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Start Delivery Modal --}}
<div id="startModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto mb-4">
                <i class="fas fa-rocket text-3xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Start Delivery?</h3>
            <p class="text-gray-600 text-center mb-6">This will mark the departure time and set the drone in flight mode.</p>
            
            <form action="{{ route('operator.deliveries.start', $delivery) }}" method="POST">
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
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4">
                <i class="fas fa-check-circle text-3xl text-blue-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Mark as Delivered?</h3>
            <p class="text-gray-600 text-center mb-6">Confirm that the delivery has been successfully completed.</p>
            
            <form action="{{ route('operator.deliveries.mark-delivered', $delivery) }}" method="POST">
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

{{-- Report Issue Modal --}}
<div id="incidentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-3xl text-yellow-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-6">Report Issue</h3>
            
            <form action="{{ route('operator.deliveries.report-incident', $delivery) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Issue Type</label>
                        <select name="incident_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                            <option value="">Select type...</option>
                            <option value="weather">Weather Condition</option>
                            <option value="technical">Technical Issue</option>
                            <option value="battery">Battery Problem</option>
                            <option value="obstacle">Obstacle/No-Fly Zone</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                        <select name="incident_severity" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="incident_description" rows="4" required placeholder="Describe the issue in detail..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeModal('incidentModal')"
                            class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-paper-plane mr-2"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cancel Delivery Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-4">
                <i class="fas fa-times-circle text-3xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Cancel Delivery?</h3>
            <p class="text-gray-600 text-center mb-6">This action cannot be undone. Please provide a reason.</p>
            
            <form action="{{ route('operator.deliveries.cancel', $delivery) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason</label>
                        <textarea name="cancellation_reason" rows="4" required placeholder="Please provide a detailed reason for cancellation..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeModal('cancelModal')"
                            class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                        Keep Delivery
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-times mr-2"></i>Cancel Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showStartModal() {
    document.getElementById('startModal').classList.remove('hidden');
}
function showCompleteModal() {
    document.getElementById('completeModal').classList.remove('hidden');
}
function showIncidentModal() {
    document.getElementById('incidentModal').classList.remove('hidden');
}
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close on outside click
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});
</script>
@endpush
@endsection
