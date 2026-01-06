<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    session()->set(['redirect_url' => $currentUrl::currentUrl()]);
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
            'pageTitle' => 'All Courses',
            'pageDescription' => 'Manage and view all learning hub courses',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses')],
                ['label' => 'All Courses']
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/create') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add New Course
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/courses/partials/header_section') ?>

        <!-- Quick Stats Cards -->
        <?= $this->include('backendV2/pages/courses/partials/quick_stats_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/courses/partials/navigation_section') ?>

        <!-- Courses Table -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">All Courses</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage and view all learning hub courses</p>
                </div>
                <div class="flex space-x-3 mt-4 md:mt-0">
                    <button type="button" onclick="window.location.href='<?= site_url('auth/courses/create') ?>'" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Add Course</span>
                    </button>
                </div>
            </div>

            <!-- Courses DataTable -->
            <div class="overflow-x-auto">
                <table id="coursesTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Course Title</th>
                            <th scope="col">Category</th>
                            <th scope="col">Level</th>
                            <th scope="col">Price</th>
                            <th scope="col">Enrollments</th>
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
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    let coursesTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Courses DataTable
        coursesTable = $('#coursesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/courses/get-courses') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "title",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="ml-0">
                                    <div class="text-sm font-medium text-gray-900">${data}</div>
                                    <div class="text-xs text-gray-500">${row.slug || ''}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "category_name",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-700">${data || 'Uncategorized'}</span>`;
                    }
                },
                {
                    "data": "level",
                    "render": function(data) {
                        const colors = {
                            'beginner': 'bg-green-100 text-green-800',
                            'intermediate': 'bg-yellow-100 text-yellow-800',
                            'advanced': 'bg-red-100 text-red-800'
                        };
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${colors[data] || 'bg-gray-100 text-gray-800'}">${data}</span>`;
                    }
                },
                {
                    "data": "price",
                    "render": function(data, type, row) {
                        if (row.is_paid == 0 || data == 0) {
                            return `<span class="text-sm font-semibold text-green-600">Free</span>`;
                        }
                        return `<span class="text-sm font-medium text-gray-900">KES ${parseFloat(data).toLocaleString()}</span>`;
                    }
                },
                {
                    "data": "enrollments_count",
                    "className": "text-center",
                    "render": function(data) {
                        return `<span class="text-sm font-medium">${data || 0}</span>`;
                    }
                },
                {
                    "data": "status",
                    "className": "text-left",
                    "render": function(data) {
                        // Convert numeric status to text
                        let statusText = data;
                        if (data == 0 || data === 'draft') {
                            statusText = 'draft';
                        } else if (data == 1 || data === 'published') {
                            statusText = 'published';
                        } else if (data == 2 || data === 'archived') {
                            statusText = 'archived';
                        }

                        const statusColors = {
                            'published': 'bg-green-100 text-green-800',
                            'draft': 'bg-yellow-100 text-yellow-800',
                            'archived': 'bg-red-100 text-red-800'
                        };

                        const statusLabels = {
                            'published': 'Published',
                            'draft': 'Draft',
                            'archived': 'Archived'
                        };

                        return `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[statusText] || 'bg-gray-100 text-gray-800'}">
                                ${statusLabels[statusText] || statusText}
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
                                <button onclick="viewEnrolledStudents('${data}')" class="text-purple-600 hover:text-purple-900" title="View Enrolled Students">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </button>
                                <button onclick="editCourse('${data}')" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteCourse('${data}')" class="text-red-600 hover:text-red-900" title="Delete">
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
                "searchPlaceholder": "Search courses...",
            },
            "order": [[0, 'desc']],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });

    function viewEnrolledStudents(id) {
        window.location.href = `<?= site_url('auth/courses/enrolled-students') ?>/${id}`;
    }

    function editCourse(id) {
        window.location.href = `<?= site_url('auth/courses/edit') ?>/${id}`;
    }

    function deleteCourse(id) {
        const warningContent = `
            <div class="space-y-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-sm text-red-800 font-medium">⚠️ Warning: This action cannot be undone!</p>
                </div>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-sm text-yellow-800 font-medium">📌 Note: Courses with enrolled students cannot be deleted. Consider archiving instead.</p>
                </div>
                
                <p class="text-gray-700 text-sm">
                    Are you sure you want to permanently delete this course?
                </p>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-semibold text-gray-800 mb-2">This action will:</p>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Delete the course and all its content</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Remove all associated sections and lectures</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Delete all enrolled students from this course</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Remove all course progress and completion records</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Delete course reviews and ratings</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Remove all associated files and materials</span>
                        </li>
                    </ul>
                </div>
            </div>
        `;
        
        const footerButtons = `
            <button type="button" onclick="closeModal()" 
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors mr-2">
                Cancel
            </button>
            <button type="button" onclick="confirmDeleteCourse(${id})" 
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Yes, Delete Course
            </button>
        `;
        
        openModal({
            title: 'Delete Course',
            content: warningContent,
            footer: footerButtons,
            icon: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            iconBg: 'bg-red-100'
        });
    }

    function confirmDeleteCourse(id) {
        closeModal();
        
        $.ajax({
            url: `<?= site_url('auth/courses/delete') ?>/${id}`,
            method: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    coursesTable.ajax.reload();
                    showSuccessModal('Course Deleted', response.message || 'The course has been successfully deleted.');
                } else {
                    showErrorModal('Deletion Failed', response.message || 'Failed to delete course.');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showErrorModal('Error', response?.message || 'Failed to delete course. Please try again.');
            }
        });
    }

    function showSuccessModal(title, message) {
        openModal({
            title: title,
            content: `<p class="text-gray-700">${message}</p>`,
            footer: '<button type="button" onclick="closeModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">OK</button>',
            icon: '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            iconBg: 'bg-green-100'
        });
    }

    function showErrorModal(title, message) {
        openModal({
            title: title,
            content: `<p class="text-gray-700">${message}</p>`,
            footer: '<button type="button" onclick="closeModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">OK</button>',
            icon: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            iconBg: 'bg-red-100'
        });
    }
</script>
<?= $this->endSection() ?>
