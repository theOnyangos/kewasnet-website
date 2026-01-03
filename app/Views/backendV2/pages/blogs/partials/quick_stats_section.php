<?php
// Mock data - replace with actual data from controller
$blogStats = [
    'total_posts' => $blogStats['total_posts'] ?? 245,
    'published_posts' => $blogStats['published_posts'] ?? 189,
    'draft_posts' => $blogStats['draft_posts'] ?? 56,
    'total_categories' => $blogStats['total_categories'] ?? 12,
    'total_views' => $blogStats['total_views'] ?? 15420,
    'total_comments' => $blogStats['total_comments'] ?? 348,
    'newsletters_sent' => $blogStats['newsletters_sent'] ?? 23,
    'subscribers' => $blogStats['subscribers'] ?? 1250
];
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Posts</p>
                <p class="text-3xl font-bold text-gray-900"><?= $blogStats['total_posts'] ?></p>
                <p class="text-sm text-green-600 mt-1"><?= $blogStats['published_posts'] ?> published</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
                <i data-lucide="file-text" class="w-8 h-8 text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Views</p>
                <p class="text-3xl font-bold text-gray-900"><?= number_format($blogStats['total_views']) ?></p>
                <p class="text-sm text-green-600 mt-1">+15% from last month</p>
            </div>
            <div class="p-3 bg-green-100 rounded-full">
                <i data-lucide="eye" class="w-8 h-8 text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Categories</p>
                <p class="text-3xl font-bold text-gray-900"><?= $blogStats['total_categories'] ?></p>
                <p class="text-sm text-blue-600 mt-1"><?= $blogStats['total_comments'] ?> comments</p>
            </div>
            <div class="p-3 bg-orange-100 rounded-full">
                <i data-lucide="folder" class="w-8 h-8 text-orange-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Newsletter Subscribers</p>
                <p class="text-3xl font-bold text-gray-900"><?= number_format($blogStats['subscribers']) ?></p>
                <p class="text-sm text-blue-600 mt-1"><?= $blogStats['newsletters_sent'] ?> campaigns sent</p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
                <i data-lucide="mail" class="w-8 h-8 text-purple-600"></i>
            </div>
        </div>
    </div>
</div>
