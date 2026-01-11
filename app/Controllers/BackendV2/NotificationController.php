<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Services\NotificationService;

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
        $data = [
            'title' => 'Notifications | KEWASNET',
        ];

        return view('backendV2/pages/notifications/index', $data);
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

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

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

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        $limit = $this->request->getGet('limit') ?? 10;
        $notifications = $this->notificationService->getUnread($userId, $limit);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread count (AJAX/Polling)
     */
    public function getUnreadCount()
    {
        // Allow AJAX requests or requests with X-Requested-With header (for fetch API)
        $xRequestedWith = $this->request->getHeaderLine('X-Requested-With');
        if (!$this->request->isAJAX() && empty($xRequestedWith)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

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

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

        if (!$notificationId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Notification ID is required',
            ]);
        }

        // Convert notificationId to integer
        $notificationId = (int) $notificationId;
        $userId = (int) $userId;

        $result = $this->notificationService->markAsRead($notificationId, $userId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Notification marked as read',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to mark notification as read. Notification may not exist or may not belong to you.',
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

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

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

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

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

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated',
            ]);
        }

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

    /**
     * Server-Sent Events stream for real-time notifications
     * 
     * DISABLED: SSE has been replaced with simple polling for better performance.
     * This method is kept for potential future use.
     * 
     * To re-enable SSE, remove the early return below.
     */
    public function stream()
    {
        // SSE is disabled - using polling instead
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'SSE stream is disabled. Use polling instead.'
        ])->setStatusCode(503);

        // Disable all output buffering completely
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Disable time limit for long-running connection
        set_time_limit(0);
        
        // Disable compression for SSE
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);

        // Set SSE headers (must be sent before any output)
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable nginx buffering
        
        // Send retry directive for SSE (3 seconds)
        echo "retry: 3000\n\n";
        if (ob_get_level() == 0) {
            flush();
        }

        // Get user ID from session
        $userId = session()->get('id');
        if (!$userId) {
            echo "event: error\n";
            echo "data: " . json_encode(['error' => 'User not authenticated']) . "\n\n";
            if (ob_get_level() == 0) {
                flush();
            }
            exit;
        }

        // Track last count to detect changes
        $lastCount = $this->notificationService->getUnreadCount($userId);

        // Send initial connection message immediately
        echo "event: connected\n";
        echo "data: " . json_encode([
            'count' => $lastCount,
            'connected' => true
        ]) . "\n\n";
        if (ob_get_level() == 0) {
            flush();
        }

        // Keep connection alive and poll for changes
        $maxIterations = 3600; // 1 hour max (2 seconds * 3600 = 7200 seconds)
        $iteration = 0;
        $heartbeatInterval = 30; // Send heartbeat every 30 iterations (60 seconds)

        while ($iteration < $maxIterations) {
            // Check if connection is still alive
            if (connection_aborted()) {
                break;
            }

            // Validate session on each iteration
            $currentUserId = session()->get('id');
            if (!$currentUserId || $currentUserId != $userId) {
                echo "event: error\n";
                echo "data: " . json_encode(['error' => 'Session expired']) . "\n\n";
                if (ob_get_level() == 0) {
                    flush();
                }
                break;
            }

            // Get current unread count
            $currentCount = $this->notificationService->getUnreadCount($userId);

            // Check if count changed
            if ($currentCount != $lastCount) {
                // Get the latest notification if count increased
                $latestNotification = null;
                if ($currentCount > $lastCount) {
                    $unreadNotifications = $this->notificationService->getUnread($userId, 1);
                    if (!empty($unreadNotifications)) {
                        $latestNotification = $unreadNotifications[0];
                    }
                }

                // Send update immediately
                echo "event: update\n";
                echo "data: " . json_encode([
                    'count' => $currentCount,
                    'previous_count' => $lastCount,
                    'notification' => $latestNotification
                ]) . "\n\n";
                if (ob_get_level() == 0) {
                    flush();
                }

                $lastCount = $currentCount;
            }

            // Send heartbeat periodically to keep connection alive
            if ($iteration % $heartbeatInterval === 0 && $iteration > 0) {
                echo "event: heartbeat\n";
                echo "data: " . json_encode(['heartbeat' => time()]) . "\n\n";
                if (ob_get_level() == 0) {
                    flush();
                }
            }

            // Wait 2 seconds before next check
            sleep(2);
            $iteration++;
        }

        // Send close message
        echo "event: close\n";
        echo "data: " . json_encode(['message' => 'Connection closed']) . "\n\n";
        if (ob_get_level() == 0) {
            flush();
        }

        exit;
    }
}
