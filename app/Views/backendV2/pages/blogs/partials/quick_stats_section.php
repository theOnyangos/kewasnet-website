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

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Posts</p>
                <h3 class="text-2xl font-bold mt-1"><?= $blogStats['total_posts'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="file-text" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> <?= $blogStats['published_posts'] ?> published
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Views</p>
                <h3 class="text-2xl font-bold mt-1"><?= number_format($blogStats['total_views']) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="eye" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +15% from last month
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Categories</p>
                <h3 class="text-2xl font-bold mt-1"><?= $blogStats['total_categories'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="folder" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="message-square" class="w-4 h-4 mr-1"></i> <?= $blogStats['total_comments'] ?> comments
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Newsletter Subscribers</p>
                <h3 class="text-2xl font-bold mt-1"><?= number_format($blogStats['subscribers']) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="mail" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="send" class="w-4 h-4 mr-1"></i> <?= $blogStats['newsletters_sent'] ?> campaigns sent
        </p>
    </div>
</div>
