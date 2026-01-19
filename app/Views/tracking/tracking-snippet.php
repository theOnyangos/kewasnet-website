<!-- KEWASNET Activity Tracking Integration -->
<!-- Include this snippet in your main layout file (before closing </body> tag) -->

<!-- Load the tracking library -->
<script src="<?= base_url('assets/js/kewasnet-tracker.js') ?>" defer></script>

<!-- Optional: Custom tracking events -->
<script>
// Wait for tracker to be initialized before setting up event listeners
function initializeTrackingEvents() {
    // Check if tracker is available
    if (!window.kewasnetTracker && !window.trackEvent) {
        // Retry after a short delay if tracker not yet loaded
        setTimeout(initializeTrackingEvents, 100);
        return;
    }

    // Helper function to safely track events
    function safeTrackEvent(type, action, label, value, category) {
        if (window.kewasnetTracker && window.kewasnetTracker.hasAnalyticsConsent()) {
            window.kewasnetTracker.trackEvent(type, action, label, value, category);
        } else if (window.trackEvent) {
            window.trackEvent(type, action, label, value, category);
        }
    }

    // Track page category based on current URL
    const path = window.location.pathname;
    let pageCategory = 'General';
    
    if (path.includes('blog')) pageCategory = 'Blog';
    else if (path.includes('resources')) pageCategory = 'Resources';
    else if (path.includes('events')) pageCategory = 'Events';
    else if (path.includes('about')) pageCategory = 'About';
    else if (path.includes('contact')) pageCategory = 'Contact';
    else if (path.includes('news')) pageCategory = 'News';
    else if (path.includes('careers')) pageCategory = 'Careers';
    else if (path === '/' || path === '') pageCategory = 'Home';
    
    // Track newsletter signups
    document.querySelectorAll('[data-newsletter-form]').forEach(form => {
        form.addEventListener('submit', function() {
            if (window.kewasnetTracker) {
                window.kewasnetTracker.trackNewsletterSignup();
            }
        });
    });
    
    // Track contact form submissions
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function() {
            safeTrackEvent('form_submit', 'submit', 'Contact Form', null, 'Form');
        });
    }
    
    // Track event booking form submissions
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function() {
            safeTrackEvent('form_submit', 'submit', 'Event Booking Form', null, 'Form');
        });
    }
    
    // Track job application form submissions
    const jobApplicationForm = document.getElementById('jobApplicationForm');
    if (jobApplicationForm) {
        jobApplicationForm.addEventListener('submit', function() {
            safeTrackEvent('form_submit', 'submit', 'Job Application Form', null, 'Form');
        });
    }
    
    // Track social media clicks
    document.querySelectorAll('a[href*="facebook.com"], a[href*="twitter.com"], a[href*="linkedin.com"], a[href*="instagram.com"], a[href*="youtube.com"]').forEach(link => {
        link.addEventListener('click', function() {
            const platform = this.href.includes('facebook') ? 'Facebook' :
                           this.href.includes('twitter') ? 'Twitter' :
                           this.href.includes('linkedin') ? 'LinkedIn' :
                           this.href.includes('instagram') ? 'Instagram' :
                           this.href.includes('youtube') ? 'YouTube' : 'Social';
            
            safeTrackEvent('social_click', 'click', platform, this.href, 'Social Media');
        });
    });
    
    // Track external link clicks
    document.querySelectorAll('a[href^="http"]:not([href*="' + window.location.hostname + '"])').forEach(link => {
        link.addEventListener('click', function() {
            safeTrackEvent('external_link', 'click', this.textContent.trim(), this.href, 'External Link');
        });
    });
    
    // Track phone number clicks
    document.querySelectorAll('a[href^="tel:"]').forEach(link => {
        link.addEventListener('click', function() {
            safeTrackEvent('phone_click', 'click', this.href.replace('tel:', ''), null, 'Contact');
        });
    });
    
    // Track email clicks
    document.querySelectorAll('a[href^="mailto:"]').forEach(link => {
        link.addEventListener('click', function() {
            safeTrackEvent('email_click', 'click', this.href.replace('mailto:', ''), null, 'Contact');
        });
    });
    
    // Track video plays (if you have video elements)
    document.querySelectorAll('video').forEach(video => {
        video.addEventListener('play', function() {
            safeTrackEvent('video_play', 'play', this.src || 'Unknown Video', null, 'Media');
        });
    });
    
    // Track search form submissions
    document.querySelectorAll('form[role="search"], .search-form, #search-form').forEach(form => {
        form.addEventListener('submit', function() {
            const searchInput = this.querySelector('input[type="search"], input[name*="search"], input[name*="query"]');
            if (searchInput && searchInput.value) {
                if (window.kewasnetTracker) {
                    window.kewasnetTracker.trackSearch(searchInput.value.trim());
                } else if (window.trackSearch) {
                    window.trackSearch(searchInput.value.trim());
                }
            }
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTrackingEvents);
} else {
    // DOM already loaded, but wait a bit for tracker to initialize
    setTimeout(initializeTrackingEvents, 100);
}

// Global function to manually track custom events
window.trackCustomEvent = function(action, label, value, category) {
    if (window.trackEvent) {
        window.trackEvent('custom', action, label, value, category || 'Custom');
    }
};

// Global function to track user registrations
window.trackUserRegistration = function(type) {
    if (window.kewasnetTracker) {
        window.kewasnetTracker.trackRegistration(type || 'User Registration');
    }
};
</script>

<!-- Privacy Notice (only shows if tracking is active) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only show privacy notice if tracking is active
    if (window.kewasnetTracker && window.kewasnetTracker.hasAnalyticsConsent()) {
        // You can add a small privacy indicator here if needed
        console.log('KEWASNET Activity Tracking is active based on your cookie preferences');
    }
});
</script>
