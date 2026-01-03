<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetLearningHub extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'learning-hub:reset';
    protected $description = 'Reset Learning Hub data (clear all courses, sections, lectures, etc.)';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Starting Learning Hub data reset...', 'yellow');

        try {
            // Disable foreign key checks
            $db->query('SET FOREIGN_KEY_CHECKS=0');

            // Clear tables in correct order (child tables first)
            $tables = [
                'quiz_answers',
                'quiz_attempts',
                'quiz_question_options',
                'quiz_questions',
                'quizzes',
                'lecture_completions',
                'lecture_attachments',
                'lecture_links',
                'vimeo_videos',
                'user_progress',
                'course_lectures',
                'course_sections',
                'course_certificates',
                'course_completions',
                'course_purchases',
                'course_instructors',
                'courses'
            ];

            foreach ($tables as $table) {
                if ($db->tableExists($table)) {
                    $count = $db->table($table)->countAll();
                    $db->table($table)->truncate();
                    CLI::write("✓ Cleared {$count} records from {$table}", 'green');
                } else {
                    CLI::write("⚠ Table {$table} does not exist", 'yellow');
                }
            }

            // Re-enable foreign key checks
            $db->query('SET FOREIGN_KEY_CHECKS=1');

            CLI::newLine();
            CLI::write('Learning Hub data cleared successfully!', 'green');
            CLI::newLine();
            CLI::write('Now running seeder...', 'yellow');
            CLI::newLine();

            // Run the seeder
            $seeder = \Config\Database::seeder();
            $seeder->call('LearningHubCoursesSeeder');

            CLI::newLine();
            CLI::write('All done! Learning Hub has been reset with fresh data.', 'green');

        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            $db->query('SET FOREIGN_KEY_CHECKS=1');
            
            CLI::error('Error: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}
