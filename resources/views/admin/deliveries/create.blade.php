@extends('layouts.app')

@section('title', 'Create Delivery')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-4">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <a href="{{ route('admin.deliveries.index') }}" class="hover:text-blue-600">Deliveries</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <span class="text-gray-900">Create Delivery</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Create New Delivery</h1>
        <p class="text-gray-600 mt-1">Create a new drone delivery from an approved delivery request</p>
    </div>

    <form action="{{ route('admin.deliveries.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Delivery Request Selection -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                Delivery Request
            </h2>

            <div>
                <label for="delivery_request_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Delivery Request <span class="text-red-500">*</span>
                </label>
                <select name="delivery_request_id" id="delivery_request_id" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('delivery_request_id') border-red-500 @enderror">
                    <option value="">-- Select Delivery Request --</option>
                    @foreach($deliveryRequests ?? [] as $request)
                    <option value="{{ $request->id }}" {{ old('delivery_request_id') == $request->id ? 'selected' : '' }}>
                        #{{ $request->request_number }} - {{ $request->hospital->name }} - {{ ucfirst($request->priority) }} Priority
                    </option>
                    @endforeach
                </select>
                @error('delivery_request_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Only approved delivery requests are shown</p>
            </div>
        </div>

        <!-- Drone Assignment -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-helicopter text-blue-600 mr-2"></i>
                Drone Assignment
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="drone_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Drone <span class="text-red-500">*</span>
                    </label>
                    <select name="drone_id" id="drone_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('drone_id') border-red-500 @enderror">
                        <option value="">-- Select Drone --</option>
                        @foreach($availableDrones ?? [] as $drone)
                        <option value="{{ $drone->id }}" {{ old('drone_id') == $drone->id ? 'selected' : '' }}>
                            {{ $drone->name }} ({{ $drone->model }}) - Battery: {{ $drone->battery_level }}%
                        </option>
                        @endforeach
                    </select>
                    @error('drone_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Only available drones are shown</p>
                </div>

                <div>
                    <label for="assigned_pilot_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Assign Pilot (Optional)
                    </label>
                    <select name="assigned_pilot_id" id="assigned_pilot_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Auto-assign or Manual Control --</option>
                        @foreach($pilots ?? [] as $pilot)
                        <option value="{{ $pilot->id }}" {{ old('assigned_pilot_id') == $pilot->id ? 'selected' : '' }}>
                            {{ $pilot->name }} - {{ $pilot->email }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                Schedule
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="scheduled_departure_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Scheduled Departure Time <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="scheduled_departure_time" id="scheduled_departure_time" 
                           value="{{ old('scheduled_departure_time') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('scheduled_departure_time') border-red-500 @enderror">
                    @error('scheduled_departure_time')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_arrival_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Arrival Time
                    </label>
                    <input type="datetime-local" name="estimated_arrival_time" id="estimated_arrival_time" 
                           value="{{ old('estimated_arrival_time') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-gray-500 text-sm mt-1">Will be calculated automatically if left empty</p>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Additional Details
            </h2>

            <div class="space-y-4">
                <div>
                    <label for="special_handling_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Special Handling Notes
                    </label>
                    <textarea name="special_handling_notes" id="special_handling_notes" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('special_handling_notes') }}</textarea>
                    <p class="text-gray-500 text-sm mt-1">Any special instructions for handling the medical supplies</p>
                </div>

                <div>
                    <label for="pilot_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilot Notes
                    </label>
                    <textarea name="pilot_notes" id="pilot_notes" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('pilot_notes') }}</textarea>
                    <p class="text-gray-500 text-sm mt-1">Internal notes for the drone operator</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="requires_return_trip" id="requires_return_trip" value="1" 
                           {{ old('requires_return_trip') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="requires_return_trip" class="ml-2 block text-sm text-gray-900">
                        This delivery requires a return trip
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center bg-gray-50 rounded-lg p-6">
            <a href="{{ route('admin.deliveries.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-check mr-2"></i>Create Delivery
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Auto-calculate ETA based on departure time and estimated flight duration
document.getElementById('scheduled_departure_time').addEventListener('change', function() {
    const departureTime = new Date(this.value);
    if (departureTime && !document.getElementById('estimated_arrival_time').value) {
        // Assume average flight time of 30 minutes
        const arrivalTime = new Date(departureTime.getTime() + 30 * 60000);
        document.getElementById('estimated_arrival_time').value = arrivalTime.toISOString().slice(0, 16);
    }
});
</script>
@endsection
