<?php

require 'vendor/autoload.php';

// Bootstrap CodeIgniter
$app = require_once FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();

// Bootstrap the application
require FCPATH . '../system/bootstrap.php';

// Get database connection
$db = \Config\Database::connect();

// Query discussions
echo "Recent Discussions:\n";
echo str_repeat('=', 80) . "\n";

$discussions = $db->query('SELECT id, title, slug, view_count FROM discussions ORDER BY created_at DESC LIMIT 5')->getResult();

foreach ($discussions as $disc) {
    echo "Title: {$disc->title}\n";
    echo "Slug: {$disc->slug}\n";
    echo "View Count: {$disc->view_count}\n";
    echo "ID: {$disc->id}\n";
    echo str_repeat('-', 80) . "\n";
}

// Query discussion_views
echo "\nDiscussion Views Table:\n";
echo str_repeat('=', 80) . "\n";

$views = $db->query('SELECT * FROM discussion_views ORDER BY viewed_at DESC LIMIT 5')->getResult();

if (empty($views)) {
    echo "No views recorded yet.\n";
} else {
    foreach ($views as $view) {
        echo "Discussion ID: {$view->discussion_id}\n";
        echo "User ID: " . ($view->user_id ?? 'Guest') . "\n";
        echo "IP Address: {$view->ip_address}\n";
        echo "Viewed At: {$view->viewed_at}\n";
        echo str_repeat('-', 80) . "\n";
    }
}
