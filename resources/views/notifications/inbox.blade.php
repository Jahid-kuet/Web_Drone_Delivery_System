@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-1">
                @if($unreadCount > 0)
                    You have <span class="font-semibold text-blue-600">{{ $unreadCount }}</span> unread notification(s)
                @else
                    All caught up! No unread notifications
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
        <form action="{{ 
            request()->routeIs('admin.*') 
                ? route('admin.notifications.mark-all-read') 
                : (request()->routeIs('operator.*') 
                    ? route('operator.notifications.mark-all-read') 
                    : route('hospital.notifications.mark-all-read'))
        }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-check-double mr-2"></i>Mark All as Read
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
    @endif

    @if($notifications->isEmpty())
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Notifications</h3>
        <p class="text-gray-500">You don't have any notifications yet.</p>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
            <div class="p-4 hover:bg-gray-50 transition {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            @php
                                $typeIcons = [
                                    'info' => 'fa-info-circle text-blue-500',
                                    'success' => 'fa-check-circle text-green-500',
                                    'warning' => 'fa-exclamation-triangle text-yellow-500',
                                    'error' => 'fa-times-circle text-red-500',
                                ];
                                $icon = $typeIcons[$notification->type] ?? 'fa-bell text-gray-500';
                            @endphp
                            <i class="fas {{ $icon }} mr-2"></i>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $notification->title }}</h3>
                            @if(!$notification->is_read)
                            <span class="ml-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-full">New</span>
                            @endif
                        </div>
                        <p class="text-gray-700 mb-2">{{ $notification->message }}</p>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            @if($notification->sender)
                            <span>
                                <i class="fas fa-user mr-1"></i>From: {{ $notification->sender->name }}
                            </span>
                            @endif
                            <span>
                                <i class="fas fa-clock mr-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                            @if($notification->read_at)
                            <span>
                                <i class="fas fa-eye mr-1"></i>Read {{ $notification->read_at->diffForHumans() }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        @if(!$notification->is_read)
                        <form action="{{ 
                            request()->routeIs('admin.*') 
                                ? route('admin.notifications.mark-read', $notification) 
                                : (request()->routeIs('operator.*') 
                                    ? route('operator.notifications.mark-read', $notification) 
                                    : route('hospital.notifications.mark-read', $notification))
                        }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-check mr-1"></i>Mark as Read
                            </button>
                        </form>
                        @else
                        <span class="text-green-600 text-sm">
                            <i class="fas fa-check-circle mr-1"></i>Read
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
