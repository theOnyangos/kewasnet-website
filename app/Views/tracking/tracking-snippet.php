<!-- KEWASNET Activity Tracking Integration -->
<!-- Include this snippet in your main layout file (before closing </body> tag) -->

<!-- Load the tracking library -->
<script src="<?= base_url('assets/js/kewasnet-tracker.js') ?>" defer></script>

<!-- Optional: Custom tracking events -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Track social media clicks
    document.querySelectorAll('a[href*="facebook.com"], a[href*="twitter.com"], a[href*="linkedin.com"], a[href*="instagram.com"], a[href*="youtube.com"]').forEach(link => {
        link.addEventListener('click', function() {
            const platform = this.href.includes('facebook') ? 'Facebook' :
                           this.href.includes('twitter') ? 'Twitter' :
                           this.href.includes('linkedin') ? 'LinkedIn' :
                           this.href.includes('instagram') ? 'Instagram' :
                           this.href.includes('youtube') ? 'YouTube' : 'Social';
            
            if (window.trackEvent) {
                window.trackEvent('social_click', 'click', platform, this.href, 'Social Media');
            }
        });
    });
    
    // Track external link clicks
    document.querySelectorAll('a[href^="http"]:not([href*="' + window.location.hostname + '"])').forEach(link => {
        link.addEventListener('click', function() {
            if (window.trackEvent) {
                window.trackEvent('external_link', 'click', this.textContent.trim(), this.href, 'External Link');
            }
        });
    });
    
    // Track phone number clicks
    document.querySelectorAll('a[href^="tel:"]').forEach(link => {
        link.addEventListener('click', function() {
            if (window.trackEvent) {
                window.trackEvent('phone_click', 'click', this.href.replace('tel:', ''), null, 'Contact');
            }
        });
    });
    
    // Track email clicks
    document.querySelectorAll('a[href^="mailto:"]').forEach(link => {
        link.addEventListener('click', function() {
            if (window.trackEvent) {
                window.trackEvent('email_click', 'click', this.href.replace('mailto:', ''), null, 'Contact');
            }
        });
    });
    
    // Track video plays (if you have video elements)
    document.querySelectorAll('video').forEach(video => {
        video.addEventListener('play', function() {
            if (window.trackEvent) {
                window.trackEvent('video_play', 'play', this.src || 'Unknown Video', null, 'Media');
            }
        });
    });
    
    // Track search form submissions
    document.querySelectorAll('form[role="search"], .search-form, #search-form').forEach(form => {
        form.addEventListener('submit', function() {
            const searchInput = this.querySelector('input[type="search"], input[name*="search"], input[name*="query"]');
            if (searchInput && searchInput.value && window.trackSearch) {
                window.trackSearch(searchInput.value.trim());
            }
        });
    });
});

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
