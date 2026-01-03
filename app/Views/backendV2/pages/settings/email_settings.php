<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
Email Settings
<?php $this->endSection(); ?>

<?php $this->section('additionalCSS'); ?>
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<?php $this->endSection(); ?>

<?php $this->section('additionalJS'); ?>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<main class="flex-1 overflow-y-auto p-6">
    <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

    <!-- Email Settings Panel -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Email Configuration</h2>
                <p class="mt-1 text-sm text-gray-500">Configure SMTP settings for email notifications</p>
            </div>
            <button onclick="saveForm('emailForm', '<?= base_url('auth/settings/saveEmailSettings') ?>', 'Email settings saved successfully!')" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                <i data-lucide="save" class="w-4 h-4 mr-2 inline z-10"></i>
                <span>Save Settings</span>
            </button>
        </div>
        
        <form id="emailForm" class="space-y-6">
            <!-- SMTP Configuration -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center mb-3">
                    <i data-lucide="mail" class="w-5 h-5 text-blue-600 mr-2"></i>
                    <h3 class="text-sm font-medium text-blue-800">SMTP Configuration</h3>
                </div>
                <p class="text-sm text-blue-700">Configure your email server settings for sending notifications</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                    <input type="text" name="host" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="smtp.gmail.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                    <select name="port" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="587">587 (TLS)</option>
                        <option value="465">465 (SSL)</option>
                        <option value="25">25 (No encryption)</option>
                        <option value="2525">2525 (Alternative)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                    <input type="email" name="username" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="your-email@gmail.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                    <div class="password-input-container">
                        <input type="password" name="password" id="smtpPassword" class="password-input-with-buttons w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="App password or email password" readonly>
                        <div class="!absolute !inset-y-0 !right-0 flex items-center gap-2 pr-3">
                            <button type="button" onclick="requestPasswordForField('smtpPassword', 'password')" class="view-sensitive-btn">
                                View
                            </button>
                            <div class="flex items-center">
                                <input type="checkbox" id="toggleSmtpPassword" onchange="togglePasswordWithCheckbox('smtpPassword', this)" class="w-3 h-3 rounded">
                                <label for="toggleSmtpPassword" class="ml-1 text-xs text-gray-600 whitespace-nowrap">Show</label>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Click "View" to reveal sensitive data</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                    <select name="encryption" class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="">None</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Timeout (seconds)</label>
                    <input type="number" name="smtp_timeout" value="30" min="5" max="300" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Email Addresses -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Addresses</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                        <input type="email" name="from_address" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="noreply@yoursite.com">
                        <p class="text-xs text-gray-500 mt-1">Email address that will appear as sender</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                        <input type="text" name="from_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="KEWASNET">
                        <p class="text-xs text-gray-500 mt-1">Name that will appear as sender</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reply-To Email</label>
                        <input type="email" name="reply_to_email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="support@yoursite.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">BCC Email (Optional)</label>
                        <input type="email" name="bcc_email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="admin@yoursite.com">
                        <p class="text-xs text-gray-500 mt-1">Receive copy of all emails sent</p>
                    </div>
                </div>
            </div>
            
            <!-- Email Templates -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Email Templates</h3>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="previewEmailTemplate()" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                            Preview
                        </button>
                        <button type="button" onclick="loadDefaultTemplates()" class="text-gray-600 hover:text-gray-800 text-sm flex items-center">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>
                            Load Defaults
                        </button>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">Template Variables</h4>
                            <p class="text-sm text-blue-700 mt-1">You can use these variables in your templates:</p>
                            <div class="mt-2 text-xs text-blue-600 space-x-4">
                                <code class="bg-blue-100 px-1 rounded">{{site_name}}</code>
                                <code class="bg-blue-100 px-1 rounded">{{site_url}}</code>
                                <code class="bg-blue-100 px-1 rounded">{{current_year}}</code>
                                <code class="bg-blue-100 px-1 rounded">{{user_name}}</code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Header Template
                            <span class="text-gray-500 font-normal">(Added to the top of all emails)</span>
                        </label>
                        <textarea name="email_header" id="emailHeaderTemplate" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter HTML header template..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">This header will be added to the top of all outgoing emails</p>
                        <div class="mt-2">
                            <button type="button" onclick="insertVariableToHeader('{{site_name}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded mr-1">+ Site Name</button>
                            <button type="button" onclick="insertVariableToHeader('{{site_url}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded mr-1">+ Site URL</button>
                            <button type="button" onclick="insertVariableToHeader('{{current_year}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded mr-1">+ Current Year</button>
                            <button type="button" onclick="insertVariableToHeader('{{user_name}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">+ User Name</button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Footer Template
                            <span class="text-gray-500 font-normal">(Added to the bottom of all emails)</span>
                        </label>
                        <textarea name="email_footer" id="emailFooterTemplate" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter HTML footer template..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">This footer will be added to the bottom of all outgoing emails</p>
                        <div class="mt-2">
                            <button type="button" onclick="insertVariableToFooter('{{site_name}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded mr-1">+ Site Name</button>
                            <button type="button" onclick="insertVariableToFooter('{{site_url}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded mr-1">+ Site URL</button>
                            <button type="button" onclick="insertVariableToFooter('{{current_year}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded mr-1">+ Current Year</button>
                            <button type="button" onclick="insertVariableToFooter('{{user_name}}')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">+ User Name</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Email Settings -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Options</h3>
                
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="email_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Enable email notifications</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="debug_mode" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Enable debug mode (logs email details)</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="html_emails" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Send HTML emails (recommended)</span>
                    </label>
                </div>
            </div>
            
            <!-- Test Email Section -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Test Email Configuration</h3>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Test Your Configuration</h4>
                            <p class="text-sm text-yellow-700 mt-1">Send a test email to verify your SMTP settings are working correctly</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="email" id="testEmail" placeholder="test@example.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="button" onclick="sendTestEmail()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="send" class="w-4 h-4 mr-2 inline"></i>
                        Send Test Email
                    </button>
                </div>
            </div>
        </form>
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
                            <p class="text-sm text-yellow-700 mt-1">Enter your account password to view sensitive email credentials</p>
                        </div>
                    </div>
                </div>
                
                <form id="passwordVerificationForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Password</label>
                        <div class="relative">
                            <input type="password" id="verificationPassword" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-24 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your password" required>
                            <div class="absolute inset-y-0 right-0 flex items-center gap-1 pr-3">
                                <input type="checkbox" id="toggleVerificationPassword" onchange="togglePasswordWithCheckbox('verificationPassword', this)" class="w-3 h-3 rounded">
                                <label for="toggleVerificationPassword" class="ml-1 text-xs text-gray-600 whitespace-nowrap">Show</label>
                            </div>
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

