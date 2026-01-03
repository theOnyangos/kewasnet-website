/**
 * Notifications Handler
 * Manages notification dropdown and real-time updates
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        refreshInterval: 30000, // 30 seconds
        maxNotificationsInDropdown: 10,
        apiBaseUrl: window.location.origin,
    };

    // State
    let refreshTimer = null;
    let isDropdownOpen = false;

    /**
     * Initialize notifications
     */
    function init() {
        console.log('Initializing notification system...');

        // Check if required elements exist
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (!notificationButton) {
            console.error('Notification button not found');
            return;
        }

        if (!notificationDropdown) {
            console.error('Notification dropdown not found');
            return;
        }

        console.log('Notification elements found successfully');

        setupEventListeners();
        updateUnreadCount();
        startAutoRefresh();
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBackdrop = document.getElementById('notificationBackdrop');
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        const closePanel = document.getElementById('closeNotificationPanel');

        // Toggle panel
        if (notificationButton) {
            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log('Notification button clicked');
                toggleDropdown();
            });
            console.log('Click listener added to notification button');
        }

        // Close panel button
        if (closePanel) {
            closePanel.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeDropdown();
            });
        }

        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                markAllAsRead();
            });
        }

        // Close panel when clicking backdrop
        if (notificationBackdrop) {
            notificationBackdrop.addEventListener('click', function() {
                closeDropdown();
            });
        }

        // Prevent panel from closing when clicking inside
        if (notificationDropdown) {
            notificationDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isDropdownOpen) {
                closeDropdown();
            }
        });
    }

    /**
     * Toggle notification dropdown
     */
    function toggleDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        if (!dropdown) {
            console.error('Notification dropdown element not found');
            return;
        }

        if (isDropdownOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    /**
     * Open dropdown
     */
    function openDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        const backdrop = document.getElementById('notificationBackdrop');

        if (!dropdown) {
            console.error('Notification dropdown element not found');
            return;
        }

        console.log('Opening notification panel...');

        // Show backdrop
        if (backdrop) {
            backdrop.style.display = 'block';
            backdrop.classList.remove('hidden');
        }

        // Show panel
        dropdown.style.display = 'block';
        dropdown.classList.remove('hidden');

        isDropdownOpen = true;
        loadNotifications();

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Reinitialize Lucide icons for close button
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        console.log('Notification panel opened');
    }

    /**
     * Close dropdown
     */
    function closeDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        const backdrop = document.getElementById('notificationBackdrop');

        if (!dropdown) return;

        console.log('Closing notification panel...');

        // Hide panel
        dropdown.style.display = 'none';
        dropdown.classList.add('hidden');

        // Hide backdrop
        if (backdrop) {
            backdrop.style.display = 'none';
            backdrop.classList.add('hidden');
        }

        isDropdownOpen = false;

        // Restore body scroll
        document.body.style.overflow = '';

        console.log('Notification panel closed');
    }

    /**
     * Load notifications
     */
    function loadNotifications() {
        showLoading();

        fetch(`${CONFIG.apiBaseUrl}/auth/notifications/get-recent?limit=${CONFIG.maxNotificationsInDropdown}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayNotifications(data.data);
            } else {
                showError('Failed to load notifications');
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            showError('Failed to load notifications');
        });
    }

    /**
     * Display notifications in dropdown
     */
    function displayNotifications(notifications) {
        const content = document.getElementById('notificationsContent');
        const loading = document.getElementById('notificationsLoading');
        const empty = document.getElementById('notificationsEmpty');

        hideLoading();

        if (!notifications || notifications.length === 0) {
            content.classList.add('hidden');
            empty.classList.remove('hidden');
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            return;
        }

        empty.classList.add('hidden');
        content.classList.remove('hidden');
        content.innerHTML = '';

        notifications.forEach(notification => {
            const item = createNotificationItem(notification);
            content.appendChild(item);
        });

        // Reinitialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    /**
     * Create notification item element
     */
    function createNotificationItem(notification) {
        const template = document.getElementById('notificationItemTemplate');
        const clone = template.content.cloneNode(true);

        const item = clone.querySelector('.notification-item');
        item.dataset.notificationId = notification.id;
        item.classList.add(`notification-type-${notification.type || 'info'}`);

        if (notification.status === 'read') {
            item.classList.add('read');
            const unreadDot = clone.querySelector('.notification-unread-dot');
            if (unreadDot) {
                unreadDot.classList.add('hidden');
            }
        }

        // Set icon
        const icon = clone.querySelector('.notification-icon');
        if (icon && notification.icon) {
            icon.setAttribute('data-lucide', notification.icon);
        }

        // Set title
        const title = clone.querySelector('.notification-title');
        if (title) {
            title.textContent = notification.title || 'Notification';
        }

        // Set message
        const message = clone.querySelector('.notification-message');
        if (message) {
            message.textContent = notification.message;
        }

        // Set time
        const time = clone.querySelector('.notification-time');
        if (time) {
            time.textContent = formatTimeAgo(notification.created_at);
        }

        // Set action button
        if (notification.action_url) {
            const actionBtn = clone.querySelector('.notification-action-btn');
            const actionText = clone.querySelector('.notification-action-text');
            if (actionBtn && actionText) {
                actionBtn.href = notification.action_url;
                actionText.textContent = notification.action_text || 'View';
                actionBtn.classList.remove('hidden');
            }
        }

        // Click to mark as read and navigate
        item.addEventListener('click', function(e) {
            if (!e.target.classList.contains('notification-delete-btn') &&
                !e.target.closest('.notification-delete-btn')) {
                markAsRead(notification.id);
                if (notification.action_url) {
                    window.location.href = notification.action_url;
                }
            }
        });

        // Delete button
        const deleteBtn = clone.querySelector('.notification-delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                deleteNotification(notification.id);
            });
        }

        return clone;
    }

    /**
     * Update unread count badge
     */
    function updateUnreadCount() {
        fetch(`${CONFIG.apiBaseUrl}/auth/notifications/unread-count`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateBadge(data.count);
            }
        })
        .catch(error => {
            console.error('Error updating unread count:', error);
        });
    }

    /**
     * Update badge display
     */
    function updateBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;

        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    }

    /**
     * Mark notification as read
     */
    function markAsRead(notificationId) {
        fetch(`${CONFIG.apiBaseUrl}/auth/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateUnreadCount();
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    /**
     * Mark all notifications as read
     */
    function markAllAsRead() {
        fetch(`${CONFIG.apiBaseUrl}/auth/notifications/mark-all-read`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateUnreadCount();
                loadNotifications();
                showToast('All notifications marked as read', 'success');
            } else {
                showToast('Failed to mark all as read', 'error');
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
            showToast('Failed to mark all as read', 'error');
        });
    }

    /**
     * Delete notification
     */
    function deleteNotification(notificationId) {
        fetch(`${CONFIG.apiBaseUrl}/auth/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateUnreadCount();
                loadNotifications();
                showToast('Notification deleted', 'success');
            } else {
                showToast('Failed to delete notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
            showToast('Failed to delete notification', 'error');
        });
    }

    /**
     * Show loading state
     */
    function showLoading() {
        const loading = document.getElementById('notificationsLoading');
        const content = document.getElementById('notificationsContent');
        const empty = document.getElementById('notificationsEmpty');

        if (loading) loading.classList.remove('hidden');
        if (content) content.classList.add('hidden');
        if (empty) empty.classList.add('hidden');
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        const loading = document.getElementById('notificationsLoading');
        if (loading) loading.classList.add('hidden');
    }

    /**
     * Show error message
     */
    function showError(message) {
        hideLoading();
        const content = document.getElementById('notificationsContent');
        const empty = document.getElementById('notificationsEmpty');

        if (content) content.classList.add('hidden');
        if (empty) {
            empty.classList.remove('hidden');
            const emptyText = empty.querySelector('p');
            if (emptyText) {
                emptyText.textContent = message;
            }
        }
    }

    /**
     * Show toast notification
     */
    function showToast(message, type = 'info') {
        // Use SweetAlert2 if available
        if (typeof Swal !== 'undefined') {
            const icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

    /**
     * Format time ago
     */
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        const intervals = {
            year: 31536000,
            month: 2592000,
            week: 604800,
            day: 86400,
            hour: 3600,
            minute: 60,
        };

        for (const [unit, secondsInUnit] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInUnit);
            if (interval >= 1) {
                return interval === 1 ? `1 ${unit} ago` : `${interval} ${unit}s ago`;
            }
        }

        return 'just now';
    }

    /**
     * Start auto-refresh
     */
    function startAutoRefresh() {
        refreshTimer = setInterval(() => {
            updateUnreadCount();
            if (isDropdownOpen) {
                loadNotifications();
            }
        }, CONFIG.refreshInterval);
    }

    /**
     * Stop auto-refresh
     */
    function stopAutoRefresh() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
            refreshTimer = null;
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', stopAutoRefresh);

    // Expose API for external use
    window.NotificationManager = {
        refresh: loadNotifications,
        updateCount: updateUnreadCount,
        markAsRead: markAsRead,
        markAllAsRead: markAllAsRead,
        deleteNotification: deleteNotification,
    };
})();
