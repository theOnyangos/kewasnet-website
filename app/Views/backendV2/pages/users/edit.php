<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <!-- Hero Banner with Gradient Overlay -->
        <div class="relative h-48 mb-6 overflow-hidden bg-cover bg-center" style="background-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%), url('<?= base_url('images/dashboard-breadcrumb.jpg') ?>');">
            <!-- Content -->
            <div class="relative z-10 h-full flex flex-col justify-center px-6 md:px-12">
                <!-- Breadcrumbs -->
                <nav class="mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center text-sm font-medium text-white/80 hover:text-white transition-colors">
                                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                                <a href="<?= base_url('auth/users') ?>" class="ml-1 text-sm font-medium text-white/80 hover:text-white transition-colors md:ml-2">
                                    Users
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                                <span class="ml-1 text-sm font-medium text-white md:ml-2">Edit User</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Title -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 drop-shadow-lg">
                            Edit <?= esc($user['first_name']) . ' ' . esc($user['last_name']) ?>'s Profile
                        </h1>
                        <p class="text-lg text-white/90">
                            Update user information, permissions, and account settings
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="hidden md:flex gap-3">
                        <a href="<?= base_url('auth/users') ?>" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                            Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 pb-6">

        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Edit User</h1>
                <p class="mt-1 text-sm text-slate-500">Update user information, change password, and manage account status</p>
            </div>

            <!-- User Details Form -->
            <form id="editUserForm" class="space-y-6" method="post" action="<?= base_url('auth/users/update/' . $user['id']) ?>">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">User Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="<?= esc($user['first_name']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   required>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="<?= esc($user['last_name']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   required>
                        </div>

                        <!-- Email (Read-only) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="<?= esc($user['email']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                   readonly
                                   disabled>
                            <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="<?= esc($user['phone'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role_id"
                                    name="role_id"
                                    class="select2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= esc($role['role_id']) ?>" <?= $user['role_id'] == $role['role_id'] ? 'selected' : '' ?>>
                                        <?= esc($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Account Status -->
                        <div>
                            <label for="account_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <select id="account_status"
                                    name="account_status"
                                    class="select2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required>
                                <option value="active" <?= ($user['account_status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="suspended" <?= ($user['account_status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="mt-4">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                            Bio
                        </label>
                        <textarea id="bio"
                                  name="bio"
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?= esc($user['bio'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Profile Image Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h3>
                    <p class="text-sm text-gray-600 mb-4">Update the user's profile picture. Recommended size: 400x400px</p>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <!-- Current Profile Image -->
                        <div class="flex flex-col items-center">
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 shadow-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                    <?php if (!empty($user['picture']) && file_exists(FCPATH . 'uploads/profiles/' . $user['picture'])): ?>
                                        <img id="currentProfileImage"
                                             src="<?= base_url('uploads/profiles/' . esc($user['picture'])) ?>"
                                             alt="Profile Picture"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div id="currentProfileImage" class="w-full h-full flex items-center justify-center text-white text-4xl font-bold">
                                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="absolute inset-0 rounded-full bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                    <i data-lucide="camera" class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 text-center">Current Picture</p>
                        </div>

                        <!-- Upload Section -->
                        <div class="flex-1">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-primary transition-colors duration-300">
                                <input type="file"
                                       id="profileImageInput"
                                       name="profile_image"
                                       accept="image/*"
                                       class="hidden">

                                <div id="uploadArea" class="text-center cursor-pointer" onclick="document.getElementById('profileImageInput').click()">
                                    <div class="flex justify-center mb-3">
                                        <div class="w-16 h-16 rounded-full bg-primary bg-opacity-10 flex items-center justify-center">
                                            <i data-lucide="upload" class="w-8 h-8 text-primary"></i>
                                        </div>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-1">Click to upload profile picture</h4>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                </div>

                                <!-- Preview Section (Hidden by default) -->
                                <div id="imagePreviewSection" class="hidden">
                                    <div class="flex items-center gap-4">
                                        <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-primary shadow-md">
                                            <img id="imagePreview" src="" alt="Preview" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1">
                                            <p id="fileName" class="text-sm font-medium text-gray-900"></p>
                                            <p id="fileSize" class="text-xs text-gray-500"></p>
                                        </div>
                                        <button type="button"
                                                onclick="clearImagePreview()"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Button -->
                            <div class="mt-4 flex gap-2">
                                <button type="button"
                                        id="uploadProfileImageBtn"
                                        onclick="uploadProfileImage()"
                                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                    <i data-lucide="upload-cloud" class="w-4 h-4 mr-2"></i>
                                    Upload Picture
                                </button>
                                <?php if (!empty($user['picture'])): ?>
                                <button type="button"
                                        onclick="removeProfileImage()"
                                        class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors flex items-center">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                                    Remove Picture
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= base_url('auth/users') ?>"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300">
                        <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                        Update User
                    </button>
                </div>
            </form>

            <!-- Change Password Section -->
            <div class="border-t mt-8 pt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
                <p class="text-sm text-gray-600 mb-4">Generate a new password for this user. The new password will be sent to their email address.</p>

                <form id="changePasswordForm" class="space-y-4">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <input type="hidden" name="user_id" value="<?= esc($user['id']) ?>">

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input type="password"
                                       id="new_password"
                                       name="new_password"
                                       placeholder="Enter new password or generate one"
                                       class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       required>
                                <div class="!absolute !inset-y-0 !right-0 pr-3 flex items-center">
                                    <button type="button"
                                            id="togglePassword"
                                            class="text-gray-500 hover:text-primary transition-colors cursor-pointer"
                                            title="Show/Hide Password">
                                        <i data-lucide="eye" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button"
                                    id="generatePassword"
                                    class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap">
                                <i data-lucide="key" class="w-5 h-5"></i>
                                <span>Generate Strong Password</span>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Password must be at least 8 characters long. Use the "Generate Strong Password" button to create a secure password.</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit"
                                class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors flex items-center">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            Change Password & Send Email
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="border-t border-red-200 mt-8 pt-8 bg-red-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                    Danger Zone
                </h3>

                <div class="bg-white border-l-4 border-red-600 rounded-r-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-red-900 mb-2">Account Deletion</h4>
                    <p class="text-sm text-red-700 mb-3">Deleting this user account is a permanent action that cannot be undone. When you delete this account:</p>

                    <ul class="text-sm text-red-700 space-y-2 ml-4">
                        <li class="flex items-start">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>The user will be <strong>immediately logged out</strong> from all active sessions</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>All user data including profile information, bio, and settings will be <strong>permanently removed</strong></span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>User access to all resources, courses, and forums will be <strong>revoked</strong></span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>The user's email address <strong>cannot be reused</strong> for new account creation</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>This action is <strong>irreversible</strong> - deleted accounts cannot be recovered</span>
                        </li>
                    </ul>

                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-xs text-yellow-800 flex items-start">
                            <i data-lucide="info" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span><strong>Alternative:</strong> If you want to temporarily restrict user access, consider using the "Suspended" account status instead of deleting the account.</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button type="button"
                            onclick="deleteUser('<?= esc($user['id']) ?>')"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        Delete Account Permanently
                    </button>
                </div>
            </div>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!-- JavaScript Section -->
<?= $this->section('scripts') ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const userId = '<?= esc($user['id']) ?>';

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for dropdowns
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumResultsForSearch: Infinity
            });
        }

        // Initialize Lucide icons first
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Small delay to ensure all icons are rendered
        setTimeout(function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 100);

        // Password visibility toggle
        const togglePasswordBtn = document.getElementById('togglePassword');
        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const passwordInput = document.getElementById('new_password');
                const icon = this.querySelector('i');

                if (passwordInput && icon) {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.setAttribute('title', 'Hide Password');

                        // Replace icon element for proper re-rendering
                        const newIcon = document.createElement('i');
                        newIcon.setAttribute('data-lucide', 'eye-off');
                        newIcon.className = 'w-5 h-5';
                        icon.replaceWith(newIcon);
                    } else {
                        passwordInput.type = 'password';
                        this.setAttribute('title', 'Show Password');

                        // Replace icon element for proper re-rendering
                        const newIcon = document.createElement('i');
                        newIcon.setAttribute('data-lucide', 'eye');
                        newIcon.className = 'w-5 h-5';
                        icon.replaceWith(newIcon);
                    }

                    // Reinitialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        }

        // Generate strong password
        const generatePasswordBtn = document.getElementById('generatePassword');
        if (generatePasswordBtn) {
            generatePasswordBtn.addEventListener('click', function() {
                const password = generateStrongPassword();
                const passwordInput = document.getElementById('new_password');
                const toggleIcon = document.querySelector('#togglePassword i');

                if (passwordInput) {
                    passwordInput.value = password;
                    passwordInput.type = 'text'; // Show generated password
                }

                // Update toggle icon
                if (toggleIcon) {
                    toggleIcon.setAttribute('data-lucide', 'eye-off');
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }

                // Show success message
                Swal.fire({
                    title: 'Password Generated!',
                    text: 'A strong password has been generated and filled in.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }

        // Profile image upload handler
        const profileImageInput = document.getElementById('profileImageInput');
        if (profileImageInput) {
            profileImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            title: 'Invalid File Type',
                            text: 'Please select a valid image file (PNG, JPG, JPEG)',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                        e.target.value = '';
                        return;
                    }

                    // Validate file size (5MB max)
                    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                    if (file.size > maxSize) {
                        Swal.fire({
                            title: 'File Too Large',
                            text: 'Image size must be less than 5MB',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                        e.target.value = '';
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('imagePreview').src = event.target.result;
                        document.getElementById('fileName').textContent = file.name;
                        document.getElementById('fileSize').textContent = formatFileSize(file.size);

                        // Show preview section and hide upload area
                        document.getElementById('uploadArea').classList.add('hidden');
                        document.getElementById('imagePreviewSection').classList.remove('hidden');

                        // Enable upload button
                        document.getElementById('uploadProfileImageBtn').disabled = false;

                        // Reinitialize icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Generate strong password function
    function generateStrongPassword(length = 16) {
        const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const lowercase = 'abcdefghijklmnopqrstuvwxyz';
        const numbers = '0123456789';
        const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        const allChars = uppercase + lowercase + numbers + symbols;

        let password = '';

        // Ensure at least one character from each set
        password += uppercase[Math.floor(Math.random() * uppercase.length)];
        password += lowercase[Math.floor(Math.random() * lowercase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];

        // Fill the rest randomly
        for (let i = password.length; i < length; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }

        // Shuffle the password
        return password.split('').sort(() => Math.random() - 0.5).join('');
    }

    // Handle user details update
    document.addEventListener('DOMContentLoaded', function() {
        const editUserForm = document.getElementById('editUserForm');
        if (editUserForm) {
            editUserForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonContent = submitButton.innerHTML;

                // Disable button and show loading
                submitButton.disabled = true;
                submitButton.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 inline animate-spin"></i> Updating...';
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                fetch(`<?= base_url('auth/users/update/') ?>${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'User updated successfully',
                            icon: 'success',
                            confirmButtonColor: '#3b82f6'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An error occurred while updating the user',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                })
                .finally(() => {
                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonContent;
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            });
        }

        // Handle password change
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonContent = submitButton.innerHTML;

                // Disable button and show loading
                submitButton.disabled = true;
                submitButton.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 inline animate-spin"></i> Processing...';
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                fetch(`<?= base_url('auth/users/change-password/') ?>${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'Password changed and email sent successfully',
                            icon: 'success',
                            confirmButtonColor: '#3b82f6'
                        }).then(() => {
                            changePasswordForm.reset();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to change password');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An error occurred while changing the password',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                })
                .finally(() => {
                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonContent;
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            });
        }
    });

    // Delete user function
    function deleteUser(userId) {
        Swal.fire({
            title: 'Delete User?',
            text: 'Are you sure you want to delete this user? This action cannot be undone and will permanently remove all user data.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete user',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the user account',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`<?= base_url('auth/users/delete/') ?>${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message || 'User has been deleted successfully',
                            icon: 'success',
                            confirmButtonColor: '#3b82f6'
                        }).then(() => {
                            window.location.href = '<?= base_url('auth/users') ?>';
                        });
                    } else {
                        throw new Error(data.message || 'Failed to delete user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An error occurred while deleting the user',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }

    // Format file size helper function
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Clear image preview function
    function clearImagePreview() {
        document.getElementById('profileImageInput').value = '';
        document.getElementById('uploadArea').classList.remove('hidden');
        document.getElementById('imagePreviewSection').classList.add('hidden');
        document.getElementById('uploadProfileImageBtn').disabled = true;

        // Reinitialize icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Upload profile image function
    function uploadProfileImage() {
        const fileInput = document.getElementById('profileImageInput');
        const file = fileInput.files[0];

        if (!file) {
            Swal.fire({
                title: 'No File Selected',
                text: 'Please select an image to upload',
                icon: 'warning',
                confirmButtonColor: '#f59e0b'
            });
            return;
        }

        const formData = new FormData();
        formData.append('profile_image', file);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        const uploadBtn = document.getElementById('uploadProfileImageBtn');
        const originalContent = uploadBtn.innerHTML;

        // Disable button and show loading
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 inline animate-spin"></i> Uploading...';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        fetch(`<?= base_url('auth/users/upload-profile-image/') ?>${userId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Profile picture uploaded successfully',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to upload profile picture');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An error occurred while uploading the image',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = originalContent;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }

    // Remove profile image function
    function removeProfileImage() {
        Swal.fire({
            title: 'Remove Profile Picture?',
            text: 'Are you sure you want to remove this user\'s profile picture?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Removing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`<?= base_url('auth/users/remove-profile-image/') ?>${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Removed!',
                            text: data.message || 'Profile picture removed successfully',
                            icon: 'success',
                            confirmButtonColor: '#3b82f6'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to remove profile picture');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An error occurred while removing the image',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
