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
        
        <!-- Overview Tab Content - Blog Categories -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Blog Categories</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage blog categories and view detailed statistics</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="showCreateCategoryModal()" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="folder-plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Category</span>
                    </button>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="overflow-x-auto">
                <table id="categoriesTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Category</th>
                            <th scope="col">Description</th>
                            <th scope="col">Posts</th>
                            <th scope="col">Status</th>
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

    <!-- Create Category Modal -->
    <div id="createCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 transform transition-all">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Create New Category</h3>
                <button type="button" onclick="closeCreateCategoryModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form id="createCategoryForm" method="POST" action="<?= site_url('auth/blogs/create-category') ?>">
                <?= csrf_field() ?>
                
                <div class="p-6 space-y-4">
                    <!-- Name Field -->
                    <div>
                        <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="category_name" name="name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                               placeholder="Enter category name" required>
                        <p class="mt-1 text-xs text-gray-500">This will be the display name of your category</p>
                    </div>

                    <!-- Slug Field -->
                    <div>
                        <label for="category_slug" class="block text-sm font-medium text-gray-700 mb-1">
                            URL Slug <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                /category/
                            </span>
                            <input type="text" id="category_slug" name="slug" 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                                   placeholder="url-friendly-slug" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-generated from name, or enter custom slug</p>
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="category_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea id="category_description" name="description" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" 
                                  placeholder="Brief description of the category"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Optional description for the category</p>
                    </div>

                    <!-- Status Field -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked 
                                   class="h-4 w-4 text-secondary focus:ring-secondary border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Active (visible on website)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 px-6 py-4 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="closeCreateCategoryModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="gradient-btn px-6 py-2 text-white rounded-lg hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 inline"></i>
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 transform transition-all">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Edit Category</h3>
                <button type="button" onclick="closeEditCategoryModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form id="editCategoryForm" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_category_id">
                
                <div class="p-6 space-y-5">
                    <div>
                        <label for="edit_category_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit_category_name" name="name" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                               placeholder="Enter category name">
                    </div>

                    <div>
                        <label for="edit_category_slug" class="block text-sm font-semibold text-gray-700 mb-2">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit_category_slug" name="slug" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                               placeholder="category-slug">
                        <p class="mt-1.5 text-xs text-gray-500">URL-friendly version (auto-generated)</p>
                    </div>

                    <div>
                        <label for="edit_category_description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="edit_category_description" name="description" rows="4" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none"
                                  placeholder="Brief description of this category"></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="edit_is_active" name="is_active" value="1" 
                               class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <label for="edit_is_active" class="ml-2.5 text-sm font-medium text-gray-700">
                            Active (visible on website)
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                    <button type="button" onclick="closeEditCategoryModal()" 
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition-colors flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Update Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Categories DataTable
    let categoriesTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Auto-generate slug from category name
        $('#category_name').on('input', function() {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            $('#category_slug').val(slug);
        });

        // Handle create category form submission
        $('#createCategoryForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            // Disable submit button
            submitBtn.prop('disabled', true).html('<i class="animate-spin w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full inline-block"></i>Creating...');
            
            // Clear previous errors
            $('.text-red-500').remove();
            $('.border-red-500').removeClass('border-red-500');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', response.message || 'Category created successfully!');
                        closeCreateCategoryModal();
                        categoriesTable.ajax.reload();
                    } else {
                        showNotification('error', response.message || 'Failed to create category');
                    }
                },
                error: function(xhr) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.errors) {
                            // Display field-specific errors
                            $.each(response.errors, function(field, message) {
                                const $field = $('[name="' + field + '"]');
                                $field.addClass('border-red-500');
                                $field.after('<p class="text-red-500 text-xs mt-1">' + message + '</p>');
                            });
                        }
                        
                        showNotification('error', response.message || 'An error occurred');
                    } catch (e) {
                        showNotification('error', 'A network error occurred');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                    lucide.createIcons();
                }
            });
        });

        // Close modal when clicking outside
        $('#createCategoryModal').on('click', function(e) {
            if (e.target.id === 'createCategoryModal') {
                closeCreateCategoryModal();
            }
        });

        // Close modal on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#createCategoryModal').is(':visible')) {
                closeCreateCategoryModal();
            }
        });

        // Initialize Categories DataTable
        categoriesTable = $('#categoriesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/blogs/get-categories') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "name",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary via-secondary to-primary rounded-lg flex items-center justify-center text-white font-bold">
                                    ${data.substring(0, 2).toUpperCase()}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">Blog Category</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "description",
                    "render": function(data) {
                        if (!data) return '<div class="text-sm text-gray-600">No description</div>';
                        const maxLength = 60;
                        if (data.length <= maxLength) {
                            return `<div class="text-sm text-gray-600">${data}</div>`;
                        }
                        let truncated = data.substr(0, maxLength);
                        truncated = truncated.substr(0, Math.min(truncated.length, truncated.lastIndexOf(" ")));
                        return `<div class="text-sm text-gray-600" title="${data}">${truncated}...</div>`;
                    }
                },
                {
                    "data": "post_count",
                    "className": "text-left",
                    "render": function(data) {
                        return `<span class="text-sm font-medium">${data || 0}</span>`;
                    }
                },
                {
                    "data": "is_active",
                    "className": "text-left",
                    "render": function(data) {
                        return `
                            <span class="inline-flex items-center justify-start px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                Active
                            </span>
                        `;
                    }
                },
                {
                    "data": "created_at",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-500">${new Date(data).toLocaleDateString()}</span>`;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex justify-center space-x-2">
                                <button onclick="editCategory('${data}')" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteCategory('${data}')" class="text-red-600 hover:text-red-900" title="Delete">
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
                "searchPlaceholder": "Search categories...",
            },
            "order": [[0, 'asc']],
            "initComplete": function() {
                lucide.createIcons();
            }
        });
    });

    // Action functions
    function showCreateCategoryModal() {
        $('#createCategoryModal').fadeIn(200);
        setTimeout(() => {
            lucide.createIcons();
            $('#category_name').focus();
        }, 100);
    }

    function closeCreateCategoryModal() {
        $('#createCategoryModal').fadeOut(200);
        $('#createCategoryForm')[0].reset();
        // Clear any error messages
        $('.text-red-500').remove();
        $('.border-red-500').removeClass('border-red-500');
    }

    function showEditCategoryModal() {
        $('#editCategoryModal').fadeIn(200);
        setTimeout(() => {
            lucide.createIcons();
        }, 100);
    }

    function closeEditCategoryModal() {
        $('#editCategoryModal').fadeOut(200);
        $('#editCategoryForm')[0].reset();
        $('.text-red-500').remove();
        $('.border-red-500').removeClass('border-red-500');
    }

    function editCategory(id) {
        $.ajax({
            url: '<?= base_url('auth/blogs/get-category') ?>/' + id,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    const category = response.data;
                    $('#edit_category_id').val(category.id);
                    $('#edit_category_name').val(category.name);
                    $('#edit_category_slug').val(category.slug);
                    $('#edit_category_description').val(category.description || '');
                    $('#edit_is_active').prop('checked', category.is_active == 1);
                    showEditCategoryModal();
                } else {
                    showNotification('error', 'Failed to load category data');
                }
            },
            error: function() {
                showNotification('error', 'Failed to load category data');
            }
        });
    }

    function deleteCategory(id) {
        if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
            $.ajax({
                url: '<?= base_url('auth/blogs/delete-category') ?>/' + id,
                method: 'DELETE',
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', response.message || 'Category deleted successfully!');
                        categoriesTable.ajax.reload();
                    } else {
                        showNotification('error', response.message || 'Failed to delete category');
                    }
                },
                error: function(xhr) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        showNotification('error', response.message || 'Failed to delete category');
                    } catch (e) {
                        showNotification('error', 'A network error occurred');
                    }
                }
            });
        }
    }

    // Handle edit category form submission
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Updating...');
        lucide.createIcons();
        
        $('.text-red-500').remove();
        $('.border-red-500').removeClass('border-red-500');
        
        const categoryId = $('#edit_category_id').val();
        
        $.ajax({
            url: '<?= base_url('auth/blogs/update-category') ?>/' + categoryId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    showNotification('success', response.message || 'Category updated successfully!');
                    closeEditCategoryModal();
                    categoriesTable.ajax.reload();
                } else {
                    showNotification('error', response.message || 'Failed to update category');
                }
            },
            error: function(xhr) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.errors) {
                        $.each(response.errors, function(field, message) {
                            const $field = $('#edit_category_' + field);
                            $field.addClass('border-red-500');
                            $field.after('<p class="text-red-500 text-xs mt-1">' + message + '</p>');
                        });
                    }
                    
                    showNotification('error', response.message || 'An error occurred');
                } catch (e) {
                    showNotification('error', 'A network error occurred');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
                lucide.createIcons();
            }
        });
    });

    // Close edit modal when clicking outside
    $('#editCategoryModal').on('click', function(e) {
        if (e.target.id === 'editCategoryModal') {
            closeEditCategoryModal();
        }
    });

    // Auto-generate slug for edit form
    $('#edit_category_name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#edit_category_slug').val(slug);
    });
</script>
<?= $this->endSection() ?>