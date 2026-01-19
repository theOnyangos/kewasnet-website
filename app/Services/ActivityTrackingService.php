<?php

namespace App\Services;

use App\Models\UserSessionModel;
use App\Models\PageViewModel;
use App\Models\UserEventModel;

class ActivityTrackingService
{
    protected $sessionModel;
    protected $pageViewModel;
    protected $eventModel;
    protected $request;
    protected $session;

    public function __construct()
    {
        $this->sessionModel = new UserSessionModel();
        $this->pageViewModel = new PageViewModel();
        $this->eventModel = new UserEventModel();
        $this->request = \Config\Services::request();
        $this->session = session();
    }

    /**
     * Initialize tracking session
     */
    public function initializeSession($analyticsConsent = false, $marketingConsent = false)
    {
        $sessionId = session_id();
        
        // Check if session already exists
        $existingSession = $this->sessionModel->where('session_id', $sessionId)->first();
        
        if (!$existingSession) {
            $userAgent = $this->request->getUserAgent();
            $deviceInfo = $this->parseUserAgent($userAgent->getAgentString());
            $location = $this->getLocationFromIP($this->request->getIPAddress());
            
            $sessionData = [
                'session_id' => $sessionId,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $userAgent->getAgentString(),
                'browser' => $deviceInfo['browser'],
                'device' => $deviceInfo['device'],
                'os' => $deviceInfo['os'],
                'country' => $location['country'] ?? null,
                'city' => $location['city'] ?? null,
                'referrer' => $this->request->getServer('HTTP_REFERER'),
                'analytics_consent' => $analyticsConsent,
                'marketing_consent' => $marketingConsent,
                'session_start' => date('Y-m-d H:i:s'),
                'page_views' => 0,
                'is_bounce' => true
            ];
            
            $result = $this->sessionModel->insert($sessionData);
            
            if ($result) {
                // Since the model generates UUID in beforeInsert, we need to retrieve it
                // Find the session we just inserted by session_id
                $insertedSession = $this->sessionModel->where('session_id', $sessionId)->first();
                if ($insertedSession) {
                    $this->session->set('tracking_session_id', $insertedSession->id);
                    return true;
                }
            }
            
            return false;
        } else {
            // Update consent if needed
            if ($existingSession->analytics_consent != $analyticsConsent || 
                $existingSession->marketing_consent != $marketingConsent) {
                
                $this->sessionModel->update($existingSession->id, [
                    'analytics_consent' => $analyticsConsent,
                    'marketing_consent' => $marketingConsent
                ]);
            }
            
            $this->session->set('tracking_session_id', $existingSession->id);
            return true;
        }
    }

    /**
     * Track page view
     */
    public function trackPageView($pageUrl, $pageTitle = null, $pageCategory = null)
    {
        // Check consent first
        if (!$this->hasAnalyticsConsent()) {
            log_message('debug', 'Page view tracking failed: No analytics consent. Cookies: ' . json_encode($_COOKIE));
            return false;
        }

        // Check session - if not initialized, try to initialize it
        $sessionId = $this->getTrackingSessionId();
        if (!$sessionId) {
            log_message('debug', 'Page view tracking: No tracking session ID found, attempting to initialize session...');
            
            // Try to initialize session automatically
            $analyticsConsent = $this->hasAnalyticsConsent();
            $marketingConsent = $this->hasMarketingConsent();
            
            $initialized = $this->initializeSession($analyticsConsent, $marketingConsent);
            
            if ($initialized) {
                $sessionId = $this->getTrackingSessionId();
                log_message('debug', 'Page view tracking: Session initialized successfully. Session ID: ' . $sessionId);
            } else {
                log_message('debug', 'Page view tracking failed: Could not initialize session. Session data: ' . json_encode($this->session->get()));
                return false;
            }
        }

        if (!$sessionId) {
            log_message('debug', 'Page view tracking failed: No tracking session ID after initialization attempt. Session data: ' . json_encode($this->session->get()));
            return false;
        }

        // Record page view
        $viewedAt = date('Y-m-d H:i:s');
        $pageViewData = [
            'session_id' => $sessionId,
            'page_url' => $this->cleanUrl($pageUrl),
            'page_title' => $pageTitle,
            'page_category' => $pageCategory ?: $this->categorizeUrl($pageUrl),
            'viewed_at' => $viewedAt
        ];

        $result = $this->pageViewModel->insert($pageViewData);
        
        if ($result) {
            // Retrieve the inserted page view to get its UUID
            $insertedPageView = $this->pageViewModel
                ->where('session_id', $sessionId)
                ->where('page_url', $this->cleanUrl($pageUrl))
                ->where('viewed_at', $viewedAt)
                ->orderBy('created_at', 'DESC')
                ->first();
            
            if ($insertedPageView) {
                $pageViewId = $insertedPageView->id;
                
                // Update session page count
                $this->incrementPageViews($sessionId);
                
                // Store current page view ID in session for later updates
                $this->session->set('current_page_view_id', $pageViewId);
                
                return $pageViewId;
            }
        }
        
        return false;
    }

