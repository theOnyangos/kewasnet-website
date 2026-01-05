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
            'pageTitle' => 'Enrolled Students',
            'pageDescription' => 'Students enrolled in: ' . esc($course['title']),
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses/courses')],
                ['label' => esc($course['title']), 'url' => site_url('auth/courses/edit/' . $courseId)],
                ['label' => 'Enrolled Students']
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/courses') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Courses
            </a>'
        ]) ?>

        <div class="px-6 pb-6">

        <!-- Enrolled Students Table -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Enrolled Students</h2>
                    <p class="mt-1 text-sm text-slate-500">View all students enrolled in this course</p>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="tableLoader" class="flex flex-col items-center justify-center py-12">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
                <p class="mt-4 text-sm text-gray-600">Loading enrolled students...</p>
            </div>

            <!-- Students DataTable -->
            <div id="tableContainer" class="overflow-x-auto hidden">
                <table id="enrolledStudentsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Student Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Progress</th>
                            <th scope="col">Last Accessed</th>
                            <th scope="col">Completed</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        </div>

        <!-- Progress Modal -->
        <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4"></div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Loading Students</h3>
                    <p class="text-sm text-gray-500 mb-4">Please wait while we fetch enrolled students...</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="loadProgress" class="bg-primary h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    let enrolledStudentsTable;

    $(document).ready(function() {
        lucide.createIcons();
        
        // Show progress modal
        $('#progressModal').show();
        
        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += 10;
            $('#loadProgress').css('width', progress + '%');
            if (progress >= 90) {
                clearInterval(progressInterval);
            }
        }, 100);

        // Initialize Enrolled Students DataTable
        enrolledStudentsTable = $('#enrolledStudentsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/courses/get-enrolled-students/' . $courseId) ?>",
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
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "email",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-700">${data || 'N/A'}</span>`;
                    }
                },
                {
                    "data": "phone",
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
                            <div class="w-full">
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
                    "data": "user_id",
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <a href="<?= base_url('auth/courses/enrolled-students/' . $courseId) ?>/student/${data}" 
                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="bar-chart-2" class="w-4 h-4 mr-1.5"></i>
                                View Statistics
                            </a>
                        `;
                    }
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search students...",
            },
            "order": [[5, 'desc']],
            "initComplete": function() {
                // Complete the progress bar
                $('#loadProgress').css('width', '100%');
                
                // Hide progress modal and show table
                setTimeout(function() {
                    $('#progressModal').fadeOut(300, function() {
                        $('#tableLoader').fadeOut(300, function() {
                            $('#tableContainer').removeClass('hidden').hide().fadeIn(300);
                        });
                    });
                }, 300);
            },
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });
</script>
<?= $this->endSection() ?>
