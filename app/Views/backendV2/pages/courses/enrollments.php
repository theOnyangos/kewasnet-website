<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set(['redirect_url' => $currentUrl::currentUrl()]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto p-6">
        <?= $this->include('backendV2/pages/courses/partials/header_section') ?>
        <?= $this->include('backendV2/pages/courses/partials/quick_stats_section') ?>
        <?= $this->include('backendV2/pages/courses/partials/navigation_section') ?>

        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Course Enrollments</h1>
                    <p class="mt-1 text-sm text-slate-500">View and manage student enrollments</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="enrollmentsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Progress</th>
                            <th>Last Accessed</th>
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        lucide.createIcons();

        $('#enrollmentsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/courses/get-enrollments') ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "student_name",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                                        ${data ? data.charAt(0).toUpperCase() : 'U'}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${data || 'Unknown'}</div>
                                    <div class="text-xs text-gray-500">${row.student_email || ''}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "course_title",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-700">${data || 'N/A'}</span>`;
                    }
                },
                {
                    "data": "progress_percentage",
                    "render": function(data) {
                        const progress = parseFloat(data) || 0;
                        let colorClass = 'bg-red-500';
                        if (progress > 70) colorClass = 'bg-green-500';
                        else if (progress > 30) colorClass = 'bg-yellow-500';
                        
                        return `
                            <div class="w-full max-w-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">${progress.toFixed(1)}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="${colorClass} h-2 rounded-full transition-all duration-300" style="width: ${progress}%"></div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "last_accessed_at",
                    "render": function(data) {
                        if (!data) return '<span class="text-sm text-gray-500">Never</span>';
                        const date = new Date(data);
                        const now = new Date();
                        const diffMs = now - date;
                        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                        
                        let timeText = '';
                        if (diffDays === 0) timeText = 'Today';
                        else if (diffDays === 1) timeText = 'Yesterday';
                        else if (diffDays < 7) timeText = `${diffDays} days ago`;
                        else timeText = date.toLocaleDateString();
                        
                        return `<span class="text-sm text-gray-700">${timeText}</span>`;
                    }
                },
                {
                    "data": "completed_at",
                    "render": function(data) {
                        if (!data) {
                            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">In Progress</span>`;
                        }
                        return `
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-1">Completed</span>
                                <div class="text-xs text-gray-500">${new Date(data).toLocaleDateString()}</div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "id",
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <div class="flex justify-center space-x-2">
                                <button onclick="viewEnrollmentDetails('${row.course_id}', '${row.user_id}')" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search enrollments...",
            },
            "order": [[3, 'desc']],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });

    function viewEnrollmentDetails(courseId, userId) {
        window.location.href = `<?= site_url('auth/courses/enrolled-students') ?>/${courseId}`;
    }
</script>
<?= $this->endSection() ?>
