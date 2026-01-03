<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>Deleted Blog Posts<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Deleted Blog Posts',
            'pageDescription' => 'View and restore deleted blog posts',
            'breadcrumbs' => [
                ['label' => 'Blogs', 'url' => 'auth/blogs'],
                ['label' => 'Deleted Posts']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/blogs/partials/header_section') ?>

        <!-- Quick Stats Cards -->
        <?= $this->include('backendV2/pages/blogs/partials/quick_stats_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/blogs/partials/navigation_section', ['activeTab' => 'deleted']) ?>
        
        <!-- Deleted Posts Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Deleted Blog Posts</h1>
                    <p class="mt-1 text-sm text-slate-500">View and restore deleted blog posts</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="deleted-posts-table" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Category</th>
                            <th scope="col">Author</th>
                            <th scope="col">Status</th>
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
    const table = $('#deleted-posts-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('auth/blogs/deleted/get') ?>",
            "type": "POST"
        },
        "columns": [
            {
                "data": "title",
                "render": function(data, type, row) {
                    return `
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center text-white font-bold">
                                <i data-lucide="trash-2" class="w-6 h-6"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${data}</div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                "data": "category_name",
                "render": function(data) {
                    return data ? `<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">${data}</span>` : '<span class="text-gray-400">Uncategorized</span>';
                }
            },
            {
                "data": "author_name",
                "render": function(data) {
                    return `<span class="text-sm text-gray-900">${data || 'Unknown'}</span>`;
                }
            },
            {
                "data": "status",
                "className": "text-center",
                "render": function(data) {
                    const statusColors = {
                        'published': 'bg-green-100 text-green-800',
                        'draft': 'bg-yellow-100 text-yellow-800',
                        'pending': 'bg-orange-100 text-orange-800'
                    };
                    const colorClass = statusColors[data] || 'bg-gray-100 text-gray-800';
                    return `<span class="px-2 py-1 ${colorClass} text-xs rounded-full">${data}</span>`;
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
                            <button onclick="restorePost('${data}', '${row.title.replace(/'/g, "\\'")}')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i>
                                Restore
                            </button>
                            <button onclick="permanentDeletePost('${data}', '${row.title.replace(/'/g, "\\'")}')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                Delete
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "order": [[4, 'desc']],
        "pageLength": 10,
        "drawCallback": function() {
            lucide.createIcons();
        }
    });

    // Restore post function
    window.restorePost = function(postId, postTitle) {
        Swal.fire({
            title: 'Restore Post?',
            text: `Are you sure you want to restore "${postTitle}"?`,
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
                    url: '<?= base_url('auth/blogs/restore/') ?>' + postId,
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
                                text: response.message || 'Blog post has been restored successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                table.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to restore blog post.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while restoring the post.'
                        });
                    }
                });
            }
        });
    };

    // Permanent delete post function
    window.permanentDeletePost = function(postId, postTitle) {
        Swal.fire({
            title: 'Permanently Delete?',
            html: `<p>Are you sure you want to <strong>permanently delete</strong> "${postTitle}"?</p><p class="text-red-600 mt-2">This action cannot be undone!</p>`,
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
                    url: '<?= base_url('auth/blogs/permanent-delete/') ?>' + postId,
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
                                text: response.message || 'Blog post has been permanently deleted.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                table.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete blog post.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the post.'
                        });
                    }
                });
            }
        });
    };
});
</script>
<?= $this->endSection() ?>
