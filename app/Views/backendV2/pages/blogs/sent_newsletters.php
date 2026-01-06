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
                        <p class="text-primaryShades-300">Total Newsletters</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['total_newsletters'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="file-text" class="w-4 h-4 mr-1"></i> All newsletters
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Sent Newsletters</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['sent_newsletters'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Successfully delivered
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Avg. Open Rate</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['avg_open_rate'] ?? 0, 1) ?>%</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> Email engagement
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Avg. Click Rate</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($statistics['avg_click_rate'] ?? 0, 1) ?>%</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="mouse-pointer" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="mouse-pointer-click" class="w-4 h-4 mr-1"></i> Link interactions
                </p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/blogs/partials/navigation_section') ?>
        
        <!-- Sent Newsletters Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Sent Newsletters</h1>
                    <p class="mt-1 text-sm text-slate-500">View all sent newsletter campaigns and their performance</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?= site_url('auth/blogs/newsletters') ?>" class="flex items-center px-6 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all duration-300">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        <span>Back to Subscribers</span>
                    </a>
                    <a href="<?= site_url('auth/blogs/newsletters/create') ?>" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="mail" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Create Newsletter</span>
                    </a>
                </div>
            </div>

            <!-- Sent Newsletters Table -->
            <div class="overflow-x-auto">
                <table id="sentNewslettersTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Subject</th>
                            <th scope="col">Status</th>
                            <th scope="col">Recipients</th>
                            <th scope="col">Sent Count</th>
                            <th scope="col">Open Rate</th>
                            <th scope="col">Click Rate</th>
                            <th scope="col">Sent Date</th>
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
    // Initialize Sent Newsletters DataTable
    let sentNewslettersTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Sent Newsletters DataTable
        sentNewslettersTable = $('#sentNewslettersTable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[6, 'desc']], // Sort by sent date descending
            "ajax": {
                "url": "<?= base_url('auth/blogs/newsletters/data') ?>",
                "type": "POST",
                "data": function(d) {
                    // Filter only sent newsletters
                    d.status = 'sent';
                }
            },
            "columns": [
                {
                    "data": "subject",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary via-secondary to-primary rounded-lg flex items-center justify-center text-white font-bold">
                                    <i data-lucide="mail" class="w-6 h-6"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data || 'Untitled'}</div>
                                    <div class="text-sm text-gray-500">Newsletter Campaign</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "status",
                    "className": "text-left",
                    "render": function(data) {
                        const statusMap = {
                            'draft': '<span class="badge bg-secondary">Draft</span>',
                            'scheduled': '<span class="badge bg-warning">Scheduled</span>',
                            'sending': '<span class="badge bg-info">Sending</span>',
                            'sent': '<span class="badge bg-success">Sent</span>',
                            'failed': '<span class="badge bg-danger">Failed</span>'
                        };
                        return `<div class="flex items-center justify-start">${statusMap[data] || statusMap['draft']}</div>`;
                    }
                },
                {
                    "data": "recipient_count",
                    "className": "text-left",
                    "render": function(data) {
                        return `
                            <div class="flex items-center justify-start">
                                <i data-lucide="users" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm font-medium">${data || 0}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "sent_count",
                    "className": "text-left",
                    "render": function(data, type, row) {
                        const failedCount = row.failed_count || 0;
                        const successRate = row.recipient_count > 0 ? ((data / row.recipient_count) * 100).toFixed(1) : 0;
                        return `
                            <div class="flex flex-col justify-start">
                                <span class="text-sm font-medium text-green-600">${data || 0} sent</span>
                                ${failedCount > 0 ? `<span class="text-xs text-red-600">${failedCount} failed</span>` : ''}
                                <span class="text-xs text-gray-500">${successRate}% success</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "open_rate",
                    "className": "text-left",
                    "render": function(data) {
                        const rate = parseFloat(data) || 0;
                        const colorClass = rate >= 20 ? 'text-green-600' : rate >= 10 ? 'text-yellow-600' : 'text-gray-600';
                        return `
                            <div class="flex items-center justify-start">
                                <i data-lucide="eye" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm font-medium ${colorClass}">${rate.toFixed(1)}%</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "click_rate",
                    "className": "text-left",
                    "render": function(data) {
                        const rate = parseFloat(data) || 0;
                        const colorClass = rate >= 5 ? 'text-green-600' : rate >= 2 ? 'text-yellow-600' : 'text-gray-600';
                        return `
                            <div class="flex items-center justify-start">
                                <i data-lucide="mouse-pointer" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm font-medium ${colorClass}">${rate.toFixed(1)}%</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "sent_at",
                    "className": "text-left",
                    "render": function(data) {
                        return `
                            <div class="flex items-center justify-start">
                                <i data-lucide="calendar" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm">${data || 'Not sent'}</span>
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
                                <button onClick="viewNewsletter('${data}')"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-md transition-colors duration-200"
                                        title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button onClick="deleteNewsletter('${data}')"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 rounded-md transition-colors duration-200"
                                        title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });

    // View newsletter details
    function viewNewsletter(id) {
        // Open in new tab or show modal with details
        window.open(`<?= site_url('auth/blogs/newsletters') ?>/${id}/view`, '_blank');
    }

    // Delete newsletter
    function deleteNewsletter(id) {
        Swal.fire({
            title: 'Delete Newsletter?',
            text: 'Are you sure you want to delete this newsletter? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= site_url('auth/blogs/newsletters') ?>/${id}`,
                    method: 'DELETE',
                    data: {
                        [<?= json_encode(csrf_token()) ?>]: $('input[name="<?= csrf_token() ?>"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success || response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: 'Newsletter deleted successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            sentNewslettersTable.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete newsletter'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response?.message || 'An error occurred while deleting the newsletter'
                        });
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
