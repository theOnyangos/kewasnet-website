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
            'pageTitle' => 'Blog Management',
            'pageDescription' => 'Manage blog posts, categories, and tags',
            'breadcrumbs' => [
                ['label' => 'Blogs']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/blogs/partials/header_section') ?>

        <!-- Newsletter Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Total Subscriptions</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $statistics['total_newsletters'] ?? 23 ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="users" class="w-4 h-4 mr-1"></i> All subscribers
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Active Subscribers</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['total_subscribers'] ?? 1250) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +8% from last month
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Recent Subscriptions</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $statistics['recent_subscriptions'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Last 30 days
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Inactive Subscribers</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $statistics['inactive_subscribers'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="user-x" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i> Unsubscribed users
                </p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/blogs/partials/navigation_section') ?>
        
        <!-- Newsletter Management Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Newsletter Subscribers</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage newsletter subscriptions and email campaigns</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?= site_url('auth/blogs/newsletters/sent') ?>" class="flex items-center px-6 py-2 rounded-full bg-purple-100 text-purple-700 hover:bg-purple-200 transition-all duration-300">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                        <span>View Sent</span>
                    </a>
                    <a href="<?= site_url('auth/blogs/newsletters/create') ?>" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="mail" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Create Newsletter</span>
                    </a>
                </div>
            </div>

            <!-- Newsletter Subscriptions Table -->
            <div class="overflow-x-auto">
                <table id="newslettersTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Email Address</th>
                            <th scope="col">Status</th>
                            <th scope="col">Subscribed Date</th>
                            <th scope="col">Last Updated</th>
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
    // Initialize Newsletters DataTable
    let newslettersTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Newsletters DataTable
        newslettersTable = $('#newslettersTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/blogs/newsletters/get') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "email",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary via-secondary to-primary rounded-lg flex items-center justify-center text-white font-bold">
                                    <i data-lucide="mail" class="w-6 h-6"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">Newsletter Subscriber</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "is_active",
                    "className": "text-left",
                    "render": function(data) {
                        const isActive = data == 1 || data === true || data === 'active';
                        return `
                            <div class="flex items-center justify-start">
                                ${isActive ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}
                            </div>
                        `;
                    }
                },
                {
                    "data": "created_at",
                    "className": "text-left",
                    "render": function(data) {
                        return `
                            <div class="flex items-center justify-start">
                                <i data-lucide="calendar" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm">${data ?? 'N/A'}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "updated_at",
                    "className": "text-left",
                    "render": function(data) {
                        return `
                            <div class="flex items-center justify-start">
                                <i data-lucide="clock" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm">${data}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "px-6 py-4 whitespace-nowrap text-right text-sm font-medium",
                    "render": function(data, type, row) {
                        const isActive = row.is_active == 1 || row.is_active === true || row.is_active === 'active';
                        return `
                            <div class="flex justify-start space-x-1">
                                ${isActive ?
                                    `<button onClick="unsubscribeUser('${row.id}')"
                                             class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 hover:bg-yellow-200 text-yellow-600 hover:text-yellow-700 rounded-md transition-colors duration-200"
                                             title="Unsubscribe">
                                        <i data-lucide="user-minus" class="w-4 h-4"></i>
                                     </button>` :
                                    `<button onClick="reactivateSubscription('${row.id}')"
                                             class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                             title="Reactivate">
                                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                                     </button>`
                                }
                                <button onClick="deleteSubscriber('${row.id}')"
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
                "searchPlaceholder": "Search subscribers...",
            },
            "order": [[2, 'desc']], // Order by subscribed_date descending
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50');
            },
            "drawCallback": function() {
                lucide.createIcons();
            },
            "initComplete": function() {
                lucide.createIcons();
            }
        });
    });

    // Action functions
    function showCreateNewsletterModal() {
        // Implement subscriber addition modal
        window.open('<?= site_url('auth/blogs/newsletters/add-subscriber') ?>', '_blank');
    }

    function unsubscribeUser(id) {
        Swal.fire({
            title: 'Unsubscribe User?',
            text: "This user will be marked as inactive and will no longer receive newsletters.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, unsubscribe',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= site_url('auth/blogs/newsletters/unsubscribe') ?>/${id}`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            newslettersTable.ajax.reload(null, false);
                            Swal.fire('Unsubscribed!', response.message || 'User has been unsubscribed successfully.', 'success');
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to unsubscribe user.', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Failed to unsubscribe user. Please try again.', 'error');
                    }
                });
            }
        });
    }

    function reactivateSubscription(id) {
        Swal.fire({
            title: 'Reactivate Subscription?',
            text: "This user will be marked as active and will receive newsletters again.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, reactivate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= site_url('auth/blogs/newsletters/reactivate') ?>/${id}`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            newslettersTable.ajax.reload(null, false);
                            Swal.fire('Reactivated!', response.message || 'Subscription has been reactivated successfully.', 'success');
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to reactivate subscription.', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Failed to reactivate subscription. Please try again.', 'error');
                    }
                });
            }
        });
    }

    function deleteSubscriber(id) {
        Swal.fire({
            title: 'Delete Subscriber?',
            text: "This action cannot be undone! The subscriber will be permanently deleted.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete permanently',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= site_url('auth/blogs/newsletters/delete') ?>/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            newslettersTable.ajax.reload(null, false);
                            Swal.fire('Deleted!', response.message || 'Subscriber has been permanently deleted.', 'success');
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to delete subscriber.', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Failed to delete subscriber. Please try again.', 'error');
                    }
                });
            }
        });
    }
        
</script>
<?= $this->endSection() ?>