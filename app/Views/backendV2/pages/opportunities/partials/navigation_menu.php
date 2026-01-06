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
        $activeTab = 'opportunities';
    }
?>
<div class="bg-white rounded-t-xl shadow-sm pb-6 pt-10">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="<?= base_url('auth/opportunities') ?>" 
                class="py-4 px-1 <?= ($activeTab === 'opportunities' || $activeTab === 'index' || empty($activeTab)) ? $activeClass : $inactiveClass ?>">
                <i data-lucide="briefcase" class="w-4 h-4 mr-2 inline"></i>
                Job Opportunities
            </a>
            <a href="<?= base_url('auth/opportunities/applications') ?>" 
                class="py-4 px-1 <?= $activeTab === 'applications' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="users" class="w-4 h-4 mr-2 inline"></i>
                Job Applications
            </a>
            <?php if (session()->get('role_id') == 1): ?>
            <a href="<?= base_url('auth/opportunities/deleted') ?>" 
                class="py-4 px-1 <?= $activeTab === 'deleted' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="trash-2" class="w-4 h-4 mr-2 inline"></i>
                Deleted Opportunities
            </a>
            <?php endif; ?>
        </nav>
    </div>
</div>