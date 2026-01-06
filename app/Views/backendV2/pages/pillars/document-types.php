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
    <main class="flex-1 overflow-y-auto">
        <!-- Page Banner -->
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Document Types',
            'pageDescription' => 'Manage document types and classifications for pillar articles',
            'breadcrumbs' => [
                ['label' => 'Pillars', 'url' => base_url('auth/pillars')],
                ['label' => 'Document Types']
            ],
            'bannerActions' => '<button type="button" onclick="showCreateDocumentTypeModal()" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="file-type" class="w-4 h-4 mr-2"></i>
                Add Document Type
            </button>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/pillars/partials/navigation_section') ?>
        
        <!-- Document Types Management Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">

            <!-- Document Types Table -->
            <div class="overflow-x-auto">
                <table id="documentTypesTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Document Type</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Color</th>
                            <th scope="col">Articles</th>
                            <th scope="col">Created</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Document Types DataTable
    let documentTypesTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Document Types DataTable
        documentTypesTable = $('#documentTypesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/pillars/get-document-types') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "name",
                    "render": function(data, type, row) {
                        const color = row.color || '#6B7280';
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold" style="background-color: ${color};">
                                    <i data-lucide="file-type" class="w-5 h-5"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">Document Type</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "slug",
                    "className": "text-left",
                    "render": function(data) {
                        return `<code class="bg-gray-100 text-gray-800 text-xs font-mono px-2 py-1 rounded">${data}</code>`;
                    }
                },
                {
                    "data": "color",
                    "className": "text-left",
                    "render": function(data) {
                        if (!data) return '<span class="text-gray-500">No color</span>';
                        return `
                            <div class="flex items-center justify-start">
                                <div class="w-6 h-6 rounded-full border border-gray-300" style="background-color: ${data};" title="${data}"></div>
                                <span class="ml-2 text-xs text-gray-500">${data}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "resources_count",
                    "className": "text-center",
                    "render": function(data) {
                        return `<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">${data || 0}</span>`;
                    }
                },
                {
                    "data": "created_at",
                    "className": "text-left",
                    "render": function(data) {
                        const formattedDate = formatDate(data);

                        return `
                            <div class="flex items-center justify-start">
                                <i data-lucide="calendar" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm">${formattedDate}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "px-6 py-4 whitespace-nowrap text-right text-sm font-medium",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex justify-start space-x-1">
                                <button onClick="editDocumentType('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                        title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onClick="duplicateDocumentType('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 hover:bg-purple-200 text-purple-600 hover:text-purple-700 rounded-md transition-colors duration-200"
                                        title="Duplicate">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                </button>
                                <button onClick="deleteDocumentType('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 rounded-md transition-colors duration-200"
                                        title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search document types...",
            },
            "order": [[4, 'desc']], // Order by created_at descending
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50');
            },
            "initComplete": function() {
                lucide.createIcons();
            }
        });
    });


    // Function to show create document type modal
    function showCreateDocumentTypeModal() {
        openModal({
            title: 'Create Document Type',
            iconBg: 'bg-blue-100',
            content: `<?= view('backendV2/pages/pillars/partials/create_document_type_form') ?>`,
            size: 'sm:max-w-2xl',
            headerClasses: 'sm:flex sm:items-start', // Add this if you need custom header classes
            onOpen: function() {
                // Initialize form elements
                lucide.createIcons();

                const $submitBtn = $('#submitBtn');
                const slugInput = document.getElementById('articleSlug');
                const nameInput = document.getElementById('documentTypeName')

                // Auto-generate slug from title
                nameInput.addEventListener('input', function() {
                    const title = this.value;
                    const slug = title
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '') // Remove special characters
                        .replace(/\s+/g, '-')     // Replace spaces with hyphens
                        .replace(/-+/g, '-')      // Replace multiple hyphens with single
                        .substring(0, 100);       // Limit to 100 characters
                    slugInput.value = slug;
                });

                // Create Document Type
                $('form').on('submit', function(e) {
                    e.preventDefault();
                    clearFormErrors();

                    $submitBtn.prop('disabled', true).html('<span class="flex items-center"><i data-lucide="loader" class="w-4 h-4 animate-spin mr-2"></i> Posting...</span>');
                    lucide.createIcons();

                    const form = this;
                    const formData = new FormData(form);
                    formData.append('csrf_test_name', '<?= csrf_hash() ?>');

                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            $submitBtn.prop('disabled', false).html('<span>Create Document Type</span>');
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message || 'Document type created successfully',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reset Form
                                    $(form)[0].reset();
                                    // Clear Error
                                    clearFormErrors();
                                    // Close the modal
                                    closeModal();
                                    // Reload table
                                    documentTypesTable.ajax.reload(function() {
                                        lucide.createIcons();
                                    });
                                });
                            } else if (response.errors) {
                                showFormErrors(response.errors);
                                Swal.fire({
                                    title: 'Validation Error',
                                    html: '<p class="text-gray-700">Please fix the errors and try again.</p>',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'An unexpected error occurred. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            $submitBtn.prop('disabled', false).html('<span>Create Document Type</span>');
                            const response = xhr.responseJSON || {};
                            const errors = response.errors || {};
                            if (Object.keys(errors).length > 0) {
                                showFormErrors(errors);
                                Swal.fire({
                                    title: 'Validation Error',
                                    html: '<p class="text-gray-700">Please fix the errors and try again.</p>',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to create document type',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }
                    });
                });
            },
            onClose: function() {
                // Cleanup or reset actions
                $('#documentTypeForm')[0].reset();
                lucide.createIcons();
            }
        });
    }

    function viewDocumentType(id) {
        // Implementation for viewing document type details
        console.log('View document type:', id);
    }

    function editDocumentType(id) {
        // Redirect to edit page
        window.location.href = '<?= site_url('auth/pillars/edit-document-type') ?>/' + id;
    }

    function duplicateDocumentType(id) {
        Swal.fire({
            title: 'Duplicate Document Type?',
            html: '<p class="text-gray-700 mb-4">Are you sure you want to duplicate this document type?</p><p class="text-sm text-gray-500">A new document type will be created with the same properties.</p>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#8b5cf6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i data-lucide="copy" class="w-4 h-4 mr-1"></i> Yes, duplicate it',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Duplicating...',
                    text: 'Please wait while we duplicate the document type',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= site_url('auth/pillars/duplicate-document-type') ?>/${id}`,
                    type: 'POST',
                    data: {
                        csrf_test_name: '<?= csrf_token() ?>',
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Duplicated!',
                                text: response.message || 'Document type duplicated successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                documentTypesTable.ajax.reload(function() {
                                    lucide.createIcons();
                                });
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Error duplicating document type',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error duplicating document type';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    function deleteDocumentType(id) {
        Swal.fire({
            title: 'Are you sure?',
            html: '<p class="text-gray-700 mb-4">Are you sure you want to delete this document type?</p><p class="text-sm text-gray-500">This action may interfere with the normal operation of some resources. Once triggered, this cannot be undone.</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Yes, delete it',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the document type',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= site_url('auth/pillars/delete-document-type') ?>/${id}`,
                    type: 'POST',
                    data: {
                        csrf_test_name: '<?= csrf_token() ?>',
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Document type deleted successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                documentTypesTable.ajax.reload(function() {
                                    lucide.createIcons();
                                });
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Error deleting document type',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error deleting document type';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        documentTypesTable.ajax.reload(function() {
                            lucide.createIcons();
                        });
                    }
                });
            }
        });
    }

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

    function clearFormErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }
</script>
<?= $this->endSection() ?>