    /**
     * Update page view with time spent and scroll depth
     */
    public function updatePageView($timeOnPage, $scrollDepth = null, $isExit = false)
    {
        if (!$this->hasAnalyticsConsent()) {
            return false;
        }

        $pageViewId = $this->session->get('current_page_view_id');
        if (!$pageViewId) {
            return false;
        }

        return $this->pageViewModel->updateTimeOnPage($pageViewId, $timeOnPage, $scrollDepth, $isExit);
    }

    /**
     * Track user event
     */
    public function trackEvent($eventType, $eventAction, $eventLabel = null, $eventValue = null, $eventCategory = null)
    {
        // Check consent first
        if (!$this->hasAnalyticsConsent()) {
            log_message('debug', 'Event tracking failed: No analytics consent. Cookies: ' . json_encode($_COOKIE));
            return false;
        }

        // Check session - if not initialized, try to initialize it
        $sessionId = $this->getTrackingSessionId();
        if (!$sessionId) {
            log_message('debug', 'Event tracking: No tracking session ID found, attempting to initialize session...');
            
            // Try to initialize session automatically
            $analyticsConsent = $this->hasAnalyticsConsent();
            $marketingConsent = $this->hasMarketingConsent();
            
            $initialized = $this->initializeSession($analyticsConsent, $marketingConsent);
            
            if ($initialized) {
                $sessionId = $this->getTrackingSessionId();
                log_message('debug', 'Event tracking: Session initialized successfully. Session ID: ' . $sessionId);
            } else {
                log_message('debug', 'Event tracking failed: Could not initialize session. Session data: ' . json_encode($this->session->get()));
                return false;
            }
        }

        if (!$sessionId) {
            log_message('debug', 'Event tracking failed: No tracking session ID after initialization attempt. Session data: ' . json_encode($this->session->get()));
            return false;
        }

        // Get current page URL for the event
        $pageUrl = current_url();
        if (empty($pageUrl) || $pageUrl === '/') {
            $pageUrl = $this->request->getUri()->getPath() ?? '/';
        }
        
        // Clean the URL (remove query strings for consistency)
        $parsedUrl = parse_url($pageUrl);
        $pageUrl = $parsedUrl['path'] ?? '/';

        return $this->eventModel->trackEvent(
            $sessionId,
            $eventType,
            $eventAction,
            $eventLabel,
            $eventValue,
            $eventCategory,
            $this->session->get('current_page_view_id'),
            $pageUrl
        );
    }

    /**
     * End current session
     */
    public function endSession()
    {
        $sessionId = session_id();
        return $this->sessionModel->endSession($sessionId);
    }

    /**
     * Get comprehensive analytics dashboard data
     */
    public function getDashboardData($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
        } else {
            // Ensure startDate has time component
            if (strlen($startDate) === 10) {
                $startDate .= ' 00:00:00';
            }
        }
        
        if (!$endDate) {
            $endDate = date('Y-m-d 23:59:59');
        } else {
            // Ensure endDate has time component
            if (strlen($endDate) === 10) {
                $endDate .= ' 23:59:59';
            }
        }

