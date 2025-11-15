<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feedback Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the feedback system including email settings,
    | rate limiting, attachment settings, and security options.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    |
    | The email address where new feedback notifications will be sent.
    | If not specified, the default mail.from.address will be used.
    |
    */
    'admin_email' => env('FEEDBACK_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS')),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting settings to prevent spam and abuse.
    |
    */
    'rate_limiting' => [
        'submissions_per_hour' => env('FEEDBACK_SUBMISSIONS_PER_HOUR', 3),
        'replies_per_hour' => env('FEEDBACK_REPLIES_PER_HOUR', 5),
        'decay_seconds' => env('FEEDBACK_RATE_LIMIT_DECAY', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Attachment Settings
    |--------------------------------------------------------------------------
    |
    | Settings for file attachments in feedback submissions.
    |
    */
    'attachments' => [
        'max_size' => env('FEEDBACK_MAX_ATTACHMENT_SIZE', 5120), // KB (5MB)
        'allowed_mimes' => [
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'storage_disk' => env('FEEDBACK_STORAGE_DISK', 'local'),
        'storage_path' => env('FEEDBACK_STORAGE_PATH', 'feedback-attachments'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    |
    | Settings for email notifications sent by the feedback system.
    |
    */
    'email' => [
        'send_admin_notifications' => env('FEEDBACK_SEND_ADMIN_NOTIFICATIONS', true),
        'send_visitor_confirmations' => env('FEEDBACK_SEND_VISITOR_CONFIRMATIONS', true),
        'send_admin_reply_notifications' => env('FEEDBACK_SEND_ADMIN_REPLY_NOTIFICATIONS', true),
        'visitor_token_expiry_days' => env('FEEDBACK_TOKEN_EXPIRY_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security settings for the feedback system.
    |
    */
    'security' => [
        'honeypot_field' => env('FEEDBACK_HONEYPOT_FIELD', 'honeypot'),
        'require_consent' => env('FEEDBACK_REQUIRE_CONSENT', true),
        'validate_qq_email' => env('FEEDBACK_VALIDATE_QQ_EMAIL', true),
        'log_submissions' => env('FEEDBACK_LOG_SUBMISSIONS', true),
        'log_ip_addresses' => env('FEEDBACK_LOG_IP_ADDRESSES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Custom validation rules for feedback form fields.
    |
    */
    'validation' => [
        'subject_min_length' => env('FEEDBACK_SUBJECT_MIN_LENGTH', 3),
        'subject_max_length' => env('FEEDBACK_SUBJECT_MAX_LENGTH', 255),
        'message_min_length' => env('FEEDBACK_MESSAGE_MIN_LENGTH', 10),
        'message_max_length' => env('FEEDBACK_MESSAGE_MAX_LENGTH', 5000),
        'name_max_length' => env('FEEDBACK_NAME_MAX_LENGTH', 255),
        'admin_notes_max_length' => env('FEEDBACK_ADMIN_NOTES_MAX_LENGTH', 1000),
        'reply_min_length' => env('FEEDBACK_REPLY_MIN_LENGTH', 5),
        'reply_max_length' => env('FEEDBACK_REPLY_MAX_LENGTH', 2000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    |
    | Settings for how feedback is displayed in the admin interface.
    |
    */
    'display' => [
        'threads_per_page' => env('FEEDBACK_THREADS_PER_PAGE', 20),
        'recent_days_default' => env('FEEDBACK_RECENT_DAYS_DEFAULT', 7),
        'show_attachments' => env('FEEDBACK_SHOW_ATTACHMENTS', true),
        'allow_redaction' => env('FEEDBACK_ALLOW_REDACTION', true),
        'allow_deletion' => env('FEEDBACK_ALLOW_DELETION', true),
        'enable_sidebar' => env('FEEDBACK_ENABLE_SIDEBAR', true),
    ],
];