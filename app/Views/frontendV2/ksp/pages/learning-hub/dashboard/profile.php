<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

    <style>
        .events-page-pattern-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            pointer-events: none;
            background-image: 
                repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px),
                repeating-linear-gradient(-45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px);
            background-size: 40px 40px;
        }
    </style>

<div class="h-full py-8 relative">
    <!-- Diagonal Grid Pattern -->
    <div class="events-page-pattern-bg"></div>

    <div class="container mx-auto px-4 pt-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-dark mb-2">Profile Settings</h1>
            <p class="text-slate-600">Manage your account information and security</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md border border-slate-200 p-4 sticky top-4 hover:shadow-2xl transition-shadow z-10 relative">
                    <nav class="space-y-1">
                        <button onclick="showSection('profile')" 
                                class="section-btn w-full text-left px-4 py-3 rounded-lg font-medium flex items-center gap-3 text-primary bg-primary/10">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            Profile Information
                        </button>
                        <button onclick="showSection('password')" 
                                class="section-btn w-full text-left px-4 py-3 rounded-lg font-medium flex items-center gap-3 text-slate-600 hover:bg-slate-50">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                            Change Password
                        </button>
                        <button onclick="showSection('danger')" 
                                class="section-btn w-full text-left px-4 py-3 rounded-lg font-medium flex items-center gap-3 text-slate-600 hover:bg-slate-50">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                            Danger Zone
                        </button>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Profile Information Section -->
                <div id="profile-section" class="section-content bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <h2 class="text-xl font-bold text-dark mb-6">Profile Information</h2>
                    
                    <form id="profileForm" enctype="multipart/form-data">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        
                        <!-- Profile Picture -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-3">Profile Picture</label>
                            <div class="flex items-center gap-4">
                                <?php 
                                $picture = $user['picture'] ?? $user->picture ?? '';
                                if (!empty($picture)): 
                                ?>
                                    <img id="profilePreview" src="<?= base_url($picture) ?>" alt="Profile" 
                                         class="w-20 h-20 rounded-full object-cover border-2 border-slate-200">
                                <?php else: ?>
                                    <div id="profilePreview" class="w-20 h-20 rounded-full bg-slate-200 flex items-center justify-center border-2 border-slate-200">
                                        <i data-lucide="user" class="w-10 h-10 text-slate-400"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <input type="file" name="picture" id="picture" accept="image/*" class="hidden">
                                    <label for="picture" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 cursor-pointer font-medium">
                                        <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                                        Upload New
                                    </label>
                                    <p class="text-xs text-slate-500 mt-2">JPG, PNG or GIF. Max 2MB.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Name -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">First Name *</label>
                                <input type="text" name="first_name" value="<?= isset($user['first_name']) ? esc($user['first_name']) : (isset($user->first_name) ? esc($user->first_name) : '') ?>" required
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Last Name *</label>
                                <input type="text" name="last_name" value="<?= isset($user['last_name']) ? esc($user['last_name']) : (isset($user->last_name) ? esc($user->last_name) : '') ?>" required
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                        </div>
                        
                        <!-- Email (Read-only) -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                            <input type="email" value="<?= isset($user['email']) ? esc($user['email']) : (isset($user->email) ? esc($user->email) : '') ?>" 
                                   disabled class="w-full px-4 py-2.5 border border-slate-300 rounded-lg bg-slate-50 text-slate-500">
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i>
                                Email address cannot be changed
                            </p>
                        </div>
                        
                        <!-- Phone -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="<?= isset($user['phone']) ? esc($user['phone']) : (isset($user->phone) ? esc($user->phone) : '') ?>" 
                                   placeholder="+254 700 000 000"
                                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        
                        <!-- Username -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                            <input type="text" name="username" value="<?= isset($user['username']) ? esc($user['username']) : (isset($user->username) ? esc($user->username) : '') ?>" 
                                   placeholder="Your username"
                                   class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        
                        <!-- Bio -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Bio</label>
                            <textarea name="bio" rows="4" 
                                      placeholder="Tell us about yourself..."
                                      class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"><?= isset($user['bio']) ? esc($user['bio']) : (isset($user->bio) ? esc($user->bio) : '') ?></textarea>
                            <p class="text-xs text-slate-500 mt-1">Brief description for your profile</p>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                            <a href="<?= base_url('ksp/learning-hub/dashboard') ?>" 
                               class="px-6 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="gradient-btn px-6 py-2.5 rounded-lg text-white font-medium flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Change Password Section -->
                <div id="password-section" class="section-content bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6 hidden shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <h2 class="text-xl font-bold text-dark mb-2">Change Password</h2>
                    <p class="text-sm text-slate-600 mb-6">Ensure your account stays secure by using a strong password</p>
                    
                    <form id="passwordForm">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        
                        <!-- Current Password -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Current Password *</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" required placeholder="Enter your current password"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary pr-10">
                                <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- New Password -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">New Password *</label>
                            <div class="relative">
                                <input type="password" name="new_password" id="new_password" required placeholder="Enter your new password"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary pr-10">
                                <button type="button" onclick="togglePassword('new_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Minimum 8 characters with letters and numbers</p>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password *</label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm your new password"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary pr-10">
                                <button type="button" onclick="togglePassword('confirm_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                            <button type="button" onclick="showSection('profile')"
                                    class="px-6 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-primary hover:bg-primary-dark px-6 py-2.5 rounded-lg text-white font-medium flex items-center gap-2">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Danger Zone Section -->
                <div id="danger-section" class="section-content bg-white rounded-lg shadow-sm border border-red-200 p-6 hidden shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <div class="flex items-start gap-3 mb-6">
                        <div class="bg-red-100 p-2 rounded-full">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-dark mb-1">Danger Zone</h2>
                            <p class="text-sm text-slate-600">Irreversible actions that affect your account</p>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <h3 class="font-bold text-dark mb-2">Delete Account</h3>
                        <p class="text-sm text-slate-600 mb-4">
                            Once you delete your account, there is no going back. This action will permanently delete:
                        </p>
                        <ul class="text-sm text-slate-600 space-y-2 mb-6 ml-4">
                            <li class="flex items-start gap-2">
                                <i data-lucide="x" class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>All your personal information and profile data</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="x" class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>Your course enrollments and progress</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="x" class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>All certificates and achievements</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="x" class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>Access to all purchased courses</span>
                            </li>
                        </ul>
                        <button type="button" onclick="confirmDeleteAccount()" 
                                class="bg-red-600 hover:bg-red-700 px-6 py-2.5 rounded-lg text-white font-medium flex items-center gap-2">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Delete My Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="statusMessage" class="fixed top-4 right-4 z-50 hidden"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    // Profile picture preview
    $('#picture').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profilePreview').html(`<img src="${e.target.result}" alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 border-slate-200">`);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Profile form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Saving...');
        lucide.createIcons();
        
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/profile/update') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    showStatus(response.message || 'Profile updated successfully', 'check-circle', 'bg-green-100 text-green-800');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showStatus(response.message || 'Failed to update profile', 'alert-circle', 'bg-red-100 text-red-800');
                    submitBtn.prop('disabled', false).html(originalText);
                    lucide.createIcons();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showStatus(response?.message || 'An error occurred. Please try again.', 'alert-circle', 'bg-red-100 text-red-800');
                submitBtn.prop('disabled', false).html(originalText);
                lucide.createIcons();
            }
        });
    });
    
    // Password form submission
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const newPassword = form.find('input[name="new_password"]').val();
        const confirmPassword = form.find('input[name="confirm_password"]').val();
        
        // Validate passwords match
        if (newPassword !== confirmPassword) {
            showStatus('Passwords do not match', 'alert-circle', 'bg-red-100 text-red-800');
            return;
        }
        
        // Validate password strength
        if (newPassword.length < 8) {
            showStatus('Password must be at least 8 characters', 'alert-circle', 'bg-red-100 text-red-800');
            return;
        }
        
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Updating...');
        lucide.createIcons();
        
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/profile/change-password') ?>',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    showStatus(response.message || 'Password updated successfully', 'check-circle', 'bg-green-100 text-green-800');
                    form[0].reset();
                    // Redirect to login page after 2 seconds
                    setTimeout(() => {
                        window.location.href = response.redirect || '<?= base_url('ksp/login') ?>';
                    }, 2000);
                } else {
                    showStatus(response.message || 'Failed to update password', 'alert-circle', 'bg-red-100 text-red-800');
                    submitBtn.prop('disabled', false).html(originalText);
                    lucide.createIcons();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showStatus(response?.message || 'An error occurred. Please try again.', 'alert-circle', 'bg-red-100 text-red-800');
                submitBtn.prop('disabled', false).html(originalText);
                lucide.createIcons();
            }
        });
    });
});

