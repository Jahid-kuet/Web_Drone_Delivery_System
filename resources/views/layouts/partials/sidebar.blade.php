{{-- Admin Navigation --}}
@if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
    <div class="space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('admin.medical-supplies.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.medical-supplies.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-pills w-5"></i>
            <span class="ml-3">Medical Supplies</span>
        </a>

        <a href="{{ route('admin.drones.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.drones.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-drone w-5"></i>
            <span class="ml-3">Drones</span>
        </a>

        <a href="{{ route('admin.hospitals.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.hospitals.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-hospital w-5"></i>
            <span class="ml-3">Hospitals</span>
        </a>

        <a href="{{ route('admin.delivery-requests.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.delivery-requests.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-clipboard-list w-5"></i>
            <span class="ml-3">Delivery Requests</span>
        </a>

        <a href="{{ route('admin.deliveries.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.deliveries.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-shipping-fast w-5"></i>
            <span class="ml-3">Deliveries</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-users w-5"></i>
            <span class="ml-3">Users</span>
        </a>

        <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-user-shield w-5"></i>
            <span class="ml-3">Roles & Permissions</span>
        </a>

        <a href="{{ route('admin.reports') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.reports') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-chart-bar w-5"></i>
            <span class="ml-3">Reports</span>
        </a>
    </div>
@endif

{{-- Hospital Portal Navigation --}}
@if(auth()->user()->hasRole('hospital_admin') || auth()->user()->hasRole('hospital_staff'))
    <div class="space-y-1">
        <a href="{{ route('hospital.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('hospital.delivery-requests.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.delivery-requests.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-clipboard-list w-5"></i>
            <span class="ml-3">Delivery Requests</span>
        </a>

        <a href="{{ route('hospital.deliveries') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.deliveries') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-shipping-fast w-5"></i>
            <span class="ml-3">Active Deliveries</span>
        </a>

        <a href="{{ route('hospital.inventory') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('hospital.inventory') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-boxes w-5"></i>
            <span class="ml-3">Inventory</span>
        </a>
    </div>
@endif

{{-- Drone Operator Navigation --}}
@if(auth()->user()->hasRole('drone_operator'))
    <div class="space-y-1">
        <a href="{{ route('operator.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('operator.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('operator.deliveries') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('operator.deliveries') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-tasks w-5"></i>
            <span class="ml-3">My Deliveries</span>
        </a>

        <a href="{{ route('operator.drones') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('operator.drones') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-drone w-5"></i>
            <span class="ml-3">My Drones</span>
        </a>
    </div>
@endif
