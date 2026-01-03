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

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Pillars</p>
                <p class="text-3xl font-bold text-gray-900"><?= $pillarStats['total_pillars'] ?></p>
                <p class="text-sm text-green-600 mt-1"><?= $pillarStats['active_pillars'] ?> active</p>
            </div>
            <div class="p-3 bg-emerald-100 rounded-full">
                <i data-lucide="columns" class="w-8 h-8 text-emerald-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Articles</p>
                <p class="text-3xl font-bold text-gray-900"><?= $pillarStats['total_articles'] ?></p>
                <p class="text-sm text-green-600 mt-1"><?= $pillarStats['published_articles'] ?> published</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
                <i data-lucide="file-text" class="w-8 h-8 text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Downloads</p>
                <p class="text-3xl font-bold text-gray-900"><?= number_format($pillarStats['total_downloads']) ?></p>
                <p class="text-sm text-blue-600 mt-1">+23% this month</p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
                <i data-lucide="download" class="w-8 h-8 text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Document Types</p>
                <p class="text-3xl font-bold text-gray-900"><?= $pillarStats['total_document_types'] ?></p>
                <p class="text-sm text-blue-600 mt-1"><?= $pillarStats['draft_articles'] ?> drafts</p>
            </div>
            <div class="p-3 bg-orange-100 rounded-full">
                <i data-lucide="file-type" class="w-8 h-8 text-orange-600"></i>
            </div>
        </div>
    </div>
</div>