// Section navigation
function showSection(section) {
    // Hide all sections
    $('.section-content').addClass('hidden');
    
    // Reset all buttons
    $('.section-btn').removeClass('text-primary bg-primary/10').addClass('text-slate-600');
    
    // Show selected section
    $(`#${section}-section`).removeClass('hidden');
    
    // Highlight active button
    event.target.closest('.section-btn').classList.remove('text-slate-600');
    event.target.closest('.section-btn').classList.add('text-primary', 'bg-primary/10');
    
    lucide.createIcons();
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = event.currentTarget.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        field.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}

// Confirm account deletion
function confirmDeleteAccount() {
    const warningContent = `
        <div class="space-y-4">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <p class="text-sm text-red-800 font-medium">⚠️ Warning: This action is permanent and cannot be undone!</p>
            </div>
            
            <p class="text-gray-700 text-sm">
                Are you absolutely sure you want to delete your account?
            </p>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-800 mb-2">This will permanently delete:</p>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Your profile and all personal information</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>All course enrollments and learning progress</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>All certificates and achievements earned</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Access to all your purchased courses</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <p class="text-xs text-yellow-800">
                    <strong>Note:</strong> You will be logged out immediately and will not be able to recover your account.
                </p>
            </div>
        </div>
    `;
    
    const footerButtons = `
        <button type="button" onclick="closeModal()" 
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            Cancel
        </button>
        <button type="button" onclick="executeAccountDeletion()" 
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            Yes, Delete My Account
        </button>
    `;
    
    openModal({
        title: 'Delete Account',
        content: warningContent,
        footer: footerButtons,
        icon: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        iconBg: 'bg-red-100'
    });
}

