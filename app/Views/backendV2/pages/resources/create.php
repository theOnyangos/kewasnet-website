<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    // Categories should be passed from controller
    if (!isset($categories)) {
        $categories = [];
    }

    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?? 'Create New Resource' ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Breadcrumb -->
        <nav class="flex mb-4 bg-white p-3 rounded-md" aria-label="Breadcrumb">
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
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create New Resource</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-6">
            <a href="<?= base_url('auth/resources') ?>" class="inline-flex items-center text-slate-600 hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                <span>Back to Resources</span>
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 max-w-full">
            <h1 class="text-2xl font-bold text-slate-800 mb-6">Create New Resource</h1>

            <form id="createResourceForm" class="space-y-6" enctype="multipart/form-data" method="post" action="<?= base_url('auth/resources/create') ?>">
                <!-- Resource Title -->
                <div>
                    <label for="resource_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Resource Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="resource_title"
                           name="resource_title"
                           required
                           class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                           placeholder="Enter resource title">
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id"
                            name="category_id"
                            required
                            class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->id ?>"><?= esc($category->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label for="resource_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="resource_description"
                              name="resource_description"
                              required
                              rows="4"
                              class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                              placeholder="Enter resource description"></textarea>
                </div>

                <!-- File Upload Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Attach Files <span class="text-red-500">*</span>
                    </label>
                    <div id="upload-container" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-primary transition-colors">
                        <div class="dz-message">
                            <i data-lucide="upload" class="w-8 h-8 text-slate-400 mx-auto"></i>
                            <p class="text-sm text-slate-500 mt-2">Drag & drop your files here or click to browse</p>
                            <p class="text-xs text-slate-400 mt-1">(Max file size: 20MB per file. Select multiple files)</p>
                        </div>
                    </div>
                    <div id="preview-container" class="mt-4"></div>
                </div>

                <!-- Upload Progress -->
                <div id="upload-progress" class="hidden">
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-primary bg-primary-100">
                                    Uploading
                                </span>
                            </div>
                            <div class="text-right">
                                <span id="progress-percent" class="text-xs font-semibold inline-block text-primary">
                                    0%
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-primary-100">
                            <div id="progress-bar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary transition-all duration-300"></div>
                        </div>
                    </div>
                </div>

                <!-- Published Status -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_published" 
                           name="is_published" 
                           value="1"
                           class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2">
                    <label for="is_published" class="ml-2 text-sm font-medium text-gray-700">
                        Publish this resource immediately
                    </label>
                </div>

                <!-- Featured Status -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_featured" 
                           name="is_featured" 
                           value="1"
                           class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2">
                    <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700">
                        Feature this resource
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" 
                            class="gradient-btn flex items-center justify-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="save" class="w-4 h-4 mr-2 z-10"></i>
                        <span class="z-10">Create Resource</span>
                    </button>
                    <a href="<?= base_url('auth/resources') ?>" 
                       class="inline-flex items-center justify-center px-8 py-2 border border-gray-300 rounded-full text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        <span>Cancel</span>
                    </a>
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
        // Global variable to store selected files
        window.selectedFiles = [];

        $(document).ready(function() {
            lucide.createIcons();
            initFileUpload();

            // Submit Resource Form
            $('#createResourceForm').on('submit', function(e) {
                e.preventDefault();

                if (!window.selectedFiles || window.selectedFiles.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No Files Selected',
                        text: 'Please select at least one file to upload',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                const form = this;
                const submitBtn = $(form).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                
                submitBtn.prop('disabled', true);
                submitBtn.html(`
                    <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                    <span class="z-10">Processing...</span>
                `);
                
                // Show progress bar
                $('#upload-progress').removeClass('hidden');
                $('#progress-bar').css('width', '0%');
                $('#progress-percent').text('0%');

                // Prepare form data
                const formData = new FormData(form);
                
                // Add all selected files to form data
                window.selectedFiles.forEach((file, index) => {
                    formData.append('resource_filename[]', file);
                });

                // Send the request
                $.ajax({
                    url: "<?= base_url('auth/resources/create') ?>",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                $('#progress-bar').css('width', percent + '%');
                                $('#progress-percent').text(percent + '%');
                            }
                        });
                        return xhr;
                    },
                    success: function(response) {
                        // Complete the progress bar
                        $('#progress-bar').css('width', '100%');
                        $('#progress-percent').text('100%');
                        
                        setTimeout(() => {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Resource created successfully',
                                    confirmButtonColor: '#3b82f6'
                                }).then(() => {
                                    window.location.href = '<?= base_url('auth/resources') ?>';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to create resource',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        }, 500);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error creating resource:', xhr.responseJSON || error);
                        const errorMsg = xhr.responseJSON?.message || 'Failed to create resource';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalBtnText);
                        
                        // Hide progress bar after a delay
                        setTimeout(() => {
                            $('#upload-progress').addClass('hidden');
                            $('#progress-bar').css('width', '0%');
                            $('#progress-percent').text('0%');
                        }, 1000);
                    }
                });
            });
        });

        function initFileUpload() {
            const uploadContainer = $('#upload-container');
            
            // Handle click to select file(s)
            uploadContainer.on('click', function() {
                $('<input>').attr({
                    type: 'file',
                    accept: 'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    multiple: 'multiple',
                    class: 'hidden'
                }).on('change', function(e) {
                    handleFileSelect(e.target.files);
                }).click();
            });

            // Handle drag and drop
            uploadContainer.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-primary-500 bg-gray-50');
            }).on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('border-primary-500 bg-gray-50');
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-primary-500 bg-gray-50');
                if (e.originalEvent.dataTransfer.files.length) {
                    handleFileSelect(e.originalEvent.dataTransfer.files);
                }
            });
        }

        function handleFileSelect(files) {
            if (files.length === 0) return;

            // Reset selectedFiles array
            window.selectedFiles = [];

            const validTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ];
            
            const maxSize = 20 * 1024 * 1024; // 20MB
            let hasError = false;

            // Validate each file
            Array.from(files).forEach(file => {
                if (!validTypes.some(type => file.type.match(type))) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type',
                        text: `${file.name} is not a supported file type`,
                        confirmButtonColor: '#ef4444'
                    });
                    hasError = true;
                    return;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: `${file.name} exceeds the 20MB limit`,
                        confirmButtonColor: '#ef4444'
                    });
                    hasError = true;
                    return;
                }

                window.selectedFiles.push(file);
            });

            if (!hasError && window.selectedFiles.length > 0) {
                showFilePreview();
            }
        }

        function showFilePreview() {
            const previewContainer = $('#preview-container');
            if (!window.selectedFiles || window.selectedFiles.length === 0) return;

            const filesHtml = window.selectedFiles.map((file, index) => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                return `
                    <div class="flex items-center p-2 bg-gray-50 rounded border mb-1">
                        <i data-lucide="file" class="w-4 h-4 mr-2 text-gray-500"></i>
                        <span class="text-sm flex-1">${file.name} (${fileSize} MB)</span>
                        <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                `;
            }).join('');

            previewContainer.html(`
                <div class="mt-2">
                    <p class="text-sm font-medium mb-1">${window.selectedFiles.length} file(s) selected</p>
                    ${filesHtml}
                </div>
            `);
            lucide.createIcons();
        }

        function removeFile(index) {
            window.selectedFiles.splice(index, 1);
            if (window.selectedFiles.length === 0) {
                $('#preview-container').empty();
            } else {
                showFilePreview();
            }
        }
    </script>
<?= $this->endSection() ?>
