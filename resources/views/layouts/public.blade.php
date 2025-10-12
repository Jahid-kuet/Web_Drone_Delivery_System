<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Drone Delivery System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #9333ea;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #7c3aed;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Public Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-80 transition">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-drone text-white text-xl"></i>
                        </div>
                        <div class="hidden sm:flex flex-col">
                            <span class="text-lg font-bold text-gray-900 leading-tight">Drone Delivery</span>
                            <span class="text-xs text-gray-600 leading-tight">Medical System</span>
                        </div>
                        <span class="sm:hidden text-lg font-bold text-gray-900">DDS</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">
                        <i class="fas fa-home mr-1"></i>Home
                    </a>
                    <a href="{{ route('tracking.public') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">
                        <i class="fas fa-map-marker-alt mr-1"></i>Track
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">
                        <i class="fas fa-info-circle mr-1"></i>About
                    </a>
                    <a href="{{ route('services') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">
                        <i class="fas fa-cogs mr-1"></i>Services
                    </a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">
                        <i class="fas fa-envelope mr-1"></i>Contact
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition shadow-md">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition shadow-md">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden text-gray-700 hover:text-purple-600 p-2"
                        x-data="{ mobileMenuOpen: false }"
                    >
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div 
                x-data="{ mobileMenuOpen: false }"
                x-show="mobileMenuOpen" 
                @click.away="mobileMenuOpen = false"
                x-transition
                class="md:hidden py-4 border-t border-gray-200"
            >
                <div class="flex flex-col space-y-3">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-purple-600 font-medium py-2">
                        <i class="fas fa-home mr-2 w-6"></i>Home
                    </a>
                    <a href="{{ route('tracking.public') }}" class="text-gray-700 hover:text-purple-600 font-medium py-2">
                        <i class="fas fa-map-marker-alt mr-2 w-6"></i>Track Delivery
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-purple-600 font-medium py-2">
                        <i class="fas fa-info-circle mr-2 w-6"></i>About
                    </a>
                    <a href="{{ route('services') }}" class="text-gray-700 hover:text-purple-600 font-medium py-2">
                        <i class="fas fa-cogs mr-2 w-6"></i>Services
                    </a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-purple-600 font-medium py-2">
                        <i class="fas fa-envelope mr-2 w-6"></i>Contact
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition shadow-md sm:hidden">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-drone text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Drone Delivery</h3>
                            <p class="text-xs text-gray-400">Medical System</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Revolutionizing healthcare logistics with autonomous drone delivery for critical medical supplies.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-white transition">Services</a></li>
                        <li><a href="{{ route('tracking.public') }}" class="text-gray-400 hover:text-white transition">Track Delivery</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="font-bold mb-4">Services</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="text-gray-400">Emergency Deliveries</li>
                        <li class="text-gray-400">Medical Supplies</li>
                        <li class="text-gray-400">Blood Products</li>
                        <li class="text-gray-400">Vaccine Distribution</li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 w-5"></i>
                            <a href="mailto:info@dronedelivery.com" class="hover:text-white transition">info@dronedelivery.com</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 w-5"></i>
                            <a href="tel:+1234567890" class="hover:text-white transition">+123-456-7890</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 w-5"></i>
                            <span>24/7 Service Available</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Drone Delivery System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
