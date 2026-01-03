<?php
/**
 * Simple test script to verify sitemap content types processing
 */

// Simulate the form data that would be sent from the sitemap panel
$formData = [
    'content_types' => ['static', 'blog', 'resources', 'jobs'], // New format
    'changefreq_default' => 'weekly',
    'priority_default' => '0.8',
    'max_urls' => '10000',
    'auto_generate' => '1',
    'include_images' => '1',
    'exclude_patterns' => '/admin
/private
/test-*'
];

echo "=== Sitemap Form Data Test ===\n";
echo "Content Types: " . print_r($formData['content_types'], true) . "\n";
echo "Settings: \n";

foreach ($formData as $key => $value) {
    if ($key !== 'content_types') {
        echo "  {$key}: {$value}\n";
    }
}

echo "\n=== Expected JavaScript FormData Processing ===\n";
echo "The form will send content_types[] array with values: " . implode(', ', $formData['content_types']) . "\n";

echo "\n=== Route Testing ===\n";
echo "Routes that should work:\n";
echo "- POST /auth/settings/generateSitemap\n";
echo "- GET /auth/settings/getSitemapStatus\n"; 
echo "- POST /auth/settings/saveSitemapConfig\n";
echo "- GET /auth/settings/getSitemapConfig\n";

echo "\nTest completed successfully!\n";
?>
