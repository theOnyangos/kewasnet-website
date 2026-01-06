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
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Job Opportunities',
            'pageDescription' => 'Create and manage job opportunities for your organization',
            'breadcrumbs' => [
                ['label' => 'Opportunities']
            ]
        ]) ?>

 <!-- Navigation Menu -->
        <div class="px-6 pb-6">
            <?= $this->include('backendV2/pages/opportunities/partials/navigation_menu') ?>
            
            <!-- Page Content -->
            <div class="bg-white rounded-b-xl shadow-sm p-6">
                <!-- Top Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Job Opportunities</h1>
                        <p class="mt-1 text-sm text-slate-500">Create and manage job opportunities for your organization</p>
                    </div>
                    <div class="flex space-x-3 mt-4 md:mt-0">
                        <button type="button" onClick="window.location.href='<?= site_url('auth/opportunities/create') ?>'" 
                                class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                            <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                            <span>Add Opportunity</span>
                        </button>
                    </div>
                </div>

                <!-- Opportunities Table -->
                <div class="overflow-x-auto">
                    <table id="opportunitiesTable" class="data-table stripe hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Applications</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Initialize Opportunities DataTable
        const opportunitiesTable = $('#opportunitiesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search opportunities...",
                "lengthMenu": "Show _MENU_ opportunities per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ opportunities",
                "infoEmpty": "No opportunities found",
                "infoFiltered": "(filtered from _MAX_ total opportunities)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "ajax": {
                "url": "<?= base_url('auth/opportunities/get') ?>",
                "type": "POST",
                "error": function(xhr, error, code) {
                    console.error('DataTable Ajax Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            },
            "columns": [
                { 
                    data: 'title', 
                    name: 'title',
                    render: function(data, type, row) {
                        return `<div class="font-medium text-gray-900 flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-4 h-4 text-secondary"></i>
                        <span class="truncate text-sm">${data}</span>
                        </div>`;
                    }
                },
                { 
                    data: 'opportunity_type', 
                    name: 'opportunity_type',
                    render: function(data, type, row) {
                        const typeMap = {
                            'full-time': '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Full-time</span>',
                            'part-time': '<span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Part-time</span>',
                            'contract': '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Contract</span>',
                            'internship': '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Internship</span>',
                            'freelance': '<span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">Freelance</span>'
                        };
                        return typeMap[data] || data;
                    }
                },
                { 
                    data: 'company', 
                    name: 'company',
                    render: function(data, type, row) {
                        return `<span class="truncate text-sm">${data || '<span class="text-gray-400">N/A</span>'}</span>`;
                    }
                },
                { 
                    data: 'location', 
                    name: 'location',
                    render: function(data, type, row) {
                        if (row.is_remote) {
                            return '<span class="text-green-600 text-sm"><i data-lucide="globe" class="w-4 h-4 inline mr-1"></i>Remote</span>';
                        }
                        return data || '<span class="text-gray-400">N/A</span>';
                    }
                },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data, type, row) {
                        const statusMap = {
                            'draft': '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Draft</span>',
                            'published': '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Published</span>',
                            'closed': '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Closed</span>'
                        };
                        return statusMap[data] || data;
                    }
                },
                { 
                    data: 'application_count', 
                    name: 'application_count',
                    render: function(data, type, row) {
                        const count = data || 0;
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${count}</span>`;
                    }
                },
                { 
                    data: 'created_at', 
                    name: 'created_at',
                    render: function(data, type, row) {
                        const formattedDate = formatDate(new Date(data));
                        return `<span class="text-gray-600 text-sm">${formattedDate}</span>`;
                    }
                },
                {
                    data: 'id',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 p-1" onclick="viewOpportunity('${row.slug}')" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 p-1" onclick="editOpportunity('${data}')" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900 p-1" onclick="deleteOpportunity('${data}')" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "order": [[6, 'desc']], // Order by created_at descending
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Reinitialize Lucide icons after DataTable draw
        opportunitiesTable.on('draw', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        // Action functions
        window.viewOpportunity = function(slug) {
            // Navigate to frontend opportunity page
            window.location.href = `<?= base_url('opportunities') ?>/${slug}`;
        };
        
        window.editOpportunity = function(id) {
            // Implement edit functionality
            window.location.href = `<?= site_url('auth/opportunities/edit') ?>/${id}`;
        };
        
        window.deleteOpportunity = function(id) {
            Swal.fire({
                title: 'Delete Opportunity?',
                text: "This action cannot be undone. All associated data will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'px-4 py-2 rounded-lg',
                    cancelButton: 'px-4 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the opportunity',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Implement delete functionality
                    fetch(`<?= site_url('auth/opportunities/delete') ?>/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' || data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The opportunity has been deleted successfully.',
                                icon: 'success',
                                confirmButtonColor: '#059669',
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                // Reload table after success message
                                opportunitiesTable.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to delete opportunity',
                                icon: 'error',
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while deleting the opportunity',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    });
                }
            });
        };
    });
</script>
<?= $this->endSection() ?>