        $sessionStats = $this->sessionModel->getSessionStats($startDate, $endDate);
        $popularPages = $this->pageViewModel->getPopularPages(10, $startDate, $endDate);
        $viewsByCategory = $this->pageViewModel->getViewsByCategory($startDate, $endDate);
        $deviceBreakdown = $this->sessionModel->getSessionsByDevice($startDate, $endDate);
        
        // Debug: Log device breakdown
        log_message('debug', 'getDashboardData - deviceBreakdown raw: ' . json_encode($deviceBreakdown));
        log_message('debug', 'getDashboardData - deviceBreakdown count: ' . (is_array($deviceBreakdown) ? count($deviceBreakdown) : 'not array'));
        
        $topCountries = $this->sessionModel->getTopCountries(10, $startDate, $endDate);
        $eventStats = $this->eventModel->getEventStats($startDate, $endDate);
        $topEvents = $this->eventModel->getTopEvents(10, $startDate, $endDate);
        $eventsByCategory = $this->eventModel->getEventsByCategory($startDate, $endDate);
        $topClicks = $this->eventModel->getTopClicks(10, $startDate, $endDate);
        $formSubmissions = $this->eventModel->getFormStats($startDate, $endDate);
        $downloads = $this->eventModel->getDownloadStats($startDate, $endDate);
        $searchQueries = $this->eventModel->getSearchQueries(10, $startDate, $endDate);
        $newRegistrations = $this->eventModel->getNewRegistrations($startDate, $endDate);
        
        // Get page views timeline
        $dailyViews = $this->pageViewModel->getDailyViewsTimeline($startDate, $endDate);
        $pageViewsTimeline = $this->formatTimelineData($dailyViews);
        
        // Format device breakdown for charts
        // Debug: Check deviceBreakdown before formatting
        if (empty($deviceBreakdown)) {
            log_message('warning', 'getDashboardData - deviceBreakdown is empty! Re-querying...');
            // Re-query to verify
            $deviceBreakdown = $this->sessionModel->getSessionsByDevice($startDate, $endDate);
            log_message('debug', 'getDashboardData - deviceBreakdown after re-query: ' . json_encode($deviceBreakdown));
        }
        
        $deviceStats = $this->formatDeviceStats($deviceBreakdown);
        
        // Format event stats for charts
        $eventStatsChart = $this->formatEventStatsForChart($eventStats);
        
        // Debug logging
        log_message('debug', 'Dashboard data - page_views_timeline: ' . json_encode($pageViewsTimeline));
        log_message('debug', 'Dashboard data - device_stats: ' . json_encode($deviceStats));
        log_message('debug', 'Dashboard data - device_breakdown raw: ' . json_encode($deviceBreakdown));

        // Calculate overview metrics
        $totalPageViews = 0;
        if (is_array($popularPages)) {
            foreach ($popularPages as $page) {
                $totalPageViews += is_object($page) ? ($page->views ?? 0) : ($page['views'] ?? 0);
            }
        }
        
        $activeSessions = is_array($sessionStats) ? ($sessionStats['total_sessions'] ?? 0) : 0;
        
        $totalEvents = 0;
        if (is_array($eventStats)) {
            foreach ($eventStats as $event) {
                $totalEvents += is_object($event) ? ($event->count ?? 0) : ($event['count'] ?? 0);
            }
        }
        
        $avgSessionDuration = is_array($sessionStats) ? ($sessionStats['avg_duration'] ?? 0) : 0;

