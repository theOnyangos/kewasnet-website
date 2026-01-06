<?php
// Get pillar statistics from controller data
$pillarStats = [
    'total_pillars' => $pillarStats['total_pillars'] ?? 8,
    'active_pillars' => $pillarStats['active_pillars'] ?? 6,
    'inactive_pillars' => $pillarStats['inactive_pillars'] ?? 2,
    'total_articles' => $pillarStats['total_articles'] ?? 142,
    'published_articles' => $pillarStats['published_articles'] ?? 115,
    'draft_articles' => $pillarStats['draft_articles'] ?? 27,
    'total_downloads' => $pillarStats['total_downloads'] ?? 5430,
    'total_document_types' => $pillarStats['total_document_types'] ?? 5
];
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Pillars</p>
                <h3 class="text-2xl font-bold mt-1"><?= $pillarStats['total_pillars'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="columns" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> <?= $pillarStats['active_pillars'] ?> active
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Articles</p>
                <h3 class="text-2xl font-bold mt-1"><?= $pillarStats['total_articles'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="file-text" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> <?= $pillarStats['published_articles'] ?> published
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Downloads</p>
                <h3 class="text-2xl font-bold mt-1"><?= number_format($pillarStats['total_downloads']) ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="download" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +23% this month
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Document Types</p>
                <h3 class="text-2xl font-bold mt-1"><?= $pillarStats['total_document_types'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="file-type" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="edit" class="w-4 h-4 mr-1"></i> <?= $pillarStats['draft_articles'] ?> drafts
        </p>
    </div>
</div>
