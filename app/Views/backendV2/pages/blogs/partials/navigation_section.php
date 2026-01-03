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
            <a href="<?= base_url('auth/blogs') ?>" 
                class="py-4 px-1 <?= ($activeTab === 'overview' || $activeTab === 'index') ? $activeClass : $inactiveClass ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2 inline"></i>
                Overview
            </a>
            <a href="<?= base_url('auth/blogs/blogs') ?>" 
                class="py-4 px-1 <?= $activeTab === 'blogs' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="file-text" class="w-4 h-4 mr-2 inline"></i>
                Blog Posts
            </a>
            <a href="<?= base_url('auth/blogs/newsletters') ?>" 
                class="py-4 px-1 <?= $activeTab === 'newsletters' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="mail" class="w-4 h-4 mr-2 inline"></i>
                Newsletters
            </a>
            <?php if(session()->get('role_id') == 1): ?>
            <a href="<?= base_url('auth/blogs/deleted') ?>" 
                class="py-4 px-1 <?= $activeTab === 'deleted' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="trash-2" class="w-4 h-4 mr-2 inline"></i>
                Deleted Posts
            </a>
            <?php endif; ?>
        </nav>
    </div>
</div>
