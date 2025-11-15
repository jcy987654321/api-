# Database Schema Design

## Entity Relationship Diagram

```mermaid
erDiagram
    users ||--o{ user_sessions : has
    users ||--o{ login_logs : has
    users ||--o{ feedback_threads : creates
    users ||--o{ feedback_messages : sends
    users ||--o{ friend_link_applications : submits
    users ||--o{ donations : makes
    users ||--o{ api_calls : makes
    
    roles ||--o{ users : assigns
    
    api_categories ||--o{ api_endpoints : contains
    api_categories ||--o{ api_media_assets : has
    
    api_endpoints ||--o{ api_parameters : has
    api_endpoints ||--o{ api_examples : has
    api_endpoints ||--o{ api_calls : receives
    api_endpoints ||--o{ api_daily_stats : generates
    
    visitor_sessions ||--o{ api_calls : makes
    
    feedback_threads ||--o{ feedback_messages : contains
    feedback_threads ||--o{ friend_link_applications : related_to
    
    announcements ||--o{ announcements_users : targets
    users ||--o{ announcements_users : receives
    
    friend_links ||--o{ friend_link_applications : approves
    
    site_settings }|--|| backups : creates
    
    remote_tokens ||--o{ api_calls : authenticates

    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        enum status
        timestamp last_login_at
        string last_login_ip
        text bio
        string avatar
        tinyint is_admin
        timestamps created_at, updated_at
        timestamp deleted_at
    }

    roles {
        bigint id PK
        string name UK
        string slug UK
        text description
        json permissions
        timestamps created_at, updated_at
    }

    user_sessions {
        bigint id PK
        bigint user_id FK
        string ip_address
        text user_agent
        text payload
        timestamp last_activity
        timestamps created_at, updated_at
    }

    login_logs {
        bigint id PK
        bigint user_id FK
        string ip_address
        text user_agent
        enum status
        text failure_reason
        timestamps created_at, updated_at
    }

    api_categories {
        bigint id PK
        string name
        string slug UK
        text description
        string icon
        tinyint sort_order
        enum status
        timestamps created_at, updated_at
        timestamp deleted_at
    }

    api_endpoints {
        bigint id PK
        bigint category_id FK
        string name
        string slug UK
        string method
        string path
        text description
        text response_format
        tinyint sort_order
        enum status
        bigint hits_count
        timestamp last_called_at
        timestamps created_at, updated_at
        timestamp deleted_at
    }

    api_parameters {
        bigint id PK
        bigint endpoint_id FK
        string name
        enum type
        enum location
        text description
        boolean required
        string default_value
        text validation_rules
        tinyint sort_order
        timestamps created_at, updated_at
    }

    api_examples {
        bigint id PK
        bigint endpoint_id FK
        string title
        text request_example
        text response_example
        text description
        tinyint sort_order
        timestamps created_at, updated_at
    }

    api_media_assets {
        bigint id PK
        bigint category_id FK
        string name
        string file_path
        string mime_type
        bigint file_size
        text description
        timestamps created_at, updated_at
    }

    api_calls {
        bigint id PK
        bigint endpoint_id FK
        bigint user_id FK nullable
        bigint visitor_session_id FK nullable
        bigint remote_token_id FK nullable
        string ip_address
        text user_agent
        text request_headers
        text request_body
        integer response_status
        text response_headers
        text response_body
        integer response_time_ms
        timestamps created_at, updated_at
    }

    api_daily_stats {
        bigint id PK
        bigint endpoint_id FK
        date date
        integer total_calls
        integer successful_calls
        integer failed_calls
        integer unique_visitors
        bigint total_response_time
        timestamps created_at, updated_at
    }

    visitor_sessions {
        bigint id PK
        string session_id UK
        string ip_address
        text user_agent
        text referrer
        string landing_page
        timestamp last_activity
        timestamps created_at, updated_at
    }

    announcements {
        bigint id PK
        string title
        text content
        enum type
        enum status
        timestamp starts_at
        timestamp ends_at
        timestamps created_at, updated_at
    }

    announcements_users {
        bigint announcement_id FK
        bigint user_id FK
        timestamp read_at
    }

    feedback_threads {
        bigint id PK
        bigint user_id FK
        string title
        text content
        enum status
        enum priority
        string contact_encrypted
        timestamps created_at, updated_at
        timestamp deleted_at
    }

    feedback_messages {
        bigint id PK
        bigint thread_id FK
        bigint user_id FK nullable
        text content
        enum type
        timestamps created_at, updated_at
    }

    friend_link_applications {
        bigint id PK
        bigint user_id FK nullable
        bigint feedback_thread_id FK nullable
        string site_name
        string site_url
        string description
        string contact_encrypted
        enum status
        timestamps created_at, updated_at
    }

    friend_links {
        bigint id PK
        string site_name
        string site_url
        string description
        string logo_url
        tinyint sort_order
        enum status
        timestamps created_at, updated_at
    }

    donations {
        bigint id PK
        bigint user_id FK nullable
        string donor_name
        string donor_email_encrypted
        decimal amount
        string currency
        string payment_method
        string transaction_id
        enum status
        text message
        timestamps created_at, updated_at
    }

    advertisements {
        bigint id PK
        string name
        string image_url
        string link_url
        text description
        enum position
        enum status
        timestamp starts_at
        timestamp ends_at
        integer impressions
        integer clicks
        timestamps created_at, updated_at
    }

    site_settings {
        bigint id PK
        string key UK
        text value
        string type
        text description
        boolean is_public
        timestamps created_at, updated_at
    }

    backups {
        bigint id PK
        string filename
        string file_path
        bigint file_size
        string type
        enum status
        text notes
        timestamps created_at, updated_at
    }

    remote_tokens {
        bigint id PK
        string name
        string token UK
        text description
        json permissions
        integer rate_limit_per_hour
        timestamp expires_at
        timestamps created_at, updated_at
        timestamp deleted_at
    }
```

