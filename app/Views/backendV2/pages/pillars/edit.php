<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    $roles = model('RoleModel')->findAll();
    
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
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Edit Pillar',
        'pageDescription' => 'Update pillar information and settings',
        'breadcrumbs' => [
            ['label' => 'Pillars', 'url' => base_url('auth/pillars')],
            ['label' => 'Edit']
        ],
        'bannerActions' => '<a href="' . base_url('auth/pillars') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Pillars
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <!-- Pillar Form Section -->
        <div class="bg-white rounded-b-xl shadow-sm max-w-full pillar-form-container">
            <div class="w-full flex justify-between items-center p-6 border-b border-gray-200">
                <div class="">
                    <h3 class="text-2xl font-bold text-primary">Edit Pillar</h3>
                    <p class="text-gray-600">Update the details below to modify this pillar</p>
                </div>

                <div class="mt-4 md:mt-0">
                    <button onclick="window.location.href='<?= site_url('auth/pillars') ?>'" class="flex justify-center items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                        Back to Pillars
                    </button>
                </div>
            </div>

            <!-- Main Form -->
            <form class="p-6 space-y-6" autocomplete="off" id="editPillarForm" method="POST" action="<?= site_url('auth/pillars/' . $pillar['id'] . '/edit') ?>" enctype="multipart/form-data">
                <!-- Hidden field for pillar ID -->
                <input type="hidden" name="pillar_id" value="<?= esc($pillar['id']) ?>">

                <!-- Title Field -->
                <div>
                    <label for="title" class="block text-sm font-medium text-dark mb-1">Pillar Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" required maxlength="100" value="<?= esc($pillar['title'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter pillar title">
                </div>

                <!-- Slug Field -->
                <div>
                    <label for="pillarSlug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                    <div class="flex w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                            kewasnet.co.ke/pillars/
                        </span>
                        <input type="text" id="pillarSlug" name="slug" required maxlength="50" value="<?= esc($pillar['slug'] ?? '') ?>" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This will be the URL for your pillar page. It should be unique and descriptive.</p>
                </div>

                <!-- Icon Field -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-dark mb-1">Icon</label>
                    <input type="text" id="icon" name="icon" value="<?= esc($pillar['icon'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="e.g., fas fa-users, lucide-home, etc.">
                    <p class="mt-1 text-xs text-gray-500">Icon class name (FontAwesome, Lucide, etc.) to display with this pillar</p>
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block text-sm font-medium text-dark mb-1">Description</label>
                    <div class="relative">
                        <textarea id="description" name="description" rows="3" maxlength="500" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="A brief description of this pillar"><?= esc($pillar['description'] ?? '') ?></textarea>
                        <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                            <i data-lucide="file-text" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>Write a brief description that summarizes this pillar</span>
                        <span id="description-count"><?= strlen($pillar['description'] ?? '') ?>/500</span>
                    </div>
                </div>

                <!-- Content Editor -->
                <div>
                    <label for="content" class="block text-sm font-medium text-dark mb-1">Content</label>
                    <div class="rounded-lg">
                        <textarea id="content" name="content" class="summernote-editor w-full px-4 py-3 focus:outline-none" placeholder="Write detailed content about this pillar..."><?= esc($pillar['content'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-dark mb-1">Pillar Image</label>
                    <?php 
                    $imagePath = $pillar['image_path'] ?? null;
                    // Ensure image path is properly formatted
                    if (!empty($imagePath)) {
                        // If it's already a full URL, use it as is
                        if (strpos($imagePath, 'http') === 0 || strpos($imagePath, '//') === 0) {
                            // Already a full URL
                        } elseif (strpos($imagePath, '/') === 0) {
                            // Absolute path from root
                            $imagePath = base_url(ltrim($imagePath, '/'));
                        } else {
                            // Relative path
                            $imagePath = base_url($imagePath);
                        }
                    }
                    ?>
                    <?php if (!empty($imagePath) && !empty($pillar['image_path'])): ?>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                            <div class="relative inline-block group">
                                <img src="<?= esc($imagePath) ?>" alt="Current Pillar Image" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200 object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display: none;" class="text-sm text-red-500">Image not found</div>
                                <button type="button" onclick="removePillarImage('<?= esc($pillar['id']) ?>')" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200" title="Remove image">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <button type="button" onclick="removePillarImage('<?= esc($pillar['id']) ?>')" class="mt-2 text-sm text-red-600 hover:text-red-700 flex items-center gap-1">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Remove Image
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600">
                                <label for="pillar_image" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none">
                                    <span><?= !empty($pillar['image_path']) ? 'Replace image' : 'Upload a pillar image' ?></span>
                                    <input id="pillar_image" name="pillar_image" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500">PNG, JPG, GIF up to 5MB</p>
                            <div id="pillar-image-preview" class="mt-4"></div>
                        </div>
                    </div>
                </div>

                <!-- Button Configuration -->
                <div class="pt-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Button Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="button_text" class="block text-sm font-medium text-dark mb-1">Button Text</label>
                            <input type="text" id="button_text" name="button_text" value="<?= esc($pillar['button_text'] ?? 'Explore Resources') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Button text">
                        </div>
                        
                        <div>
                            <label for="button_link" class="block text-sm font-medium text-dark mb-1">Button Link</label>
                            <input type="url" id="button_link" name="button_link" value="<?= esc($pillar['button_link'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="https://example.com">
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="pt-6">
                    <h3 class="text-lg font-medium text-dark mb-4">SEO Settings</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-dark mb-1">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" value="<?= esc($pillar['meta_title'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Title for search engines">
                        </div>
                        
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-dark mb-1">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" rows="3" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Description for search engines"><?= esc($pillar['meta_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Visibility and Status Options -->
                <div class="pt-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Visibility & Status</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Privacy</label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_private" value="0" <?= (!isset($pillar['is_private']) || $pillar['is_private'] == 0) ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Public</span>
                                        <p class="text-xs text-gray-500">Visible to all users</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_private" value="1" <?= (isset($pillar['is_private']) && $pillar['is_private'] == 1) ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Private</span>
                                        <p class="text-xs text-gray-500">Only visible to logged-in users</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Status</label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_active" value="1" <?= (!isset($pillar['is_active']) || $pillar['is_active'] == 1) ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Active</span>
                                        <p class="text-xs text-gray-500">Pillar is live and visible</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_active" value="0" <?= (isset($pillar['is_active']) && $pillar['is_active'] == 0) ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Inactive</span>
                                        <p class="text-xs text-gray-500">Pillar is hidden from display</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CSRF Token for security -->
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-borderColor">
                    <button type="button" onclick="window.location.href='<?= site_url('auth/pillars') ?>'" class="px-8 py-3 rounded-[50px] text-primary bg-primaryShades-50 hover:bg-primaryShades-100">
                        Cancel
                    </button>
                    <button type="submit" class="gradient-btn rounded-[50px] px-8 text-white flex justify-center items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5 z-10"></i>
                        <span>Update Pillar</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Progress Modal -->
        <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex flex-col items-center">
                    <!-- Animated spinner -->
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4"></div>
                    
                    <!-- Progress text -->
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Updating Pillar</h3>
                    <p class="text-sm text-gray-500 mb-4">Please wait while we process your changes...</p>
                    
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                        <div id="uploadProgress" class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                    
                    <!-- Progress percentage -->
                    <p class="text-sm font-medium text-gray-700">
                        <span id="progressPercent">0</span>% complete
                    </p>

                    <button id="cancelUploadBtn" type="button" class="mt-4 px-4 py-2 border border-red-500 text-red-500 rounded-md hover:bg-red-50 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize lucide icons
        lucide.createIcons();

        // Initialize Summernote editor
        if (typeof $('.summernote-editor').summernote === 'function') {
            $('.summernote-editor').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        // Helper function to clear field errors
        function clearFieldError(field) {
            field.removeClass('is-invalid border-red-500');
            field.next('.invalid-feedback').remove();
        }

        // Helper function to generate slug
        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .replace(/^-+|-+$/g, '')
                .trim();
        }

        // Auto-generate slug from title (only if slug is empty)
        $('#title').on('input', function() {
            clearFieldError($(this));
            
            const $slugField = $('#pillarSlug');
            if (!$slugField.val() || !$slugField.data('manual')) {
                const title = $(this).val();
                const slug = generateSlug(title);
                $slugField.val(slug);
            }
        });

        // Mark slug as manually edited when user types in it
        $('#pillarSlug').on('input', function() {
            clearFieldError($(this));
            $(this).data('manual', true);
        });

        // Description character counter
        const initialDescriptionLength = $('#description').val().length;
        $('#description-count').text(`${initialDescriptionLength}/500`);

        $('#description').on('input', function() {
            const currentLength = $(this).val().length;
            const maxLength = 500;
            const remaining = maxLength - currentLength;
            const counter = $('#description-count');
            
            counter.text(`${currentLength}/500`);
            
            counter.removeClass('text-gray-500 text-orange-500 text-red-500');
            if (remaining <= 50) {
                counter.addClass('text-orange-500');
            } else if (remaining <= 20) {
                counter.removeClass('text-orange-500').addClass('text-red-500');
            } else {
                counter.addClass('text-gray-500');
            }
            
            if (remaining <= 20 && remaining > 0) {
                if (!$('.description-warning').length) {
                    $(this).parent().parent().append(`
                        <div class="description-warning text-xs text-yellow-600 mt-1">
                            <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                            Only ${remaining} characters remaining
                        </div>
                    `);
                    lucide.createIcons();
                }
            } else if (remaining === 0) {
                $('.description-warning').remove();
                $(this).parent().parent().append(`
                    <div class="description-warning text-xs text-red-600 mt-1">
                        <i data-lucide="alert-circle" class="w-3 h-3 inline mr-1"></i>
                        Character limit reached
                    </div>
                `);
                lucide.createIcons();
            } else {
                $('.description-warning').remove();
            }
            
            clearFieldError($(this));
        });

        // Image upload functionality
        $('#pillar_image').on('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = $('#pillar-image-preview');
            previewContainer.empty();
            
            if (file && file.type.startsWith('image/')) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Image must be less than 5MB',
                        confirmButtonColor: '#ef4444'
                    });
                    $(this).val('');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = $(`
                        <div class="file-preview mt-2">
                            <img src="${e.target.result}" alt="Pillar Image Preview" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200">
                            <div class="remove-file cursor-pointer" onclick="removePillarImagePreview()">×</div>
                        </div>
                    `);
                    previewContainer.append(preview);
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove pillar image function (for file input preview)
        window.removePillarImagePreview = function() {
            $('#pillar_image').val('');
            $('#pillar-image-preview').empty();
        }

        // Remove existing pillar image from server
        window.removePillarImage = function(pillarId) {
            Swal.fire({
                title: 'Remove Image?',
                text: 'Are you sure you want to remove this image? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Removing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= site_url('auth/pillars') ?>/${pillarId}/remove-image`,
                        type: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: {
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removed!',
                                    text: response.message || 'Image removed successfully',
                                    confirmButtonColor: '#3b82f6',
                                    timer: 2000,
                                    showConfirmButton: true
                                }).then(() => {
                                    // Reload the page to show updated state
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'Failed to remove image',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON || {};
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'An error occurred while removing the image',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    });
                }
            });
        }

        // Handle form submission
        $('#editPillarForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = $('button[type="submit"]');
            const originalText = submitBtn.html();
            
            // Show progress modal
            const progressModal = $('#progressModal');
            const progressBar = $('#uploadProgress');
            const progressPercent = $('#progressPercent');
            
            progressModal.show();
            
            // Disable form elements during submission
            form.find('input, textarea, button, select').prop('disabled', true);
            submitBtn.html('<i class="animate-spin w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full inline-block"></i>Updating...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.css('width', percent + '%');
                            progressPercent.text(percent);
                        }
                    }, false);
                    
                    return xhr;
                },
                success: function(response) {
                    if (response.status === 'success') {
                        progressBar.css('width', '100%');
                        progressPercent.text('100');
                        progressModal.hide();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Pillar updated successfully!',
                            confirmButtonColor: '#3b82f6',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                window.location.href = '<?= site_url('auth/pillars') ?>';
                            }
                        });
                    } else {
                        progressModal.hide();
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'An error occurred',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                        
                        if (response.errors) {
                            showFormErrors(response.errors);
                        }
                    }
                },
                error: function(xhr) {
                    progressModal.hide();
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.errors) {
                            showFormErrors(response.errors);
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please correct the errors and try again',
                                confirmButtonColor: '#ef4444'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'An error occurred while processing your request',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    } catch (e) {
                        let errorMessage = 'A network error occurred. Please check your connection and try again.';
                        if (xhr.status === 413) {
                            errorMessage = 'The uploaded file is too large. Please reduce file size and try again.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                },
                complete: function() {
                    form.find('input, textarea, button, select').prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });

        // Function to show form errors
        function showFormErrors(errors) {
            clearFormErrors();
            
            $.each(errors, function(fieldName, message) {
                const $field = $('[name="' + fieldName + '"]');
                
                if ($field.length) {
                    $field.addClass('is-invalid border-red-500');
                    
                    const $errorDiv = $('<div>', {
                        class: 'invalid-feedback text-red-500 text-xs mt-1',
                        text: message
                    });
                    
                    $field.after($errorDiv);
                    
                    if ($('.is-invalid').first().is($field)) {
                        $field.focus();
                        $('html, body').animate({
                            scrollTop: $field.offset().top - 100
                        }, 500);
                    }
                }
            });
        }

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid border-red-500');
            $('.invalid-feedback').remove();
        }

        // Clear field errors on input
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('is-invalid border-red-500');
            $(this).next('.invalid-feedback').remove();
        });
    });
</script>
<?= $this->endSection() ?>

