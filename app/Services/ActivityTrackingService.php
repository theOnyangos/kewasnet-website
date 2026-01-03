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
        $sessionId = $this->session->session_id;
        
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
            $this->session->set('tracking_session_id', $this->sessionModel->getInsertID());
            
            return $result;
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
        if (!$this->hasAnalyticsConsent()) {
            return false;
        }

        $sessionId = $this->getTrackingSessionId();
        if (!$sessionId) {
            return false;
        }

        // Record page view
        $pageViewData = [
            'session_id' => $sessionId,
            'page_url' => $this->cleanUrl($pageUrl),
            'page_title' => $pageTitle,
            'page_category' => $pageCategory ?: $this->categorizeUrl($pageUrl),
            'viewed_at' => date('Y-m-d H:i:s')
        ];

        $pageViewId = $this->pageViewModel->insert($pageViewData);
        
        // Update session page count
        $this->incrementPageViews($sessionId);
        
        // Store current page view ID in session for later updates
        $this->session->set('current_page_view_id', $pageViewId);
        
        return $pageViewId;
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
        if (!$this->hasAnalyticsConsent()) {
            return false;
        }

        $sessionId = $this->getTrackingSessionId();
        if (!$sessionId) {
            return false;
        }

        return $this->eventModel->trackEvent(
            $sessionId,
            $eventType,
            $eventAction,
            $eventLabel,
            $eventValue,
            $eventCategory,
            $this->session->get('current_page_view_id')
        );
    }

    /**
     * End current session
     */
    public function endSession()
    {
        $sessionId = $this->session->session_id;
        return $this->sessionModel->endSession($sessionId);
    }

    /**
     * Get comprehensive analytics dashboard data
     */
    public function getDashboardData($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }

        return [
            'session_stats' => $this->sessionModel->getSessionStats($startDate, $endDate),
            'popular_pages' => $this->pageViewModel->getPopularPages(10, $startDate, $endDate),
            'views_by_category' => $this->pageViewModel->getViewsByCategory($startDate, $endDate),
            'device_breakdown' => $this->sessionModel->getSessionsByDevice($startDate, $endDate),
            'top_countries' => $this->sessionModel->getTopCountries(10, $startDate, $endDate),
            'event_stats' => $this->eventModel->getEventStats($startDate, $endDate),
            'top_clicks' => $this->eventModel->getTopClicks(10, $startDate, $endDate),
            'form_submissions' => $this->eventModel->getFormStats($startDate, $endDate),
            'downloads' => $this->eventModel->getDownloadStats($startDate, $endDate),
            'search_queries' => $this->eventModel->getSearchQueries(10, $startDate, $endDate),
            'new_registrations' => $this->eventModel->getNewRegistrations($startDate, $endDate)
        ];
    }

    /**
     * Check if user has given analytics consent
     */
    private function hasAnalyticsConsent()
    {
        $cookieConsent = $this->getCookieValue('cookieConsent');
        $analyticsConsent = $this->getCookieValue('analyticsCookies');
        
        return $cookieConsent === 'all' || $analyticsConsent === 'accepted';
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
     */
    private function detectDeviceType($userAgentString)
    {
        $userAgent = $this->request->getUserAgent();
        
        // Check for tablet first (more specific)
        $tabletKeywords = ['iPad', 'tablet', 'Tab', 'Kindle', 'PlayBook', 'Nexus 7', 'Nexus 10', 'GT-P', 'SM-T'];
        foreach ($tabletKeywords as $keyword) {
            if (stripos($userAgentString, $keyword) !== false) {
                return 'Tablet';
            }
        }
        
        // Then check for mobile
        if ($userAgent->isMobile()) {
            return 'Mobile';
        }
        
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
}
