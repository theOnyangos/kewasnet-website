<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?? 'Edit Profile' ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Edit Profile',
        'pageDescription' => 'Update your profile information and settings',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'My Profile', 'url' => base_url('auth/profile')],
            ['label' => 'Edit']
        ],
        'bannerActions' => '<a href="' . base_url('auth/profile') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Profile
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Profile Information</h2>
                <p class="mt-1 text-sm text-slate-500">Update your personal information and profile settings</p>
            </div>

            <form id="editProfileForm" enctype="multipart/form-data">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Personal Information -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="<?= esc($user['first_name']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Enter first name">
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="<?= esc($user['last_name']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Enter last name">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="<?= esc($user['email']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="user@example.com">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="<?= esc($user['phone'] ?? '') ?>"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="+254 700 000 000">
                        </div>

                        <div class="md:col-span-2">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                Bio
                            </label>
                            <textarea id="bio"
                                      name="bio"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                      placeholder="Enter bio (optional)"><?= esc($user['bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Profile Image Section -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h3>
                    <p class="text-sm text-gray-600 mb-4">Upload a profile picture for the user. Recommended size: 400x400px</p>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <!-- Preview -->
                        <div class="flex flex-col items-center">
                            <div class="relative group">
                                <div id="profilePreviewContainer" class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 shadow-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                    <?php if (!empty($user['picture'])): ?>
                                        <img id="currentProfileImage"
                                             src="<?= base_url(esc($user['picture'])) ?>"
                                             alt="Profile Picture"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div id="initialsPreview" class="w-full h-full flex items-center justify-center text-white text-4xl font-bold">
                                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <img id="profileImagePreview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 text-center">Profile Preview</p>
                        </div>

                        <!-- Upload Section -->
                        <div class="flex-1">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-primary transition-colors duration-300">
                                <input type="file"
                                       id="picture"
                                       name="picture"
                                       accept="image/*"
                                       class="hidden">

                                <div id="uploadArea" class="text-center cursor-pointer" onclick="document.getElementById('picture').click()">
                                    <div class="flex justify-center mb-3">
                                        <div class="w-16 h-16 rounded-full bg-primary bg-opacity-10 flex items-center justify-center">
                                            <i data-lucide="upload" class="w-8 h-8 text-primary"></i>
                                        </div>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-1">Click to upload profile picture</h4>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                </div>

                                <!-- File Info Section (Hidden by default) -->
                                <div id="fileInfoSection" class="hidden">
                                    <div class="flex items-center gap-4">
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
                        </div>
                    </div>
                </div>

                <!-- Profile Cover Image Section -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Cover Image</h3>
                    <p class="text-sm text-gray-600 mb-4">Upload a cover image for your profile. Recommended size: 1200x400px</p>

                    <div class="flex flex-col gap-6">
                        <!-- Current Cover Image -->
                        <?php if (!empty($user['profile_cover_image'])): ?>
                            <div class="relative">
                                <img id="currentCoverImage" src="<?= base_url(esc($user['profile_cover_image'])) ?>" alt="Cover Image" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200 shadow-sm">
                                <p class="mt-2 text-xs text-gray-500">Current Cover Image</p>
                            </div>
                        <?php endif; ?>

                        <!-- Upload Section -->
                        <div>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 hover:border-primary transition-colors duration-300">
                                <input type="file"
                                       id="profile_cover_image"
                                       name="profile_cover_image"
                                       accept="image/*"
                                       class="hidden">

                                <div id="coverUploadArea" class="text-center cursor-pointer" onclick="document.getElementById('profile_cover_image').click()">
                                    <div class="flex justify-center mb-4">
                                        <div class="w-20 h-20 rounded-lg bg-primary bg-opacity-10 flex items-center justify-center">
                                            <i data-lucide="upload" class="w-10 h-10 text-primary"></i>
                                        </div>
                                    </div>
                                    <h4 class="text-base font-medium text-gray-900 mb-2">Click to upload cover image</h4>
                                    <p class="text-sm text-gray-500 mb-1">PNG, JPG, JPEG up to 5MB</p>
                                    <p class="text-xs text-gray-400">Recommended dimensions: 1200x400px</p>
                                </div>

                                <!-- File Info Section (Hidden by default) -->
                                <div id="coverFileInfoSection" class="hidden">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0">
                                                <img id="coverPreviewImage" src="" alt="Cover Preview" class="w-32 h-20 object-cover rounded-lg border-2 border-primary shadow-sm">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p id="coverFileName" class="text-sm font-medium text-gray-900 truncate"></p>
                                                <p id="coverFileSize" class="text-xs text-gray-500"></p>
                                                <p class="text-xs text-gray-400 mt-1">Recommended: 1200x400px</p>
                                            </div>
                                            <button type="button"
                                                    onclick="clearCoverImagePreview()"
                                                    class="flex-shrink-0 p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <i data-lucide="x" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= base_url('auth/profile') ?>"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            id="submitBtn"
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        <span id="submitText">Update Profile</span>
                        <i data-lucide="loader" id="submitLoader" class="w-4 h-4 ml-2 hidden animate-spin"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let selectedFile = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Profile image upload handler
        const profileImageInput = document.getElementById('picture');
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

                    selectedFile = file;

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const previewImg = document.getElementById('profileImagePreview');
                        const initialsPreview = document.getElementById('initialsPreview');
                        const currentImg = document.getElementById('currentProfileImage');

                        if (previewImg && initialsPreview) {
                            previewImg.src = event.target.result;
                            previewImg.classList.remove('hidden');
                            initialsPreview.classList.add('hidden');
                        } else if (currentImg) {
                            currentImg.src = event.target.result;
                        }

                        const fileName = document.getElementById('fileName');
                        const fileSize = document.getElementById('fileSize');
                        if (fileName) fileName.textContent = file.name;
                        if (fileSize) fileSize.textContent = formatFileSize(file.size);

                        // Show file info section and hide upload area
                        const uploadArea = document.getElementById('uploadArea');
                        const fileInfoSection = document.getElementById('fileInfoSection');
                        if (uploadArea) uploadArea.classList.add('hidden');
                        if (fileInfoSection) fileInfoSection.classList.remove('hidden');

                        // Reinitialize icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Cover image upload handler
        const coverImageInput = document.getElementById('profile_cover_image');
        let selectedCoverFile = null;
        
        if (coverImageInput) {
            coverImageInput.addEventListener('change', function(e) {
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

                    selectedCoverFile = file;

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const previewImg = document.getElementById('coverPreviewImage');
                        const coverUploadArea = document.getElementById('coverUploadArea');
                        const coverFileInfoSection = document.getElementById('coverFileInfoSection');
                        const currentCoverImg = document.getElementById('currentCoverImage');

                        // Update preview image
                        if (previewImg) {
                            previewImg.src = event.target.result;
                        }

                        // Update file info
                        const coverFileName = document.getElementById('coverFileName');
                        const coverFileSize = document.getElementById('coverFileSize');
                        if (coverFileName) coverFileName.textContent = file.name;
                        if (coverFileSize) coverFileSize.textContent = formatFileSize(file.size);

                        // Show file info section and hide upload area
                        if (coverUploadArea) coverUploadArea.classList.add('hidden');
                        if (coverFileInfoSection) coverFileInfoSection.classList.remove('hidden');

                        // Hide current cover image if it exists (new upload takes precedence)
                        if (currentCoverImg && currentCoverImg.parentElement) {
                            currentCoverImg.parentElement.classList.add('hidden');
                        }

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

    // Clear image preview function
    function clearImagePreview() {
        selectedFile = null;
        const profileImageInput = document.getElementById('picture');
        if (profileImageInput) profileImageInput.value = '';

        const uploadArea = document.getElementById('uploadArea');
        const fileInfoSection = document.getElementById('fileInfoSection');
        if (uploadArea) uploadArea.classList.remove('hidden');
        if (fileInfoSection) fileInfoSection.classList.add('hidden');

        const previewImg = document.getElementById('profileImagePreview');
        const initialsPreview = document.getElementById('initialsPreview');
        if (previewImg && initialsPreview) {
            previewImg.classList.add('hidden');
            initialsPreview.classList.remove('hidden');
            initialsPreview.innerHTML = '<i data-lucide="user" class="w-16 h-16"></i>';
        }

        // Reinitialize icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Clear cover image preview function
    function clearCoverImagePreview() {
        selectedCoverFile = null;
        const coverImageInput = document.getElementById('profile_cover_image');
        if (coverImageInput) coverImageInput.value = '';

        const coverUploadArea = document.getElementById('coverUploadArea');
        const coverFileInfoSection = document.getElementById('coverFileInfoSection');
        if (coverUploadArea) coverUploadArea.classList.remove('hidden');
        if (coverFileInfoSection) coverFileInfoSection.classList.add('hidden');

        // Show current cover image again if it exists
        const currentCoverImg = document.getElementById('currentCoverImage');
        if (currentCoverImg && currentCoverImg.parentElement) {
            currentCoverImg.parentElement.classList.remove('hidden');
        }

        // Reinitialize icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Format file size helper function
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Handle form submission
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const formData = new FormData(this);

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 inline animate-spin"></i> Updating...';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        fetch('<?= base_url('auth/profile/update') ?>', {
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
                    text: data.message || 'Profile updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    window.location.href = '<?= base_url('auth/profile') ?>';
                });
            } else {
                throw new Error(data.message || 'Failed to update profile');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            let errorMessage = 'An error occurred while updating the profile';
            if (error.message) {
                errorMessage = error.message;
            }

            Swal.fire({
                title: 'Error!',
                text: errorMessage,
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="save" class="w-4 h-4 mr-2"></i> <span id="submitText">Update Profile</span>';
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    });

    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
<?= $this->endSection() ?>