function executeAccountDeletion() {
    closeModal();
    
    $.ajax({
        url: '<?= base_url('ksp/learning-hub/profile/delete-account') ?>',
        type: 'POST',
        data: {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.status === 'success') {
                showSuccessModal('Account Deleted', response.message || 'Your account has been successfully deleted. You will be redirected to the home page.');
                setTimeout(() => {
                    window.location.href = '<?= base_url() ?>';
                }, 3000);
            } else {
                showErrorModal('Deletion Failed', response.message || 'Failed to delete account.');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showErrorModal('Error', response?.message || 'Failed to delete account. Please try again.');
        }
    });
}

function showSuccessModal(title, message) {
    openModal({
        title: title,
        content: `<p class="text-gray-700">${message}</p>`,
        footer: '<button type="button" onclick="closeModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">OK</button>',
        icon: '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        iconBg: 'bg-green-100'
    });
}

function showErrorModal(title, message) {
    openModal({
        title: title,
        content: `<p class="text-gray-700">${message}</p>`,
        footer: '<button type="button" onclick="closeModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">OK</button>',
        icon: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
        iconBg: 'bg-red-100'
    });
}

function showStatus(message, icon, classes) {
    $('#statusMessage').removeClass('hidden')
        .html(`<div class="${classes} px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
            <i data-lucide="${icon}" class="w-5 h-5"></i>
            <span>${message}</span>
        </div>`);
    lucide.createIcons();
    setTimeout(() => $('#statusMessage').addClass('hidden'), 5000);
}
</script>
<?= $this->endSection() ?>

