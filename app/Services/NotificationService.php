<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    /**
     * Create a new notification
     *
     * @param int|string $userId
     * @param string $message
     * @param array $options Additional options (type, title, icon, action_url, etc.)
     * @return bool|int
     */
    public function create(int|string $userId, string $message, array $options = [])
    {
        $data = [
            'user_id' => $userId,
            'message' => $message,
            'type' => $options['type'] ?? 'info',
            'title' => $options['title'] ?? null,
            'icon' => $options['icon'] ?? $this->getDefaultIcon($options['type'] ?? 'info'),
            'action_url' => $options['action_url'] ?? null,
            'action_text' => $options['action_text'] ?? null,
            'reference_id' => $options['reference_id'] ?? null,
            'reference_type' => $options['reference_type'] ?? null,
            'status' => 'unread',
        ];

        return $this->notificationModel->insert($data);
    }

    /**
     * Create notification for multiple users
     *
     * @param array $userIds
     * @param string $message
     * @param array $options
     * @return bool
     */
    public function createForMultipleUsers(array $userIds, string $message, array $options = []): bool
    {
        $success = true;
        foreach ($userIds as $userId) {
            if (!$this->create($userId, $message, $options)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Get unread notifications for a user
     *
     * @param int|string $userId
     * @param int $limit
     * @return array
     */
    public function getUnread(int|string $userId, int $limit = 10): array
    {
        return $this->notificationModel->getUnreadForUser($userId, $limit);
    }

    /**
     * Get all notifications for a user
     *
     * @param int|string $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll(int|string $userId, int $limit = 50, int $offset = 0): array
    {
        return $this->notificationModel->getForUser($userId, $limit, $offset);
    }

    /**
     * Get unread count for a user
     *
     * @param int|string $userId
     * @return int
     */
    public function getUnreadCount(int|string $userId): int
    {
        return $this->notificationModel->getUnreadCount($userId);
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @param int|string $userId (for security check)
     * @return bool
     */
    public function markAsRead(int $notificationId, int|string $userId): bool
    {
        // Verify the notification belongs to the user
        $notification = $this->notificationModel->find($notificationId);
        if (!$notification || $notification['user_id'] != $userId) {
            return false;
        }

        return $this->notificationModel->markAsRead($notificationId);
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int|string $userId
     * @return bool
     */
    public function markAllAsRead(int|string $userId): bool
    {
        return $this->notificationModel->markAllAsReadForUser($userId);
    }

    /**
     * Delete a notification
     *
     * @param int $notificationId
     * @param int|string $userId (for security check)
     * @return bool
     */
    public function delete(int $notificationId, int|string $userId): bool
    {
        // Verify the notification belongs to the user
        $notification = $this->notificationModel->find($notificationId);
        if (!$notification || $notification['user_id'] != $userId) {
            return false;
        }

        return $this->notificationModel->delete($notificationId);
    }

    /**
     * Delete all read notifications for a user
     *
     * @param int|string $userId
     * @return bool
     */
    public function clearAllRead(int|string $userId): bool
    {
        return $this->notificationModel->deleteReadForUser($userId);
    }

    /**
     * Get default icon based on notification type
     *
     * @param string $type
     * @return string
     */
    protected function getDefaultIcon(string $type): string
    {
        $icons = [
            'success' => 'check-circle',
            'warning' => 'alert-triangle',
            'error' => 'x-circle',
            'info' => 'info',
            'system' => 'bell',
        ];

        return $icons[$type] ?? 'bell';
    }

    // ==================== Notification Type Helpers ====================

    /**
     * Create a success notification
     */
    public function notifySuccess(int|string $userId, string $title, string $message, array $options = [])
    {
        $options['type'] = 'success';
        $options['title'] = $title;
        return $this->create($userId, $message, $options);
    }

    /**
     * Create a warning notification
     */
    public function notifyWarning(int|string $userId, string $title, string $message, array $options = [])
    {
        $options['type'] = 'warning';
        $options['title'] = $title;
        return $this->create($userId, $message, $options);
    }

    /**
     * Create an error notification
     */
    public function notifyError(int|string $userId, string $title, string $message, array $options = [])
    {
        $options['type'] = 'error';
        $options['title'] = $title;
        return $this->create($userId, $message, $options);
    }

    /**
     * Create an info notification
     */
    public function notifyInfo(int|string $userId, string $title, string $message, array $options = [])
    {
        $options['type'] = 'info';
        $options['title'] = $title;
        return $this->create($userId, $message, $options);
    }

    // ==================== Event-Specific Notification Helpers ====================

    /**
     * Notify about new job application
     */
    public function notifyNewJobApplication(int|string $userId, string $jobTitle, string $applicantName, string $jobId)
    {
        return $this->create($userId, "New application from {$applicantName}", [
            'type' => 'info',
            'title' => 'New Job Application',
            'icon' => 'briefcase',
            'action_url' => base_url("auth/opportunities/applications/{$jobId}"),
            'action_text' => 'View Application',
            'reference_id' => $jobId,
            'reference_type' => 'job_opportunity',
        ]);
    }

    /**
     * Notify about new course enrollment
     */
    public function notifyNewCourseEnrollment(int|string $userId, string $courseName, string $studentName, string $courseId)
    {
        return $this->create($userId, "{$studentName} enrolled in {$courseName}", [
            'type' => 'success',
            'title' => 'New Course Enrollment',
            'icon' => 'graduation-cap',
            'action_url' => base_url("auth/courses/view/{$courseId}"),
            'action_text' => 'View Course',
            'reference_id' => $courseId,
            'reference_type' => 'course',
        ]);
    }

    /**
     * Notify about course completion
     */
    public function notifyCourseCompletion(int|string $userId, string $courseName, string $courseId)
    {
        return $this->create($userId, "Congratulations! You completed {$courseName}", [
            'type' => 'success',
            'title' => 'Course Completed',
            'icon' => 'award',
            'action_url' => base_url("learning-hub/certificates"),
            'action_text' => 'View Certificate',
            'reference_id' => $courseId,
            'reference_type' => 'course',
        ]);
    }

    /**
     * Notify about new blog comment
     */
    public function notifyNewBlogComment(int|string $userId, string $blogTitle, string $commenterName, string $blogSlug)
    {
        return $this->create($userId, "{$commenterName} commented on your blog post", [
            'type' => 'info',
            'title' => 'New Comment',
            'icon' => 'message-circle',
            'action_url' => base_url("news/{$blogSlug}"),
            'action_text' => 'View Comment',
            'reference_id' => $blogSlug,
            'reference_type' => 'blog',
        ]);
    }

    /**
     * Notify about new forum reply
     */
    public function notifyNewForumReply(int|string $userId, string $discussionTitle, string $replierName, string $discussionId)
    {
        return $this->create($userId, "{$replierName} replied to your discussion", [
            'type' => 'info',
            'title' => 'New Reply',
            'icon' => 'messages-square',
            'action_url' => base_url("forums/discussion/{$discussionId}"),
            'action_text' => 'View Reply',
            'reference_id' => $discussionId,
            'reference_type' => 'forum',
        ]);
    }

    /**
     * Notify about new resource
     */
    public function notifyNewResource(int|string $userId, string $resourceTitle, string $categoryName, string $resourceId)
    {
        return $this->create($userId, "New resource added in {$categoryName}", [
            'type' => 'info',
            'title' => 'New Resource Available',
            'icon' => 'file-text',
            'action_url' => base_url("resources/{$resourceId}"),
            'action_text' => 'View Resource',
            'reference_id' => $resourceId,
            'reference_type' => 'resource',
        ]);
    }

    /**
     * Notify admins about new user registration
     */
    public function notifyNewUserRegistration(array $adminIds, string $userName, string $userId)
    {
        return $this->createForMultipleUsers($adminIds, "New user registered: {$userName}", [
            'type' => 'info',
            'title' => 'New User Registration',
            'icon' => 'user-plus',
            'action_url' => base_url("auth/users/view/{$userId}"),
            'action_text' => 'View User',
            'reference_id' => $userId,
            'reference_type' => 'user',
        ]);
    }

    /**
     * Notify about system update
     */
    public function notifySystemUpdate(int|string $userId, string $message, array $options = [])
    {
        $options['type'] = 'system';
        $options['title'] = $options['title'] ?? 'System Update';
        $options['icon'] = 'settings';
        return $this->create($userId, $message, $options);
    }
}
