<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyLearningHubData extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'learninghub:verify';
    protected $description = 'Verify Learning Hub seeded data';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Learning Hub Data Verification', 'green');
        CLI::newLine();
        
        $tables = [
            'quiz_questions' => 'Quiz Questions',
            'quiz_question_options' => 'Quiz Question Options',
            'quiz_attempts' => 'Quiz Attempts',
            'quiz_answers' => 'Quiz Answers',
            'lecture_attachments' => 'Lecture Attachments',
            'lecture_links' => 'Lecture Links',
            'course_instructors' => 'Course Instructors',
            'vimeo_videos' => 'Vimeo Videos',
            'user_progress' => 'User Progress',
            'course_certificates' => 'Course Certificates',
        ];
        
        foreach ($tables as $table => $label) {
            if ($db->tableExists($table)) {
                $count = $db->table($table)->countAllResults();
                CLI::write(sprintf('  %s: %d records', $label, $count), $count > 0 ? 'green' : 'yellow');
            } else {
                CLI::write(sprintf('  %s: Table does not exist', $label), 'red');
            }
        }
        
        CLI::newLine();
        CLI::write('Verification complete!', 'green');
    }
}

