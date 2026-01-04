<?php

// Bootstrap CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);

require 'vendor/autoload.php';

// Load environment
$app = require_once FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();

// Get database instance
$db = \Config\Database::connect();

// Check quiz attempts
echo "=== RECENT QUIZ ATTEMPTS ===\n";
$attempts = $db->table('quiz_attempts')
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get()
    ->getResultArray();

echo json_encode($attempts, JSON_PRETTY_PRINT) . "\n\n";

if (!empty($attempts)) {
    $attemptId = $attempts[0]['id'];
    
    echo "=== ANSWERS FOR ATTEMPT: $attemptId ===\n";
    $answers = $db->table('quiz_answers')
        ->where('attempt_id', $attemptId)
        ->get()
        ->getResultArray();
    
    echo json_encode($answers, JSON_PRETTY_PRINT) . "\n\n";
    
    // Get quiz details
    $quizId = $attempts[0]['quiz_id'];
    echo "=== QUIZ DETAILS: $quizId ===\n";
    $quiz = $db->table('quizzes')
        ->where('id', $quizId)
        ->get()
        ->getRowArray();
    
    echo json_encode($quiz, JSON_PRETTY_PRINT) . "\n";
}
