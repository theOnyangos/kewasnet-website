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
        if (!$notification) {
            log_message('error', "Notification not found: ID={$notificationId}");
            return false;
        }

        // Convert user_id to int for comparison
        $notificationUserId = (int) $notification['user_id'];
        $userId = (int) $userId;

        if ($notificationUserId !== $userId) {
            log_message('error', "Notification user mismatch: Notification user_id={$notificationUserId}, Current user_id={$userId}");
            return false;
        }

        $result = $this->notificationModel->markAsRead($notificationId);
        if (!$result) {
            $errors = $this->notificationModel->errors();
            log_message('error', "Failed to mark notification as read: ID={$notificationId}, Errors: " . json_encode($errors));
        }

        return $result;
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
     * Notify admins about new forum report
     */
    public function notifyForumReport(int|string $userId, string $reporterName, string $reportedUserName, string $reason, string $reportId)
    {
        return $this->create($userId, "{$reporterName} reported {$reportedUserName} for: {$reason}", [
            'type' => 'warning',
            'title' => 'New Forum Report',
            'icon' => 'flag',
            'action_url' => base_url("auth/forums/reports"),
            'action_text' => 'Review Report',
            'reference_id' => $reportId,
            'reference_type' => 'forum_report',
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

    // ==================== Frontend User Notification Helpers ====================

    /**
     * Welcome notification for new user signup
     */
    public function notifyUserWelcome(int|string $userId, string $userName)
    {
        return $this->create($userId, "Welcome to KEWASNET! Your account has been created. Start exploring our Knowledge Sharing Platform.", [
            'type' => 'success',
            'title' => 'Welcome to KEWASNET!',
            'icon' => 'user-plus',
            'action_url' => base_url('ksp/dashboard'),
            'action_text' => 'Go to Dashboard',
            'reference_id' => $userId,
            'reference_type' => 'user',
        ]);
    }

    /**
     * Notify user about forum join
     */
    public function notifyForumJoin(int|string $userId, string $forumName, string $forumId)
    {
        return $this->create($userId, "You successfully joined '{$forumName}'. Welcome to the community!", [
            'type' => 'success',
            'title' => 'Forum Joined',
            'icon' => 'users',
            'action_url' => base_url("ksp/networking-corner-forum-discussion/{$forumId}"),
            'action_text' => 'Visit Forum',
            'reference_id' => $forumId,
            'reference_type' => 'forum',
        ]);
    }

    /**
     * Notify user about forum leave
     */
    public function notifyForumLeave(int|string $userId, string $forumName)
    {
        return $this->create($userId, "You left '{$forumName}'. You can rejoin anytime.", [
            'type' => 'info',
            'title' => 'Forum Left',
            'icon' => 'user-minus',
            'action_url' => base_url('ksp/networking-corner'),
            'action_text' => 'Browse Forums',
            'reference_type' => 'forum',
        ]);
    }

    /**
     * Notify forum moderators about new member
     */
    public function notifyForumNewMember(array $moderatorIds, string $userName, string $forumName, string $forumId)
    {
        return $this->createForMultipleUsers($moderatorIds, "{$userName} joined forum '{$forumName}'", [
            'type' => 'info',
            'title' => 'New Forum Member',
            'icon' => 'user-plus',
            'action_url' => base_url("auth/forums/view/{$forumId}"),
            'action_text' => 'View Forum',
            'reference_id' => $forumId,
            'reference_type' => 'forum',
        ]);
    }

    /**
     * Notify discussion author when someone likes their discussion
     */
    public function notifyDiscussionLiked(int|string $userId, string $discussionTitle, string $likerName, string $discussionId, string $forumName = '')
    {
        $message = !empty($forumName) 
            ? "{$likerName} liked your discussion '{$discussionTitle}' in '{$forumName}'."
            : "{$likerName} liked your discussion '{$discussionTitle}'.";
            
        return $this->create($userId, $message, [
            'type' => 'info',
            'title' => 'Discussion Liked',
            'icon' => 'heart',
            'action_url' => base_url("ksp/discussion/{$discussionId}/view"),
            'action_text' => 'View Discussion',
            'reference_id' => $discussionId,
            'reference_type' => 'discussion',
        ]);
    }

    /**
     * Notify discussion author when someone replies to their discussion
     */
    public function notifyDiscussionReply(int|string $userId, string $discussionTitle, string $replierName, string $discussionId, string $replyPreview = '')
    {
        $message = !empty($replyPreview)
            ? "{$replierName} replied to your discussion '{$discussionTitle}': '{$replyPreview}'"
            : "{$replierName} replied to your discussion '{$discussionTitle}'";
            
        return $this->create($userId, $message, [
            'type' => 'info',
            'title' => 'New Reply',
            'icon' => 'message-circle',
            'action_url' => base_url("ksp/discussion/{$discussionId}/view"),
            'action_text' => 'View Reply',
            'reference_id' => $discussionId,
            'reference_type' => 'discussion',
        ]);
    }

    /**
     * Notify parent reply author when someone replies to their comment
     */
    public function notifyReplyReply(int|string $userId, string $replierName, string $discussionId)
    {
        return $this->create($userId, "{$replierName} replied to your comment", [
            'type' => 'info',
            'title' => 'New Reply',
            'icon' => 'reply',
            'action_url' => base_url("ksp/discussion/{$discussionId}/view"),
            'action_text' => 'View Reply',
            'reference_id' => $discussionId,
            'reference_type' => 'discussion',
        ]);
    }

    /**
     * Notify user about course enrollment
     */
    public function notifyUserCourseEnrollment(int|string $userId, string $courseName, string $courseId)
    {
        return $this->create($userId, "You enrolled in '{$courseName}'. Start learning now!", [
            'type' => 'success',
            'title' => 'Course Enrolled',
            'icon' => 'graduation-cap',
            'action_url' => base_url("ksp/learning-hub/learn/{$courseId}"),
            'action_text' => 'Start Learning',
            'reference_id' => $courseId,
            'reference_type' => 'course',
        ]);
    }

    /**
     * Notify user about payment success and course enrollment
     */
    public function notifyPaymentSuccess(int|string $userId, string $amount, string $courseName, string $courseId)
    {
        return $this->create($userId, "Payment of {$amount} processed successfully. You now have access to '{$courseName}'.", [
            'type' => 'success',
            'title' => 'Payment Successful',
            'icon' => 'check-circle',
            'action_url' => base_url("ksp/learning-hub/learn/{$courseId}"),
            'action_text' => 'Access Course',
            'reference_id' => $courseId,
            'reference_type' => 'course',
        ]);
    }

    /**
     * Notify admins about new payment received
     */
    public function notifyAdminPaymentReceived(array $adminIds, string $amount, string $courseName, string $userName, string $orderId)
    {
        return $this->createForMultipleUsers($adminIds, "New payment received: {$amount} for '{$courseName}' from {$userName}", [
            'type' => 'success',
            'title' => 'New Payment',
            'icon' => 'dollar-sign',
            'action_url' => base_url("auth/courses/orders/{$orderId}"),
            'action_text' => 'View Order',
            'reference_id' => $orderId,
            'reference_type' => 'order',
        ]);
    }

    /**
     * Notify user about course completion
     */
    public function notifyUserCourseCompletion(int|string $userId, string $courseName, string $courseId)
    {
        return $this->create($userId, "Congratulations! You completed '{$courseName}'. Generate your certificate now.", [
            'type' => 'success',
            'title' => 'Course Completed',
            'icon' => 'award',
            'action_url' => base_url("ksp/learning-hub/certificate/{$courseId}"),
            'action_text' => 'Generate Certificate',
            'reference_id' => $courseId,
            'reference_type' => 'course',
        ]);
    }

    /**
     * Notify user about certificate generation
     */
    public function notifyCertificateGenerated(int|string $userId, string $courseName, string $certificateId)
    {
        return $this->create($userId, "Your certificate for '{$courseName}' is ready for download.", [
            'type' => 'success',
            'title' => 'Certificate Ready',
            'icon' => 'award',
            'action_url' => base_url("ksp/learning-hub/certificate/{$certificateId}"),
            'action_text' => 'View Certificate',
            'reference_id' => $certificateId,
            'reference_type' => 'certificate',
        ]);
    }

    /**
     * Notify user about event booking confirmation
     */
    public function notifyEventBooking(int|string $userId, string $eventTitle, string $bookingNumber, string $bookingId, string $paymentStatus = '')
    {
        $message = "Booking confirmed for '{$eventTitle}'. Booking ID: {$bookingNumber}";
        if (!empty($paymentStatus)) {
            $message .= ". Payment status: {$paymentStatus}";
        }
        
        return $this->create($userId, $message, [
            'type' => 'success',
            'title' => 'Booking Confirmed',
            'icon' => 'calendar-check',
            'action_url' => base_url("events/booking/{$bookingId}/tickets"),
            'action_text' => 'View Tickets',
            'reference_id' => $bookingId,
            'reference_type' => 'event_booking',
        ]);
    }

    /**
     * Notify admins about new event booking
     */
    public function notifyAdminEventBooking(array $adminIds, string $bookingNumber, string $eventTitle, string $userName, string $bookingId)
    {
        return $this->createForMultipleUsers($adminIds, "New booking: {$bookingNumber} for '{$eventTitle}' from {$userName}", [
            'type' => 'info',
            'title' => 'New Event Booking',
            'icon' => 'calendar-plus',
            'action_url' => base_url("auth/events/bookings/view/{$bookingId}"),
            'action_text' => 'View Booking',
            'reference_id' => $bookingId,
            'reference_type' => 'event_booking',
        ]);
    }

    /**
     * Notify user about event payment success
     */
    public function notifyEventPaymentSuccess(int|string $userId, string $eventTitle, string $amount, string $bookingId)
    {
        return $this->create($userId, "Payment successful! Tickets for '{$eventTitle}' have been sent to your email.", [
            'type' => 'success',
            'title' => 'Event Payment Successful',
            'icon' => 'check-circle',
            'action_url' => base_url("events/booking/{$bookingId}/tickets"),
            'action_text' => 'View Tickets',
            'reference_id' => $bookingId,
            'reference_type' => 'event_booking',
        ]);
    }

    /**
     * Notify admins about event payment received
     */
    public function notifyAdminEventPayment(array $adminIds, string $amount, string $eventTitle, string $bookingNumber, string $bookingId)
    {
        return $this->createForMultipleUsers($adminIds, "Payment received: {$amount} for event '{$eventTitle}' booking {$bookingNumber}", [
            'type' => 'success',
            'title' => 'Event Payment Received',
            'icon' => 'dollar-sign',
            'action_url' => base_url("auth/events/bookings/view/{$bookingId}"),
            'action_text' => 'View Booking',
            'reference_id' => $bookingId,
            'reference_type' => 'event_booking',
        ]);
    }

    /**
     * Notify user about moderator contact confirmation
     */
    public function notifyModeratorContactConfirmation(int|string $userId, string $forumName)
    {
        return $this->create($userId, "Your message to forum moderators has been sent. You will receive a response via email.", [
            'type' => 'success',
            'title' => 'Message Sent',
            'icon' => 'send',
            'action_url' => base_url("ksp/networking-corner"),
            'action_text' => 'Browse Forums',
            'reference_type' => 'forum',
        ]);
    }

    /**
     * Notify moderators when user contacts them
     */
    public function notifyModeratorsContact(array $moderatorIds, string $userName, string $forumName, string $forumId)
    {
        return $this->createForMultipleUsers($moderatorIds, "{$userName} contacted moderators about '{$forumName}'", [
            'type' => 'info',
            'title' => 'Moderator Contact',
            'icon' => 'mail',
            'action_url' => base_url("auth/forums/view/{$forumId}"),
            'action_text' => 'View Forum',
            'reference_id' => $forumId,
            'reference_type' => 'forum',
        ]);
    }

    /**
     * Notify resource author when someone comments on their article
     */
    public function notifyResourceComment(int|string $userId, string $articleTitle, string $commenterName, string $articleSlug)
    {
        return $this->create($userId, "{$commenterName} commented on your article '{$articleTitle}'", [
            'type' => 'info',
            'title' => 'New Comment',
            'icon' => 'message-circle',
            'action_url' => base_url("ksp/pillar-article/{$articleSlug}"),
            'action_text' => 'View Comment',
            'reference_id' => $articleSlug,
            'reference_type' => 'resource',
        ]);
    }

    /**
     * Notify parent comment author when someone replies to their comment
     */
    public function notifyResourceCommentReply(int|string $userId, string $replierName, string $articleSlug)
    {
        return $this->create($userId, "{$replierName} replied to your comment", [
            'type' => 'info',
            'title' => 'New Reply',
            'icon' => 'reply',
            'action_url' => base_url("ksp/pillar-article/{$articleSlug}"),
            'action_text' => 'View Reply',
            'reference_id' => $articleSlug,
            'reference_type' => 'resource',
        ]);
    }

    /**
     * Notify resource author when someone finds their article helpful
     */
    public function notifyResourceHelpful(int|string $userId, string $articleTitle, string $voterName, string $articleSlug)
    {
        return $this->create($userId, "{$voterName} found your article '{$articleTitle}' helpful", [
            'type' => 'success',
            'title' => 'Article Helpful',
            'icon' => 'thumbs-up',
            'action_url' => base_url("ksp/pillar-article/{$articleSlug}"),
            'action_text' => 'View Article',
            'reference_id' => $articleSlug,
            'reference_type' => 'resource',
        ]);
    }

    /**
     * Notify admins about user account deletion
     */
    public function notifyUserAccountDeletion(array $adminIds, string $userName, string $userEmail, string $deletedAt)
    {
        return $this->createForMultipleUsers($adminIds, "User account deleted: {$userName} ({$userEmail}) on {$deletedAt}", [
            'type' => 'warning',
            'title' => 'User Account Deleted',
            'icon' => 'user-x',
            'action_url' => base_url("auth/users"),
            'action_text' => 'View Users',
            'reference_type' => 'user',
        ]);
    }

    // ==================== Backend Admin Action Notification Helper ====================

    /**
     * Notify admin about CRUD action performed
     *
     * @param int|string $adminId Admin who performed the action
     * @param string $action Action type: 'created', 'updated', 'deleted'
     * @param string $entityType Entity type: 'blog', 'course', 'event', 'forum', 'resource', 'user', etc.
     * @param string $entityName Name/title of the entity
     * @param string $entityId ID of the entity
     * @param array $details Additional details (e.g., field changes, status updates)
     * @return bool|int
     */
    public function notifyAdminAction(int|string $adminId, string $action, string $entityType, string $entityName, string $entityId, array $details = [])
    {
        $actionMap = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
        ];

        $actionText = $actionMap[$action] ?? ucfirst($action);
        $entityTypeText = ucfirst(str_replace('_', ' ', $entityType));

        $adminName = session()->get('name') ?? 'Admin';
        $datetime = date('M j, Y g:i A');

        $message = "{$actionText} {$entityTypeText}: '{$entityName}' by {$adminName} on {$datetime}";

        // Add details if provided
        if (!empty($details)) {
            $detailMessages = [];
            foreach ($details as $key => $value) {
                $detailMessages[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
            }
            if (!empty($detailMessages)) {
                $message .= ' - ' . implode(', ', $detailMessages);
            }
        }

        $type = $action === 'deleted' ? 'warning' : ($action === 'created' ? 'success' : 'info');
        $icon = $action === 'deleted' ? 'trash-2' : ($action === 'created' ? 'plus-circle' : 'edit');

        // Determine action URL based on entity type
        $actionUrl = $this->getAdminActionUrl($entityType, $entityId, $action);

        return $this->create($adminId, $message, [
            'type' => $type,
            'title' => "{$actionText} {$entityTypeText}",
            'icon' => $icon,
            'action_url' => $actionUrl,
            'action_text' => $action === 'deleted' ? 'View List' : 'View Details',
            'reference_id' => $entityId,
            'reference_type' => $entityType,
        ]);
    }

    /**
     * Get admin action URL based on entity type
     */
    protected function getAdminActionUrl(string $entityType, string $entityId, string $action): ?string
    {
        if ($action === 'deleted') {
            // For deleted items, return list page
            $routes = [
                'blog' => 'auth/blogs',
                'course' => 'auth/courses',
                'event' => 'auth/events',
                'forum' => 'auth/forums',
                'resource' => 'auth/pillars/articles',
                'user' => 'auth/users',
                'partner' => 'auth/partners',
                'leadership' => 'auth/leadership',
                'opportunity' => 'auth/opportunities',
            ];
            return base_url($routes[$entityType] ?? 'auth/dashboard');
        }

        // For created/updated, return view/edit page
        $routes = [
            'blog' => "auth/blogs/edit/{$entityId}",
            'course' => "auth/courses/edit/{$entityId}",
            'event' => "auth/events/edit/{$entityId}",
            'forum' => "auth/forums/view/{$entityId}",
            'resource' => "auth/pillars/articles/edit/{$entityId}",
            'user' => "auth/account/view/{$entityId}",
            'partner' => "auth/partners/edit/{$entityId}",
            'leadership' => "auth/leadership/edit/{$entityId}",
            'opportunity' => "auth/opportunities/edit/{$entityId}",
        ];

        return base_url($routes[$entityType] ?? 'auth/dashboard');
    }
}
