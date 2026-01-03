<!-- Critical Settings Warning -->
<div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-6">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-600"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-800">
                <strong>System Settings:</strong> Changes here affect core functionality. Proceed with caution.
                <button onclick="toggleWarningDetails()" class="ml-2 text-yellow-600 hover:text-yellow-800 underline text-xs">
                    Show Details
                </button>
            </p>
            <div id="warningDetails" class="hidden mt-2 text-xs text-yellow-700">
                Incorrect configurations may disrupt payments, emails, authentication, or cause system malfunctions.
            </div>
        </div>
    </div>
</div>

<!-- Settings Navigation Tabs -->
<div class="bg-white rounded-xl shadow-sm mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Settings Tabs">
            <?php 
            $baseClass = "border-b-2 py-4 px-1 text-sm font-medium transition-colors duration-200";
            $activeClass = $baseClass . " border-blue-500 text-blue-600";
            $inactiveClass = $baseClass . " border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300";
            ?>
            
            <a href="<?= base_url('auth/settings/social-media') ?>" class="<?= ($activeTab ?? '') === 'social-media' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="share-2" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'social-media' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                Social Media
            </a>
            <a href="<?= base_url('auth/settings/youtube') ?>" class="<?= ($activeTab ?? '') === 'youtube' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="play" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'youtube' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                YouTube Links
            </a>
            <a href="<?= base_url('auth/settings/partners') ?>" class="<?= ($activeTab ?? '') === 'partners' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="handshake" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'partners' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                Partners
            </a>
            <a href="<?= base_url('auth/settings/payments') ?>" class="<?= ($activeTab ?? '') === 'payments' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="credit-card" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'payments' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                Payments
            </a>
            <a href="<?= base_url('auth/settings/email') ?>" class="<?= ($activeTab ?? '') === 'email' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="mail" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'email' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                Email
            </a>
            <a href="<?= base_url('auth/settings/sms') ?>" class="<?= ($activeTab ?? '') === 'sms' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="message-square" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'sms' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                SMS
            </a>
            <a href="<?= base_url('auth/settings/google') ?>" class="<?= ($activeTab ?? '') === 'google' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="chrome" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'google' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                Google
            </a>
            <a href="<?= base_url('auth/settings/sitemap') ?>" class="<?= ($activeTab ?? '') === 'sitemap' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="sitemap" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'sitemap' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                Sitemap
            </a>
            <a href="<?= base_url('auth/settings/faqs') ?>" class="<?= ($activeTab ?? '') === 'faqs' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="help-circle" class="w-4 h-4 mr-2 inline <?= ($activeTab ?? '') === 'faqs' ? 'text-blue-600' : 'text-gray-400' ?>"></i>
                FAQs
            </a>
        </nav>
    </div>
</div>

<script>
function toggleWarningDetails() {
    const details = document.getElementById('warningDetails');
    const button = event.target;
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        button.textContent = 'Hide Details';
    } else {
        details.classList.add('hidden');
        button.textContent = 'Show Details';
    }
}
</script>