# 🧪 Testing Guide for New Features

## 📋 Features to Test

1. **Emergency Priority Queue** - Auto-assignment of deliveries to drones
2. **OTP System** - Delivery verification with one-time passwords
3. **Photo Upload** - Delivery proof with photo and signature capture

---

## 🚀 Setup Before Testing

### 1. Run Database Migrations

```bash
php artisan migrate
```

Expected output:
```
Migration: 2025_10_16_131200_add_otp_fields_to_deliveries_table
Migrated:  2025_10_16_131200_add_otp_fields_to_deliveries_table
```

### 2. Verify Tables Updated

Check that deliveries table has new columns:
```sql
DESCRIBE deliveries;
```

Should include:
- `delivery_otp`
- `otp_generated_at`
- `otp_expires_at`
- `otp_verified_at`
- `otp_verified_by`
- `delivery_photo_path`
- `recipient_name`
- `recipient_phone`
- `recipient_signature_path`
- `is_verified`
- `verified_at`

### 3. Create Test Data

You need:
- ✅ At least 1 hospital
- ✅ At least 1 hub (with operational status)
- ✅ At least 2 drones (status: available, at a hub)
- ✅ At least 1 medical supply
- ✅ Delivery requests with different priorities

---

## 🧪 Test 1: Emergency Priority Queue System

### A. Manual Command Test

#### Step 1: Create Test Delivery Requests

Login as **Hospital Admin/Staff** and create delivery requests:

**Test Case 1 - Emergency Request:**
```
Medical Supply: Blood (Type O-)
Quantity: 3 units
Priority: Emergency
Hospital: Khulna Medical College Hospital
Notes: Critical patient in ICU
```

**Test Case 2 - Urgent Request:**
```
Medical Supply: Emergency Medicine
Quantity: 1 unit
Priority: Urgent
Hospital: Ad-din Akij Medical College
```

**Test Case 3 - Normal Request:**
```
Medical Supply: Regular Medicine
Quantity: 2 units
Priority: Normal
Hospital: Gazi Medical College
```

#### Step 2: Run Auto-Assignment Command

Open terminal and run:
```bash
php artisan deliveries:auto-assign
```

**Expected Output:**
```
🚁 Starting automatic delivery assignment...

📊 Queue Status:
  • Total Pending: 3
  • Emergency: 1
  • Urgent: 1
  • Normal: 1
  • Available Drones: 2
  • Oldest Wait: X minutes

🔄 Processing assignments...

✅ Assignment Results:
  • Assigned: 2
  • Failed: 1
  • Skipped: 0

📝 Assignment Details:
┌────────────────┬───────────┬──────────┬───────┐
│ Delivery       │ Drone     │ Priority │ Score │
├────────────────┼───────────┼──────────┼───────┤
│ Delivery #1    │ Drone #1  │ EMERGENCY│ 200   │
│ Delivery #2    │ Drone #2  │ URGENT   │ 90    │
└────────────────┴───────────┴──────────┴───────┘

⚠️  Failed Assignments:
  • Delivery #3: No suitable drone available

🎯 Auto-assignment completed!
```

#### Step 3: Verify Assignments in Database

Check deliveries table:
```sql
SELECT id, delivery_number, status, drone_id, assigned_at 
FROM deliveries 
WHERE status = 'assigned';
```

Should show:
- Emergency delivery assigned first (highest priority score)
- Urgent delivery assigned second
- Normal delivery still pending (no available drone)

#### Step 4: Check Emergency Alerts

Run alert check only:
```bash
php artisan deliveries:auto-assign --check-alerts
```

If any emergency deliveries have been waiting >15 minutes:
```
⚠️  EMERGENCY ALERTS:
  • CRITICAL: Emergency delivery #1 waiting 18 minutes without assignment
```

### B. Test Priority Scoring

The system calculates priority scores based on:
1. **Base Priority**: Emergency=100, Urgent=50, Normal=10
2. **Supply Multiplier**: Blood=2.0x, Medicine=1.5x, etc.
3. **Time Factor**: +2 points per hour waiting (max 20)
4. **Hospital Priority**: +10 if high-priority hospital

**Example Calculations:**

Emergency Blood Delivery (just created):
- Base: 100
- Supply: 100 × 2.0 = 200
- Time: 0 (just created)
- Hospital: +10 (if high priority)
- **Total: 210**

Urgent Medicine (waiting 2 hours):
- Base: 50
- Supply: 50 × 1.5 = 75
- Time: 2 hours × 2 = 4
- Hospital: 0
- **Total: 79**

### C. Test Scheduled Auto-Assignment

The system automatically runs every 5 minutes via Laravel scheduler.

#### Step 1: Start Laravel Scheduler

