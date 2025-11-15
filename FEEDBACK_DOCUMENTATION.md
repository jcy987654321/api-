# Feedback System Documentation

## Overview

This feedback system provides end-to-end encrypted feedback submission and management with the following features:

- **Encrypted Storage**: All sensitive data (emails, subjects, messages) are encrypted in the database
- **Visitor Portal**: Secure thread access via signed URLs sent by email
- **Admin Interface**: Complete feedback management with status updates and moderation
- **Email Notifications**: Automated notifications for new submissions and replies
- **File Attachments**: Support for document uploads with MIME validation
- **Rate Limiting**: Protection against spam and abuse
- **Audit Logging**: Complete audit trail for compliance

## Installation

### 1. Database Setup

Run the migrations to create the necessary tables:

```bash
php artisan migrate
```

### 2. Configuration

Add the following to your `.env` file:

```env
# Feedback System Configuration
FEEDBACK_ADMIN_EMAIL="admin@example.com"
FEEDBACK_SUBMISSIONS_PER_HOUR=3
FEEDBACK_REPLIES_PER_HOUR=5
FEEDBACK_MAX_ATTACHMENT_SIZE=5120
FEEDBACK_TOKEN_EXPIRY_DAYS=30
FEEDBACK_SEND_ADMIN_NOTIFICATIONS=true
FEEDBACK_SEND_VISITOR_CONFIRMATIONS=true
FEEDBACK_SEND_ADMIN_REPLY_NOTIFICATIONS=true
FEEDBACK_REQUIRE_CONSENT=true
FEEDBACK_VALIDATE_QQ_EMAIL=true
FEEDBACK_LOG_SUBMISSIONS=true
FEEDBACK_LOG_IP_ADDRESSES=true
FEEDBACK_ENABLE_SIDEBAR=true
```

### 3. Mail Configuration

Ensure your mail configuration is set up in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Queue Setup (Recommended)

For better performance, set up a queue worker:

```bash
php artisan queue:work --tries=3
```

## Features

### Visitor Experience

1. **Feedback Form**: Accessible at `/feedback`
   - QQ email validation
   - Subject and message fields
   - Optional file attachment (5MB max)
   - Consent checkbox requirement
   - Honeypot field for spam protection

2. **Confirmation**: Automatic email with secure thread link
   - 30-day expiry for security
   - Signed URL for thread access
   - Reference ID for tracking

3. **Thread View**: Secure conversation interface at `/feedback/thread/{token}`
   - View all messages in chronological order
   - Download attachments
   - Reply to admin messages
   - Rate limited to prevent abuse

### Admin Interface

1. **Dashboard**: `/admin/feedback`
   - Statistics overview
   - Filtering and search
   - Bulk operations
   - CSV export functionality

2. **Thread Management**: `/admin/feedback/{thread}`
   - View complete conversation
   - Reply to visitors
   - Update thread status (open/closed/archived)
   - Add admin notes
   - Redact or delete messages
   - Delete entire threads

### Security Features

1. **Encryption**: All sensitive data encrypted using Laravel's built-in encryption
2. **Rate Limiting**: Configurable limits for submissions and replies
3. **Honeypot**: Bot protection via hidden form fields
4. **IP Logging**: Audit trail of submission sources
5. **Token Security**: Signed URLs with expiration

## API Endpoints

### Public Routes

- `GET /feedback` - Feedback form
- `POST /feedback` - Submit feedback
- `GET /feedback/thankyou` - Confirmation page
- `GET /feedback/thread/{token}` - View thread
- `POST /feedback/thread/{token}/reply` - Reply to thread
- `GET /feedback/attachment/{message}` - Download attachment

### Admin Routes

- `GET /admin/feedback` - Thread listing
- `GET /admin/feedback/export` - Export CSV
- `GET /admin/feedback/{thread}` - View thread
- `POST /admin/feedback/{thread}/reply` - Admin reply
- `PUT /admin/feedback/{thread}/status` - Update status
- `POST /admin/feedback/message/{message}/redact` - Redact message
- `DELETE /admin/feedback/message/{message}` - Delete message
- `DELETE /admin/feedback/{thread}` - Delete thread

