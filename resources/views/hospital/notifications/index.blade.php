@extends('layouts.app')

@section('title', 'All Notifications')

@section('breadcrumb')
    <i class="fas fa-bell mr-2"></i> Notifications
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">All Notifications</h1>
                <p class="text-gray-600 mt-1">Stay updated with your delivery status changes</p>
            </div>
            <a href="{{ route('hospital.dashboard') }}" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-lg shadow">
        @forelse($notifications as $notification)
            <div class="px-6 py-4 border-b last:border-0 hover:bg-gray-50 transition
                        {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
                <div class="flex items-start">
                    {{-- Icon --}}
                    <div class="mr-4
                        @if($notification->type === 'delivered') bg-green-100 text-green-600
                        @elseif($notification->type === 'dispatched' || $notification->type === 'in_transit') bg-blue-100 text-blue-600
                        @elseif($notification->type === 'cancelled') bg-red-100 text-red-600
                        @elseif($notification->type === 'delayed') bg-yellow-100 text-yellow-600
                        @else bg-gray-100 text-gray-600
                        @endif
                        w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        @if($notification->type === 'delivered')
                            <i class="fas fa-check-circle text-xl"></i>
                        @elseif($notification->type === 'dispatched' || $notification->type === 'in_transit')
                            <i class="fas fa-plane text-xl"></i>
                        @elseif($notification->type === 'cancelled')
                            <i class="fas fa-times-circle text-xl"></i>
                        @elseif($notification->type === 'delayed')
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        @else
                            <i class="fas fa-bell text-xl"></i>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ $notification->message }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                    @if($notification->read_at)
                                        <span class="ml-3">
                                            <i class="fas fa-check-double mr-1"></i>
                                            Read {{ $notification->read_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if(is_null($notification->read_at))
                                <span class="w-3 h-3 bg-blue-600 rounded-full ml-4 flex-shrink-0"></span>
                            @endif
                        </div>

                        {{-- Related Delivery Info --}}
                        @if($notification->data && isset($notification->data['delivery_id']))
                            <a href="{{ route('hospital.deliveries.index') }}" 
                               class="inline-block mt-3 text-sm text-teal-600 hover:text-teal-800 font-medium">
                                <i class="fas fa-external-link-alt mr-1"></i>View Delivery
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Notifications Yet</h3>
                <p class="text-gray-600">You'll receive notifications when your deliveries status changes</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="bg-white rounded-lg shadow px-6 py-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
