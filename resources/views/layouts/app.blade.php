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
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js for charts (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Axios for AJAX requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    
    <!-- Custom CSS (no build required) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* Enhanced Responsive Utilities */
        @media (max-width: 768px) {
            .mobile-hidden { display: none !important; }
            .mobile-full-width { width: 100% !important; }
            .mobile-text-sm { font-size: 0.875rem !important; }
            .mobile-p-2 { padding: 0.5rem !important; }
            .mobile-overflow-x { overflow-x: auto !important; }
        }
        
        /* Smooth Transitions */
        * {
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
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
        
        /* Responsive Tables */
        @media (max-width: 768px) {
            .responsive-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            
            .responsive-table table {
                min-width: 800px;
            }
            
            /* Stack table on very small screens */
            @media (max-width: 480px) {
                .stack-table thead {
                    display: none;
                }
                
                .stack-table tr {
                    display: block;
                    margin-bottom: 1rem;
                    border: 1px solid #e5e7eb;
                    border-radius: 0.5rem;
                    padding: 0.5rem;
                }
                
                .stack-table td {
                    display: block;
                    text-align: right;
                    padding: 0.5rem;
                    border-bottom: 1px solid #f3f4f6;
                }
                
                .stack-table td:before {
                    content: attr(data-label);
                    float: left;
                    font-weight: bold;
                    color: #374151;
                }
            }
        }
        
        /* Card Hover Effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        
        /* Loading Spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #9333ea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Grid Improvements */
        @media (max-width: 640px) {
            .sm-grid-cols-1 {
                grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
            }
        }
        
        @media (min-width: 641px) and (max-width: 1024px) {
            .md-grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
        
        /* Responsive Text Sizes */
        @media (max-width: 640px) {
            h1 { font-size: 1.5rem !important; }
            h2 { font-size: 1.25rem !important; }
            h3 { font-size: 1.125rem !important; }
        }
        
        /* Mobile Form Improvements */
        @media (max-width: 768px) {
            input, select, textarea {
                font-size: 16px !important; /* Prevents zoom on iOS */
            }
        }
        
        /* Responsive Spacing */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        <!-- Sidebar - Black/Dark Gray Background -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0"
            style="background-color: #111827 !important;"
        >
            <div class="flex items-center justify-between h-16 px-6 bg-gray-800" style="background-color: #1f2937 !important;">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-drone text-purple-400 text-2xl"></i>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-white leading-tight">Drone Delivery</span>
                        <span class="text-xs text-gray-400 leading-tight">Medical System</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex flex-col h-[calc(100vh-4rem)]">
                <nav class="flex-1 mt-6 px-4 space-y-2 overflow-y-auto">
                    @include('layouts.partials.sidebar')
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-4 lg:px-6">
                    <!-- Mobile Menu Button & Logo -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Mobile Logo (visible only on small screens) -->
                        <div class="flex items-center space-x-2 md:hidden">
                            <i class="fas fa-drone text-purple-600 text-xl"></i>
                            <span class="font-bold text-gray-900 text-sm">Drone Delivery System</span>
                        </div>
                    </div>

                    <div class="flex-1 flex items-center justify-between ml-4 lg:ml-0">
                        <!-- Breadcrumb (hidden on mobile) -->
                        <div class="hidden lg:block text-sm text-gray-600">
                            @yield('breadcrumb')
                        </div>

                        <div class="flex items-center space-x-2 lg:space-x-4 ml-auto">

                        <!-- Notifications (Hospital Portal Only) -->
                        @if(Auth::user()->hasAnyRoleSlug(['hospital_admin', 'hospital_staff']))
                        <div x-data="{ 
                            notificationOpen: false,
                            unreadCount: 0,
                            notifications: [],
                            async fetchNotifications() {
                                try {
                                    const response = await axios.get('/hospital/notifications');
                                    this.notifications = response.data.notifications;
                                    this.unreadCount = response.data.unread_count;
                                } catch (error) {
                                    console.error('Error fetching notifications:', error);
                                }
                            },
                            async markAsRead(notificationId) {
                                try {
                                    await axios.post(`/hospital/notifications/${notificationId}/mark-read`);
                                    this.fetchNotifications();
                                } catch (error) {
                                    console.error('Error marking notification as read:', error);
                                }
                            },
                            async markAllAsRead() {
                                try {
                                    await axios.post('/hospital/notifications/mark-all-read');
                                    this.fetchNotifications();
                                } catch (error) {
                                    console.error('Error marking all as read:', error);
                                }
                            },
                            init() {
                                this.fetchNotifications();
                                // Refresh every 30 seconds
                                setInterval(() => this.fetchNotifications(), 30000);
                            }
                        }" class="relative mr-4">
                            <button @click="notificationOpen = !notificationOpen; if(notificationOpen) fetchNotifications()" 
                                    class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="unreadCount > 0" 
                                      x-text="unreadCount" 
                                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-semibold animate-pulse"></span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="notificationOpen" 
                                 @click.away="notificationOpen = false" 
                                 x-transition
                                 class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border max-h-[500px] overflow-hidden z-50">
                                
                                <!-- Header -->
                                <div class="flex items-center justify-between px-4 py-3 border-b bg-gray-50">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                    <button @click="markAllAsRead()" 
                                            x-show="unreadCount > 0"
                                            class="text-xs text-blue-600 hover:text-blue-800">
                                        Mark all as read
                                    </button>
                                </div>

                                <!-- Notifications List -->
                                <div class="overflow-y-auto max-h-[400px]">
                                    <template x-if="notifications.length === 0">
                                        <div class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-bell-slash text-4xl mb-3 text-gray-300"></i>
                                            <p>No notifications yet</p>
                                        </div>
                                    </template>

                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div :class="notification.read_at ? 'bg-white' : 'bg-blue-50'" 
                                             class="px-4 py-3 border-b hover:bg-gray-50 cursor-pointer transition"
                                             @click="if(!notification.read_at) markAsRead(notification.id)">
                                            <div class="flex items-start">
                                                <div :class="{
                                                    'bg-green-100 text-green-600': notification.type === 'delivered',
                                                    'bg-blue-100 text-blue-600': notification.type === 'dispatched' || notification.type === 'in_transit',
                                                    'bg-red-100 text-red-600': notification.type === 'cancelled' || notification.type === 'delayed',
                                                    'bg-yellow-100 text-yellow-600': notification.type === 'pending'
                                                }" class="w-10 h-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                                    <i :class="{
                                                        'fa-check-circle': notification.type === 'delivered',
                                                        'fa-plane': notification.type === 'dispatched' || notification.type === 'in_transit',
                                                        'fa-times-circle': notification.type === 'cancelled',
                                                        'fa-exclamation-triangle': notification.type === 'delayed',
                                                        'fa-clock': notification.type === 'pending'
                                                    }" class="fas"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900 text-sm" x-text="notification.title"></p>
                                                    <p class="text-gray-600 text-xs mt-1" x-text="notification.message"></p>
                                                    <p class="text-gray-400 text-xs mt-1" x-text="notification.time_ago"></p>
                                                </div>
                                                <div x-show="!notification.read_at" class="w-2 h-2 bg-blue-600 rounded-full ml-2 flex-shrink-0"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Footer -->
                                <div class="px-4 py-2 bg-gray-50 border-t text-center">
                                    <a href="{{ route('hospital.notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- User Menu -->
                        <div x-data="{ userMenuOpen: false }" class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 lg:space-x-3 hover:bg-gray-100 px-2 lg:px-3 py-2 rounded-lg transition">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-purple-600 to-blue-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block font-medium text-gray-700 text-sm lg:text-base">{{ Str::limit(Auth::user()->name, 15) }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500 hidden sm:block"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 border border-gray-200 z-50">
                                <!-- User Info Header -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-purple-50 transition">
                                    <i class="fas fa-user mr-3 text-purple-600 w-5"></i>
                                    <span class="text-sm">My Profile</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-purple-50 transition">
                                    <i class="fas fa-cog mr-3 text-purple-600 w-5"></i>
                                    <span class="text-sm">Settings</span>
                                </a>
                                
                                <div class="border-t border-gray-100 my-1"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2.5 text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                                        <span class="text-sm font-medium">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-3 sm:p-4 md:p-5 lg:p-6 xl:p-8">
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition class="mb-3 sm:mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg shadow-md">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center min-w-0">
                                    <i class="fas fa-check-circle mr-2 sm:mr-3 text-base sm:text-lg flex-shrink-0"></i>
                                    <span class="font-medium text-sm sm:text-base truncate">{{ session('success') }}</span>
                                </div>
                                <button @click="show = false" class="text-green-700 hover:text-green-900 flex-shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-transition class="mb-3 sm:mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg shadow-md">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center min-w-0">
                                    <i class="fas fa-exclamation-circle mr-2 sm:mr-3 text-base sm:text-lg flex-shrink-0"></i>
                                    <span class="font-medium text-sm sm:text-base truncate">{{ session('error') }}</span>
                                </div>
                                <button @click="show = false" class="text-red-700 hover:text-red-900 flex-shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-md">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                                        <span class="font-bold">Please fix the following errors:</span>
                                    </div>
                                    <ul class="list-disc list-inside space-y-1 ml-6">
                                        @foreach($errors->all() as $error)
                                            <li class="text-sm">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button @click="show = false" class="text-red-700 hover:text-red-900 ml-4">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Custom JavaScript (Inline - No Build Required) -->
    <script>
        /**
         * =========================================
         * DRONE DELIVERY SYSTEM - MAIN SCRIPT
         * =========================================
         * Loaded via CDN - No Node.js/npm needed!
         */
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[DRONE] Drone Delivery System - Ready!');
            
            // Initialize features
            initDeleteConfirmation();
            initImagePreviews();
            initTooltips();
        });
        
        /**
         * Delete Confirmation
         */
        function initDeleteConfirmation() {
            document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            });
        }
        
        /**
         * Image Preview
         */
        function initImagePreviews() {
            document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            let preview = document.querySelector(`#${input.id}-preview`);
                            if (!preview) {
                                preview = document.createElement('img');
                                preview.id = `${input.id}-preview`;
                                preview.className = 'mt-2 max-w-xs rounded-lg shadow';
                                input.parentElement.appendChild(preview);
                            }
                            preview.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        }
        
        /**
         * Tooltips
         */
        function initTooltips() {
            document.querySelectorAll('[title]').forEach(element => {
                element.addEventListener('mouseenter', function() {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'fixed z-50 px-3 py-2 text-sm text-white bg-gray-900 rounded shadow-lg';
                    tooltip.textContent = this.title;
                    tooltip.id = 'tooltip-' + Math.random();
                    
                    document.body.appendChild(tooltip);
                    
                    const rect = this.getBoundingClientRect();
                    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + window.scrollY + 'px';
                    tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';
                    
                    this.addEventListener('mouseleave', function() {
                        tooltip.remove();
                    }, { once: true });
                });
            });
        }
        
        /**
         * Show Notification
         */
        function showNotification(message, type = 'info') {
            const colors = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            };
            
            const icons = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            };
            
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-xl text-white ${colors[type]} transition-all transform translate-x-full opacity-0`;
            toast.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${icons[type]}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 10);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Make functions globally available
        window.showNotification = showNotification;
        
        // Configure Axios if available
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        }
    </script>

    @stack('scripts')
</body>
</html>
