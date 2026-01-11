<!-- Dashboard Header Section -->
 <header class="bg-white shadow-sm z-10 p-2">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center space-x-4">
            <h1 class="text-xl font-semibold text-primary"><?= $dashboardTitle ?? "Dashboard Overview" ?></h1>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Notifications Dropdown -->
            <div id="notificationDropdownContainer">
                <button id="notificationButton" class="p-2 rounded-full hover:bg-primaryShades-200 relative">
                    <i data-lucide="bell" class="w-5 h-5 text-slate-500"></i>
                    <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden z-50">0</span>
                </button>
            </div>

            <button class="p-2 rounded-full hover:bg-primaryShades-200">
                <i data-lucide="mail" class="w-5 h-5 text-slate-500"></i>
            </button>
            <a href="<?= base_url('auth/logout') ?>" class="p-2 rounded-full hover:bg-primaryShades-200">
                <i data-lucide="log-out" class="w-5 h-5 text-slate-500"></i>
            </a>
        </div>
    </div>
</header>