<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Services\NotificationService;
use App\Libraries\ClientAuth;

class NotificationController extends BaseController
{
    protected $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    /**
     * Display notifications page
     */
    public function index()
    {
        if (!ClientAuth::isLoggedIn()) {
            return redirect()->to('ksp/login');
        }

        $data = [
            'title' => 'Notifications | KEWASNET',
            'dashboardTitle' => 'My Notifications',
        ];

        return view('frontendV2/ksp/pages/notifications/index', $data);
    }

    /**
     * Get notifications for DataTables (AJAX)
     */
    public function getNotifications()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        if (!ClientAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        $userId = ClientAuth::getId();

        // Get request parameters
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $searchValue = $this->request->getPost('search')['value'] ?? '';
        $filterType = $this->request->getPost('filterType') ?? 'all';

        // Get notifications
        $notifications = $this->notificationService->getAll($userId, $length, $start);
        $totalRecords = count($this->notificationService->getAll($userId, 1000)); // Get total count

        // Filter by type if needed
        if ($filterType !== 'all') {
            $notifications = array_filter($notifications, function($notification) use ($filterType) {
                if ($filterType === 'unread') {
                    return $notification['status'] === 'unread';
                } elseif ($filterType === 'read') {
                    return $notification['status'] === 'read';
                }
                return true;
            });
        }

        // Format notifications for display
        $data = array_map(function($notification) {
            return [
                'id' => $notification['id'],
                'type' => $notification['type'],
                'title' => $notification['title'] ?? 'Notification',
                'message' => $notification['message'],
                'icon' => $notification['icon'] ?? 'bell',
                'action_url' => $notification['action_url'],
                'action_text' => $notification['action_text'],
                'status' => $notification['status'],
                'created_at' => $notification['created_at'],
                'read_at' => $notification['read_at'] ?? null,
            ];
        }, $notifications);

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    /**
     * Get recent notifications for dropdown (AJAX)
     */
    public function getRecent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        if (!ClientAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        $userId = ClientAuth::getId();
        $limit = $this->request->getGet('limit') ?? 10;
        $notifications = $this->notificationService->getUnread($userId, $limit);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread count (AJAX)
     */
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        if (!ClientAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'success',
                'count' => 0,
            ]);
        }

        $userId = ClientAuth::getId();
        $count = $this->notificationService->getUnreadCount($userId);

        return $this->response->setJSON([
            'status' => 'success',
            'count' => $count,
        ]);
    }

    /**
     * Mark notification as read (AJAX)
     */
    public function markAsRead($notificationId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        if (!ClientAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        $userId = ClientAuth::getId();

        if (!$notificationId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Notification ID is required',
            ]);
        }

        $result = $this->notificationService->markAsRead($notificationId, $userId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Notification marked as read',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to mark notification as read',
        ]);
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function markAllAsRead()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        if (!ClientAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        $userId = ClientAuth::getId();
        $result = $this->notificationService->markAllAsRead($userId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'All notifications marked as read',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to mark all notifications as read',
        ]);
    }

    /**
     * Delete notification (AJAX)
     */
    public function delete($notificationId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        if (!ClientAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        $userId = ClientAuth::getId();

        if (!$notificationId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Notification ID is required',
            ]);
        }

        $result = $this->notificationService->delete($notificationId, $userId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Notification deleted successfully',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete notification',
        ]);
    }

    /**
     * Clear all read notifications (AJAX)
     */
    public function clearAll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        if (!ClientAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        $userId = ClientAuth::getId();
        $result = $this->notificationService->clearAllRead($userId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'All read notifications cleared',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to clear notifications',
        ]);
    }
}
