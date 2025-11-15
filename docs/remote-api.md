# Remote Admin API Documentation

## Overview

The Remote Admin API provides secure programmatic access to administrative functions including API search, friend link management, rankings retrieval, feedback management, statistics, and announcements.

All endpoints require token-based authentication using personal access tokens with scope-based permissions.

## Base URL

```
https://your-domain.com/admin/api
```

## Authentication

### Token-Based Authentication

All API requests must include an `Authorization` header with a Bearer token:

```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Obtaining Access Tokens

Access tokens are created through the Admin UI at `/admin/tokens`. Each token has:

- **Name**: Descriptive identifier
- **Scopes**: Permissions (e.g., `api:search`, `feedback:reply`)
- **Rate Limit**: Maximum requests per minute
- **Secret Key**: Used for optional payload encryption

### Available Scopes

| Scope | Description |
|-------|-------------|
| `api:search` | Query API metadata |
| `friend_links:read` | View friend links |
| `friend_links:approve` | Approve or reject friend links |
| `rankings:read` | Access API rankings |
| `feedback:read` | Read feedback threads |
| `feedback:reply` | Reply to feedback threads |
| `stats:read` | Access statistics |
| `announcements:read` | View announcements |

## Encrypted Responses

For sensitive operations, you can request encrypted responses by adding the header:

```
X-Encrypt-Response: true
```

The API will return a Base64-encoded payload with an HMAC-SHA256 signature:

```json
{
  "encrypted": true,
  "payload": "eyJ0ZXN0IjoidGVzdCJ9...",
  "signature": "abc123...",
  "algorithm": "HMAC-SHA256"
}
```

To decrypt:
1. Decode the Base64 `payload`
2. Verify the signature using your token's secret key
3. Parse the JSON data

## Rate Limiting

Each token has a configurable rate limit (default: 60 requests/minute). If you exceed this limit, you'll receive:

```json
{
  "error": "Too Many Requests",
  "message": "Rate limit exceeded"
}
```

**Status Code**: `429`

## Audit Logging

All API requests are logged with:
- Timestamp
- Endpoint and method
- IP address and user agent
- Success/failure status
- Status code

View audit logs at `/admin/audit-logs` or via the token detail page.

## API Endpoints

### 1. Health Check

Check API availability.

**Endpoint**: `GET /admin/api/health`  
**Scopes**: Any valid token  
**Parameters**: None

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-domain.com/admin/api/health
```

**Example Response**:
```json
{
  "status": "healthy",
  "timestamp": "2024-01-15T12:00:00"
}
```

---

### 2. API Search

Query API metadata with filters.

**Endpoint**: `GET /admin/api/search`  
**Scopes**: `api:search`

**Query Parameters**:
| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `q` | string | Search query (name or description) | - |
| `category` | string | Filter by category | - |
| `status` | string | Filter by status | - |
| `page` | integer | Page number | 1 |
| `per_page` | integer | Results per page (max 100) | 20 |

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/search?q=weather&category=environment&page=1"
```

**Example Response**:
```json
{
  "items": [
    {
      "id": 1,
      "name": "Weather API",
      "description": "Provides current weather and forecast data",
      "category": "environment",
      "tags": ["weather", "forecast"],
      "version": "v1",
      "endpoint": "/api/weather",
      "status": "active",
      "created_at": "2024-01-01T00:00:00"
    }
  ],
  "total": 1,
  "page": 1,
  "per_page": 20,
  "total_pages": 1
}
```

---

### 3. Friend Links Management

#### List Friend Links

**Endpoint**: `GET /admin/api/friend-links`  
**Scopes**: `friend_links:read`

**Query Parameters**:
| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `status` | string | Filter by status (`pending`, `approved`, `rejected`) | - |
| `page` | integer | Page number | 1 |
| `per_page` | integer | Results per page (max 100) | 20 |

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/friend-links?status=approved"
```

**Example Response**:
```json
{
  "items": [
    {
      "id": 1,
      "name": "TechDaily",
      "url": "https://techdaily.example.com",
      "description": "Latest technology news",
      "status": "approved",
      "created_at": "2024-01-01T00:00:00",
      "approved_at": "2024-01-02T10:30:00"
    }
  ],
  "total": 1,
  "page": 1,
  "per_page": 20,
  "total_pages": 1
}
```

#### List Pending Friend Links

**Endpoint**: `GET /admin/api/friend-links/pending`  
**Scopes**: `friend_links:read`

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-domain.com/admin/api/friend-links/pending
```

**Example Response**:
```json
{
  "items": [
    {
      "id": 2,
      "name": "APIHub",
      "url": "https://apihub.example.com",
      "description": "API developer community",
      "status": "pending",
      "created_at": "2024-01-05T00:00:00",
      "approved_at": null
    }
  ]
}
```

#### Approve Friend Link

**Endpoint**: `POST /admin/api/friend-links/<link_id>/approve`  
**Scopes**: `friend_links:approve`

**Example Request**:
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-domain.com/admin/api/friend-links/2/approve
```

**Example Response**:
```json
{
  "message": "Friend link approved",
  "link": {
    "id": 2,
    "name": "APIHub",
    "url": "https://apihub.example.com",
    "status": "approved",
    "approved_at": "2024-01-15T12:00:00"
  }
}
```

#### Reject Friend Link

**Endpoint**: `POST /admin/api/friend-links/<link_id>/reject`  
**Scopes**: `friend_links:approve`

