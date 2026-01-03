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
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/pillars/partials/header_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/pillars/partials/navigation_section') ?>
        
        <!-- Overview Tab Content - Pillar Resource Categories -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Pillar Resource</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage pillar resource categories and view detailed statistics</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="showCreateCategoryModal()" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="folder-plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Category</span>
                    </button>
                </div>
            </div>

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
                        return `<div class="flex flex-col items-start space-y-2">
                            <div class="text-sm font-medium text-gray-900">${row.pillar_title || 'No Pillar'}</div>
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                ${row.resource_count || 0} resource${(row.resource_count || 0) !== 1 ? 's' : ''}
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
        console.log('View category:', id);
    }

    function editCategory(id) {
        // Implementation for editing category
        console.log('Edit category:', id);
    }

    // Confirmation Modal HTML
    const confirmModalHtml = `
        <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
                <div class="flex flex-col gap-5 items-center justify-center mb-3">
                    <div class="h-20 w-20 flex justify-center items-center rounded-full bg-red-50 border border-red-100">
                        <i data-lucide="alert-triangle" class="w-10 h-10 text-red-500"></i>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">Confirm Deletion?</h3>
                </div>
                <div class="p-3 bg-red-50 rounded-md mb-6 border border-red-100">
                    <p class="text-dark">Are you sure you want to delete this category? This action may interfere with the normal operation of some resources. Once triggered this cannot be undone.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button id="cancelDeleteBtn" class="px-8 py-2 rounded-[50px] bg-red-100 text-red-700 hover:bg-red-200">
                        <i data-lucide="circle-minus" class="w-4 h-4 text-red-500 inline-block"></i>
                        Cancel
                    </button>
                    <button id="confirmDeleteBtn" class="px-8 py-2 rounded-[50px] bg-gradient-to-br from-red-800 via-red-500 to-red-800 text-white hover:bg-red-700">
                        <i data-lucide="trash-2" class="w-4 h-4 text-white inline-block"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    `;
    if (!document.getElementById('confirmModal')) {
        $('body').append(confirmModalHtml);
        lucide.createIcons();
    }

    let deleteCategoryId = null;
    function deleteCategory(id) {
        deleteCategoryId = id;
        $('#confirmModal').removeClass('hidden').addClass('flex');
    }

    // Modal button handlers
    $(document).on('click', '#cancelDeleteBtn', function() {
        $('#confirmModal').addClass('hidden').removeClass('flex');
        deleteCategoryId = null;
    });
    $(document).on('click', '#confirmDeleteBtn', function() {
        if (!deleteCategoryId) return;
        $('#confirmModal').addClass('hidden').removeClass('flex');
        $.ajax({
            url: `<?= site_url('auth/pillars/delete-category') ?>/${deleteCategoryId}`,
            type: 'POST',
            data: {
                csrf_test_name: '<?= csrf_token() ?>',
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showNotification('success', 'Category deleted successfully');
                } else {
                    showNotification('error', response.message || 'Error deleting category');
                }
            },
            error: function(xhr) {
                showNotification('error', 'Error deleting category');
            },
            complete: function() {
                categoriesTable.ajax.reload(function() {
                    lucide.createIcons();
                });
            }
        });
        deleteCategoryId = null;
    });

    function showToast(message, type) {
        // Implementation for showing toast notifications
        console.log(type + ': ' + message);
    }
</script>
<?= $this->endSection() ?>