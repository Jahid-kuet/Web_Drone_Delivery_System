@extends('layouts.app')

@section('title', 'Delivery Control Panel')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('operator.deliveries.index') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Deliveries
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Delivery Control Panel</h1>
        <p class="text-gray-600 mt-1">Manage and monitor this delivery</p>
    </div>

    <!-- Delivery Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tracking #: DR-2024-0001</h2>
                <p class="text-gray-600 mt-1">Assigned to you</p>
            </div>
            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-medium">Pending</span>
        </div>
    </div>

    <!-- Pre-Flight Checklist -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6" x-data="{ checklist: [] }">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Pre-Flight Checklist</h3>
        <div class="space-y-3">
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Battery fully charged (100%)</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Weather conditions acceptable</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Cargo secured properly</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">GPS signal strong</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="w-5 h-5 text-blue-600 rounded">
                <span class="ml-3 text-gray-700">Communication systems operational</span>
            </label>
        </div>
    </div>

    <!-- Delivery Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Hospital</p>
                    <p class="text-gray-900 font-medium">Hospital Name</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Scheduled Departure</p>
                    <p class="text-gray-900">Not scheduled</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estimated Arrival</p>
                    <p class="text-gray-900">Not calculated</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Drone Status</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Drone Model</p>
                    <p class="text-gray-900 font-medium">Model Name</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Battery Level</p>
                    <p class="text-gray-900">100%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Available</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-wrap gap-4">
            @if(isset($delivery) && $delivery->status === 'pending')
                <form action="{{ route('operator.deliveries.start', $delivery->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">
                        <i class="fas fa-play mr-2"></i>Start Delivery
                    </button>
                </form>
            @elseif(isset($delivery) && $delivery->status === 'in_transit')
                <form action="{{ route('operator.deliveries.mark-delivered', $delivery->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                        <i class="fas fa-check mr-2"></i>Mark as Delivered
                    </button>
                </form>
            @endif
            
            @if(isset($delivery) && in_array($delivery->status, ['pending', 'in_transit']))
                <button onclick="showCancelModal()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg flex-1">
                    <i class="fas fa-times mr-2"></i>Cancel Delivery
                </button>
                <button onclick="showIncidentModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg flex-1">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Report Issue
                </button>
            @endif
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Delivery</h3>
            <form action="{{ isset($delivery) ? route('operator.deliveries.cancel', $delivery->id) : '#' }}" method="POST">
                @csrf
                <textarea name="cancellation_reason" rows="4" required placeholder="Please provide a reason for cancellation..." 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Incident Modal -->
    <div id="incidentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Report Incident</h3>
            <form action="{{ isset($delivery) ? route('operator.deliveries.report-incident', $delivery->id) : '#' }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Incident Type</label>
                        <select name="incident_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select Type</option>
                            <option value="weather">Weather Condition</option>
                            <option value="technical">Technical Issue</option>
                            <option value="battery">Battery Problem</option>
                            <option value="obstacle">Obstacle/No-Fly Zone</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                        <select name="incident_severity" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="incident_description" rows="4" required placeholder="Describe the incident..." 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeIncidentModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }
        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }
        function showIncidentModal() {
            document.getElementById('incidentModal').classList.remove('hidden');
        }
        function closeIncidentModal() {
            document.getElementById('incidentModal').classList.add('hidden');
        }
    </script>
</div>
@endsection
