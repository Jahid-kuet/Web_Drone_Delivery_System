<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMS Service for sending OTP and notifications
 * Supports multiple SMS gateways in Bangladesh
 */
class SmsService
{
    private string $gateway;
    private array $config;

    public function __construct()
    {
        $this->gateway = config('sms.default_gateway', 'sslwireless');
        $this->config = config('sms.gateways.' . $this->gateway, []);
    }

    /**
     * Send OTP SMS to recipient
     *
     * @param string $phone Phone number in Bangladesh format (01XXXXXXXXX)
     * @param string $otp 6-digit OTP code
     * @param array $data Additional data (tracking_number, hospital_name, etc.)
     * @return array ['success' => bool, 'message' => string, 'message_id' => string|null]
     */
    public function sendOTP(string $phone, string $otp, array $data = []): array
    {
        $phone = $this->formatPhone($phone);

        // Validate phone number
        if (!$this->isValidBangladeshPhone($phone)) {
            return [
                'success' => false,
                'message' => 'Invalid Bangladesh phone number format',
                'message_id' => null,
            ];
        }

        // Build message
        $trackingNumber = $data['tracking_number'] ?? 'N/A';
        $message = "Your Drone Delivery OTP: {$otp}\n"
            . "Tracking: {$trackingNumber}\n"
            . "Valid for 10 minutes. Do not share this code.\n"
            . "- Drone Delivery System";

        return $this->send($phone, $message);
    }

    /**
     * Send delivery notification SMS
     *
     * @param string $phone Phone number
     * @param string $trackingNumber Tracking number
     * @param string $status Delivery status
     * @return array
     */
    public function sendDeliveryNotification(string $phone, string $trackingNumber, string $status): array
    {
        $phone = $this->formatPhone($phone);

        $statusMessages = [
            'assigned' => 'Your delivery has been assigned to a drone.',
            'in_transit' => 'Your delivery is on the way!',
            'approaching_destination' => 'Drone approaching destination. Prepare to receive.',
            'landed' => 'Drone has landed. Please verify OTP and collect your delivery.',
            'delivered' => 'Delivery completed successfully. Thank you!',
            'cancelled' => 'Your delivery has been cancelled.',
        ];

        $message = "Drone Delivery Update\n"
            . "Tracking: {$trackingNumber}\n"
            . "Status: " . ($statusMessages[$status] ?? ucfirst(str_replace('_', ' ', $status))) . "\n"
            . "Track: dronedelivery.bd/track/{$trackingNumber}";

        return $this->send($phone, $message);
    }

