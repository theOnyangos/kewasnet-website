<?php 
    // dd($tags);
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
            'pageTitle' => 'Create New Blog Post',
            'pageDescription' => 'Fill in the details below to publish a new article',
            'breadcrumbs' => [
                ['label' => 'Blogs', 'url' => base_url('auth/blogs')],
                ['label' => 'Create']
            ],
            'bannerActions' => '<a href="' . base_url('auth/blogs') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Blogs
            </a>'
        ]) ?>

        <div class="bg-white rounded-b-xl shadow-sm max-w-full blog-form-container mx-5">
            <!-- Main Form -->
            <form class="p-6 space-y-6" id="createBlogForm" method="POST" action="<?= site_url('auth/blogs/create') ?>" enctype="multipart/form-data">
                <!-- Title Field -->
                <div>
                    <label for="title" class="block text-sm font-medium text-dark mb-1">Post Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter your post title">
                    <p class="mt-1 text-xs text-gray-500">This will be the main title of your blog post</p>
                </div>

                <!-- Slug Field -->
                <div>
                    <label for="blogSlug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                    <div class="flex w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                            kewasnet.co.ke/posts/
                        </span>
                        <input type="text" id="blogSlug" name="slug" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This will be the URL for your blog post. It should be unique and descriptive.</p>
                </div>

                <!-- Category Selection -->
                <div>
                    <label class="block text-sm font-medium text-dark mb-3">Category <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php foreach ($categories as $category): ?>
                            <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                <input type="radio" name="category" value="<?= $category['id'] ?>" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                <span class="ml-3 text-sm font-medium text-dark"><?= $category['name'] ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <!-- Dedicated error container for category -->
                    <div id="category-error" class="category-error-container mt-1"></div>
                    <p class="mt-1 text-xs text-gray-500">Select a category that best fits your blog post</p>
                </div>

                <!-- Tags Selection -->
                <div>
                    <label for="blogTags" class="block text-sm font-medium text-dark mb-1">Tags</label>
                    <div class="flex flex-wrap items-center gap-2 p-3 border border-borderColor rounded-lg min-h-[46px]">
                        <input type="text" id="blogTags" name="tag_input" class="flex-1 min-w-[100px] border-none focus:outline-none focus:ring-0" placeholder="Add tags (max 8)">
                    </div>
                    <!-- Hidden input to store tags as JSON -->
                    <input type="hidden" id="tagsHidden" name="tags" value="[]">
                    <div class="mt-2 flex flex-wrap gap-2">

                        <?php foreach ($tags as $tag): ?>
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary" data-preset-tag="<?= $tag['name'] ?>">#<?= $tag['name'] ?></span>
                        <?php endforeach; ?>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Add up to 8 tags to help categorize your blog post</p>
                </div>

                <!-- Featured Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-dark mb-1">Featured Image</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600">
                                <label for="featured_image" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none">
                                    <span>Upload a featured image</span>
                                    <input id="featured_image" name="featured_image" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500">PNG, JPG, GIF up to 2MB</p>
                            <div id="featured-preview" class="mt-4"></div>
                        </div>
                    </div>
                </div>

                <!-- Additional Images -->
                <div>
                    <label class="block text-sm font-medium text-dark mb-1">Additional Images</label>
                    <div class="border-2 border-dashed border-borderColor rounded-lg p-6 text-center">
                        <input type="file" id="additional-images" name="additional_images[]" class="hidden" accept="image/*" multiple>
                        <label for="additional-images" class="cursor-pointer">
                            <i data-lucide="upload" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                            <p class="text-sm text-slate-600">Drag & drop images or <span class="text-secondary">Click to browse</span></p>
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG, GIF up to 5MB each</p>
                        </label>
                        <div id="additional-preview" class="mt-4 flex flex-wrap gap-2"></div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Upload additional images for your blog post content</p>
                </div>

                <!-- Excerpt Field -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-dark mb-1">Excerpt</label>
                    <div class="relative">
                        <textarea id="excerpt" name="excerpt" rows="3" maxlength="1000" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="A short summary of your post (appears in listings)"></textarea>
                        <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                            <i data-lucide="file-text" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Write a compelling summary that will appear in blog listings and search results</span>
                        <span id="excerpt-count">0/1000</span>
                    </div>
                </div>

                <!-- Content Editor -->
                <div>
                    <label for="content" class="block text-sm font-medium text-dark mb-1">Content <span class="text-red-500">*</span></label>
                    <div class="rounded-lg">
                        <textarea id="content" name="content" required class="summernote-editor w-full px-4 py-3 focus:outline-none" placeholder="Write your post content here..."></textarea>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="pt-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-dark">SEO Settings</h3>
                        <p class="text-sm text-gray-500">Optimize your blog post for search engines by providing relevant metadata.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-dark mb-1">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Title for search engines">
                            <p class="mt-1 text-xs text-gray-500">This will be the title displayed in search engine results</p>
                        </div>
                        
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-dark mb-1">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" rows="3" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Description for search engines"></textarea>
                            <p class="mt-1 text-xs text-gray-500">This will be the description displayed in search engine results</p>
                        </div>
                        
                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-dark mb-1">Meta Keywords</label>
                            <input type="text" id="meta_keywords" name="meta_keywords" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Comma-separated keywords">
                            <p class="mt-1 text-xs text-gray-500">These will be the keywords associated with your blog post</p>
                        </div>
                    </div>
                </div>

                <!-- Status and Options -->
                <div class="pt-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-dark">Publishing Options</h3>
                        <p class="text-sm text-gray-500">Control the visibility and accessibility of your blog post.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Status</label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="status" value="draft" checked class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Draft</span>
                                        <p class="text-xs text-gray-500">Save as draft for later editing</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="status" value="published" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Published</span>
                                        <p class="text-xs text-gray-500">Make it live immediately</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="status" value="archived" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Archived</span>
                                        <p class="text-xs text-gray-500">Hide from public view</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-3 w-full">
                            <div>
                                <label for="datepicker" class="block text-sm font-medium text-dark mb-2">Publish Date</label>
                                <input id="datepicker" name="published_at" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">Select the date and time to publish this post</p>
                            </div>
                            
                            <div class="py-3">
                                <div class="flex items-center">
                                    <input id="is_featured" name="is_featured" type="checkbox" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor rounded">
                                    <label for="is_featured" class="ml-2 block text-sm text-dark">Feature this post</label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 block">Highlight this post on the blog homepage</p>
                            </div>
                            
                            <div>
                                <label for="reading_time" class="block text-sm font-medium text-dark mb-2">Reading Time (minutes)</label>
                                <input type="number" id="reading_time" name="reading_time" min="1" value="5" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">Estimate the time it takes to read this post</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CSRF Token for security -->
                <input id="csrfToken" type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-borderColor">
                    <button type="button" class="px-8 py-3 rounded-[50px] text-primary bg-primaryShades-50 hover:bg-primaryShades-100">
                        Save Draft
                    </button>
                    <button type="submit" class="gradient-btn rounded-[50px] px-8 text-white flex justify-center items-center gap-2">
                        <i data-lucide="send" class="w-5 h-5 z-10"></i>
                        <span>Publish Post</span>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Creating Blog Post</h3>
                    <p class="text-sm text-gray-500 mb-4">Please wait while we process your blog post and upload files...</p>
                    
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                        <div id="uploadProgress" class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                    
                    <!-- Progress percentage -->
                    <p class="text-sm font-medium text-gray-700">
                        <span id="progressPercent">0</span>% complete
                    </p>
                    
                    <!-- Upload details -->
                    <div id="uploadDetails" class="text-xs text-gray-500 mt-2" style="display: none;">
                        <span id="uploadedSize">0</span> of <span id="totalSize">0</span> MB uploaded
                        (<span id="uploadSpeed">0</span> MB/s)
                    </div>
    
                    <button id="cancelUploadBtn" type="button" class="mt-4 px-4 py-2 border border-red-500 text-red-500 rounded-md hover:bg-red-50 transition-colors">
                        Cancel Upload
                    </button>
                </div>
            </div>
        </div>
    </main>

