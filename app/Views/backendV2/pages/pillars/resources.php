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
            'pageTitle' => 'Resource Categories',
            'pageDescription' => 'Manage pillar resource categories and view detailed statistics',
            'breadcrumbs' => [
                ['label' => 'Pillars', 'url' => base_url('auth/pillars')],
                ['label' => 'Resource Categories']
            ],
            'bannerActions' => '<button type="button" onclick="showCreateCategoryModal()" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="folder-plus" class="w-4 h-4 mr-2"></i>
                Add Category
            </button>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/pillars/partials/navigation_section') ?>
        
        <!-- Overview Tab Content - Pillar Resource Categories -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">

            <!-- Resource Categories Table -->
            <div class="overflow-x-auto">
                <table id="categoriesTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Category</th>
                            <th scope="col">Description</th>
                            <th scope="col">Pillar & Resources</th>
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
    // Initialize Categories DataTable
    let categoriesTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Resource Categories DataTable
        categoriesTable = $('#categoriesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/pillars/get-categories') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "name",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary via-secondary to-primary rounded-lg flex items-center justify-center text-white font-bold">
                                    ${data.substring(0, 2).toUpperCase()}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">Resource Category</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "description",
                    "render": function(data) {
                        if (!data) return '<div class="text-sm text-gray-600">No description</div>';
                        const maxLength = 60;
                        return data.length > maxLength 
                            ? `<div class="text-sm text-gray-600" title="${data}">${data.substring(0, maxLength)}...</div>`
                            : `<div class="text-sm text-gray-600">${data}</div>`;
                    }
                },
                {
                    "data": null,
                    "className": "text-left",
                    "render": function(data, type, row) {
                        const resourceCount = parseInt(row.resource_count || 0);
                        return `<div class="flex flex-col items-start space-y-2">
                            <div class="text-sm font-medium text-gray-900">${row.pillar_title || 'No Pillar'}</div>
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                ${resourceCount} resource${resourceCount !== 1 ? 's' : ''}
                            </span>
                        </div>`;
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
                                <button onClick="viewCategory('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-md transition-colors duration-200"
                                        title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button onClick="editCategory('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                        title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onClick="deleteCategory('${row.id}')" 
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
                "searchPlaceholder": "Search categories...",
            },
            "order": [[3, 'desc']], // Order by created_at descending
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50');
            },
            "initComplete": function() {
                lucide.createIcons();
            }
        });
    });

    // Action functions
    function showCreateCategoryModal() {
        // Implementation for showing create category modal
        window.open('<?= site_url('auth/pillars/create-resource-category') ?>', '_blank');
    }

    function viewCategory(id) {
        // Implementation for viewing category details
        Swal.fire({
            title: 'Category Details',
            text: 'Category details view coming soon',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    }

    function editCategory(id) {
        // Redirect to edit page
        window.location.href = '<?= site_url('auth/pillars/edit-resource-category') ?>/' + id;
    }

    function deleteCategory(id) {
        Swal.fire({
            title: 'Are you sure?',
            html: '<p class="text-gray-700 mb-4">Are you sure you want to delete this category?</p><p class="text-sm text-gray-500">This action may interfere with the normal operation of some resources. Once triggered, this cannot be undone.</p>',
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
                    text: 'Please wait while we delete the category',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= site_url('auth/pillars/delete-category') ?>/${id}`,
                    type: 'POST',
                    data: {
                        csrf_test_name: '<?= csrf_token() ?>',
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Category deleted successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                categoriesTable.ajax.reload(function() {
                                    lucide.createIcons();
                                });
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Error deleting category',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error deleting category';
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
                        categoriesTable.ajax.reload(function() {
                            lucide.createIcons();
                        });
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>