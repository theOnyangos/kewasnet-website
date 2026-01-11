<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Edit Leadership Team Member',
        'pageDescription' => 'Update leadership team member information',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Leadership Team', 'url' => base_url('auth/leadership')],
            ['label' => 'Edit']
        ],
        'bannerActions' => '<a href="' . base_url('auth/leadership') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Leadership
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Member Information</h2>
                <p class="mt-1 text-sm text-slate-500">Update leadership team member details</p>
            </div>

            <form id="editLeadershipForm" enctype="multipart/form-data">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Personal Information -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Member Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="<?= esc($member['name']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Enter full name">
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                Position <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="position"
                                   name="position"
                                   value="<?= esc($member['position']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Chief Executive Officer">
                        </div>

                        <div>
                            <label for="experience" class="block text-sm font-medium text-gray-700 mb-2">
                                Experience
                            </label>
                            <input type="text"
                                   id="experience"
                                   name="experience"
                                   value="<?= esc($member['experience'] ?? '') ?>"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="15+ years">
                        </div>

                        <div>
                            <label for="order_index" class="block text-sm font-medium text-gray-700 mb-2">
                                Display Order <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="order_index"
                                   name="order_index"
                                   value="<?= esc($member['order_index'] ?? 0) ?>"
                                   required
                                   min="0"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="0">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    required
                                    class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="active" <?= ($member['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($member['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                      placeholder="Enter member description (optional)"><?= esc($member['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Social Media Links</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Social Media Profiles</label>
                        <p class="mb-4 text-xs text-gray-500">Add social media links (LinkedIn, Twitter, Facebook, etc.). Click "Add Link" to add more platforms.</p>

                        <div id="socialMediaContainer" class="space-y-3 mb-4">
                            <?php
                            // Parse existing social media data
                            $existingSocialMedia = [];
                            if (!empty($member['social_media'])) {
                                if (is_string($member['social_media'])) {
                                    $decoded = json_decode($member['social_media'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $existingSocialMedia = $decoded;
                                    }
                                } elseif (is_array($member['social_media'])) {
                                    $existingSocialMedia = $member['social_media'];
                                }
                            }
                            // If no social media exists, show one empty field
                            if (empty($existingSocialMedia)) {
                                $existingSocialMedia = [['platform' => '', 'url' => '']];
                            }
                            
                            foreach ($existingSocialMedia as $index => $social):
                            ?>
                            <div class="social-media-item grid grid-cols-1 md:grid-cols-2 gap-3 items-start" data-index="<?= $index ?>">
                                <div>
                                    <select name="social_media_platform[]" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                        <option value="">Select Platform</option>
                                        <option value="LinkedIn" <?= ($social['platform'] ?? '') === 'LinkedIn' ? 'selected' : '' ?>>LinkedIn</option>
                                        <option value="github" <?= ($social['platform'] ?? '') === 'github' ? 'selected' : '' ?>>GitHub</option>
                                        <option value="Facebook" <?= ($social['platform'] ?? '') === 'Facebook' ? 'selected' : '' ?>>Facebook</option>
                                        <option value="Instagram" <?= ($social['platform'] ?? '') === 'Instagram' ? 'selected' : '' ?>>Instagram</option>
                                        <option value="YouTube" <?= ($social['platform'] ?? '') === 'YouTube' ? 'selected' : '' ?>>YouTube</option>
                                        <option value="WhatsApp" <?= ($social['platform'] ?? '') === 'WhatsApp' ? 'selected' : '' ?>>WhatsApp</option>
                                        <option value="Website" <?= ($social['platform'] ?? '') === 'Website' ? 'selected' : '' ?>>Website</option>
                                        <option value="X" <?= ($social['platform'] ?? '') === 'X' ? 'selected' : '' ?>>X</option>
                                        <option value="Other" <?= ($social['platform'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="flex gap-2">
                                    <input type="url" name="social_media_url[]" value="<?= esc($social['url'] ?? '') ?>" placeholder="https://..." class="flex-1 px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                    <button type="button" class="remove-social-media-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors flex-shrink-0" style="<?= count($existingSocialMedia) > 1 ? '' : 'display: none;' ?>" title="Remove this link">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" id="addSocialMediaBtn" class="px-4 py-2 bg-secondary/10 border border-secondary rounded-lg text-secondary hover:bg-secondary/20 transition-colors text-sm font-medium inline-flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add Link
                        </button>
                    </div>
                </div>

                <!-- Profile Image Section -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h3>
                    <p class="text-sm text-gray-600 mb-4">Update the member's profile picture. Recommended size: 400x400px</p>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <!-- Current Profile Image -->
                        <div class="flex flex-col items-center">
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 shadow-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                    <?php if (!empty($member['image'])): ?>
                                        <img id="currentProfileImage"
                                             src="<?= base_url(esc($member['image'])) ?>"
                                             alt="Profile Picture"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div id="currentProfileImage" class="w-full h-full flex items-center justify-center text-white text-4xl font-bold">
                                            <i data-lucide="user" class="w-16 h-16"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 text-center">Current Picture</p>
                        </div>

                        <!-- Upload Section -->
                        <div class="flex-1">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-primary transition-colors duration-300">
                                <input type="file"
                                       id="image"
                                       name="image"
                                       accept="image/*"
                                       class="hidden">

                                <div id="uploadArea" class="text-center cursor-pointer" onclick="document.getElementById('image').click()">
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
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= base_url('auth/leadership') ?>"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            id="submitBtn"
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        <span id="submitText">Update Member</span>
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
        const profileImageInput = document.getElementById('image');
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
                        document.getElementById('imagePreview').src = event.target.result;
                        document.getElementById('fileName').textContent = file.name;
                        document.getElementById('fileSize').textContent = formatFileSize(file.size);

                        // Show preview section and hide upload area
                        document.getElementById('uploadArea').classList.add('hidden');
                        document.getElementById('imagePreviewSection').classList.remove('hidden');

                        // Reinitialize icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Social Media Links Management
        let socialMediaCount = <?= count($existingSocialMedia ?? [['platform' => '', 'url' => '']]) ?>;

        // Initialize Select2 for existing social media selects
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumResultsForSearch: Infinity
            });
        }

        // Add new social media link field
        const addSocialMediaBtn = document.getElementById('addSocialMediaBtn');
        if (addSocialMediaBtn) {
            addSocialMediaBtn.addEventListener('click', function() {
                const socialMediaHtml = `
                    <div class="social-media-item grid grid-cols-1 md:grid-cols-2 gap-3 items-start" data-index="${socialMediaCount}">
                        <div>
                            <select name="social_media_platform[]" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="">Select Platform</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Twitter">Twitter</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Instagram">Instagram</option>
                                <option value="YouTube">YouTube</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Website">Website</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <input type="url" name="social_media_url[]" value="" placeholder="https://..." class="flex-1 px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                            <button type="button" class="remove-social-media-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors flex-shrink-0" title="Remove this link">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                `;
                const container = document.getElementById('socialMediaContainer');
                container.insertAdjacentHTML('beforeend', socialMediaHtml);
                socialMediaCount++;

                // Initialize Select2 for the new select
                if (typeof $.fn.select2 !== 'undefined') {
                    $(container.querySelectorAll('.select2').item(container.querySelectorAll('.select2').length - 1)).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        minimumResultsForSearch: Infinity
                    });
                }

                // Show all remove buttons if more than one link
                if (container.querySelectorAll('.social-media-item').length > 1) {
                    container.querySelectorAll('.remove-social-media-btn').forEach(btn => {
                        btn.style.display = 'block';
                    });
                }

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        }

        // Remove social media link field
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-social-media-btn')) {
                const btn = e.target.closest('.remove-social-media-btn');
                const item = btn.closest('.social-media-item');
                const select2Instance = $(item).find('.select2').select2('destroy');
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    const container = document.getElementById('socialMediaContainer');
                    if (container.querySelectorAll('.social-media-item').length <= 1) {
                        container.querySelectorAll('.remove-social-media-btn').forEach(btn => {
                            btn.style.display = 'none';
                        });
                    }
                }, 300);
            }
        });
    });

    // Clear image preview function
    function clearImagePreview() {
        selectedFile = null;
        document.getElementById('image').value = '';
        document.getElementById('uploadArea').classList.remove('hidden');
        document.getElementById('imagePreviewSection').classList.add('hidden');

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
    document.getElementById('editLeadershipForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const formData = new FormData(this);

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 inline animate-spin"></i> Updating...';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        fetch('<?= base_url('auth/leadership/update/' . $member['id']) ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Member updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    window.location.href = '<?= base_url('auth/leadership') ?>';
                });
            } else {
                throw new Error(data.message || 'Failed to update member');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            let errorMessage = 'An error occurred while updating the member';
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
            submitBtn.innerHTML = '<i data-lucide="save" class="w-4 h-4 mr-2"></i> <span id="submitText">Update Member</span>';
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