**Example Request**:
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-domain.com/admin/api/friend-links/3/reject
```

---

### 4. API Rankings

Retrieve API usage rankings.

**Endpoint**: `GET /admin/api/rankings`  
**Scopes**: `rankings:read`

**Query Parameters**:
| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `period` | string | Period (`daily`, `weekly`, `monthly`) | `daily` |
| `limit` | integer | Maximum results (max 100) | 50 |

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/rankings?period=daily&limit=10"
```

**Example Response**:
```json
{
  "items": [
    {
      "id": 1,
      "api_id": 1,
      "api_name": "Weather API",
      "calls_count": 1000,
      "unique_users": 200,
      "period": "daily",
      "recorded_at": "2024-01-15T00:00:00"
    }
  ],
  "period": "daily"
}
```

---

### 5. Feedback Management

#### List Feedback Threads

**Endpoint**: `GET /admin/api/feedback/threads`  
**Scopes**: `feedback:read`

**Query Parameters**:
| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `status` | string | Filter by status (`open`, `closed`) | - |
| `page` | integer | Page number | 1 |
| `per_page` | integer | Results per page (max 100) | 20 |

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/feedback/threads?status=open"
```

**Example Response**:
```json
{
  "items": [
    {
      "id": 1,
      "subject": "Issue with Weather API",
      "status": "open",
      "created_by": "user@example.com",
      "created_at": "2024-01-10T10:00:00",
      "messages": [
        {
          "id": 1,
          "author": "user@example.com",
          "body": "Cannot retrieve forecast",
          "is_admin": false,
          "created_at": "2024-01-10T10:00:00"
        }
      ]
    }
  ],
  "total": 1,
  "page": 1,
  "per_page": 20,
  "total_pages": 1
}
```

#### Get Thread Details

**Endpoint**: `GET /admin/api/feedback/threads/<thread_id>`  
**Scopes**: `feedback:read`

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-domain.com/admin/api/feedback/threads/1
```

#### Reply to Thread

**Endpoint**: `POST /admin/api/feedback/threads/<thread_id>/reply`  
**Scopes**: `feedback:reply`

**Request Body**:
```json
{
  "body": "We are investigating the issue.",
  "author": "Support Agent"
}
```

**Example Request**:
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"body":"We are investigating the issue.","author":"Support Agent"}' \
  https://your-domain.com/admin/api/feedback/threads/1/reply
```

**Example Response**:
```json
{
  "message": "Reply posted",
  "reply": {
    "id": 2,
    "thread_id": 1,
    "author": "Support Agent",
    "body": "We are investigating the issue.",
    "is_admin": true,
    "created_at": "2024-01-15T12:00:00"
  }
}
```

#### Close Thread

**Endpoint**: `POST /admin/api/feedback/threads/<thread_id>/close`  
**Scopes**: `feedback:reply`

**Example Request**:
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-domain.com/admin/api/feedback/threads/1/close
```

---

### 6. Statistics

Retrieve visitor and API call statistics.

**Endpoint**: `GET /admin/api/stats`  
**Scopes**: `stats:read`

**Query Parameters**:
| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `days` | integer | Number of days (max 90) | 30 |

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/stats?days=7"
```

**Example Response**:
```json
{
  "period_days": 7,
  "start_date": "2024-01-08",
  "end_date": "2024-01-15",
  "total_visitors": 3500,
  "total_api_calls": 14000,
  "daily_breakdown": [
    {
      "id": 1,
      "stat_date": "2024-01-15",
      "visitors": 500,
      "api_calls": 2000
    }
  ]
}
```

---

### 7. Announcements

Retrieve system announcements.

**Endpoint**: `GET /admin/api/announcements`  
**Scopes**: `announcements:read`

**Query Parameters**:
| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `active_only` | boolean | Only return active announcements | `false` |
| `page` | integer | Page number | 1 |
| `per_page` | integer | Results per page (max 100) | 20 |

**Example Request**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "https://your-domain.com/admin/api/announcements?active_only=true"
```

**Example Response**:
```json
{
  "items": [
    {
      "id": 1,
      "title": "New Payment API Release",
      "body": "We have released version 3.0 of the Payments API with improved fraud detection.",
      "is_active": true,
      "created_at": "2024-01-01T00:00:00",
      "expires_at": null
    }
  ],
  "total": 1,
  "page": 1,
  "per_page": 20,
  "total_pages": 1
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "error": "Unauthorized",
  "message": "Missing or invalid Authorization header"
}
```

### 403 Forbidden
```json
{
  "error": "Forbidden",
  "message": "Missing required scopes: ['api:search']"
}
```

### 404 Not Found
```json
{
  "error": "Not Found",
  "message": "Resource not found"
}
```

### 429 Too Many Requests
```json
{
  "error": "Too Many Requests",
  "message": "Rate limit exceeded"
}
```

### 500 Internal Server Error
```json
{
  "error": "Internal Server Error",
  "message": "An unexpected error occurred"
}
```

---

## Best Practices

1. **Store tokens securely**: Never commit tokens to version control
2. **Use HTTPS**: Always use HTTPS in production
3. **Request only necessary scopes**: Follow principle of least privilege
4. **Monitor rate limits**: Track your usage to avoid hitting limits
5. **Implement retry logic**: Handle rate limiting with exponential backoff
6. **Validate signatures**: When using encrypted responses, always verify signatures
7. **Rotate tokens periodically**: Revoke and recreate tokens regularly
8. **Review audit logs**: Monitor token usage for suspicious activity

---

## Security Considerations

- All tokens are hashed using SHA-256 before storage
- Rate limiting is enforced per token per minute
- All API requests are logged with IP address and user agent
- Tokens can be revoked instantly through the Admin UI
- Optional payload encryption with HMAC-SHA256 signature verification
- HTTPS required for all production deployments

---

## Support

For issues or questions, create a feedback thread or contact the administrator.
