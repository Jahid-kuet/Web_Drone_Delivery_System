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
                <span class="text-xl font-bold text-white">
                    <i class="fas fa-drone mr-2"></i>DDS
                </span>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="mt-6 px-4 space-y-2 overflow-y-auto h-[calc(100vh-4rem)]">
                @include('layouts.partials.sidebar')
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="flex-1 flex items-center justify-between ml-4 lg:ml-0">
                        <!-- Breadcrumb -->
                        <div class="text-sm text-gray-600">
                            @yield('breadcrumb')
                        </div>

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
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 hover:bg-gray-100 px-3 py-2 rounded-lg transition">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <button @click="show = false" class="absolute top-0 right-0 px-4 py-3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <button @click="show = false" class="absolute top-0 right-0 px-4 py-3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button @click="show = false" class="absolute top-0 right-0 px-4 py-3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
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
            console.log('🚁 Drone Delivery System - Ready!');
            
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
