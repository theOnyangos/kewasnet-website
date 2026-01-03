<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    // Categories should be passed from controller
    if (!isset($categories)) {
        $categories = [];
    }
    
    $activeView = $activeView ?? 'resources'; // Default to resources view

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
            'pageTitle' => 'Resource Management',
            'pageDescription' => 'Manage document resources and resource categories',
            'breadcrumbs' => [
                ['label' => 'Resources']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Title and Description -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Document Resource Management</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage categories and their resources</p>
                </div>
                <div class="flex space-x-3">
                    <?php if ($activeView === 'categories'): ?>
                    <button type="button" onclick="showCreateCategoryModal()" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="folder-plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Category</span>
                    </button>
                    <?php endif; ?>
                    <?php if ($activeView === 'resources'): ?>
                    <a href="<?= base_url('auth/resources/create') ?>" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Resource</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Navigation -->
            <div class="mb-4 border-b border-gray-200">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <a href="<?= base_url('auth/resources/categories') ?>" 
                       class="<?= $activeView === 'categories' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        <i data-lucide="folder" class="w-4 h-4 mr-2 inline"></i>
                        Categories
                    </a>
                    <a href="<?= base_url('auth/resources') ?>" 
                       class="<?= $activeView === 'resources' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2 inline"></i>
                        All Resources
                    </a>
                </nav>
            </div>

            <!-- Categories Content -->
            <?php if ($activeView === 'categories'): ?>
            <div id="categoriesContent">
                <table id="categoriesTable" class="data-table stripe hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="">Category Name</th>
                            <th class="">Category Description</th>
                            <th class="">Created At</th>
                            <th class="">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <?php endif; ?>

            <!-- Resources Content -->
            <?php if ($activeView === 'resources'): ?>
            <div id="resourcesContent">
                <table id="resourcesTable" class="data-table stripe hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Featured</th>
                            <th>Published</th>
                            <th>Stats</th>
                            <th>Files</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <?php endif; ?>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!-- JavaScript Section -->
<?= $this->section('scripts') ?>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let resourcesTable;
        let categoriesTable;

        const createResourceCategoryURL = "<?= base_url('auth/resources/create-category') ?>";
        let selectedFile = null; // Variable to store selected file

        $(document).ready(function() {
            lucide.createIcons();
            
            <?php if ($activeView === 'categories'): ?>
            // Initialize Categories DataTable
            categoriesTable = $('#categoriesTable').DataTable({
                "processing": true,
                "serverSide": true,
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search resource categories...",
                },
                "ajax": {
                    "url": "<?= base_url('auth/resources/get-categories') ?>",
                    "type": "POST"
                },
                "columns": [
                    // { "data": "id" },
                    { 
                        "data": "name",
                        "render": function(data) {
                            return `<span class="text-sm font-medium">${data}</span>`;
                        }
                    },
                    { 
                        "data": "description",
                        "render": function(data) {
                            return data ? `<span class="text-sm">${data}</span>` : '<span class="text-gray-400">No description</span>';
                        }
                    },
                    { 
                        "data": "created_at",
                        "render": function(data) {
                            return $('<span>').addClass('text-sm font-medium text-gray-500').text(data)[0].outerHTML;
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <div class="flex space-x-2">
                                    <button class="text-primary hover:text-primary-dark" onclick="editCategory(${row.id})" title="Edit Category">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700" onclick="deleteCategory(${row.id})" title="Delete Category">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });
            <?php endif; ?>

            <?php if ($activeView === 'resources'): ?>
            // Initialize Resources DataTable
            resourcesTable = $('#resourcesTable').DataTable({
                "processing": true,
                "serverSide": true,
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search resources...",
                },
                "ajax": {
                    "url": "<?= base_url('auth/resources/get') ?>",
                    "type": "POST"
                },
                "columns": [
                    { 
                        "data": "title",
                        "render": function(data, type, row) {
                            return `
                                <div class="flex items-center">
                                    <div class="mr-3">
                                        <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                                    </div>
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="text-sm font-medium">${data}</span>
                                        ${row.description ? `<span class="text-xs text-gray-500">${row.description.substring(0, 50)}${row.description.length > 50 ? '...' : ''}</span>` : ''}
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { 
                        "data": "category_name",
                        "render": function(data) {
                            return data ? 
                                `<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">${data}</span>` : 
                                '<span class="text-gray-400">Uncategorized</span>';
                        }
                    },
                    { 
                        "data": "is_featured",
                        "render": function(data) {
                            return data == 1 ? 
                                '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full flex items-center justify-center"><i data-lucide="star" class="w-3 h-3 mr-1"></i>Featured</span>' : 
                                '<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full text-center">Regular</span>';
                        }
                    },
                    { 
                        "data": "is_published",
                        "render": function(data) {
                            return data == 1 ? 
                                '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Published</span>' : 
                                '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Draft</span>';
                        }
                    },
                    { 
                        "data": "view_count",
                        "render": function(data, type, row) {
                            return `
                                <div class="flex items-center justify-center gap-2">
                                    <div class="flex items-center justify-center">
                                        <i data-lucide="download" class="w-4 h-4 mr-1 text-gray-500"></i>
                                        <span class="text-sm">${row.total_downloads || 0}</span>
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1 text-gray-500"></i>
                                        <span class="text-sm">${row.view_count || 0}</span>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { 
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <div class="flex items-center justify-center">
                                    <button onclick="viewFile('${row.download_url}')" class="text-blue-500 hover:text-blue-700 px-2 py-1 bg-blue-50 rounded text-xs" onclick="viewResourceFiles('${row.id}')" title="View Files">
                                        <i data-lucide="paperclip" class="w-3 h-3 mr-1 inline"></i>
                                        Files
                                    </button>
                                </div>
                            `;
                        }
                    },
                    { 
                        "data": "created_at",
                        "render": function(data) {
                            const formatedDate = formatDate(data);
                            return `<span class="text-sm text-gray-500">${formatedDate}</span>`;
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const featuredToggle = row.is_featured == 1 ? 
                                `<button class="text-yellow-500 hover:text-yellow-700" onclick="toggleFeatured('${row.id}', 0)" title="Remove from Featured">
                                    <i data-lucide="star-off" class="w-4 h-4"></i>
                                </button>` :
                                `<button class="text-gray-400 hover:text-yellow-500" onclick="toggleFeatured('${row.id}', 1)" title="Mark as Featured">
                                    <i data-lucide="star" class="w-4 h-4"></i>
                                </button>`;
                            
                            return `
                                <div class="flex items-center justify-center space-x-2">
                                    ${featuredToggle}
                                    <button class="text-primary hover:text-primary-dark" onclick="editResource('${row.id}')" title="Edit Resource">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700" onclick="deleteResource('${row.id}')" title="Delete Resource">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                "order": [[6, "desc"]], // Default order by created_at descending
                "columnDefs": [
                    { "width": "25%", "targets": 0 }, // Title column wider
                    { "width": "12%", "targets": [1, 2, 3] }, // Category, Featured, Published
                    { "width": "8%", "targets": [4, 5] }, // Views, Files
                    { "width": "12%", "targets": 6 }, // Date
                    { "width": "15%", "targets": 7 }, // Actions wider
                    { "orderable": false, "targets": [5, 7] } // Disable ordering on Files and Actions columns
                ]
            });
            <?php endif; ?>

            // Initialize Lucide icons
            lucide.createIcons();
            
            <?php if ($activeView === 'categories'): ?>
            // Reinitialize icons when Categories DataTable redraws
            $('#categoriesTable').on('draw.dt', function() {
                lucide.createIcons();
            });
            <?php endif; ?>
            
            <?php if ($activeView === 'resources'): ?>
            // Reinitialize icons when Resources DataTable redraws
            $('#resourcesTable').on('draw.dt', function() {
                lucide.createIcons();
            });
            <?php endif; ?>
        });

        <?php if ($activeView === 'categories'): ?>
            function showCreateCategoryModal() {
                openModal({
                    title: 'Create New Resource Category',
                    iconBg: 'bg-blue-100',
                    content: `<?= view('backendV2/pages/resources/partials/resource_category_form') ?>`,
                    size: 'sm:max-w-2xl',
                    headerClasses: 'sm:flex sm:items-start',
                    onOpen: function() {
                        // Initialize Lucide icons
                        lucide.createIcons();

                        $('#createResourceCategoryForm').on('submit', function(e) {
                            e.preventDefault();
                            
                            const form = this;
                            const submitBtn = $(form).find('button[type="submit"]');
                            const originalBtnText = submitBtn.html();

                            submitBtn.prop('disabled', true);
                            submitBtn.html(`
                                <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                                <span class="z-10">Processing...</span>
                            `);

                            // Prepare form data
                            const formData = new FormData(form);

                            // Send the request
                            $.ajax({
                                url: createResourceCategoryURL,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status === 'success') {
                                        // Handle success
                                        closeModal();
                                        showNotification('success', 'Resource category created successfully');
                                        categoriesTable.ajax.reload(); // Refresh the table
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // Handle error
                                    console.error('Error creating resource category:', error);
                                    showNotification('error', 'Error creating resource category');
                                },
                                complete: function() {
                                    // Reset button state
                                    submitBtn.prop('disabled', false);
                                    submitBtn.html(originalBtnText);
                                }
                            });
                        });
                    },
                    onClose: function() {
                        // Clean up
                        $('#createResourceCategoryForm')[0].reset();
                        $('#progress-bar').css('width', '0%');
                        $('#progress-percent').text('0%');
                    }
                });
            }
        <?php endif; ?>

        function getFileIcon(fileType) {
            const icons = {
                'pdf': 'file-text',
                'msword': 'file-text',
                'wordprocessingml': 'file-text',
                'excel': 'file-spreadsheet',
                'spreadsheetml': 'file-spreadsheet'
            };
            
            const icon = Object.keys(icons).find(key => fileType.includes(key)) || 'file';
            return `<i data-lucide="${icons[icon] || 'file'}" class="w-6 h-6 text-blue-500"></i>`;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i]);
        }

        function editCategory(id) {
            console.log('Edit category:', id);
            // Your edit implementation
        }

        function deleteCategory(id) {
            if(confirm('Are you sure you want to delete this category?')) {
                console.log('Delete category:', id);
                // Your delete implementation
            }
        }

        function editResource(id) {
            console.log('Edit resource:', id);
            // Your edit implementation
        }

        function deleteResource(id) {
            if(confirm('Are you sure you want to delete this resource?')) {
                console.log('Delete resource:', id);
                // Your delete implementation
            }
        }

        function viewFile(url) {
            window.open('<?= base_url("client/view/preview-attachment/") ?>' + url, '_blank');
            resourcesTable.ajax.reload(() => {
                lucide.createIcons();
            });
        }

        function deleteResource(id) {
            Swal.fire({
                title: 'Delete Resource?',
                html: `
                    <div class="text-left">
                        <p class="text-gray-700 mb-3">This action will permanently delete this resource and cannot be undone.</p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-3">
                            <p class="text-sm text-yellow-800">
                                <strong>What will be deleted:</strong>
                            </p>
                            <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                                <li>• Resource information and metadata</li>
                                <li>• All attached files from the server</li>
                                <li>• File attachment records from database</li>
                                <li>• View and download statistics</li>
                            </ul>
                        </div>
                        <p class="text-sm text-gray-600">Are you sure you want to proceed?</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'swal-wide',
                    htmlContainer: 'text-left'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the resource',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('auth/resources/delete/') ?>${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message || 'Resource has been deleted successfully',
                                    icon: 'success',
                                    confirmButtonColor: '#3b82f6'
                                }).then(() => {
                                    if (typeof resourcesTable !== 'undefined') {
                                        resourcesTable.ajax.reload(() => {
                                            lucide.createIcons();
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to delete resource',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'An error occurred while deleting the resource',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    });
                }
            });
        }

        function editResource(id) {
            // Navigate to the edit page instead of opening a modal
            window.location.href = `<?= base_url('auth/resources/edit/') ?>${id}`;
        }

        function editCategory(id) {
            console.log('Edit category:', id);
            // Your edit implementation
        }

        function deleteCategory(id) {
            if(confirm('Are you sure you want to delete this category?')) {
                console.log('Delete category:', id);
                // Your delete implementation
            }
        }

        function getFileIcon(fileType) {
            if (fileType.includes('pdf')) {
                return '<i data-lucide="file-text" class="w-6 h-6 text-red-500"></i>';
            } else if (fileType.includes('word') || fileType.includes('document')) {
                return '<i data-lucide="file-text" class="w-6 h-6 text-blue-500"></i>';
            } else if (fileType.includes('sheet') || fileType.includes('excel')) {
                return '<i data-lucide="file-spreadsheet" class="w-6 h-6 text-green-500"></i>';
            } else {
                return '<i data-lucide="file" class="w-6 h-6 text-gray-500"></i>';
            }
        }

        // New functions for DocumentResource table
        function viewResourceFiles(resourceId) {
            console.log('View files for resource:', resourceId);
            // TODO: Implement file viewing modal
            showNotification('info', 'File viewing feature coming soon');
        }

        function toggleFeatured(resourceId, featuredStatus) {
            const isMarking = featuredStatus == 1;
            
            Swal.fire({
                title: isMarking ? 'Mark as Featured?' : 'Remove from Featured?',
                html: `<div class="text-left">
                    <p class="mb-3">${isMarking ? 'This resource will be highlighted as featured and appear prominently on the website.' : 'This resource will be removed from the featured list.'}</p>
                    <div class="bg-${isMarking ? 'yellow' : 'gray'}-50 border border-${isMarking ? 'yellow' : 'gray'}-200 rounded-lg p-3">
                        <p class="text-sm text-${isMarking ? 'yellow' : 'gray'}-800">
                            ${isMarking ? '<strong>Featured resources:</strong>' : '<strong>Regular resources:</strong>'}
                        </p>
                        <ul class="mt-2 text-sm text-${isMarking ? 'yellow' : 'gray'}-700 space-y-1">
                            <li>• ${isMarking ? 'Displayed in featured section' : 'Shown in regular list'}</li>
                            <li>• ${isMarking ? 'Prioritized in search results' : 'Normal search priority'}</li>
                            <li>• ${isMarking ? 'Visible to all visitors' : 'Accessible to all visitors'}</li>
                        </ul>
                    </div>
                </div>`,
                icon: isMarking ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: isMarking ? '#eab308' : '#6b7280',
                cancelButtonColor: '#d33',
                confirmButtonText: isMarking ? 'Yes, mark as featured' : 'Yes, remove featured',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('auth/resources/toggle-featured/') ?>${resourceId}`,
                        type: 'POST',
                        data: { is_featured: featuredStatus },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: isMarking ? 'Resource marked as featured' : 'Featured status removed',
                                    icon: 'success',
                                    confirmButtonColor: '#3b82f6',
                                    timer: 2000
                                });
                                if (typeof resourcesTable !== 'undefined') {
                                    resourcesTable.ajax.reload();
                                }
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to update',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while updating',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    });
                }
            });
        }

        function initEditFileUpload() {
            let editSelectedFile = null;
            const uploadContainer = $('#edit-upload-container');
            
            // Clear previous content and events
            uploadContainer.off().empty().html(`
                <div class="dz-message">
                    <i data-lucide="upload" class="w-8 h-8 text-slate-400 mx-auto"></i>
                    <p class="text-sm text-slate-500 mt-2">Drag & drop new file here or click to browse</p>
                    <p class="text-xs text-slate-400 mt-1">(Max file size: 20MB)</p>
                </div>
            `);
            lucide.createIcons();

            // Handle click to select file
            uploadContainer.on('click', function() {
                $('<input>').attr({
                    type: 'file',
                    accept: 'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    class: 'hidden'
                }).on('change', function(e) {
                    handleEditFileSelect(e.target.files);
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
                    handleEditFileSelect(e.originalEvent.dataTransfer.files);
                }
            });

            function handleEditFileSelect(files) {
                if (files.length === 0) return;

                const file = files[0];
                const validTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ];

                if (!validTypes.includes(file.type)) {
                    showToast('Please select a valid file type (PDF, Word, Excel)', 'error');
                    return;
                }

                if (file.size > 20 * 1024 * 1024) {
                    showToast('File size must be less than 20MB', 'error');
                    return;
                }

                editSelectedFile = file;
                $('#edit_resource_filename').val(file.name);

                // Show preview
                $('#edit-preview-container').html(`
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <i data-lucide="file" class="w-5 h-5 text-gray-500"></i>
                        <span class="text-sm">${file.name}</span>
                        <span class="text-xs text-gray-400">(${formatFileSize(file.size)})</span>
                        <button type="button" onclick="clearEditFileSelection()" class="text-red-500 hover:text-red-700">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                `);
                lucide.createIcons();

                // Store globally for form submission
                window.editSelectedFile = editSelectedFile;
            }

            window.clearEditFileSelection = function() {
                editSelectedFile = null;
                window.editSelectedFile = null;
                $('#edit_resource_filename').val('');
                $('#edit-preview-container').empty();
            };
        }

        function updateResource() {
            const form = $('#editResourceForm')[0];
            const formData = new FormData(form);
            const submitBtn = $(form).find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
            
            // Add file if one was selected
            if (window.editSelectedFile) {
                formData.append('resource_filename', window.editSelectedFile);
            }

            submitBtn.prop('disabled', true);
            submitBtn.html(`
                <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                <span class="z-10">Updating...</span>
            `);

            // Show progress bar if uploading new file
            if (window.editSelectedFile) {
                $('#edit-upload-progress').removeClass('hidden');
                $('#edit-progress-bar').css('width', '0%');
                $('#edit-progress-percent').text('0%');
            }

            const resourceId = $('#edit_resource_id').val();

            $.ajax({
                url: `<?= base_url('auth/resources/update/') ?>${resourceId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    if (window.editSelectedFile) {
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                $('#edit-progress-bar').css('width', percent + '%');
                                $('#edit-progress-percent').text(percent + '%');
                            }
                        });
                    }
                    return xhr;
                },
                success: function(response) {
                    if (window.editSelectedFile) {
                        $('#edit-progress-bar').css('width', '100%');
                        $('#edit-progress-percent').text('100%');
                    }
                    
                    setTimeout(() => {
                        if (response.status === 'success') {
                            closeModal();
                            showToast('Resource updated successfully', 'success');
                            
                            setTimeout(() => {
                                if (typeof resourcesTable !== 'undefined') {
                                    resourcesTable.ajax.reload(function() {
                                        lucide.createIcons();
                                    });
                                }
                            }, 500);
                        } else {
                            showToast(response.message || 'Failed to update resource', 'error');
                        }
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating resource:', xhr.responseJSON || error);
                    const errorMsg = xhr.responseJSON?.message || 'Failed to update resource';
                    showToast(errorMsg, 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalBtnText);
                    
                    if (window.editSelectedFile) {
                        setTimeout(() => {
                            $('#edit-upload-progress').addClass('hidden');
                            $('#edit-progress-bar').css('width', '0%');
                            $('#edit-progress-percent').text('0%');
                        }, 1000);
                    }
                }
            });
        }

        function removeCurrentFile() {
            if (confirm('Are you sure you want to remove the current file? You can upload a new one to replace it.')) {
                // Mark file for removal
                $('#remove_current_file').val('1');
                
                // Update UI to show file will be removed
                $('#current-file-info').html(`
                    <div class="p-3 bg-red-50 border border-red-200 rounded">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                            <div>
                                <p class="text-sm font-medium text-red-900">File will be removed on save</p>
                                <button type="button" onclick="undoRemoveFile()" class="text-xs text-red-600 hover:text-red-800 underline mt-1">
                                    Undo removal
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                lucide.createIcons();
                
                showToast('File marked for removal. Click "Update Resource" to save changes.', 'info');
            }
        }

        function undoRemoveFile() {
            // Unmark file for removal
            $('#remove_current_file').val('0');
            
            // This will be called from within the modal, so we need to restore the original display
            // We'll need to get the resource data again or store it
            const resourceId = $('#edit_resource_id').val();
            
            // Fetch fresh data to restore the display
            $.ajax({
                url: `<?= base_url('auth/resources/get/') ?>${resourceId}`,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success' && response.data.attachments && response.data.attachments.length > 0) {
                        const attachment = response.data.attachments[0];
                        $('#current_attachment_id').val(attachment.id);
                        $('#current-file-info').html(`
                            <div id="current-file-display-item" class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center space-x-3">
                                    <i data-lucide="file" class="w-5 h-5 text-blue-500"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">${attachment.file_name || attachment.original_name || 'Resource file'}</p>
                                        <p class="text-xs text-gray-500">${formatFileSize(attachment.file_size || 0)}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="<?= base_url('client/view/preview-attachment/') ?>${attachment.file_path}" 
                                       target="_blank" 
                                       class="text-primary hover:text-primary-dark text-sm font-medium">
                                        <i data-lucide="eye" class="w-4 h-4 inline"></i> View
                                    </a>
                                    <button type="button" onclick="removeCurrentFile()" class="text-red-500 hover:text-red-700 text-sm font-medium">
                                        <i data-lucide="trash-2" class="w-4 h-4 inline"></i> Remove
                                    </button>
                                </div>
                            </div>
                        `);
                        lucide.createIcons();
                        showToast('File removal cancelled', 'success');
                    }
                },
                error: function() {
                    showToast('Error restoring file display', 'error');
                }
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function showToast(message, type = 'info') {
            // Remove any existing toasts
            $('.custom-toast').remove();
            
            const icon = type === 'success' ? 'check-circle' : 
                        type === 'error' ? 'alert-circle' : 'info';
            
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            
            const toast = $(`
                <div class="custom-toast fixed top-4 right-4 px-4 py-3 rounded-md shadow-lg text-white z-50 ${bgColor}">
                    <div class="flex items-center">
                        <i data-lucide="${icon}" class="w-5 h-5 mr-2"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `).appendTo('body');
            
            // Initialize the icon
            lucide.createIcons();
            
            // Animate in
            toast.hide().fadeIn(300);
            
            // Auto remove after 3 seconds
            setTimeout(function() {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    </script>
<?= $this->endSection() ?>