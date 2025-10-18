# API Rate Limiting Documentation

## Overview

This document describes the rate limiting strategy implemented for the Drone Delivery System API to prevent abuse and ensure fair usage.

## Rate Limit Strategy

### Public API Endpoints
**Rate Limit:** 60 requests per minute per IP address

**Applies to:**
- `GET /api/v1/public/track/{trackingNumber}` - Public delivery tracking
- `GET /api/v1/public/track/{trackingNumber}/realtime` - Real-time position updates

**Purpose:** Allow reasonable tracking without authentication while preventing abuse.

---

### Authenticated Read Operations
**Rate Limit:** 100 requests per minute per user

**Applies to:**
- User information retrieval
- Listing deliveries, drones, hospitals, supplies
- Statistics and analytics endpoints
- Notification retrieval

**Purpose:** Support dashboard and monitoring interfaces that need frequent updates.

---

### Authenticated Write Operations
**Rate Limit:** 30 requests per minute per user

**Applies to:**
- Creating delivery requests
- Updating delivery status
- Marking notifications as read

**Purpose:** Prevent spam and abusive data creation.

---

### Real-Time Tracking & Updates
**Rate Limit:** 120 requests per minute per user

**Applies to:**
- `POST /api/v1/deliveries/track/{trackingNumber}/position` - Update delivery position
- `POST /api/v1/drones/{droneId}/position` - Update drone position
- `POST /api/v1/drones/{droneId}/battery` - Update battery level
- Real-time tracking queries

**Purpose:** Support high-frequency position updates from drones (every 500ms = 120/min).

---

### OTP Generation (Special Rate Limit)
**Rate Limit:** 
- **5 requests per minute** per user
- **20 requests per hour** per user (additional layer)

**Applies to:**
- `POST /api/v1/deliveries/{id}/otp/generate` - Generate OTP
- `POST /api/v1/deliveries/{id}/otp/resend` - Resend OTP

**Purpose:** Prevent OTP spam and brute-force attacks.

---

### File Uploads
**Rate Limit:** 10 requests per minute per user

**Applies to:**
- `POST /api/v1/deliveries/{id}/photo` - Upload delivery photo
- `POST /api/v1/deliveries/{id}/signature` - Upload signature

**Purpose:** Prevent storage abuse and excessive bandwidth usage.

---

### Health Check
**Rate Limit:** 180 requests per minute per IP

**Applies to:**
- `GET /api/health` - System health status

**Purpose:** Support monitoring tools that check frequently.

---

### Login Attempts
**Rate Limit:** 5 requests per minute per IP

**Applies to:**
- Login endpoints

**Purpose:** Prevent brute-force attacks.

---

## Rate Limit Headers

All API responses include the following headers:

```
X-RateLimit-Limit: 60          # Maximum requests allowed
X-RateLimit-Remaining: 45      # Requests remaining
X-RateLimit-Reset: 1634567890  # Unix timestamp when limit resets
```

## Rate Limit Exceeded Response

When rate limit is exceeded, the API returns:

**Status Code:** `429 Too Many Requests`

**Response Body:**
```json
{
  "success": false,
  "message": "Too many requests. Please try again later.",
  "error": "rate_limit_exceeded",
  "retry_after": 60
}
```

## Best Practices for API Consumers

### 1. Respect Rate Limits
- Check response headers to track remaining requests
- Implement exponential backoff when receiving 429 responses

### 2. Optimize Request Frequency
- **For tracking:** Poll every 5-10 seconds instead of every second
- **For dashboards:** Cache data locally, refresh every 30-60 seconds
- **For position updates:** Batch updates when possible

### 3. Use WebSocket for Real-Time Updates (Future)
- Phase 2 will include WebSocket support for true real-time updates
- This will bypass rate limits for position streaming

### 4. Implement Client-Side Caching
```javascript
// Example: Cache dashboard data for 30 seconds
let cachedData = null;
let lastFetch = 0;

async function getDashboardStats() {
  const now = Date.now();
  if (cachedData && now - lastFetch < 30000) {
    return cachedData;
  }
  
  cachedData = await fetch('/api/v1/stats/dashboard');
  lastFetch = now;
  return cachedData;
}
```

