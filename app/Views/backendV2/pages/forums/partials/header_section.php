<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Forums</h1>
            <p class="text-slate-600">Manage your community forums, discussions, and moderation</p>
        </div>
        <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
            <button type="button" onClick="window.location.href='<?= site_url('auth/forums/tags') ?>'" class="flex items-center px-6 py-3 rounded-[50px] bg-primaryShades-100 text-primary hover:bg-primaryShades-200 transition-all duration-300">
                <i data-lucide="tag" class="w-5 h-5 mr-2"></i>
                <span>Manage Tags</span>
            </button>
        </div>
    </div>
</div>