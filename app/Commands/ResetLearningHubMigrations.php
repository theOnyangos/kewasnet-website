<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetLearningHubMigrations extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'migrate:reset-learning-hub';
    protected $description = 'Resets learning hub migration records to allow recreation with UUIDs';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Migration versions to reset
        $migrations = [
            '2024-01-30-092906', // CreateCoursesTable
            '2024-01-30-094407', // CreateCourseCategoryTable
            '2024-01-30-094507', // CreateCourseSubCategoryTable
            '2024-01-30-095053', // CreateCourseLecturesTable
            '2024-01-30-121143', // CreateCourseCompleteTable
            '2024-01-30-121201', // CreateCourseSubscriptionTable
            '2024-01-30-121218', // CreateCoursePurchaseTable
            '2024-01-30-121227', // CreateCourseChatTable
            '2024-01-31-143005', // CreateLectureCompletionTable
            '2024-02-07-105544', // CreateCourseWishlistTable
            '2024-02-07-110830', // CreateCourseReviewsTable
            '2024-02-19-053519', // CreateQuizzesTable
            '2024-02-19-053545', // CreateQuestionsTable
            '2024-02-19-053607', // CreateAnswersTable
            '2024-02-19-053640', // CreateUserQuizzesTable
            '2024-02-19-054123', // CreateCourseSectionsTable
            '2024-02-19-060127', // CreateUserSelectedAnswersTable
            '2024-03-07-183220', // CreateCourseGoalsTable
            '2024-03-07-183739', // CreateCourseRequirementsTable
            '2024-03-10-041459', // CreateCourseAnnouncementsTable
            '2024-03-12-141500', // CreateCourseCartTable
            '2024-03-20-081542', // CreateCourseQuestionsTable
            '2024-03-20-084335', // CreateCourseQuestionRepliesTable
            '2024-03-20-091512', // CreateCourseQuestionReplyLikesTable
            '2024-03-21-105340', // CreateCourseLectureProgressTable
            '2025-12-24-111756', // CreateCourseCertificatesTable
            '2025-12-24-111801', // CreateQuizQuestionsTable
            '2025-12-24-111805', // CreateQuizQuestionOptionsTable
            '2025-12-24-111809', // CreateQuizAttemptsTable
            '2025-12-24-111814', // CreateQuizAnswersTable
            '2025-12-24-111819', // CreateLectureAttachmentsTable
            '2025-12-24-111827', // CreateLectureLinksTable
            '2025-12-24-111834', // CreateCourseInstructorsTable
            '2025-12-24-111840', // CreateVimeoVideosTable
            '2025-12-24-111845', // CreateUserProgressTable
            '2025-12-24-111848', // EnhanceCourseLecturesTable
            '2025-12-24-111852', // EnhanceCourseSectionsTable
            '2025-12-24-111855', // EnhanceCoursesTable
            '2025-12-27-000000', // ConvertCoursesToUuid
            '2025-12-27-100000', // RecreateCoursesTable
        ];

        $deletedCount = 0;
        foreach ($migrations as $version) {
            $result = $db->table('migrations')
                ->where('version', $version)
                ->delete();
            
            if ($result) {
                CLI::write("Deleted migration record: $version", 'green');
                $deletedCount++;
            }
        }

        CLI::newLine();
        CLI::write("Successfully deleted $deletedCount migration records!", 'green');
        CLI::write("You can now run 'php spark migrate' to recreate tables with UUIDs", 'yellow');
    }
}
