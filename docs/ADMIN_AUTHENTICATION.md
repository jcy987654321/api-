# Admin Authentication System

## Overview

This document describes the comprehensive admin authentication system with encryption, logging, brute-force defense, and session management.

## Features

### 1. Secure Admin Login
- **Encrypted Form Submission**: Client-side RSA encryption of passwords before transmission
- **CSRF Protection**: Built-in Laravel CSRF token protection
- **Separate Admin Guard**: Dedicated authentication guard for admin users
- **Hashed Passwords**: Argon2id password hashing using bcrypt

### 2. Login Attempt Logging
Records comprehensive login attempt information:
- IP address and geolocation (country, city, coordinates)
- User agent and device information (browser, OS, device type)
- Success/failure status with failure reasons
- Timestamp for audit trails

### 3. Brute-Force Protection
- **Login Throttling**: Maximum 5 failed attempts before account lock (configurable)
- **Account Lockout**: 30-minute lockout after max attempts (configurable)
- **Reset Window**: Failure count resets after 60 minutes of inactivity (configurable)
- **IP-based Tracking**: Tracks attempts per IP address

### 4. Admin Session Management
- **Session Timeout**: 30-minute idle session timeout (configurable)
- **Database Storage**: Sessions stored in database for tracking
- **Auto-logout**: Automatic logout on session expiration
- **Session Cleanup**: Expired sessions automatically cleaned up

### 5. User Management
- **CRUD Operations**: Create, read, update, delete admin/staff users
- **Role-based Access**: Support for multiple roles (Admin, Staff, Moderator)
- **Password Management**: Secure password resets with email notifications
- **Permission System**: Fine-grained permissions via roles

### 6. Security Notifications
- **Suspicious Activity Alerts**: Email notifications on failed login attempts
- **New Device Login**: Alert when logging in from a new device/browser
- **Account Lockout**: Notification when account is locked
- **Password Changes**: Confirmation on password resets

## Configuration

All configuration is done via environment variables in `.env`:

```env
# Authentication
AUTH_GUARD=web
AUTH_PASSWORD_BROKER=users

# Login Throttle Configuration (in seconds/minutes)
AUTH_THROTTLE_MAX_ATTEMPTS=5              # Failed attempts before lockout
AUTH_THROTTLE_LOCKOUT_DURATION=30         # Lockout duration in minutes
AUTH_THROTTLE_RESET_WINDOW=60             # Window to reset attempts in minutes

# Admin Session Configuration (in seconds)
ADMIN_SESSION_TIMEOUT=1800                # Session timeout (30 minutes)
ADMIN_SESSION_REMEMBER=40320              # Remember me duration (28 days)
```

## Database Schema

### login_logs Table
Records all login attempts with comprehensive details:
- user_id: Foreign key to users table
- ip_address: Client IP address
- user_agent: Browser/device user agent string
- status: 'success', 'failure', or 'blocked'
- failure_reason: Reason for failed login
- country, country_name: Geolocation data
- city: City from geolocation
- latitude, longitude: Geographic coordinates
- device_type: 'mobile', 'tablet', 'desktop', or 'bot'
- browser_name, browser_version: Browser information
- os_name, os_version: Operating system information
- is_mobile: Boolean flag for mobile detection
- created_at, updated_at: Timestamps

### login_throttles Table
Tracks login attempt throttling per IP/user:
- user_id: Foreign key to users table (nullable)
- email: Email address being attempted
- ip_address: Client IP address
- failed_attempts: Count of failed attempts
- first_attempt_at: Timestamp of first attempt in current window
- last_attempt_at: Timestamp of last attempt
- locked_until: Timestamp when lockout expires
- is_locked: Boolean flag for current lock status
- created_at, updated_at: Timestamps

### admin_sessions Table
Tracks admin-specific session information:
- id: Session ID (string, primary key)
- user_id: Foreign key to users table
- ip_address: Session IP address
- user_agent: Session browser/device info
- last_activity: Last activity timestamp
- expires_at: Session expiration timestamp
- created_at, updated_at: Timestamps

### users Table Additions
Enhanced user table fields:
- otp_secret: OTP secret for 2FA (future enhancement)
- otp_enabled: Boolean flag for OTP status
- failed_login_attempts: Counter for failed login attempts
- account_locked_until: Timestamp for lock expiration
- is_account_locked: Boolean flag for lock status
- password_changed_at: Timestamp of last password change
- password_history: JSON array of previous password hashes

## API Endpoints

### Authentication
- `GET /admin/login` - Show login form
- `POST /admin/login` - Submit login credentials
- `POST /admin/logout` - Logout (requires authentication)
- `GET /admin/login/public-key` - Get RSA public key for encryption

### Dashboard
- `GET /admin/dashboard` - Admin dashboard (requires authentication)

### User Management
- `GET /admin/users` - List all admin/staff users
- `GET /admin/users/create` - Show create user form
- `POST /admin/users` - Create new user
- `GET /admin/users/{id}` - View user profile
- `GET /admin/users/{id}/edit` - Show edit form
- `PUT /admin/users/{id}` - Update user
- `DELETE /admin/users/{id}` - Delete user
- `POST /admin/users/{id}/unlock` - Unlock account
- `GET /admin/users/{id}/login-history` - View login history

