# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability, please email us at **security@dronedelivery.local**. Do not create public GitHub issues for security vulnerabilities.

**Response Time:** We aim to acknowledge reports within 24 hours and provide a detailed response within 72 hours.

---

## Security Features

### 1. Authentication & Authorization

#### Multi-Role System
- **Admin:** Full system access
- **Operator:** Drone and delivery management
- **Hospital:** Request and track deliveries
- **Staff:** Limited hospital operations

#### Password Requirements
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one digit
- At least one special character (!@#$%^&*(),.?":{}|<>_-+=[]\/~`)

```php
// Example: Strong password validation
use App\Rules\StrongPassword;

$request->validate([
    'password' => ['required', 'confirmed', new StrongPassword()],
]);
```

#### Session Security
- Database-based session storage
- 2-hour session lifetime
- Automatic session regeneration on login
- Secure cookies in production (HTTPS only)

### 2. API Security

#### Rate Limiting
Comprehensive rate limiting implemented to prevent abuse:

| Endpoint Type | Rate Limit | Description |
|--------------|------------|-------------|
| Public API | 60/min | Tracking, public routes |
| Read Operations | 100/min | GET requests (authenticated) |
| Write Operations | 30/min | POST, PUT, DELETE (authenticated) |
| Real-time Updates | 120/min | WebSocket, polling |
| OTP Generation | 5/min, 20/hour | SMS OTP requests |
| File Uploads | 10/min | Photo, signature uploads |
| Health Checks | 180/min | Monitoring endpoints |

```php
// Example: Apply rate limiting
Route::middleware(['auth', 'throttle:api-write'])->post('/deliveries', [DeliveryController::class, 'store']);
```

#### API Authentication
- Laravel Sanctum token-based authentication
- Token expiration and rotation
- Per-user token management
- API key verification for third-party integrations

### 3. Input Validation & Sanitization

#### All user inputs are validated:

```php
// Example: Comprehensive validation
$validated = $request->validate([
    'hospital_id' => 'required|exists:hospitals,id',
    'quantity_requested' => 'required|integer|min:1|max:1000',
    'priority' => 'required|in:low,normal,high,urgent,emergency',
    'delivery_hospital_phone' => ['required', 'regex:/^01[0-9]{9}$/'], // Bangladesh format
    'blood_group' => 'required|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
    'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
]);
```

#### Custom Validation Rules
- **StrongPassword:** Enforces password complexity
- **BangladeshPhone:** Validates 11-digit phone format (01XXXXXXXXX)
- **GPSCoordinates:** Validates latitude/longitude ranges
- **BloodGroup:** Validates standard blood type format

### 4. SQL Injection Protection

#### Eloquent ORM Usage
All database queries use Eloquent ORM with automatic parameter binding:

```php
// SECURE: Eloquent query builder
$deliveries = Delivery::where('status', $request->status)
    ->where('hospital_id', $request->hospital_id)
    ->get();

// SECURE: Raw query with parameter binding
$drones = DB::table('drones')
    ->whereRaw('battery_level >= ?', [30])
    ->get();
```

**Never use string concatenation for SQL queries.**

### 5. Cross-Site Scripting (XSS) Protection

#### Blade Template Escaping
- Use `{{ $variable }}` for automatic escaping (default)
- Use `{!! $variable !!}` ONLY with `json_encode()` or trusted content
- All user-generated content is escaped

```blade
{{-- SECURE: Automatic escaping --}}
<h1>{{ $delivery->tracking_number }}</h1>

{{-- SECURE: JSON encoding --}}
<script>
    const data = {!! json_encode($data) !!};
</script>

{{-- DANGEROUS: Avoid unescaped output --}}
{!! $userInput !!}  <!-- Never do this! -->
```

### 6. Cross-Site Request Forgery (CSRF) Protection

#### Automatic CSRF Protection
Laravel automatically verifies CSRF tokens for POST, PUT, PATCH, DELETE requests.

```blade
{{-- Always include @csrf in forms --}}
<form method="POST" action="{{ route('deliveries.store') }}">
    @csrf
    <!-- Form fields -->
</form>
```

#### API CSRF Exception
API routes are exempt from CSRF (protected by Sanctum tokens instead):

```php
// In bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'api/*', // API routes use Sanctum
    ]);
})
```

### 7. File Upload Security

#### Validation Rules
- **Allowed types:** JPEG, PNG only
- **Max size:** 5MB (5120KB)
- **Storage:** Outside public directory
- **Mime type checking:** Server-side verification

```php
// Secure file upload handling
$request->validate([
    'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
]);

$path = $request->file('photo')->store('delivery-proofs', 'public');
```

#### Storage Security
- Files stored in `storage/app/public/`
- Symbolic link to `public/storage/`
- Direct access to `storage/app/` blocked by server configuration

### 8. Environment Configuration

#### Production Settings (.env)

```env
# Application
APP_ENV=production
APP_DEBUG=false  # CRITICAL: Must be false
APP_KEY=  # Strong, unique key

# Security
SESSION_SECURE_COOKIE=true  # HTTPS only
SESSION_ENCRYPT=true  # Encrypt session data
SANCTUM_STATEFUL_DOMAINS=yourdomain.com

# Database
DB_CONNECTION=mysql  # Not SQLite in production
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=  # Secure database name
DB_USERNAME=  # Limited privilege user
DB_PASSWORD=  # Strong password

# Mail
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error  # Don't log sensitive data
```

### 9. Security Headers

#### Recommended HTTP Headers (Production)

Add to `app/Http/Middleware/SecurityHeaders.php`:

```php
public function handle($request, Closure $next) {
    $response = $next($request);
    
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    
    return $response;
}
```

### 10. Database Security

#### Connection Security
- Use SSL/TLS for database connections
- Limit database user privileges
- Separate users for read/write operations
- Regular backups with encryption

#### Sensitive Data
- Passwords: Bcrypt hashing (cost factor 12)
- API keys: Encrypted in database
- Personal data: Minimal retention, GDPR compliant

---

## Security Best Practices

### For Developers

1. **Never commit sensitive data**
   - Add `.env` to `.gitignore`
   - Use `.env.example` for templates
   - Rotate credentials regularly

2. **Keep dependencies updated**
   ```bash
   composer update
   composer audit  # Check for vulnerabilities
   npm audit fix
   ```

3. **Validate all inputs**
   - Use Laravel validation
   - Create custom rules for complex validation
   - Sanitize file uploads

4. **Use Eloquent ORM**
   - Avoid raw SQL when possible
   - Always use parameter binding
   - Enable query logging in development

5. **Test security features**
   ```bash
   php artisan test --filter Security
   ```

6. **Review code for security issues**
   - Check for XSS vulnerabilities
   - Verify CSRF protection
   - Test authentication flows
   - Review authorization logic

### For Administrators

1. **Server Hardening**
   - Disable directory listing
   - Configure proper file permissions (755/644)
   - Use firewall (UFW, iptables)
   - Enable fail2ban for brute force protection

2. **SSL/TLS Configuration**
   - Use valid SSL certificate (Let's Encrypt)
   - Enable HTTPS redirect
   - Configure HSTS header
   - Use TLS 1.2 or higher

3. **Database Security**
   - Use strong database passwords
   - Restrict database access by IP
   - Enable query logging
   - Regular backups

4. **Monitoring & Logging**
   - Monitor failed login attempts
   - Log suspicious API activity
   - Set up alerts for unusual patterns
   - Regular security audits

5. **Backup Strategy**
   - Daily database backups
   - Weekly full system backups
   - Offsite backup storage
   - Test restore procedures

---

## Common Vulnerabilities & Prevention

### 1. SQL Injection
**Prevention:** ✅ Use Eloquent ORM, parameter binding

### 2. XSS (Cross-Site Scripting)
**Prevention:** ✅ Blade escaping, Content Security Policy

### 3. CSRF (Cross-Site Request Forgery)
**Prevention:** ✅ @csrf tokens, Sanctum for APIs

### 4. Authentication Bypass
**Prevention:** ✅ Middleware checks, role verification

### 5. Insecure File Uploads
**Prevention:** ✅ Type validation, size limits, storage outside public

### 6. Session Hijacking
**Prevention:** ✅ Secure cookies, HTTPS, session regeneration

### 7. Brute Force Attacks
**Prevention:** ✅ Rate limiting, account lockout, strong passwords

### 8. Sensitive Data Exposure
**Prevention:** ✅ Environment variables, encryption, HTTPS

---

## Incident Response

### If a Security Incident Occurs:

1. **Immediate Actions:**
   - Isolate affected systems
   - Preserve logs and evidence
   - Notify security team

2. **Investigation:**
   - Identify vulnerability
   - Assess impact
   - Determine scope

3. **Remediation:**
   - Patch vulnerability
   - Update credentials
   - Deploy fix

4. **Communication:**
   - Notify affected users
   - Report to authorities (if required)
   - Document incident

5. **Post-Incident:**
   - Conduct security audit
   - Update security policies
   - Implement preventive measures

---

## Security Checklist

### Before Production Deployment:

- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production`
- [ ] Strong `APP_KEY` generated
- [ ] HTTPS enabled with valid certificate
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_ENCRYPT=true`
- [ ] Database credentials secured
- [ ] File permissions set (755/644)
- [ ] `.env` not in version control
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] Security headers configured
- [ ] Rate limiting tested
- [ ] Backup strategy implemented
- [ ] Monitoring and alerts configured
- [ ] Security audit completed
- [ ] Penetration testing performed

### Regular Maintenance:

- [ ] Weekly: Check logs for suspicious activity
- [ ] Monthly: Update dependencies (`composer update`)
- [ ] Monthly: Review user access and permissions
- [ ] Quarterly: Security audit
- [ ] Quarterly: Backup restore test
- [ ] Annually: Penetration testing
- [ ] Annually: Review and update security policies

---

## Resources

### Laravel Security Documentation
- [Laravel Security](https://laravel.com/docs/security)
- [Authentication](https://laravel.com/docs/authentication)
- [Authorization](https://laravel.com/docs/authorization)
- [CSRF Protection](https://laravel.com/docs/csrf)

### OWASP Resources
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org/)

### Tools
- [Laravel Security Checker](https://github.com/enlightn/laravel-security-checker)
- [PHPStan](https://phpstan.org/) - Static analysis
- [Psalm](https://psalm.dev/) - Security analysis

---

## Contact

**Security Team:** security@dronedelivery.local  
**Response Time:** 24-72 hours  
**Severity Levels:** Critical (24h), High (48h), Medium (72h), Low (7 days)

---

**Last Updated:** October 18, 2025  
**Version:** 1.0  
**Next Review:** January 18, 2026
