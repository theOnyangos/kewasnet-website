# KEWASNET Sitemap System Documentation

## Overview

The KEWASNET sitemap system automatically generates and maintains XML and HTML sitemaps for the website. It stores sitemap data in a database and provides both automated generation and manual management capabilities.

## Features

- **Database Storage**: All sitemap URLs are stored in a dedicated `sitemaps` table
- **Automatic Generation**: Collects URLs from blog posts, resources, pillars, events, and job opportunities
- **XML Sitemap**: Search engine-friendly XML format at `/sitemap.xml`
- **HTML Sitemap**: User-friendly HTML version at `/sitemap`
- **API Endpoints**: RESTful API for sitemap management
- **Statistics**: Track sitemap health and statistics

## Database Structure

### Sitemaps Table
```sql
CREATE TABLE `sitemaps` (
    `id` CHAR(36) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `title` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `category` VARCHAR(100) NOT NULL DEFAULT 'Other Pages',
    `changefreq` ENUM('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never') NOT NULL DEFAULT 'monthly',
    `priority` DECIMAL(2,1) NOT NULL DEFAULT '0.5',
    `last_modified` DATETIME NOT NULL,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    `deleted_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sitemaps_url` (`url`),
    KEY `idx_sitemaps_category` (`category`),
    KEY `idx_sitemaps_is_active` (`is_active`),
    KEY `idx_sitemaps_last_modified` (`last_modified`)
)
```

## Available Endpoints

### Public Endpoints
- `GET /sitemap.xml` - XML sitemap for search engines
- `GET /sitemap` - HTML sitemap for users

### Administrative Endpoints
- `GET /sitemap/generate` - Generate sitemap data (admin only)
- `GET /sitemap/statistics` - Get sitemap statistics
- `GET /sitemap/api` - Get paginated sitemap data with filters
- `PUT /sitemap/update/{id}` - Update a sitemap entry
- `DELETE /sitemap/delete/{id}` - Delete a sitemap entry

### API Parameters for `/sitemap/api`
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 50)
- `category` - Filter by category
- `search` - Search in URL, title, or description

## Command Line Tools

### Generate Sitemap
```bash
php spark sitemap:generate
```
This command:
- Collects all static and dynamic URLs
- Creates/updates sitemap entries in the database
- Deactivates URLs that no longer exist
- Shows generation statistics

### Create Sitemap Table
```bash
php spark create:sitemap-table
```
Creates the sitemap table if it doesn't exist.

## Automated Generation Setup

### Cron Job Configuration

Add the following to your crontab to regenerate the sitemap daily:

```bash
# Edit crontab
crontab -e

# Add this line for daily generation at 2:00 AM
0 2 * * * cd /path/to/your/codeigniter/project && php spark sitemap:generate

# For more frequent updates (every 6 hours)
0 */6 * * * cd /path/to/your/codeigniter/project && php spark sitemap:generate
```

### Web-based Generation

You can also trigger generation via web request (requires admin authentication):
```bash
curl -X GET "https://yoursite.com/sitemap/generate"
```

## Categories

The system automatically categorizes URLs:

1. **Main Pages** - Core website pages (Home, About, Services, Contact)
2. **WASH Topics** - Water, sanitation, climate change related pages
3. **Resources** - Resource documents and materials
4. **Blog Posts** - Blog articles and posts
5. **Events** - Event listings and details
6. **Careers** - Job opportunities
7. **Legal Pages** - Privacy policy, terms of service

## Usage Examples

### Generate Sitemap Programmatically
```php
use App\Services\SitemapService;

$sitemapService = new SitemapService();
$result = $sitemapService->generateSitemap();

if ($result['success']) {
    echo "Generated {$result['count']} URLs";
} else {
    echo "Error: {$result['message']}";
}
```

### Get Sitemap Statistics
```php
use App\Services\SitemapService;

$sitemapService = new SitemapService();
$stats = $sitemapService->getStatistics();

echo "Total URLs: {$stats['total']}";
echo "Active URLs: {$stats['active']}";
```

### Custom URL Addition
```php
use App\Models\SitemapModel;

$sitemapModel = new SitemapModel();
$sitemapModel->updateOrCreate('custom-page', [
    'title' => 'Custom Page Title',
    'description' => 'Description of the custom page',
    'category' => 'Custom Category',
    'changefreq' => 'monthly',
    'priority' => '0.8',
    'last_modified' => date('Y-m-d H:i:s'),
    'is_active' => 1
]);
```

## SEO Best Practices

1. **Regular Updates**: Set up automated generation to keep sitemaps current
2. **Priority Setting**: Use appropriate priority values (0.0 to 1.0)
3. **Change Frequency**: Set realistic changefreq values
4. **Submit to Search Engines**: Submit your XML sitemap to Google Search Console and Bing Webmaster Tools
5. **Monitor**: Use the statistics endpoint to monitor sitemap health

## Troubleshooting

### Common Issues

1. **Database Connection**: Ensure database is properly configured
2. **Model Errors**: Check that all referenced models exist and are properly named
3. **Field Mismatches**: Verify that model fields match database columns
4. **Permission Issues**: Ensure web server has write permissions for logs

### Debug Commands
```bash
# Check database connection
php spark db:table sitemaps

# Validate routes
php spark routes | grep sitemap

# Check logs
tail -f writable/logs/log-*.php
```

## Security Considerations

- Admin endpoints should be protected with proper authentication
- Validate all input data before database operations
- Use parameterized queries to prevent SQL injection
- Implement rate limiting for public API endpoints

## Performance Optimization

- Index frequently queried columns (url, category, is_active)
- Use database transactions for bulk operations
- Cache sitemap output for high-traffic sites
- Implement proper pagination for large datasets

## Future Enhancements

- **Multilingual Support**: Support for multiple language sitemaps
- **Image Sitemaps**: Include image metadata
- **News Sitemaps**: Special formatting for news content
- **Video Sitemaps**: Support for video content
- **Automatic Submission**: Automatically notify search engines of updates
