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
            'pageTitle' => 'Ticket Types Management',
            'pageDescription' => 'Manage ticket types for all events',
            'breadcrumbs' => [
                ['label' => 'Events', 'url' => base_url('auth/events')],
                ['label' => 'Ticket Types']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Ticket Type Statistics -->
        <?php
        $stats = [
            'total_ticket_types'    => $ticketTypeStats['total_ticket_types'] ?? 0,
            'active_ticket_types'   => $ticketTypeStats['active_ticket_types'] ?? 0,
            'inactive_ticket_types' => $ticketTypeStats['inactive_ticket_types'] ?? 0,
            'total_tickets_sold'    => $ticketTypeStats['total_tickets_sold'] ?? 0,
            'active_tickets_sold'   => $ticketTypeStats['active_tickets_sold'] ?? 0,
            'total_revenue'         => $ticketTypeStats['total_revenue'] ?? 0,
            'tickets_checked_in'    => $ticketTypeStats['tickets_checked_in'] ?? 0
        ];
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Ticket Types</p>
                        <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_ticket_types']) ?></p>
                        <p class="text-sm text-green-600 mt-1"><?= number_format($stats['active_ticket_types']) ?> active</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i data-lucide="ticket" class="w-8 h-8 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Tickets Sold</p>
                        <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_tickets_sold']) ?></p>
                        <p class="text-sm text-blue-600 mt-1"><?= number_format($stats['tickets_checked_in']) ?> checked in</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i data-lucide="users" class="w-8 h-8 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-3xl font-bold text-gray-900">KES <?= number_format($stats['total_revenue'], 2) ?></p>
                        <p class="text-sm text-green-600 mt-1">From paid bookings</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i data-lucide="dollar-sign" class="w-8 h-8 text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Tickets Sold</p>
                        <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['active_tickets_sold']) ?></p>
                        <p class="text-sm text-gray-600 mt-1">From active types</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i data-lucide="check-circle" class="w-8 h-8 text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <?= view('backendV2/pages/events/partials/navigation_section') ?>
        
        <!-- Ticket Types Table -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
                <!-- Title and Description -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Ticket Types</h1>
                        <p class="mt-1 text-sm text-slate-500">Manage ticket types for all events</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="<?= base_url('auth/events/ticket-types/create') ?>" class="gradient-btn flex items-center px-4 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                            <i data-lucide="plus" class="w-4 h-4 mr-2 z-10"></i>
                            <span>Create Ticket Type</span>
                        </a>
                    </div>
                </div>

                <!-- Ticket Types Table -->
                <table id="ticketTypesTable" class="data-table stripe hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Sales Period</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
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
    let ticketTypesDataTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize DataTable
        ticketTypesDataTable = $('#ticketTypesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search ticket types...",
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
                "url": "<?= base_url('auth/events/get-ticket-types') ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    return json.data;
                },
                "error": function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load ticket types data',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    console.error("DataTables error:", xhr.responseText);
                }
            },
            "columns": [
                { 
                    "data": "event_title",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark').text(data || 'N/A')[0].outerHTML;
                    }
                },
                { 
                    "data": "name",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark').text(data)[0].outerHTML;
                    }
                },
                { 
                    "data": "price",
                    "render": function(data) {
                        return `<span class="text-sm font-medium text-dark">KES ${parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                    }
                },
                { 
                    "data": "quantity",
                    "render": function(data) {
                        return data ? `<span class="text-sm text-gray-600">${parseInt(data).toLocaleString()}</span>` : '<span class="text-sm text-green-600">Unlimited</span>';
                    }
                },
                { 
                    "data": "sales_period",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm text-gray-600').text(data || 'N/A')[0].outerHTML;
                    }
                },
                { 
                    "data": "status",
                    "render": function(data) {
                        const badgeClass = data === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
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
                                <a href="<?= base_url('auth/events/ticket-types/edit') ?>/${row.id}" class="p-1 text-slate-500 hover:text-primary" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="deleteTicketType('${row.id}')" class="p-1 text-slate-500 hover:text-red-500" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "order": [[6, "desc"]],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });


    function deleteTicketType(id) {
        Swal.fire({
            title: 'Delete Ticket Type?',
            html: `
                <div class="text-left">
                    <p class="text-gray-700 mb-3">Are you sure you want to delete this ticket type? This action cannot be undone.</p>
                    <div class="bg-red-50 border-l-4 border-red-400 p-3">
                        <p class="text-sm text-red-800">
                            <strong>Warning:</strong> Deleting this ticket type will affect any existing bookings that use it.
                        </p>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal-wide',
                htmlContainer: 'text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('auth/events/delete-ticket-type') ?>/' + id,
                    type: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Ticket type deleted successfully',
                                icon: 'success',
                                confirmButtonColor: '#3b82f6',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                ticketTypesDataTable.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to delete ticket type',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'An error occurred while deleting the ticket type',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }

</script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $this->endSection() ?>

