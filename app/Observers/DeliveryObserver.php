<?php

namespace App\Observers;

use App\Models\Delivery;
use App\Services\SmsService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

class DeliveryObserver
{
    protected SmsService $smsService;
    protected CacheService $cacheService;

    public function __construct(SmsService $smsService, CacheService $cacheService)
    {
        $this->smsService = $smsService;
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Delivery "updated" event.
     * Send SMS notification when delivery status changes
     */
    public function updated(Delivery $delivery): void
    {
        // Invalidate cache
        $this->cacheService->invalidateDeliveryCache($delivery);

        // Check if status has changed
        if ($delivery->isDirty('status') && config('sms.notifications.delivery_status_updates', true)) {
            $this->sendStatusNotification($delivery);
        }
    }

    /**
     * Handle the Delivery "created" event.
     */
    public function created(Delivery $delivery): void
    {
        // Invalidate cache when new delivery is created
        $this->cacheService->invalidateDeliveryCache($delivery);
    }

    /**
     * Handle the Delivery "deleted" event.
     */
    public function deleted(Delivery $delivery): void
    {
        // Invalidate cache when delivery is deleted
        $this->cacheService->invalidateDeliveryCache($delivery);
    }

    /**
     * Send SMS notification for status change
     */
    protected function sendStatusNotification(Delivery $delivery): void
    {
        // Get phone number (prefer delivery-specific, fallback to hospital)
        $phone = $delivery->delivery_hospital_phone ?? $delivery->hospital->phone ?? null;

        if (!$phone) {
            Log::warning("Cannot send SMS notification: No phone number for delivery #{$delivery->id}");
            return;
        }

        // Only send SMS for important status changes
        $notifiableStatuses = [
            'assigned',
            'in_transit',
            'approaching_destination',
            'landed',
            'delivered',
            'cancelled',
        ];

        if (in_array($delivery->status, $notifiableStatuses)) {
            try {
                $result = $this->smsService->sendDeliveryNotification(
                    $phone,
                    $delivery->tracking_number,
                    $delivery->status
                );

                if ($result['success']) {
                    Log::info("SMS notification sent for delivery #{$delivery->id}, status: {$delivery->status}");
                } else {
                    Log::warning("Failed to send SMS notification for delivery #{$delivery->id}: " . $result['message']);
                }
            } catch (\Exception $e) {
                Log::error("Error sending SMS notification for delivery #{$delivery->id}: " . $e->getMessage());
            }
        }
    }
}