## Services

### EncryptionService
Handles RSA encryption/decryption and password hashing:
- `generateRsaKeyPair()`: Generate new RSA key pair
- `decryptRsa($encrypted, $privateKey)`: Decrypt RSA data
- `encryptAes($data)`: Encrypt with AES (Laravel Crypt)
- `decryptAes($encrypted)`: Decrypt AES data
- `hashPassword($password)`: Hash password with bcrypt
- `verifyPassword($password, $hash)`: Verify password

### GeolocationService
Fetches geolocation data from IP addresses:
- `getLocationByIp($ip)`: Get location data (cached 24 hours)
- Integrates with ipapi.co service
- Handles private/local IPs gracefully
- Fallback to defaults on API errors

### DeviceParserService
Parses user agent strings for device information:
- `parseUserAgent($userAgent)`: Parse complete device info
- Detects: device type, browser, browser version, OS, OS version, mobile flag

### LoginThrottleService
Manages login attempt throttling and account locking:
- `checkThrottle($request, $user)`: Check if login allowed
- `recordFailure($request, $user)`: Record failed attempt
- `recordSuccess($request, $user)`: Record successful login
- `unlock($ip)`: Manually unlock IP address
- `getAttempts($ip)`: Get current attempt count

### LoginLogService
Logs login attempts with full device/geolocation details:
- `recordLoginAttempt($request, $user, $status, $reason)`: Log attempt
- `recordBlockedLogin($request, $reason)`: Log blocked attempt
- `getLoginHistory($user, $limit)`: Get user's login history
- `getSuspiciousActivities($user, $days)`: Get failed attempts
- `getNewDeviceLogins($user, $limit)`: Get new device logins

## Middleware

### AdminAuthenticated
Protects admin routes and enforces security checks:
- Verifies user is authenticated with admin guard
- Checks account lock status
- Verifies user is active
- Enforces session timeout
- Automatically logs out expired sessions

## Notifications

### AdminUserCreated
Sent when a new admin account is created with login credentials.

### PasswordResetEmail
Sent when admin requests password reset.

### SuspiciousLoginAttempt
Sent on failed login attempts with full details.

### AccountLocked
Sent when account is locked due to failed attempts.

### NewDeviceLogin
Sent on successful login from new device/browser.

## Usage Examples

### Login Flow
1. User visits `/admin/login`
2. RSA public key is generated and stored in session
3. User enters email and password
4. Password is encrypted client-side with JSEncrypt library
5. Form is submitted with encrypted password
6. Server decrypts password with private key from session
7. Credentials are validated
8. If valid: Login is recorded with full device/location info, session created
9. If invalid: Attempt is recorded, throttle counter incremented, user is notified

### Creating Admin User
```bash
# Via seeder
php artisan db:seed --class=AdminSeeder

# Via admin panel
POST /admin/users
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123",
  "password_confirmation": "SecurePassword123",
  "is_admin": true
}
```

### Checking User Permissions
```php
// In controller or middleware
if ($user->isAdmin()) {
    // Full access
}

if ($user->hasRole('staff')) {
    // Limited access
}
```

### Viewing Login History
```php
// Get paginated history
$history = $user->loginLogs()->paginate(20);

// Get recent suspicious activities
$suspicious = LoginLog::where('user_id', $user->id)
    ->where('status', '!=', 'success')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

## Security Best Practices

1. **Always use HTTPS** in production to prevent MITM attacks
2. **Rotate RSA keys regularly** for maximum security
3. **Monitor login_logs table** for suspicious patterns
4. **Regularly review user access** and remove unnecessary accounts
5. **Enforce strong passwords** via password policy settings
6. **Enable email notifications** for all admin accounts
7. **Backup login_logs** for audit and compliance
8. **Use VPN/IP whitelisting** for additional protection
9. **Implement 2FA** via OTP (feature ready, not yet enabled)
10. **Review session logs** periodically for anomalies

## Troubleshooting

### Users can't login
- Check if account is locked: `SELECT * FROM login_throttles WHERE is_locked = 1`
- Verify account status: `SELECT status FROM users WHERE id = ?`
- Check login_logs for errors: `SELECT * FROM login_logs ORDER BY created_at DESC`

### RSA encryption issues
- Ensure OpenSSL is installed: `php -r "echo openssl_get_cert_locations();"`
- Check session configuration in `.env`
- Verify browser supports JavaScript execution

### Geolocation not working
- Check internet connectivity for API calls
- Verify API rate limits aren't exceeded
- Check cache for stale data: `php artisan cache:clear`

### Session timeout too aggressive
- Increase `ADMIN_SESSION_TIMEOUT` in `.env`
- Consider setting `SESSION_LIFETIME` in `.env`

## Future Enhancements

1. Two-Factor Authentication (OTP)
2. Device fingerprinting for anomaly detection
3. IP whitelist/blacklist management
4. WebAuthn support for passwordless login
5. Risk-based authentication
6. Login activity dashboard with analytics
7. Automated incident response
8. Integration with SIEM systems
9. Hardware security key support
10. Biometric authentication

## Support

For issues or questions, refer to the Laravel documentation:
- https://laravel.com/docs/authentication
- https://laravel.com/docs/authorization
- https://laravel.com/docs/session
