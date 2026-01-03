<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Create New Program',
            'pageDescription' => 'Fill in the details below to create a new KEWASNET program',
            'breadcrumbs' => [
                ['label' => 'Programs', 'url' => base_url('auth/programs')],
                ['label' => 'Create']
            ],
            'bannerActions' => '<a href="' . base_url('auth/programs') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Programs
            </a>'
        ]) ?>

        <div class="bg-white rounded-b-xl shadow-sm max-w-full program-form-container mx-5">
            <!-- Main Form -->
            <form class="p-6 space-y-6" id="createProgramForm" method="POST" action="<?= site_url('auth/programs/create') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- Title Field -->
                <div>
                    <label for="title" class="block text-sm font-medium text-dark mb-1">Program Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter program title" value="<?= old('title') ?>">
                    <?php if (session('errors.title')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.title') ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">This will be the main title of your program</p>
                </div>

                <!-- Slug Field -->
                <div>
                    <label for="programSlug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                    <div class="flex w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                            kewasnet.co.ke/programs/
                        </span>
                        <input type="text" id="programSlug" name="slug" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug" value="<?= old('slug') ?>">
                    </div>
                    <?php if (session('errors.slug')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.slug') ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">This will be the URL for your program. It should be unique and descriptive.</p>
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block text-sm font-medium text-dark mb-1">Short Description <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <textarea id="description" name="description" rows="3" maxlength="500" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="A brief overview of the program"><?= old('description') ?></textarea>
                        <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                            <i data-lucide="file-text" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                        </div>
                    </div>
                    <?php if (session('errors.description')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.description') ?></p>
                    <?php endif; ?>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Write a compelling summary that will appear in program listings</span>
                        <span id="description-count">0/500</span>
                    </div>
                </div>

                <!-- Program Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-dark mb-1">Program Content <span class="text-red-500">*</span></label>
                    <div class="rounded-lg">
                        <textarea id="content" name="content" class="summernote-editor w-full px-4 py-3 focus:outline-none" placeholder="Write detailed program content here..."><?= old('content') ?></textarea>
                    </div>
                    <?php if (session('errors.content')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.content') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Icon SVG Field -->
                <div>
                    <label for="icon_svg" class="block text-sm font-medium text-dark mb-1">Program Icon (SVG Code)</label>
                    <div class="relative">
                        <textarea id="icon_svg" name="icon_svg" rows="4" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent font-mono text-sm" placeholder="<svg>...</svg>"><?= old('icon_svg', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>') ?></textarea>
                        <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                            <i data-lucide="code" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                        </div>
                    </div>
                    <?php if (session('errors.icon_svg')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.icon_svg') ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">Paste SVG code for the program icon. Leave empty for default icon.</p>
                </div>

                <!-- Background Color -->
                <div>
                    <label for="background_color" class="block text-sm font-medium text-dark mb-1">Background Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="background_color" name="background_color" class="h-12 w-20 border border-borderColor rounded-lg cursor-pointer" value="<?= old('background_color', '#3B82F6') ?>">
                        <input type="text" id="background_color_text" class="flex-1 px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="#3B82F6" value="<?= old('background_color', '#3B82F6') ?>">
                    </div>
                    <?php if (session('errors.background_color')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.background_color') ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">Choose a background color for the program card</p>
                </div>

                <!-- Featured Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-dark mb-1">Program Image</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600">
                                <label for="image_url" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none">
                                    <span>Upload a program image</span>
                                    <input id="image_url" name="image_url" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500">PNG, JPG, GIF up to 2MB</p>
                            <div id="image-preview" class="mt-4"></div>
                        </div>
                    </div>
                    <?php if (session('errors.image_url')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.image_url') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-dark mb-1">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" min="0" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="0" value="<?= old('sort_order', 0) ?>">
                    <?php if (session('errors.sort_order')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.sort_order') ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">Lower numbers appear first in the program list</p>
                </div>

                <!-- SEO Section -->
                <div class="pt-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-dark">SEO Settings</h3>
                        <p class="text-sm text-gray-500">Optimize your program for search engines by providing relevant metadata.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-dark mb-1">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Title for search engines" value="<?= old('meta_title') ?>">
                            <?php if (session('errors.meta_title')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('errors.meta_title') ?></p>
                            <?php endif; ?>
                            <p class="mt-1 text-xs text-gray-500">This will be the title displayed in search engine results</p>
                        </div>
                        
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-dark mb-1">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" rows="3" maxlength="500" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="Description for search engines"><?= old('meta_description') ?></textarea>
                            <?php if (session('errors.meta_description')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('errors.meta_description') ?></p>
                            <?php endif; ?>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>This will be the description displayed in search engine results</span>
                                <span id="meta-description-count">0/500</span>
                            </div>
                        </div>
                        
                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-dark mb-1">Meta Keywords</label>
                            <input type="text" id="meta_keywords" name="meta_keywords" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Comma-separated keywords" value="<?= old('meta_keywords') ?>">
                            <?php if (session('errors.meta_keywords')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('errors.meta_keywords') ?></p>
                            <?php endif; ?>
                            <p class="mt-1 text-xs text-gray-500">These will be the keywords associated with your program</p>
                        </div>
                    </div>
                </div>

                <!-- Status and Options -->
                <div class="pt-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-dark">Program Options</h3>
                        <p class="text-sm text-gray-500">Control the visibility and features of your program.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Active Status -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Status</label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_active" value="1" <?= old('is_active', '1') == '1' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Active</span>
                                        <p class="text-xs text-gray-500">Program is visible to public</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_active" value="0" <?= old('is_active') == '0' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Inactive</span>
                                        <p class="text-xs text-gray-500">Program is hidden from public</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Featured Status -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Featured Program</label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="checkbox" name="is_featured" value="1" <?= old('is_featured') ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor rounded">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Make this program featured</span>
                                        <p class="text-xs text-gray-500">Featured programs appear prominently on the homepage</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row sm:justify-between items-center pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                    <button type="button" onclick="window.location.href='<?= site_url('auth/programs') ?>'" class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                        <button type="submit" name="action" value="draft" class="w-full sm:w-auto px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Save as Draft
                        </button>
                        <button type="submit" name="action" value="publish" class="w-full sm:w-auto gradient-btn px-6 py-3 text-white rounded-lg hover:shadow-md transition-all duration-300">

                            <span>Create Program</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Progress Modal -->
    <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex flex-col items-center">
                <!-- Animated spinner -->
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4"></div>
                
                <!-- Progress text -->
                <h3 class="text-lg font-medium text-gray-900 mb-1">Creating Program</h3>
                <p class="text-sm text-gray-500 mb-4">Please wait while we process your program...</p>
                
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

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl p-8 w-full max-w-md mx-4 transform transition-all duration-300 scale-95">
            <div class="flex flex-col items-center text-center">
                <!-- Success Icon -->
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <!-- Success Message -->
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Program Created Successfully!</h3>
                <p class="text-gray-600 mb-6">Your program has been created and is now available.</p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 w-full">
                    <button id="viewProgramBtn" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i data-lucide="eye" class="w-4 h-4 mr-2 inline"></i>
                        View Program
                    </button>
                    <button id="backToListBtn" class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        <i data-lucide="list" class="w-4 h-4 mr-2 inline"></i>
                        Back to List
                    </button>
                </div>
                
                <!-- Create Another Button -->
                <button id="createAnotherBtn" class="mt-3 text-sm text-blue-600 hover:text-blue-800 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-1 inline"></i>
                    Create Another Program
                </button>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper function to clear field errors
            function clearFieldError(field) {
                field.removeClass('is-invalid border-red-500');
                field.next('.invalid-feedback').remove();
            }

            // Auto-generate slug from title
            $('#title').on('input', function() {
                clearFieldError($(this));
                
                const title = $(this).val();
                const slug = title.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
                $('#programSlug').val(slug);
            });

            // Mark slug as manually edited when user types in it
            $('#programSlug').on('input', function() {
                clearFieldError($(this));
            });

            // Character counter for description
            $('#description').on('input', function() {
                clearFieldError($(this));
                const count = $(this).val().length;
                const counter = $('#description-count');
                counter.text(count + '/500');
                
                // Update counter color based on remaining characters
                counter.removeClass('text-gray-500 text-orange-500 text-red-500');
                if (count > 450) {
                    counter.addClass('text-orange-500');
                } else if (count >= 500) {
                    counter.addClass('text-red-500');
                } else {
                    counter.addClass('text-gray-500');
                }
            });

            // Character counter for meta description
            $('#meta_description').on('input', function() {
                clearFieldError($(this));
                const count = $(this).val().length;
                const counter = $('#meta-description-count');
                counter.text(count + '/500');
                
                // Update counter color based on remaining characters
                counter.removeClass('text-gray-500 text-orange-500 text-red-500');
                if (count > 450) {
                    counter.addClass('text-orange-500');
                } else if (count >= 500) {
                    counter.addClass('text-red-500');
                } else {
                    counter.addClass('text-gray-500');
                }
            });

            // Sync color picker with text input
            const colorPicker = document.getElementById('background_color');
            const colorText = document.getElementById('background_color_text');
            
            colorPicker.addEventListener('change', function() {
                colorText.value = this.value;
            });
            
            colorText.addEventListener('input', function() {
                if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                    colorPicker.value = this.value;
                }
            });

            // Image preview
            document.getElementById('image_url').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('image-preview');
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <div class="relative">
                                <img src="${e.target.result}" alt="Preview" class="max-w-full h-32 object-cover rounded-lg">
                                <button type="button" onclick="removeImagePreview()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                        lucide.createIcons();
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Handle form submission with AJAX
        $('#createProgramForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields before submission
            const title = $('#title').val().trim();
            const slug = $('#programSlug').val().trim();
            const description = $('#description').val().trim();
            const content = $('#content').summernote('code').trim();
            
            // Clear previous errors
            clearFormErrors();
            
            let hasErrors = false;
            
            if (!title) {
                showFieldError('title', 'Program title is required');
                hasErrors = true;
            }
            
            if (!slug) {
                showFieldError('slug', 'URL slug is required');
                hasErrors = true;
            }
            
            if (!description) {
                showFieldError('description', 'Program description is required');
                hasErrors = true;
            }
            
            if (!content || content === '<p><br></p>') {
                showFieldError('content', 'Program content is required');
                hasErrors = true;
            }
            
            if (hasErrors) {
                showNotification('error', 'Please fill in all required fields');
                scrollToFirstError();
                return false;
            }
            
            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = $(document.activeElement);
            const originalText = submitBtn.html();
            
            // Show progress modal
            const progressModal = $('#progressModal');
            const progressBar = $('#uploadProgress');
            const progressPercent = $('#progressPercent');
            
            progressModal.show();
            
            // Disable form elements during submission
            form.find('input, textarea, button, select').prop('disabled', true);
            submitBtn.html('<i class="animate-spin w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full inline-block"></i>Creating...');
            
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
                    console.log('Success response:', response);
                    
                    if (response.status === 'success') {
                        // Complete progress bar
                        progressBar.css('width', '100%');
                        progressPercent.text('100');
                        
                        // Show success message
                        progressModal.find('h3').text('Program Created!');
                        progressModal.find('p:first').text('Redirecting...');
                        
                        // Hide spinner
                        $('.animate-spin', progressModal).hide();

                        // Show success notification
                        showNotification('success', response.message || 'Program created successfully!');

                        // Show success modal
                        setTimeout(() => {
                            progressModal.hide();
                            showSuccessModal(response);
                        }, 1500);
                    } else {
                        showNotification('error', response.message || 'An error occurred');
                        progressModal.hide();
                        
                        // Show form errors if validation failed
                        if (response.errors) {
                            showFormErrors(response.errors);
                        }
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    progressModal.hide();
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.errors) {
                            showFormErrors(response.errors);
                            showNotification('error', 'Please correct the errors and try again');
                        } else {
                            showNotification('error', response.message || 'An error occurred while processing your request');
                        }
                    } catch (e) {
                        if (xhr.status === 413) {
                            showNotification('error', 'The uploaded file is too large. Please reduce file size and try again.');
                        } else {
                            showNotification('error', 'A network error occurred. Please check your connection and try again.');
                        }
                    }
                },
                complete: function() {
                    setTimeout(() => {
                        // Re-enable form elements
                        form.find('input, textarea, button, select').prop('disabled', false);
                        submitBtn.html(originalText);
                        progressBar.css('width', '0%');
                        progressModal.hide();
                        form[0].reset();
                        removeImagePreview();
                    }, 3000);
                }
            });
        });

        // Function to scroll to the first error
        function scrollToFirstError() {
            const $firstError = $('.is-invalid').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 500);
            }
        }

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
                }
            });
        }

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid border-red-500');
            $('.invalid-feedback').remove();
        }

        // Function to show individual field error
        function showFieldError(fieldName, message) {
            const $field = $('[name="' + fieldName + '"]');
            
            if ($field.length) {
                $field.addClass('is-invalid border-red-500');
                
                const $errorDiv = $('<div>', {
                    class: 'invalid-feedback text-red-500 text-xs mt-1',
                    text: message
                });
                
                $field.after($errorDiv);
            }
        }

        // Cancel upload functionality
        $('#cancelUploadBtn').on('click', function() {
            if (confirm('Are you sure you want to cancel the upload?')) {
                window.location.reload();
            }
        });

        function removeImagePreview() {
            document.getElementById('image-preview').innerHTML = '';
            document.getElementById('image_url').value = '';
        }

        // Success Modal Functions
        function showSuccessModal(response) {
            const successModal = $('#successModal');
            
            // Store response data for button handlers
            successModal.data('response', response);
            
            // Show modal with animation
            successModal.show();
            setTimeout(() => {
                successModal.find('.transform').removeClass('scale-95').addClass('scale-100');
            }, 50);
            
            // Initialize Lucide icons
            lucide.createIcons();
        }

        // Success Modal Button Handlers
        $('#viewProgramBtn').on('click', function() {
            const response = $('#successModal').data('response');
            if (response && response.view_url) {
                window.location.href = response.view_url;
            } else {
                window.location.href = '<?= site_url('auth/programs') ?>';
            }
        });

        $('#backToListBtn').on('click', function() {
            const response = $('#successModal').data('response');
            if (response && response.redirect_url) {
                window.location.href = response.redirect_url;
            } else {
                window.location.href = '<?= site_url('auth/programs') ?>';
            }
        });

        $('#createAnotherBtn').on('click', function() {
            window.location.reload();
        });

        // Clear field errors on input
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('is-invalid border-red-500');
            $(this).next('.invalid-feedback').remove();
        });
    </script>
<?= $this->endSection(); ?>
