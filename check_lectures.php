<?php

// Bootstrap CodeIgniter
$pathsConfig = require 'app/Config/Paths.php';
$paths = new \Config\Paths();
$bootstrap = require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
$app = \CodeIgniter\Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== Course Lectures ===\n\n";
$lectures = $db->table('course_lectures')
    ->select('id, section_id, title, deleted_at')
    ->get()
    ->getResultArray();

foreach($lectures as $lecture) {
    echo "ID: " . $lecture['id'] . "\n";
    echo "Section ID: " . ($lecture['section_id'] ?: 'NULL') . "\n";
    echo "Title: " . $lecture['title'] . "\n";
    echo "Deleted: " . ($lecture['deleted_at'] ?: 'NO') . "\n";
    echo "---\n";
}

echo "\n=== Course Sections ===\n\n";
$sections = $db->table('course_sections')
    ->select('id, title, course_id')
    ->get()
    ->getResultArray();

foreach($sections as $section) {
    echo "ID: " . $section['id'] . "\n";
    echo "Title: " . $section['title'] . "\n";
    echo "Course ID: " . $section['course_id'] . "\n";
    echo "---\n";
}
