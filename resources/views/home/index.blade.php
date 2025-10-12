<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Drone Delivery System') }} - Medical Supply Delivery</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Custom Tailwind Configuration */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: moveBackground 20s linear infinite;
        }
        
        @keyframes moveBackground {
            0% { background-position: 0 0; }
            100% { background-position: 60px 60px; }
        }
        
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        
        .feature-card:hover::before {
            left: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .stat-card:hover::after {
            opacity: 1;
        }
        
        .stat-card:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 25px -5px rgba(124, 58, 237, 0.2);
            border-color: #a78bfa;
        }
        
        .dark .stat-card {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            border-color: #4a5568;
        }
        
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(124, 58, 237, 0.5); }
            50% { box-shadow: 0 0 40px rgba(124, 58, 237, 0.8); }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        
        /* Mobile Menu Animation */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .mobile-menu.open {
            max-height: 500px;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Scroll margin for anchor links (accounts for fixed nav) */
        section[id] {
            scroll-margin-top: 80px;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        /* Glassmorphism Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Modern Button Styles */
        .btn-modern {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
            z-index: -1;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        /* Number Counter Animation */
        .number-animate {
            display: inline-block;
            transition: transform 0.3s;
        }
        
        .stat-card:hover .number-animate {
            transform: scale(1.2);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-3 fade-in-up">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-600 via-purple-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg pulse-glow">
                        <!-- Realistic Drone SVG Icon -->
                        <svg class="w-9 h-9 text-white floating-animation" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <!-- Propellers -->
                            <circle cx="5" cy="5" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                            <circle cx="19" cy="5" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                            <circle cx="5" cy="19" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                            <circle cx="19" cy="19" r="2.5" stroke-width="1.8" fill="currentColor" opacity="0.3"/>
                            
                            <!-- Arms connecting to propellers -->
                            <line x1="8" y1="8" x2="5" y2="5" stroke-width="2"/>
                            <line x1="16" y1="8" x2="19" y2="5" stroke-width="2"/>
                            <line x1="8" y1="16" x2="5" y2="19" stroke-width="2"/>
                            <line x1="16" y1="16" x2="19" y2="19" stroke-width="2"/>
                            
                            <!-- Central body -->
                            <rect x="9" y="9" width="6" height="6" rx="1.5" fill="currentColor" stroke-width="0"/>
                            
                            <!-- Camera/Gimbal -->
                            <circle cx="12" cy="15" r="1.5" fill="currentColor" opacity="0.7"/>
                            <line x1="12" y1="15" x2="12" y2="17" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span class="text-2xl md:text-3xl font-extrabold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        Drone Delivery System
                    </span>
                </div>
                
                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 font-semibold transition-all duration-300 relative group">
                        Home
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#features" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 font-semibold transition-all duration-300 relative group">
                        Features
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#how-it-works" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 font-semibold transition-all duration-300 relative group">
                        How It Works
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="{{ route('tracking.public') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 font-semibold transition-all duration-300 relative group">
                        Track Delivery
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </div>
                
                <!-- Desktop Auth Links -->
                <div class="hidden lg:flex items-center space-x-4">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn-modern px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 font-semibold transition-all duration-300 px-4 py-2 rounded-lg hover:bg-purple-50 dark:hover:bg-gray-700">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-modern px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-rocket mr-2"></i>Get Started
                            </a>
                        @endif
                    @endauth
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="lg:hidden text-gray-700 dark:text-gray-300 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="mobile-menu lg:hidden">
                <div class="px-2 pt-2 pb-6 space-y-3">
                    <a href="{{ route('home') }}" class="block px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 font-semibold transition-all">
                        <i class="fas fa-home mr-3"></i>Home
                    </a>
                    <a href="#features" class="block px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 font-semibold transition-all">
                        <i class="fas fa-star mr-3"></i>Features
                    </a>
                    <a href="#how-it-works" class="block px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 font-semibold transition-all">
                        <i class="fas fa-cogs mr-3"></i>How It Works
                    </a>
                    <a href="{{ route('tracking.public') }}" class="block px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 font-semibold transition-all">
                        <i class="fas fa-map-marker-alt mr-3"></i>Track Delivery
                    </a>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold rounded-lg text-center shadow-lg">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 font-semibold transition-all">
                            <i class="fas fa-sign-in-alt mr-3"></i>Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block px-4 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold rounded-lg text-center shadow-lg">
                                <i class="fas fa-rocket mr-2"></i>Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            
            menu.classList.toggle('open');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                const menu = document.getElementById('mobile-menu');
                const menuIcon = document.getElementById('menu-icon');
                const closeIcon = document.getElementById('close-icon');
                
                menu.classList.remove('open');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            });
        });
        
        // Smooth scroll with offset for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                
                // Skip empty anchors
                if (href === '#' || href === '#!') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    
                    // Calculate offset (navigation height)
                    const navHeight = 80; // Approximate height of fixed nav
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without jumping
                    if (history.pushState) {
                        history.pushState(null, null, href);
                    }
                }
            });
        });
    </script>

    <!-- Hero Section -->
    <section class="hero-gradient text-white pt-32 md:pt-40 pb-24 md:pb-32 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-block px-4 py-2 bg-white/20 rounded-full text-sm font-semibold mb-6 fade-in-up backdrop-blur-sm">
                        <i class="fas fa-bolt text-yellow-300 mr-2"></i>
                        Next-Gen Medical Logistics
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight fade-in-up delay-100">
                        Fast & Reliable<br>
                        <span class="bg-gradient-to-r from-yellow-300 to-pink-300 bg-clip-text text-transparent">
                            Medical Supply
                        </span><br>
                        Delivery
                    </h1>
                    <p class="text-lg sm:text-xl md:text-2xl mb-8 text-purple-100 fade-in-up delay-200 max-w-2xl mx-auto lg:mx-0">
                        Revolutionizing healthcare logistics with autonomous drone delivery. 
                        Delivering critical medical supplies when every second counts.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start fade-in-up delay-300">
                        @guest
                            <a href="{{ route('register') }}" class="btn-modern px-8 py-4 bg-white text-purple-600 font-bold rounded-xl hover:bg-gray-100 transition-all shadow-2xl hover:shadow-3xl transform hover:scale-105 text-center">
                                <i class="fas fa-rocket mr-2"></i>Get Started Free
                            </a>
                        @endguest
                        <a href="{{ route('tracking.public') }}" class="btn-modern px-8 py-4 glass-effect hover:bg-white/20 text-white font-bold rounded-xl transition-all shadow-xl border-2 border-white/50 text-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>Track Delivery
                        </a>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="mt-12 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm fade-in-up delay-400">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-shield-check text-green-300 text-xl"></i>
                            <span class="font-semibold">FDA Compliant</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock text-blue-300 text-xl"></i>
                            <span class="font-semibold">24/7 Service</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-lock text-yellow-300 text-xl"></i>
                            <span class="font-semibold">Secure Transport</span>
                        </div>
                    </div>
                </div>
                
                <div class="hidden lg:block fade-in-up delay-500">
                    <div class="relative">
                        <!-- Decorative Elements -->
                        <div class="absolute -top-10 -left-10 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                        
                        <div class="relative glass-effect rounded-3xl p-8 shadow-2xl">
                            <div class="space-y-4">
                                <!-- Stat Card 1 -->
                                <div class="flex items-center space-x-4 bg-white/30 backdrop-blur-lg rounded-xl p-5 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-clock text-3xl text-white"></i>
                                    </div>
                                    <div>
                                        <div class="font-black text-3xl number-animate">< 30 min</div>
                                        <div class="text-sm font-semibold opacity-90">Average Delivery Time</div>
                                    </div>
                                </div>
                                
                                <!-- Stat Card 2 -->
                                <div class="flex items-center space-x-4 bg-white/30 backdrop-blur-lg rounded-xl p-5 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-check-circle text-3xl text-white"></i>
                                    </div>
                                    <div>
                                        <div class="font-black text-3xl number-animate">99.8%</div>
                                        <div class="text-sm font-semibold opacity-90">Success Rate</div>
                                    </div>
                                </div>
                                
                                <!-- Stat Card 3 -->
                                <div class="flex items-center space-x-4 bg-white/30 backdrop-blur-lg rounded-xl p-5 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-hospital text-3xl text-white"></i>
                                    </div>
                                    <div>
                                        <div class="font-black text-3xl number-animate">24/7</div>
                                        <div class="text-sm font-semibold opacity-90">Emergency Service</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl floating-animation"></div>
        <div class="absolute bottom-20 right-20 w-32 h-32 bg-pink-300/10 rounded-full blur-xl floating-animation" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-10 w-16 h-16 bg-yellow-300/10 rounded-full blur-xl floating-animation" style="animation-delay: 2s;"></div>
    </section>
    
    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -50px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(50px, 50px) scale(1.05); }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>

    <!-- Stats Section -->
    <section class="py-20 bg-gradient-to-br from-gray-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <!-- Total Drones -->
                <div class="stat-card glass-effect bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl fade-in-up delay-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-helicopter text-3xl text-white"></i>
                    </div>
                    <div class="font-black text-4xl text-gray-800 mb-2 number-animate">{{ $stats['total_drones'] }}</div>
                    <div class="text-sm font-semibold text-gray-600">Total Drones</div>
                    <div class="mt-3 inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                        <i class="fas fa-arrow-up mr-1"></i>Active Fleet
                    </div>
                </div>
                
                <!-- Active Deliveries -->
                <div class="stat-card glass-effect bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl fade-in-up delay-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-shipping-fast text-3xl text-white"></i>
                    </div>
                    <div class="font-black text-4xl text-gray-800 mb-2 number-animate">{{ $stats['active_deliveries'] }}</div>
                    <div class="text-sm font-semibold text-gray-600">Active Deliveries</div>
                    <div class="mt-3 inline-block px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">
                        <i class="fas fa-pulse mr-1"></i>In Transit
                    </div>
                </div>
                
                <!-- Completed Deliveries -->
                <div class="stat-card glass-effect bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl fade-in-up delay-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-check-double text-3xl text-white"></i>
                    </div>
                    <div class="font-black text-4xl text-gray-800 mb-2 number-animate">{{ number_format($stats['completed_deliveries']) }}</div>
                    <div class="text-sm font-semibold text-gray-600">Completed</div>
                    <div class="mt-3 inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                        <i class="fas fa-check mr-1"></i>Success
                    </div>
                </div>
                
                <!-- Hospitals -->
                <div class="stat-card glass-effect bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl fade-in-up delay-400">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-hospital text-3xl text-white"></i>
                    </div>
                    <div class="font-black text-4xl text-gray-800 mb-2 number-animate">{{ $stats['registered_hospitals'] }}</div>
                    <div class="text-sm font-semibold text-gray-600">Hospitals</div>
                    <div class="mt-3 inline-block px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">
                        <i class="fas fa-network-wired mr-1"></i>Network
                    </div>
                </div>
                
                <!-- Available Drones -->
                <div class="stat-card glass-effect bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl fade-in-up delay-500">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-circle-check text-3xl text-white"></i>
                    </div>
                    <div class="font-black text-4xl text-gray-800 mb-2 number-animate">{{ $stats['available_drones'] }}</div>
                    <div class="text-sm font-semibold text-gray-600">Available</div>
                    <div class="mt-3 inline-block px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                        <i class="fas fa-battery-full mr-1"></i>Ready
                    </div>
                </div>
                
                <!-- Users -->
                <div class="stat-card glass-effect bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-xl hover:shadow-2xl fade-in-up delay-600">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-users text-3xl text-white"></i>
                    </div>
                    <div class="font-black text-4xl text-gray-800 mb-2 number-animate">{{ $stats['registered_users'] }}</div>
                    <div class="text-sm font-semibold text-gray-600">Users</div>
                    <div class="mt-3 inline-block px-3 py-1 bg-teal-100 text-teal-700 text-xs font-bold rounded-full">
                        <i class="fas fa-user-check mr-1"></i>Active
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-20 left-10 w-64 h-64 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 fade-in-up">
                <div class="inline-block px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-bold mb-4">
                    <i class="fas fa-star mr-2"></i>FEATURES
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    Why Choose Our Service?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Advanced technology meets healthcare logistics for unmatched reliability
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card glass-effect bg-gradient-to-br from-purple-50 to-white rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-purple-100 fade-in-up delay-100">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-rocket text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">
                        Ultra-Fast Delivery
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Autonomous drones deliver medical supplies in under 30 minutes, 
                        ensuring critical care reaches patients quickly.
                    </p>
                    <div class="mt-6 inline-flex items-center text-purple-600 font-bold text-sm">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card glass-effect bg-gradient-to-br from-blue-50 to-white rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-blue-100 fade-in-up delay-200">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-map-marked-alt text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">
                        Real-Time Tracking
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Monitor your delivery in real-time with GPS tracking and 
                        receive instant notifications at every stage.
                    </p>
                    <div class="mt-6 inline-flex items-center text-blue-600 font-bold text-sm">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card glass-effect bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-green-100 fade-in-up delay-300">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-shield-alt text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">
                        Temperature Control
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Advanced climate control systems maintain optimal temperatures 
                        for sensitive medical supplies and vaccines.
                    </p>
                    <div class="mt-6 inline-flex items-center text-green-600 font-bold text-sm">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card glass-effect bg-gradient-to-br from-red-50 to-white rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-red-100 fade-in-up delay-400">
                    <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-clock text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">
                        24/7 Availability
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Round-the-clock emergency delivery service ensuring healthcare 
                        facilities get supplies whenever needed.
                    </p>
                    <div class="mt-6 inline-flex items-center text-red-600 font-bold text-sm">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card glass-effect bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-indigo-100 fade-in-up delay-500">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-brain text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">
                        AI-Powered Routes
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Intelligent route optimization ensures the fastest and safest 
                        delivery path while avoiding obstacles.
                    </p>
                    <div class="mt-6 inline-flex items-center text-indigo-600 font-bold text-sm">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card glass-effect bg-gradient-to-br from-orange-50 to-white rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-orange-100 fade-in-up delay-600">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-leaf text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">
                        Eco-Friendly
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Electric-powered drones reduce carbon emissions and 
                        contribute to sustainable healthcare logistics.
                    </p>
                    <div class="mt-6 inline-flex items-center text-orange-600 font-bold text-sm">
                        Learn More <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    How It Works
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    Simple, fast, and efficient process
                </p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        Place Request
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Hospitals submit delivery requests through our secure portal
                    </p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        Auto Assignment
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        System assigns nearest available drone with optimal route
                    </p>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        In Transit
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Real-time tracking as drone flies to destination
                    </p>
                </div>
                
                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        Delivered
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Safe landing and confirmation of successful delivery
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6">
                Ready to Transform Healthcare Logistics?
            </h2>
            <p class="text-xl mb-8 text-purple-100">
                Join hospitals and healthcare facilities already using our drone delivery service
            </p>
            @guest
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-purple-600 font-bold rounded-lg hover:bg-gray-100 transition shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i>Register Now
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-purple-700 hover:bg-purple-800 text-white font-bold rounded-lg transition shadow-lg border-2 border-white">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            @else
                <a href="{{ route('admin.dashboard') }}" class="inline-block px-8 py-4 bg-white text-purple-600 font-bold rounded-lg hover:bg-gray-100 transition shadow-lg">
                    <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-drone text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-white">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-sm">
                        Revolutionizing medical supply delivery with cutting-edge drone technology.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-purple-400 transition">Home</a></li>
                        <li><a href="#features" class="hover:text-purple-400 transition">Features</a></li>
                        <li><a href="#about" class="hover:text-purple-400 transition">About</a></li>
                        <li><a href="{{ route('tracking.public') }}" class="hover:text-purple-400 transition">Track Delivery</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-4">Services</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-purple-400 transition">Emergency Delivery</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Scheduled Delivery</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Blood Transport</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Vaccine Delivery</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-4">Contact</h3>
                    <ul class="space-y-2 text-sm">
                        <li><i class="fas fa-envelope mr-2 text-purple-400"></i> support@meddrone.com</li>
                        <li><i class="fas fa-phone mr-2 text-purple-400"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-map-marker-alt mr-2 text-purple-400"></i> 123 Tech Park, CA</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
