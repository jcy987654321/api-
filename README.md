# api- 强大的管理系统

A powerful administration system with secure remote API access.

## Overview

This system provides secure programmatic interfaces for administrative functions including:
- API search and metadata management
- Friend link management (approval/rejection)
- API ranking retrieval
- Feedback thread management with replies
- Statistics and visitor tracking
- Announcements management

## Features

### Token-Based Authentication
- Personal access tokens stored securely (SHA-256 hashed)
- Scope-based permission system
- Rate limiting per token
- Audit logging with IP capture

### Admin UI
- Token management interface at `/admin/tokens`
- Token creation with scope assignment
- Token revocation and activation
- Audit log viewer

### Remote API Endpoints
All endpoints under `/admin/api`:
- `GET /search` - Query API metadata
- `GET /friend-links` - List friend links
- `GET /friend-links/pending` - Get pending friend links
- `POST /friend-links/<id>/approve` - Approve friend link
- `POST /friend-links/<id>/reject` - Reject friend link
- `GET /rankings` - Get API rankings
- `GET /feedback/threads` - List feedback threads
- `GET /feedback/threads/<id>` - Get thread details
- `POST /feedback/threads/<id>/reply` - Reply to thread
- `POST /feedback/threads/<id>/close` - Close thread
- `GET /stats` - Get visitor and API call statistics
- `GET /announcements` - List announcements
- `GET /health` - Health check

### Security Features
- HTTPS required for production
- Optional encrypted responses (Base64 + HMAC-SHA256)
- Rate limiting (configurable per token)
- Comprehensive audit logging
- Token revocation support
- Scope-based access control

## Installation

```bash
# Create virtual environment
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Run the application
python run.py
```

## Usage

### Creating Access Tokens

1. Navigate to `/admin/tokens`
2. Click "Create New Token"
3. Enter token name and select scopes
4. Set rate limit (requests per minute)
5. Save the displayed token (shown only once!)

### Making API Requests

```bash
# Example: Search APIs
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/search?q=weather"

# Example: List pending friend links
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/friend-links/pending"

# Example: Get statistics
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/stats?days=30"
```

### Encrypted Responses

Add header `X-Encrypt-Response: true` to receive encrypted payloads:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "X-Encrypt-Response: true" \
  "https://your-domain.com/admin/api/search?q=weather"
```

Response format:
```json
{
  "encrypted": true,
  "payload": "base64_encoded_data",
  "signature": "hmac_sha256_signature",
  "algorithm": "HMAC-SHA256"
}
```

## Testing

```bash
# Run tests
pytest

# Run with coverage
pytest --cov=app tests/
```

## Documentation

Comprehensive API documentation is available at `docs/remote-api.md` including:
- Authentication workflow
- All endpoint specifications
- Request/response examples
- Error handling
- Best practices

## Project Structure

```
├── app/
│   ├── __init__.py          # Flask app factory
│   ├── models.py            # Database models
│   ├── auth.py              # Token authentication & rate limiting
│   ├── utils.py             # Utility functions
│   ├── errors.py            # Error handlers
│   ├── seed.py              # Initial data seeding
│   ├── routes/
│   │   ├── admin_api.py     # Remote API endpoints
│   │   └── admin_ui.py      # Admin UI routes
│   └── templates/admin/     # HTML templates
├── tests/
│   ├── conftest.py          # Test fixtures
│   └── test_admin_api.py    # API endpoint tests
├── docs/
│   └── remote-api.md        # API documentation
├── config.py                # Configuration
├── run.py                   # Application entry point
└── requirements.txt         # Python dependencies
```

## Database Models

- **RemoteToken**: Access tokens with scopes and rate limits
- **TokenAuditLog**: Audit trail for all API requests
- **APIMetadata**: API information and metadata
- **FriendLink**: Friend link submissions
- **APIRanking**: API usage rankings
- **FeedbackThread**: User feedback threads
- **FeedbackMessage**: Messages in feedback threads
- **DailyStat**: Daily visitor and API call statistics
- **Announcement**: System announcements

## Configuration

Environment variables:
- `SECRET_KEY`: Flask secret key
- `DATABASE_URL`: Database connection string
- `RATE_LIMIT_PER_MIN`: Default rate limit (default: 60)
- `AUDIT_LOG_RETENTION_DAYS`: Audit log retention (default: 90)

## Security Best Practices

1. **Always use HTTPS in production**
2. Store tokens securely (never commit to git)
3. Rotate tokens periodically
4. Use minimum required scopes (principle of least privilege)
5. Monitor audit logs for suspicious activity
6. Set appropriate rate limits
7. Verify encrypted payload signatures

## License

Proprietary - All rights reserved
