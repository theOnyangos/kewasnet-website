<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
Payment Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

        <!-- Payments Panel -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Payment Gateway Settings</h2>
                    <p class="mt-1 text-sm text-gray-500">Configure your payment processing systems</p>
                </div>
                <button onclick="saveAllPaymentSettings()" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2 inline z-10"></i>
                    <span>Save All</span>
                </button>
            </div>
            
            <!-- Payment Gateway Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8" aria-label="Payment Tabs">
                    <button onclick="showPaymentTab('paystack')" class="payment-tab active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                        <img src="<?= base_url('paystack-logo.png') ?>" alt="Paystack" class="w-5 h-5 mr-2 inline" onerror="this.style.display='none'">
                        Paystack
                    </button>
                    <button onclick="showPaymentTab('mpesa')" class="payment-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <img src="<?= base_url('mpesa-logo.png') ?>" alt="M-Pesa" class="w-5 h-5 mr-2 inline" onerror="this.style.display='none'">
                        M-Pesa
                    </button>
                </nav>
            </div>
            
            <!-- Paystack Settings -->
            <div id="paystack-settings" class="payment-section">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i data-lucide="info" class="w-5 h-5 text-green-600 mr-2"></i>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">Paystack Configuration</h4>
                            <p class="text-sm text-green-700 mt-1">Configure your Paystack payment gateway for online transactions</p>
                        </div>
                    </div>
                </div>
                
                <form id="paystackForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Public Key</label>
                            <div class="relative">
                                <input type="password" name="public_key" id="paystackPublic" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-20 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="pk_test_..." readonly>
                                <div class="!absolute !inset-y-0 !right-0 flex items-center z-10">
                                    <button type="button" onclick="requestPasswordForField('paystackPublic', 'public_key')" class="px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded mr-1 hover:bg-blue-200">
                                        View
                                    </button>
                                    <button type="button" onclick="togglePassword('paystackPublic')" class="pr-3 flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click "View" to reveal sensitive data</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                            <div class="relative">
                                <input type="password" name="secret_key" id="paystackSecret" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-20 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="sk_test_..." readonly>
                                <div class="!absolute !inset-y-0 !right-0 flex items-center z-10">
                                    <button type="button" onclick="requestPasswordForField('paystackSecret', 'secret_key')" class="px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded mr-1 hover:bg-blue-200">
                                        View
                                    </button>
                                    <button type="button" onclick="togglePassword('paystackSecret')" class="pr-3 flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click "View" to reveal sensitive data</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                            <select name="environment" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="test">Test Mode</option>
                                <option value="live">Live Mode</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <select name="currency" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="KES">KES - Kenyan Shilling</option>
                                <option value="NGN">NGN - Nigerian Naira</option>
                                <option value="GHS">GHS - Ghanaian Cedi</option>
                                <option value="USD">USD - US Dollar</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                            <div class="flex">
                                <input type="url" name="webhook_url" readonly value="<?= base_url('webhooks/paystack') ?>" class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 bg-gray-50 text-gray-500">
                                <button type="button" onclick="copyToClipboard('<?= base_url('webhooks/paystack') ?>')" class="bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 hover:bg-gray-300">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Copy this URL to your Paystack dashboard webhook settings</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="enabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Enable Paystack</span>
                            </label>
                            
                            <button type="button" onclick="testPaystackConnection()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i data-lucide="wifi" class="w-4 h-4 mr-1 inline"></i>
                                Test Connection
                            </button>
                        </div>
                        
                        <button type="button" onclick="savePaystackSettings()" class="bg-primary hover:bg-primaryShades-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors">
                            <i data-lucide="save" class="w-4 h-4 mr-1 inline"></i>
                            Save Paystack
                        </button>
                    </div>
                    
                    <!-- CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    
                    <!-- CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                </form>
            </div>
            
            <!-- M-Pesa Settings -->
            <div id="mpesa-settings" class="payment-section hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i data-lucide="info" class="w-5 h-5 text-green-600 mr-2"></i>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">M-Pesa Configuration</h4>
                            <p class="text-sm text-green-700 mt-1">Configure your M-Pesa STK Push for mobile payments</p>
                        </div>
                    </div>
                </div>
                
                <form id="mpesaForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Consumer Key</label>
                            <div class="relative">
                                <input type="password" name="consumer_key" id="mpesaConsumerKey" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-20 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Consumer Key" readonly>
                                <div class="!absolute !inset-y-0 !right-0 flex items-center">
                                    <button type="button" onclick="requestPasswordForField('mpesaConsumerKey', 'consumer_key')" class="px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded mr-1 hover:bg-blue-200">
                                        View
                                    </button>
                                    <button type="button" onclick="togglePassword('mpesaConsumerKey')" class="pr-3 flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click "View" to reveal sensitive data</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Consumer Secret</label>
                            <div class="relative">
                                <input type="password" name="consumer_secret" id="mpesaSecret" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-20 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Consumer Secret" readonly>
                                <div class="!absolute !inset-y-0 !right-0 flex items-center">
                                    <button type="button" onclick="requestPasswordForField('mpesaSecret', 'consumer_secret')" class="px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded mr-1 hover:bg-blue-200">
                                        View
                                    </button>
                                    <button type="button" onclick="togglePassword('mpesaSecret')" class="pr-3 flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click "View" to reveal sensitive data</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Short Code</label>
                            <input type="text" name="business_short_code" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="174379">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Passkey</label>
                            <div class="relative">
                                <input type="password" name="pass_key" id="mpesaPasskey" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-20 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Lipa Na M-Pesa Passkey" readonly>
                                <div class="!absolute !inset-y-0 !right-0 flex items-center">
                                    <button type="button" onclick="requestPasswordForField('mpesaPasskey', 'pass_key')" class="px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded mr-1 hover:bg-blue-200">
                                        View
                                    </button>
                                    <button type="button" onclick="togglePassword('mpesaPasskey')" class="pr-3 flex items-center">
                                        <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Click "View" to reveal sensitive data</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                            <select name="environment" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="sandbox">Sandbox</option>
                                <option value="live">Live</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                            <select name="transaction_type" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="CustomerPayBillOnline">Pay Bill</option>
                                <option value="CustomerBuyGoodsOnline">Buy Goods</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Callback URL</label>
                            <div class="flex">
                                <input type="url" name="callback_url" readonly value="<?= base_url('webhooks/mpesa') ?>" class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 bg-gray-50 text-gray-500">
                                <button type="button" onclick="copyToClipboard('<?= base_url('webhooks/mpesa') ?>')" class="bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 hover:bg-gray-300">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Use this URL in your M-Pesa application callback settings</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="enabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Enable M-Pesa</span>
                            </label>
                            
                            <button type="button" onclick="testMpesaConnection()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i data-lucide="wifi" class="w-4 h-4 mr-1 inline"></i>
                                Test Connection
                            </button>
                        </div>
                        
                        <button type="button" onclick="saveMpesaSettings()" class="bg-primary hover:bg-primaryShades-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors">
                            <i data-lucide="save" class="w-4 h-4 mr-1 inline"></i>
                            Save M-Pesa
                        </button>
                    </div>
                    
                    <!-- CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    
                    <!-- CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                </form>
            </div>
        </div>
        
        <!-- Password Verification Modal -->
        <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Security Verification</h3>
                        <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i data-lucide="shield-alert" class="w-5 h-5 text-yellow-600 mr-2"></i>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Sensitive Data Access</h4>
                                <p class="text-sm text-yellow-700 mt-1">Enter your account password to view sensitive payment credentials</p>
                            </div>
                        </div>
                    </div>
                    
                    <form id="passwordVerificationForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Password</label>
                            <div class="relative">
                                <input type="password" id="verificationPassword" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your password" required>
                                <button type="button" onclick="togglePassword('verificationPassword')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <button type="button" onclick="closePasswordModal()" class="bg-primaryShades-100 text-primary py-2 px-4 rounded-lg hover:bg-primaryShades-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-primaryShades-700 transition-colors">
                                <i data-lucide="unlock" class="w-4 h-4 mr-1 inline"></i>
                                Verify & Show
                            </button>
                        </div>
                        
                        <!-- CSRF Token -->
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    </form>
                </div>
            </div>
        </div>
    </main>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // Global variables for password verification
    let currentFieldId = null;
    let currentFieldName = null;
    let sensitiveData = {};
    
    // Password verification functions
    function requestPasswordForField(fieldId, fieldName) {
        currentFieldId = fieldId;
        currentFieldName = fieldName;
        document.getElementById('passwordModal').classList.remove('hidden');
        document.getElementById('verificationPassword').focus();
    }
    
    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
        document.getElementById('verificationPassword').value = '';
        currentFieldId = null;
        currentFieldName = null;
    }
    
    // Handle password verification form
    $('#passwordVerificationForm').on('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('verificationPassword').value;
        const formData = new FormData();
        formData.append('password', password);
        formData.append('field_name', currentFieldName);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        $.ajax({
            url: '<?= base_url("auth/settings/verifyPasswordForSensitiveData") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Password verification response:', response);
                if (response.success) {
                    console.log('Response data:', response.data);
                    // Store the sensitive data
                    sensitiveData[currentFieldName] = response.data;
                    
                    // Populate the field
                    const field = document.getElementById(currentFieldId);
                    field.value = response.data;
                    field.readOnly = false;
                    field.classList.remove('bg-gray-50');
                    field.classList.add('bg-white');
                    
                    // Update the button
                    const viewButton = field.parentElement.querySelector('button[onclick*="requestPasswordForField"]');
                    viewButton.textContent = 'Hide';
                    viewButton.onclick = function() { hideSensitiveField(currentFieldId, currentFieldName); };
                    viewButton.classList.remove('bg-blue-100', 'text-blue-600');
                    viewButton.classList.add('bg-red-100', 'text-red-600');
                    
                    closePasswordModal();
                    showNotification('success', 'Sensitive data revealed successfully');
                } else {
                    console.log('Password verification failed:', response.message);
                    showNotification('error', response.message || 'Invalid password');
                }
            },
            error: function(xhr, status, error) {
                console.log('Password verification error:', {xhr, status, error});
                console.log('Response text:', xhr.responseText);
                showNotification('error', 'Failed to verify password');
            }
        });
    });
    
    function hideSensitiveField(fieldId, fieldName) {
        const field = document.getElementById(fieldId);
        field.value = '••••••••••••••••';
        field.readOnly = true;
        field.type = 'password';
        field.classList.remove('bg-white');
        field.classList.add('bg-gray-50');
        
        // Update the button
        const viewButton = field.parentElement.querySelector('button[onclick*="hideSensitiveField"]');
        viewButton.textContent = 'View';
        viewButton.onclick = function() { requestPasswordForField(fieldId, fieldName); };
        viewButton.classList.remove('bg-red-100', 'text-red-600');
        viewButton.classList.add('bg-blue-100', 'text-blue-600');
        
        showNotification('info', 'Sensitive data hidden');
    }
    
    // Payment tab management
    function showPaymentTab(tabName) {
        // Hide all payment sections
        document.querySelectorAll('.payment-section').forEach(section => {
            section.classList.add('hidden');
        });
        
        // Remove active class from all payment tabs
        document.querySelectorAll('.payment-tab').forEach(tab => {
            tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected section
        document.getElementById(tabName + '-settings').classList.remove('hidden');
        
        // Add active class to selected tab
        event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
        event.target.classList.remove('border-transparent', 'text-gray-500');
    }
    
    // Password toggle functionality
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = event.target.closest('button');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
            icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" x2="22" y1="2" y2="22"></line></svg>';
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
            icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        }
    }
    
    // Copy to clipboard functionality
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('success', 'URL copied to clipboard');
        }, function() {
            showNotification('error', 'Failed to copy URL');
        });
    }
    
    // Test connection functions
    function testPaystackConnection() {
        const form = document.getElementById('paystackForm');
        const formData = new FormData(form);
        
        $.ajax({
            url: '<?= base_url("auth/settings/testPaystackConnection") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', 'Paystack connection successful!');
                } else {
                    showNotification('error', response.message || 'Connection failed');
                }
            },
            error: function() {
                showNotification('error', 'Failed to test connection');
            }
        });
    }
    
    function testMpesaConnection() {
        const form = document.getElementById('mpesaForm');
        const formData = new FormData(form);
        
        $.ajax({
            url: '<?= base_url("auth/settings/testMpesaConnection") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', 'M-Pesa connection successful!');
                } else {
                    showNotification('error', response.message || 'Connection failed');
                }
            },
            error: function() {
                showNotification('error', 'Failed to test connection');
            }
        });
    }
    
    // Save individual payment settings
    function savePaystackSettings() {
        const form = document.getElementById('paystackForm');
        const formData = new FormData(form);
        
        // Handle sensitive data - if field contains masked data, use the stored value
        const publicKeyField = document.getElementById('paystackPublic');
        if (publicKeyField.value === '••••••••••••••••' && sensitiveData['public_key']) {
            formData.set('public_key', sensitiveData['public_key']);
        }
        
        const secretKeyField = document.getElementById('paystackSecret');
        if (secretKeyField.value === '••••••••••••••••' && sensitiveData['secret_key']) {
            formData.set('secret_key', sensitiveData['secret_key']);
        }
        
        $.ajax({
            url: '<?= base_url("auth/settings/savePaystackSettings") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message || 'Paystack settings saved successfully!');
                } else {
                    showNotification('error', response.message || 'Failed to save Paystack settings');
                }
            },
            error: function() {
                showNotification('error', 'Failed to save Paystack settings');
            }
        });
    }
    
    function saveMpesaSettings() {
        const form = document.getElementById('mpesaForm');
        const formData = new FormData(form);
        
        // Handle sensitive data - if field contains masked data, use the stored values
        const consumerKeyField = document.getElementById('mpesaConsumerKey');
        const consumerSecretField = document.getElementById('mpesaSecret');
        const passkeyField = document.getElementById('mpesaPasskey');
        
        if (consumerKeyField.value === '••••••••••••••••' && sensitiveData['consumer_key']) {
            formData.set('consumer_key', sensitiveData['consumer_key']);
        }
        
        if (consumerSecretField.value === '••••••••••••••••' && sensitiveData['consumer_secret']) {
            formData.set('consumer_secret', sensitiveData['consumer_secret']);
        }
        
        if (passkeyField.value === '••••••••••••••••' && sensitiveData['passkey']) {
            formData.set('passkey', sensitiveData['passkey']);
        }
        
        $.ajax({
            url: '<?= base_url("auth/settings/saveMpesaSettings") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message || 'M-Pesa settings saved successfully!');
                } else {
                    showNotification('error', response.message || 'Failed to save M-Pesa settings');
                }
            },
            error: function() {
                showNotification('error', 'Failed to save M-Pesa settings');
            }
        });
    }
    
    // Save all payment settings
    function saveAllPaymentSettings() {
        const paystackData = new FormData(document.getElementById('paystackForm'));
        const mpesaData = new FormData(document.getElementById('mpesaForm'));
        
        Promise.all([
            $.ajax({
                url: '<?= base_url("auth/settings/savePaystackSettings") ?>',
                method: 'POST',
                data: paystackData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }),
            $.ajax({
                url: '<?= base_url("auth/settings/saveMpesaSettings") ?>',
                method: 'POST',
                data: mpesaData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
        ]).then(() => {
            showNotification('success', 'All payment settings saved successfully!');
        }).catch(() => {
            showNotification('error', 'Some payment settings failed to save');
        });
    }
    
    // Load existing settings
    $(document).ready(function() {
        // Load Paystack settings
        $.ajax({
            url: '<?= base_url("auth/settings/getPaystackSettings") ?>',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    const form = document.getElementById('paystackForm');
                    Object.keys(response.data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (input.type === 'checkbox') {
                                input.checked = response.data[key] == '1';
                            } else if (input.classList.contains('select2')) {
                                $(input).val(response.data[key]).trigger('change');
                            } else if (key === 'public_key') {
                                // Handle sensitive data
                                input.value = response.data[key] ? '••••••••••••••••' : '';
                                input.readOnly = true;
                                input.classList.add('bg-gray-50');
                            } else if (key === 'secret_key') {
                                // Handle sensitive data
                                input.value = response.data[key] ? '••••••••••••••••' : '';
                                input.readOnly = true;
                                input.classList.add('bg-gray-50');
                            } else {
                                input.value = response.data[key] || '';
                            }
                        }
                    });
                }
            }
        });
        
        // Load M-Pesa settings
        $.ajax({
            url: '<?= base_url("auth/settings/getMpesaSettings") ?>',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    const form = document.getElementById('mpesaForm');
                    Object.keys(response.data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (input.type === 'checkbox') {
                                input.checked = response.data[key] == '1';
                            } else if (input.classList.contains('select2')) {
                                $(input).val(response.data[key]).trigger('change');
                            } else if (['consumer_key', 'consumer_secret', 'pass_key'].includes(key)) {
                                // Handle sensitive data
                                input.value = response.data[key] ? '••••••••••••••••' : '';
                                input.readOnly = true;
                                input.classList.add('bg-gray-50');
                            } else {
                                input.value = response.data[key] || '';
                            }
                        }
                    });
                }
            }
        });
    });
</script>
<?php $this->endSection(); ?>
