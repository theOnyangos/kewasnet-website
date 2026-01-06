<?php
$courseStats = [
    'total_courses' => $courseStats['total_courses'] ?? 0,
    'published_courses' => $courseStats['published_courses'] ?? 0,
    'draft_courses' => $courseStats['draft_courses'] ?? 0,
    'total_enrollments' => $courseStats['total_enrollments'] ?? 0,
    'total_sections' => $courseStats['total_sections'] ?? 0,
    'total_lectures' => $courseStats['total_lectures'] ?? 0,
    'certificates_issued' => $courseStats['certificates_issued'] ?? 0,
    'total_revenue' => $courseStats['total_revenue'] ?? 0
];
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
    <!-- Total Courses Card -->
    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Courses</p>
                <h3 class="text-2xl font-bold mt-1"><?= $courseStats['total_courses'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="book-open" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> <?= $courseStats['published_courses'] ?> published
        </p>
    </div>

    <!-- Total Enrollments Card -->
    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Enrollments</p>
                <h3 class="text-2xl font-bold mt-1"><?= number_format($courseStats['total_enrollments']) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="award" class="w-4 h-4 mr-1"></i> <?= $courseStats['certificates_issued'] ?> certificates issued
        </p>
    </div>

    <!-- Content Card -->
    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Course Content</p>
                <h3 class="text-2xl font-bold mt-1"><?= $courseStats['total_sections'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="play-circle" class="w-4 h-4 mr-1"></i> <?= $courseStats['total_lectures'] ?> lectures
        </p>
    </div>

    <!-- Total Revenue Card -->
    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Revenue</p>
                <h3 class="text-2xl font-bold mt-1">KES <?= number_format($courseStats['total_revenue']) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="dollar-sign" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> From paid courses
        </p>
    </div>
</div>
