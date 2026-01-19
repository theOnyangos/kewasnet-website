<?php
$courseStats = $courseStats ?? [];
$stats = [
    'total_courses' => $courseStats['total_courses'] ?? 0,
    'published_courses' => $courseStats['published_courses'] ?? 0,
    'draft_courses' => $courseStats['draft_courses'] ?? 0,
    'total_enrollments' => $courseStats['total_enrollments'] ?? 0,
    'total_sections' => $courseStats['total_sections'] ?? 0,
    'total_lectures' => $courseStats['total_lectures'] ?? 0,
    'certificates_issued' => $courseStats['certificates_issued'] ?? 0,
    'total_revenue' => $courseStats['total_revenue'] ?? 0,
    // Section-specific stats
    'active_sections' => $courseStats['active_sections'] ?? 0,
    'inactive_sections' => $courseStats['inactive_sections'] ?? 0,
    'sections_with_lectures' => $courseStats['sections_with_lectures'] ?? 0,
    // Lecture-specific stats
    'preview_lectures' => $courseStats['preview_lectures'] ?? 0,
    'total_duration_minutes' => $courseStats['total_duration_minutes'] ?? 0,
    'lectures_with_video' => $courseStats['lectures_with_video'] ?? 0,
    // Enrollment-specific stats
    'active_enrollments' => $courseStats['active_enrollments'] ?? 0,
    'completed_enrollments' => $courseStats['completed_enrollments'] ?? 0,
    'avg_progress' => $courseStats['avg_progress'] ?? 0,
];

// Determine page type based on available stats
$hasSectionStats = isset($courseStats['active_sections']);
$hasLectureStats = isset($courseStats['preview_lectures']);
$hasEnrollmentStats = isset($courseStats['active_enrollments']);
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
    <?php if ($hasSectionStats): ?>
        <!-- Sections Page Stats -->
        <!-- Total Sections Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Sections</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['total_sections'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> <?= $stats['active_sections'] ?> active
            </p>
        </div>

        <!-- Active Sections Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Active Sections</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['active_sections'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="play" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i> <?= $stats['inactive_sections'] ?> inactive
            </p>
        </div>

        <!-- Sections with Lectures Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">With Lectures</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['sections_with_lectures'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="play-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="book-open" class="w-4 h-4 mr-1"></i> Sections with content
            </p>
        </div>

        <!-- Courses Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Courses</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['total_courses'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="list" class="w-4 h-4 mr-1"></i> Across all courses
            </p>
        </div>
    <?php elseif ($hasLectureStats): ?>
        <!-- Lectures Page Stats -->
        <!-- Total Lectures Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Lectures</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['total_lectures'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="play-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="video" class="w-4 h-4 mr-1"></i> <?= $stats['lectures_with_video'] ?> with video
            </p>
        </div>

        <!-- Preview Lectures Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Preview Lectures</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['preview_lectures'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="unlock" class="w-4 h-4 mr-1"></i> Available for preview
            </p>
        </div>

        <!-- Total Duration Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Duration</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_duration_minutes']) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="timer" class="w-4 h-4 mr-1"></i> Minutes of content
            </p>
        </div>

        <!-- Sections Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Sections</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['total_sections'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="list" class="w-4 h-4 mr-1"></i> Across all courses
            </p>
        </div>
    <?php elseif ($hasEnrollmentStats): ?>
        <!-- Enrollments Page Stats -->
        <!-- Total Enrollments Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Enrollments</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_enrollments']) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="play" class="w-4 h-4 mr-1"></i> <?= number_format($stats['active_enrollments']) ?> active
            </p>
        </div>

        <!-- Active Enrollments Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Active Enrollments</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['active_enrollments']) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="clock" class="w-4 h-4 mr-1"></i> Last 30 days
            </p>
        </div>

        <!-- Completed Enrollments Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Completed</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['completed_enrollments']) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="award" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Certificates issued
            </p>
        </div>

        <!-- Average Progress Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Avg Progress</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['avg_progress'] ?>%</h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="bar-chart-2" class="w-4 h-4 mr-1"></i> Across all enrollments
            </p>
        </div>
    <?php else: ?>
        <!-- Default Course Stats -->
        <!-- Total Courses Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Courses</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['total_courses'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> <?= $stats['published_courses'] ?> published
            </p>
        </div>

        <!-- Total Enrollments Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Enrollments</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_enrollments']) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="award" class="w-4 h-4 mr-1"></i> <?= $stats['certificates_issued'] ?> certificates issued
            </p>
        </div>

        <!-- Content Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Course Content</p>
                    <h3 class="text-2xl font-bold mt-1"><?= $stats['total_sections'] ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="play-circle" class="w-4 h-4 mr-1"></i> <?= $stats['total_lectures'] ?> lectures
            </p>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primaryShades-300">Total Revenue</p>
                    <h3 class="text-2xl font-bold mt-1">KES <?= number_format($stats['total_revenue']) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-sm text-white/80 mt-3 flex items-center">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> From paid courses
            </p>
        </div>
    <?php endif; ?>
</div>