<?= $this->section('scripts') ?>
<style>
    /* Password input styling */
    .password-input-container {
        position: relative;
    }
    
    .password-input-with-buttons {
        padding-right: 120px !important;
    }
    
    /* View button styling */
    .view-sensitive-btn {
        background: #dbeafe !important;
        color: #2563eb !important;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        margin-right: 4px;
        z-index: 10;
        position: relative;
    }
    
    .view-sensitive-btn:hover {
        background: #bfdbfe !important;
    }
    
    .hide-sensitive-btn {
        background: #fee2e2 !important;
        color: #dc2626 !important;
    }
    
    .hide-sensitive-btn:hover {
        background: #fecaca !important;
    }
</style>
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
            url: '<?= base_url("auth/settings/verifyEmailPasswordForSensitiveData") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
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
                    viewButton.classList.remove('view-sensitive-btn');
                    viewButton.classList.add('hide-sensitive-btn');
                    
                    closePasswordModal();
                    showNotification('success', 'Sensitive data revealed successfully');
                } else {
                    showNotification('error', response.message || 'Invalid password');
                }
            },
            error: function() {
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
        viewButton.classList.remove('hide-sensitive-btn');
        viewButton.classList.add('view-sensitive-btn');

        showNotification('info', 'Sensitive data hidden');
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
    
    // Checkbox toggle functionality for password fields
    function togglePasswordWithCheckbox(inputId, checkbox) {
        const input = document.getElementById(inputId);
        
        if (checkbox.checked) {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
    
    // Send test email
    function sendTestEmail() {
        const testEmail = document.getElementById('testEmail').value;
        if (!testEmail) {
            showNotification('error', 'Please enter a test email address');
            return;
        }
        
        const formData = new FormData(document.getElementById('emailForm'));
        formData.append('test_email', testEmail);
        
        // Handle sensitive data - if field contains masked data, use the stored value
        const passwordField = document.getElementById('smtpPassword');
        if (passwordField.value === '••••••••••••••••' && sensitiveData['password']) {
            formData.set('password', sensitiveData['password']);
        }
        
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="w-4 h-4 mr-2 inline animate-spin" data-lucide="loader"></i> Sending...';
        button.disabled = true;
        
        $.ajax({
            url: '<?= base_url("auth/settings/sendTestEmail") ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('success', 'Test email sent successfully!');
                } else {
                    showNotification('error', response.message || 'Failed to send test email');
                }
            },
            error: function() {
                showNotification('error', 'An error occurred while sending test email');
            },
            complete: function() {
                button.innerHTML = originalText;
                button.disabled = false;
                lucide.createIcons();
            }
        });
    }

    // Save email settings
    function saveForm(formId, url, successMessage) {
        const form = document.getElementById(formId);
        const formData = new FormData(form);
        
        // Get content from Summernote editors before saving
        const headerContent = $('#emailHeaderTemplate').summernote('code');
        const footerContent = $('#emailFooterTemplate').summernote('code');
        
        // Update form data with Summernote content
        formData.set('email_header', headerContent);
        formData.set('email_footer', footerContent);
        
        // Handle sensitive data - if field contains masked data, use the stored value
        const passwordField = document.getElementById('smtpPassword');
        if (passwordField.value === '••••••••••••••••' && sensitiveData['password']) {
            formData.set('password', sensitiveData['password']);
        }
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('success', successMessage);
                } else {
                    showNotification('error', response.message || 'Failed to save settings');
                }
            },
            error: function() {
                showNotification('error', 'An error occurred while saving settings');
            }
        });
    }
    
    // Load existing email settings
    $(document).ready(function() {
        $.ajax({
            url: '<?= base_url("auth/settings/getEmailSettings") ?>',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const form = document.getElementById('emailForm');
                    Object.keys(response.data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            if (input.type === 'checkbox') {
                                input.checked = response.data[key] == '1';
                            } else if (input.classList.contains('select2')) {
                                $(input).val(response.data[key]).trigger('change');
                            } else if (key === 'password') {
                                // Handle sensitive data
                                input.value = response.data[key] ? '••••••••••••••••' : '';
                                input.readOnly = true;
                                input.classList.add('bg-gray-50');
                            } else if (key === 'email_header' || key === 'email_footer') {
                                // Handle Summernote fields
                                if (response.data[key]) {
                                    $('#' + (key === 'email_header' ? 'emailHeaderTemplate' : 'emailFooterTemplate')).summernote('code', response.data[key]);
                                }
                            } else {
                                input.value = response.data[key] || '';
                            }
                        }
                    });
                }
            },
            error: function() {
                showNotification('error', 'Failed to load email settings');
            }
        });
    });
    
    // Initialize Summernote for template editors
    $(document).ready(function() {
        // Initialize Summernote with custom toolbar
        $('#emailHeaderTemplate, #emailFooterTemplate').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onChange: function(contents, $editable) {
                    // Update the original textarea with the content
                    $(this).val(contents);
                }
            }
        });
    });
    
    // Insert template variables into Summernote editors
    function insertVariableToHeader(variable) {
        $('#emailHeaderTemplate').summernote('editor.insertText', variable);
    }
    
    function insertVariableToFooter(variable) {
        $('#emailFooterTemplate').summernote('editor.insertText', variable);
    }
    
    // Email template management functions
    function loadDefaultTemplates() {
        if (confirm('This will replace your current templates with default ones. Continue?')) {
            const defaultHeader = `
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f8f9fa;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center;">
        <h1 style="margin: 0; font-size: 24px;">{{site_name}}</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Professional Email Communications</p>
    </div>
    <div style="background: white; padding: 20px;">
`;

            const defaultFooter = `
    </div>
    <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef;">
        <p style="margin: 0; color: #6c757d; font-size: 14px;">
            © {{current_year}} {{site_name}}. All rights reserved.
        </p>
        <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 12px;">
            This email was sent from <a href="{{site_url}}" style="color: #667eea;">{{site_url}}</a>
        </p>
        <div style="margin-top: 15px;">
            <a href="{{site_url}}" style="color: #667eea; text-decoration: none; margin: 0 10px;">Visit Website</a>
            <a href="{{site_url}}/contact" style="color: #667eea; text-decoration: none; margin: 0 10px;">Contact Us</a>
        </div>
    </div>
</div>
`;

            // Set content in Summernote editors
            $('#emailHeaderTemplate').summernote('code', defaultHeader.trim());
            $('#emailFooterTemplate').summernote('code', defaultFooter.trim());
            
            showNotification('success', 'Default templates loaded successfully');
        }
    }
    
    function previewEmailTemplate() {
        // Get content from Summernote editors
        const header = $('#emailHeaderTemplate').summernote('code');
        const footer = $('#emailFooterTemplate').summernote('code');
        
        const sampleContent = `
            <h2 style="color: #333; margin-bottom: 15px;">Sample Email Content</h2>
            <p>Dear {{user_name}},</p>
            <p>This is a preview of how your emails will look with the current header and footer templates.</p>
            <p>Your email content will appear in this section, styled according to your template design.</p>
            <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #667eea; margin: 20px 0;">
                <p style="margin: 0;"><strong>Template Variables:</strong></p>
                <ul style="margin: 10px 0 0 20px;">
                    <li>{{site_name}} - Your website name</li>
                    <li>{{site_url}} - Your website URL</li>
                    <li>{{current_year}} - Current year</li>
                    <li>{{user_name}} - Recipient's name</li>
                </ul>
            </div>
            <p>Best regards,<br>The {{site_name}} Team</p>
        `;
        
        const fullPreview = header + sampleContent + footer;
        
        // Replace template variables with sample data
        const previewContent = fullPreview
            .replace(/\{\{site_name\}\}/g, 'KEWASNET')
            .replace(/\{\{site_url\}\}/g, window.location.origin)
            .replace(/\{\{current_year\}\}/g, new Date().getFullYear())
            .replace(/\{\{user_name\}\}/g, 'John Doe');
        
        // Open preview in new window
        const previewWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes');
        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Email Template Preview</title>
                <meta charset="utf-8">
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 0; 
                        padding: 20px; 
                        background: #f5f5f5; 
                    }
                    .preview-container { 
                        max-width: 800px; 
                        margin: 0 auto; 
                        background: white; 
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    }
                </style>
            </head>
            <body>
                <div class="preview-container">
                    ${previewContent}
                </div>
            </body>
            </html>
        `);
        previewWindow.document.close();
    }
</script>
<?php $this->endSection(); ?>
