<?php

// Fix all remaining seeders that use model() calls

$seeders = [
    'EventRegistrationSeeder.php',
    'EventTicketsSeeder.php',
    'CourseCompletionSeeder.php',
    'CourseLecturesSeeder.php',
    'LectureCompletionSeeder.php',
    'BlogNewsletterSubscriptionsSeeder.php',
    'BlogPostViewsSeeder.php',
    'UserBookmarkSeeder.php',
    'ContributorSeeder.php',
    'ResourceContributorSeeder.php',
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
    $modified = false;
    
    // Replace model() calls with database builder
    if (preg_match('/\$\w+\s*=\s*model\([\'"](\w+)[\'"]\);?\s*\n\s*\$\w+->insert/s', $content)) {
        // Add database connection at the start of run method
        $content = preg_replace(
            '/(public function run\(\)\s*\{)/',
            "$1\n      \$db = \\Config\\Database::connect();",
            $content,
            1
        );
        
        // Replace model()->insert() with $db->table()->insert()
        $content = preg_replace(
            '/\$(\w+)\s*=\s*model\([\'"](\w+)[\'"]\);?\s*\n\s*\$\1->insert\(/s',
            '$db->table(\'TABLENAME\')->insert(',
            $content
        );
        
        $modified = true;
    }
    
    if ($modified) {
        file_put_contents($filePath, $content);
        echo "  ✓ Fixed {$seeder}\n";
    } else {
        echo "  - No changes needed for {$seeder}\n";
    }
}

echo "\n✓ All seeders processed!\n";
