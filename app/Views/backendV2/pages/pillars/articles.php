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
        
        <!-- Articles Management Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Pillar Articles (Resources)</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage articles, resources, and documentation for organizational pillars</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="showCreateArticleModal()" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="file-plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Article</span>
                    </button>
                </div>
            </div>

            <!-- Articles Table -->
            <div class="overflow-x-auto">
                <table id="articlesTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Article</th>
                            <th scope="col">Pillar</th>
                            <th scope="col">Doc. Type</th>
                            <th scope="col">Category</th>
                            <th scope="col">Status</th>
                            <th scope="col">Downloads</th>
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
    // Initialize Articles DataTable
    let articlesTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Articles DataTable
        articlesTable = $('#articlesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/pillars/get-articles') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "title",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-secondary to-secondaryShades-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    <i data-lucide="file-text" class="w-6 h-6"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">Pillar Article</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "pillar_name",
                    "render": function(data) {
                        return `<span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded-full">${data || 'Unassigned'}</span>`;
                    }
                },
                {
                    "data": "document_type_name",
                    "render": function(data, type, row) {
                        const color = row.document_type_color || '#6B7280';
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: ${color}20; color: ${color};">${data || 'N/A'}</span>`;
                    }
                },
                {
                    "data": "category_name",
                    "render": function(data) {
                        return `<span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">${data || 'Uncategorized'}</span>`;
                    }
                },
                {
                    "data": "is_published",
                    "className": "text-center",
                    "render": function(data) {
                        return data === '1' || data === 1 
                            ? '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Published</span>'
                            : '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Draft</span>';
                    }
                },
                {
                    "data": "downloaded_count",
                    "className": "text-center",
                    "render": function(data) {
                        return `
                            <div class="flex items-center justify-center">
                                <i data-lucide="download" class="w-4 h-4 mr-1 text-gray-500"></i>
                                <span class="text-sm">${data || 0}</span>
                            </div>
                        `;
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
                        const statusAction = row.is_published === '1' || row.is_published === 1
                            ? `<button onClick="unpublishArticle('${row.id}')" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 hover:bg-yellow-200 text-yellow-600 hover:text-yellow-700 rounded-md transition-colors duration-200"
                                       title="Unpublish">
                                  <i data-lucide="power-off" class="w-4 h-4"></i>
                               </button>`
                            : `<button onClick="publishArticle('${row.id}')" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                       title="Publish">
                                  <i data-lucide="power" class="w-4 h-4"></i>
                               </button>`;

                        return `
                            <div class="flex justify-start space-x-1">
                                <button onClick="viewArticle('${row.slug}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-md transition-colors duration-200"
                                        title="View Details">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </button>
                                <button onClick="editArticle('${row.id}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-md transition-colors duration-200"
                                        title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onClick="downloadArticle('${row.download_url}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 hover:text-indigo-700 rounded-md transition-colors duration-200"
                                        title="Download">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </button>
                                ${statusAction}
                                <button onClick="deleteArticle('${row.id}')" 
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
                "searchPlaceholder": "Search articles...",
            },
            "order": [[6, 'desc']], // Order by created_at descending
            "createdRow": function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50');
            },
            "initComplete": function() {
                lucide.createIcons();
            }
        });
    });

    // Action functions
    function showCreateArticleModal() {
        window.location.href = '<?= site_url('auth/pillars/create-pillar-article') ?>';
    }

    function viewArticle(slug) {
        window.open(`<?= base_url('ksp/pillar-article') ?>/${slug}`, '_blank');
    }

    function editArticle(id) {
        console.log('Edit article:', id);
    }

    function downloadArticle(url) {
        window.location.href = url;
        
        // Reload table
        articlesTable.ajax.reload(() => {
            lucide.createIcons();
        });
    }

    function publishArticle(id) {
        if (confirm('Are you sure you want to publish this article?')) {
            updateArticleStatus(id, 1);
        }
    }

    function unpublishArticle(id) {
        if (confirm('Are you sure you want to unpublish this article?')) {
            updateArticleStatus(id, 0);
        }
    }

    function updateArticleStatus(id, status) {
        $.ajax({
            url: `<?= site_url('auth/pillars/update-article-status') ?>/${id}`,
            type: 'POST',
            data: { is_published: status },
            success: function(response) {
                articlesTable.ajax.reload();
                showToast('Article status updated successfully', 'success');
            },
            error: function(xhr) {
                showToast('Error updating article status', 'error');
            }
        });
    }

    function deleteArticle(id) {
        if (confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
            $.ajax({
                url: `<?= site_url('auth/pillars/delete-article') ?>/${id}`,
                type: 'DELETE',
                success: function(response) {
                    articlesTable.ajax.reload();
                    showToast('Article deleted successfully', 'success');
                },
                error: function(xhr) {
                    showToast('Error deleting article', 'error');
                }
            });
        }
    }

    function showToast(message, type) {
        console.log(type + ': ' + message);
    }
</script>
<?= $this->endSection() ?>