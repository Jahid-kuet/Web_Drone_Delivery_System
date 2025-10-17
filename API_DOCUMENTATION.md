# 🚀 Drone Delivery API - Complete Reference

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {your_access_token}
```

### Get Access Token
```http
POST /api/login
Content-Type: application/json

{
    "email": "operator@drone.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "access_token": "1|abc123...",
    "token_type": "Bearer"
}
```

---

## 📦 Delivery Confirmation Endpoints

### 1. Generate OTP
Generate a 6-digit OTP for delivery verification.

```http
POST /api/v1/delivery-confirmation/{deliveryId}/otp/generate
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "OTP generated successfully",
    "otp": "123456",
    "expires_at": "2025-10-16 13:25:00",
    "expires_in_minutes": 10
}
```

**Note**: In production, OTP should be sent via SMS and not returned in the response.

---

### 2. Verify OTP
Verify the OTP entered by the recipient.

```http
POST /api/v1/delivery-confirmation/{deliveryId}/otp/verify
Authorization: Bearer {token}
Content-Type: application/json

{
    "otp": "123456",
    "verified_by": "Dr. Ahmed Rahman"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "OTP verified successfully"
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "Invalid OTP. Please check and try again."
}
```

**Possible Error Messages:**
- "No OTP has been generated for this delivery"
- "OTP has already been verified"
- "OTP has expired. Please request a new one."
- "Invalid OTP. Please check and try again."

---

### 3. Get OTP Status
Check the current status of an OTP.

```http
GET /api/v1/delivery-confirmation/{deliveryId}/otp/status
Authorization: Bearer {token}
```

**Response (Active):**
```json
{
    "success": true,
    "data": {
        "status": "active",
        "message": "OTP is valid",
        "expires_at": "2025-10-16 13:25:00",
        "expires_in_minutes": 5
    }
}
```

**Response (Verified):**
```json
{
    "success": true,
    "data": {
        "status": "verified",
        "message": "OTP verified",
        "verified_at": "2025-10-16 13:15:00",
        "verified_by": "Dr. Ahmed Rahman"
    }
}
```

**Status Values:**
- `not_generated` - No OTP has been created
- `active` - OTP is valid and waiting for verification
- `verified` - OTP has been successfully verified
- `expired` - OTP has expired (>10 minutes old)

---

### 4. Resend OTP
Generate a new OTP (invalidates previous one).

```http
POST /api/v1/delivery-confirmation/{deliveryId}/otp/resend
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "OTP resent successfully",
    "otp": "789012",
    "expires_at": "2025-10-16 13:30:00"
}
```

---

### 5. Upload Delivery Photo
Upload a photo as proof of delivery.

```http
POST /api/v1/delivery-confirmation/{deliveryId}/photo
Authorization: Bearer {token}
Content-Type: multipart/form-data

Form Data:
- photo: [image file - max 5MB, jpeg/png/jpg]
- recipient_name: "Dr. Ahmed Rahman" (optional)
- recipient_phone: "+880-1711-123456" (optional)
- notes: "Delivered to emergency room" (optional)
```

**Response:**
```json
{
    "success": true,
    "message": "Photo uploaded successfully",
    "data": {
        "photo_path": "delivery-proofs/delivery_1_1697456789.jpg",
        "photo_url": "http://localhost:8000/storage/delivery-proofs/delivery_1_1697456789.jpg",
        "uploaded_at": "2025-10-16 13:15:00"
    }
}
```

**Validation Rules:**
- `photo`: Required, image file (jpeg/png/jpg), max 5MB
- `recipient_name`: Optional, max 255 characters
- `recipient_phone`: Optional, max 20 characters
- `notes`: Optional, max 1000 characters

---

### 6. Upload Recipient Signature
Upload a digital signature (base64 encoded image).

```http
POST /api/v1/delivery-confirmation/{deliveryId}/signature
Authorization: Bearer {token}
Content-Type: application/json

