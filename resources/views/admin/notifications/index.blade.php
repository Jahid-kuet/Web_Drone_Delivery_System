@extends('layouts.app')

@section('title', 'Sent Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sent Notifications</h1>
            <p class="text-gray-600 mt-1">View all notifications you have sent</p>
        </div>
        <a href="{{ route('admin.notifications.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Send New Notification
        </a>
    </div>

    @if($notifications->isEmpty())
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Notifications Sent</h3>
        <p class="text-gray-500 mb-6">You haven't sent any notifications yet.</p>
        <a href="{{ route('admin.notifications.create') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Send Your First Notification
        </a>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($notifications as $notification)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $notification->title }}</div>
                        <div class="text-sm text-gray-500 truncate max-w-md">{{ Str::limit($notification->message, 60) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($notification->recipient)
                        <div class="text-sm text-gray-900">{{ $notification->recipient->name }}</div>
                        <div class="text-sm text-gray-500">{{ $notification->recipient->email }}</div>
                        @else
                        <span class="text-sm text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $typeColors = [
                                'info' => 'bg-blue-100 text-blue-800',
                                'success' => 'bg-green-100 text-green-800',
                                'warning' => 'bg-yellow-100 text-yellow-800',
                                'error' => 'bg-red-100 text-red-800',
                            ];
                            $color = $typeColors[$notification->type] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                            {{ ucfirst($notification->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($notification->is_read)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Read
                        </span>
                        @if($notification->read_at)
                        <div class="text-xs text-gray-500 mt-1">{{ $notification->read_at->format('M d, Y H:i') }}</div>
                        @endif
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            <i class="fas fa-envelope mr-1"></i>Unread
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $notification->sent_at ? $notification->sent_at->format('M d, Y H:i') : $notification->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="viewNotification({{ $notification->id }})" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

<!-- Modal for viewing notification details -->
<div id="notificationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mb-4">
            <span class="text-sm font-medium text-gray-500">Type:</span>
            <span id="modalType" class="ml-2"></span>
        </div>
        <div class="mb-4">
            <span class="text-sm font-medium text-gray-500">Recipient:</span>
            <span id="modalRecipient" class="ml-2 text-sm text-gray-900"></span>
        </div>
        <div class="mb-4">
            <p class="text-sm font-medium text-gray-500 mb-2">Message:</p>
            <div id="modalMessage" class="text-sm text-gray-900 bg-gray-50 p-4 rounded border border-gray-200"></div>
        </div>
        <div class="text-sm text-gray-500">
            <span class="font-medium">Sent:</span>
            <span id="modalSentAt"></span>
        </div>
    </div>
</div>

<script>
const notifications = @json($notifications->items());

function viewNotification(id) {
    const notification = notifications.find(n => n.id === id);
    if (!notification) return;

    document.getElementById('modalTitle').textContent = notification.title;
    document.getElementById('modalMessage').textContent = notification.message;
    document.getElementById('modalSentAt').textContent = notification.sent_at 
        ? new Date(notification.sent_at).toLocaleString() 
        : new Date(notification.created_at).toLocaleString();
    
    const typeColors = {
        'info': 'bg-blue-100 text-blue-800',
        'success': 'bg-green-100 text-green-800',
        'warning': 'bg-yellow-100 text-yellow-800',
        'error': 'bg-red-100 text-red-800',
    };
    const color = typeColors[notification.type] || 'bg-gray-100 text-gray-800';
    document.getElementById('modalType').innerHTML = 
        `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${color}">${notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}</span>`;
    
    if (notification.recipient) {
        document.getElementById('modalRecipient').textContent = 
            `${notification.recipient.name} (${notification.recipient.email})`;
    } else {
        document.getElementById('modalRecipient').textContent = 'N/A';
    }

    document.getElementById('notificationModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('notificationModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('notificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
