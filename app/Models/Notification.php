<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'priority',
        'channel',
        'is_read',
        'read_at',
        'is_sent',
        'sent_at',
        'reference_type',
        'reference_id',
        'recipients',
        'action_url',
        'expires_at',
        'metadata'
    ];

    protected $casts = [
        'data' => 'array',
        'recipients' => 'array',
        'metadata' => 'array',
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    protected $dates = ['deleted_at'];

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    // Channel constants
    const CHANNEL_IN_APP = 'in_app';
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_PUSH = 'push';
    const CHANNEL_WEBHOOK = 'webhook';

    // Type constants
    const TYPE_DELIVERY_UPDATE = 'delivery_update';
    const TYPE_DRONE_STATUS = 'drone_status';
    const TYPE_EMERGENCY_ALERT = 'emergency_alert';
    const TYPE_SYSTEM_NOTIFICATION = 'system_notification';
    const TYPE_APPROVAL_REQUEST = 'approval_request';
    const TYPE_DELIVERY_REQUEST = 'delivery_request';

    /**
     * Get the user this notification belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referenced model (polymorphic)
     */
    public function reference()
    {
        if ($this->reference_type && $this->reference_id) {
            return $this->morphTo('reference', 'reference_type', 'reference_id');
        }
        return null;
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for unsent notifications
     */
    public function scopeUnsent($query)
    {
        return $query->where('is_sent', false);
    }

    /**
     * Scope for high priority notifications
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL]);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by channel
     */
    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope for active (not expired) notifications
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Mark as read
     */
    public function markAsRead(): bool
    {
        $this->is_read = true;
        $this->read_at = now();
        return $this->save();
    }

    /**
     * Mark as unread
     */
    public function markAsUnread(): bool
    {
        $this->is_read = false;
        $this->read_at = null;
        return $this->save();
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): bool
    {
        $this->is_sent = true;
        $this->sent_at = now();
        return $this->save();
    }

    /**
     * Check if notification is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute(): string
    {
        switch ($this->priority) {
            case self::PRIORITY_CRITICAL:
                return 'danger';
            case self::PRIORITY_HIGH:
                return 'warning';
            case self::PRIORITY_MEDIUM:
                return 'info';
            default:
                return 'secondary';
        }
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Send notification to user
     */
    public static function sendToUser($userId, $type, $title, $message, $data = [], $priority = self::PRIORITY_MEDIUM): self
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'channel' => self::CHANNEL_IN_APP
        ]);
    }

    /**
     * Get priorities array
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_CRITICAL => 'Critical'
        ];
    }

    /**
     * Get channels array
     */
    public static function getChannels(): array
    {
        return [
            self::CHANNEL_IN_APP => 'In-App',
            self::CHANNEL_EMAIL => 'Email',
            self::CHANNEL_SMS => 'SMS',
            self::CHANNEL_PUSH => 'Push Notification',
            self::CHANNEL_WEBHOOK => 'Webhook'
        ];
    }
}
