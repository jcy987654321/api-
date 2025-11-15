-- API Management System Database Schema

-- Categories table (optional, can be derived from APIs)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- APIs table
CREATE TABLE IF NOT EXISTS apis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    category VARCHAR(100) NOT NULL,
    status ENUM('active', 'beta', 'deprecated', 'draft') DEFAULT 'draft',
    documentation_url VARCHAR(500),
    api_endpoint VARCHAR(500),
    auth_type ENUM('none', 'api_key', 'oauth', 'bearer', 'basic') DEFAULT 'none',
    popularity_score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_slug (slug),
    INDEX idx_popularity (popularity_score),
    FULLTEXT idx_search (name, description)
);

-- Announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    summary TEXT,
    content LONGTEXT,
    author VARCHAR(100),
    status ENUM('published', 'draft', 'archived') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    
    INDEX idx_status (status),
    INDEX idx_slug (slug),
    INDEX idx_published (published_at),
    INDEX idx_featured (featured),
    FULLTEXT idx_search (title, summary, content)
);

-- Feedback table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    type ENUM('general', 'bug', 'feature', 'api', 'other') DEFAULT 'general',
    message TEXT NOT NULL,
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_type (type),
    INDEX idx_created (created_at)
);

-- API endpoints table (detailed endpoint information)
CREATE TABLE IF NOT EXISTS api_endpoints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_id INT NOT NULL,
    method ENUM('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS') NOT NULL,
    path VARCHAR(500) NOT NULL,
    description TEXT,
    parameters JSON,
    responses JSON,
    examples JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (api_id) REFERENCES apis(id) ON DELETE CASCADE,
    INDEX idx_api_method (api_id, method),
    INDEX idx_path (path(255))
);

-- SEO metadata cache table
CREATE TABLE IF NOT EXISTS seo_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_type VARCHAR(50) NOT NULL,
    page_identifier VARCHAR(255) NOT NULL,
    meta_data JSON NOT NULL,
    json_ld_data JSON,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_page (page_type, page_identifier),
    INDEX idx_expires (expires_at)
);

-- Sitemap generation log
CREATE TABLE IF NOT EXISTS sitemap_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    urls_count INT NOT NULL,
    file_size_bytes INT NOT NULL,
    cache_hit BOOLEAN DEFAULT FALSE,
    generation_time_ms INT,
    ping_sent BOOLEAN DEFAULT FALSE,
    ping_engines JSON
);

-- RSS feed generation log
CREATE TABLE IF NOT EXISTS rss_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    items_count INT NOT NULL,
    file_size_bytes INT NOT NULL,
    cache_hit BOOLEAN DEFAULT FALSE,
    generation_time_ms INT,
    feed_sources JSON
);

-- Settings table for dynamic configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT,
    description TEXT,
    type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (`key`, `value`, description, type) VALUES
('site_name', 'API Management System', 'Site name', 'string'),
('site_description', 'Discover and explore powerful APIs for your projects', 'Site description', 'string'),
('site_keywords', 'API, integration, development, REST, GraphQL', 'Default SEO keywords', 'string'),
('site_url', 'https://api-management.example.com', 'Base URL of the site', 'string'),
('site_author', 'API Management Team', 'Site author', 'string'),
('twitter_handle', '@api_management', 'Twitter handle for social sharing', 'string'),
('meta_image', '/assets/images/og-default.jpg', 'Default Open Graph image', 'string'),
('twitter_card_type', 'summary_large_image', 'Default Twitter card type', 'string'),
('sitemap_cache_duration', '3600', 'Sitemap cache duration in seconds', 'number'),
('sitemap_ping_engines', 'true', 'Whether to ping search engines', 'boolean'),
('rss_cache_duration', '1800', 'RSS cache duration in seconds', 'number'),
('rss_items_per_feed', '50', 'Maximum items in RSS feed', 'number'),
('custom_keywords', '', 'Custom SEO keywords', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Insert sample data
INSERT INTO apis (name, slug, description, category, status, documentation_url) VALUES
('Weather API', 'weather-api', 'Get current weather conditions and forecasts for any location worldwide. Provides temperature, humidity, wind speed, and more.', 'Weather', 'active', 'https://weather-api.example.com/docs'),
('Payment Gateway', 'payment-gateway', 'Secure payment processing API supporting credit cards, debit cards, and digital wallets. PCI-compliant and easy integration.', 'Finance', 'active', 'https://payment.example.com/docs'),
('Email Service', 'email-service', 'Send transactional emails, marketing campaigns, and automated email sequences with high deliverability rates.', 'Communication', 'beta', 'https://email.example.com/docs'),
('Geocoding API', 'geocoding-api', 'Convert addresses to coordinates and vice versa. Support for multiple countries and address formats.', 'Maps', 'active', 'https://geo.example.com/docs'),
('File Storage', 'file-storage', 'Cloud storage API for uploading, storing, and serving files. Includes image processing and CDN integration.', 'Storage', 'active', 'https://storage.example.com/docs');

INSERT INTO announcements (title, slug, summary, content, author, status, published_at) VALUES
('New Weather API Released', 'new-weather-api-released', 'We are excited to announce the launch of our new Weather API with enhanced accuracy and global coverage.', '<p>We are excited to announce the launch of our new Weather API!</p><p>Features include:</p><ul><li>Global coverage</li><li>Hourly forecasts</li><li>Historical data</li><li>Weather alerts</li></ul>', 'API Team', 'published', NOW()),
('Maintenance Scheduled', 'maintenance-scheduled', 'Scheduled maintenance will occur this weekend. Some services may experience brief interruptions.', '<p>We will be performing scheduled maintenance this Saturday from 2 AM to 4 AM UTC.</p><p>During this time, some APIs may experience brief interruptions. We apologize for any inconvenience.</p>', 'Operations', 'published', NOW() - INTERVAL 1 DAY),
('Payment Gateway Updates', 'payment-gateway-updates', 'Our Payment Gateway API now supports additional payment methods and improved security features.', '<p>Our Payment Gateway API has been updated with new features:</p><ul><li>Apple Pay support</li><li>Google Pay integration</li><li>Enhanced fraud detection</li><li>Improved error handling</li></ul>', 'Finance Team', 'published', NOW() - INTERVAL 2 DAY);