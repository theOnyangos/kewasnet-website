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

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Events Management',
            'pageDescription' => 'Manage events, bookings, and ticket types',
            'breadcrumbs' => [
                ['label' => 'Events']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Event Statistics -->
        <?php
        $stats = [
            'total_events' => $eventStats['total_events'] ?? 0,
            'published_events' => $eventStats['published_events'] ?? 0,
            'draft_events' => $eventStats['draft_events'] ?? 0,
            'cancelled_events' => $eventStats['cancelled_events'] ?? 0,
            'paid_events' => $eventStats['paid_events'] ?? 0,
            'free_events' => $eventStats['free_events'] ?? 0,
            'total_bookings' => $eventStats['total_bookings'] ?? 0,
            'total_tickets_sold' => $eventStats['total_tickets_sold'] ?? 0,
            'total_revenue' => $eventStats['total_revenue'] ?? 0,
            'upcoming_events' => $eventStats['upcoming_events'] ?? 0
        ];
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 stats-container">
            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Total Events</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_events']) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> <?= number_format($stats['published_events']) ?> published
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Total Bookings</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_bookings']) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="ticket" class="w-4 h-4 mr-1"></i> <?= number_format($stats['total_tickets_sold']) ?> tickets sold
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Total Revenue</p>
                        <h3 class="text-2xl font-bold mt-1">KES <?= number_format($stats['total_revenue'], 2) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i> From paid bookings
                </p>
            </div>

            <div class="bg-gradient-to-br from-primaryShades-400 to-primaryShades-600 rounded-xl shadow-sm p-6 stat-card text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primaryShades-300">Upcoming Events</p>
                        <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['upcoming_events']) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-sm text-white/80 mt-3 flex items-center">
                    <i data-lucide="calendar-days" class="w-4 h-4 mr-1"></i> <?= number_format($stats['paid_events']) ?> paid, <?= number_format($stats['free_events']) ?> free
                </p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <?= view('backendV2/pages/events/partials/navigation_section') ?>
        
        <!-- Events Table -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <!-- Title and Description -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Events Management</h1>
                    <p class="mt-1 text-sm text-slate-500">View and manage all events in the system</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="<?= base_url('auth/events/create') ?>" class="gradient-btn flex items-center px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                        <span>Create New Event</span>
                    </a>
                </div>
            </div>

            <!-- Events Table -->
            <table id="eventsTable" class="data-table stripe hover" style="width:100%">
                <thead>
                    <tr>
                        <th class="">Title</th>
                        <th class="">Type</th>
                        <th class="">Start Date</th>
                        <th class="">Venue</th>
                        <th class="">Status</th>
                        <th class="">Created At</th>
                        <th class="">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    let eventsDataTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize DataTable
        eventsDataTable = $('#eventsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search events...",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "ajax": {
                "url": "<?= base_url('auth/events/get-events') ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    return json.data;
                },
                "error": function(xhr) {
                    showToast('Failed to load events data', 'error');
                    console.error("DataTables error:", xhr.responseText);
                }
            },
            "columns": [
                { 
                    "data": "title",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark').text(data)[0].outerHTML;
                    }
                },
                { 
                    "data": "event_type",
                    "render": function(data) {
                        const badgeClass = data === 'Paid' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    "data": "start_date",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm text-gray-600').text(data)[0].outerHTML;
                    }
                },
                { 
                    "data": "venue",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm text-gray-600').text(data)[0].outerHTML;
                    }
                },
                { 
                    "data": "status",
                    "render": function(data) {
                        let badgeClass = 'bg-gray-100 text-gray-800';
                        if (data === 'Published') badgeClass = 'bg-green-100 text-green-800';
                        if (data === 'Cancelled') badgeClass = 'bg-red-100 text-red-800';
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    "data": "created_at",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-gray-500').text(data)[0].outerHTML;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex space-x-2">
                                <a href="<?= base_url('auth/events/edit') ?>/${row.id}" class="p-1 text-slate-500 hover:text-primary" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= base_url('auth/events') ?>/${row.id}/bookings" class="p-1 text-slate-500 hover:text-blue-500" title="View Bookings">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </a>
                                <button onclick="deleteEvent('${row.id}')" class="p-1 text-slate-500 hover:text-red-500" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "order": [[5, "desc"]], // Default order by created_at descending
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });

    function deleteEvent(eventId) {
        if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: '<?= base_url('auth/events/delete') ?>/' + eventId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    showToast(response.message, 'success');
                    eventsDataTable.ajax.reload();
                } else {
                    showToast(response.message || 'Failed to delete event', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showToast(response.message || 'An error occurred', 'error');
            }
        });
    }
</script>
<?= $this->endSection() ?>

