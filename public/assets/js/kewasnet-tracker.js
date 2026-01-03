/**
 * KEWASNET Activity Tracking Library
 * Respects user cookie preferences and tracks site activity
 */

class KewasnetTracker {
    constructor() {
        this.baseUrl = window.location.origin;
        this.apiEndpoint = '/api/tracking';
        this.sessionId = null;
        this.currentPageViewId = null;
        this.pageStartTime = Date.now();
        this.lastScrollDepth = 0;
        this.maxScrollDepth = 0;
        this.isInitialized = false;
        this.eventQueue = [];
        this.isOnline = navigator.onLine;
        
        // Initialize tracking based on consent
        this.init();
    }

    /**
     * Initialize tracking
     */
    async init() {
        // Check consent first
        if (!this.hasAnalyticsConsent()) {
            console.log('KEWASNET Tracker: Analytics consent not given');
            return;
        }

        try {
            // Initialize session
            await this.initializeSession();
            
            // Track initial page view
            await this.trackCurrentPage();
            
            // Setup event listeners
            this.setupEventListeners();
            
            this.isInitialized = true;
            console.log('KEWASNET Tracker: Initialized successfully');
        } catch (error) {
            console.error('KEWASNET Tracker: Initialization failed', error);
        }
    }

    /**
     * Check if user has given analytics consent
     */
    hasAnalyticsConsent() {
        const cookieConsent = this.getCookie('cookieConsent');
        const analyticsConsent = this.getCookie('analyticsCookies');
        
        return cookieConsent === 'all' || analyticsConsent === 'accepted';
    }

    /**
     * Initialize tracking session
     */
    async initializeSession() {
        const analyticsConsent = this.hasAnalyticsConsent();
        const marketingConsent = this.hasMarketingConsent();
        
        try {
            const response = await this.makeRequest('/init-session', {
                analytics_consent: analyticsConsent,
                marketing_consent: marketingConsent
            });
            
            if (response.success) {
                this.sessionId = response.session_id;
            }
        } catch (error) {
            console.error('Failed to initialize session:', error);
        }
    }

    /**
     * Track current page view
     */
    async trackCurrentPage() {
        if (!this.hasAnalyticsConsent()) return;

        const pageData = {
            page_url: window.location.pathname,
            page_title: document.title,
            page_category: this.categorizeCurrentPage()
        };

        try {
            const response = await this.makeRequest('/track-page', pageData);
            if (response.success) {
                this.currentPageViewId = response.page_view_id;
            }
        } catch (error) {
            console.error('Failed to track page view:', error);
            this.queueEvent('page_view', pageData);
        }
    }

    /**
     * Update page view with time spent and scroll depth
     */
    async updatePageView(isExit = false) {
        if (!this.hasAnalyticsConsent() || !this.currentPageViewId) return;

        const timeOnPage = Math.floor((Date.now() - this.pageStartTime) / 1000);
        
        const updateData = {
            time_on_page: timeOnPage,
            scroll_depth: this.maxScrollDepth,
            is_exit: isExit
        };

        try {
            await this.makeRequest('/update-page', updateData);
        } catch (error) {
            console.error('Failed to update page view:', error);
            this.queueEvent('page_update', updateData);
        }
    }

    /**
     * Track custom event
     */
    async trackEvent(eventType, eventAction, eventLabel = null, eventValue = null, eventCategory = null) {
        if (!this.hasAnalyticsConsent()) return;

        const eventData = {
            event_type: eventType,
            event_action: eventAction,
            event_label: eventLabel,
            event_value: eventValue,
            event_category: eventCategory
        };

        try {
            await this.makeRequest('/track-event', eventData);
        } catch (error) {
            console.error('Failed to track event:', error);
            this.queueEvent('event', eventData);
        }
    }

    /**
     * Track click events
     */
    trackClick(element, label = null) {
        const elementInfo = this.getElementInfo(element);
        this.trackEvent('click', 'click', label || elementInfo.label, null, elementInfo.category);
    }

    /**
     * Track form submission
     */
    trackFormSubmission(formElement, formName = null) {
        const formInfo = this.getFormInfo(formElement);
        this.trackEvent('form_submit', 'submit', formName || formInfo.name, null, 'Form');
    }

    /**
     * Track file download
     */
    trackDownload(fileName, fileUrl = null) {
        this.trackEvent('download', 'download', fileName, fileUrl, 'Download');
    }

    /**
     * Track search
     */
    trackSearch(searchQuery, searchCategory = 'Site Search') {
        this.trackEvent('search', 'search', searchCategory, searchQuery, 'Search');
    }

    /**
     * Track user registration
     */
    trackRegistration(registrationType = 'General') {
        this.trackEvent('registration', 'register', registrationType, null, 'User');
    }

