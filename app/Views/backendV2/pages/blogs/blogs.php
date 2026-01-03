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

        <!-- Quick Stats Cards -->
        <?= $this->include('backendV2/pages/blogs/partials/quick_stats_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/blogs/partials/navigation_section') ?>
        
        <!-- Blog Posts Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">All Blog Posts</h1>
                    <p class="mt-1 text-sm text-slate-500">Create and manage all your blog posts</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="window.open('<?= site_url('auth/blogs/create') ?>', '_blank')" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Create Blog Post</span>
                    </button>
                </div>
            </div>

            <!-- Blogs Table -->
            <div class="overflow-x-auto">
                <table id="blogsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Category</th>
                            <th scope="col">Author</th>
                            <th scope="col">Status</th>
                            <th scope="col">Views</th>
                            <th scope="col">Published</th>
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
    // Initialize Blogs DataTable
    let blogsTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Blogs DataTable
        blogsTable = $('#blogsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/blogs/get') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "title",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    <i data-lucide="file-text" class="w-6 h-6"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">${row.slug}</div>
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
                    "render": function(data, type, row) {
                        const isPublished = data === 'published';
                        const isArchived = data === 'archived';

                        if (isArchived) {
                            return `<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Archived</span>`;
                        }

                        return `
                            <div class="flex items-center justify-center space-x-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer publish-toggle"
                                           data-blog-id="${row.id}"
                                           ${isPublished ? 'checked' : ''}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                </label>
                                <span class="text-xs font-medium ${isPublished ? 'text-green-600' : 'text-gray-600'}">
                                    ${isPublished ? 'Published' : 'Draft'}
                                </span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "views",
                    "className": "text-center",
                    "render": function(data) {
                        return `
                            <div class="flex items-center justify-center">
                                <i data-lucide="eye" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm font-medium">${data || 0}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "published_at",
                    "render": function(data, type, row) {
                        if (!data || row.status !== 'published') {
                            return `
                                <div class="flex flex-col items-start">
                                    <span class="text-xs text-gray-400">Not published</span>
                                    <span class="text-xs text-gray-400">-</span>
                                </div>
                            `;
                        }

                        const date = new Date(data);
                        const formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                        const formattedTime = date.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        return `
                            <div class="flex flex-col items-start">
                                <span class="text-sm font-medium text-gray-900">${formattedDate}</span>
                                <span class="text-xs text-gray-500">${formattedTime}</span>
                            </div>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        const isPublished = row.status === 'published';
                        const viewButton = isPublished
                            ? `<button onclick="viewBlog('${row.slug}')" class="text-green-600 hover:text-green-900" title="View Published Post">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                               </button>`
                            : `<button onclick="previewDraft('${data}')" class="text-gray-400 hover:text-gray-600" title="Preview Draft (Not Published)">
                                <i data-lucide="eye-off" class="w-4 h-4"></i>
                               </button>`;

                        return `
                            <div class="flex justify-center space-x-2">
                                <button onclick="window.location.href='<?= site_url('auth/blogs') ?>/${data}/edit'" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                ${viewButton}
                                <button onclick="duplicateBlog('${data}')" class="text-purple-600 hover:text-purple-900" title="Duplicate">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteBlog('${data}')" class="text-red-600 hover:text-red-900" title="Delete">
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
                "searchPlaceholder": "Search blog posts...",
            },
            "order": [[5, 'desc']],
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50');
            },
            "drawCallback": function() {
                lucide.createIcons();

                // Attach event handler to publish toggles
                $('.publish-toggle').off('change').on('change', function() {
                    const blogId = $(this).data('blog-id');
                    const isPublished = $(this).is(':checked');
                    toggleBlogStatus(blogId, isPublished);
                });
            },
            "initComplete": function() {
                lucide.createIcons();
            }
        });
    });

    // Action functions
    function viewBlog(slug) {
        // Navigate to the published blog post page using the slug
        window.open(`<?= site_url('news-details') ?>/${slug}`, '_blank');
    }

    function previewDraft(id) {
        // Show a notification that this is a draft
        showNotification('info', 'This post is not published yet. Please publish it first to view it on the website.');
    }

    function duplicateBlog(id) {
        Swal.fire({
            title: 'Duplicate Blog Post?',
            text: 'This will create a copy of the blog post as a draft.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Duplicate It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= site_url('auth/blogs/duplicate') ?>/${id}`,
                    type: 'POST',
                    data: {
                        [<?= json_encode(csrf_token()) ?>]: $('input[name="<?= csrf_token() ?>"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success || response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Duplicated!',
                                text: response.message || 'Blog post duplicated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            blogsTable.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to duplicate blog post'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response?.message || 'An error occurred while duplicating blog post'
                        });
                    }
                });
            }
        });
    }

    function deleteBlog(id) {
        Swal.fire({
            title: 'Delete Blog Post?',
            text: 'This action cannot be undone. The blog post will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= site_url('auth/blogs') ?>/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('input[name="<?= csrf_token() ?>"]').val()
                    },
                    data: {
                        [<?= json_encode(csrf_token()) ?>]: $('input[name="<?= csrf_token() ?>"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success || response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message || 'Blog post deleted successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            blogsTable.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete blog post'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response?.message || 'An error occurred while deleting blog post'
                        });
                    }
                });
            }
        });
    }

    function toggleBlogStatus(blogId, isPublished) {
        const newStatus = isPublished ? 'published' : 'draft';

        $.ajax({
            url: `<?= site_url('auth/blogs/toggle-status') ?>/${blogId}`,
            type: 'POST',
            data: {
                status: newStatus,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    showNotification('success', response.message || `Blog post ${isPublished ? 'published' : 'unpublished'} successfully`);
                    blogsTable.ajax.reload(null, false); // Reload without resetting pagination
                } else {
                    showNotification('error', response.message || 'Failed to update blog post status');
                    // Revert the toggle
                    blogsTable.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    showNotification('error', response.message || 'Failed to update blog post status');
                } catch (e) {
                    showNotification('error', 'A network error occurred');
                }
                // Revert the toggle
                blogsTable.ajax.reload(null, false);
            }
        });
    }

    function showToast(message, type) {
        // Implement your toast notification here
        console.log(`${type}: ${message}`);
    }
</script>
<?= $this->endSection() ?>