    /**
     * Send generic SMS
     *
     * @param string $phone Phone number
     * @param string $message Message content
     * @return array ['success' => bool, 'message' => string, 'message_id' => string|null]
     */
    public function send(string $phone, string $message): array
    {
        $phone = $this->formatPhone($phone);

        // Check if SMS is enabled
        if (!config('sms.enabled', false)) {
            Log::info("SMS (DISABLED): To {$phone}: {$message}");
            return [
                'success' => true,
                'message' => 'SMS service disabled. Message logged.',
                'message_id' => 'test_' . uniqid(),
            ];
        }

        // Send via gateway
        try {
            switch ($this->gateway) {
                case 'sslwireless':
                    return $this->sendViaSSLWireless($phone, $message);

                case 'bulk_sms_bd':
                    return $this->sendViaBulkSmsBD($phone, $message);

                case 'alpha_net':
                    return $this->sendViaAlphaNet($phone, $message);

                case 'mimsms':
                    return $this->sendViaMimSMS($phone, $message);

                case 'log':
                default:
                    return $this->sendViaLog($phone, $message);
            }
        } catch (\Exception $e) {
            Log::error("SMS Gateway Error: " . $e->getMessage(), [
                'phone' => $phone,
                'gateway' => $this->gateway,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
                'message_id' => null,
            ];
        }
    }

    /**
     * Send via SSL Wireless (Most popular in Bangladesh)
     */
    private function sendViaSSLWireless(string $phone, string $message): array
    {
        $url = 'https://smsplus.sslwireless.com/api/v3/send-sms';

        $response = Http::asForm()->post($url, [
            'api_token' => $this->config['api_token'] ?? '',
            'sid' => $this->config['sid'] ?? '',
            'msisdn' => $phone,
            'sms' => $message,
            'csms_id' => uniqid('otp_'),
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['status']) && $data['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'message_id' => $data['smsinfo'][0]['smsid'] ?? null,
                ];
            }
        }

        Log::error('SSL Wireless SMS Failed', [
            'phone' => $phone,
            'response' => $response->body(),
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send SMS via SSL Wireless',
            'message_id' => null,
        ];
    }

    /**
     * Send via BulkSMS Bangladesh
     */
    private function sendViaBulkSmsBD(string $phone, string $message): array
    {
        $url = 'http://api.greenweb.com.bd/api.php';

        $response = Http::get($url, [
            'token' => $this->config['api_token'] ?? '',
            'to' => $phone,
            'message' => $message,
        ]);

        if ($response->successful() && str_contains($response->body(), 'Ok')) {
            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'message_id' => uniqid('bulksms_'),
            ];
        }

        Log::error('BulkSMS Bangladesh Failed', [
            'phone' => $phone,
            'response' => $response->body(),
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send SMS via BulkSMS BD',
            'message_id' => null,
        ];
    }

    /**
     * Send via Alpha Net SMS
     */
    private function sendViaAlphaNet(string $phone, string $message): array
    {
        $url = 'http://api.icombd.com/api/v1/sms/send';

        $response = Http::asForm()->post($url, [
            'api_key' => $this->config['api_key'] ?? '',
            'type' => 'text',
            'contacts' => $phone,
            'senderid' => $this->config['sender_id'] ?? '',
            'msg' => $message,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'message_id' => uniqid('alphanet_'),
            ];
        }

        Log::error('Alpha Net SMS Failed', [
            'phone' => $phone,
            'response' => $response->body(),
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send SMS via Alpha Net',
            'message_id' => null,
        ];
    }

    /**
     * Send via MIM SMS
     */
    private function sendViaMimSMS(string $phone, string $message): array
    {
        $url = 'https://esms.mimsms.com/smsapi';

        $response = Http::get($url, [
            'api_key' => $this->config['api_key'] ?? '',
            'type' => 'text',
            'contacts' => $phone,
            'senderid' => $this->config['sender_id'] ?? '',
            'msg' => $message,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'message_id' => uniqid('mimsms_'),
            ];
        }

        Log::error('MIM SMS Failed', [
            'phone' => $phone,
            'response' => $response->body(),
        ]);

        return [
            'success' => false,
            'message' => 'Failed to send SMS via MIM SMS',
            'message_id' => null,
        ];
    }

    /**
     * Log-only mode (for development/testing)
     */
    private function sendViaLog(string $phone, string $message): array
    {
        Log::info("SMS TO {$phone}: {$message}");

        return [
            'success' => true,
            'message' => 'SMS logged successfully (dev mode)',
            'message_id' => 'log_' . uniqid(),
        ];
    }

    /**
     * Format phone number to Bangladesh standard (880XXXXXXXXXX or 01XXXXXXXXX)
     *
     * @param string $phone Raw phone number
     * @return string Formatted phone number
     */
    private function formatPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove country code if present (880)
        if (str_starts_with($phone, '880')) {
            $phone = substr($phone, 3);
        }

        // Add leading 0 if missing
        if (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    /**
     * Validate Bangladesh phone number format (01XXXXXXXXX)
     *
     * @param string $phone Phone number
     * @return bool
     */
    private function isValidBangladeshPhone(string $phone): bool
    {
        // Bangladesh mobile: 01XXXXXXXXX (11 digits starting with 01)
        return preg_match('/^01[0-9]{9}$/', $phone) === 1;
    }

    /**
     * Get SMS gateway status
     *
     * @return array Gateway info and configuration status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => config('sms.enabled', false),
            'gateway' => $this->gateway,
            'configured' => !empty($this->config),
            'has_credentials' => !empty($this->config['api_token'] ?? $this->config['api_key'] ?? ''),
        ];
    }

    /**
     * Test SMS service with a test message
     *
     * @param string $phone Test phone number
     * @return array Test result
     */
    public function test(string $phone): array
    {
        $testMessage = "Test message from Drone Delivery System. " . now()->format('Y-m-d H:i:s');
        return $this->send($phone, $testMessage);
    }
}
