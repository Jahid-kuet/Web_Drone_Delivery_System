<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class DeliveryConfirmation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'delivery_id',
        'hospital_id',
        'confirmed_by_user_id',
        'confirmation_number',
        'confirmation_method',
        'confirmed_at',
        'delivered_items',
        'missing_items',
        'damaged_items',
        'delivery_condition',
        'overall_rating',
        'feedback',
        'issues_reported',
        'recipient_name',
        'recipient_title',
        'recipient_phone',
        'recipient_email',
        'digital_signature_path',
        'confirmation_photos',
        'temperature_log',
        'delivery_latitude',
        'delivery_longitude',
        'packaging_condition',
        'all_items_received',
        'customer_satisfied',
        'additional_notes',
        'quality_checks',
        'confirmation_code',
        'requires_follow_up',
        'follow_up_reason',
        'metadata'
    ];

    protected $casts = [
        'delivered_items' => 'array',
        'missing_items' => 'array',
        'damaged_items' => 'array',
        'confirmation_photos' => 'array',
        'temperature_log' => 'array',
        'packaging_condition' => 'array',
        'quality_checks' => 'array',
        'metadata' => 'array',
        'confirmed_at' => 'datetime',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'all_items_received' => 'boolean',
        'customer_satisfied' => 'boolean',
        'requires_follow_up' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    // Confirmation method constants
    const METHOD_DIGITAL_SIGNATURE = 'digital_signature';
    const METHOD_SMS_CODE = 'sms_code';
    const METHOD_EMAIL_CONFIRMATION = 'email_confirmation';
    const METHOD_BIOMETRIC = 'biometric';
    const METHOD_PHOTO_VERIFICATION = 'photo_verification';
    const METHOD_AUTOMATED_SENSOR = 'automated_sensor';

    // Delivery condition constants
    const CONDITION_EXCELLENT = 'excellent';
    const CONDITION_GOOD = 'good';
    const CONDITION_ACCEPTABLE = 'acceptable';
    const CONDITION_POOR = 'poor';
    const CONDITION_DAMAGED = 'damaged';

    /**
     * Get the delivery this confirmation belongs to
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the hospital that received the delivery
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the user who confirmed the delivery
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    /**
     * Scope for confirmations requiring follow-up
     */
    public function scopeRequiresFollowUp($query)
    {
        return $query->where('requires_follow_up', true);
    }

    /**
     * Scope for confirmations with issues
     */
    public function scopeWithIssues($query)
    {
        return $query->where('all_items_received', false)
                    ->orWhere('customer_satisfied', false)
                    ->orWhereNotNull('damaged_items')
                    ->orWhereNotNull('missing_items');
    }

    /**
     * Scope for high-rated confirmations
     */
    public function scopeHighRated($query, $rating = 4)
    {
        return $query->where('overall_rating', '>=', $rating);
    }

    /**
     * Scope by delivery condition
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('delivery_condition', $condition);
    }

    /**
     * Check if delivery had issues
     */
    public function hasIssues(): bool
    {
        return !$this->all_items_received ||
               !$this->customer_satisfied ||
               !empty($this->damaged_items) ||
               !empty($this->missing_items) ||
               in_array($this->delivery_condition, [self::CONDITION_POOR, self::CONDITION_DAMAGED]);
    }

    /**
     * Check if delivery was successful
     */
    public function wasSuccessful(): bool
    {
        return $this->all_items_received &&
               $this->customer_satisfied &&
               empty($this->damaged_items) &&
               empty($this->missing_items) &&
               $this->overall_rating >= 3;
    }

    /**
     * Generate unique confirmation number
     */
    public static function generateConfirmationNumber(): string
    {
        $prefix = 'CONF';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', now()->toDateString())->count() + 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get delivery coordinates
     */
    public function getDeliveryCoordinatesAttribute(): array
    {
        return [
            'latitude' => $this->delivery_latitude,
            'longitude' => $this->delivery_longitude
        ];
    }

    /**
     * Get items summary
     */
    public function getItemsSummaryAttribute(): array
    {
        return [
            'delivered' => count($this->delivered_items ?? []),
            'missing' => count($this->missing_items ?? []),
            'damaged' => count($this->damaged_items ?? [])
        ];
    }

    /**
     * Get condition color
     */
    public function getConditionColorAttribute(): string
    {
        switch ($this->delivery_condition) {
            case self::CONDITION_EXCELLENT:
                return 'success';
            case self::CONDITION_GOOD:
                return 'info';
            case self::CONDITION_ACCEPTABLE:
                return 'warning';
            case self::CONDITION_POOR:
                return 'danger';
            case self::CONDITION_DAMAGED:
                return 'dark';
            default:
                return 'secondary';
        }
    }

    /**
     * Get rating stars
     */
    public function getRatingStarsAttribute(): string
    {
        return str_repeat('★', $this->overall_rating) . str_repeat('☆', 5 - $this->overall_rating);
    }

    /**
     * Get confirmation methods
     */
    public static function getConfirmationMethods(): array
    {
        return [
            self::METHOD_DIGITAL_SIGNATURE => 'Digital Signature',
            self::METHOD_SMS_CODE => 'SMS Code',
            self::METHOD_EMAIL_CONFIRMATION => 'Email Confirmation',
            self::METHOD_BIOMETRIC => 'Biometric',
            self::METHOD_PHOTO_VERIFICATION => 'Photo Verification',
            self::METHOD_AUTOMATED_SENSOR => 'Automated Sensor'
        ];
    }

    /**
     * Get delivery conditions
     */
    public static function getDeliveryConditions(): array
    {
        return [
            self::CONDITION_EXCELLENT => 'Excellent',
            self::CONDITION_GOOD => 'Good',
            self::CONDITION_ACCEPTABLE => 'Acceptable',
            self::CONDITION_POOR => 'Poor',
            self::CONDITION_DAMAGED => 'Damaged'
        ];
    }
}