```bash
php artisan schedule:work
```

**Expected Output:**
```
Running scheduled command: deliveries:auto-assign
  ✓ Command ran successfully
```

Every 5 minutes, you should see the auto-assignment run automatically.

#### Step 2: Monitor Logs

Check logs:
```bash
tail -f storage/logs/laravel.log
```

Look for:
```
[INFO] Auto-assign: Processing X pending deliveries
[INFO] Assigned drone #X to delivery #X (Priority: 200)
[INFO] Auto-assignment completed: X assigned, X failed, X skipped
```

---

## 🧪 Test 2: OTP System for Delivery Verification

### A. Generate OTP via API

#### Test with Postman/cURL:

**Endpoint:** `POST /api/v1/deliveries/{deliveryId}/otp/generate`

**Headers:**
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Example Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/otp/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "OTP generated successfully",
  "otp": "123456",
  "expires_at": "2025-10-16 13:45:00",
  "expires_in_minutes": 10
}
```

**Note:** In production, OTP should be sent via SMS, not returned in response!

### B. Verify OTP

**Endpoint:** `POST /api/v1/deliveries/{deliveryId}/otp/verify`

**Request Body:**
```json
{
  "otp": "123456",
  "verified_by": "Dr. Ahmed Rahman"
}
```

**Example Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/otp/verify \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"otp":"123456","verified_by":"Dr. Ahmed Rahman"}'
```

**Expected Response (Success):**
```json
{
  "success": true,
  "message": "OTP verified successfully"
}
```

**Expected Response (Invalid OTP):**
```json
{
  "success": false,
  "message": "Invalid OTP. Please check and try again."
}
```

**Expected Response (Expired):**
```json
{
  "success": false,
  "message": "OTP has expired. Please request a new one."
}
```

### C. Check OTP Status

**Endpoint:** `GET /api/v1/deliveries/{deliveryId}/otp/status`

**Example Request:**
```bash
curl http://127.0.0.1:8000/api/v1/deliveries/1/otp/status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response (Active):**
```json
{
  "success": true,
  "data": {
    "status": "active",
    "message": "OTP is valid",
    "expires_at": "2025-10-16 13:45:00",
    "expires_in_minutes": 8
  }
}
```

### D. Resend OTP

**Endpoint:** `POST /api/v1/deliveries/{deliveryId}/otp/resend`

**Example Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/otp/resend \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "OTP resent successfully",
  "otp": "654321",
  "expires_at": "2025-10-16 14:00:00"
}
```

### E. Test OTP Expiration

1. Generate OTP
2. Wait 11 minutes (or modify `otp_expires_at` in database to past time)
3. Try to verify - should fail with expiration message
4. Resend OTP - should work with new code

### F. Test Model Methods Directly

In **Tinker**:
```bash
php artisan tinker
```

```php
// Get a delivery
$delivery = App\Models\Delivery::find(1);

// Generate OTP
$otp = $delivery->generateOTP();
echo "OTP: $otp\n";

// Check status
$status = $delivery->getOTPStatus();
print_r($status);

// Verify OTP
$result = $delivery->verifyOTP($otp, 'Test User');
print_r($result);

// Check if valid
echo $delivery->isOTPValid() ? "Valid" : "Invalid";
```

---

## 🧪 Test 3: Photo Upload for Delivery Proof

### A. Upload Delivery Photo

**Endpoint:** `POST /api/v1/deliveries/{deliveryId}/photo`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN
Content-Type: multipart/form-data
```

**Form Data:**
- `photo` - Image file (JPEG/PNG, max 5MB)
- `recipient_name` - String (optional)
- `recipient_phone` - String (optional)
- `notes` - Text (optional)

**Example with Postman:**
1. Select POST method
2. URL: `http://127.0.0.1:8000/api/v1/deliveries/1/photo`
3. Authorization: Bearer Token
4. Body → form-data:
   - Key: `photo`, Type: File, Value: Select image
   - Key: `recipient_name`, Type: Text, Value: "Dr. Ahmed"
   - Key: `recipient_phone`, Type: Text, Value: "+880-1711-123456"
   - Key: `notes`, Type: Text, Value: "Delivered successfully"

