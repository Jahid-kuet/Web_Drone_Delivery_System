<?php

namespace App\Models\Traits;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Trait Notifiable
 * 
 * Enhanced notification functionality with multiple channels
 */
trait NotifiableEnhanced
{
    /**
     * Send a notification to this model
     */
    public function notify(
        string $title,
        string $message,
        string $type = 'info',
        string $priority = 'normal',
        array $channels = ['in_app'],
        ?array $metadata = null
    ): Notification {
        return Notification::create([
            'notifiable_type' => get_class($this),
            'notifiable_id' => $this->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'priority' => $priority,
            'channels' => json_encode($channels),
            'metadata' => $metadata ? json_encode($metadata) : null,
            'is_read' => false,
        ]);
    }

    /**
     * Send a high priority notification
     */
    public function notifyHighPriority(string $title, string $message, string $type = 'alert'): Notification
    {
        return $this->notify(
            $title,
            $message,
            $type,
            'high',
            ['in_app', 'email', 'sms']
        );
    }

    /**
     * Send a critical notification (all channels)
     */
    public function notifyCritical(string $title, string $message): Notification
    {
        return $this->notify(
            $title,
            $message,
            'emergency',
            'critical',
            ['in_app', 'email', 'sms', 'push']
        );
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(): int
    {
        return $this->notifications()->unread()->update(['is_read' => true]);
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(int $limit = 10)
    {
        return $this->notifications()
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
