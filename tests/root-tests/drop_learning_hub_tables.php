<?php

// Bootstrap CodeIgniter
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
require ROOTPATH . 'vendor/autoload.php';

// Boot the system
$paths = new Config\Paths();
$bootstrap = new CodeIgniter\Boot($paths);
$bootstrap->bootEnv(ROOTPATH);
Services::autoloader()->initialize(new Config\Autoload(), new Config\Modules());

$db = \Config\Database::connect();

// List of all learning hub related tables
$tables = [
    'course_question_reply_likes',
    'course_question_replies',
    'course_questions',
    'course_lecture_progress',
    'quiz_answers',
    'quiz_attempts',
    'quiz_question_options',
    'quiz_questions',
    'lecture_attachments',
    'lecture_links',
    'lecture_completions',
    'course_lectures',
    'course_instructors',
    'course_sections',
    'course_certificates',
    'course_requirements',
    'course_goals',
    'course_announcements',
    'course_reviews',
    'course_wishlists',
    'course_carts',
    'course_purchases',
    'course_subscriptions',
    'course_completions',
    'course_chats',
    'user_progress',
    'vimeo_videos',
    'course_sub_categories',
    'course_categories',
    // 'courses', // Drop this last
];

echo "Disabling foreign key checks...\n";
$db->query('SET FOREIGN_KEY_CHECKS = 0');

foreach ($tables as $table) {
    if ($db->tableExists($table)) {
        echo "Dropping table: $table\n";
        $db->query("DROP TABLE IF EXISTS `$table`");
    } else {
        echo "Table does not exist: $table\n";
    }
}

// Drop courses table last
if ($db->tableExists('courses')) {
    echo "Dropping table: courses\n";
    $db->query("DROP TABLE IF EXISTS `courses`");
}

echo "Enabling foreign key checks...\n";
$db->query('SET FOREIGN_KEY_CHECKS = 1');

echo "\nAll learning hub tables dropped successfully!\n";
