<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Forums</p>
                <p class="text-3xl font-bold text-gray-900"><?= $forumStats['forum_count'] ?></p>
                <p class="text-sm text-green-600 mt-1">+2 this month</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
                <i data-lucide="message-square" class="w-8 h-8 text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Active Discussions</p>
                <p class="text-3xl font-bold text-gray-900"><?= $forumStats['total_discussions'] ?></p>
                <p class="text-sm text-green-600 mt-1">+15% from last week</p>
            </div>
            <div class="p-3 bg-green-100 rounded-full">
                <i data-lucide="message-circle" class="w-8 h-8 text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Pending Reports</p>
                <p class="text-3xl font-bold text-gray-900"><?= $forumStats['total_reports'] ?></p>
                <p class="text-sm text-red-600 mt-1">Needs attention</p>
            </div>
            <div class="p-3 bg-orange-100 rounded-full">
                <i data-lucide="flag" class="w-8 h-8 text-orange-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Moderators</p>
                <p class="text-3xl font-bold text-gray-900"><?= $forumStats['total_moderator'] ?></p>
                <p class="text-sm text-blue-600 mt-1">Across all forums</p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
                <i data-lucide="shield-check" class="w-8 h-8 text-purple-600"></i>
            </div>
        </div>
    </div>
</div>