<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Create Discussion',
            'pageDescription' => 'Start a new discussion in the forum',
            'breadcrumbs' => [
                ['label' => 'Forums', 'url' => base_url('auth/forums')],
                ['label' => 'Forum Details', 'url' => base_url('auth/forums/' . $forumId)],
                ['label' => 'Create Discussion']
            ],
            'bannerActions' => '<a href="' . base_url('auth/forums/' . $forumId) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Forum
            </a>'
        ]) ?>

        <!-- Reports Tab -->
        <div id="reports" class="">
            <div class="bg-white rounded-b-xl shadow-sm mx-6">

                <form class="space-y-6 p-6" id="createDiscussionForm" method="POST" action="<?= site_url('auth/forums/' . $forumId . '/discussions/create') ?>" enctype="multipart/form-data">
                    <!-- Discussion Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Discussion Title*</label>
                        <input type="text" id="title" name="title" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                            placeholder="What's your question or topic?">
                        <p class="mt-1 text-sm text-slate-500">Be specific and concise with your title</p>
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="discussionSlug" class="block text-sm font-medium text-gray-700 mb-2">
                            URL Slug *
                        </label>
                        <div class="flex w-full">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-700 text-sm whitespace-nowrap">
                                kewasnet.co.ke/discussion/
                            </span>
                            <input 
                                type="text" 
                                id="discussionSlug" 
                                name="slug"
                                pattern="^[a-z0-9-]+$"
                                class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="water-conservation-tips"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Auto-generated from discussion title. Use lowercase letters, numbers, and hyphens only.</p>
                    </div>
                    
                    <!-- Forum Selection -->
                    <div>
                        <input class="hidden" value="<?= $forumId ?>" name="forum_id" id="forum_id" />
                    </div>
                    
                    <!-- Discussion Content -->
                    <div>
                        <label for="discussionContent" class="block text-sm font-medium text-slate-700 mb-1">Discussion Details*</label>
                        <div class="flex-1 relative">
                            <textarea 
                                id="messageInput" 
                                name="content" 
                                placeholder="Type your message..." 
                                rows="1"
                                class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary min-h-[100px] max-h-[300px] transition-all duration-200 overflow-hidden resize-none"
                            ></textarea>
                            <div id="messageInputMirror" class="invisible absolute top-0 left-0 w-full px-4 py-3 whitespace-pre-wrap break-words pointer-events-none"></div>
                        </div>
                        <p class="text-sm text-slate-500">Minimum 20 characters. Be clear and provide context for better responses.</p>
                    </div>
                    
                    <!-- Tags -->
                    <div>
                        <label for="discussionTags" class="block text-sm font-medium text-slate-700 mb-1">Tags</label>
                        <div class="flex flex-wrap items-center gap-2 p-1 border borderColor rounded-md min-h-[46px]">
                            <input type="text" id="discussionTags" name="tag_input" 
                                class="flex-1 min-w-[100px] w-full px-4 py-3 rounded-lg border-none focus:outline-none focus:ring-2 focus:ring-secondary" 
                                placeholder="Add tags (max 5)">
                        </div>
                        <!-- Hidden input to store tags as JSON -->
                        <input type="hidden" id="tagsHidden" name="tags" value="[]">
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary">#water-management</span>
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary">#conservation</span>
                            <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary">#best-practices</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Add up to 5 tags to help others find your discussion</p>
                    </div>

                    <!-- CSRF Token for security -->
                    <input id="csrfToken" type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    
                    <!-- File Attachment -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Attachments</label>
                        <!-- Add to your form -->
                        <div class="border-2 border-dashed borderColor rounded-lg p-6 text-center">
                            <input type="file" id="document-upload" name="attachments[]" class="hidden" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.csv" multiple>
                            <label for="document-upload" class="cursor-pointer">
                            <i data-lucide="upload" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                            <p class="text-sm text-slate-600">Drag & drop documents or <span class="text-primary">Click to browse files</span></p>
                            <p class="text-xs text-slate-400 mt-1">PDF, DOC, DOCX, TXT, XLS, XLSX, PPT, PPTX, CSV up to 5MB</p>
                            </label>
                            <div id="preview-container" class="mt-4 flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t borderColor">
                        <button type="button" id="cancelDiscussionBtn" class="px-8 py-2 bg-primaryShades-50 hover:bg-primaryShades-100 rounded-[50px] text-primary">
                            Cancel
                        </button>
                        <button type="submit" class="gradient-btn px-8 py-2 rounded-[50px] text-white font-medium">
                            <span>Post Discussion</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Progress Modal -->
        <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex flex-col items-center">
                    <!-- Animated spinner -->
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4"></div>
                    
                    <!-- Progress text -->
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Creating Discussion</h3>
                    <p class="text-sm text-gray-500 mb-4">Please wait while we process your discussion and upload files...</p>
                    
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
<style>
    /* Error input styling */
    .is-invalid {
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
    }
    
    .is-invalid:focus {
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
    }
    
    .invalid-feedback {
        display: block;
        color: #DC2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        line-height: 1.25;
    }

    /* File preview styles */
    .file-preview {
        position: relative;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .file-preview img {
        max-width: 100px;
        max-height: 100px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
    }

    .file-preview .remove-file {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc2626;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
    }

    /* Progress modal styles */
    #progressModal {
        transition: opacity 0.3s ease;
    }

    /* Smooth progress bar transition */
    #uploadProgress {
        transition: width 0.3s ease;
    }

    /* File upload preview enhancements */
    .file-preview {
        transition: all 0.3s ease;
    }

    .file-preview:hover {
        transform: scale(1.05);
    }

    /* Auto-growing textarea */
    #messageInput {
        transition: height 0.2s ease;
    }

    /* Mirror element styling */
    #messageInputMirror {
        visibility: hidden;
        position: absolute;
        z-index: -1;
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    /* Better scrollbar (when content exceeds max height) */
    #messageInput::-webkit-scrollbar {
        width: 8px;
    }
    #messageInput::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>
