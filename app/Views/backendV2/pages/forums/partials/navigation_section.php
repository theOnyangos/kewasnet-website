<?php 
    $uri = service('uri');
    $segments = $uri->getSegments();
    $activeClass = "border-b-2 border-cyan-500 text-sm text-cyan-600"; 
    $inactiveClass = "border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300";
    
    // Determine active tab based on URI segment
    $activeTab = '';
    if (isset($segments[2])) {
        $activeTab = str_replace('forums-', '', $segments[2]);
    }
?>
<div class="bg-white rounded-t-xl shadow-sm pb-6 pt-10">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button onClick="location.href='<?= base_url('auth/forums') ?>'" 
                class="forum-tab-btn py-4 px-1 <?= ($activeTab === 'forums' || empty($activeTab)) ? $activeClass : $inactiveClass ?>">
                <i data-lucide="message-square" class="w-4 h-4 mr-2 inline"></i>
                Forums
            </button>
            <button onClick="location.href='<?= base_url('auth/forums/reports') ?>'" 
                class="forum-tab-btn py-4 px-1 <?= $activeTab === 'reports' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="flag" class="w-4 h-4 mr-2 inline"></i>
                Reports
            </button>
        </nav>
    </div>
</div>