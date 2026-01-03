<?php
/**
 * Test script to generate sitemap and check URLs
 */

require_once 'vendor/autoload.php';

// Initialize CodeIgniter
$pathsConfig = new \Config\Paths();
$app = \Config\Services::codeigniter($pathsConfig);
$app->initialize();

use App\Services\SitemapService;

$sitemapService = new SitemapService();

echo "=== Testing Sitemap Generation ===\n";

// Test generating sitemap with all content types
$contentTypes = ['static', 'blog', 'resources', 'pillars', 'jobs'];
$result = $sitemapService->generateSitemap($contentTypes);

echo "Generation Result: " . $result['status'] . "\n";
echo "Message: " . $result['message'] . "\n";

if (isset($result['count'])) {
    echo "URLs Generated: " . $result['count'] . "\n";
}

echo "\n=== Testing URL Patterns ===\n";
echo "Expected URL patterns:\n";
echo "- Blog posts: news-details/[slug]\n";
echo "- Pillars: pillar-articles/[slug]\n";
echo "- Jobs: opportunities/[slug]\n";
echo "- Resources: resources/[slug] (or excluded if no detail pages)\n";

echo "\nSitemap generation test completed!\n";
?>
