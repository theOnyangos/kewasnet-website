<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Edit Discussion',
            'pageDescription' => 'Update discussion details',
            'breadcrumbs' => [
                ['label' => 'Forums', 'url' => base_url('auth/forums')],
                ['label' => 'Forum Details', 'url' => base_url('auth/forums/' . $discussion['forum_id'])],
                ['label' => 'Edit Discussion']
            ],
            'bannerActions' => '<a href="' . base_url('auth/forums/' . $discussion['forum_id']) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Forum
            </a>'
        ]) ?>

        <!-- Edit Discussion Form -->
        <div id="reports" class="">
            <div class="bg-white rounded-b-xl shadow-sm mx-6">

                <form class="space-y-6 p-6" id="editDiscussionForm" method="POST" action="<?= base_url('auth/forums/discussions/' . $discussion['id'] . '/update') ?>">
                    <!-- Discussion Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Discussion Title*</label>
                        <input type="text" id="title" name="title" value="<?= esc($discussion['title']) ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
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
                                value="<?= esc($discussion['slug']) ?>"
                                pattern="^[a-z0-9-]+$"
                                class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="water-conservation-tips"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Use lowercase letters, numbers, and hyphens only.</p>
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
                            ><?= esc($discussion['content']) ?></textarea>
                            <div id="messageInputMirror" class="invisible absolute top-0 left-0 w-full px-4 py-3 whitespace-pre-wrap break-words pointer-events-none"></div>
                        </div>
                        <p class="text-sm text-slate-500">Minimum 20 characters. Be clear and provide context for better responses.</p>
                    </div>
                    
                    <!-- Tags -->
                    <?php 
                        // Safely decode tags if it's a JSON string
                        $tags = $discussion['tags'] ?? [];
                        if (is_string($tags)) {
                            $decodedTags = json_decode($tags, true);
                            $tags = is_array($decodedTags) ? $decodedTags : [];
                        }
                        $tagsCount = is_array($tags) ? count($tags) : 0;
                    ?>
                    <div>
                        <label for="discussionTags" class="block text-sm font-medium text-slate-700 mb-1">Tags (<?= $tagsCount ?>/5)</label>
                        <div id="tagsContainer" class="flex flex-wrap items-center gap-2 p-3 border borderColor rounded-md min-h-[56px]">
                            <!-- Existing tags will be rendered here by JavaScript -->
                            <input type="text" id="discussionTags" name="tag_input" 
                                class="flex-1 min-w-[100px] px-2 py-1 border-none focus:outline-none focus:ring-0" 
                                placeholder="<?= $tagsCount === 0 ? 'Add tags (max 5)' : 'Add more tags...' ?>">
                        </div>
                        <!-- Hidden input to store tags as JSON -->
                        <input type="hidden" id="tagsHidden" name="tags" value='<?= esc(json_encode($tags)) ?>'>
                        <div class="mt-2">
                            <p class="text-xs text-slate-500">
                                <i data-lucide="info" class="w-3 h-3 inline"></i>
                                Press Enter or comma to add a tag. Click the X to remove existing tags.
                            </p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <p class="text-xs text-slate-400 w-full">Suggested tags:</p>
                                <button type="button" class="suggested-tag bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary" data-tag="water-management">water-management</button>
                                <button type="button" class="suggested-tag bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary" data-tag="conservation">conservation</button>
                                <button type="button" class="suggested-tag bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary" data-tag="best-practices">best-practices</button>
                                <button type="button" class="suggested-tag bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary" data-tag="water-quality">water-quality</button>
                                <button type="button" class="suggested-tag bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-full cursor-pointer hover:bg-primaryShades-100 hover:text-primary" data-tag="sustainability">sustainability</button>
                            </div>
                        </div>
                    </div>

                    <!-- CSRF Token for security -->
                    <input id="csrfToken" type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    
                    <!-- Existing Attachments -->
                    <?php if (!empty($discussion['attachments'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Existing Attachments</label>
                        <div id="existing-attachments-container" class="space-y-2">
                            <?php foreach ($discussion['attachments'] as $attachment): ?>
                            <div class="existing-attachment flex items-center justify-between p-3 bg-slate-50 rounded-lg" data-attachment-id="<?= esc($attachment['id']) ?>">
                                <div class="flex items-center">
                                    <i data-lucide="file-text" class="w-4 h-4 text-slate-400 mr-2"></i>
                                    <span class="text-sm text-slate-700"><?= esc($attachment['original_name']) ?></span>
                                    <span class="text-xs text-slate-500 ml-2">(<?= number_format($attachment['file_size'] / 1024, 2) ?> KB)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('ksp/discussion/view-attachment/' . $attachment['file_path']) ?>" target="_blank" class="text-primary hover:text-primary-dark text-sm">
                                        View
                                    </a>
                                    <button type="button" class="remove-existing-attachment text-red-600 hover:text-red-800" data-attachment-id="<?= esc($attachment['id']) ?>">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- New Attachments -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Add New Attachments</label>
                        <div class="border-2 border-dashed borderColor rounded-lg p-6 text-center">
                            <input type="file" id="document-upload" name="attachments[]" class="hidden" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.csv" multiple>
                            <label for="document-upload" class="cursor-pointer">
                                <i data-lucide="upload" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                                <p class="text-sm text-slate-600">Drag & drop documents or <span class="text-primary">Click to browse files</span></p>
                                <p class="text-xs text-slate-400 mt-1">PDF, DOC, DOCX, TXT, XLS, XLSX, PPT, PPTX, CSV up to 5MB</p>
                            </label>
                            <div id="new-attachments-preview" class="mt-4 flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t borderColor">
                        <button type="button" id="cancelDiscussionBtn" class="px-8 py-2 bg-primaryShades-50 hover:bg-primaryShades-100 rounded-[50px] text-primary">
                            Cancel
                        </button>
                        <button type="submit" class="gradient-btn px-8 py-2 rounded-[50px] text-white font-medium">
                            <span>Update Discussion</span>
                        </button>
                    </div>
                </form>
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
    .new-file-preview {
        transition: all 0.3s ease;
    }
    
    .new-file-preview:hover {
        transform: scale(1.02);
    }
    
    .existing-attachment {
        transition: all 0.3s ease;
    }
    
    .existing-attachment:hover {
        background-color: #f8fafc;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editDiscussionForm');
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('discussionSlug');
        const contentInput = document.getElementById('messageInput');
        const cancelBtn = document.getElementById('cancelDiscussionBtn');
        const tagsInput = document.getElementById('discussionTags');
        const tagsHidden = document.getElementById('tagsHidden');
        
        // Auto-generate slug from title (only if slug is empty)
        if (titleInput && slugInput) {
            titleInput.addEventListener('input', function() {
                if (!slugInput.value) {
                    const slug = this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugInput.value = slug;
                }
            });
        }
        
        // Auto-resize textarea
        if (contentInput) {
            const mirror = document.getElementById('messageInputMirror');
            contentInput.addEventListener('input', function() {
                mirror.textContent = this.value + '\n';
                this.style.height = 'auto';
                this.style.height = Math.min(mirror.offsetHeight, 300) + 'px';
            });
            
            // Trigger initial resize
            const event = new Event('input');
            contentInput.dispatchEvent(event);
        }
        
        // Tags functionality
        let tags = [];
        try {
            const tagsValue = tagsHidden.value || '[]';
            const parsedTags = JSON.parse(tagsValue);
            // Ensure it's an array
            tags = Array.isArray(parsedTags) ? parsedTags : [];
        } catch (e) {
            console.error('Failed to parse tags:', e);
            tags = [];
        }
        const maxTags = 5;
        
        function updateTagCounter() {
            const label = document.querySelector('label[for="discussionTags"]');
            if (label) {
                label.textContent = `Tags (${tags.length}/${maxTags})`;
            }
            
            // Update placeholder
            if (tagsInput) {
                tagsInput.placeholder = tags.length >= maxTags ? 'Maximum tags reached' : 
                                       tags.length === 0 ? 'Add tags (max 5)' : 'Add more tags...';
                tagsInput.disabled = tags.length >= maxTags;
            }
        }
        
        function renderTags() {
            const container = document.getElementById('tagsContainer');
            if (!container) return;
            
            // Remove existing tag chips
            const existingTags = container.querySelectorAll('.tag-chip');
            existingTags.forEach(tag => tag.remove());
            
            // Ensure tags is an array before iterating
            if (!Array.isArray(tags)) {
                tags = [];
            }
            
            // Render each tag
            tags.forEach((tag, index) => {
                const chip = document.createElement('span');
                chip.className = 'tag-chip inline-flex items-center gap-1 bg-primary text-white text-xs px-3 py-1.5 rounded-full transition-all hover:bg-primary-dark';
                chip.innerHTML = `
                    <span>${tag}</span>
                    <button type="button" class="remove-tag hover:bg-white/20 rounded-full p-0.5 transition-colors" data-index="${index}" title="Remove tag">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </button>
                `;
                container.insertBefore(chip, tagsInput);
            });
            
            lucide.createIcons();
            tagsHidden.value = JSON.stringify(tags);
            updateTagCounter();
        }
        
        if (tagsInput) {
            // Render existing tags
            renderTags();
            
            // Add tag on Enter or comma
            tagsInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    const tag = this.value.trim().toLowerCase().replace(/[^a-z0-9-]/g, '');
                    
                    if (tag && tags.length < maxTags && !tags.includes(tag)) {
                        tags.push(tag);
                        renderTags();
                        this.value = '';
                    } else if (tags.length >= maxTags) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Tags Reached',
                            text: 'You can only add up to 5 tags',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else if (tags.includes(tag)) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Duplicate Tag',
                            text: 'This tag is already added',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            });
            
            // Remove tag by clicking X button
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-tag')) {
                    const btn = e.target.closest('.remove-tag');
                    const index = parseInt(btn.dataset.index);
                    
                    if (!isNaN(index) && index >= 0 && index < tags.length) {
                        const removedTag = tags[index];
                        tags.splice(index, 1);
                        renderTags();
                        
                        // Optional: Show brief confirmation
                        const toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        });
                        
                        toast.fire({
                            icon: 'success',
                            title: `Tag "${removedTag}" removed`
                        });
                    }
                }
            });
            
            // Add suggested tags
            document.addEventListener('click', function(e) {
                if (e.target.closest('.suggested-tag')) {
                    const btn = e.target.closest('.suggested-tag');
                    const tag = btn.dataset.tag;
                    
                    if (tag && tags.length < maxTags && !tags.includes(tag)) {
                        tags.push(tag);
                        renderTags();
                    } else if (tags.length >= maxTags) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maximum Tags Reached',
                            text: 'You can only add up to 5 tags. Remove a tag to add a new one.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else if (tags.includes(tag)) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Already Added',
                            text: 'This tag is already in your discussion',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }
        
        // Cancel button
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                window.location.href = '<?= base_url('auth/forums/' . $discussion['forum_id']) ?>';
            });
        }
        
        // Attachment management
        let deletedAttachments = [];
        let newFiles = [];
        
        // Handle removing existing attachments
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-existing-attachment')) {
                const btn = e.target.closest('.remove-existing-attachment');
                const attachmentId = btn.dataset.attachmentId;
                const attachmentEl = btn.closest('.existing-attachment');
                
                Swal.fire({
                    title: 'Delete Attachment?',
                    text: 'This will permanently delete the file. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Make AJAX call to delete attachment
                        const formData = new FormData();
                        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                        
                        fetch(`<?= base_url('auth/forums/attachments') ?>/${attachmentId}/delete`, {
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
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message || 'Attachment has been deleted successfully.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                
                                // Remove the element from DOM
                                attachmentEl.remove();
                                
                                // Check if container is empty and hide it
                                const container = document.getElementById('existing-attachments-container');
                                if (container && container.children.length === 0) {
                                    container.closest('div').style.display = 'none';
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'Failed to delete attachment'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred while deleting the attachment'
                            });
                        });
                    }
                });
            }
        });
        
        // Handle new file uploads
        const documentUpload = document.getElementById('document-upload');
        if (documentUpload) {
            documentUpload.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                const preview = document.getElementById('new-attachments-preview');
                
                files.forEach((file, index) => {
                    const fileId = 'new_file_' + Date.now() + '_' + index;
                    newFiles.push({ id: fileId, file: file });
                    
                    const fileSize = (file.size / 1024).toFixed(2);
                    const fileEl = document.createElement('div');
                    fileEl.className = 'new-file-preview';
                    fileEl.dataset.fileId = fileId;
                    fileEl.innerHTML = `
                        <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium truncate">${file.name}</div>
                                <div class="text-xs text-slate-500">${fileSize} KB</div>
                            </div>
                            <button type="button" class="remove-new-file text-red-600 hover:text-red-800" data-file-id="${fileId}">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    `;
                    preview.appendChild(fileEl);
                });
                
                lucide.createIcons();
            });
        }
        
        // Handle removing new files
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-new-file')) {
                const btn = e.target.closest('.remove-new-file');
                const fileId = btn.dataset.fileId;
                newFiles = newFiles.filter(f => f.id !== fileId);
                document.querySelector(`[data-file-id="${fileId}"]`).remove();
            }
        });
        
        // Helper function to format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
        
        // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                
                // Show loading state
                Swal.fire({
                    title: 'Updating Discussion',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form
                const formData = new FormData(form);
                
                // Add new files to FormData
                newFiles.forEach(fileObj => {
                    formData.append('attachments[]', fileObj.file);
                });
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message || 'Discussion updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '<?= base_url('auth/forums/' . $discussion['forum_id']) ?>';
                        });
                    } else {
                        Swal.close();
                        
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const error = document.createElement('div');
                                    error.className = 'invalid-feedback';
                                    error.textContent = data.errors[field];
                                    input.parentElement.appendChild(error);
                                }
                            });
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please fix the errors and try again'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update discussion'
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred'
                    });
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
