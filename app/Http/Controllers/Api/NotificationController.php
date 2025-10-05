<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notifications();
        
        // Filter by read status
        if ($request->has('unread_only')) {
            $query->unread();
        }
        
        // Filter by priority
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }
        
        // Filter by type
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        
        $notifications = $query->latest()
            ->paginate($request->input('per_page', 20));
        
        return response()->json([
            'success' => true,
            'data' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->toIso8601String(),
                    'metadata' => $notification->metadata ? json_decode($notification->metadata, true) : null,
                ];
            }),
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ],
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->notifications()->unread()->count();
        
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = $user->notifications()->unread()->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
            'count' => $count,
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }
}
