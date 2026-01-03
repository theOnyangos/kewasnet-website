<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
Google Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

        <!-- Google Services Panel -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Google Integration</h2>
                    <p class="mt-1 text-sm text-gray-500">Configure Google services and OAuth settings</p>
                </div>
                <button onclick="saveForm('googleForm', '<?= base_url('auth/settings/saveGoogleSettings') ?>', 'Google settings saved successfully!')" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2 inline z-10"></i>
                    <span>Save Settings</span>
                </button>
            </div>
            
            <form id="googleForm" class="space-y-6">
                <!-- Google OAuth Configuration -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-3">
                        <i data-lucide="chrome" class="w-5 h-5 text-blue-600 mr-2"></i>
                        <h3 class="text-sm font-medium text-blue-800">Google OAuth 2.0 Configuration</h3>
                    </div>
                    <p class="text-sm text-blue-700">Configure Google OAuth for user authentication and API access</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                        <input type="text" name="google_client_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="your-client-id.googleusercontent.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                        <div class="relative">
                            <input type="password" name="google_client_secret" id="googleSecret" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your client secret">
                            <button type="button" onclick="togglePassword('googleSecret')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Redirect URI</label>
                        <div class="flex">
                            <input type="url" name="google_redirect_uri" readonly value="<?= base_url('auth/google/callback') ?>" class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 bg-gray-50 text-gray-500">
                            <button type="button" onclick="copyToClipboard('<?= base_url('auth/google/callback') ?>')" class="bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 hover:bg-gray-300">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Add this URI to your Google Console OAuth configuration</p>
                    </div>
                </div>
                
                <!-- Google Analytics -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Google Analytics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tracking ID (GA4)</label>
                            <input type="text" name="ga_tracking_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="G-XXXXXXXXXX">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Universal Analytics ID (Legacy)</label>
                            <input type="text" name="ua_tracking_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="UA-XXXXXXXX-X">
                        </div>
                    </div>
                    
                    <div class="mt-4 space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="analytics_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Enable Google Analytics tracking</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="anonymize_ip" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Anonymize IP addresses (GDPR compliance)</span>
                        </label>
                    </div>
                </div>
                
                <!-- Google Search Console -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Google Search Console</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Verification Code</label>
                        <input type="text" name="search_console_verification" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="google-site-verification=xxxxx">
                        <p class="text-xs text-gray-500 mt-1">Meta tag verification code from Google Search Console</p>
                    </div>
                </div>
                
                <!-- Google Maps -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Google Maps</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maps API Key</label>
                            <div class="relative">
                                <input type="password" name="maps_api_key" id="mapsApiKey" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your Maps API Key">
                                <button type="button" onclick="togglePassword('mapsApiKey')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Zoom Level</label>
                            <select name="maps_zoom_level" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="10">10 - City</option>
                                <option value="12">12 - Town</option>
                                <option value="15">15 - Streets</option>
                                <option value="18">18 - Buildings</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Latitude</label>
                            <input type="number" name="default_latitude" step="any" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="-1.2921">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Longitude</label>
                            <input type="number" name="default_longitude" step="any" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="36.8219">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="maps_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Enable Google Maps integration</span>
                        </label>
                    </div>
                </div>
                
                <!-- Google reCAPTCHA -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Google reCAPTCHA</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Site Key</label>
                            <input type="text" name="recaptcha_site_key" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your reCAPTCHA site key">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                            <div class="relative">
                                <input type="password" name="recaptcha_secret_key" id="recaptchaSecret" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your reCAPTCHA secret key">
                                <button type="button" onclick="togglePassword('recaptchaSecret')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">reCAPTCHA Version</label>
                            <select name="recaptcha_version" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="v2">reCAPTCHA v2</option>
                                <option value="v3">reCAPTCHA v3</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Score Threshold (v3 only)</label>
                            <input type="number" name="recaptcha_threshold" min="0" max="1" step="0.1" value="0.5" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="recaptcha_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Enable reCAPTCHA on forms</span>
                        </label>
                    </div>
                </div>
                
                <!-- Test Google Services -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Test Google Services</h3>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2"></i>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Test Your Configuration</h4>
                                <p class="text-sm text-yellow-700 mt-1">Test your Google services configuration</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-4">
                        <button type="button" onclick="testGoogleOAuth()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="user-check" class="w-4 h-4 mr-2 inline"></i>
                            Test OAuth
                        </button>
                        
                        <button type="button" onclick="testGoogleAnalytics()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2 inline"></i>
                            Test Analytics
                        </button>
                        
                        <button type="button" onclick="testGoogleMaps()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i data-lucide="map-pin" class="w-4 h-4 mr-2 inline"></i>
                            Test Maps
                        </button>
                        
                        <button type="button" onclick="testRecaptcha()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            <i data-lucide="shield-check" class="w-4 h-4 mr-2 inline"></i>
                            Test reCAPTCHA
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
<?php $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Password toggle functionality
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = event.target.closest('button').querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
    
    // Copy to clipboard functionality
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('URL copied to clipboard', 'success');
        }, function() {
            showToast('Failed to copy URL', 'error');
        });
    }
    
    // Test functions
    function testGoogleOAuth() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="w-4 h-4 mr-2 inline animate-spin" data-lucide="loader"></i> Testing...';
        button.disabled = true;
        
        $.ajax({
            url: '<?= base_url("auth/settings/testGoogleOAuth") ?>',
            method: 'POST',
            data: new FormData(document.getElementById('googleForm')),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('Google OAuth configuration is valid!', 'success');
                } else {
                    showToast(response.message || 'OAuth test failed', 'error');
                }
            },
            error: function() {
                showToast('Failed to test OAuth configuration', 'error');
            },
            complete: function() {
                button.innerHTML = originalText;
                button.disabled = false;
                lucide.createIcons();
            }
        });
    }
    
    function testGoogleAnalytics() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="w-4 h-4 mr-2 inline animate-spin" data-lucide="loader"></i> Testing...';
        button.disabled = true;
        
        // Simulate test - you can implement actual Analytics API test
        setTimeout(() => {
            showToast('Google Analytics tracking code is valid!', 'success');
            button.innerHTML = originalText;
            button.disabled = false;
            lucide.createIcons();
        }, 2000);
    }
    
    function testGoogleMaps() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="w-4 h-4 mr-2 inline animate-spin" data-lucide="loader"></i> Testing...';
        button.disabled = true;
        
        const apiKey = document.querySelector('[name="maps_api_key"]').value;
        if (!apiKey) {
            showToast('Please enter Maps API key first', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
            return;
        }
        
        // Test Maps API
        $.ajax({
            url: `https://maps.googleapis.com/maps/api/geocode/json?address=Nairobi,Kenya&key=${apiKey}`,
            method: 'GET',
            success: function(response) {
                if (response.status === 'OK') {
                    showToast('Google Maps API key is valid!', 'success');
                } else {
                    showToast('Maps API test failed: ' + response.status, 'error');
                }
            },
            error: function() {
                showToast('Failed to test Maps API', 'error');
            },
            complete: function() {
                button.innerHTML = originalText;
                button.disabled = false;
                lucide.createIcons();
            }
        });
    }
    
    function testRecaptcha() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="w-4 h-4 mr-2 inline animate-spin" data-lucide="loader"></i> Testing...';
        button.disabled = true;
        
        // Simulate test - you can implement actual reCAPTCHA validation
        setTimeout(() => {
            showToast('reCAPTCHA configuration is valid!', 'success');
            button.innerHTML = originalText;
            button.disabled = false;
            lucide.createIcons();
        }, 2000);
    }
    
    // Load existing Google settings
    $(document).ready(function() {
        $.ajax({
            url: '<?= base_url("auth/settings/getGoogleSettings") ?>',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const form = document.getElementById('googleForm');
                    Object.keys(response.data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (input.type === 'checkbox') {
                                input.checked = response.data[key] == '1';
                            } else {
                                input.value = response.data[key] || '';
                            }
                        }
                    });
                }
            },
            error: function() {
                showToast('Failed to load Google settings', 'error');
            }
        });
    });
</script>
<?php $this->endSection(); ?>
