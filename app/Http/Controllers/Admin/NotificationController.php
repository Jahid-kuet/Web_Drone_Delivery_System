<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notification form
     */
    public function create()
    {
        // Get all users with their roles
        $users = User::with('roles')->get();
        
        return view('admin.notifications.create', compact('users'));
    }

    /**
     * Send notification to selected users
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'exists:users,id',
        ]);

        $sender = Auth::user();
        $count = 0;

        // Create notification for each recipient
        foreach ($validated['recipients'] as $recipientId) {
            Notification::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipientId,
                'user_id' => $recipientId, // for backward compatibility
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'priority' => 'medium',
                'is_read' => false,
                'is_sent' => true,
                'sent_at' => now(),
            ]);
            $count++;
        }

        return redirect()->route('admin.notifications.create')
            ->with('success', "Notification sent to {$count} user(s) successfully!");
    }

    /**
     * Display notifications sent by admin
     */
    public function index()
    {
        $notifications = Notification::with(['sender', 'recipient'])
            ->where('sender_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }
}
