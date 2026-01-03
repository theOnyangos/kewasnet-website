<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Learning Hub Course Management</h1>
            <p class="text-slate-600">Create, manage, and publish courses, sections, lectures, and track enrollments</p>
        </div>
        <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
            <button type="button" onClick="window.location.href='<?= site_url('auth/courses/create') ?>'" class="gradient-btn flex items-center px-6 py-3 rounded-[50px] text-white hover:shadow-md transition-all duration-300">
                <i data-lucide="plus" class="w-5 h-5 mr-2 z-10"></i>
                <span>Create Course</span>
            </button>
            <button type="button" onClick="window.open('<?= site_url('ksp/learning-hub') ?>', '_blank')" class="flex items-center px-6 py-3 rounded-[50px] bg-green-100 text-green-700 hover:bg-green-200 transition-all duration-300">
                <i data-lucide="external-link" class="w-5 h-5 mr-2"></i>
                <span>View Frontend</span>
            </button>
        </div>
    </div>
</div>
