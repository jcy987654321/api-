# Content Management Modules

This document describes the content management modules implemented in the system.

## Overview

This implementation provides comprehensive backend management UIs for:
1. Site Settings
2. Announcements
3. Donations
4. Advertisements
5. Friend Links
6. Role-based Permissions
7. Audit Logs

## Installation

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed Roles and Permissions

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 3. Create Storage Link

```bash
php artisan storage:link
```

### 4. Build Assets

```bash
npm install
npm run build
```

## Modules

### 1. Site Settings

**Admin Route**: `/admin/site-settings`

**Features**:
- Site name, description, keywords (SEO)
- Favicon and logo upload
- Contact information (email, phone)
- Donation and group links
- Encrypted storage for sensitive data

**Model**: `App\Models\SiteSetting`

**Usage**:
```php
SiteSetting::get('site_name'); // Get a setting
SiteSetting::set('site_name', 'My Site'); // Set a setting
```

### 2. Announcements

**Admin Routes**: `/admin/announcements`

**Public Routes**: `/announcements`, `/api/announcements/modal`

**Features**:
- CRUD operations with status (draft, published, archived)
- Type support (info, warning, success, danger)
- Display types (modal, banner, inline)
- Scheduling (scheduled_at, expires_at)
- Priority ordering
- PJAX-compatible modal display
- Session-based "seen" tracking

**Model**: `App\Models\Announcement`

**Frontend Integration**:
The announcement modal automatically loads on page load and respects PJAX navigation. Include the JavaScript bundle to enable this feature.

### 3. Donations

**Admin Routes**: `/admin/donations`

**Public Routes**: `/donations`

**Features**:
- Multiple payment options
- QR code and icon image uploads
- Payment links
- Instructions text
- Sort ordering
- Active/inactive status

**Model**: `App\Models\DonationOption`

### 4. Advertisements

**Admin Routes**: `/admin/advertisements`, `/admin/ad-slots`

**API Routes**: `/api/ads/{slot}`, `/api/ads/{advertisement}/click`

**Features**:
- Ad slot management with positions
- Advertisement types (image, HTML, script)
- Start and end date scheduling
- Priority ordering
- Impression and click tracking
- Active/inactive status

**Models**: `App\Models\AdSlot`, `App\Models\Advertisement`

**Frontend Integration**:
```blade
<x-ad-slot identifier="header-banner" />
<x-ad-slot identifier="sidebar-ad" />
```

### 5. Friend Links

**Admin Routes**: `/admin/friend-links`

**Public Routes**: `/friend-links`, `/friend-links/apply`

**Features**:
- Application submission form (public)
- Review queue with filtering
- Approve/Reject with email notifications
- Logo upload
- Sort ordering
- Status tracking (pending, approved, rejected, inactive)

**Model**: `App\Models\FriendLink`

**Email Template**: `resources/views/emails/friend-link-status.blade.php`

### 6. Role-Based Permissions

**Features**:
- Role and permission management
- Middleware-based route protection
- User-role assignments
- Permission checking methods

**Models**: `App\Models\Role`, `App\Models\Permission`

**Middleware**: `App\Http\Middleware\CheckPermission`

**Usage**:
```php
// In routes
Route::middleware('permission:module.action')->group(function () {
    // Protected routes
});

// In code
if (auth()->user()->hasPermission('announcements.manage')) {
    // User has permission
}
```

**Default Permissions**:
- `site_settings.manage`
- `announcements.manage`, `announcements.view`
- `donations.manage`, `donations.view`
- `advertisements.manage`, `advertisements.view`
- `friend_links.manage`, `friend_links.view`
- `audit_logs.view`

### 7. Audit Logs

**Admin Routes**: `/admin/audit-logs`

**Features**:
- Automatic logging of all admin actions
- User tracking
- Before/after values for updates
- IP address and user agent tracking
- Filtering by module and action

**Model**: `App\Models\AuditLog`

**Usage**:
```php
AuditLog::log(
    'module_name',
    'action_name',
    'Description of action',
    'EntityType',
    $entityId,
    $oldValues, // optional
    $newValues  // optional
);
```

## Security Features

1. **Encrypted Settings**: Sensitive data can be encrypted in the database
2. **Permission Middleware**: All admin routes protected by permissions
3. **Audit Trail**: Complete logging of all administrative actions
4. **File Upload Validation**: Image uploads validated for type and size
5. **XSS Protection**: Blade templating with automatic escaping
6. **CSRF Protection**: All forms include CSRF tokens

## Frontend Features

### PJAX Compatibility
All admin interfaces work with PJAX for seamless navigation.

### Announcement Modals
Automatically display on page load with session-based tracking to prevent repeated displays.

### Ad Display
Simple component-based ad rendering with automatic impression tracking.

## API Endpoints

### Announcements
- `GET /api/announcements/modal` - Get active modal announcement

### Advertisements
- `GET /api/ads/{slot}` - Get advertisement for a slot
- `POST /api/ads/{advertisement}/click` - Record ad click

## Database Schema

All tables are created with proper indexes for performance:
- Foreign keys with cascade deletes
- Composite unique constraints where appropriate
- Indexes on frequently queried columns

## Configuration

### Environment Variables
Configure mail settings in `.env` for email notifications:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Storage
File uploads are stored in:
- Settings: `storage/app/public/settings/`
- Donations: `storage/app/public/donations/`
- Friend Links: `storage/app/public/friend-links/`

## Testing

To test the modules:

1. Create a test user and assign the admin role
2. Access `/admin/dashboard`
3. Navigate to each module via the admin panel
4. Test CRUD operations
5. Verify permissions work by creating users with different roles

## Troubleshooting

### Images Not Displaying
Ensure storage link is created:
```bash
php artisan storage:link
```

### Permissions Not Working
1. Check middleware is registered in `bootstrap/app.php`
2. Verify user has roles assigned
3. Check roles have permissions assigned

### Announcements Not Showing
1. Verify announcement is active and published
2. Check scheduled/expiry dates
3. Clear session storage if testing modal display

## Future Enhancements

Possible improvements:
- Rich text editor for content fields
- Image cropping/resizing
- Bulk operations for admin panels
- Export functionality for audit logs
- API rate limiting for public endpoints
- Caching for frequently accessed settings
