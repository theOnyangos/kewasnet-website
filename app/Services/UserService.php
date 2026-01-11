<?php

namespace App\Services;

use Carbon\Carbon;
use App\Libraries\GenerateIDs;
use App\Services\AuthService;

class UserService
{
    protected $userModel;
    protected $authService;
    protected $generateIDs;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->generateIDs = new GenerateIDs();
        $this->userModel = model('App\Models\UserModel');
    }

    /**
     * Create a new system user
     * 
     * @param array $postData
     * @return array
     */
    public function createSystemUser(array $postData): array
    {
        // Prepare user data
        $userData = $this->prepareUserData($postData);

        // Create user in database
        $userId = $this->userModel->createUserInternal($userData);

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Failed to create user',
                'statusCode' => 500
            ];
        }

        // Prepare and send verification email
        $emailResult = $this->sendVerificationEmail($userData, $userId);

        // Log email errors but don't fail user creation
        if (!$emailResult['success']) {
            log_message('error', 'Failed to send verification email to ' . $userData['email'] . ': ' . $emailResult['message']);
        }

        // Notify admins about new user registration
        try {
            $adminUsers = $this->userModel->getAdministrators();
            $userName = ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? '');
            
            if (!empty($adminUsers)) {
                $notificationService = new \App\Services\NotificationService();
                $adminIds = array_column($adminUsers, 'id');
                $notificationService->notifyNewUserRegistration($adminIds, trim($userName), $userId);
            }
        } catch (\Exception $notificationError) {
            log_message('error', "Error sending admin notification for new user registration: " . $notificationError->getMessage());
            // Don't fail user creation if notification fails
        }

        return [
            'success' => true,
            'message' => $emailResult['success']
                ? 'Registration successful. Please check your email for a verification link'
                : 'User created successfully. Verification email will be sent separately.',
            'data' => [
                'userId' => $userId
            ]
        ];
    }

    /**
     * Prepare user data for creation
     * 
     * @param array $postData
     * @return array
     */
    protected function prepareUserData(array $postData): array
    {
        $password = $this->generatePassword();
        $employeeNumber = $this->generateIDs->generateEmployeeNumber();

        return [
            'full_name' => $postData['first_name'] . ' ' . $postData['last_name'],
            'first_name' => $postData['first_name'],
            'last_name' => $postData['last_name'],
            'email' => $postData['email'],
            'phone' => $postData['phone'] ?? null,
            'role' => $postData['role_id'],
            'bio' => $postData['bio'] ?? null,
            'password' => $password,
            'employee_number' => $employeeNumber
        ];
    }

    /**
     * Send verification email to new user
     * 
     * @param array $userData
     * @param mixed $userId
     * @return array
     */
    protected function sendVerificationEmail(array $userData, $userId): array
    {
        $emailData = [
            'full_name' => $userData['full_name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'join_date' => Carbon::now()->toDateTimeString(),
            'verification_url' => base_url('ksp/verify_account?code=' . $userId)
        ];

        $result = $this->authService->sendEmailToUser($emailData);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'An error occurred while sending you an email. Please try again'
            ];
        }

        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
    }

    /**
     * Generate a random password
     *
     * @return string
     */
    protected function generatePassword(): string
    {
        return bin2hex(random_bytes(8));
    }

    /**
     * Delete a system user and all related data
     *
     * @param string $userId
     * @return array
     */
    public function deleteSystemUser(string $userId): array
    {
        // Get user details before deletion
        $user = $this->userModel->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'statusCode' => 404
            ];
        }

        // Prevent deletion of currently logged-in user
        if ($userId === session()->get('id')) {
            return [
                'success' => false,
                'message' => 'You cannot delete your own account',
                'statusCode' => 403
            ];
        }

        // Begin transaction for safe deletion
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Delete user's related data
            $this->deleteUserRelatedData($userId);

            // Soft delete the user
            $deleted = $this->userModel->delete($userId);

            if (!$deleted) {
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Failed to delete user',
                    'statusCode' => 500
                ];
            }

            // Commit transaction
            $db->transCommit();

            // Send notification email to user (after successful deletion)
            $this->sendAccountDeletionEmail($user);

            // Send notification to admins about account deletion
            try {
                $userName = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
                $userEmail = $user['email'] ?? '';
                $deletedAt = date('Y-m-d H:i:s');

                $adminUsers = $this->userModel->getAdministrators();
                if (!empty($adminUsers)) {
                    $notificationService = new \App\Services\NotificationService();
                    $adminIds = array_column($adminUsers, 'id');
                    $notificationService->notifyUserAccountDeletion($adminIds, trim($userName), $userEmail, $deletedAt);
                }
            } catch (\Exception $notificationError) {
                log_message('error', "Error sending account deletion notification: " . $notificationError->getMessage());
                // Don't fail deletion if notification fails
            }

            return [
                'success' => true,
                'message' => 'User account deleted successfully',
                'data' => [
                    'deletedUserId' => $userId,
                    'deletedUserEmail' => $user['email']
                ]
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error deleting user: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred while deleting the user account: ' . $e->getMessage(),
                'statusCode' => 500
            ];
        }
    }

    /**
     * Delete all user-related data from various tables
     *
     * @param string $userId
     * @return void
     */
    protected function deleteUserRelatedData(string $userId): void
    {
        $db = \Config\Database::connect();

        // Delete from course enrollments
        if ($db->tableExists('course_enrollments')) {
            $db->table('course_enrollments')->where('user_id', $userId)->delete();
        }

        // Delete from course completions
        if ($db->tableExists('course_completions')) {
            $db->table('course_completions')->where('user_id', $userId)->delete();
        }

        // Delete from user progress
        if ($db->tableExists('user_progress')) {
            $db->table('user_progress')->where('user_id', $userId)->delete();
        }

        // Delete from quiz attempts
        if ($db->tableExists('quiz_attempts')) {
            $db->table('quiz_attempts')->where('user_id', $userId)->delete();
        }

        // Delete from certificates
        if ($db->tableExists('certificates')) {
            $db->table('certificates')->where('user_id', $userId)->delete();
        }

        // Delete from orders
        if ($db->tableExists('orders')) {
            $db->table('orders')->where('user_id', $userId)->delete();
        }

        // Delete from forum discussions (soft delete or mark as deleted)
        if ($db->tableExists('discussions')) {
            $db->table('discussions')->where('user_id', $userId)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
        }

        // Delete from discussion replies
        if ($db->tableExists('replies')) {
            $db->table('replies')->where('user_id', $userId)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
        }

        // Delete from bookmarks
        if ($db->tableExists('bookmarks')) {
            $db->table('bookmarks')->where('user_id', $userId)->delete();
        }

        // Delete from likes
        if ($db->tableExists('likes')) {
            $db->table('likes')->where('user_id', $userId)->delete();
        }

        // Delete from blog comments
        if ($db->tableExists('blog_comments')) {
            $db->table('blog_comments')->where('user_id', $userId)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
        }

        // Delete from resource comments
        if ($db->tableExists('resource_comments')) {
            $db->table('resource_comments')->where('user_id', $userId)->delete();
        }

        // Delete from notifications
        if ($db->tableExists('notifications')) {
            $db->table('notifications')->where('user_id', $userId)->delete();
        }

        // Delete from user sessions/activity
        if ($db->tableExists('user_sessions')) {
            $db->table('user_sessions')->where('user_id', $userId)->delete();
        }

        // Delete from course questions
        if ($db->tableExists('course_questions')) {
            $db->table('course_questions')->where('user_id', $userId)->delete();
        }

        // Delete from course question replies
        if ($db->tableExists('course_question_replies')) {
            $db->table('course_question_replies')->where('user_id', $userId)->delete();
        }

        // Delete from event registrations
        if ($db->tableExists('event_registrations')) {
            $db->table('event_registrations')->where('user_id', $userId)->delete();
        }

        // Delete from job applications
        if ($db->tableExists('job_applicants')) {
            $db->table('job_applicants')->where('user_id', $userId)->delete();
        }

        // Log the deletion activity
        log_message('info', "Deleted all related data for user ID: {$userId}");
    }

    /**
     * Send account deletion notification email to user
     *
     * @param array $user
     * @return void
     */
    protected function sendAccountDeletionEmail(array $user): void
    {
        try {
            $message = view('backend/emails/account_deletion_template', [
                'firstName' => $user['first_name'],
                'deletionDate' => Carbon::now()->format('F j, Y \a\t g:i A')
            ]);

            $emailQueueModel = new \App\Models\EmailQueue();
            $emailQueueModel->queueEmail(
                $user['email'],
                'Your Account Has Been Deleted',
                $message,
                null,
                'noreply@kewasnet.org',
                'KEWASNET'
            );
        } catch (\Exception $e) {
            log_message('error', 'Failed to queue account deletion email: ' . $e->getMessage());
        }
    }
}