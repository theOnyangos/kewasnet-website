<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
Job Applications
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Job Applications',
            'pageDescription' => 'Review and manage applications for your job opportunities',
            'breadcrumbs' => [
                ['label' => 'Opportunities', 'url' => site_url('auth/opportunities')],
                ['label' => 'Applications']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Navigation Menu -->
        <?= $this->include('backendV2/pages/opportunities/partials/navigation_menu') ?>
        
        <!-- Page Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <!-- Top Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Job Applications</h1>
                    <p class="mt-1 text-sm text-slate-500">Review and manage applications for your job opportunities</p>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="overflow-x-auto">
                <table id="applicationsTable" class="data-table stripe hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Opportunity</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Applied At</th>
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
        
        // Initialize Applications DataTable
        const applicationsTable = $('#applicationsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search applications...",
                "lengthMenu": "Show _MENU_ applications per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ applications",
                "infoEmpty": "No applications found",
                "infoFiltered": "(filtered from _MAX_ total applications)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "ajax": {
                "url": "<?= base_url('auth/opportunities/applications/get') ?>",
                "type": "POST",
                "error": function(xhr, error, code) {
                    console.error('DataTable Ajax Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            },
            "columns": [
                { 
                    data: 'applicant_name', 
                    name: 'applicant_name',
                    render: function(data, type, row) {
                        return `<div class="font-medium text-secondary flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span class="truncate text-sm">${data}</span>
                        </div>`;
                    }
                },
                { 
                    data: 'job_title', 
                    name: 'job_title',
                    render: function(data, type, row) {
                        return `<div class="text-sm text-gray-700">${data}</div>`;
                    }
                },
                { 
                    data: 'email', 
                    name: 'email',
                    render: function(data, type, row) {
                        return `<a href="mailto:${data}" class="text-blue-600 hover:text-blue-900">${data}</a>`;
                    }
                },
                { 
                    data: 'phone', 
                    name: 'phone',
                    render: function(data, type, row) {
                        return `<span class="text-sm text-gray-700">${data || '<span class="text-gray-400">N/A</span>'}</span>`;
                    }
                },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data, type, row) {
                        const statusMap = {
                            'pending': '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Pending</span>',
                            'reviewed': '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Reviewed</span>',
                            'interviewed': '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Interviewed</span>',
                            'rejected': '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Rejected</span>',
                            'hired': '<span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Hired</span>'
                        };
                        return statusMap[data] || data;
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
                                <button class="text-blue-600 hover:text-blue-900 p-1" onclick="viewApplication('${data}')" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 p-1" onclick="reviewApplication('${data}')" title="Review">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </button>
                                <button class="text-purple-600 hover:text-purple-900 p-1" onclick="updateStatus('${data}')" title="Update Status">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900 p-1" onclick="deleteApplication('${data}')" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "order": [[5, 'desc']], // Order by created_at descending
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Reinitialize Lucide icons after DataTable draw
        applicationsTable.on('draw', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        // Action functions
        window.viewApplication = function(id) {
            // Implement view functionality
            window.location.href = `<?= site_url('auth/opportunities/applications/view') ?>/${id}`;
        };
        
        window.reviewApplication = function(id) {
            // Implement review functionality
            window.location.href = `<?= site_url('auth/opportunities/applications/review') ?>/${id}`;
        };
        
        window.updateStatus = function(id) {
            // Implement status update functionality - could be a modal
            // For now, redirect to edit page
            window.location.href = `<?= site_url('auth/opportunities/applications/edit') ?>/${id}`;
        };
        
        window.deleteApplication = function(id) {
            if (confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
                // Implement delete functionality
                fetch(`<?= site_url('auth/opportunities/applications/delete') ?>/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        applicationsTable.ajax.reload();
                        if (typeof showNotification === 'function') {
                            showNotification('success', 'Application deleted successfully');
                        }
                    } else {
                        if (typeof showNotification === 'function') {
                            showNotification('error', data.message || 'Failed to delete application');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showNotification === 'function') {
                        showNotification('error', 'An error occurred while deleting the application');
                    }
                });
            }
        };
    });
</script>
<?= $this->endSection() ?>