        return [
            'overview' => [
                'total_page_views' => $totalPageViews,
                'active_sessions' => $activeSessions,
                'total_events' => $totalEvents,
                'avg_session_duration' => $avgSessionDuration
            ],
            'session_stats' => $sessionStats,
            'popular_pages' => $popularPages,
            'views_by_category' => $viewsByCategory,
            'device_breakdown' => $deviceBreakdown,
            'device_stats' => $deviceStats, // Formatted for charts
            'top_countries' => $topCountries,
            'event_stats' => $eventStats,
            'event_stats_chart' => $eventStatsChart, // Formatted for charts
            'top_events' => $topEvents,
            'events_by_category' => $eventsByCategory,
            'top_clicks' => $topClicks,
            'form_submissions' => $formSubmissions,
            'downloads' => $downloads,
            'search_queries' => $searchQueries,
            'new_registrations' => $newRegistrations,
            'page_views_timeline' => $pageViewsTimeline // Timeline data for charts
        ];
    }

    /**
     * Format timeline data for charts
     */
    private function formatTimelineData($dailyViews)
    {
        $labels = [];
        $data = [];
        
        if (empty($dailyViews) || !is_array($dailyViews)) {
            // Generate default labels for the last 7 days if no data
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
                if ($dateObj) {
                    $labels[] = $dateObj->format('M d');
                } else {
                    $labels[] = $date;
                }
                $data[] = 0;
            }
        } else {
            foreach ($dailyViews as $day) {
                $date = is_object($day) ? ($day->date ?? null) : ($day['date'] ?? null);
                $views = is_object($day) ? ($day->views ?? 0) : ($day['views'] ?? 0);
                
                if ($date) {
                    // Format date as M d (e.g., "Jan 15")
                    $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
                    if ($dateObj) {
                        $labels[] = $dateObj->format('M d');
                    } else {
                        $labels[] = $date;
                    }
                    
                    $data[] = (int)$views;
                }
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Format device stats for charts
     * Ensures all device types (Desktop, Mobile, Tablet) are included even if count is 0
     */
    private function formatDeviceStats($deviceBreakdown)
    {
        // Initialize with all device types set to 0
        $deviceCounts = [
            'Desktop' => 0,
            'Mobile' => 0,
            'Tablet' => 0
        ];
        
        // Populate with actual data
        if (is_array($deviceBreakdown) && !empty($deviceBreakdown)) {
            foreach ($deviceBreakdown as $device) {
                $deviceType = is_object($device) ? ($device->device ?? 'Unknown') : ($device['device'] ?? 'Unknown');
                $count = is_object($device) ? ($device->count ?? 0) : ($device['count'] ?? 0);
                
                if ($deviceType && $deviceType !== 'Unknown' && $deviceType !== null) {
                    // Normalize device type name (capitalize first letter)
                    $normalizedType = ucfirst(strtolower(trim($deviceType)));
                    
                    // Map common variations
                    if (in_array($normalizedType, ['Desktop', 'Mobile', 'Tablet'])) {
                        $deviceCounts[$normalizedType] = (int)$count;
                    } else {
                        // For unknown device types, add them as-is
                        if (!isset($deviceCounts[$normalizedType])) {
                            $deviceCounts[$normalizedType] = 0;
                        }
                        $deviceCounts[$normalizedType] += (int)$count;
                    }
                }
            }
        }
        
        // Build labels and data arrays - ALWAYS include Desktop, Mobile, Tablet
        $labels = [];
        $data = [];
        
        // Always include Desktop, Mobile, Tablet in order (even if count is 0)
        foreach (['Desktop', 'Mobile', 'Tablet'] as $type) {
            $labels[] = $type;
            $data[] = isset($deviceCounts[$type]) ? (int)$deviceCounts[$type] : 0;
        }
        
        // Add any other device types that were found (with non-zero counts)
        foreach ($deviceCounts as $type => $count) {
            if (!in_array($type, ['Desktop', 'Mobile', 'Tablet']) && $count > 0) {
                $labels[] = $type;
                $data[] = (int)$count;
            }
        }
        
        // Log for debugging
        log_message('debug', 'formatDeviceStats - Input: ' . json_encode($deviceBreakdown));
        log_message('debug', 'formatDeviceStats - Output: ' . json_encode(['labels' => $labels, 'data' => $data]));
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Format event stats for charts
     */
    private function formatEventStatsForChart($eventStats)
    {
        $labels = [];
        $data = [];
        
        if (is_array($eventStats)) {
            foreach ($eventStats as $stat) {
                $eventType = is_object($stat) ? ($stat->event_type ?? 'Unknown') : ($stat['event_type'] ?? 'Unknown');
                $count = is_object($stat) ? ($stat->count ?? 0) : ($stat['count'] ?? 0);
                
                // Format event type name
                $displayName = str_replace('_', ' ', $eventType);
                $displayName = ucwords($displayName);
                
                $labels[] = $displayName;
                $data[] = (int)$count;
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Check if user has given analytics consent
     * Made public for debugging purposes
     */
    public function hasAnalyticsConsent()
    {
        $cookieConsent = $this->getCookieValue('cookieConsent');
        $analyticsConsent = $this->getCookieValue('analyticsCookies');
        
        $hasConsent = $cookieConsent === 'all' || $analyticsConsent === 'accepted';
        
        if (!$hasConsent) {
            log_message('debug', 'Analytics consent check failed. cookieConsent: ' . $cookieConsent . ', analyticsCookies: ' . $analyticsConsent);
        }
        
        return $hasConsent;
    }

    /**
     * Check if user has given marketing consent
     */
    private function hasMarketingConsent()
    {
        $cookieConsent = $this->getCookieValue('cookieConsent');
        $marketingConsent = $this->getCookieValue('marketingCookies');
        
        return $cookieConsent === 'all' || $marketingConsent === 'accepted';
    }

    /**
     * Get cookie value
     */
    private function getCookieValue($name)
    {
        return $this->request->getCookie($name) ?? '';
    }

    /**
     * Get tracking session ID
     */
    private function getTrackingSessionId()
    {
        return $this->session->get('tracking_session_id');
    }

    /**
     * Increment page views for session
     */
    private function incrementPageViews($sessionId)
    {
        $session = $this->sessionModel->find($sessionId);
        if ($session) {
            $this->sessionModel->update($sessionId, [
                'page_views' => $session->page_views + 1,
                'is_bounce' => false // No longer a bounce if more than 1 page
            ]);
        }
    }

    /**
     * Parse user agent string
     */
    private function parseUserAgent($userAgentString)
    {
        $userAgent = $this->request->getUserAgent();
        
        // Detect device type with tablet support
        $deviceType = $this->detectDeviceType($userAgentString);
        
        return [
            'browser' => $userAgent->getBrowser(),
            'device' => $deviceType,
            'os' => $userAgent->getPlatform()
        ];
    }

    /**
     * Enhanced device detection including tablet support
     * Detects Desktop, Mobile, or Tablet based on user agent string
     */
    private function detectDeviceType($userAgentString)
    {
        $userAgent = $this->request->getUserAgent();
        $uaLower = strtolower($userAgentString);
        
        // Check for tablet first (more specific than mobile)
        // iPad detection (including newer iPadOS)
        if (stripos($userAgentString, 'iPad') !== false) {
            return 'Tablet';
        }
        
        // iPadOS 13+ reports as Macintosh with Safari, but without "Mobile"
        if (stripos($userAgentString, 'Macintosh') !== false && 
            stripos($userAgentString, 'Safari') !== false && 
            stripos($userAgentString, 'Mobile') === false) {
            // This could be an iPad (iPadOS 13+) or a Mac
            // We'll treat it as Desktop for now, as distinguishing requires client-side detection
            // In production, you might want to use JavaScript to detect touch support
        }
        
        // Android tablets
        $androidTabletKeywords = [
            'tablet', 'Tab', 'GT-P', 'SM-T', 'SCH-I800', 'SHW-M180W', 
            'Kindle', 'Silk', 'PlayBook', 'Nexus 7', 'Nexus 10', 'Nexus 9',
            'KFAPWI', 'KFAPWA', 'KFJWA', 'KFJWI', 'KFTT', 'KFOT', 'KFTHWA', 'KFTHWI',
            'KFSOWI', 'KFASWI', 'KFASWA', 'KFASWI', 'KFASWA', 'KFASWI', 'KFASWA',
            'SGP', 'Xoom', 'Transformer', 'TF101', 'TF201', 'TF300T', 'TF700T',
            'A100', 'A101', 'A200', 'A210', 'A211', 'A500', 'A501', 'A510', 'A511',
            'A700', 'A701', 'W500', 'W500P', 'W501', 'W501P', 'W700', 'W701'
        ];
        
        foreach ($androidTabletKeywords as $keyword) {
            if (stripos($uaLower, strtolower($keyword)) !== false) {
                return 'Tablet';
            }
        }
        
        // Windows tablets (Surface, etc.)
        if (stripos($uaLower, 'touch') !== false && 
            (stripos($uaLower, 'windows') !== false || stripos($uaLower, 'tablet pc') !== false)) {
            return 'Tablet';
        }
        
        // Check for mobile devices (phones)
        // Use CodeIgniter's built-in mobile detection first
        if ($userAgent->isMobile()) {
            // Double-check it's not a tablet that was misidentified
            // Most tablets will have been caught above, but some Android tablets
            // might only be detected as mobile
            if (stripos($uaLower, 'tablet') !== false || 
                stripos($uaLower, 'pad') !== false ||
                (stripos($uaLower, 'android') !== false && stripos($uaLower, 'mobile') === false)) {
                // Android without "Mobile" in UA string is usually a tablet
                return 'Tablet';
            }
            return 'Mobile';
        }
        
        // Additional mobile detection patterns
        $mobilePatterns = [
            'iphone', 'ipod', 'android.*mobile', 'blackberry', 'windows phone',
            'opera mini', 'mobile', 'palm', 'smartphone', 'iemobile'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $userAgentString)) {
                return 'Mobile';
            }
        }
        
        // Default to Desktop
        return 'Desktop';
    }

    /**
     * Get location from IP (simplified - you can integrate with services like GeoIP)
     */
    private function getLocationFromIP($ipAddress)
    {
        // Placeholder for IP geolocation
        // You can integrate with services like MaxMind GeoIP, IPinfo, etc.
        return [
            'country' => 'Unknown',
            'city' => 'Unknown'
        ];
    }

    /**
     * Clean URL for storage
     */
    private function cleanUrl($url)
    {
        // Remove query parameters and fragments for cleaner tracking
        $parsed = parse_url($url);
        return $parsed['path'] ?? $url;
    }

    /**
     * Auto-categorize URL based on path
     */
    private function categorizeUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        
        if (strpos($path, '/blog') === 0) return 'Blog';
        if (strpos($path, '/resources') === 0) return 'Resources';
        if (strpos($path, '/events') === 0) return 'Events';
        if (strpos($path, '/about') === 0) return 'About';
        if (strpos($path, '/contact') === 0) return 'Contact';
        if (strpos($path, '/news') === 0) return 'News';
        if (strpos($path, '/careers') === 0) return 'Careers';
        if ($path === '/' || $path === '') return 'Home';
        
        return 'Other';
    }

    /**
     * Get real-time statistics
     */
    public function getRealTimeStats()
    {
        $activeSessions = $this->sessionModel->getActiveSessions();
        $todayStart = date('Y-m-d 00:00:00');
        $now = date('Y-m-d H:i:s');
        
        return [
            'active_users' => count($activeSessions),
            'todays_sessions' => $this->sessionModel->where('session_start >=', $todayStart)->countAllResults(),
            'todays_page_views' => $this->pageViewModel->where('viewed_at >=', $todayStart)->countAllResults(),
            'todays_events' => $this->eventModel->where('occurred_at >=', $todayStart)->countAllResults()
        ];
    }

    /**
     * Get recent activities for real-time feed
     */
    public function getRecentActivities($limit = 10)
    {
        $activities = [];
        
        // Get recent page views
        $recentPageViews = $this->pageViewModel
            ->orderBy('viewed_at', 'DESC')
            ->limit($limit)
            ->findAll();
        
        foreach ($recentPageViews as $pageView) {
            $activities[] = [
                'type' => 'page_view',
                'description' => 'Page viewed: ' . ($pageView->page_title ?: $pageView->page_url),
                'created_at' => $pageView->viewed_at,
                'url' => $pageView->page_url
            ];
        }
        
        // Get recent events
        $recentEvents = $this->eventModel
            ->orderBy('occurred_at', 'DESC')
            ->limit($limit)
            ->findAll();
        
        foreach ($recentEvents as $event) {
            $activities[] = [
                'type' => $event->event_type,
                'description' => ucfirst($event->event_action) . ': ' . ($event->event_label ?: 'Unknown'),
                'created_at' => $event->occurred_at,
                'category' => $event->event_category
            ];
        }
        
        // Sort by created_at descending and limit
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, $limit);
    }

    /**
     * Get activities for server-side data table
     * Supports pagination, sorting, and filtering
     */
    public function getActivitiesForDataTable($draw, $start = 0, $length = 10, $search = '', $orderColumn = 0, $orderDir = 'DESC')
    {
        $activities = [];
        
        // Get all page views
        $pageViewsBuilder = $this->pageViewModel->builder();
        if (!empty($search)) {
            $pageViewsBuilder->groupStart()
                ->like('page_url', $search)
                ->orLike('page_title', $search)
                ->groupEnd();
        }
        $recentPageViews = $pageViewsBuilder->orderBy('viewed_at', 'DESC')->get()->getResult();
        
        foreach ($recentPageViews as $pageView) {
            $activities[] = [
                'type' => 'page_view',
                'description' => 'Page viewed: ' . ($pageView->page_title ?: $pageView->page_url),
                'created_at' => $pageView->viewed_at,
                'category' => 'Page View',
                'url' => $pageView->page_url,
                'raw_type' => 'page_view',
                'raw_created_at' => strtotime($pageView->viewed_at)
            ];
        }
        
        // Get all events
        $eventsBuilder = $this->eventModel->builder();
        if (!empty($search)) {
            $eventsBuilder->groupStart()
                ->like('event_action', $search)
                ->orLike('event_label', $search)
                ->orLike('event_category', $search)
                ->orLike('page_url', $search)
                ->groupEnd();
        }
        $recentEvents = $eventsBuilder->orderBy('occurred_at', 'DESC')->get()->getResult();
        
        foreach ($recentEvents as $event) {
            // Determine event type - use event_type if available, otherwise derive from category
            $eventType = $event->event_type;
            if (empty($eventType)) {
                // Derive type from category if type is missing
                $category = strtolower($event->event_category ?? '');
                if (strpos($category, 'media') !== false || strpos($category, 'social') !== false) {
                    $eventType = 'media';
                } elseif (strpos($category, 'form') !== false) {
                    $eventType = 'form_submit';
                } elseif (strpos($category, 'ai') !== false || strpos($category, 'assistant') !== false) {
                    $eventType = 'ai_chat';
                } else {
                    $eventType = 'custom';
                }
            }
            
            $activities[] = [
                'type' => $eventType,
                'description' => ucfirst($event->event_action) . ': ' . ($event->event_label ?: 'Unknown'),
                'created_at' => $event->occurred_at,
                'category' => $event->event_category ?? 'N/A',
                'url' => $event->page_url ?? 'N/A',
                'raw_type' => $eventType,
                'raw_created_at' => strtotime($event->occurred_at)
            ];
        }
        
        // Get total count (before filtering) - reset query first
        $totalPageViews = $this->pageViewModel->builder()->countAllResults(false);
        $totalEvents = $this->eventModel->builder()->countAllResults(false);
        $totalRecords = $totalPageViews + $totalEvents;
        
        // Sort activities
        $columns = ['type', 'description', 'created_at', 'category'];
        $orderColumnName = $columns[$orderColumn] ?? 'created_at';
        
        usort($activities, function($a, $b) use ($orderColumnName, $orderDir) {
            if ($orderColumnName === 'created_at') {
                $comparison = $a['raw_created_at'] - $b['raw_created_at'];
            } elseif ($orderColumnName === 'type') {
                $comparison = strcmp($a['raw_type'], $b['raw_type']);
            } elseif ($orderColumnName === 'description') {
                $comparison = strcmp($a['description'], $b['description']);
            } elseif ($orderColumnName === 'category') {
                $comparison = strcmp($a['category'], $b['category']);
            } else {
                $comparison = 0;
            }
            
            return $orderDir === 'ASC' ? $comparison : -$comparison;
        });
        
        // Get filtered count
        $filteredRecords = count($activities);
        
        // Apply pagination
        $paginatedActivities = array_slice($activities, $start, $length);
        
        // Format for DataTables (remove helper fields)
        $data = [];
        foreach ($paginatedActivities as $activity) {
            $data[] = [
                'type' => $activity['type'],
                'description' => $activity['description'],
                'created_at' => $activity['created_at'],
                'category' => $activity['category'],
                'url' => $activity['url']
            ];
        }
        
        return [
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    /**
     * Generate UUID for tracking records
     */
    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
