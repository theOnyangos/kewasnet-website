<!-- Notification Modal Panel -->
<!-- Backdrop -->
<div id="notificationBackdrop" class="hidden fixed inset-0 bg-black bg-opacity-30 z-[9998]" style="display: none; z-index: 9998;"></div>

<!-- Notification Panel (Fixed Position) -->
<div id="notificationDropdown" class="hidden fixed top-16 right-4 w-96 bg-white rounded-lg shadow-2xl border border-gray-200 z-[99999]" style="display: none; max-height: calc(100vh - 100px); backdrop-filter: none; -webkit-backdrop-filter: none; z-index: 99999;">
    <!-- Panel Header -->
    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
        <div class="flex items-center space-x-2">
            <button id="markAllReadBtn" class="text-sm text-primary hover:text-primaryShades-600 font-medium">
                Mark all as read
            </button>
            <button id="closeNotificationPanel" type="button" class="p-1 hover:bg-gray-100 rounded-full transition-colors cursor-pointer" onclick="if(typeof window.NotificationManager !== 'undefined') { window.NotificationManager.close(); }">
                <i data-lucide="x" class="w-5 h-5 text-gray-500 pointer-events-none"></i>
            </button>
        </div>
    </div>

    <!-- Notifications List -->
    <div id="notificationsList" class="overflow-y-auto" style="max-height: calc(100vh - 200px); backdrop-filter: none; -webkit-backdrop-filter: none;">
        <!-- Loading State -->
        <div id="notificationsLoading" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <!-- Empty State -->
        <div id="notificationsEmpty" class="hidden text-center py-8 px-4">
            <i data-lucide="bell-off" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
            <p class="text-gray-500 text-sm">No notifications yet</p>
            <p class="text-gray-400 text-xs mt-1">We'll notify you when something arrives</p>
        </div>

        <!-- Notifications Items Container -->
        <div id="notificationsContent" class="hidden">
            <!-- Notifications will be dynamically inserted here -->
        </div>
    </div>

    <!-- Panel Footer -->
    <div class="px-4 py-3 border-t border-gray-200 text-center">
        <a href="<?= base_url('ksp/notifications') ?>" class="text-sm text-primary hover:text-primaryShades-600 font-medium">
            View all notifications
        </a>
    </div>
</div>

<!-- Notification Item Template (Hidden) -->
<template id="notificationItemTemplate">
    <div class="notification-item border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" data-notification-id="">
        <div class="px-4 py-3 flex items-start space-x-3">
            <!-- Icon -->
            <div class="notification-icon-container flex-shrink-0 mt-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center">
                    <i data-lucide="bell" class="notification-icon w-5 h-5"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="notification-title text-sm font-semibold text-gray-800 truncate"></p>
                        <p class="notification-message text-sm text-gray-600 mt-1 line-clamp-2"></p>
                        <p class="notification-time text-xs text-gray-400 mt-1"></p>
                    </div>
                    <!-- Unread Indicator -->
                    <span class="notification-unread-dot flex-shrink-0 ml-2 mt-1 w-2 h-2 bg-primary rounded-full"></span>
                </div>

                <!-- Action Button -->
                <a class="notification-action-btn hidden mt-2 text-xs text-primary hover:text-primaryShades-600 font-medium" href="#">
                    <span class="notification-action-text"></span> →
                </a>
            </div>

            <!-- Delete Button -->
            <button class="notification-delete-btn flex-shrink-0 ml-2 p-1 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</template>

<style>
/* Notification Type Colors */
.notification-type-success .notification-icon-container > div {
    @apply bg-green-100;
}
.notification-type-success .notification-icon {
    @apply text-green-600;
}

.notification-type-warning .notification-icon-container > div {
    @apply bg-yellow-100;
}
.notification-type-warning .notification-icon {
    @apply text-yellow-600;
}

.notification-type-error .notification-icon-container > div {
    @apply bg-red-100;
}
.notification-type-error .notification-icon {
    @apply text-red-600;
}

.notification-type-info .notification-icon-container > div {
    @apply bg-blue-100;
}
.notification-type-info .notification-icon {
    @apply text-blue-600;
}

.notification-type-system .notification-icon-container > div {
    @apply bg-purple-100;
}
.notification-type-system .notification-icon {
    @apply text-purple-600;
}

/* Read notification styling */
.notification-item.read {
    @apply bg-gray-50 opacity-75;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive Mobile Styles */
@media (max-width: 768px) {
    #notificationDropdown {
        left: 1rem !important;
        right: 1rem !important;
        width: calc(100% - 2rem) !important;
        max-width: none !important;
        top: 4rem !important;
    }
}

/* Smooth animations */
#notificationDropdown,
#notificationBackdrop {
    transition: opacity 0.2s ease-in-out;
}

/* Remove any blur effects from dropdown */
#notificationDropdown {
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    filter: none !important;
    z-index: 9999 !important;
    position: fixed !important;
    pointer-events: auto !important;
}

#notificationBackdrop {
    z-index: 9998 !important;
    position: fixed !important;
    pointer-events: auto !important;
}

#notificationDropdown * {
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    pointer-events: auto !important;
}

/* Ensure dropdown is clickable and on top */
#notificationDropdown,
#notificationBackdrop {
    isolation: isolate;
}
</style>
