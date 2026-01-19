<?php

namespace App\Controllers\API;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Services\ActivityTrackingService;
use App\Libraries\CIAuth;

class TrackingController extends BaseController
{
    use ResponseTrait;

    protected $trackingService;

    public function __construct()
    {
        $this->trackingService = new ActivityTrackingService();
    }

    /**
     * Initialize tracking session
     */
    public function initSession()
    {
        try {
            $input = $this->request->getJSON(true);
            
            $analyticsConsent = $input['analytics_consent'] ?? false;
            $marketingConsent = $input['marketing_consent'] ?? false;
            
            $result = $this->trackingService->initializeSession($analyticsConsent, $marketingConsent);
            
            if ($result) {
                $sessionId = session()->get('tracking_session_id');
                return $this->respond([
                    'success' => true,
                    'message' => 'Tracking session initialized',
                    'session_id' => $sessionId
                ]);
            } else {
                return $this->fail('Failed to initialize tracking session');
            }
        } catch (\Exception $e) {
            log_message('error', 'Tracking session initialization failed: ' . $e->getMessage());
            return $this->fail('An error occurred while initializing tracking');
        }
    }

    /**
     * Track page view
     */
    public function trackPage()
    {
        try {
            $input = $this->request->getJSON(true);
            
            // Validate input
            if (empty($input)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Invalid request data',
                    'error' => 'No data provided'
                ], 400);
            }
            
            $pageUrl = $input['page_url'] ?? current_url();
            $pageTitle = $input['page_title'] ?? null;
            $pageCategory = $input['page_category'] ?? null;
            
