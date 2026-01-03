<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Blog Management</h1>
            <p class="text-slate-600">Create, manage, and publish your blog content and newsletters</p>
        </div>
        <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
            <button type="button" onClick="window.location.href='<?= site_url('auth/blogs/settings') ?>'" class="flex items-center px-6 py-3 rounded-[50px] bg-primaryShades-100 text-primary hover:bg-primaryShades-200 transition-all duration-300">
                <i data-lucide="settings" class="w-5 h-5 mr-2"></i>
                <span>Blog Settings</span>
            </button>
            <button type="button" onClick="window.open('<?= site_url('auth/activity-dashboard') ?>', '_blank')" class="flex items-center px-6 py-3 rounded-[50px] bg-green-100 text-green-700 hover:bg-green-200 transition-all duration-300">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2"></i>
                <span>Analytics</span>
            </button>
        </div>
    </div>
</div>