**Example with cURL:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/photo \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "photo=@/path/to/delivery_proof.jpg" \
  -F "recipient_name=Dr. Ahmed Rahman" \
  -F "recipient_phone=+880-1711-123456" \
  -F "notes=Package delivered in good condition"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Photo uploaded successfully",
  "data": {
    "photo_path": "delivery-proofs/delivery_1_1697461234.jpg",
    "photo_url": "http://127.0.0.1:8000/storage/delivery-proofs/delivery_1_1697461234.jpg",
    "uploaded_at": "2025-10-16T13:27:14.000000Z"
  }
}
```

**Verify Upload:**
1. Check file exists: `storage/app/public/delivery-proofs/`
2. Access URL in browser to view photo
3. Check database: `SELECT delivery_photo_path FROM deliveries WHERE id=1;`

### B. Upload Signature

**Endpoint:** `POST /api/v1/deliveries/{deliveryId}/signature`

**Request Body:**
```json
{
  "signature": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
}
```

**Example with JavaScript (from web app):**
```javascript
// Capture signature from canvas
const canvas = document.getElementById('signatureCanvas');
const signatureData = canvas.toDataURL('image/png');

// Upload
fetch('http://127.0.0.1:8000/api/v1/deliveries/1/signature', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ signature: signatureData })
})
.then(res => res.json())
.then(data => console.log(data));
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Signature uploaded successfully",
  "data": {
    "signature_path": "delivery-signatures/signature_1_1697461300.png",
    "signature_url": "http://127.0.0.1:8000/storage/delivery-signatures/signature_1_1697461300.png",
    "uploaded_at": "2025-10-16T13:28:20.000000Z"
  }
}
```

### C. Complete Delivery Confirmation (All-in-One)

**Endpoint:** `POST /api/v1/deliveries/{deliveryId}/confirm`

**Request:** Multipart form with:
- `otp` - 6-digit code (required)
- `photo` - Image file (required)
- `recipient_name` - String (required)
- `recipient_phone` - String (optional)
- `signature` - Base64 string (optional)
- `notes` - Text (optional)

**Example with Postman:**
1. Body → form-data:
   - `otp`: "123456"
   - `photo`: [Select file]
   - `recipient_name`: "Dr. Ahmed Rahman"
   - `recipient_phone`: "+880-1711-123456"
   - `signature`: "data:image/png;base64,iVBOR..."
   - `notes`: "Delivered successfully"

**Expected Response:**
```json
{
  "success": true,
  "message": "Delivery confirmed successfully",
  "data": {
    "delivery_id": 1,
    "status": "delivered",
    "verified_at": "2025-10-16T13:30:00.000000Z",
    "photo_url": "http://127.0.0.1:8000/storage/delivery-proofs/delivery_1_1697461800.jpg",
    "signature_url": "http://127.0.0.1:8000/storage/delivery-signatures/signature_1_1697461800.png"
  }
}
```

**Verify in Database:**
```sql
SELECT 
  id, 
  status, 
  delivery_otp,
  otp_verified_at,
  otp_verified_by,
  delivery_photo_path,
  recipient_name,
  is_verified,
  verified_at
FROM deliveries 
WHERE id = 1;
```

### D. Get Confirmation Details

**Endpoint:** `GET /api/v1/deliveries/{deliveryId}/confirmation`

**Example Request:**
```bash
curl http://127.0.0.1:8000/api/v1/deliveries/1/confirmation \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "delivery": {
      "id": 1,
      "delivery_number": "DEL-2025-0001",
      "status": "delivered",
      "hospital": "Khulna Medical College Hospital",
      "medical_supply": "Blood Type O-"
    },
    "otp_status": {
      "status": "verified",
      "message": "OTP verified",
      "verified_at": "2025-10-16T13:30:00.000000Z",
      "verified_by": "Dr. Ahmed Rahman"
    },
    "photo_uploaded": true,
    "photo_url": "http://127.0.0.1:8000/storage/delivery-proofs/delivery_1_1697461800.jpg",
    "signature_uploaded": true,
    "signature_url": "http://127.0.0.1:8000/storage/delivery-signatures/signature_1_1697461800.png",
    "recipient_name": "Dr. Ahmed Rahman",
    "recipient_phone": "+880-1711-123456",
    "delivery_notes": "Delivered successfully",
    "is_verified": true,
    "verified_at": "2025-10-16T13:30:00.000000Z",
    "completed_at": "2025-10-16T13:30:00.000000Z"
  }
}
```

---

## 🧪 Test 4: Integration Testing (Full Workflow)

### Complete Delivery Lifecycle Test

**Step 1: Create Emergency Delivery Request** (Hospital Staff)
- Login as hospital staff
- Navigate to Delivery Requests → New Request
- Fill form with emergency priority
- Submit

**Step 2: Auto-Assignment Runs**
```bash
php artisan deliveries:auto-assign
```
- Check delivery assigned to drone
- Verify emergency delivery got highest priority

**Step 3: Drone Departs**
- Update delivery status to "departed"
- Set `actual_departure_time`

**Step 4: Drone In Transit**
- Update status to "in_transit"
- Update coordinates periodically

**Step 5: Drone Approaching**
- Update status to "approaching_destination"
- Generate OTP:
```bash
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/otp/generate \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Step 6: Drone Lands**
- Update status to "landed"
- OTP should still be valid