## Configuration Options

### Rate Limiting

- `FEEDBACK_SUBMISSIONS_PER_HOUR`: Maximum feedback submissions per IP per hour (default: 3)
- `FEEDBACK_REPLIES_PER_HOUR`: Maximum replies per thread per hour (default: 5)

### Attachments

- `FEEDBACK_MAX_ATTACHMENT_SIZE`: Maximum file size in KB (default: 5120 = 5MB)
- Allowed MIME types: text documents, PDFs, images, spreadsheets

### Email Notifications

- `FEEDBACK_SEND_ADMIN_NOTIFICATIONS`: Notify admin of new submissions
- `FEEDBACK_SEND_VISITOR_CONFIRMATIONS`: Send confirmation emails
- `FEEDBACK_SEND_ADMIN_REPLY_NOTIFICATIONS`: Notify visitors of admin replies
- `FEEDBACK_TOKEN_EXPIRY_DAYS`: Expiry time for visitor links (default: 30)

### Security

- `FEEDBACK_REQUIRE_CONSENT`: Require consent checkbox
- `FEEDBACK_VALIDATE_QQ_EMAIL`: Enforce QQ email format
- `FEEDBACK_LOG_SUBMISSIONS`: Log all submissions
- `FEEDBACK_LOG_IP_ADDRESSES`: Log IP addresses

## Database Schema

### feedback_threads

- `id` - Primary key
- `reference_id` - Unique public identifier (e.g., "FB-ABC12345")
- `visitor_name` - Optional visitor name
- `visitor_email_encrypted` - Encrypted email address
- `subject_encrypted` - Encrypted subject line
- `status` - Thread status (open/closed/archived)
- `admin_notes_encrypted` - Encrypted admin notes
- `visitor_ip` - IP address for audit
- `visitor_user_agent` - Browser user agent
- `last_activity_at` - Last activity timestamp
- `created_at`, `updated_at` - Timestamps

### feedback_messages

- `id` - Primary key
- `feedback_thread_id` - Foreign key to threads table
- `sender_type` - Message sender (visitor/admin)
- `content_encrypted` - Encrypted message content
- `attachment_path` - File storage path
- `attachment_original_name` - Original filename
- `attachment_mime_type` - File MIME type
- `attachment_size` - File size in bytes
- `is_redacted` - Content redaction flag
- `sent_at` - Message timestamp
- `created_at`, `updated_at` - Timestamps

## Testing

Run the test suite:

```bash
php artisan test tests/Feature/FeedbackSystemTest.php
```

## Troubleshooting

### Common Issues

1. **Email Not Sending**: Check mail configuration in `.env`
2. **Attachments Not Uploading**: Verify storage permissions and disk configuration
3. **Encryption Errors**: Ensure `APP_KEY` is properly set
4. **Rate Limiting**: Adjust limits in configuration if too restrictive

### Logs

Check Laravel logs for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

## Security Considerations

1. **Regular Key Rotation**: Consider rotating `APP_KEY` periodically
2. **Access Control**: Implement proper authentication for admin routes
3. **File Storage**: Use secure storage for attachments in production
4. **Email Security**: Configure SPF/DKIM for better email deliverability
5. **Monitoring**: Monitor submission patterns for abuse

## Performance Optimization

1. **Queue Workers**: Use background queues for email processing
2. **Database Indexing**: Ensure proper indexes on frequently queried columns
3. **Caching**: Cache thread lists and statistics
4. **CDN**: Use CDN for static assets and attachments

## Extending the System

### Custom Validation

Add custom validation rules in `StoreFeedbackRequest.php`:

```php
public function rules(): array
{
    return [
        // Existing rules...
        'custom_field' => ['required', 'string', 'max:255'],
    ];
}
```

### Custom Notifications

Extend notification classes in `app/Notifications/Feedback/`:

```php
class CustomNotification extends Notification
{
    // Custom notification logic
}
```

### Additional Features

- Multi-language support
- Advanced filtering
- Automated responses
- Integration with help desk systems
- Analytics and reporting

## Support

For issues and questions, refer to the Laravel documentation and check the application logs for detailed error information.