@extends('layouts.public')

@section('title', 'About Us')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">About Drone Delivery System</h1>
        <p class="text-gray-700 mb-4">Drone Delivery System (DDS) is a next-generation medical logistics platform delivering time-sensitive medical supplies to hospitals and clinics using autonomous drones. Our mission is to save lives by reducing delivery times for critical supplies.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <h2 class="text-xl font-semibold mb-2">Our Technology</h2>
                <p class="text-gray-600">We use advanced flight planning, redundant safety systems, and secure payload containers to ensure safe and reliable deliveries even in challenging conditions.</p>
            </div>
            <div>
                <h2 class="text-xl font-semibold mb-2">Our Network</h2>
                <p class="text-gray-600">DDS partners with hospitals, logistics providers, and regulatory agencies to create a resilient delivery network. We adhere to local regulations and maintain high standards for safety and privacy.</p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">Contact & Careers</h2>
            <p class="text-gray-600">Interested in partnering with DDS or joining our team? Visit our <a href="{{ route('home.contact') }}" class="text-purple-600">Contact</a> page to reach out.</p>
        </div>
    </div>
</div>
@endsection