<script>
    const redirectUrl = '<?= site_url('auth/forums/' . $forumId) ?>';

    $(document).ready(function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Helper function to generate slug
        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove all non-word characters (except spaces and hyphens)
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/--+/g, '-')     // Replace multiple hyphens with single hyphen
                .replace(/^-+|-+$/g, '')  // Remove leading/trailing hyphens
                .trim();
        }

        // Auto-generate slug from discussion title
        $('#title').on('input', function() {
            // Clear error state when user starts typing
            clearFieldError($(this));
            
            // Only auto-generate slug if the slug field is empty or hasn't been manually edited
            const $slugField = $('#discussionSlug');
            if (!$slugField.val() || !$slugField.data('manual')) {
                const title = $(this).val();
                const slug = generateSlug(title);
                $slugField.val(slug);
            }
        });

        // Mark slug as manually edited and clear errors
        $('#discussionSlug').on('input', function() {
            clearFieldError($(this));
            $(this).data('manual', true);
        });

        // Helper function to clear field error
        function clearFieldError($field) {
            $field.removeClass('is-invalid');
            $field.next('.invalid-feedback').remove();
            $field.closest('div').find('.invalid-feedback').remove();
        }
        let currentXHR = null;

        // Initialize tag functionality
        const tagsInput = document.getElementById('discussionTags');
        const tagsContainer = tagsInput.parentElement;
        let tags = [];
        let uploadedFiles = [];

        // Cancel upload handler
        $('#cancelUploadBtn').on('click', function() {
            if (currentXHR) {
                currentXHR.abort();
                showToast('Upload canceled', 'warning');
                progressModal.hide();
                currentXHR = null;
            }
        });
        
        tagsInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const tag = tagsInput.value.trim();
                if (tag && !tags.includes(tag)) {
                    if (tags.length < 5) {
                        tags.push(tag);
                        renderTags();
                        tagsInput.value = '';
                    }
                }
            }
        });
        
        function renderTags() {
            // Clear existing tag elements
            const existingTags = tagsContainer.querySelectorAll('.tag-badge');
            existingTags.forEach(tag => tag.remove());
            
            // Add new tags
            tags.forEach(tag => {
                const tagElement = document.createElement('span');
                tagElement.className = 'tag-badge bg-primaryShades-100 text-primary text-xs px-3 py-1 rounded-full flex items-center';
                tagElement.innerHTML = `
                    ${tag}
                    <button type="button" class="ml-1 text-primaryShades-400 hover:text-primary" data-tag="${tag}">
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

        // Handle file upload preview
        $('#document-upload').on('change', function(e) {
            const files = Array.from(e.target.files);
            const previewContainer = $('#preview-container');
            
            files.forEach((file, index) => {
                // For document files, show file name and size
                const fileId = 'file_' + Date.now() + '_' + index;
                uploadedFiles.push({
                    id: fileId,
                    file: file,
                    name: file.name
                });

                const preview = $(`
                    <div class="file-preview" data-file-id="${fileId}">
                        <div class="file-info p-3 border rounded bg-gray-50 flex items-center gap-2 min-w-[200px]">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium truncate">${file.name}</div>
                                <div class="text-xs text-gray-500">${formatFileSize(file.size)}</div>
                            </div>
                        </div>
                        <div class="remove-file" data-file-id="${fileId}">×</div>
                    </div>
                `);
                
                $('#preview-container').append(preview);
            });
            
            // Re-initialize icons for new elements
            lucide.createIcons();
        });

        // Handle file removal
        $(document).on('click', '.remove-file', function() {
            const fileId = $(this).data('file-id');
            uploadedFiles = uploadedFiles.filter(f => f.id !== fileId);
            $(`.file-preview[data-file-id="${fileId}"]`).remove();
        });

        // Auto-generate slug from title
        $('#title').on('input', function() {
            clearFieldError($(this));
            
            // Only auto-generate slug if the slug field is empty or hasn't been manually edited
            if (!$('#forumSlug').val() || !$('#forumSlug').data('manual')) {
                const title = $(this).val();
                const slug = generateSlug(title);
                $('#forumSlug').val(slug);
            }
        });

        // Helper function to generate slug
        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove all non-word characters (except spaces and hyphens)
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/--+/g, '-')      // Replace multiple hyphens with single hyphen
                .trim();                   // Trim leading/trailing hyphens
        }

        // Mark slug as manually edited when user types in it
        $('#forumSlug').on('input', function() {
            clearFieldError($(this));
            $(this).data('manual', true);
        });

        // Clear errors on content input
        $('#messageInput').on('input', function() {
            clearFieldError($(this));
        });

        // Function to clear field error
        function clearFieldError(field) {
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();
        }

        // Handle form submission
        $('#createDiscussionForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            // Add tags to FormData manually since they're stored in JavaScript array
            formData.set('tags', JSON.stringify(tags));
            
            // Add uploaded files to FormData
            uploadedFiles.forEach((fileObj, index) => {
                formData.append(`attachments[${index}]`, fileObj.file, fileObj.name);
            });
            
            // Debug: Log form data including tags
            console.log('Submitting discussion with tags:', tags);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(key + ': File - ' + value.name + ' (' + value.size + ' bytes)');
                } else {
                    console.log(key + ': ' + value);
                }
            }
            
            // Show progress modal
            const progressModal = $('#progressModal');
            const progressBar = $('#uploadProgress');
            const progressPercent = $('#progressPercent');
            const uploadDetails = $('#uploadDetails');
            const uploadedSize = $('#uploadedSize');
            const totalSize = $('#totalSize');
            const uploadSpeed = $('#uploadSpeed');
            
            // Calculate total file size
            let totalFileSize = 0;
            uploadedFiles.forEach(file => {
                totalFileSize += file.file.size;
            });
            
            // Update total size display
            if (totalFileSize > 0) {
                totalSize.text((totalFileSize / 1024 / 1024).toFixed(2));
                uploadDetails.show();
            }
            
            progressModal.show();
            
            // Disable form elements during submission
            form.find('input, textarea, button').prop('disabled', true);
            
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
                        $('h3', progressModal).text('Discussion Created!');
                        $('p:first', progressModal).text('Redirecting to your new discussion...');
                        
                        // Hide details and spinner
                        uploadDetails.hide();
                        $('.animate-spin', progressModal).hide();
                        
                        // Redirect after delay
                        setTimeout(() => {
                            window.location.href = response.redirect_url || redirectUrl;
                        }, 1500);
                    } else {
                        showToast(response.message || 'An error occurred', 'error');
                        progressModal.hide();
                    }
                },
                error: function(xhr) {
                    console.error('Error response:', xhr);
                    
                    // Hide progress modal
                    progressModal.hide();
                    
                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    
                    if (Object.keys(errors).length > 0) {
                        showFormErrors(errors);
                        showToast('Please fix the errors and try again', 'error');
                    } else {
                        showToast(response.message || 'Failed to create discussion', 'error');
                    }
                },
                complete: function() {
                    // Re-enable form elements
                    form.find('input, textarea, button').prop('disabled', false);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Helper function to format file sizes
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i]);
        }

        // Function to show form errors
        function showFormErrors(errors) {
            clearFormErrors();
            
            $.each(errors, function(fieldName, message) {
                const $field = $('[name="' + fieldName + '"]');
                
                if ($field.length) {
                    $field.addClass('is-invalid');
                    
                    const $errorDiv = $('<div>', {
                        class: 'invalid-feedback text-red-500 text-xs mt-1',
                        text: message
                    });
                    
                    $field.after($errorDiv);
                    
                    if ($('.is-invalid').first().is($field)) {
                        $field.focus();
                    }
                }
            });
        }

        // Helper function to show toast notifications
        function showToast(message, type = 'success') {
            // Remove existing toasts
            $('.custom-toast').remove();
            
            // Create toast element
            const toast = $(`
                <div class="custom-toast fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg text-white z-50 ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }">
                    <div class="flex items-center">
                        <i data-lucide="${
                            type === 'success' ? 'check-circle' : 'alert-circle'
                        }" class="w-5 h-5 mr-2"></i>
                        ${message}
                    </div>
                </div>
            `);
            
            // Append to body and show
            $('body').append(toast);
            lucide.createIcons();
            toast.hide().fadeIn(300);
            
            // Auto-remove after delay
            setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
        }

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        // Cancel button handler
        $('#cancelDiscussionBtn').on('click', function() {
            window.location.href = redirectUrl;
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const messageInput = document.getElementById("messageInput");

        // Auto-grow textarea based on content
        messageInput.addEventListener("input", function() {
            this.style.height = "auto";
            this.style.height = (this.scrollHeight) + "px";
        });
    });
</script>
<?= $this->endSection() ?>