{
    "signature": "data:image/png;base64,iVBORw0KGgoAAAANS..."
}
```

**Response:**
```json
{
    "success": true,
    "message": "Signature uploaded successfully",
    "data": {
        "signature_path": "delivery-signatures/signature_1_1697456789.png",
        "signature_url": "http://localhost:8000/storage/delivery-signatures/signature_1_1697456789.png",
        "uploaded_at": "2025-10-16 13:15:30"
    }
}
```

---

### 7. Complete Confirmation (All-in-One)
Complete the entire delivery confirmation process in one request.

```http
POST /api/v1/delivery-confirmation/{deliveryId}/complete
Authorization: Bearer {token}
Content-Type: multipart/form-data

Form Data:
- otp: "123456" (required)
- photo: [image file] (required)
- recipient_name: "Dr. Ahmed Rahman" (required)
- recipient_phone: "+880-1711-123456" (optional)
- signature: "data:image/png;base64,..." (optional)
- notes: "Delivered to emergency room" (optional)
```

**Response:**
```json
{
    "success": true,
    "message": "Delivery confirmed successfully",
    "data": {
        "delivery_id": 1,
        "status": "delivered",
        "verified_at": "2025-10-16 13:15:45",
        "photo_url": "http://localhost:8000/storage/delivery-proofs/delivery_1_1697456789.jpg",
        "signature_url": "http://localhost:8000/storage/delivery-signatures/signature_1_1697456789.png"
    }
}
```

**What This Does:**
1. Verifies OTP
2. Uploads delivery photo
3. Uploads signature (if provided)
4. Updates delivery status to "delivered"
5. Updates drone status to "returning"
6. Marks delivery as verified

---

### 8. Get Confirmation Details
Retrieve all confirmation details for a delivery.

```http
GET /api/v1/delivery-confirmation/{deliveryId}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "delivery": {
            "id": 1,
            "delivery_number": "DEL-20251016-001",
            "status": "delivered",
            "hospital": "Khulna Medical College Hospital",
            "medical_supply": "Blood Type O-"
        },
        "otp_status": {
            "status": "verified",
            "message": "OTP verified",
            "verified_at": "2025-10-16 13:15:00",
            "verified_by": "Dr. Ahmed Rahman"
        },
        "photo_uploaded": true,
        "photo_url": "http://localhost:8000/storage/delivery-proofs/delivery_1_1697456789.jpg",
        "signature_uploaded": true,
        "signature_url": "http://localhost:8000/storage/delivery-signatures/signature_1_1697456789.png",
        "recipient_name": "Dr. Ahmed Rahman",
        "recipient_phone": "+880-1711-123456",
        "delivery_notes": "Delivered to emergency room",
        "is_verified": true,
        "verified_at": "2025-10-16 13:15:45",
        "completed_at": "2025-10-16 13:15:45"
    }
}
```

---

## 📱 Mobile App Integration Example

### Delivery Confirmation Flow

```javascript
// 1. Generate OTP when drone lands
async function generateOTP(deliveryId) {
    const response = await fetch(`${API_BASE}/delivery-confirmation/${deliveryId}/otp/generate`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${accessToken}`,
        }
    });
    
    const data = await response.json();
    
    if (data.success) {
        // In production: OTP is sent via SMS to recipient
        // For testing: Display OTP to operator
        console.log('OTP:', data.otp);
        alert(`OTP sent to recipient! (Test OTP: ${data.otp})`);
    }
}

// 2. Verify OTP entered by recipient
async function verifyOTP(deliveryId, otp, recipientName) {
    const response = await fetch(`${API_BASE}/delivery-confirmation/${deliveryId}/otp/verify`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            otp: otp,
            verified_by: recipientName
        })
    });
    
    const data = await response.json();
    return data.success;
}

// 3. Upload delivery photo
async function uploadPhoto(deliveryId, photoFile, recipientInfo) {
    const formData = new FormData();
    formData.append('photo', photoFile);
    formData.append('recipient_name', recipientInfo.name);
    formData.append('recipient_phone', recipientInfo.phone);
    formData.append('notes', recipientInfo.notes);
    
    const response = await fetch(`${API_BASE}/delivery-confirmation/${deliveryId}/photo`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${accessToken}`
        },
        body: formData
    });
    
    return await response.json();
}