## Table Relationships Summary

### User Management
- **users**: Core user accounts with soft deletes
- **roles**: User roles with JSON permissions
- **user_sessions**: Active user sessions
- **login_logs**: Authentication attempt logs

### API Management
- **api_categories**: Groupings for API endpoints
- **api_endpoints**: Individual API endpoints with soft deletes
- **api_parameters**: Parameters for API endpoints
- **api_examples**: Usage examples for endpoints
- **api_media_assets**: Media files for API documentation
- **api_calls**: Log of all API calls made
- **api_daily_stats**: Aggregated daily statistics for endpoints

### Analytics & Tracking
- **visitor_sessions**: Anonymous visitor tracking
- **api_daily_stats**: Performance metrics

### Communication
- **announcements**: System announcements
- **announcements_users**: Read status for announcements
- **feedback_threads**: Support/feedback conversations with soft deletes
- **feedback_messages**: Messages within feedback threads

### External Features
- **friend_link_applications**: Applications for friend links
- **friend_links**: Approved friend links
- **donations**: Donation records with encrypted contact info
- **advertisements**: Advertisement management

### System Management
- **site_settings**: Configuration key-value store
- **backups**: System backup records
- **remote_tokens**: API authentication tokens with soft deletes

## Security Features

### Encrypted Fields
- `feedback_threads.contact_encrypted`
- `friend_link_applications.contact_encrypted`
- `donations.donor_email_encrypted`

### Soft Deletes
- `users`, `api_categories`, `api_endpoints`, `feedback_threads`, `remote_tokens`

### Indexes & Performance
- Foreign key constraints on all relationships
- Unique indexes where appropriate (email, slugs, tokens)
- Composite indexes for common query patterns

### Enums
- User status management
- API endpoint states
- Feedback priority and status
- Application approval workflows
- Content publishing states