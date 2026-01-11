<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  ?>
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Notifications',
            'pageDescription' => 'Manage and view all your notifications',
            'breadcrumbs' => [
                ['label' => 'Notifications']
            ]
        ]) ?>

        <div class="px-6 pb-6">
            <!-- Page Content -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <!-- Top Header with Filters -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">All Notifications</h1>
                        <p class="mt-1 text-sm text-slate-500">View and manage all your notifications</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <!-- Filter Buttons -->
                        <div class="flex gap-2 bg-gray-100 rounded-lg p-1">
                            <button data-filter="all" class="filter-btn px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 bg-white text-primary shadow-sm">
                                All
                            </button>
                            <button data-filter="unread" class="filter-btn px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-900">
                                Unread
                            </button>
                            <button data-filter="read" class="filter-btn px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-900">
                                Read
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <button id="markAllReadBtnPage" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primaryShades-600 transition-colors text-sm font-medium flex items-center gap-2">
                            <i data-lucide="check-check" class="w-4 h-4"></i>
                            Mark All Read
                        </button>
                        <button id="clearAllReadBtn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium flex items-center gap-2">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Clear Read
                        </button>
                    </div>
                </div>

                <!-- Notifications Table -->
                <div class="overflow-x-auto">
                    <table id="notificationsTable" class="data-table stripe hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    let notificationsTable;
    let currentFilter = 'all';

    $(document).ready(function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Initialize Notifications DataTable
        notificationsTable = $('#notificationsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[4, 'desc']], // Order by date descending
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search notifications...",
                "lengthMenu": "Show _MENU_ notifications per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ notifications",
                "infoEmpty": "No notifications found",
                "infoFiltered": "(filtered from _MAX_ total notifications)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "ajax": {
                "url": "<?= base_url('auth/notifications/get') ?>",
                "type": "POST",
                "data": function(d) {
                    d.filterType = currentFilter;
                },
                "error": function(xhr, error, code) {
                    console.error('DataTable Ajax Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            },
            "columns": [
                {
                    data: 'type',
                    name: 'type',
                    width: '80px',
                    render: function(data, type, row) {
                        const typeIcons = {
                            'success': '<i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>',
                            'warning': '<i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>',
                            'error': '<i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>',
                            'info': '<i data-lucide="info" class="w-5 h-5 text-blue-600"></i>',
                            'system': '<i data-lucide="settings" class="w-5 h-5 text-purple-600"></i>'
                        };
                        return `<div class="flex items-center justify-center">${typeIcons[data] || typeIcons['info']}</div>`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    width: '200px',
                    render: function(data, type, row) {
                        const icon = row.icon || 'bell';
                        const isUnread = row.status === 'unread';
                        const fontWeight = isUnread ? 'font-semibold' : 'font-normal';
                        return `<div class="flex items-center gap-2">
                            <i data-lucide="${icon}" class="w-8 h-8 text-secondary"></i>
                            <span class="${fontWeight} text-primary text-sm">${data}</span>
                            ${isUnread ? '<span class="w-2 h-2 bg-primary rounded-full"></span>' : ''}
                        </div>`;
                    }
                },
                {
                    data: 'message',
                    name: 'message',
                    render: function(data, type, row) {
                        const opacity = row.status === 'read' ? 'opacity-70' : '';
                        return `<span class="text-sm text-gray-600 ${opacity}">${data}</span>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    width: '100px',
                    render: function(data, type, row) {
                        if (data === 'unread') {
                            return '<span class="px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">Unread</span>';
                        } else {
                            return '<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Read</span>';
                        }
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    width: '150px',
                    render: function(data, type, row) {
                        const formattedDate = formatDate(new Date(data));
                        return `<span class="text-gray-600 text-sm">${formattedDate}</span>`;
                    }
                },
                {
                    data: 'id',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    width: '150px',
                    render: function(data, type, row) {
                        let actionBtn = '';
                        if (row.action_url) {
                            actionBtn = `<a href="${row.action_url}" class="text-primary hover:text-primaryShades-600 p-1" title="${row.action_text || 'View'}">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>`;
                        }

                        const markReadBtn = row.status === 'unread'
                            ? `<button class="text-green-600 hover:text-green-900 p-1" onclick="markAsReadSingle('${data}')" title="Mark as Read">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>`
                            : '';

                        return `
                            <div class="flex space-x-2 justify-center">
                                ${actionBtn}
                                ${markReadBtn}
                                <button class="text-red-600 hover:text-red-900 p-1" onclick="deleteNotification('${data}')" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Reinitialize Lucide icons after DataTable draw
        notificationsTable.on('draw', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Filter button clicks
        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('bg-white text-primary shadow-sm').addClass('text-gray-600');
            $(this).addClass('bg-white text-primary shadow-sm').removeClass('text-gray-600');

            currentFilter = $(this).data('filter');
            notificationsTable.ajax.reload();
        });

        // Mark all as read
        $('#markAllReadBtnPage').on('click', function() {
            markAllAsRead();
        });

        // Clear all read notifications
        $('#clearAllReadBtn').on('click', function() {
            clearAllRead();
        });
    });

    // Mark single notification as read
    window.markAsReadSingle = function(notificationId) {
        $.ajax({
            url: `<?= base_url('auth/notifications') ?>/${notificationId}/mark-read`,
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    notificationsTable.ajax.reload(null, false);

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Notification marked as read',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to mark notification as read'
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Failed to mark notification as read';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    };

    // Mark all as read
    function markAllAsRead() {
        Swal.fire({
            title: 'Mark all as read?',
            text: "This will mark all notifications as read",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, mark all',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('auth/notifications/mark-all-read') ?>',
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            notificationsTable.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'All notifications marked as read',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to mark all notifications as read'
                        });
                    }
                });
            }
        });
    }

    // Clear all read notifications
    function clearAllRead() {
        Swal.fire({
            title: 'Clear all read notifications?',
            text: "This will permanently delete all read notifications",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, clear them',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('auth/notifications/clear-all') ?>',
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            notificationsTable.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'All read notifications cleared',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to clear notifications'
                        });
                    }
                });
            }
        });
    }

    // Delete single notification
    window.deleteNotification = function(notificationId) {
        Swal.fire({
            title: 'Delete notification?',
            text: "This action cannot be undone",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('auth/notifications') ?>/${notificationId}`,
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            notificationsTable.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: 'Notification deleted successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete notification'
                        });
                    }
                });
            }
        });
    };
</script>
<?= $this->endSection() ?>
