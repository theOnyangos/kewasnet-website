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
            
            $pageUrl = $input['page_url'] ?? current_url();
            $pageTitle = $input['page_title'] ?? null;
            $pageCategory = $input['page_category'] ?? null;
            
            $result = $this->trackingService->trackPageView($pageUrl, $pageTitle, $pageCategory);
            
            if ($result) {
                return $this->respond([
                    'success' => true,
                    'page_view_id' => $result,
                    'message' => 'Page view tracked'
                ]);
            } else {
                return $this->respond([
                    'success' => false,
                    'message' => 'Page view not tracked (consent required)'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Page tracking failed: ' . $e->getMessage());
            return $this->fail('An error occurred while tracking page view');
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
     * Get analytics dashboard data
     */
    public function dashboard()
    {
        try {
            // Check admin permission
            if (!$this->isAdmin()) {
                return $this->failUnauthorized('Access denied');
            }
            
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            
            $data = $this->trackingService->getDashboardData($startDate, $endDate);
            
            return $this->respond([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard data fetch failed: ' . $e->getMessage());
            return $this->fail('An error occurred while fetching dashboard data');
        }
    }

    /**
     * Get real-time statistics
     */
    public function realTime()
    {
        try {
            // Check admin permission
            if (!$this->isAdmin()) {
                return $this->failUnauthorized('Access denied');
            }
            
            $stats = $this->trackingService->getRealTimeStats();
            
            return $this->respond([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Real-time stats fetch failed: ' . $e->getMessage());
            return $this->fail('An error occurred while fetching real-time stats');
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
        return CIAuth::isLoggedIn() && CIAuth::isAdmin();
    }
}
