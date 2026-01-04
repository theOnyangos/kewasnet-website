<?php
    $uri = service('uri');
    $segments = $uri->getSegments();
    $activeClass = "border-b-2 border-cyan-500 text-sm text-cyan-600";
    $inactiveClass = "border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300";

    // Determine active tab based on URI segment
    $activeTab = '';
    if (isset($segments[2])) {
        $activeTab = $segments[2];
    }

    // Set default tab
    if (empty($activeTab) || $activeTab === 'index') {
        $activeTab = 'overview';
    }
?>
<div class="bg-white rounded-t-xl shadow-sm pb-6 pt-10">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="<?= base_url('auth/courses') ?>"
                class="py-4 px-1 <?= ($activeTab === 'overview' || $activeTab === 'index') ? $activeClass : $inactiveClass ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2 inline"></i>
                Overview
            </a>
            <a href="<?= base_url('auth/courses/courses') ?>"
                class="py-4 px-1 <?= $activeTab === 'courses' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="book-open" class="w-4 h-4 mr-2 inline"></i>
                All Courses
            </a>
            <a href="<?= base_url('auth/courses/sections') ?>"
                class="py-4 px-1 <?= $activeTab === 'sections' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="layers" class="w-4 h-4 mr-2 inline"></i>
                Sections
            </a>
            <a href="<?= base_url('auth/courses/lectures') ?>"
                class="py-4 px-1 <?= $activeTab === 'lectures' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="play-circle" class="w-4 h-4 mr-2 inline"></i>
                Lectures
            </a>
            <a href="<?= base_url('auth/courses/quizzes') ?>"
                class="py-4 px-1 <?= $activeTab === 'quizzes' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="clipboard-list" class="w-4 h-4 mr-2 inline"></i>
                Quizzes
            </a>
            <a href="<?= base_url('auth/courses/enrollments') ?>"
                class="py-4 px-1 <?= $activeTab === 'enrollments' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="users" class="w-4 h-4 mr-2 inline"></i>
                Enrollments
            </a>
        </nav>
    </div>
</div>
