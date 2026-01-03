<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>Deleted Job Opportunities<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Deleted Job Opportunities',
            'pageDescription' => 'View and restore deleted job opportunities',
            'breadcrumbs' => [
                ['label' => 'Opportunities', 'url' => 'auth/opportunities'],
                ['label' => 'Deleted Opportunities']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Navigation Menu -->
        <?= $this->include('backendV2/pages/opportunities/partials/navigation_menu') ?>
        
        <!-- Deleted Opportunities Content -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Deleted Job Opportunities</h1>
                    <p class="mt-1 text-sm text-slate-500">View and restore deleted job opportunities</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="deleted-opportunities-table" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Company</th>
                            <th scope="col">Location</th>
                            <th scope="col">Type</th>
                            <th scope="col">Status</th>
                            <th scope="col">Applications</th>
                            <th scope="col">Deleted At</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        </div>
    </main>

<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();

    // Initialize DataTable
    const table = $('#deleted-opportunities-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('auth/opportunities/deleted/get') ?>",
            "type": "POST"
        },
        "columns": [
            {
                "data": "title",
                "render": function(data, type, row) {
                    return `
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center text-white font-bold">
                                <i data-lucide="briefcase" class="w-6 h-6"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${data}</div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                "data": "company",
                "render": function(data) {
                    return `<span class="text-sm text-gray-900">${data || 'N/A'}</span>`;
                }
            },
            {
                "data": "location",
                "render": function(data) {
                    return data ? `<span class="text-sm text-gray-600">${data}</span>` : '<span class="text-gray-400">N/A</span>';
                }
            },
            {
                "data": "opportunity_type",
                "className": "text-center",
                "render": function(data) {
                    const typeColors = {
                        'full-time': 'bg-blue-100 text-blue-800',
                        'part-time': 'bg-green-100 text-green-800',
                        'contract': 'bg-yellow-100 text-yellow-800',
                        'internship': 'bg-purple-100 text-purple-800',
                        'volunteer': 'bg-pink-100 text-pink-800'
                    };
                    const colorClass = typeColors[data] || 'bg-gray-100 text-gray-800';
                    const label = data ? data.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') : 'N/A';
                    return `<span class="px-2 py-1 ${colorClass} text-xs rounded-full">${label}</span>`;
                }
            },
            {
                "data": "status",
                "className": "text-center",
                "render": function(data) {
                    const statusColors = {
                        'active': 'bg-green-100 text-green-800',
                        'closed': 'bg-red-100 text-red-800',
                        'draft': 'bg-yellow-100 text-yellow-800'
                    };
                    const colorClass = statusColors[data] || 'bg-gray-100 text-gray-800';
                    const label = data ? data.charAt(0).toUpperCase() + data.slice(1) : 'N/A';
                    return `<span class="px-2 py-1 ${colorClass} text-xs rounded-full">${label}</span>`;
                }
            },
            {
                "data": "application_count",
                "className": "text-center",
                "render": function(data) {
                    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${data || 0}
                    </span>`;
                }
            },
            {
                "data": "deleted_at",
                "className": "text-center",
                "render": function(data) {
                    if (!data) return '-';
                    const date = new Date(data);
                    return `<span class="text-sm text-gray-600">${date.toLocaleDateString()} ${date.toLocaleTimeString()}</span>`;
                }
            },
            {
                "data": "id",
                "orderable": false,
                "className": "text-center",
                "render": function(data, type, row) {
                    return `
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="restoreOpportunity('${data}', '${row.title.replace(/'/g, "\\'")}')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i>
                                Restore
                            </button>
                            <button onclick="permanentDeleteOpportunity('${data}', '${row.title.replace(/'/g, "\\'")}')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                Delete
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "order": [[6, 'desc']],
        "pageLength": 10,
        "drawCallback": function() {
            lucide.createIcons();
        }
    });

    // Restore opportunity function
    window.restoreOpportunity = function(opportunityId, opportunityTitle) {
        Swal.fire({
            title: 'Restore Opportunity?',
            text: `Are you sure you want to restore "${opportunityTitle}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, restore it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Restoring...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('auth/opportunities/restore/') ?>' + opportunityId,
                    type: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Restored!',
                                text: response.message || 'Opportunity has been restored successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                table.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to restore opportunity.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while restoring the opportunity.'
                        });
                    }
                });
            }
        });
    };

    // Permanent delete opportunity function
    window.permanentDeleteOpportunity = function(opportunityId, opportunityTitle) {
        Swal.fire({
            title: 'Permanently Delete?',
            html: `<p>Are you sure you want to <strong>permanently delete</strong> "${opportunityTitle}"?</p><p class="text-red-600 mt-2">This action cannot be undone!</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('auth/opportunities/permanent-delete/') ?>' + opportunityId,
                    type: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message || 'Opportunity has been permanently deleted.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                table.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete opportunity.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the opportunity.'
                        });
                    }
                });
            }
        });
    };
});
</script>
<?= $this->endSection() ?>
