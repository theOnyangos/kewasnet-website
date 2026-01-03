<div class="cookie-consent-modal" id="cookieConsent">
    <div class="consent-content">
        <div class="">
            <i data-lucide="cookie" class="w-10 h-10 text-primary"></i>
        </div>
        <div class="consent-text">
            <h3 class="consent-title">We value your privacy</h3>
            <p>KEWASNET uses cookies to enhance your browsing experience, analyze site traffic, and personalize content. By clicking "Accept All", you consent to our use of cookies.</p>
        </div>
        <div class="consent-buttons">
            <button class="consent-btn preferences-btn" id="preferencesBtn">Preferences</button>
            <button class="consent-btn accept-all-btn" id="acceptAllBtn">Accept All</button>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the cookie consent modal
        const cookieConsent = document.getElementById('cookieConsent');
        
        // Check if user has already made a choice
        if (!getCookie('cookieConsent')) {
            // Animate the cookie consent modal from the bottom
            gsap.to(cookieConsent, {
                duration: 0.8,
                y: 0,
                ease: "power3.out",
                delay: 0.5
            });
        }
        
        // Accept All button
        document.getElementById('acceptAllBtn').addEventListener('click', function() {
            setCookie('cookieConsent', 'all', 365);
            gsap.to(cookieConsent, {
                duration: 0.5,
                y: "100%",
                ease: "power2.in"
            });
        });
        
        // Preferences button
        document.getElementById('preferencesBtn').addEventListener('click', function() {
            // Set a cookie to remember user clicked preferences
            setCookie('cookieConsentAction', 'preferences', 1);
            
            // Close the banner with animation
            gsap.to(cookieConsent, {
                duration: 0.5,
                y: "100%",
                ease: "power2.in",
                onComplete: function() {
                    // Navigate to cookies page after animation completes
                    window.location.href = '<?= base_url("cookies") ?>';
                }
            });
        });
        
        // Save preferences button
        const savePrefsBtn = document.querySelector('.btn-secondary');
        if (savePrefsBtn) {
            savePrefsBtn.addEventListener('click', function() {
                // Get the state of each toggle
                const analyticsChecked = document.querySelector('.cookie-types .toggle-switch:nth-child(2) input').checked;
                const marketingChecked = document.querySelector('.cookie-types .toggle-switch:nth-child(3) input').checked;
                
                // Save preferences
                setCookie('cookieConsent', 'custom', 365);
                setCookie('analyticsCookies', analyticsChecked ? 'accepted' : 'rejected', 365);
                setCookie('marketingCookies', marketingChecked ? 'accepted' : 'rejected', 365);
                
                // Show confirmation
                alert('Your cookie preferences have been saved successfully.');
            });
        }
        
        // Accept all from the main section
        const acceptAllBtn = document.querySelector('.btn-primary');
        if (acceptAllBtn) {
            acceptAllBtn.addEventListener('click', function() {
                setCookie('cookieConsent', 'all', 365);
                alert('All cookies have been accepted.');
            });
        }
        
        // Cookie functions
        function setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }
        
        function getCookie(name) {
            const cookieName = name + "=";
            const decodedCookie = decodeURIComponent(document.cookie);
            const cookieArray = decodedCookie.split(';');
            
            for(let i = 0; i < cookieArray.length; i++) {
                let cookie = cookieArray[i];
                while (cookie.charAt(0) == ' ') {
                    cookie = cookie.substring(1);
                }
                if (cookie.indexOf(cookieName) == 0) {
                    return cookie.substring(cookieName.length, cookie.length);
                }
            }
            return "";
        }
    });
</script>
<?= $this->endSection() ?>