**Step 7: Recipient Verification**
- Recipient receives OTP (via SMS in production)
- Operator takes delivery photo
- Recipient signs on tablet
- Complete confirmation:
```bash
# Upload all at once
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/confirm \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "otp=123456" \
  -F "photo=@delivery_photo.jpg" \
  -F "recipient_name=Dr. Ahmed" \
  -F "signature=data:image/png;base64,..." \
  -F "notes=Delivered successfully"
```

**Step 8: Verify Completion**
- Check delivery status = "delivered"
- Check `is_verified` = true
- View photo and signature in admin panel
- Drone status should update to "returning"

---

## 📊 Test Results Checklist

### Priority Queue System
- [ ] Emergency deliveries assigned first
- [ ] Priority scores calculated correctly
- [ ] Drones assigned based on battery/availability
- [ ] No drone assigned if none available
- [ ] Emergency alerts triggered after 15 min wait
- [ ] Scheduled command runs every 5 minutes
- [ ] Logs recorded correctly

### OTP System
- [ ] OTP generated successfully (6 digits)
- [ ] OTP expires after 10 minutes
- [ ] Invalid OTP rejected
- [ ] Expired OTP rejected
- [ ] OTP can be resent
- [ ] Already verified OTP rejected
- [ ] Verification records who verified
- [ ] Database timestamps correct

### Photo Upload System
- [ ] Photo uploaded to correct directory
- [ ] Photo accessible via URL
- [ ] File size validation works (max 5MB)
- [ ] File type validation works (JPEG/PNG only)
- [ ] Signature base64 decoded correctly
- [ ] Signature saved as PNG
- [ ] Recipient details stored
- [ ] Delivery notes recorded

### Integration
- [ ] Complete workflow works end-to-end
- [ ] Drone status updates correctly
- [ ] Delivery status transitions properly
- [ ] All timestamps recorded
- [ ] Photos and signatures accessible
- [ ] Verification flags set correctly

---

## 🐛 Troubleshooting

### Migration Failed
```bash
# Rollback
php artisan migrate:rollback

# Clear cache
php artisan config:clear
php artisan cache:clear

# Try again
php artisan migrate
```

### Storage Directory Not Writable
```bash
# Fix permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create symlink for public storage
php artisan storage:link
```

### OTP Not Working
- Check `otp_expires_at` in database (should be future time)
- Verify `delivery_otp` is 6 digits
- Check logs: `tail -f storage/logs/laravel.log`

### Photo Upload Failing
- Check storage directory exists: `storage/app/public/delivery-proofs/`
- Verify symlink: `public/storage` → `storage/app/public`
- Check file permissions: `ls -la storage/app/public/`
- Test with small image first (< 1MB)

### Auto-Assignment Not Working
- Verify drones exist with `status='available'`
- Check drones have `hub_id` set
- Verify hubs have `status='operational'`
- Check delivery has `status='pending'`
- Check drone `battery_level >= 30`
- Run with debug: Check logs for detailed errors

---

## 📝 API Testing Collection

Import this Postman collection for quick testing:

**File:** `postman_collection.json`

**Or use these cURL examples:**

```bash
# 1. Generate OTP
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/otp/generate \
  -H "Authorization: Bearer YOUR_TOKEN"

# 2. Verify OTP
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/otp/verify \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"otp":"123456","verified_by":"Dr. Ahmed"}'

# 3. Upload Photo
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/photo \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "photo=@photo.jpg" \
  -F "recipient_name=Dr. Ahmed"

# 4. Complete Confirmation
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/confirm \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "otp=123456" \
  -F "photo=@photo.jpg" \
  -F "recipient_name=Dr. Ahmed"

# 5. Get Confirmation Details
curl http://127.0.0.1:8000/api/v1/deliveries/1/confirmation \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## ✅ Success Criteria

All features working correctly when:

1. ✅ Emergency deliveries auto-assigned within 5 minutes
2. ✅ OTP generated, verified, and expired correctly
3. ✅ Photos uploaded and accessible via URL
4. ✅ Signatures captured and stored
5. ✅ Complete workflow from request to confirmation works
6. ✅ All database records accurate with timestamps
7. ✅ API endpoints return correct responses
8. ✅ Error handling works (invalid OTP, expired, etc.)
9. ✅ Logs recorded for audit trail
10. ✅ Scheduled commands run automatically

---

**Testing Date:** October 16, 2025  
**Features:** Priority Queue, OTP System, Photo Upload  
**Status:** Ready for Testing ✅