### 5. Handle 429 Responses Gracefully
```javascript
async function apiRequest(url, options) {
  const response = await fetch(url, options);
  
  if (response.status === 429) {
    const data = await response.json();
    const retryAfter = data.retry_after || 60;
    
    console.warn(`Rate limit exceeded. Retry after ${retryAfter} seconds`);
    
    // Wait and retry
    await new Promise(resolve => setTimeout(resolve, retryAfter * 1000));
    return apiRequest(url, options);
  }
  
  return response;
}
```

## Rate Limit Configuration

Rate limits are configured in `app/Providers/RateLimitServiceProvider.php`.

### Modifying Rate Limits

To change rate limits for production:

1. Edit `app/Providers/RateLimitServiceProvider.php`
2. Modify the `perMinute()` or `perHour()` values
3. Clear cache: `php artisan cache:clear`
4. Restart queue workers if using queues

### Environment-Specific Limits

You can make limits environment-specific:

```php
RateLimiter::for('api-read', function (Request $request) {
    $limit = config('app.env') === 'production' ? 100 : 1000;
    return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
});
```

## Testing Rate Limits

### Using cURL

```bash
# Test public tracking (60/min limit)
for i in {1..70}; do
  curl -w "\nStatus: %{http_code}\n" \
    http://127.0.0.1:8000/api/v1/public/track/TRK-2025-001
done
```

### Using Postman

1. Create a collection with your API requests
2. Use Postman's "Run Collection" feature
3. Set iterations to exceed rate limit (e.g., 70 iterations)
4. Check for 429 responses

### Monitoring Rate Limits

Check logs for rate limit violations:

```bash
tail -f storage/logs/laravel.log | grep "rate_limit"
```

## Production Recommendations

### 1. Use Redis for Rate Limiting
In `.env`:
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Benefits:
- Faster than database
- Better for distributed systems
- Atomic operations

### 2. Adjust Limits Based on Traffic
Monitor API usage and adjust limits:
- Start conservative (current limits)
- Increase gradually based on legitimate usage patterns
- Use analytics to identify abuse patterns

### 3. Implement API Keys for Partners
For hospital systems or mobile apps:
- Issue API keys with higher rate limits
- Track usage per API key
- Bill based on usage tiers

### 4. Set Up Alerts
Monitor for:
- Frequent 429 responses (may need higher limits)
- Unusual traffic patterns
- Potential DDoS attacks

## Bypassing Rate Limits (Admin)

For administrative tasks or testing:

```php
// In routes/api.php
Route::middleware(['auth:sanctum', 'throttle:none'])->group(function () {
    // Admin endpoints without rate limits
});
```

**Warning:** Use sparingly and only for trusted admin operations.

## Contact & Support

If you encounter rate limit issues:
1. Check your request frequency
2. Review this documentation
3. Implement proper caching and polling strategies
4. Contact support if legitimate usage is affected

## Rate Limit Summary Table

| Endpoint Type | Requests/Minute | Requests/Hour | Use Case |
|---------------|-----------------|---------------|----------|
| Public Tracking | 60 | 3,600 | Public delivery tracking |
| Authenticated Read | 100 | 6,000 | Dashboard, listings |
| Authenticated Write | 30 | 1,800 | Creating/updating data |
| Real-time Updates | 120 | 7,200 | Drone position updates |
| OTP Generation | 5 | 20* | Delivery verification |
| File Uploads | 10 | 600 | Photos, signatures |
| Health Check | 180 | 10,800 | System monitoring |
| Login Attempts | 5 | 300 | Authentication |

*Additional hourly limit of 20 requests

## Changelog

### Version 1.0 (October 18, 2025)
- Initial rate limiting implementation
- Public API: 60 req/min
- Authenticated Read: 100 req/min
- Authenticated Write: 30 req/min
- Real-time: 120 req/min
- OTP: 5 req/min + 20 req/hour
- Health: 180 req/min

---

**Last Updated:** October 18, 2025  
**Version:** 1.0  
**Status:** Production Ready
