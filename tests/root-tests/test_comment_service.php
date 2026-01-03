<?php

// Bootstrap CodeIgniter 4
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

require_once 'vendor/autoload.php';

// Test the ResourceCommentService to see if it loads without errors
try {
    
    // Initialize CodeIgniter app
    $app = \Config\Services::codeigniter();
    
    // Test ResourceCommentService
    $commentService = new \App\Services\ResourceCommentService();
    
    echo "ResourceCommentService loaded successfully!\n";
    
    // Test isUserLoggedIn method
    $isLoggedIn = $commentService->isUserLoggedIn();
    echo "User logged in: " . ($isLoggedIn ? 'Yes' : 'No') . "\n";
    
    // Test getCurrentUserId method
    $userId = $commentService->getCurrentUserId();
    echo "Current user ID: " . ($userId ? $userId : 'None') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
