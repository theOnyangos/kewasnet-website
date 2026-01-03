<?php
require_once __DIR__ . '/vendor/autoload.php';

$db = \Config\Database::connect();

$migrations = [
    ['2025-12-24-111805', 'CreateQuizQuestionOptionsTable'],
    ['2025-12-24-111809', 'CreateQuizAttemptsTable'],
    ['2025-12-24-111814', 'CreateQuizAnswersTable'],
    ['2025-12-24-111819', 'CreateLectureAttachmentsTable'],
    ['2025-12-24-111827', 'CreateLectureLinksTable'],
    ['2025-12-24-111834', 'CreateCourseInstructorsTable'],
    ['2025-12-27-000000', 'ConvertCoursesToUuid'],
];

foreach ($migrations as [$version, $class]) {
    $existing = $db->table('migrations')->where('version', $version)->get()->getFirstRow();
    if (!isset($existing)) {
        $db->table('migrations')->insert([
            'version' => $version,
            'class' => 'App',
            'group' => 'default',
            'namespace' => 'App\\Database\\Migrations',
            'time' => time(),
            'batch' => 79
        ]);
        echo "Marked $version as migrated\n";
    } else {
        echo "$version already marked\n";
    }
}
