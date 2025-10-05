<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Drone Delivery System') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <div class="inline-block p-4 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-drone text-5xl text-blue-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Drone Delivery System</h1>
            <p class="text-gray-600 mt-2">Fast, Reliable, Life-Saving</p>
        </div>

        <!-- Main Content Card -->
        <div class="w-full max-w-md">
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden">
                @yield('content')
            </div>

            <!-- Footer Links -->
            <div class="mt-6 text-center text-sm text-gray-600">
                @yield('footer-links')
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Drone Delivery System. All rights reserved.
        </div>
    </div>

    @stack('scripts')
</body>
</html>
