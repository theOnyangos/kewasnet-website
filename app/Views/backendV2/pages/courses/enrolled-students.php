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
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <a href="<?= site_url('auth/courses') ?>" class="text-gray-500 hover:text-gray-700">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </a>
                        <h1 class="text-3xl font-bold text-slate-800"><?= $dashboardTitle ?></h1>
                    </div>
                    <p class="text-gray-600">Students enrolled in: <span class="font-semibold text-blue-600"><?= esc($course['title']) ?></span></p>
                </div>
            </div>
        </div>

        <!-- Course Info Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm p-6 mb-6 border border-blue-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500 rounded-lg p-3">
                        <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Course Title</p>
                        <p class="text-lg font-semibold text-gray-900"><?= esc($course['title']) ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="bg-green-500 rounded-lg p-3">
                        <i data-lucide="signal" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Level</p>
                        <p class="text-lg font-semibold text-gray-900 capitalize"><?= esc($course['level']) ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="bg-purple-500 rounded-lg p-3">
                        <i data-lucide="tag" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Price</p>
                        <p class="text-lg font-semibold text-gray-900">
                            <?php if ($course['is_paid'] == 0 || $course['price'] == 0): ?>
                                <span class="text-green-600">Free</span>
                            <?php else: ?>
                                KES <?= number_format($course['price'], 2) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="bg-orange-500 rounded-lg p-3">
                        <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="text-lg font-semibold text-gray-900"><?= esc($course['duration'] ?? 'N/A') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Students Table -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Enrolled Students</h2>
                    <p class="mt-1 text-sm text-slate-500">View all students enrolled in this course</p>
                </div>
            </div>

            <!-- Students DataTable -->
            <div class="overflow-x-auto">
                <table id="enrolledStudentsTable" class="data-table stripe hover" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col">Student Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Progress</th>
                            <th scope="col">Last Accessed</th>
                            <th scope="col">Completed</th>
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
    let enrolledStudentsTable;

    $(document).ready(function() {
        lucide.createIcons();

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
                }
            ],
            "responsive": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search students...",
            },
            "order": [[5, 'desc']],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });
</script>
<?= $this->endSection() ?>
