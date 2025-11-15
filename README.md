# API Management System - SEO Automation

A powerful API management system with comprehensive SEO automation features including dynamic metadata generation, sitemap.xml, and RSS feed functionality.

## Features

### SEO Automation
- **Dynamic Meta Tags**: Page-specific title, description, and keywords generation
- **Open Graph & Twitter Cards**: Social media optimization with fallback support
- **Canonical URLs**: Automatic canonical URL generation for all pages
- **JSON-LD Structured Data**: Schema.org markup for search engines
- **Configurable Settings**: Admin-configurable SEO keywords and metadata

### Sitemap Generation
- **Dynamic Sitemap**: Automatically generates sitemap.xml with all site content
- **Smart Caching**: Configurable caching for performance optimization
- **Search Engine Pings**: Automatic pings to Google and Bing after updates
- **URL Priority Management**: Intelligent priority assignment based on content importance
- **Change Frequency**: Automatic change frequency detection

### RSS Feed
- **RSS 2.0 Compliant**: Full RSS 2.0 specification support
- **Content Aggregation**: Combines latest APIs and announcements
- **Cached Generation**: Performance-optimized caching system
- **Auto-Regeneration**: Queue-based regeneration on content changes

### Content Management
- **API Directory**: Browse and discover APIs by category
- **Announcement System**: News and updates management
- **Feedback Collection**: User feedback and suggestions
- **Responsive Design**: Mobile-friendly interface

## Installation

1. **Setup Dependencies**
   ```bash
   composer install
   ```

2. **Database Setup**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. **Configuration**
   Copy and configure environment variables:
   ```bash
   cp .env.example .env
   ```

4. **Start Development Server**
   ```bash
   composer run start
   ```

## Configuration

### SEO Settings
Configure SEO metadata through the settings table or environment variables:

```php
// Basic site information
'site' => [
    'name' => 'API Management System',
    'description' => 'Discover and explore powerful APIs',
    'url' => 'https://api-management.example.com',
    'keywords' => 'API, integration, development, REST, GraphQL',
],

// SEO-specific settings
'seo' => [
    'default_title' => 'API Management System',
    'default_description' => 'Discover and explore powerful APIs',
    'custom_keywords' => '',
    'meta_image' => '/assets/images/og-default.jpg',
    'twitter_card_type' => 'summary_large_image',
],
```

### Sitemap Configuration
```php
'sitemap' => [
    'cache_duration' => 3600, // 1 hour
    'ping_engines' => true,
],
```

### RSS Configuration
```php
'rss' => [
    'cache_duration' => 1800, // 30 minutes
    'items_per_feed' => 50,
],
```

## Usage

### Accessing SEO Features

1. **Sitemap**: Visit `/sitemap.xml` to view the generated sitemap
2. **RSS Feed**: Visit `/rss.xml` to subscribe to updates
3. **Robots.txt**: Visit `/robots.txt` for crawler instructions

### CLI Commands

#### Sitemap Generation
```bash
# Generate sitemap
php commands/sitemap.php generate

# Clear sitemap cache
php commands/sitemap.php clear

# Show help
php commands/sitemap.php help
```

#### RSS Feed Generation
```bash
# Generate RSS feed
php commands/rss.php generate

# Regenerate RSS feed (clear cache first)
php commands/rss.php regenerate

# Clear RSS cache
php commands/rss.php clear

# Show help
php commands/rss.php help
```

### API Endpoints

#### Sitemap Management
- `GET /sitemap.xml` - View sitemap
- `POST /admin/sitemap/regenerate` - Regenerate sitemap

#### RSS Management
- `GET /rss.xml` - View RSS feed
- `POST /admin/rss/regenerate` - Regenerate RSS feed

## Testing

Run the test suite to validate SEO functionality:

```bash
composer test
```

### Test Coverage
- Meta tag generation and validation
- Sitemap XML structure and content
- RSS feed compliance and structure
- JSON-LD structured data validation

## File Structure

```
├── src/
│   ├── Config/          # Configuration classes
│   ├── Controllers/     # HTTP request handlers
│   ├── Services/        # Business logic services
│   └── Router.php       # URL routing
├── views/               # Template files
├── public/              # Web-accessible files
├── commands/            # CLI tools
├── database/            # Database schema
├── tests/               # Unit tests
└── config/              # Bootstrap configuration
```

## MetaService Usage

The MetaService generates page-specific SEO metadata:

```php
$metaService = new MetaService();
$meta = $metaService->generatePageMeta('api_detail', [
    'api' => [
        'name' => 'Weather API',
        'description' => 'Get weather data',
        'category' => 'Weather',
        'status' => 'active'
    ]
]);

// Returns array with:
// - title, description, keywords
// - Open Graph tags
// - Twitter Card tags
// - Canonical URL
```

## Content Types and SEO

### API Pages
- Dynamic titles based on API name and category
- Status-aware keywords (active, beta, deprecated)
- SoftwareApplication structured data
- Category-specific meta descriptions

### Announcement Pages
- Article structured data
- Publication and modification dates
- Author attribution
- Social media optimization

### Category Pages
- Category-specific titles and descriptions
- Filter-aware meta tags
- Collection structured data

## Performance Optimization

### Caching Strategy
- Sitemap: 1-hour cache with regeneration on content changes
- RSS: 30-minute cache with automatic invalidation
- Meta tags: Database-cached with expiration

### Search Engine Integration
- Automatic sitemap pings to Google and Bing
- Robots.txt with proper directives
- Canonical URL management
- Structured data for enhanced snippets

## Security Considerations

- Input sanitization for all user-generated content
- XSS protection in meta tag output
- CSRF protection for admin actions
- Rate limiting for admin endpoints

## Monitoring and Analytics

- Sitemap generation logging
- RSS feed access tracking
- SEO performance metrics
- Search engine crawl monitoring

## Contributing

1. Fork the repository
2. Create a feature branch
3. Implement your changes
4. Add tests for new functionality
5. Run the test suite
6. Submit a pull request

## License

This project is licensed under the MIT License.