<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize lucide icons
        lucide.createIcons();

        // Helper function to clear field errors
        function clearFieldError(field) {
            field.removeClass('is-invalid border-red-500');
            field.next('.invalid-feedback').remove();
        }

        // Helper function to generate slug
        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove all non-word characters (except spaces and hyphens)
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/--+/g, '-')     // Replace multiple hyphens with single hyphen
                .replace(/^-+|-+$/g, '')  // Remove leading/trailing hyphens
                .trim();
        }

        // Auto-generate slug from title
        $('#title').on('input', function() {
            clearFieldError($(this));
            
            // Only auto-generate slug if the slug field is empty or hasn't been manually edited
            const $slugField = $('#blogSlug');
            if (!$slugField.val() || !$slugField.data('manual')) {
                const title = $(this).val();
                const slug = generateSlug(title);
                $slugField.val(slug);
            }
        });

        // Mark slug as manually edited when user types in it
        $('#blogSlug').on('input', function() {
            clearFieldError($(this));
            $(this).data('manual', true);
        });

        // Excerpt character counter
        $('#excerpt').on('input', function() {
            const currentLength = $(this).val().length;
            const maxLength = 1000;
            const remaining = maxLength - currentLength;
            const counter = $('#excerpt-count');
            
            counter.text(`${currentLength}/1000`);
            
            // Update counter color based on remaining characters
            counter.removeClass('text-gray-500 text-orange-500 text-red-500');
            if (remaining <= 100) {
                counter.addClass('text-orange-500');
            } else if (remaining <= 50) {
                counter.removeClass('text-orange-500').addClass('text-red-500');
            } else {
                counter.addClass('text-gray-500');
            }
            
            // Show warning when approaching limit
            if (remaining <= 20 && remaining > 0) {
                if (!$('.excerpt-warning').length) {
                    $(this).parent().parent().append(`
                        <div class="excerpt-warning text-xs text-yellow-600 mt-1">
                            <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                            Only ${remaining} characters remaining
                        </div>
                    `);
                    lucide.createIcons();
                }
            } else if (remaining === 0) {
                $('.excerpt-warning').remove();
                $(this).parent().parent().append(`
                    <div class="excerpt-warning text-xs text-red-600 mt-1">
                        <i data-lucide="alert-circle" class="w-3 h-3 inline mr-1"></i>
                        Character limit reached
                    </div>
                `);
                lucide.createIcons();
            } else {
                $('.excerpt-warning').remove();
            }
            
            clearFieldError($(this));
        });

        // Initialize tag functionality
        const tagsInput = document.getElementById('blogTags');
        const tagsContainer = tagsInput.parentElement;
        let tags = [];

        // Handle tag input
        tagsInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const tag = tagsInput.value.trim();
                if (tag && !tags.includes(tag)) {
                    if (tags.length < 8) {
                        tags.push(tag);
                        renderTags();
                        tagsInput.value = '';
                    } else {
                        showToast('Maximum of 8 tags allowed', 'warning');
                    }
                }
            }
        });

        // Handle preset tag clicks
        $('[data-preset-tag]').on('click', function() {
            const tag = $(this).data('preset-tag');
            if (!tags.includes(tag) && tags.length < 8) {
                tags.push(tag);
                renderTags();
            }
        });

        function renderTags() {
            // Clear existing tag elements
            const existingTags = tagsContainer.querySelectorAll('.tag-badge');
            existingTags.forEach(tag => tag.remove());
            
            // Add new tags
            tags.forEach(tag => {
                const tagElement = document.createElement('span');
                tagElement.className = 'tag-badge bg-secondaryShades-50 text-secondary text-xs px-3 py-1 rounded-full flex items-center';
                tagElement.innerHTML = `
                    ${tag}
                    <button type="button" class="ml-1 text-secondary hover:text-secondaryShades-600" data-tag="${tag}">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </button>
                `;
                tagsContainer.insertBefore(tagElement, tagsInput);
            });
            
            // Update hidden input with tags JSON
            document.getElementById('tagsHidden').value = JSON.stringify(tags);
            
            // Initialize Lucide icons for the new tags
            lucide.createIcons();
            
            // Add event listeners to remove buttons
            document.querySelectorAll('.tag-badge button').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const tagToRemove = e.currentTarget.getAttribute('data-tag');
                    tags = tags.filter(t => t !== tagToRemove);
                    renderTags();
                });
            });
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = $(`
                <div class="fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg text-white z-50 ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
                }">
                    ${message}
                </div>
            `);
            
            $('body').append(toast);
            
            setTimeout(() => {
                toast.fadeOut(() => toast.remove());
            }, 3000);
        }

        // Clear field error function
        function clearFieldError(field) {
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();
        }

        // Image upload functionality
        let uploadedAdditionalImages = [];

        // Handle featured image upload
        $('#featured_image').on('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = $('#featured-preview');
            previewContainer.empty();
            
            if (file && file.type.startsWith('image/')) {
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    showToast('Featured image must be less than 2MB', 'error');
                    $(this).val('');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = $(`
                        <div class="file-preview mt-2">
                            <img src="${e.target.result}" alt="Featured Image Preview" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200">
                            <div class="remove-file cursor-pointer" onclick="removeFeaturedImage()">×</div>
                        </div>
                    `);
                    previewContainer.append(preview);
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle additional images upload
        $('#additional-images').on('change', function(e) {
            const files = Array.from(e.target.files);
            const previewContainer = $('#additional-preview');
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    // Check file size (5MB limit)
                    if (file.size > 5 * 1024 * 1024) {
                        showToast(`Image ${file.name} must be less than 5MB`, 'error');
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const fileId = 'img_' + Date.now() + '_' + index;
                        uploadedAdditionalImages.push({
                            id: fileId,
                            file: file,
                            name: file.name
                        });

                        const preview = $(`
                            <div class="file-preview" data-file-id="${fileId}">
                                <img src="${e.target.result}" alt="Preview" class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">
                                <div class="remove-file" data-file-id="${fileId}">×</div>
                                <div class="text-xs text-center mt-1 text-gray-600 truncate">${file.name}</div>
                            </div>
                        `);
                        
                        previewContainer.append(preview);
                    };
                    reader.readAsDataURL(file);
                } else {
                    showToast(`${file.name} is not a valid image file`, 'error');
                }
            });
            
            // Clear the file input to prevent duplication in FormData
            // The files are now stored in uploadedAdditionalImages array
            $(this).val('');
        });

        // Handle additional image removal
        $(document).on('click', '.remove-file[data-file-id]', function() {
            const fileId = $(this).data('file-id');
            uploadedAdditionalImages = uploadedAdditionalImages.filter(f => f.id !== fileId);
            $(`.file-preview[data-file-id="${fileId}"]`).remove();
        });

        // Remove featured image function
        function removeFeaturedImage() {
            $('#featured_image').val('');
            $('#featured-preview').empty();
        }

        // Format file size helper function
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Drag and drop functionality for additional images
        const additionalDropZone = $('#additional-images').parent();
        
        additionalDropZone.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('border-secondary bg-secondary bg-opacity-5');
        });

        additionalDropZone.on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('border-secondary bg-secondary bg-opacity-5');
        });

        additionalDropZone.on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('border-secondary bg-secondary bg-opacity-5');
            
            const files = Array.from(e.originalEvent.dataTransfer.files);
            const imageFiles = files.filter(file => file.type.startsWith('image/'));
            
            if (imageFiles.length > 0) {
                // Trigger the file input change event with the dropped files
                const fileInput = $('#additional-images')[0];
                const dt = new DataTransfer();
                imageFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        // Variables for file management and submission
        let currentXHR = null;
        let isDrafting = false;

        // Handle Save Draft button
        $('#saveDraftBtn').on('click', function() {
            isDrafting = true;
            $('#createBlogForm').trigger('submit');
        });

        // Handle form submission
        $('#createBlogForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = isDrafting ? $('#saveDraftBtn') : $('#publishBtn');
            const originalText = submitBtn.html();
            
            // Set status based on which button was clicked
            if (isDrafting) {
                formData.set('status', 'draft');
                formData.set('action', 'save_draft');
            } else {
                // Use the selected status from radio buttons
                const selectedStatus = $('input[name="status"]:checked').val();
                formData.set('status', selectedStatus || 'draft');
                formData.set('action', 'publish');
            }
            
            // Add tags to FormData manually since they're stored in JavaScript array
            formData.set('tags', JSON.stringify(tags));
            
            // Add additional images to FormData with correct field naming
            uploadedAdditionalImages.forEach((fileObj, index) => {
                formData.append('additional_images[]', fileObj.file, fileObj.name);
            });
            
            // Ensure featured image is properly included (it should be included automatically from form)
            const featuredImageInput = $('#featured_image')[0];
            if (featuredImageInput.files.length > 0) {
                // Remove any existing featured_image from FormData and re-add to ensure it's fresh
                formData.delete('featured_image');
                formData.append('featured_image', featuredImageInput.files[0]);
            }
            
            // Debug: Log form data
            console.log('=== FORM SUBMISSION DEBUG ===');
            console.log('Submitting blog with tags:', tags);
            console.log('Action:', isDrafting ? 'save_draft' : 'publish');
            console.log('Featured image files:', featuredImageInput.files.length);
            console.log('Additional images count:', uploadedAdditionalImages.length);
            console.log('uploadedAdditionalImages array:', uploadedAdditionalImages);
            
            console.log('=== ALL FORM DATA ENTRIES ===');
            let fileCount = 0;
            let totalFileSize = 0;
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(key + ': File - ' + value.name + ' (' + value.size + ' bytes, type: ' + value.type + ')');
                    fileCount++;
                    totalFileSize += value.size;
                } else {
                    console.log(key + ': ' + value);
                }
            }
            console.log(`Total files: ${fileCount}, Total size: ${(totalFileSize / 1024 / 1024).toFixed(2)} MB`);
            console.log('=== END DEBUG ===');
            
            // Show progress modal
            const progressModal = $('#progressModal');
            const progressBar = $('#uploadProgress');
            const progressPercent = $('#progressPercent');
            const uploadDetails = $('#uploadDetails');
            const uploadedSize = $('#uploadedSize');
            const totalSize = $('#totalSize');
            const uploadSpeed = $('#uploadSpeed');
            
            // Calculate total file size (including featured image and additional images)
            totalFileSize = 0; // Reset the previously declared variable
            const featuredImage = $('#featured_image')[0].files[0];
            if (featuredImage) totalFileSize += featuredImage.size;
            
            uploadedAdditionalImages.forEach(fileObj => {
                totalFileSize += fileObj.file.size;
            });
            
            // Update total size display
            if (totalFileSize > 0) {
                totalSize.text((totalFileSize / 1024 / 1024).toFixed(2));
                uploadDetails.show();
            }
            
            progressModal.show();
            
            // Update modal text based on action
            if (isDrafting) {
                progressModal.find('h3').text('Saving Draft');
                progressModal.find('p:first').text('Please wait while we save your blog post as draft...');
            } else {
                progressModal.find('h3').text('Publishing Blog Post');
                progressModal.find('p:first').text('Please wait while we publish your blog post...');
            }
            
            // Disable form elements during submission
            form.find('input, textarea, button, select').prop('disabled', true);
            submitBtn.html('<i class="animate-spin w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full inline-block"></i>Processing...');
            
            // Track upload progress
            let uploadStartTime = Date.now();
            let lastLoaded = 0;
            let lastTime = uploadStartTime;
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    currentXHR = xhr;
                    
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            // Calculate progress
                            const percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.css('width', percent + '%');
                            progressPercent.text(percent);
                            
                            // Calculate upload speed
                            const now = Date.now();
                            const timeDiff = (now - lastTime) / 1000; // in seconds
                            const loadedDiff = e.loaded - lastLoaded;
                            
                            if (timeDiff > 0) {
                                const speed = (loadedDiff / 1024 / 1024) / timeDiff; // MB/s
                                uploadSpeed.text(speed.toFixed(2));
                                
                                // Update uploaded size
                                uploadedSize.text((e.loaded / 1024 / 1024).toFixed(2));
                            }
                            
                            lastLoaded = e.loaded;
                            lastTime = now;
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
                        if (isDrafting) {
                            progressModal.find('h3').text('Draft Saved!');
                            progressModal.find('p:first').text('Your blog post has been saved as draft.');
                        } else {
                            progressModal.find('h3').text('Blog Post Published!');
                            progressModal.find('p:first').text('Redirecting to your blog post...');
                        }
                        
                        // Hide details and spinner
                        uploadDetails.hide();
                        $('.animate-spin', progressModal).hide();
                        
                        // Show success toast
                        showToast(response.message || (isDrafting ? 'Draft saved successfully!' : 'Blog post published successfully!'), 'success');
                        
                        // Redirect after delay
                        setTimeout(() => {
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                window.location.href = '<?= site_url('auth/blogs') ?>';
                            }
                        }, isDrafting ? 1000 : 1500);
                    } else {
                        showToast(response.message || 'An error occurred', 'error');
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
                            showToast('Please correct the errors and try again', 'error');
                        } else {
                            showToast(response.message || 'An error occurred while processing your request', 'error');
                        }
                    } catch (e) {
                        if (xhr.status === 413) {
                            showToast('The uploaded files are too large. Please reduce file sizes and try again.', 'error');
                        } else {
                            showToast('A network error occurred. Please check your connection and try again.', 'error');
                        }
                    }
                },
                complete: function() {
                    // Re-enable form elements
                    form.find('input, textarea, button, select').prop('disabled', false);
                    submitBtn.html(originalText);
                    
                    // Reset submission state
                    isDrafting = false;
                    currentXHR = null;
                }
            });
        });

        // Cancel upload functionality
        $('#cancelUploadBtn').on('click', function() {
            if (currentXHR) {
                currentXHR.abort();
                currentXHR = null;
            }
            
            $('#progressModal').hide();
            
            // Re-enable form elements
            $('#createBlogForm').find('input, textarea, button, select').prop('disabled', false);
            $('#publishBtn').html('<i data-lucide="send" class="w-5 h-5 z-10"></i><span>Publish Post</span>');
            $('#saveDraftBtn').html('Save Draft');
            
            showToast('Upload cancelled', 'info');
        });

        // Function to show form errors
        function showFormErrors(errors) {
            clearFormErrors();
            
            $.each(errors, function(fieldName, message) {
                // Special handling for radio button groups like 'category'
                if (fieldName === 'category') {
                    // Add error styling to all category radio buttons
                    $('input[name="category"]').addClass('is-invalid border-red-500');
                    
                    // Show error message in dedicated container
                    const $errorContainer = $('#category-error');
                    const $errorDiv = $('<div>', {
                        class: 'invalid-feedback text-red-500 text-xs',
                        text: message
                    });
                    $errorContainer.empty().append($errorDiv);
                    
                    // Scroll to the category section
                    $('input[name="category"]').first().focus();
                    $('html, body').animate({
                        scrollTop: $('input[name="category"]').first().offset().top - 100
                    }, 500);
                } else {
                    // Handle other fields normally
                    const $field = $('[name="' + fieldName + '"]');
                    
                    if ($field.length) {
                        $field.addClass('is-invalid border-red-500');
                        
                        const $errorDiv = $('<div>', {
                            class: 'invalid-feedback text-red-500 text-xs mt-1',
                            text: message
                        });
                        
                        $field.after($errorDiv);
                        
                        // Scroll to first error
                        if ($('.is-invalid').first().is($field)) {
                            $field.focus();
                            $('html, body').animate({
                                scrollTop: $field.offset().top - 100
                            }, 500);
                        }
                    }
                }
            });
        }

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid border-red-500');
            $('.invalid-feedback').remove();
            // Clear category-specific error container
            $('#category-error').empty();
        }

        // Helper function to show toast notifications
        function showToast(message, type = 'success') {
            // Remove existing toasts
            $('.custom-toast').remove();
            
            let bgColor, iconName;
            switch(type) {
                case 'success':
                    bgColor = 'bg-green-500';
                    iconName = 'check-circle';
                    break;
                case 'error':
                    bgColor = 'bg-red-500';
                    iconName = 'alert-circle';
                    break;
                case 'warning':
                    bgColor = 'bg-yellow-500';
                    iconName = 'alert-triangle';
                    break;
                case 'info':
                    bgColor = 'bg-blue-500';
                    iconName = 'info';
                    break;
                default:
                    bgColor = 'bg-gray-500';
                    iconName = 'bell';
            }
            
            // Create toast element
            const toast = $(`
                <div class="custom-toast fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 ${bgColor} max-w-sm">
                    <div class="flex items-center">
                        <i data-lucide="${iconName}" class="w-5 h-5 mr-2 flex-shrink-0"></i>
                        <span class="text-sm">${message}</span>
                    </div>
                </div>
            `);
            
            // Append to body and show
            $('body').append(toast);
            lucide.createIcons();
            toast.hide().fadeIn(300);
            
            // Auto-remove after delay
            setTimeout(() => toast.fadeOut(300, () => toast.remove()), type === 'error' ? 5000 : 3000);
        }

        // Clear field errors on input
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('is-invalid border-red-500');
            $(this).next('.invalid-feedback').remove();
            
            // Special handling for category radio buttons
            if ($(this).attr('name') === 'category') {
                // Clear error styling from all category radio buttons
                $('input[name="category"]').removeClass('is-invalid border-red-500');
                // Clear the category error container
                $('#category-error').empty();
            }
        });
    });
</script>
<?= $this->endSection() ?>