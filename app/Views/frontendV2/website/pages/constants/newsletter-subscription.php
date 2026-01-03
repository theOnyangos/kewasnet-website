<!-- Newsletter Subscription Section -->
<section class="py-20 bg-gradient-to-r from-primary to-secondary text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-6"><?= esc($newsletterTitle ?? 'Subscribe to Our Newsletter') ?></h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
            <?= esc($newsletterDescription ?? 'Stay updated with our latest news, resources, and insights on water and sanitation initiatives.') ?>
        </p>
        
        <!-- Newsletter Subscription Form -->
        <form id="newsletterForm" class="max-w-xl mx-auto">
            <?= csrf_field() ?>
            <div class="flex flex-col sm:flex-row gap-4">
                <input 
                    type="email" 
                    id="newsletterEmail" 
                    name="email" 
                    placeholder="Enter your email address" 
                    class="flex-1 px-4 py-3 text-primary rounded-lg text-earth-800 outline-none border-none focus:ring-2 focus:ring-white">
                <button 
                    type="submit" 
                    id="subscribeBtn" 
                    class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-earth-100 hover:text-primary transition-colors flex items-center justify-center min-w-[140px]">
                    <span id="btnText">Subscribe</span>
                    <svg id="btnIcon" class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <!-- Loading spinner (hidden by default) -->
                    <svg id="loadingSpinner" class="ml-2 w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Status Messages -->
            <div id="messageContainer" class="mt-4 hidden">
                <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span id="successText"></span>
                    </div>
                </div>
                <div id="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span id="errorText"></span>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Privacy Notice -->
        <p class="text-sm mt-4 opacity-80">
            By subscribing, you agree to receive our newsletter and accept our 
            <a href="<?= base_url('terms-of-service') ?>" class="underline hover:no-underline">privacy policy</a>.
            You can unsubscribe at any time.
        </p>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterForm');
    const emailInput = document.getElementById('newsletterEmail');
    const subscribeBtn = document.getElementById('subscribeBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const messageContainer = document.getElementById('messageContainer');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const successText = document.getElementById('successText');
    const errorText = document.getElementById('errorText');

    // Form submission handler
    newsletterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = emailInput.value.trim();
        
        // Client-side validation
        if (!email) {
            showError('Please enter your email address.');
            return;
        }
        
        if (!isValidEmail(email)) {
            showError('Please enter a valid email address.');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        hideMessages();
        
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]').value;
            
            // Make AJAX request
            const response = await fetch('<?= base_url("newsletter/subscribe") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    email: email,
                    '<?= csrf_token() ?>': csrfToken
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('success', result.message || 'Thank you for subscribing to our newsletter!');
                emailInput.value = ''; // Clear the form
                
                // Optional: Analytics tracking
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'newsletter_subscription', {
                        'event_category': 'engagement',
                        'event_label': email
                    });
                }
            } else {
                showNotification('error', result.message || 'An error occurred. Please try again.');
            }
        } catch (error) {
            console.error('Newsletter subscription error:', error);
            showNotification('error', 'Network error. Please check your connection and try again.');
        } finally {
            setLoadingState(false);
        }
    });
    
    // Email input validation on blur
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && !isValidEmail(email)) {
            showError('Please enter a valid email address.');
        } else if (email) {
            hideMessages();
        }
    });
    
    // Clear error on input
    emailInput.addEventListener('input', function() {
        if (errorMessage.classList.contains('hidden') === false) {
            hideMessages();
        }
    });
    
    // Helper functions
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function setLoadingState(loading) {
        if (loading) {
            subscribeBtn.disabled = true;
            btnText.textContent = 'Subscribing...';
            btnIcon.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            subscribeBtn.classList.add('opacity-80', 'cursor-not-allowed');
        } else {
            subscribeBtn.disabled = false;
            btnText.textContent = 'Subscribe';
            btnIcon.classList.remove('hidden');
            loadingSpinner.classList.add('hidden');
            subscribeBtn.classList.remove('opacity-80', 'cursor-not-allowed');
        }
    }
    
    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        successMessage.classList.add('hidden');
        messageContainer.classList.remove('hidden');
    }
    
    function hideMessages() {
        messageContainer.classList.add('hidden');
        successMessage.classList.add('hidden');
        errorMessage.classList.add('hidden');
    }
    
    // Optional: Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Enter key when focused on email input
        if (e.key === 'Enter' && document.activeElement === emailInput) {
            e.preventDefault();
            newsletterForm.dispatchEvent(new Event('submit'));
        }
    });

    // Custom Notifications Function
    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === "success" ? "border-green-500" : "border-red-500"
        }`;

        notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="${
                        type === "success" ? "check-circle" : "x-circle"
                        }" class="w-5 h-5 ${
            type === "success" ? "text-green-500" : "text-red-500"
        }"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button type="button" class="inline-flex text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;

        document.body.appendChild(notification);
        lucide.createIcons();

        // Animate in
        setTimeout(() => {
            notification.classList.remove("translate-x-full");
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add("translate-x-full");
            setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            }, 300);
        }, 5000);
    }
});
</script>