// 4. Upload signature from canvas
async function uploadSignature(deliveryId, signatureCanvas) {
    const signatureData = signatureCanvas.toDataURL('image/png');
    
    const response = await fetch(`${API_BASE}/delivery-confirmation/${deliveryId}/signature`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            signature: signatureData
        })
    });
    
    return await response.json();
}

// 5. Complete all at once (recommended for mobile)
async function completeDelivery(deliveryId, otp, photoFile, recipientInfo, signatureCanvas) {
    const formData = new FormData();
    formData.append('otp', otp);
    formData.append('photo', photoFile);
    formData.append('recipient_name', recipientInfo.name);
    formData.append('recipient_phone', recipientInfo.phone);
    formData.append('notes', recipientInfo.notes || '');
    
    if (signatureCanvas) {
        formData.append('signature', signatureCanvas.toDataURL('image/png'));
    }
    
    const response = await fetch(`${API_BASE}/delivery-confirmation/${deliveryId}/complete`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${accessToken}`
        },
        body: formData
    });
    
    return await response.json();
}
```

---

## 🛠️ Testing with cURL

### Generate OTP
```bash
curl -X POST \
  http://localhost:8000/api/v1/delivery-confirmation/1/otp/generate \
  -H 'Authorization: Bearer YOUR_TOKEN'
```

### Verify OTP
```bash
curl -X POST \
  http://localhost:8000/api/v1/delivery-confirmation/1/otp/verify \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: application/json' \
  -d '{
    "otp": "123456",
    "verified_by": "Dr. Ahmed"
  }'
```

### Upload Photo
```bash
curl -X POST \
  http://localhost:8000/api/v1/delivery-confirmation/1/photo \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -F 'photo=@/path/to/photo.jpg' \
  -F 'recipient_name=Dr. Ahmed' \
  -F 'notes=Delivered successfully'
```

### Complete Confirmation
```bash
curl -X POST \
  http://localhost:8000/api/v1/delivery-confirmation/1/complete \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -F 'otp=123456' \
  -F 'photo=@/path/to/photo.jpg' \
  -F 'recipient_name=Dr. Ahmed'
```

---

## ⚠️ Error Handling

All endpoints follow this error response format:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": [
            "Validation error message"
        ]
    }
}
```

### Common HTTP Status Codes
- `200` - Success
- `400` - Bad Request (invalid data, OTP expired, etc.)
- `401` - Unauthorized (missing or invalid token)
- `404` - Not Found (delivery doesn't exist)
- `422` - Validation Error (invalid input)
- `500` - Server Error

---

## 📝 Best Practices

1. **Always check OTP status** before attempting verification
2. **Handle OTP expiration gracefully** - show clear message to user
3. **Compress photos** before upload to reduce bandwidth
4. **Store recipient signature** locally before upload (in case of network failure)
5. **Use the complete confirmation endpoint** for better UX on mobile
6. **Log all confirmation attempts** for audit purposes
7. **Implement retry logic** for failed uploads

---

## 🔐 Security Notes

1. **OTP Protection**:
   - Valid for 10 minutes only
   - Single-use (can't verify twice)
   - All attempts are logged

2. **File Upload Security**:
   - Max 5MB file size
   - Only jpeg/png/jpg allowed
   - Files stored with unique names
   - Access through Laravel storage system

3. **API Authentication**:
   - Bearer token required for all endpoints
   - Token should be stored securely
   - Implement token refresh mechanism

---

## 📞 Support

For API issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Database queries logged with priority queue operations
- All OTP operations logged for debugging

---

**Last Updated**: October 16, 2025  
**API Version**: 1.0  
**Laravel Version**: 11.x
