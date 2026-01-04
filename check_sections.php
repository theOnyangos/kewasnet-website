<?php

// Bootstrap CodeIgniter
$pathsConfig = require 'app/Config/Paths.php';
$bootstrap = require rtrim($pathsConfig->systemDirectory, '\\/ ') . '/bootstrap.php';
$app = \CodeIgniter\Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();
$sections = $db->table('course_sections')
    ->where('course_id', '61d2846d-d10f-492d-9c76-50a48e537277')
    ->orderBy('created_at', 'ASC')
    ->get()
    ->getResultArray();

echo "Found " . count($sections) . " sections:\n\n";
foreach($sections as $s) {
    echo "ID: " . ($s['id'] ?: 'EMPTY') . " | Title: " . $s['title'] . "\n";
}

// Fix any empty IDs
foreach($sections as $section) {
    if (empty($section['id'])) {
        $newId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        echo "\n\nFixing section '" . $section['title'] . "' with new ID: " . $newId . "\n";
        
        // Update the section with a new UUID
        $db->table('course_sections')
            ->where('title', $section['title'])
            ->where('course_id', $section['course_id'])
            ->where('created_at', $section['created_at'])
            ->update(['id' => $newId]);
        
        echo "Section updated successfully!\n";
    }
}
