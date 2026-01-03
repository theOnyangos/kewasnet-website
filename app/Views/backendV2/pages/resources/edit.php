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
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?= base_url('auth/dashboard') ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                        <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        <a href="<?= base_url('auth/resources') ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">
                            Resources
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Resource</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Back Button -->
        <div class="mb-6">
            <a href="<?= base_url('auth/resources') ?>" class="inline-flex items-center text-slate-600 hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                Back to Resources
            </a>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Edit Resource</h1>
                <p class="mt-1 text-sm text-slate-500">Update resource information and manage attached files</p>
            </div>

            <form id="editResourceForm" class="space-y-6" enctype="multipart/form-data" method="post" action="<?= base_url('auth/resources/update/' . $resource['id']) ?>">
                <!-- Resource Name -->
                <div>
                    <label for="resource_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Resource Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="resource_name" 
                           name="resource_name" 
                           value="<?= esc($resource['title']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           required>
                </div>

                <!-- Resource Description -->
                <div>
                    <label for="resource_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="resource_description" 
                              name="resource_description" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?= esc($resource['description']) ?></textarea>
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" 
                            name="category_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= esc($category['id']) ?>" <?= $resource['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Featured Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_featured" 
                           name="is_featured" 
                           value="1"
                           <?= isset($resource['is_featured']) && $resource['is_featured'] ? 'checked' : '' ?>
                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700">
                        Feature this resource
                    </label>
                </div>

                <!-- Current File Attachment Section -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        File Attachments 
                        <?php if (!empty($attachments)): ?>
                            <span class="text-sm font-normal text-gray-500">(<?= count($attachments) ?> file<?= count($attachments) > 1 ? 's' : '' ?>)</span>
                        <?php endif; ?>
                    </h3>
                    
                    <?php if (!empty($attachments)): ?>
                        <div id="current-files-display" class="space-y-3 mb-4">
                            <?php foreach ($attachments as $index => $attachment): ?>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200" data-attachment-id="<?= esc($attachment['id']) ?>">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <!-- File Icon -->
                                            <div class="flex-shrink-0">
                                                <?php 
                                                    $fileExt = strtolower($attachment['file_type'] ?? 'file');
                                                    $iconColor = 'text-gray-500';
                                                    if ($fileExt === 'pdf') $iconColor = 'text-red-500';
                                                    elseif (in_array($fileExt, ['doc', 'docx'])) $iconColor = 'text-blue-500';
                                                    elseif (in_array($fileExt, ['xls', 'xlsx'])) $iconColor = 'text-green-500';
                                                    elseif (in_array($fileExt, ['ppt', 'pptx'])) $iconColor = 'text-orange-500';
                                                ?>
                                                <i data-lucide="file-text" class="w-10 h-10 <?= $iconColor ?>"></i>
                                            </div>
                                            
                                            <!-- File Info -->
                                            <div>
                                                <p class="font-medium text-gray-900"><?= esc($attachment['original_name']) ?></p>
                                                <p class="text-sm text-gray-500">
                                                    <?= number_format($attachment['file_size'] / 1024 / 1024, 2) ?> MB
                                                    <?php if (isset($attachment['created_at'])): ?>
                                                        • Uploaded <?= date('M d, Y', strtotime($attachment['created_at'])) ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- File Actions -->
                                        <div class="flex space-x-2">
                                            <a href="<?= base_url('client/view/preview-attachment/' . $attachment['file_path']) ?>" 
                                               target="_blank"
                                               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                                View
                                            </a>
                                            <button type="button" 
                                                    onclick="removeAttachment('<?= esc($attachment['id']) ?>')"
                                                    class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Hidden input to track files marked for removal -->
                        <input type="hidden" id="files_to_remove" name="files_to_remove" value="">
                    <?php else: ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-yellow-800">No files currently attached to this resource.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Upload New/Additional Files -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?= !empty($attachments) ? 'Add More Files (Optional)' : 'Upload Files' ?>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors">
                            <div class="space-y-1 text-center">
                                <i data-lucide="upload-cloud" class="mx-auto h-12 w-12 text-gray-400"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="resource_filename" class="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary-dark">
                                        <span>Upload a file</span>
                                        <input id="resource_filename" 
                                               name="resource_filename[]" 
                                               type="file" 
                                               class="sr-only"
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                               onchange="displaySelectedFiles(this)"
                                               multiple>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX up to 20MB</p>
                                <p id="selected-file-name" class="text-sm text-gray-700 font-medium mt-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="<?= base_url('auth/resources') ?>" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300">
                        <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                        Update Resource
                    </button>
                </div>
            </form>
        </div>
    </main>
<?= $this->endSection() ?>

<!-- JavaScript Section -->
<?= $this->section('scripts') ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const resourceId = '<?= esc($resource['id']) ?>';

    // Display selected files
    function displaySelectedFiles(input) {
        const fileNameDisplay = document.getElementById('selected-file-name');
        if (input.files && input.files.length > 0) {
            if (input.files.length === 1) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                fileNameDisplay.textContent = `Selected: ${fileName} (${fileSize} MB)`;
            } else {
                const totalSize = Array.from(input.files).reduce((sum, file) => sum + file.size, 0);
                const totalSizeMB = (totalSize / 1024 / 1024).toFixed(2);
                fileNameDisplay.innerHTML = `
                    <div class="text-left">
                        <p class="font-medium">${input.files.length} files selected (${totalSizeMB} MB total)</p>
                        <ul class="mt-1 text-xs space-y-1">
                            ${Array.from(input.files).map(file => `
                                <li>• ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            }
        } else {
            fileNameDisplay.textContent = '';
        }
    }

    // Remove an attachment
    function removeAttachment(attachmentId) {
        Swal.fire({
            title: 'Remove File?',
            text: 'Are you sure you want to remove this file? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Get current list of files to remove
                const filesToRemoveInput = document.getElementById('files_to_remove');
                let filesToRemove = filesToRemoveInput.value ? filesToRemoveInput.value.split(',') : [];
                
                // Add this attachment ID to the list
                if (!filesToRemove.includes(attachmentId)) {
                    filesToRemove.push(attachmentId);
                    filesToRemoveInput.value = filesToRemove.join(',');
                }
                
                // Hide the file display element
                const fileElement = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
                if (fileElement) {
                    fileElement.style.display = 'none';
                }
                
                // Show success message
                Swal.fire({
                    title: 'Marked for Removal',
                    text: 'The file will be removed when you save the changes.',
                    icon: 'info',
                    confirmButtonColor: '#3b82f6',
                    timer: 2000
                });
            }
        });
    }

    // Handle form submission
    document.getElementById('editResourceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonContent = submitButton.innerHTML;
        
        // Disable button and show loading
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 inline animate-spin"></i> Updating...';
        lucide.createIcons();
        
        fetch(`<?= base_url('auth/resources/update/') ?>${resourceId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Resource updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    window.location.href = '<?= base_url('auth/resources') ?>';
                });
            } else {
                throw new Error(data.message || 'Failed to update resource');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An error occurred while updating the resource',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        })
        .finally(() => {
            // Re-enable button
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonContent;
            lucide.createIcons();
        });
    });

    // Initialize Lucide icons
    lucide.createIcons();
</script>
<?= $this->endSection() ?>
