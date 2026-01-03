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
            'pageTitle' => 'Pillar Management',
            'pageDescription' => 'Manage organizational pillars and strategic focus areas',
            'breadcrumbs' => [
                ['label' => 'Pillars']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/pillars/partials/header_section') ?>

        <!-- Quick Stats Cards -->
        <?= $this->include('backendV2/pages/pillars/partials/quick_stats_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/pillars/partials/navigation_section') ?>

        <!-- Pillars Management Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Organizational Pillars</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage your organization's core pillars and strategic focus areas</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="showCreatePillarModal()" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Pillar</span>
                    </button>
                </div>
            </div>

            <!-- Pillars Table -->
            <div class="overflow-x-auto">
                <table id="pillarsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Pillar</th>
                            <th scope="col">Description</th>
                            <th scope="col">Status</th>
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
    // Initialize Pillars DataTable
    let pillarsTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Pillars DataTable
        pillarsTable = $('#pillarsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/pillars/get-pillars') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "title",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-secondary to-secondaryShades-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    <i data-lucide="columns" class="w-6 h-6"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">Organizational Pillar</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "description",
                    "render": function(data) {
                        if (!data) return '<div class="text-sm text-gray-600">No description</div>';
                        const maxLength = 80;
                        return data.length > maxLength 
                            ? `<div class="text-sm text-gray-600" title="${data}">${data.substring(0, maxLength)}...</div>`
                            : `<div class="text-sm text-gray-600">${data}</div>`;
                    }
                },
                {
                    "data": "is_active",
                    "className": "text-center",
                    "render": function(data) {
                        return data === '1' || data === 1 
                            ? '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>'
                            : '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactive</span>';
                    }
                },
                {
                    "data": "articles_count",
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
                        const statusAction = row.is_active === '1' || row.is_active === 1
                            ? `<button onClick="deactivatePillar('${row.id}')" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 hover:bg-yellow-200 text-yellow-600 hover:text-yellow-700 rounded-md transition-colors duration-200"
                                       title="Deactivate">
                                  <i data-lucide="pause" class="w-4 h-4"></i>
                               </button>`
                            : `<button onClick="activatePillar('${row.id}')" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                       title="Activate">
                                  <i data-lucide="play" class="w-4 h-4"></i>
                               </button>`;

                        return `
                            <div class="flex justify-start space-x-1">
                                <button onClick="editPillar('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                        title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                ${statusAction}
                                <button onClick="deletePillar('${row.id}')" 
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
                "searchPlaceholder": "Search pillars...",
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

    // Action functions
    function showCreatePillarModal() {
        window.location.href = '<?= site_url('auth/pillars/create') ?>';
    }

    function viewPillar(id) {
        console.log('View pillar:', id);
    }

    function editPillar(id) {
        console.log('Edit pillar:', id);
    }

    function activatePillar(id) {
        if (confirm('Are you sure you want to activate this pillar?')) {
            updatePillarStatus(id, 1);
        }
    }

    function deactivatePillar(id) {
        if (confirm('Are you sure you want to deactivate this pillar?')) {
            updatePillarStatus(id, 0);
        }
    }

    function updatePillarStatus(id, status) {
        $.ajax({
            url: `<?= site_url('auth/pillars/update-status') ?>/${id}`,
            type: 'POST',
            data: { status: status },
            success: function(response) {
                pillarsTable.ajax.reload();
                showToast('Pillar status updated successfully', 'success');
            },
            error: function(xhr) {
                showToast('Error updating pillar status', 'error');
            }
        });
    }

    function deletePillar(id) {
        if (confirm('Are you sure you want to delete this pillar? This action cannot be undone.')) {
            $.ajax({
                url: `<?= site_url('auth/pillars/delete') ?>/${id}`,
                type: 'DELETE',
                success: function(response) {
                    pillarsTable.ajax.reload();
                    showToast('Pillar deleted successfully', 'success');
                },
                error: function(xhr) {
                    showToast('Error deleting pillar', 'error');
                }
            });
        }
    }

    function showToast(message, type) {
        console.log(type + ': ' + message);
    }
</script>
<?= $this->endSection() ?>