<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?? 'Create New User' ?>
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
                            <span class="ml-1 text-sm font-medium text-white md:ml-2">Create User</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Title -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 drop-shadow-lg">
                        Create New User
                    </h1>
                    <p class="text-lg text-white/90">
                        Add a new user to the system with their profile information
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

        <!-- Create Form -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">User Information</h2>
                <p class="mt-1 text-sm text-slate-500">Fill in the details to create a new user account</p>
            </div>

            <form id="createUserForm" enctype="multipart/form-data">
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
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="+254 700 000 000">
                        </div>

                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                                User Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role_id"
                                    name="role_id"
                                    required
                                    class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="">Select Role</option>
                                <?php if (!empty($roles)): ?>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['role_id'] ?? $role['id'] ?>"><?= esc($role['role_name'] ?? $role['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                Bio
                            </label>
                            <textarea id="bio"
                                      name="bio"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                      placeholder="Enter user bio (optional)"></textarea>
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
                                    <div id="initialsPreview" class="w-full h-full flex items-center justify-center text-white text-4xl font-bold">
                                        <i data-lucide="user" class="w-16 h-16"></i>
                                    </div>
                                    <img id="profileImagePreview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 text-center">Profile Preview</p>
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

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= base_url('auth/users') ?>"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            id="submitBtn"
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300 flex items-center">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<!-- JavaScript Section -->
<?= $this->section('scripts') ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let selectedFile = null;

    document.addEventListener('DOMContentLoaded', function() {
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

                    selectedFile = file;

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const previewImg = document.getElementById('profileImagePreview');
                        const initialsPreview = document.getElementById('initialsPreview');

                        previewImg.src = event.target.result;
                        previewImg.classList.remove('hidden');
                        initialsPreview.classList.add('hidden');

                        document.getElementById('fileName').textContent = file.name;
                        document.getElementById('fileSize').textContent = formatFileSize(file.size);

                        // Show file info section and hide upload area
                        document.getElementById('uploadArea').classList.add('hidden');
                        document.getElementById('fileInfoSection').classList.remove('hidden');

                        // Reinitialize icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Update preview when name fields change
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');

        function updateInitialsPreview() {
            const firstName = firstNameInput.value.trim();
            const lastName = lastNameInput.value.trim();
            const initialsPreview = document.getElementById('initialsPreview');

            if (!selectedFile && firstName && lastName) {
                const initials = firstName.charAt(0).toUpperCase() + lastName.charAt(0).toUpperCase();
                initialsPreview.innerHTML = `<span class="text-4xl font-bold text-white">${initials}</span>`;
            }
        }

        if (firstNameInput) firstNameInput.addEventListener('input', updateInitialsPreview);
        if (lastNameInput) lastNameInput.addEventListener('input', updateInitialsPreview);
    });

    // Clear image preview function
    function clearImagePreview() {
        selectedFile = null;
        document.getElementById('profileImageInput').value = '';
        document.getElementById('uploadArea').classList.remove('hidden');
        document.getElementById('fileInfoSection').classList.add('hidden');

        const previewImg = document.getElementById('profileImagePreview');
        const initialsPreview = document.getElementById('initialsPreview');

        previewImg.classList.add('hidden');
        initialsPreview.classList.remove('hidden');
        initialsPreview.innerHTML = '<i data-lucide="user" class="w-16 h-16"></i>';

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
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const formData = new FormData(this);

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 inline animate-spin"></i> Creating User...';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        fetch('<?= base_url('auth/users/create') ?>', {
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
                    text: data.message || 'User created successfully',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    window.location.href = '<?= base_url('auth/users') ?>';
                });
            } else {
                throw new Error(data.message || 'Failed to create user');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            let errorMessage = 'An error occurred while creating the user';
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
            submitBtn.innerHTML = '<i data-lucide="user-plus" class="w-4 h-4 mr-2"></i> Create User';
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
