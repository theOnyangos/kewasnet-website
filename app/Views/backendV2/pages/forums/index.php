<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    // dd($forumStats);
    
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
            'pageTitle' => 'Forum Management',
            'pageDescription' => 'Manage discussion forums, topics, and community engagement',
            'breadcrumbs' => [
                ['label' => 'Forums']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/forums/partials/header_section') ?>

        <!-- Quick Stats Cards -->
        <?= $this->include('backendV2/pages/forums/partials/quick_stats_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/forums/partials/navigation_section') ?>
        
        <!-- Forums Table -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Recent Forums</h1>
                    <p class="mt-1 text-sm text-slate-500">Create and manage discussions. View all discussions and their replies.</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="window.open('<?= site_url('auth/forums/create-forum') ?>', '_blank')" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Create a Forum</span>
                    </button>
                </div>
            </div>

            <!-- Forums Table -->
            <div class="overflow-x-auto">
                <table id="forumsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Forum</th>
                            <th scope="col">Description</th>
                            <th scope="col">Discussions</th>
                            <th scope="col">Replies</th>
                            <th scope="col">Members</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class=""></tbody>
                </table>
            </div>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize DataTables (if needed)
    let forumsTable;
    const forumsTableURL = "<?= base_url('auth/forums/get') ?>";

    $(document).ready(function() {
        $('#forumsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/forums/get') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "name",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 ${row.color ? '' : 'bg-gradient-to-br from-blue-500 to-blue-600'} rounded-lg flex items-center justify-center text-white font-bold" 
                                     style="${row.color ? 'background-color:'+row.color : ''}">
                                    ${row.icon ? `<i data-lucide="${row.icon}"></i>` : row.name.substring(0, 2).toUpperCase()}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">${row.is_public ? 'Public Forum' : 'Private Forum'}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "description",
                    "render": function(data) {
                        const maxLength = 60;
                        if (!data) return '<div class="text-sm text-gray-600 max-w-xs">No description</div>';
                        
                        if (data.length <= maxLength) {
                            return `<div class="text-sm text-gray-600 max-w-xs">${data}</div>`;
                        }
                        
                        // Find the last space before maxLength
                        let truncated = data.substr(0, maxLength);
                        truncated = truncated.substr(0, Math.min(
                            truncated.length,
                            truncated.lastIndexOf(" "))
                        );
                        
                        return `<div class="text-sm text-gray-600 max-w-xs" title="${data}">
                            ${truncated}...
                            </div>`;
                    }
                },
                {
                    "data": "total_discussions",
                    "className": "px-6 py-4 whitespace-nowrap text-start text-sm font-medium text-gray-900"
                },
                {
                    "data": "reply_count",
                    "className": "px-6 py-4 whitespace-nowrap text-start text-sm font-medium text-gray-900"
                },
                {
                    "data": "members",
                    "className": "px-6 py-4 whitespace-nowrap text-start text-sm font-medium text-gray-900",
                    "render": function(data) {
                        return data || '0';
                    }
                },
                {
                    "data": "is_active",
                    "className": "px-6 py-4 whitespace-nowrap text-start",
                    "render": function(data) {
                        return `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                <div class="w-2 h-2 ${data ? 'bg-green-500' : 'bg-red-500'} rounded-full mr-1"></div>
                                ${data ? 'Active' : 'Inactive'}
                            </span>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "px-6 py-4 whitespace-nowrap text-right text-sm font-medium",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex justify-start space-x-2">
                                <button onClick="window.location.href='<?= site_url('auth/forums/') ?>${row.id}'" 
                                        class="bg-primaryShades-100 rounded-md p-2 px-5 text-blue-600 hover:text-blue-900 mr-3">
                                    View
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search forums...",
            },
            "order": [[0, 'asc']],
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50');
            },
            "initComplete": function() {
                // Initialize all Lucide icons after table is loaded
                lucide.createIcons();
            },
        });

        function confirmDeleteForum(forumId) {
            if (confirm('Are you sure you want to delete this forum?')) {
                $.ajax({
                    url: '<?= site_url('auth/forums/delete') ?>/' + forumId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#forumsTable').DataTable().ajax.reload();
                        showToast('Forum deleted successfully', 'success');
                    },
                    error: function(xhr) {
                        showToast('Error deleting forum', 'error');
                    }
                });
            }
        }

        function showToast(message, type) {
            // Implement your toast notification here
            console.log(`${type}: ${message}`);
        }
    });
</script>
<?= $this->endSection() ?>