    /**
     * Track newsletter signup
     */
    trackNewsletterSignup() {
        this.trackEvent('newsletter', 'signup', 'Newsletter Subscription', null, 'Marketing');
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Scroll tracking
        let scrollTimer;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                this.updateScrollDepth();
            }, 100);
        });

        // Before page unload
        window.addEventListener('beforeunload', () => {
            this.updatePageView(true);
            this.processEventQueue();
        });

        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.updatePageView();
            }
        });

        // Automatic click tracking for certain elements
        document.addEventListener('click', (e) => {
            this.handleAutoClick(e);
        });

        // Form submission tracking
        document.addEventListener('submit', (e) => {
            this.handleFormSubmission(e);
        });

        // Online/offline status
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.processEventQueue();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
    }

    /**
     * Handle automatic click tracking
     */
    handleAutoClick(event) {
        const element = event.target;
        
        // Track clicks on buttons, links, and elements with data-track attribute
        if (element.tagName === 'BUTTON' || 
            element.tagName === 'A' || 
            element.hasAttribute('data-track')) {
            
            const label = element.textContent?.trim() || 
                         element.getAttribute('aria-label') || 
                         element.getAttribute('data-track') ||
                         element.className;
            
            if (label) {
                this.trackClick(element, label);
            }
        }

        // Track download links
        if (element.tagName === 'A' && element.href) {
            const url = new URL(element.href, window.location.origin);
            const fileName = url.pathname.split('/').pop();
            const isDownload = element.hasAttribute('download') || 
                              /\.(pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|txt)$/i.test(fileName);
            
            if (isDownload) {
                this.trackDownload(fileName, element.href);
            }
        }
    }

    /**
     * Handle form submission tracking
     */
    handleFormSubmission(event) {
        const form = event.target;
        const formName = form.getAttribute('name') || 
                        form.getAttribute('id') || 
                        form.className || 
                        'Unknown Form';
        
        this.trackFormSubmission(form, formName);
    }

    /**
     * Update scroll depth
     */
    updateScrollDepth() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = docHeight > 0 ? Math.round((scrollTop / docHeight) * 100) : 0;
        
        this.lastScrollDepth = scrollPercent;
        this.maxScrollDepth = Math.max(this.maxScrollDepth, scrollPercent);
    }

    /**
     * Get element information for tracking
     */
    getElementInfo(element) {
        return {
            label: element.textContent?.trim() || element.getAttribute('aria-label') || element.id || element.className,
            category: element.getAttribute('data-category') || this.categorizeElement(element)
        };
    }

    /**
     * Get form information
     */
    getFormInfo(form) {
        return {
            name: form.getAttribute('name') || form.getAttribute('id') || 'Unknown Form',
            action: form.getAttribute('action') || window.location.pathname
        };
    }

    /**
     * Categorize current page
     */
    categorizeCurrentPage() {
        const path = window.location.pathname;
        
        if (path.includes('/blog')) return 'Blog';
        if (path.includes('/resources')) return 'Resources';
        if (path.includes('/events')) return 'Events';
        if (path.includes('/about')) return 'About';
        if (path.includes('/contact')) return 'Contact';
        if (path.includes('/news')) return 'News';
        if (path.includes('/careers')) return 'Careers';
        if (path === '/' || path === '') return 'Home';
        
        return 'Other';
    }

    /**
     * Categorize element for tracking
     */
    categorizeElement(element) {
        if (element.tagName === 'A') return 'Link';
        if (element.tagName === 'BUTTON') return 'Button';
        if (element.getAttribute('role') === 'button') return 'Button';
        
        return 'Element';
    }

    /**
     * Check marketing consent
     */
    hasMarketingConsent() {
        const cookieConsent = this.getCookie('cookieConsent');
        const marketingConsent = this.getCookie('marketingCookies');
        
        return cookieConsent === 'all' || marketingConsent === 'accepted';
    }

    /**
     * Get cookie value
     */
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    /**
     * Make API request
     */
    async makeRequest(endpoint, data) {
        const response = await fetch(this.baseUrl + this.apiEndpoint + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Queue event for later processing (offline support)
     */
    queueEvent(type, data) {
        this.eventQueue.push({
            type: type,
            data: data,
            timestamp: Date.now()
        });

        // Limit queue size
        if (this.eventQueue.length > 100) {
            this.eventQueue = this.eventQueue.slice(-50);
        }
    }

    /**
     * Process queued events
     */
    async processEventQueue() {
        if (!this.isOnline || this.eventQueue.length === 0) return;

        try {
            const events = this.eventQueue.splice(0, 10); // Process in batches
            
            await this.makeRequest('/batch-track', { events: events });
        } catch (error) {
            console.error('Failed to process event queue:', error);
        }
    }

    /**
     * Destroy tracker (for privacy compliance)
     */
    destroy() {
        this.updatePageView(true);
        this.isInitialized = false;
        // Remove event listeners if needed
    }
}

// Initialize tracker when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.kewasnetTracker = new KewasnetTracker();
    });
} else {
    window.kewasnetTracker = new KewasnetTracker();
}

// Expose tracking functions globally for manual tracking
window.trackEvent = (type, action, label, value, category) => {
    if (window.kewasnetTracker) {
        window.kewasnetTracker.trackEvent(type, action, label, value, category);
    }
};

window.trackClick = (element, label) => {
    if (window.kewasnetTracker) {
        window.kewasnetTracker.trackClick(element, label);
    }
};

window.trackDownload = (fileName, fileUrl) => {
    if (window.kewasnetTracker) {
        window.kewasnetTracker.trackDownload(fileName, fileUrl);
    }
};

window.trackSearch = (query, category) => {
    if (window.kewasnetTracker) {
        window.kewasnetTracker.trackSearch(query, category);
    }
};
