<!-- Top Header for Opportunities Section -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800"><?= $pageTitle ?? 'Opportunities' ?></h1>
        <p class="mt-1 text-sm text-slate-500"><?= $pageDescription ?? 'Manage job opportunities and applications' ?></p>
    </div>
    <div class="flex space-x-3 mt-4 md:mt-0">
        <?php if (isset($showCreateButton) && $showCreateButton): ?>
        <button type="button" onClick="window.location.href='<?= site_url('auth/opportunities/create') ?>'" 
                class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
            <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
            <span>Add Opportunity</span>
        </button>
        <?php endif; ?>
        
        <?php if (isset($additionalButtons)): ?>
            <?= $additionalButtons ?>
        <?php endif; ?>
    </div>
</div>