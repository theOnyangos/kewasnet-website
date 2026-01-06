<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Forums</p>
                <h3 class="text-2xl font-bold mt-1"><?= $forumStats['forum_count'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="message-square" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +2 this month
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Active Discussions</p>
                <h3 class="text-2xl font-bold mt-1"><?= $forumStats['total_discussions'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="message-circle" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> +15% from last week
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Pending Reports</p>
                <h3 class="text-2xl font-bold mt-1"><?= $forumStats['total_reports'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="flag" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i> Needs attention
        </p>
    </div>

    <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primaryShades-300">Total Moderators</p>
                <h3 class="text-2xl font-bold mt-1"><?= $forumStats['total_moderator'] ?></h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-sm text-white/80 mt-3 flex items-center">
            <i data-lucide="users" class="w-4 h-4 mr-1"></i> Across all forums
        </p>
    </div>
</div>