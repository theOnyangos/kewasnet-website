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
            'pageTitle' => 'Course Management',
            'pageDescription' => 'Manage courses, categories, and learning content',
            'breadcrumbs' => [
                ['label' => 'Courses']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/courses/partials/header_section') ?>

        <!-- Quick Stats Cards -->
        <?= $this->include('backendV2/pages/courses/partials/quick_stats_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/courses/partials/navigation_section') ?>

        <!-- Overview Tab Content - Course Categories/Analytics -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Recent Courses -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Courses</h3>
                        <i data-lucide="book-open" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div id="recentCourses" class="space-y-3">
                        <p class="text-sm text-gray-500">Loading recent courses...</p>
                    </div>
                </div>

                <!-- Top Enrolled Courses -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Top Enrolled Courses</h3>
                        <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div id="topCourses" class="space-y-3">
                        <p class="text-sm text-gray-500">Loading top courses...</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="window.location.href='<?= site_url('auth/courses/create') ?>'" class="flex items-center justify-center gap-3 p-4 bg-white border-2 border-dashed border-blue-300 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all duration-300">
                    <i data-lucide="plus-circle" class="w-6 h-6 text-blue-600"></i>
                    <span class="font-medium text-gray-700">Create New Course</span>
                </button>

                <button onclick="window.location.href='<?= site_url('auth/courses/courses') ?>'" class="flex items-center justify-center gap-3 p-4 bg-white border-2 border-dashed border-green-300 rounded-xl hover:border-green-500 hover:bg-green-50 transition-all duration-300">
                    <i data-lucide="list" class="w-6 h-6 text-green-600"></i>
                    <span class="font-medium text-gray-700">View All Courses</span>
                </button>

                <button onclick="window.location.href='<?= site_url('auth/courses/enrollments') ?>'" class="flex items-center justify-center gap-3 p-4 bg-white border-2 border-dashed border-purple-300 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition-all duration-300">
                    <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                    <span class="font-medium text-gray-700">Manage Enrollments</span>
                </button>
            </div>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        lucide.createIcons();

    $(document).ready(function() {
        lucide.createIcons();

        // Load recent courses and top courses
        loadRecentCourses();
        loadTopCourses();
    });

    function loadRecentCourses() {
        $.ajax({
            url: '<?= base_url('auth/courses/get-recent-courses') ?>',
            method: 'GET',
            success: function(response) {
                console.log('Recent courses response:', response);
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(course) {
                        const price = course.is_paid == 0 || course.price == 0 
                            ? '<span class="text-green-600 font-semibold">Free</span>'
                            : `<span class="text-blue-600 font-semibold">KES ${parseFloat(course.price).toLocaleString()}</span>`;
                        
                        const statusColor = course.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                        const statusText = course.status === 'published' ? 'Published' : 'Draft';
                        
                        html += `
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm">${course.title}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${statusColor}">
                                            ${statusText}
                                        </span>
                                        <span class="text-xs text-gray-500">${new Date(course.created_at).toLocaleDateString()}</span>
                                    </div>
                                </div>
                                <div class="text-right ml-3">
                                    ${price}
                                </div>
                            </div>
                        `;
                    });
                    $('#recentCourses').html(html);
                } else {
                    $('#recentCourses').html('<p class="text-sm text-gray-600">No recent courses available</p>');
                }
                lucide.createIcons();
            },
            error: function(xhr, status, error) {
                console.error('Recent courses error:', xhr, status, error);
                $('#recentCourses').html('<p class="text-sm text-red-600">Failed to load recent courses</p>');
            }
        });
    }

    function loadTopCourses() {
        $.ajax({
            url: '<?= base_url('auth/courses/get-top-enrolled-courses') ?>',
            method: 'GET',
            success: function(response) {
                console.log('Top courses response:', response);
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(course) {
                        const enrollmentCount = parseInt(course.enrollment_count) || 0;
                        const price = course.is_paid == 0 || course.price == 0 
                            ? '<span class="text-green-600 font-semibold">Free</span>'
                            : `<span class="text-blue-600 font-semibold">KES ${parseFloat(course.price).toLocaleString()}</span>`;
                        
                        html += `
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm">${course.title}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i data-lucide="users" class="w-3 h-3 mr-1"></i>
                                            ${enrollmentCount} enrolled
                                        </span>
                                        ${price}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#topCourses').html(html);
                } else {
                    $('#topCourses').html('<p class="text-sm text-gray-600">No enrollment data available</p>');
                }
                lucide.createIcons();
            },
            error: function(xhr, status, error) {
                console.error('Top courses error:', xhr, status, error);
                $('#topCourses').html('<p class="text-sm text-red-600">Failed to load top courses</p>');
            }
        });
    }
</script>
<?= $this->endSection() ?>
