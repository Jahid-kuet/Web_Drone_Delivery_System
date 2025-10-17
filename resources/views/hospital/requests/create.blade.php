@extends('layouts.app')

@section('title', 'Request New Delivery')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <div class="mb-6">
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <a href="{{ route('hospital.dashboard') }}" class="hover:text-teal-600 transition">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <a href="{{ route('hospital.requests.index') }}" class="hover:text-teal-600 transition">
                    Delivery Requests
                </a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-gray-900">New Request</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Request New Delivery</h1>
                    <p class="text-gray-600 mt-2">Fill out the form to request a drone delivery for medical supplies</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-plus-circle text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-1"></i>
                <div class="flex-1">
                    <p class="text-red-800 font-semibold mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('hospital.requests.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl overflow-hidden" id="deliveryRequestForm">
            @csrf

            <div class="p-8 space-y-8">
                
                {{-- Basic Information --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-teal-600"></i>
                        </div>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Delivery Location (Hospital in Khulna) --}}
                        <div>
                            <label for="delivery_hospital_input" class="block text-sm font-medium text-gray-700 mb-2">
                                Delivery Hospital (Khulna District) <span class="text-red-500">*</span>
                            </label>
                            <input list="khulnaHospitalsList" id="delivery_hospital_input" name="delivery_hospital_input" value="{{ old('delivery_hospital_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('delivery_hospital_name') border-red-500 @enderror"
                                placeholder="Type or choose a hospital in Khulna">
                            <datalist id="khulnaHospitalsList">
                                @foreach($khulnaHospitals as $khulnaHospital)
                                <option value="{{ $khulnaHospital->name }}">{{ $khulnaHospital->address }}</option>
                                @endforeach
                            </datalist>
                            {{-- hidden id when a known hospital is selected --}}
                            <input type="hidden" name="delivery_hospital_id" id="delivery_hospital_id" value="{{ old('delivery_hospital_id') }}">
                            {{-- hidden canonical fields that the server expects (will be synced by JS) --}}
                            <input type="hidden" name="delivery_hospital_name" id="delivery_hospital_name_hidden" value="{{ old('delivery_hospital_name') }}">
                            <input type="hidden" name="delivery_hospital_address" id="delivery_hospital_address_hidden" value="{{ old('delivery_hospital_address') }}">
                            <div class="mt-2">
                                <button type="button" id="manualEntryBtn" class="text-sm text-teal-600 hover:underline">Enter address manually</button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Choose from known Khulna hospitals or type a hospital name/address to enter manually
                            </p>
                            @error('delivery_hospital_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Priority --}}
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                Priority Level <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" id="priority" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('priority') border-red-500 @enderror">
                                <option value="">Select Priority</option>
                                <option value="emergency" {{ old('priority') == 'emergency' ? 'selected' : '' }}>
                                    🔴 Emergency - Critical/Life-threatening
                                </option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                    🟠 High - Urgent within 24 hours
                                </option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                    🟡 Medium - Required within 2-3 days
                                </option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                    🟢 Low - Standard delivery
                                </option>
                            </select>
                            @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Requested Date --}}
                        <div>
                            <label for="requested_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Requested Delivery Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="requested_date" id="requested_date" required
                                value="{{ old('requested_date', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('requested_date') border-red-500 @enderror">
                            @error('requested_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Request Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4" required
                            placeholder="Provide details about this delivery request..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Manual hospital entry (hidden unless 'Other' selected) --}}
                <div id="manualHospitalBlock" class="border-t pt-8 hidden" style="position:relative; z-index:9999; pointer-events:auto;">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Enter Destination Hospital Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="delivery_hospital_name_manual" class="block text-sm font-medium text-gray-700 mb-2">Hospital Name</label>
                            <input type="text" name="delivery_hospital_name_manual" id="delivery_hospital_name_manual" value="{{ old('delivery_hospital_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg" autocomplete="organization" disabled>
                            @error('delivery_hospital_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="delivery_hospital_address_manual" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="delivery_hospital_address_manual" id="delivery_hospital_address_manual" value="{{ old('delivery_hospital_address') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg" autocomplete="street-address" disabled>
                            @error('delivery_hospital_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="border-t pt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        Contact Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Contact Person --}}
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person Name
                            </label>
                            <input type="text" name="contact_person" id="contact_person"
                                value="{{ old('contact_person', Auth::user()->name) }}"
                                placeholder="Enter contact person name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                            @error('contact_person')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Phone --}}
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Phone Number
                            </label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                value="{{ old('contact_phone', Auth::user()->phone ?? '') }}"
                                placeholder="01XXXXXXXXX (11 digits)"
                                pattern="01[0-9]{9}"
                                maxlength="11"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-phone mr-1"></i>
                                Bangladesh mobile number (11 digits starting with 01)
                            </p>
                            @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Medical Supplies --}}
                <div class="border-t pt-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box-open text-purple-600"></i>
                            </div>
                            Medical Supplies <span class="text-red-500 ml-1">*</span>
                        </h2>
                        <button type="button" onclick="addSupplyRow()"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition flex items-center shadow-sm">
                            <i class="fas fa-plus mr-2"></i> Add Supply
                        </button>
                    </div>

                    <div id="suppliesContainer" class="space-y-4">
                        {{-- Initial supply row --}}
                        <div class="supply-row bg-gray-50 p-5 rounded-xl border border-gray-200">
                            <div class="flex items-start gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Medical Supply</label>
                                    <select name="supplies[0][supply_id]" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                                        <option value="">Select a supply</option>
                                        @foreach($medicalSupplies as $supply)
                                        <option value="{{ $supply->id }}" data-stock="{{ $supply->stock_quantity }}">
                                            {{ $supply->name }} ({{ $supply->category }}) - Stock: {{ $supply->stock_quantity }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-32">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                    <input type="number" name="supplies[0][quantity]" required min="1"
                                        placeholder="Qty"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                                </div>
                                <div class="pt-8">
                                    <button type="button" onclick="removeSupplyRow(this)"
                                        class="px-3 py-3 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('supplies')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Special Instructions --}}
                <div class="border-t pt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clipboard-list text-yellow-600"></i>
                        </div>
                        Additional Details
                    </h2>
                    
                    <div>
                        <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                            Special Instructions
                        </label>
                        <textarea name="special_instructions" id="special_instructions" rows="4"
                            placeholder="Any special handling instructions, storage requirements, or additional notes..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">{{ old('special_instructions') }}</textarea>
                        @error('special_instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Delivery Location Preview (select destination hospital from Khulna list) --}}
                <div id="deliveryLocationPreview" class="bg-teal-50 p-6 rounded-xl border border-teal-200">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-teal-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div class="flex-1" id="deliveryLocationContent">
                            <h3 class="font-semibold text-gray-900 mb-2">Delivery Location</h3>
                            {{-- Default message if none selected --}}
                            <p class="text-gray-700" id="dl-name">No destination selected</p>
                            <p class="text-sm text-gray-600 mt-1" id="dl-address"></p>
                            <p class="text-xs text-gray-500 mt-1" id="dl-coords"></p>
                        </div>
                    </div>
                </div>

                <script>
                    // Prepare Khulna hospitals JSON server-side to avoid Blade parse issues
                    @php
                        $khulnaHospitalsJs = $khulnaHospitals->map(function($h) {
                            return [
                                'id' => $h->id,
                                'name' => $h->name,
                                'address' => $h->address,
                                'latitude' => $h->latitude,
                                'longitude' => $h->longitude,
                            ];
                        })->values()->toJson();
                    @endphp

                    const KHULNA_HOSPITALS = {!! $khulnaHospitalsJs !!};

                    function findHospitalByName(name) {
                        if (!name) return null;
                        const lower = String(name).trim().toLowerCase();
                        return KHULNA_HOSPITALS.find(h => String(h.name).trim().toLowerCase() === lower) || null;
                    }

                    function findHospitalById(id) {
                        if (!id) return null;
                        for (let i = 0; i < KHULNA_HOSPITALS.length; i++) {
                            if (String(KHULNA_HOSPITALS[i].id) === String(id)) return KHULNA_HOSPITALS[i];
                        }
                        return null;
                    }

                    function updateHospitalPreview(selectedId) {
                        const nameEl = document.getElementById('dl-name');
                        const addressEl = document.getElementById('dl-address');
                        const coordsEl = document.getElementById('dl-coords');

                        const hospital = findHospitalById(selectedId);
                        if (!hospital) {
                            nameEl.textContent = 'No destination selected';
                            addressEl.textContent = '';
                            coordsEl.textContent = '';
                            return;
                        }

                        nameEl.textContent = hospital.name;
                        addressEl.textContent = hospital.address || '';
                        if (hospital.latitude && hospital.longitude) {
                            coordsEl.innerHTML = '<i class="fas fa-location-dot mr-1"></i> Coordinates: ' + hospital.latitude + ', ' + hospital.longitude;
                        } else {
                            coordsEl.textContent = '';
                        }
                    }

                    // Attach change listener
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('delivery_hospital_input');
                        const hiddenId = document.getElementById('delivery_hospital_id');
                        const hiddenName = document.getElementById('delivery_hospital_name_hidden');
                        const hiddenAddress = document.getElementById('delivery_hospital_address_hidden');
                        const manualBlock = document.getElementById('manualHospitalBlock');
                        const manualName = document.getElementById('delivery_hospital_name_manual');
                        const manualAddress = document.getElementById('delivery_hospital_address_manual');

                        function setManualBlockVisible(visible) {
                            if (!manualBlock) return;
                            if (visible) {
                                manualBlock.classList.remove('hidden');
                                manualName.removeAttribute('disabled');
                                manualAddress.removeAttribute('disabled');
                            } else {
                                manualBlock.classList.add('hidden');
                                manualName.setAttribute('disabled', 'disabled');
                                manualAddress.setAttribute('disabled', 'disabled');
                            }
                        }

                        if (input) {
                            // On input blur or change, try to match a known hospital by name
                            input.addEventListener('change', function(e) {
                                const val = e.target.value || '';
                                const matched = findHospitalByName(val);
                                if (matched) {
                                    hiddenId.value = matched.id;
                                    hiddenName.value = matched.name;
                                    hiddenAddress.value = matched.address || '';
                                    updateHospitalPreview(matched.id);
                                    setManualBlockVisible(false);
                                } else {
                                    // No exact match: clear id and let user optionally fill manual block
                                    hiddenId.value = '';
                                    hiddenName.value = val;
                                    hiddenAddress.value = '';
                                    updateHospitalPreview(null);
                                    // keep manual block hidden until user requests it
                                }
                            });

                            // Initialize from old values
                            const initialId = hiddenId.value || '{{ old('delivery_hospital_id') }}';
                            if (initialId) {
                                updateHospitalPreview(initialId);
                                setManualBlockVisible(false);
                            }

                            const oldManualName = {!! json_encode(old('delivery_hospital_name')) !!};
                            if (oldManualName && (!initialId)) {
                                setManualBlockVisible(true);
                                manualName.value = oldManualName;
                                manualAddress.value = {!! json_encode(old('delivery_hospital_address')) !!} || '';
                                manualName.removeAttribute('disabled');
                                manualAddress.removeAttribute('disabled');
                            }

                            const manualBtn = document.getElementById('manualEntryBtn');
                            if (manualBtn) {
                                manualBtn.addEventListener('click', function() {
                                    hiddenId.value = '';
                                    setManualBlockVisible(true);
                                    manualName.focus();
                                });
                            }

                            // Before submit, ensure canonical hidden fields are populated
                            const form = document.getElementById('deliveryRequestForm');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    if (manualBlock && !manualBlock.classList.contains('hidden')) {
                                        // manual block visible: copy manual inputs into hidden canonical fields
                                        hiddenName.value = manualName.value || hiddenName.value || '';
                                        hiddenAddress.value = manualAddress.value || hiddenAddress.value || '';
                                    } else {
                                        // manual block hidden: copy typed input into hidden name
                                        hiddenName.value = input.value || hiddenName.value || '';
                                    }
                                });
                            }
                        }
                    });
                </script>

            </div>

            {{-- Form Actions --}}
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex items-center justify-between">
                <a href="{{ route('hospital.requests.index') }}"
                    class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-700 hover:to-blue-700 text-white rounded-lg transition flex items-center shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Request
                </button>
            </div>
        </form>

    </div>
