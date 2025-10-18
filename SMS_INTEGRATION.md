# SMS Integration Guide - Drone Delivery System

**Version:** 1.0  
**Last Updated:** October 19, 2025

---

## Table of Contents

1. [Overview](#overview)
2. [Supported SMS Gateways](#supported-sms-gateways)
3. [Quick Start](#quick-start)
4. [Configuration](#configuration)
5. [SMS Gateway Setup](#sms-gateway-setup)
6. [Usage Examples](#usage-examples)
7. [Testing](#testing)
8. [SMS Features](#sms-features)
9. [Troubleshooting](#troubleshooting)
10. [Production Deployment](#production-deployment)

---

## Overview

The Drone Delivery System includes comprehensive SMS integration for:

- **OTP Delivery:** Secure 6-digit OTP codes sent via SMS for delivery verification
- **Status Notifications:** Automatic SMS alerts when delivery status changes
- **Multiple Gateways:** Support for popular Bangladesh SMS providers
- **Fallback Mechanism:** Graceful degradation when SMS fails
- **Development Mode:** Log-based testing without actual SMS costs

### Key Features

✅ **Multi-Gateway Support** - SSL Wireless, BulkSMS BD, Alpha Net, MIM SMS  
✅ **Automatic OTP Sending** - SMS sent on OTP generation/resend  
✅ **Status Notifications** - Real-time SMS updates on delivery progress  
✅ **Fallback Mode** - Returns OTP in API response if SMS fails  
✅ **Rate Limiting** - Built-in protection (5 OTP/min, 20 OTP/hour)  
✅ **Bangladesh Phone Validation** - Validates 01XXXXXXXXX format  
✅ **Development Mode** - Log-only mode for testing

---

## Supported SMS Gateways

### 1. SSL Wireless (Recommended) ⭐

**Website:** https://sslwireless.com/  
**API Docs:** https://smsplus.sslwireless.com/developer-api  
**Cost:** ~0.25 BDT per SMS  
**Features:** Most reliable, 99.9% uptime, fast delivery

**Why SSL Wireless?**
- Market leader in Bangladesh
- Trusted by banks and government
- Excellent API documentation
- 24/7 support
- Bulk SMS discounts

### 2. BulkSMS Bangladesh (GreenWeb)

**Website:** https://greenweb.com.bd/  
**Cost:** ~0.20 BDT per SMS  
**Features:** Cost-effective, good for high volume

### 3. Alpha Net SMS

**Website:** https://icombd.com/  
**Cost:** ~0.22 BDT per SMS  
**Features:** Reliable, custom sender ID

### 4. MIM SMS

**Website:** https://mimsms.com/  
**Cost:** ~0.23 BDT per SMS  
**Features:** Enterprise features, analytics

### 5. Log Driver (Development)

**Use Case:** Local development and testing  
**Cost:** FREE  
**Features:** Logs SMS to `storage/logs/laravel.log`

---

## Quick Start

### Step 1: Choose Your SMS Gateway

For production, we recommend **SSL Wireless**.

### Step 2: Get API Credentials

1. Visit https://sslwireless.com/
2. Sign up for SMS service
3. Purchase SMS credits
4. Get your API token and SID from dashboard

### Step 3: Configure Environment

Edit `.env`:

```env
# Enable SMS
SMS_ENABLED=true
SMS_GATEWAY=sslwireless

# SSL Wireless credentials
SMS_SSLWIRELESS_API_TOKEN=your_api_token_here
SMS_SSLWIRELESS_SID=your_sid_here
SMS_SSLWIRELESS_SENDER_ID=DroneDelivery
```

### Step 4: Test SMS Service

```bash
php artisan tinker
```

```php
// Test SMS sending
$sms = app(\App\Services\SmsService::class);
$result = $sms->test('01712345678');
dd($result);
```

### Step 5: Test OTP Generation

```bash
curl -X POST http://localhost:8000/api/v1/deliveries/1/otp/generate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Configuration

### Environment Variables

#### Basic Configuration

```env
# Enable/disable SMS service
SMS_ENABLED=true

# Gateway selection: sslwireless, bulk_sms_bd, alpha_net, mimsms, log
SMS_GATEWAY=sslwireless
```

#### SSL Wireless Configuration

```env
SMS_SSLWIRELESS_API_TOKEN=abc123def456ghi789
SMS_SSLWIRELESS_SID=DRONEDELIVERYBD
SMS_SSLWIRELESS_SENDER_ID=DroneDelivery
```

#### BulkSMS Bangladesh Configuration

```env
SMS_BULKSMS_API_TOKEN=your_greenweb_token
```

#### Alpha Net Configuration

```env
SMS_ALPHANET_API_KEY=your_alpha_net_key
SMS_ALPHANET_SENDER_ID=DroneDelivery
```

#### MIM SMS Configuration

```env
SMS_MIMSMS_API_KEY=your_mimsms_key
SMS_MIMSMS_SENDER_ID=DroneDelivery
```

#### Notification Settings

```env
# Enable/disable specific SMS types
SMS_NOTIFY_DELIVERY_STATUS=true  # Status change notifications
SMS_NOTIFY_OTP=true              # OTP SMS
```

### Configuration File

`config/sms.php` contains all SMS settings:

```php
return [
    'enabled' => env('SMS_ENABLED', false),
    'default_gateway' => env('SMS_GATEWAY', 'log'),
    
    'otp' => [
        'length' => 6,
        'expiry_minutes' => 10,
        'max_attempts' => 3,
        'cooldown_seconds' => 60,
    ],
    
    'notifications' => [
        'delivery_status_updates' => env('SMS_NOTIFY_DELIVERY_STATUS', true),
        'otp_generation' => env('SMS_NOTIFY_OTP', true),
    ],
];
```

---

## SMS Gateway Setup

### SSL Wireless Setup (Recommended)

#### Step 1: Create Account

1. Visit https://sslwireless.com/
2. Click "Sign Up" → "SMS Service"
3. Fill in business details
4. Verify email and phone

#### Step 2: Purchase Credits

1. Login to dashboard
2. Go to "Recharge" section
3. Choose package (minimum 1000 SMS)
4. Payment options: bKash, Nagad, Bank Transfer

#### Step 3: Get API Credentials

1. Navigate to "API Settings"
2. Generate new API token
3. Note down:
   - API Token
   - SID (Sender ID)
4. Whitelist your server IP (if required)

#### Step 4: Configure Sender ID

1. Go to "Sender ID Management"
2. Request custom sender ID: "DroneDelivery"
3. Wait for approval (1-2 business days)
4. Use approved sender ID in configuration

#### Step 5: Test Connection

```php
// Test SSL Wireless connection
$sms = app(\App\Services\SmsService::class);
$status = $sms->getStatus();

if ($status['configured']) {
    echo "SMS service configured correctly!";
}
```

### BulkSMS Bangladesh Setup

1. Visit https://greenweb.com.bd/
2. Create account
3. Purchase SMS credits
4. Get API token from dashboard
5. Add to `.env`:

```env
SMS_GATEWAY=bulk_sms_bd
SMS_BULKSMS_API_TOKEN=your_token_here
```

---

## Usage Examples

### 1. Send OTP (Automatic)

OTP is automatically sent when generated via API:

```bash
# Generate OTP - SMS sent automatically
curl -X POST http://localhost:8000/api/v1/deliveries/1/otp/generate \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response (SMS enabled):**
```json
{
  "success": true,
  "message": "OTP generated successfully",
  "sms_sent": true,
  "expires_at": "2025-10-19T10:15:00.000000Z",
  "expires_in_minutes": 10
}
```

**Response (SMS disabled - fallback):**
```json
{
  "success": true,
  "message": "OTP generated successfully",
  "sms_sent": false,
  "otp": "123456",
  "note": "SMS delivery failed. OTP included in response for fallback.",
  "expires_at": "2025-10-19T10:15:00.000000Z"
}
```

**SMS Message (Recipient receives):**
```
Your Drone Delivery OTP: 123456
Tracking: TRK-20251019-001
Valid for 10 minutes. Do not share this code.
- Drone Delivery System
```

### 2. Resend OTP (Automatic SMS)

```bash
curl -X POST http://localhost:8000/api/v1/deliveries/1/otp/resend \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Manual SMS Sending (Programmatic)

```php
use App\Services\SmsService;

// Inject service
$smsService = app(SmsService::class);

// Send OTP
$result = $smsService->sendOTP(
    '01712345678',
    '123456',
    [
        'tracking_number' => 'TRK-20251019-001',
        'hospital_name' => 'Dhaka Medical College',
    ]
);

if ($result['success']) {
    echo "OTP sent successfully! Message ID: " . $result['message_id'];
} else {
    echo "Failed: " . $result['message'];
}
```

### 4. Send Delivery Notification

```php
// Send status update notification
$result = $smsService->sendDeliveryNotification(
    '01712345678',
    'TRK-20251019-001',
    'in_transit'
);
```

**SMS Message:**
```
Drone Delivery Update
Tracking: TRK-20251019-001
Status: Your delivery is on the way!
Track: dronedelivery.bd/track/TRK-20251019-001
```

### 5. Automatic Status Notifications

Status change SMS are sent automatically via Delivery Observer:

```php
// When delivery status changes, SMS is sent automatically
$delivery = Delivery::find(1);
$delivery->update(['status' => 'in_transit']);
// SMS automatically sent to recipient
```

**Notifiable Statuses:**
- `assigned` - Delivery assigned to drone
- `in_transit` - On the way
- `approaching_destination` - Almost there
- `landed` - Drone landed, ready for pickup
- `delivered` - Completed
- `cancelled` - Cancelled

### 6. Check SMS Service Status

```php
$smsService = app(SmsService::class);
$status = $smsService->getStatus();

/*
[
    'enabled' => true,
    'gateway' => 'sslwireless',
    'configured' => true,
    'has_credentials' => true,
]
*/
```

---

## Testing

### Development Mode (Log Driver)

For local testing without SMS costs:

```env
SMS_ENABLED=false
SMS_GATEWAY=log
```

All SMS will be logged to `storage/logs/laravel.log`:

```
[2025-10-19 10:00:00] local.INFO: SMS TO 01712345678: Your Drone Delivery OTP: 123456...
```

### Test SMS Sending

#### Method 1: Tinker

```bash
php artisan tinker
```

```php
$sms = app(\App\Services\SmsService::class);

// Test basic send
$sms->test('01712345678');

// Test OTP
$sms->sendOTP('01712345678', '123456', ['tracking_number' => 'TEST-001']);

// Test notification
$sms->sendDeliveryNotification('01712345678', 'TEST-001', 'in_transit');
```

#### Method 2: Test Route (Create temporarily)

Add to `routes/web.php`:

```php
Route::get('/test-sms/{phone}', function ($phone) {
    $sms = app(\App\Services\SmsService::class);
    $result = $sms->test($phone);
    return response()->json($result);
})->middleware('auth');
```

Visit: `http://localhost:8000/test-sms/01712345678`

### Verify Phone Format

```php
// Valid Bangladesh formats
01712345678  ✅
01812345678  ✅
01912345678  ✅
8801712345678  ✅ (converts to 01712345678)

// Invalid formats
1712345678  ❌ (missing leading 0)
02812345678  ❌ (landline, not mobile)
12345  ❌ (too short)
```

### Monitor SMS Logs

```bash
# Watch SMS logs in real-time
tail -f storage/logs/laravel.log | grep "SMS"
```

---

## SMS Features

### OTP System

**Features:**
- 6-digit numeric code
- 10-minute expiration
- Maximum 3 verification attempts
- 60-second cooldown between resends
- Rate limited (5/min, 20/hour)

**Security:**
- OTP not returned in API response when SMS succeeds
- Only visible in logs (development) or SMS (production)
- Auto-expiration prevents replay attacks
- Attempt limiting prevents brute force

### Status Notifications

**Automatic SMS sent for:**

| Status | SMS Message |
|--------|-------------|
| `assigned` | Your delivery has been assigned to a drone. |
| `in_transit` | Your delivery is on the way! |
| `approaching_destination` | Drone approaching destination. Prepare to receive. |
| `landed` | Drone has landed. Please verify OTP and collect your delivery. |
| `delivered` | Delivery completed successfully. Thank you! |
| `cancelled` | Your delivery has been cancelled. |

### Fallback Mechanism

When SMS fails:
1. System logs error
2. OTP included in API response
3. Frontend can display OTP to operator
4. Operator can relay OTP via phone call
5. Graceful degradation - system remains functional

### Phone Number Formatting

Automatic formatting handles:
- International format: `8801712345678` → `01712345678`
- Missing leading zero: `1712345678` → `01712345678`
- Extra spaces/characters removed
- Validation ensures 11 digits starting with `01`

---

## Troubleshooting

### SMS Not Sending

**Problem:** SMS enabled but not receiving messages

**Solutions:**

1. **Check Configuration**
   ```bash
   php artisan config:clear
   php artisan tinker
   
   >>> config('sms.enabled')
   => true
   
   >>> config('sms.default_gateway')
   => "sslwireless"
   ```

2. **Verify Credentials**
   ```php
   $sms = app(\App\Services\SmsService::class);
   dd($sms->getStatus());
   ```

3. **Check Logs**
   ```bash
   tail -100 storage/logs/laravel.log | grep -i "sms"
   ```

4. **Test Gateway Connection**
   ```php
   $sms = app(\App\Services\SmsService::class);
   $result = $sms->test('YOUR_PHONE');
   dd($result);
   ```

### API Token Invalid

**Error:** `Failed to send SMS via SSL Wireless`

**Solutions:**
- Verify API token in SSL Wireless dashboard
- Check for extra spaces in `.env`
- Ensure token hasn't expired
- Confirm IP whitelist (if enabled)

### Phone Number Rejected

**Error:** `Invalid Bangladesh phone number format`

**Solutions:**
- Use format: `01XXXXXXXXX` (11 digits)
- Remove country code or add it correctly: `880`
- Check for typos in phone number

### Rate Limit Exceeded

**Error:** `429 Too Many Requests`

**Cause:** OTP generation rate limit (5/min, 20/hour)

**Solution:**
- Wait 60 seconds before retry
- Check for duplicate requests
- Review rate limiting configuration

### SMS Credit Exhausted

**Problem:** SMS stops sending suddenly

**Solutions:**
- Check SMS credit balance in gateway dashboard
- Recharge account
- Set up auto-recharge
- Configure low balance alerts

### Delivery Observer Not Firing

**Problem:** Status changes but no SMS sent

**Solutions:**

1. **Verify Observer Registration**
   ```php
   // app/Providers/AppServiceProvider.php
   public function boot() {
       Delivery::observe(DeliveryObserver::class);
   }
   ```

2. **Clear Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan optimize:clear
   ```

3. **Check Notification Setting**
   ```env
   SMS_NOTIFY_DELIVERY_STATUS=true
   ```

---

## Production Deployment

### Pre-Deployment Checklist

- [ ] SMS gateway account created and verified
- [ ] SMS credits purchased (recommended: 10,000+ for launch)
- [ ] API credentials obtained
- [ ] Sender ID approved (if using custom)
- [ ] Server IP whitelisted (if required)
- [ ] `.env` configured with production credentials
- [ ] SMS service tested with real phone numbers
- [ ] Rate limiting configured
- [ ] Monitoring and alerts set up
- [ ] Low balance alerts configured

### Production Configuration

```env
# Production settings
SMS_ENABLED=true
SMS_GATEWAY=sslwireless

# SSL Wireless production credentials
SMS_SSLWIRELESS_API_TOKEN=prod_token_xxxxxxxxxxxxx
SMS_SSLWIRELESS_SID=APPROVED_SENDER_ID
SMS_SSLWIRELESS_SENDER_ID=DroneDelivery

# Enable all notifications
SMS_NOTIFY_DELIVERY_STATUS=true
SMS_NOTIFY_OTP=true
```

### Monitoring

#### 1. SMS Logs

Monitor SMS sending in real-time:

```bash
tail -f storage/logs/laravel.log | grep "SMS"
```

#### 2. Failed SMS Alert

Add to your monitoring system:

```php
// In app/Services/SmsService.php, add alert on failure
if (!$result['success']) {
    // Send alert to admin
    \Illuminate\Support\Facades\Mail::to('admin@example.com')
        ->send(new \App\Mail\SmsFailureAlert($phone, $message));
}
```

#### 3. Credit Balance Monitoring

Set up daily cron to check balance:

```php
// app/Console/Commands/CheckSmsBalance.php
public function handle() {
    $gateway = config('sms.default_gateway');
    
    // Implement gateway-specific balance check
    // Send alert if below threshold
}
```

#### 4. Success Rate Tracking

Log metrics to monitoring service (e.g., New Relic, Datadog):

```php
if ($result['success']) {
    \Log::channel('metrics')->info('sms.sent', ['gateway' => $this->gateway]);
} else {
    \Log::channel('metrics')->warning('sms.failed', ['gateway' => $this->gateway]);
}
```

### Cost Management

#### Estimated Monthly Costs

| SMS Volume | Cost (SSL Wireless @ 0.25 BDT) |
|------------|--------------------------------|
| 1,000 SMS | 250 BDT (~$2.27) |
| 10,000 SMS | 2,500 BDT (~$22.73) |
| 50,000 SMS | 12,500 BDT (~$113.64) |
| 100,000 SMS | 25,000 BDT (~$227.27) |

#### Cost Optimization

1. **Batch OTP with Status Update**
   - Send OTP + status in one message when possible

2. **Limit Status Notifications**
   - Only send for critical statuses (landed, delivered)
   - Configure in `config/sms.php`

3. **Use Shorter Messages**
   - Keep under 160 characters to avoid multi-part SMS
   - Current OTP message: ~110 characters ✅

4. **Monitor Failed Deliveries**
   - Failed SMS still cost money
   - Fix invalid phone numbers

### Security Best Practices

1. **Protect API Credentials**
   ```bash
   # Never commit .env
   echo ".env" >> .gitignore
   
   # Use environment-specific configs
   ```

2. **Rotate API Keys**
   - Change credentials every 3-6 months
   - Use different keys for staging/production

3. **Implement Rate Limiting**
   - Already configured (5 OTP/min, 20/hour)
   - Monitor for abuse

4. **Sanitize Phone Numbers**
   - Validate format before sending
   - Already implemented in SmsService

5. **Log SMS Activity**
   - Track all OTP generations
   - Monitor for suspicious patterns

### High Availability

#### Multiple Gateway Fallback

Configure fallback gateways in `config/sms.php`:

```php
'gateways' => [
    'primary' => 'sslwireless',
    'fallback' => 'bulk_sms_bd',
],
```

Implement in service:

```php
try {
    $result = $this->sendViaPrimary($phone, $message);
} catch (\Exception $e) {
    \Log::warning("Primary SMS gateway failed, trying fallback");
    $result = $this->sendViaFallback($phone, $message);
}
```

---

## API Reference

### SmsService Methods

#### `sendOTP($phone, $otp, $data)`

Send OTP SMS to recipient.

**Parameters:**
- `$phone` (string): Bangladesh phone number (01XXXXXXXXX)
- `$otp` (string): 6-digit OTP code
- `$data` (array): Additional data (tracking_number, hospital_name)

**Returns:**
```php
[
    'success' => true,
    'message' => 'SMS sent successfully',
    'message_id' => 'sslw_12345',
]
```

#### `sendDeliveryNotification($phone, $trackingNumber, $status)`

Send delivery status notification.

**Parameters:**
- `$phone` (string): Recipient phone
- `$trackingNumber` (string): Delivery tracking number
- `$status` (string): Delivery status

#### `send($phone, $message)`

Send generic SMS.

**Parameters:**
- `$phone` (string): Recipient phone
- `$message` (string): Message content

#### `test($phone)`

Send test message.

#### `getStatus()`

Get SMS service status.

**Returns:**
```php
[
    'enabled' => true,
    'gateway' => 'sslwireless',
    'configured' => true,
    'has_credentials' => true,
]
```

---

## Support & Resources

### SMS Gateway Support

| Gateway | Support Email | Phone | Documentation |
|---------|---------------|-------|---------------|
| SSL Wireless | support@sslwireless.com | +880 9612-444222 | https://sslwireless.com/docs |
| BulkSMS BD | support@greenweb.com.bd | +880 1713-149149 | https://greenweb.com.bd/api |
| Alpha Net | support@icombd.com | +880 1844-522000 | https://icombd.com/api |
| MIM SMS | support@mimsms.com | +880 1844-515115 | https://mimsms.com/docs |

### Drone Delivery Support

**Email:** support@dronedelivery.local  
**Technical:** dev@dronedelivery.local  
**Emergency:** +880 1XXXXXXXXX

---

**Document Version:** 1.0  
**Last Updated:** October 19, 2025  
**Next Review:** January 19, 2026
