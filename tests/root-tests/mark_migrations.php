<?php
$db = new mysqli('localhost', 'root', '', 'kewasnet');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get next batch number
$result = $db->query('SELECT MAX(batch) as max_batch FROM migrations');
$batch = $result->fetch_assoc()['max_batch'] + 1;

$migrations = [
    ['2025-12-24-111805', 'App\\Database\\Migrations\\CreateQuizQuestionOptionsTable'],
    ['2025-12-24-111809', 'App\\Database\\Migrations\\CreateQuizAttemptsTable'],
    ['2025-12-24-111814', 'App\\Database\\Migrations\\CreateQuizAnswersTable'],
    ['2025-12-24-111819', 'App\\Database\\Migrations\\CreateLectureAttachmentsTable'],
    ['2025-12-24-111827', 'App\\Database\\Migrations\\CreateLectureLinksTable'],
    ['2025-12-24-111834', 'App\\Database\\Migrations\\CreateCourseInstructorsTable'],
];

foreach ($migrations as $migration) {
    $sql = sprintf(
        "INSERT IGNORE INTO migrations (version, class, `group`, namespace, time, batch) VALUES ('%s', '%s', 'default', 'App', %d, %d)",
        $migration[0],
        $migration[1],
        time(),
        $batch
    );
    $db->query($sql);
}

echo "Migrations marked as completed\n";
$db->close();