            // Validate page URL
            if (empty($pageUrl)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Page URL is required',
                    'error' => 'Missing page_url parameter'
                ], 400);
            }
            
            // Check consent status for better error messages
            $hasConsent = $this->trackingService->hasAnalyticsConsent();
            if (!$hasConsent) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Page view not tracked (analytics consent required)',
                    'error' => 'consent_required',
                    'debug' => [
                        'cookieConsent' => $this->request->getCookie('cookieConsent'),
                        'analyticsCookies' => $this->request->getCookie('analyticsCookies')
                    ]
                ], 403);
            }
            
            $result = $this->trackingService->trackPageView($pageUrl, $pageTitle, $pageCategory);
            
            if ($result) {
                return $this->respond([
                    'success' => true,
                    'page_view_id' => $result,
                    'message' => 'Page view tracked successfully'
                ]);
            } else {
                // Get session status for debugging
                $session = session();
                $trackingSessionId = $session->get('tracking_session_id');
                
                return $this->respond([
                    'success' => false,
                    'message' => 'Page view tracking failed',
                    'error' => 'tracking_failed',
                    'debug' => [
                        'has_consent' => $hasConsent,
                        'session_initialized' => !empty($trackingSessionId),
                        'tracking_session_id' => $trackingSessionId
                    ]
                ], 500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Page tracking failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->respond([
                'success' => false,
                'message' => 'An error occurred while tracking page view',
                'error' => 'server_error',
                'debug' => ENVIRONMENT === 'development' ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    /**
     * Update page view with time spent
     */
    public function updatePage()
    {
        try {
            $input = $this->request->getJSON(true);
            
            $timeOnPage = $input['time_on_page'] ?? 0;
            $scrollDepth = $input['scroll_depth'] ?? null;
            $isExit = $input['is_exit'] ?? false;
            
            $result = $this->trackingService->updatePageView($timeOnPage, $scrollDepth, $isExit);
            
            return $this->respond([
                'success' => $result !== false,
                'message' => $result ? 'Page view updated' : 'Update failed or consent required'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Page update failed: ' . $e->getMessage());
            return $this->fail('An error occurred while updating page view');
        }
    }

    /**
     * Track user event
     */
    public function trackEvent()
    {
        try {
            $input = $this->request->getJSON(true);
            
            $eventType = $input['event_type'] ?? null;
            $eventAction = $input['event_action'] ?? null;
            $eventLabel = $input['event_label'] ?? null;
            $eventValue = $input['event_value'] ?? null;
            $eventCategory = $input['event_category'] ?? null;
            
            if (!$eventType || !$eventAction) {
                return $this->fail('Event type and action are required');
            }
            
            $result = $this->trackingService->trackEvent(
                $eventType,
                $eventAction,
                $eventLabel,
                $eventValue,
                $eventCategory
            );
            
            return $this->respond([
                'success' => $result !== false,
                'message' => $result ? 'Event tracked' : 'Event not tracked (consent required)'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Event tracking failed: ' . $e->getMessage());
            return $this->fail('An error occurred while tracking event');
        }
    }

    /**
     * End user session
     */
    public function endSession()
    {
        try {
            $result = $this->trackingService->endSession();
            
            return $this->respond([
                'success' => $result !== false,
                'message' => 'Session ended'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Session end failed: ' . $e->getMessage());
            return $this->fail('An error occurred while ending session');
        }
    }

    /**
     * Debug endpoint to check consent and session status
     * Useful for troubleshooting tracking issues
     */
    public function debugStatus()
    {
        $cookies = [
            'cookieConsent' => $this->request->getCookie('cookieConsent'),
            'analyticsCookies' => $this->request->getCookie('analyticsCookies'),
            'marketingCookies' => $this->request->getCookie('marketingCookies'),
        ];
        
        $session = session();
        $trackingSessionId = $session->get('tracking_session_id');
        
        return $this->respond([
            'cookies' => $cookies,
            'tracking_session_id' => $trackingSessionId,
            'has_consent' => $this->trackingService->hasAnalyticsConsent(),
            'session_initialized' => !empty($trackingSessionId)
        ]);
    }

    /**
     * Get analytics dashboard data
     */
    public function dashboard()
    {
        // Check admin permission first
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied',
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }
        
        try {
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            $range = $this->request->getGet('range');
            
            // If range is provided, calculate start and end dates
            if ($range && !$startDate && !$endDate) {
                switch ($range) {
                    case 'today':
                        $startDate = date('Y-m-d 00:00:00');
                        $endDate = date('Y-m-d 23:59:59');
                        break;
                    case 'yesterday':
                        $startDate = date('Y-m-d 00:00:00', strtotime('-1 day'));
                        $endDate = date('Y-m-d 23:59:59', strtotime('-1 day'));
                        break;
                    case 'week':
                        $startDate = date('Y-m-d 00:00:00', strtotime('-7 days'));
                        $endDate = date('Y-m-d 23:59:59');
                        break;
                    case 'month':
                        $startDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
                        $endDate = date('Y-m-d 23:59:59');
                        break;
                    default:
                        $startDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
                        $endDate = date('Y-m-d 23:59:59');
                }
            }
            
            // Ensure we have dates
            if (!$startDate) {
                $startDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
            }
            if (!$endDate) {
                $endDate = date('Y-m-d 23:59:59');
            }
            
            $data = $this->trackingService->getDashboardData($startDate, $endDate);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard data fetch failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard data',
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get real-time statistics
     */
    public function realTime()
    {
        // Check admin permission first
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied',
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }
        
        try {
            $stats = $this->trackingService->getRealTimeStats();
            
            // Get recent activities for real-time feed
            $recentActivities = $this->trackingService->getRecentActivities(10);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'activities' => $recentActivities
                ]
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'Real-time stats fetch failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while fetching real-time stats',
                'error' => $e->getMessage(),
                'data' => [
                    'stats' => [],
                    'activities' => []
                ]
            ])->setStatusCode(500);
        }
    }

    /**
     * Get activities for server-side data table
     */
    public function activitiesDataTable()
    {
        // Check admin permission first
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'error' => 'Access denied'
            ])->setStatusCode(401);
        }
        
        try {
            // Get DataTables parameters from POST data (DataTables sends POST)
            $draw = intval($this->request->getPost('draw') ?? 1);
            $start = intval($this->request->getPost('start') ?? 0);
            $length = intval($this->request->getPost('length') ?? 10);
            $searchValue = $this->request->getPost('search')['value'] ?? '';
            
            // Handle order parameter
            $orderColumn = 0;
            $orderDir = 'DESC';
            $orderData = $this->request->getPost('order');
            if (!empty($orderData) && isset($orderData[0])) {
                $orderColumn = (int)($orderData[0]['column'] ?? 0);
                $orderDir = strtoupper($orderData[0]['dir'] ?? 'DESC');
            }
            
            // Validate order direction
            if (!in_array($orderDir, ['ASC', 'DESC'])) {
                $orderDir = 'DESC';
            }
            
            $result = $this->trackingService->getActivitiesForDataTable(
                $draw,
                $start,
                $length,
                $searchValue,
                $orderColumn,
                $orderDir
            );
            
            // Log for debugging
            log_message('debug', 'Activities DataTable request - Draw: ' . $draw . ', Start: ' . $start . ', Length: ' . $length . ', Search: ' . $searchValue);
            log_message('debug', 'Activities DataTable response - Total: ' . $result['recordsTotal'] . ', Filtered: ' . $result['recordsFiltered'] . ', Data count: ' . count($result['data']));
            
            return $this->response->setJSON($result)->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'Activities data table fetch failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'draw' => (int)($this->request->getGet('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Debug endpoint to test real-time stats without authentication (development only)
     */
    public function debugRealTime()
    {
        if (ENVIRONMENT !== 'development') {
            return $this->failForbidden('Debug endpoint only available in development');
        }

        try {
            $stats = $this->trackingService->getRealTimeStats();
            
            return $this->respond([
                'success' => true,
                'data' => $stats,
                'debug' => true
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Debug endpoint to test dashboard data without authentication (development only)
     */
    public function debugDashboard()
    {
        if (ENVIRONMENT !== 'development') {
            return $this->failForbidden('Debug endpoint only available in development');
        }

        try {
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            $range = $this->request->getGet('range');
            
            $data = $this->trackingService->getDashboardData($startDate, $endDate, $range);
            
            return $this->respond([
                'success' => true,
                'data' => $data,
                'debug' => true
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Batch track multiple events (for offline support)
     */
    public function batchTrack()
    {
        try {
            $input = $this->request->getJSON(true);
            $events = $input['events'] ?? [];
            
            if (empty($events)) {
                return $this->fail('No events provided');
            }
            
            $successCount = 0;
            $failCount = 0;
            
            foreach ($events as $event) {
                $eventType = $event['type'] ?? 'custom';
                
                switch ($eventType) {
                    case 'page_view':
                        $result = $this->trackingService->trackPageView(
                            $event['page_url'] ?? '',
                            $event['page_title'] ?? null,
                            $event['page_category'] ?? null
                        );
                        break;
                        
                    case 'event':
                        $result = $this->trackingService->trackEvent(
                            $event['event_type'] ?? 'custom',
                            $event['event_action'] ?? 'unknown',
                            $event['event_label'] ?? null,
                            $event['event_value'] ?? null,
                            $event['event_category'] ?? null
                        );
                        break;
                        
                    default:
                        $result = false;
                }
                
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }
            
            return $this->respond([
                'success' => true,
                'processed' => count($events),
                'successful' => $successCount,
                'failed' => $failCount
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Batch tracking failed: ' . $e->getMessage());
            return $this->fail('An error occurred while processing batch tracking');
        }
    }

    /**
     * Check if user is admin
     */
    private function isAdmin()
    {
        try {
            return CIAuth::isLoggedIn() && CIAuth::isAdmin();
        } catch (\Exception $e) {
            log_message('error', 'Admin check failed: ' . $e->getMessage());
            return false;
        }
    }
}
