<?php $this->extend('backendV2/layouts/main'); ?>

<?php $this->section('title'); ?>
Social Media Settings
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/settings/partials/settings_navigation'); ?>

        <!-- Social Media Settings Panel -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-primary">Social Media Links</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage your social media platform links</p>
                </div>
                <button onclick="saveSocialMediaSettings()" class="gradient-btn text-white px-8 py-2 rounded-[50px] hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-1 inline z-10"></i>
                    <span>Save Changes</span>
                </button>
            </div>
            
            <form id="socialMediaForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                    <div class="relative">
                        <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="facebook" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <input type="url" name="facebook" class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://facebook.com/yourpage">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                    <div class="relative">
                        <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="twitter" class="w-5 h-5 text-blue-400"></i>
                        </div>
                        <input type="url" name="twitter" class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://twitter.com/yourhandle">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                    <div class="relative">
                        <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="instagram" class="w-5 h-5 text-pink-500"></i>
                        </div>
                        <input type="url" name="instagram" class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://instagram.com/yourhandle">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                    <div class="relative">
                        <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="linkedin" class="w-5 h-5 text-blue-700"></i>
                        </div>
                        <input type="url" name="linkedin" class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://linkedin.com/company/yourcompany">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">YouTube Channel URL</label>
                    <div class="relative">
                        <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="youtube" class="w-5 h-5 text-red-600"></i>
                        </div>
                        <input type="url" name="youtube" class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://youtube.com/channel/yourchannel">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                    <div class="relative">
                        <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="phone" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <input type="tel" name="whatsapp" class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+254700000000">
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                    <div class="relative">
                        <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="globe" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <input type="url" name="website" class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://yourwebsite.com">
                    </div>
                </div>
            </form>
            
            <!-- Social Media Preview -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
                        <i data-lucide="facebook" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-gray-600">Facebook</span>
                    </div>
                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
                        <i data-lucide="twitter" class="w-5 h-5 text-blue-400"></i>
                        <span class="text-sm text-gray-600">Twitter</span>
                    </div>
                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
                        <i data-lucide="instagram" class="w-5 h-5 text-pink-500"></i>
                        <span class="text-sm text-gray-600">Instagram</span>
                    </div>
                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
                        <i data-lucide="linkedin" class="w-5 h-5 text-blue-700"></i>
                        <span class="text-sm text-gray-600">LinkedIn</span>
                    </div>
                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
                        <i data-lucide="youtube" class="w-5 h-5 text-red-600"></i>
                        <span class="text-sm text-gray-600">YouTube</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const socialMediaLinksURLs = '<?= base_url("auth/settings/get-social-media") ?>';
    const saveSocialMediaURL = '<?= base_url("auth/settings/save-social-media") ?>';
    let socialId;

    // Load existing social media settings
    $(document).ready(function() {
        initializeLucideIcons();
        loadSocialMediaSettings();
    });

    function loadSocialMediaSettings() {
        $.ajax({
            url: socialMediaLinksURLs,
            method: 'GET',
            beforeSend: function() {
                // Show loading state
                $('button[onclick="saveSocialMediaSettings()"]').prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 mr-1 inline animate-spin z-10"></i><span>Loading...</span>');
                initializeLucideIcons(); // Re-init for loader icon
            },
            success: function(response) {
                if (response.success) {
                    socialId = response.data.uuid;
                    const form = document.getElementById('socialMediaForm');
                    
                    // Populate form fields
                    Object.keys(response.data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) input.value = response.data[key] || '';
                    });
                    
                    // Update preview links
                    updatePreviewLinks(response.data);
                    showNotification('success', 'Social media settings loaded successfully');
                } else {
                    showNotification('error', 'Failed to load social media settings');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading social media settings:', error);
                showNotification('error', 'Failed to load social media settings. Please refresh the page.');
            },
            complete: function() {
                // Reset button state
                $('button[onclick="saveSocialMediaSettings()"]').prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4 mr-1 inline z-10"></i><span>Save Changes</span>');
                // Re-initialize lucide icons
                setTimeout(initializeLucideIcons, 50);
            }
        });
    }

    function saveSocialMediaSettings() {
        const form = document.getElementById('socialMediaForm');
        const formData = new FormData(form);
        
        // Add social ID if it exists (for updates)
        if (socialId) {
            formData.append('id', socialId);
        }

        // Validate form data
        if (!validateSocialMediaForm(formData)) {
            return;
        }

        $.ajax({
            url: saveSocialMediaURL + '/' + socialId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                // Show loading state
                $('button[onclick="saveSocialMediaSettings()"]').prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 mr-1 inline animate-spin z-10"></i><span>Saving...</span>');
                initializeLucideIcons(); // Re-init for loader icon
            },
            success: function(response) {
                if (response.success) {
                    // Update social ID for future updates
                    if (response.data && response.data.id) {
                        socialId = response.data.id;
                    }
                    
                    // Show success notification
                    showNotification('success', response.message || 'Social media settings saved successfully!');
                    
                    // Update preview links
                    const formDataObj = {};
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'id') {
                            formDataObj[key] = value;
                        }
                    }
                    updatePreviewLinks(formDataObj);
                    
                } else {
                    showNotification('error', response.message || 'Failed to save social media settings');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving social media settings:', error);
                
                let errorMessage = 'Failed to save social media settings. Please try again.';
                
                // Handle specific error responses
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorMessage = 'Please check your input and try again.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred. Please contact support if this persists.';
                }

                showNotification('error', errorMessage);
            },
            complete: function() {
                // Reset button state
                $('button[onclick="saveSocialMediaSettings()"]').prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4 mr-1 inline z-10"></i><span>Save Changes</span>');
                // Re-initialize lucide icons with a small delay
                setTimeout(initializeLucideIcons, 50);
            }
        });
    }

    function validateSocialMediaForm(formData) {
        let isValid = true;
        const errors = [];

        // Validate URLs
        const urlFields = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'website'];
        urlFields.forEach(field => {
            const value = formData.get(field);
            if (value && value.trim() !== '') {
                if (!isValidURL(value)) {
                    errors.push(`Please enter a valid ${field.charAt(0).toUpperCase() + field.slice(1)} URL`);
                    isValid = false;
                }
            }
        });

        // Validate WhatsApp (phone number)
        const whatsapp = formData.get('whatsapp');
        if (whatsapp && whatsapp.trim() !== '') {
            if (!isValidPhoneNumber(whatsapp)) {
                errors.push('Please enter a valid WhatsApp phone number (e.g., +254700000000)');
                isValid = false;
            }
        }

        // Show validation errors
        if (!isValid) {
            showNotification('error', errors.join('<br>'));
        }

        return isValid;
    }

    function isValidURL(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    function isValidPhoneNumber(phone) {
        // Basic phone number validation (international format)
        const phoneRegex = /^\+[1-9]\d{1,14}$/;
        return phoneRegex.test(phone);
    }

    function updatePreviewLinks(data) {
        // Update preview section with actual links
        const previewContainer = document.querySelector('.flex.flex-wrap.gap-4');
        if (previewContainer && data) {
            // You can add logic here to show/hide preview items based on filled URLs
            // or update href attributes for actual links
            console.log('Preview updated with:', data);
        }
    }

    // Real-time URL validation
    $(document).on('blur', 'input[type="url"]', function() {
        const input = this;
        const value = input.value.trim();
        
        if (value && !isValidURL(value)) {
            input.classList.add('border-red-500');
            input.classList.remove('border-gray-300');
        } else {
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
        }
    });

    // Real-time phone validation for WhatsApp
    $(document).on('blur', 'input[name="whatsapp"]', function() {
        const input = this;
        const value = input.value.trim();
        
        if (value && !isValidPhoneNumber(value)) {
            input.classList.add('border-red-500');
            input.classList.remove('border-gray-300');
        } else {
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
        }
    });
</script>
<?php $this->endSection(); ?>