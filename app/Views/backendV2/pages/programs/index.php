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
            'pageTitle' => 'Programs Management',
            'pageDescription' => 'Manage and organize KEWASNET programs and initiatives',
            'breadcrumbs' => [
                ['label' => 'Programs']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Programs -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Total Programs</p>
                        <p class="text-2xl font-bold text-slate-900" id="totalPrograms">0</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i data-lucide="folder" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Active Programs -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Active Programs</p>
                        <p class="text-2xl font-bold text-emerald-600" id="activePrograms">0</p>
                    </div>
                    <div class="bg-emerald-100 p-3 rounded-lg">
                        <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <!-- Featured Programs -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Featured Programs</p>
                        <p class="text-2xl font-bold text-amber-600" id="featuredPrograms">0</p>
                    </div>
                    <div class="bg-amber-100 p-3 rounded-lg">
                        <i data-lucide="star" class="w-6 h-6 text-amber-600"></i>
                    </div>
                </div>
            </div>

            <!-- Draft Programs -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Draft Programs</p>
                        <p class="text-2xl font-bold text-slate-500" id="draftPrograms">0</p>
                    </div>
                    <div class="bg-slate-100 p-3 rounded-lg">
                        <i data-lucide="edit" class="w-6 h-6 text-slate-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs Table -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">All Programs</h2>
                    <p class="mt-1 text-sm text-slate-500">View and manage all KEWASNET programs</p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-3">
                    <div class="relative">
                        <input type="text" id="programSearch" placeholder="Search programs..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    </div>
                    <select id="statusFilter" class="select2 px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <a href="<?= base_url('auth/programs/create') ?>" class="gradient-btn flex items-center px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add New Program</span>
                    </a>
                </div>
            </div>

            <!-- DataTable -->
            <div class="overflow-x-auto">
                <table id="programsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col">Program</th>
                            <th scope="col">Description</th>
                            <th scope="col">Status</th>
                            <th scope="col">Featured</th>
                            <th scope="col">Order</th>
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

    <!-- Create Program Modal -->
    <div id="createProgramModal" class="modal">
        <div class="modal-content max-w-4xl">
            <div class="modal-header">
                <h2 class="text-xl font-semibold">Create New Program</h2>
                <span class="close" onclick="closeCreateProgramModal()">&times;</span>
            </div>
            <form id="createProgramForm">
                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="programTitle" class="block text-sm font-medium text-gray-700 mb-2">Program Title *</label>
                                <input type="text" id="programTitle" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                            
                            <div>
                                <label for="programSlug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                                <input type="text" id="programSlug" name="slug" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from title</p>
                            </div>
                            
                            <div>
                                <label for="backgroundColor" class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
                                <select id="backgroundColor" name="background_color" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="bg-blue-500" selected>Blue</option>
                                    <option value="bg-primary">Primary Blue</option>
                                    <option value="bg-secondary">Secondary Green</option>
                                    <option value="bg-secondaryShades-500">Secondary Light</option>
                                    <option value="bg-red-500">Red</option>
                                    <option value="bg-amber-500">Amber</option>
                                    <option value="bg-purple-500">Purple</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="sortOrder" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                <input type="number" id="sortOrder" name="sort_order" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="isFeatured" name="is_featured" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm text-gray-700">Featured Program</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" id="isActive" name="is_active" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                            
                            <div>
                                <label for="metaTitle" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" id="metaTitle" name="meta_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                            
                            <div>
                                <label for="metaDescription" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea id="metaDescription" name="meta_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="programDescription" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea id="programDescription" name="description" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="programContent" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                        <textarea id="programContent" name="content" rows="8" class="summernote-editor"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="iconSvg" class="block text-sm font-medium text-gray-700 mb-2">Icon SVG</label>
                        <textarea id="iconSvg" name="icon_svg" rows="3" placeholder='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="..."></path>' class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono text-sm"></textarea>
                        <p class="text-xs text-gray-500 mt-1">SVG path element for the program icon</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeCreateProgramModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 ml-3">Create Program</button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Programs DataTable
    let programsTable;

    $(document).ready(function() {
        initializeProgramsTable();
        loadProgramStats();
        
        // Search functionality
        $('#programSearch').on('keyup', function() {
            programsTable.search(this.value).draw();
        });
        
        // Status filter
        $('#statusFilter').on('change', function() {
            programsTable.column(2).search(this.value).draw();
        });
        
        // Auto-generate slug from title
        $('#programTitle').on('input', function() {
            const title = $(this).val();
            const slug = title.toLowerCase()
                             .replace(/[^a-z0-9\s-]/g, '')
                             .replace(/\s+/g, '-')
                             .replace(/-+/g, '-')
                             .trim('-');
            $('#programSlug').val(slug);
        });
        
        // Create program form submission
        $('#createProgramForm').on('submit', function(e) {
            e.preventDefault();
            createProgram();
        });
    });

    function initializeProgramsTable() {
        programsTable = $('#programsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/programs/getPrograms') ?>",
                "type": "POST",
                "error": function(xhr, error, thrown) {
                    console.error('DataTable Error:', error);
                    showNotification('Error loading programs data', 'error');
                }
            },
            "columns": [
                {
                    "data": "title",
                    "width": "25%",
                    "render": function(data, type, row) {
                        // Handle background color - use a default if not set or if it's a hex color
                        let bgColor = row.background_color;
                        if (!bgColor || bgColor.startsWith('#')) {
                            bgColor = 'bg-secondary'; // Default fallback
                        }

                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 ${bgColor} rounded-lg flex items-center justify-center text-white mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        ${row.icon_svg || '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'}
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-sm text-gray-500">${row.slug}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "description",
                    "render": function(data) {
                        if (!data) return '<span class="text-xs text-gray-400 italic">No description</span>';
                        return `<div class="text-xs text-gray-600 line-clamp-2 max-w-xs" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;" title="${data}">${data}</div>`;
                    }
                },
                {
                    "data": "is_active",
                    "render": function(data) {
                        return data == 1 
                            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>'
                            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>';
                    }
                },
                {
                    "data": "is_featured",
                    "render": function(data) {
                        return data == 1 
                            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800"><i class="w-3 h-3 mr-1" data-lucide="star"></i>Featured</span>'
                            : '<span class="text-gray-400">-</span>';
                    }
                },
                {
                    "data": "sort_order",
                    "className": "text-center",
                    "render": function(data) {
                        return `<span class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-600 rounded-full text-sm font-medium">${data || 0}</span>`;
                    }
                },
                {
                    "data": "created_at",
                    "className": "text-center",
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
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        const statusIcon = row.is_active == 1 ? 'toggle-right' : 'toggle-left';
                        const statusTitle = row.is_active == 1 ? 'Deactivate' : 'Activate';
                        const statusColor = row.is_active == 1 ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800';

                        return `
                            <div class="flex items-center space-x-2">
                                <button onclick="viewProgram('${row.slug}')" class="text-blue-600 hover:text-blue-800 transition duration-200" title="View">
                                    <i class="w-4 h-4" data-lucide="eye"></i>
                                </button>
                                <button onclick="editProgram('${row.id}')" class="text-slate-600 hover:text-slate-800 transition duration-200" title="Edit">
                                    <i class="w-4 h-4" data-lucide="edit"></i>
                                </button>
                                <button onclick="toggleProgramStatus('${row.id}', ${row.is_active}, '${row.title}')" class="${statusColor} transition duration-200" title="${statusTitle}">
                                    <i class="w-4 h-4" data-lucide="${statusIcon}"></i>
                                </button>
                                <button onclick="deleteProgram('${row.id}', '${row.title}')" class="text-red-600 hover:text-red-800 transition duration-200" title="Delete">
                                    <i class="w-4 h-4" data-lucide="trash-2"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search Programs...",
            },
            "order": [[4, "asc"], [5, "desc"]],
            "pageLength": 25,
            "responsive": true,
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    }

    function loadProgramStats() {
        $.ajax({
            url: '<?= base_url('auth/programs/getStats') ?>',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#totalPrograms').text(response.data.total_programs || 0);
                    $('#activePrograms').text(response.data.active_programs || 0);
                    $('#featuredPrograms').text(response.data.featured_programs || 0);
                    $('#draftPrograms').text(response.data.draft_programs || 0);
                }
            },
            error: function() {
                console.error('Failed to load program statistics');
            }
        });
    }

    function showCreateProgramModal() {
        $('#createProgramModal').modal('show');
    }

    function closeCreateProgramModal() {
        $('#createProgramModal').modal('hide');
        $('#createProgramForm')[0].reset();
    }

    function createProgram() {
        const formData = new FormData($('#createProgramForm')[0]);
        
        $.ajax({
            url: '<?= base_url('auth/programs/create') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('Program created successfully!', 'success');
                    closeCreateProgramModal();
                    refreshProgramsTable();
                    loadProgramStats();
                } else {
                    showNotification(response.message || 'Failed to create program', 'error');
                }
            },
            error: function() {
                showNotification('An error occurred while creating the program', 'error');
            }
        });
    }

    function refreshProgramsTable() {
        programsTable.ajax.reload();
        loadProgramStats();
    }

    function viewProgram(slug) {
        window.open('<?= base_url('ksp/programs/') ?>' + slug, '_blank');
    }

    function editProgram(id) {
        window.location.href = '<?= base_url('auth/programs/edit/') ?>' + id;
    }

    function toggleProgramStatus(id, currentStatus, programTitle) {
        const newStatus = currentStatus == 1 ? 0 : 1;
        const action = newStatus == 1 ? 'activate' : 'deactivate';
        const actionPast = newStatus == 1 ? 'activated' : 'deactivated';

        Swal.fire({
            title: `${action.charAt(0).toUpperCase() + action.slice(1)} Program?`,
            html: `Are you sure you want to ${action} <strong>${programTitle}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: newStatus == 1 ? '#10b981' : '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: `Yes, ${action} it!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: `${action.charAt(0).toUpperCase() + action.slice(1)}ing...`,
                    text: 'Please wait',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('auth/programs/toggleStatus/') ?>' + id,
                    method: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success || response.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: `Program ${actionPast} successfully!`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            refreshProgramsTable();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || `Failed to ${action} program`,
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || `An error occurred while ${action}ing the program`,
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }

    function deleteProgram(id, programTitle) {
        Swal.fire({
            title: 'Delete Program?',
            html: `Are you sure you want to delete <strong>${programTitle}</strong>?<br><small class="text-red-600">This action cannot be undone!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the program',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('auth/programs/delete/') ?>' + id,
                    method: 'DELETE',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.success || response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Program deleted successfully!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            refreshProgramsTable();
                            loadProgramStats();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to delete program',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'An error occurred while deleting the program',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }

    function showNotification(message, type) {
        // Implement your notification system here
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(message); // Temporary - replace with your notification system
    }
</script>
<?= $this->endSection() ?>