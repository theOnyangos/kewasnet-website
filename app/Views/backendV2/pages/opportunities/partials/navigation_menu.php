<!-- Navigation Menu for Opportunities Section -->
<nav class="mb-6">
    <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
        <a href="<?= site_url('auth/opportunities') ?>" 
           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors <?= (current_url() == site_url('auth/opportunities')) ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-blue-600' ?>">
            <i data-lucide="briefcase" class="w-4 h-4 mr-2"></i>
            Job Opportunities
        </a>
        <a href="<?= site_url('auth/opportunities/applications') ?>" 
           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors <?= (current_url() == site_url('auth/opportunities/applications')) ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-blue-600' ?>">
            <i data-lucide="users" class="w-4 h-4 mr-2"></i>
            Job Applications
        </a>
        <?php if (session()->get('role_id') == 1): ?>
        <a href="<?= site_url('auth/opportunities/deleted') ?>" 
           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors <?= (current_url() == site_url('auth/opportunities/deleted')) ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-blue-600' ?>">
            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
            Deleted Opportunities
        </a>
        <?php endif; ?>
    </div>
</nav>