{{-- Debug: Show user roles (Remove this after testing) --}}
@if(config('app.debug'))
    <div class="px-4 py-2 text-xs text-gray-400 border-b border-gray-700 mb-4">
        <div class="mb-1"><strong>Debug Info:</strong></div>
        <div>User: {{ auth()->user()->name }}</div>
        <div>Roles: {{ auth()->user()->roles->pluck('name')->join(', ') }}</div>
        <div>Slugs: {{ auth()->user()->roles->pluck('slug')->join(', ') }}</div>
    </div>
@endif

{{-- Admin Navigation --}}
@if(auth()->user()->roles->whereIn('slug', ['super_admin', 'admin'])->count() > 0)
    <div class="space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('admin.medical-supplies.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.medical-supplies.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-pills w-5"></i>
            <span class="ml-3">Medical Supplies</span>
        </a>

        <a href="{{ route('admin.drones.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.drones.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-drone w-5 {{ request()->routeIs('admin.drones.*') ? 'text-white' : 'text-gray-400' }}"></i>
            <span class="ml-3">Drones</span>
        </a>

        <a href="{{ route('admin.hospitals.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.hospitals.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-hospital w-5"></i>
            <span class="ml-3">Hospitals</span>
        </a>

        <a href="{{ route('admin.delivery-requests.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.delivery-requests.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-clipboard-list w-5"></i>
            <span class="ml-3">Delivery Requests</span>
        </a>

        <a href="{{ route('admin.deliveries.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.deliveries.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-shipping-fast w-5"></i>
            <span class="ml-3">Deliveries</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-users w-5"></i>
            <span class="ml-3">Users</span>
        </a>

        <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-user-shield w-5"></i>
            <span class="ml-3">Roles & Permissions</span>
        </a>

        <a href="{{ route('admin.reports') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.reports') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-chart-bar w-5"></i>
            <span class="ml-3">Reports</span>
        </a>
    </div>
@endif

{{-- Hospital Portal Navigation --}}
@if(auth()->user()->roles->whereIn('slug', ['hospital_admin', 'hospital_staff'])->count() > 0)
    <div class="space-y-1">
        <a href="{{ route('hospital.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.dashboard') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('hospital.requests.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.requests.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-clipboard-list w-5"></i>
            <span class="ml-3">Delivery Requests</span>
        </a>

        <a href="{{ route('hospital.deliveries.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.deliveries.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-shipping-fast w-5"></i>
            <span class="ml-3">Active Deliveries</span>
        </a>

        <a href="{{ route('hospital.history') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.history') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-history w-5"></i>
            <span class="ml-3">Delivery History</span>
        </a>

        <a href="{{ route('hospital.notifications.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.notifications.*') ? 'bg-teal-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-bell w-5"></i>
            <span class="ml-3">Notifications</span>
        </a>
    </div>
@endif

{{-- Drone Operator Navigation --}}
@if(auth()->user()->roles->where('slug', 'drone_operator')->count() > 0)
    <div class="space-y-1">
        <a href="{{ route('operator.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('operator.dashboard') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('operator.deliveries.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('operator.deliveries.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tasks w-5"></i>
            <span class="ml-3">My Deliveries</span>
        </a>

        <a href="{{ route('operator.drones.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('operator.drones.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-drone w-5 {{ request()->routeIs('operator.drones.*') ? 'text-white' : 'text-gray-400' }}"></i>
            <span class="ml-3">My Drones</span>
        </a>
    </div>
@endif

{{-- Logout Link (Always visible) --}}
<div class="mt-8 pt-4 border-t border-gray-700">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition-colors">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span class="ml-3">Logout</span>
        </button>
    </form>
</div>