</div>

<script>
let supplyRowIndex = 1;

function addSupplyRow() {
    const container = document.getElementById('suppliesContainer');
    const newRow = document.createElement('div');
    newRow.className = 'supply-row bg-gray-50 p-5 rounded-xl border border-gray-200';
    newRow.innerHTML = `
        <div class="flex items-start gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Medical Supply</label>
                <select name="supplies[${supplyRowIndex}][supply_id]" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                    <option value="">Select a supply</option>
                    @foreach($medicalSupplies as $supply)
                    <option value="{{ $supply->id }}" data-stock="{{ $supply->stock_quantity }}">
                        {{ $supply->name }} ({{ $supply->category }}) - Stock: {{ $supply->stock_quantity }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" name="supplies[${supplyRowIndex}][quantity]" required min="1"
                    placeholder="Qty"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
            </div>
            <div class="pt-8">
                <button type="button" onclick="removeSupplyRow(this)"
                    class="px-3 py-3 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    supplyRowIndex++;
}

function removeSupplyRow(button) {
    const container = document.getElementById('suppliesContainer');
    const rows = container.querySelectorAll('.supply-row');
    
    if (rows.length > 1) {
        button.closest('.supply-row').remove();
    } else {
        alert('At least one supply item is required');
    }
}

// Form validation
document.getElementById('deliveryRequestForm').addEventListener('submit', function(e) {
    const supplies = document.querySelectorAll('.supply-row');
    if (supplies.length === 0) {
        e.preventDefault();
        alert('Please add at least one medical supply');
        return false;
    }
});
</script>

@endsection
