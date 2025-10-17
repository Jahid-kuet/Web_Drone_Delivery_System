<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display user's notifications inbox
     */
    public function inbox()
    {
        $user = Auth::user();
        
        // Get notifications where user is the recipient
        $notifications = Notification::where('recipient_id', $user->id)
            ->orWhere('user_id', $user->id)
            ->with('sender')
            ->latest()
            ->paginate(20);
        
        $unreadCount = Notification::where('recipient_id', $user->id)
            ->orWhere('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return view('notifications.inbox', compact('notifications', 'unreadCount'));
    }
    
    /**
     * Mark notification as read
     */
    public function markRead(Notification $notification)
    {
        $user = Auth::user();
        
        // Ensure user owns this notification
        if ($notification->recipient_id == $user->id || $notification->user_id == $user->id) {
            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);
            
            return back()->with('success', 'Notification marked as read');
        }
        
        return back()->with('error', 'Unauthorized');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        $user = Auth::user();
        
        Notification::where(function($query) use ($user) {
            $query->where('recipient_id', $user->id)
                  ->orWhere('user_id', $user->id);
        })
        ->where('is_read', false)
        ->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        
        return back()->with('success', 'All notifications marked as read');
    }
}
