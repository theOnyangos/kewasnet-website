<?php

require_once 'vendor/autoload.php';

use Ramsey\Uuid\Uuid;

// List of seeders that need UUID generation
$seeders = [
    'PartnerSeeder.php',
    'EventSeeder.php',
    'EventOrganizerSeeder.php',
    'EventRegistrationSeeder.php',
    'EventTicketsSeeder.php',
    'CourseCategorySeeder.php',
    'CourseSubCategorySeeder.php',
    'CourseSeeder.php',
    'CourseCompletionSeeder.php',
    'CourseLecturesSeeder.php',
    'LectureCompletionSeeder.php',
    'TaskIconSeeder.php',
    'BlogCategoriesSeeder.php',
    'BlogTagsSeeder.php',
    'BlogPostsSeeder.php',
    'BlogNewsletterSubscriptionsSeeder.php',
    'BlogPostViewsSeeder.php',
    'UserBookmarkSeeder.php',
    'DocumentTypeSeeder.php',
    'ResourceCategorySeeder.php',
    'ResourceSeeder.php',
    'ContributorSeeder.php',
    'ResourceContributorSeeder.php',
    'FaqSeeder.php',
    'DiscussionSeeder.php',
    'ForumSeeder.php',
    'JobOpportunitiesSeeder.php',
    'JobApplicationsSeeder.php',
    'SitemapSettingsSeeder.php',
    'DocumentResourceCategoriesSeeder.php',
];

$seederPath = '/Users/denisonyango/Projects/kewasnet-website/app/Database/Seeds/';

foreach ($seeders as $seeder) {
    $filePath = $seederPath . $seeder;
    
    if (!file_exists($filePath)) {
        echo "Skipping {$seeder} - file not found\n";
        continue;
    }
    
    echo "Processing {$seeder}...\n";
    
    $content = file_get_contents($filePath);
    
    // Count how many array entries need UUIDs
    preg_match_all('/\[\s*\n/m', $content, $matches);
    $arrayCount = count($matches[0]);
    
    // Replace each array opening with UUID
    $count = 0;
    $content = preg_replace_callback(
        '/(\$data\s*=\s*\[(?:\s*\/\/[^\n]*\n)?)\s*\[\s*\n/m',
        function($matches) use (&$count) {
            $count++;
            $uuid = Uuid::uuid4()->toString();
            return $matches[1] . "\n            [\n                'id' => '{$uuid}',\n";
        },
        $content,
        1 // Only replace first occurrence after $data =
    );
    
    // Now replace all subsequent array openings within the data array
    $content = preg_replace_callback(
        '/,\s*\[\s*\n(?=\s*[\'"])/m',
        function($matches) use (&$count) {
            $count++;
            $uuid = Uuid::uuid4()->toString();
            return ",\n            [\n                'id' => '{$uuid}',\n";
        },
        $content
    );
    
    if ($count > 0) {
        file_put_contents($filePath, $content);
        echo "  ✓ Added {$count} UUIDs to {$seeder}\n";
    } else {
        echo "  - No changes needed for {$seeder}\n";
    }
}

echo "\n✓ All seeders processed!\n";
