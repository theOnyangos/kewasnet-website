<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DropLearningHubTables extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:drop-learning-hub';
    protected $description = 'Drops all learning hub related tables';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // List of all learning hub related tables (in order of dependencies)
        $tables = [
            'course_question_reply_likes',
            'course_question_replies',
            'course_questions',
            'course_lecture_progress',
            'quiz_answers',
            'quiz_attempts',
            'quiz_question_options',
            'quiz_questions',
            'user_selected_answers',
            'user_quizzes',
            'answers',
            'questions',
            'quizzes',
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
            'courses',
        ];

        CLI::write('Disabling foreign key checks...', 'yellow');
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        $droppedCount = 0;
        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                CLI::write("Dropping table: $table", 'green');
                $db->query("DROP TABLE IF EXISTS `$table`");
                $droppedCount++;
            } else {
                CLI::write("Table does not exist: $table", 'blue');
            }
        }

        CLI::write('Enabling foreign key checks...', 'yellow');
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        CLI::newLine();
        CLI::write("Successfully dropped $droppedCount learning hub tables!", 'green');
    }
}
