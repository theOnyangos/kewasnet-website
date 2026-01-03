<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
SMS Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

        <!-- SMS Panel -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">SMS Configuration</h2>
                    <p class="mt-1 text-sm text-gray-500">Configure SMS gateway for sending notifications</p>
                </div>
                <button onclick="saveForm('smsForm', '<?= base_url('auth/settings/saveSmsSettings') ?>', 'SMS settings saved successfully!')" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2 inline z-10"></i>
                    <span>Save Settings</span>
                </button>
            </div>
            
            <form id="smsForm" class="space-y-6">
                <!-- SMS Provider Selection -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-3">
                        <i data-lucide="message-square" class="w-5 h-5 text-green-600 mr-2"></i>
                        <h3 class="text-sm font-medium text-green-800">SMS Gateway Configuration</h3>
                    </div>
                    <p class="text-sm text-green-700">Select and configure your SMS provider for sending notifications</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMS Provider</label>
                        <select name="sms_provider" id="smsProvider" onchange="toggleProviderSettings()" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="africastalking">Africa's Talking</option>
                            <option value="twilio">Twilio</option>
                            <option value="infobip">Infobip</option>
                            <option value="custom">Custom API</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                        <select name="environment" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="sandbox">Sandbox/Test</option>
                            <option value="live">Live/Production</option>
                        </select>
                    </div>
                </div>
                
                <!-- Africa's Talking Settings -->
                <div id="africastalking-settings" class="provider-settings">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Africa's Talking Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" name="at_username" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your AT username">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                            <div class="relative">
                                <input type="password" name="at_api_key" id="atApiKey" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your API Key">
                                <button type="button" onclick="togglePassword('atApiKey')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sender ID</label>
                            <input type="text" name="at_sender_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="KEWASNET">
                        </div>
                    </div>
                </div>
                
                <!-- Twilio Settings -->
                <div id="twilio-settings" class="provider-settings hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Twilio Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account SID</label>
                            <input type="text" name="twilio_sid" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your Account SID">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Auth Token</label>
                            <div class="relative">
                                <input type="password" name="twilio_token" id="twilioToken" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your Auth Token">
                                <button type="button" onclick="togglePassword('twilioToken')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Number</label>
                            <input type="tel" name="twilio_from" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+1234567890">
                        </div>
                    </div>
                </div>
                
                <!-- Custom API Settings -->
                <div id="custom-settings" class="provider-settings hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Custom API Configuration</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Endpoint URL</label>
                            <input type="url" name="custom_api_url" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://api.yourprovider.com/sms">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">API Key Header</label>
                                <input type="text" name="custom_api_key_header" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="X-API-Key">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">API Key Value</label>
                                <div class="relative">
                                    <input type="password" name="custom_api_key" id="customApiKey" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Your API Key">
                                    <button type="button" onclick="togglePassword('customApiKey')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- General SMS Settings -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Country Code</label>
                            <select name="default_country_code" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="+254">+254 (Kenya)</option>
                                <option value="+256">+256 (Uganda)</option>
                                <option value="+255">+255 (Tanzania)</option>
                                <option value="+250">+250 (Rwanda)</option>
                                <option value="+1">+1 (US/Canada)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message Length Limit</label>
                            <select name="message_length_limit" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="160">160 characters (Single SMS)</option>
                                <option value="320">320 characters (2 SMS)</option>
                                <option value="480">480 characters (3 SMS)</option>
                                <option value="unlimited">Unlimited (Auto-split)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="sms_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Enable SMS notifications</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="log_sms" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Log all SMS messages</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="delivery_reports" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Request delivery reports</span>
                        </label>
                    </div>
                </div>
                
                <!-- Test SMS Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Test SMS Configuration</h3>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2"></i>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Test Your Configuration</h4>
                                <p class="text-sm text-yellow-700 mt-1">Send a test SMS to verify your SMS gateway settings</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="tel" id="testPhone" placeholder="+254700000000" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="button" onclick="sendTestSms()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i data-lucide="send" class="w-4 h-4 mr-2 inline"></i>
                            Send Test SMS
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
<?php $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
    // Toggle provider-specific settings
    function toggleProviderSettings() {
        const provider = document.getElementById('smsProvider').value;
        
        // Hide all provider settings
        document.querySelectorAll('.provider-settings').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Show selected provider settings
        document.getElementById(provider + '-settings').classList.remove('hidden');
    }
    
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
    
    // Send test SMS
    function sendTestSms() {
        const testPhone = document.getElementById('testPhone').value;
        if (!testPhone) {
            showToast('Please enter a test phone number', 'error');
            return;
        }
        
        const formData = new FormData(document.getElementById('smsForm'));
        formData.append('test_phone', testPhone);
        
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="w-4 h-4 mr-2 inline animate-spin" data-lucide="loader"></i> Sending...';
        button.disabled = true;
        
        $.ajax({
            url: '<?= base_url("auth/settings/sendTestSms") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('Test SMS sent successfully!', 'success');
                } else {
                    showToast(response.message || 'Failed to send test SMS', 'error');
                }
            },
            error: function() {
                showToast('An error occurred while sending test SMS', 'error');
            },
            complete: function() {
                button.innerHTML = originalText;
                button.disabled = false;
                lucide.createIcons();
            }
        });
    }
    
    // Load existing SMS settings
    $(document).ready(function() {
        $.ajax({
            url: '<?= base_url("auth/settings/getSmsSettings") ?>',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const form = document.getElementById('smsForm');
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
                    
                    // Show appropriate provider settings
                    toggleProviderSettings();
                }
            },
            error: function() {
                showToast('Failed to load SMS settings', 'error');
            }
        });
    });
</script>
<?php $this->endSection(); ?>
