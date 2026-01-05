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
        $activeTab = 'events';
    }
?>
<div class="bg-white rounded-t-xl shadow-sm pb-6 pt-10">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="<?= base_url('auth/events') ?>" 
                class="py-4 px-1 <?= ($activeTab === 'events' || $activeTab === 'index' || empty($activeTab)) ? $activeClass : $inactiveClass ?>">
                <i data-lucide="calendar" class="w-4 h-4 mr-2 inline"></i>
                Events
            </a>
            <a href="<?= base_url('auth/events/ticket-types') ?>" 
                class="py-4 px-1 <?= $activeTab === 'ticket-types' ? $activeClass : $inactiveClass ?>">
                <i data-lucide="ticket" class="w-4 h-4 mr-2 inline"></i>
                Ticket Types
            </a>
            <a href="<?= base_url('auth/events/bookings') ?>" 
                class="py-4 px-1 <?= ($activeTab === 'bookings' || $activeTab === 'allBookings') ? $activeClass : $inactiveClass ?>">
                <i data-lucide="users" class="w-4 h-4 mr-2 inline"></i>
                Bookings
            </a>
        </nav>
    </div>
</div>

