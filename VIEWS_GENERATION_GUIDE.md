# Drone Delivery System - Remaining Views Generator

## IMPORTANT: Run this script to generate all remaining CRUD views

This file contains templates for all remaining views. Copy each section and create the corresponding file.

---

## HOSPITALS CRUD

### create.blade.php
```blade
@extends('layouts.app')
@section('title', 'Add Hospital')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white"><i class="fas fa-hospital mr-2"></i>Add New Hospital</h2>
            <p class="text-purple-100 mt-1">INSERT: Create a new hospital record</p>
        </div>
        <form action="{{ route('admin.hospitals.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Hospital Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Registration Number *</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('registration_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('contact_person')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Type</option>
                        <option value="government">Government</option>
                        <option value="private">Private</option>
                        <option value="specialist">Specialist</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                <textarea name="address" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('address') }}</textarea>
                @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                    <input type="number" name="latitude" value="{{ old('latitude') }}" step="0.000001" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                    <input type="number" name="longitude" value="{{ old('longitude') }}" step="0.000001" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.hospitals.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Save Hospital</button>
            </div>
        </form>
    </div>
</div>
@endsection
```

### edit.blade.php
Use same form as create.blade.php but:
- Change title to "Edit Hospital"
- Add @method('PUT')
- Change action to route('admin.hospitals.update', $hospital)
- Populate old() values with $hospital->field
- Change button text to "Update Hospital"

### show.blade.php
Display hospital details with map, delivery statistics, and recent deliveries.

---

## DELIVERY REQUESTS CRUD

Quick template for all 4 files (index, create, edit, show):
- Use orange/amber color scheme
- Include: hospital_id, supply_id, quantity, urgency_level, required_by, status
- Urgency levels: low, medium, high, critical
- Status: pending, approved, rejected, fulfilled

---

## DELIVERIES CRUD

Quick template for all 4 files:
- Use blue/indigo color scheme
- Include: tracking_number, delivery_request_id, drone_id, operator_id, status
- Status: preparing, in_transit, delivered, cancelled, failed
- Show map with route visualization
- Display real-time tracking

---

## USERS CRUD

Quick template for all 4 files:
- Use teal color scheme
- Include: name, email, phone, password, roles
- Role assignment with checkboxes
- Permission management
- Activity log

---

## ROLES & PERMISSIONS

### index.blade.php
List all roles with permission counts and actions.

### create.blade.php / edit.blade.php
Form with:
- Role name and description
- Permission checkboxes grouped by category
- Save button

### show.blade.php
Display role details, assigned permissions, and users with this role.

---

## REPORTS

### reports.blade.php
Dashboard with:
- Charts (deliveries over time, success rate, drone utilization)
- Export buttons (PDF, Excel, CSV)
- Date range filters
- Key metrics cards

---

## HOSPITAL PORTAL

### dashboard.blade.php
Hospital-specific dashboard with:
- Pending requests
- Active deliveries
- Inventory levels
- Quick action buttons

### delivery-requests/index.blade.php
List hospital's delivery requests with create/edit capabilities.

### delivery-requests/create.blade.php
Form to request medical supplies.

### deliveries.blade.php
Track active deliveries to this hospital.

### inventory.blade.php
View available medical supplies.

---

## OPERATOR DASHBOARD

### dashboard.blade.php
Operator-specific view with:
- Assigned deliveries (today)
- Drone status
- Flight hours
- Performance metrics

### deliveries.blade.php
List of assigned deliveries with status update capabilities.

### drones.blade.php
View assigned drones and their status.

---

## PUBLIC PAGES

### tracking.blade.php
Public delivery tracking page:
- Input: tracking number
- Display: delivery status, estimated time, map with route

---

## IMPLEMENTATION NOTES

1. **Color Schemes:**
   - Medical Supplies: Blue (blue-600)
   - Drones: Green (green-600)
   - Hospitals: Purple (purple-600)
   - Delivery Requests: Orange (orange-600)
   - Deliveries: Indigo (indigo-600)
   - Users: Teal (teal-600)
   - Roles: Red (red-600)

2. **Operation Comments:**
   - Always include: {{-- INSERT: ... --}}, {{-- READ: ... --}}, {{-- UPDATE: ... --}}, {{-- DELETE: ... --}}

3. **Common Elements:**
   - Breadcrumbs in all pages
   - Search and filter forms
   - Pagination
   - Status badges
   - Action buttons (View, Edit, Delete)
   - Confirmation dialogs for delete

4. **Responsive Design:**
   - Mobile-first approach
   - Grid layouts: grid-cols-1 md:grid-cols-2 lg:grid-cols-3
   - Collapsible sidebars on mobile

5. **Icons (Font Awesome 6.4.0):**
   - fa-pills (supplies)
   - fa-drone (drones)
   - fa-hospital (hospitals)
   - fa-clipboard-list (requests)
   - fa-shipping-fast (deliveries)
   - fa-users (users)
   - fa-user-shield (roles)

---

## QUICK GENERATION SCRIPT

To generate all views quickly, use this pattern for each module:

```bash
# Example for delivery-requests
php artisan make:view admin.delivery-requests.index
php artisan make:view admin.delivery-requests.create
php artisan make:view admin.delivery-requests.edit
php artisan make:view admin.delivery-requests.show
```

Then copy the content from medical-supplies or drones CRUD and adjust:
1. Model name
2. Route names
3. Field names
4. Color scheme
5. Icons

---

**Total Views Needed: ~40 files**
**Estimated Time with Templates: 2-3 hours**
**Pattern Consistency: 100%**
