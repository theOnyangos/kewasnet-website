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

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Courses</p>
                <p class="text-3xl font-bold text-gray-900"><?= $courseStats['total_courses'] ?></p>
                <p class="text-sm text-green-600 mt-1"><?= $courseStats['published_courses'] ?> published</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
                <i data-lucide="book-open" class="w-8 h-8 text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Enrollments</p>
                <p class="text-3xl font-bold text-gray-900"><?= number_format($courseStats['total_enrollments']) ?></p>
                <p class="text-sm text-blue-600 mt-1"><?= $courseStats['certificates_issued'] ?> certificates</p>
            </div>
            <div class="p-3 bg-green-100 rounded-full">
                <i data-lucide="users" class="w-8 h-8 text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Content</p>
                <p class="text-3xl font-bold text-gray-900"><?= $courseStats['total_sections'] ?></p>
                <p class="text-sm text-blue-600 mt-1"><?= $courseStats['total_lectures'] ?> lectures</p>
            </div>
            <div class="p-3 bg-orange-100 rounded-full">
                <i data-lucide="layers" class="w-8 h-8 text-orange-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                <p class="text-3xl font-bold text-gray-900">KES <?= number_format($courseStats['total_revenue']) ?></p>
                <p class="text-sm text-green-600 mt-1">From paid courses</p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
                <i data-lucide="dollar-sign" class="w-8 h-8 text-purple-600"></i>
            </div>
        </div>
    </div>
</div>
