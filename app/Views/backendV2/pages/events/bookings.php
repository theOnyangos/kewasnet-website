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
            'pageTitle' => 'Bookings Management',
            'pageDescription' => 'View and manage all event bookings',
            'breadcrumbs' => [
                ['label' => 'Events', 'url' => base_url('auth/events')],
                ['label' => 'Bookings']
            ]
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Navigation Tabs -->
        <?= view('backendV2/pages/events/partials/navigation_section') ?>
        
        <!-- Bookings Table -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
                <!-- Title and Description -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">
                            <?php if (isset($event) && !empty($event['title'])): ?>
                                Bookings: <?= esc($event['title']) ?>
                            <?php else: ?>
                                Event Bookings
                            <?php endif; ?>
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            <?php if (isset($event) && !empty($event['title'])): ?>
                                View and manage bookings for this event
                            <?php else: ?>
                                View and manage all event bookings
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Bookings Table -->
                <table id="bookingsTable" class="data-table stripe hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Booking Number</th>
                            <?php if (!isset($event) || empty($event['id'])): ?>
                            <th>Event</th>
                            <?php endif; ?>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
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
    let bookingsDataTable;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize DataTable
        bookingsDataTable = $('#bookingsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search bookings...",
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
                "url": "<?= base_url('auth/events/get-bookings') ?>",
                "type": "POST",
                "data": function(d) {
                    // Pass event_id if available (for filtering by specific event)
                    <?php if (isset($event) && !empty($event['id'])): ?>
                    d.event_id = '<?= esc($event['id'], 'js') ?>';
                    <?php endif; ?>
                },
                "dataSrc": function(json) {
                    return json.data;
                },
                "error": function(xhr) {
                    showToast('Failed to load bookings data', 'error');
                    console.error("DataTables error:", xhr.responseText);
                }
            },
            "columns": [
                { 
                    "data": "booking_number",
                    "width": "200px",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark font-mono border border-gray-200 rounded-md px-2 py-1').text(data)[0].outerHTML;
                    },
                },
                <?php if (!isset($event) || empty($event['id'])): ?>
                { 
                    "data": "event_title",
                    "width": "200px",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark').text(data || 'N/A')[0].outerHTML;
                    }
                },
                <?php endif; ?>
                { 
                    "data": "customer_name",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm text-gray-600').text(data || 'N/A')[0].outerHTML;
                    }
                },
                { 
                    "data": "email",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm text-gray-600').text(data || 'N/A')[0].outerHTML;
                    }
                },
                { 
                    "data": "total_amount",
                    "render": function(data) {
                        return `<span class="text-sm font-medium text-dark">KES ${parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                    }
                },
                { 
                    "data": "payment_status",
                    "render": function(data) {
                        let badgeClass = 'bg-gray-100 text-gray-800';
                        if (data === 'paid') badgeClass = 'bg-green-100 text-green-800';
                        if (data === 'pending') badgeClass = 'bg-yellow-100 text-yellow-800';
                        if (data === 'failed') badgeClass = 'bg-red-100 text-red-800';
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">${data ? data.charAt(0).toUpperCase() + data.slice(1) : 'N/A'}</span>`;
                    }
                },
                { 
                    "data": "status",
                    "render": function(data) {
                        let badgeClass = 'bg-gray-100 text-gray-800';
                        if (data === 'confirmed') badgeClass = 'bg-green-100 text-green-800';
                        if (data === 'cancelled') badgeClass = 'bg-red-100 text-red-800';
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium ${badgeClass}">${data ? data.charAt(0).toUpperCase() + data.slice(1) : 'N/A'}</span>`;
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
                        let cancelButton = '';
                        if (row.status !== 'cancelled') {
                            cancelButton = `
                                <button onclick="cancelBooking('${row.id}')" class="p-1 text-slate-500 hover:text-yellow-500" title="Cancel Booking">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                </button>
                            `;
                        }
                        return `
                            <div class="flex space-x-2">
                                <a href="<?= base_url('auth/events/booking') ?>/${row.id}" class="p-1 text-slate-500 hover:text-primary" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                ${cancelButton}
                                <button onclick="deleteBooking('${row.id}')" class="p-1 text-slate-500 hover:text-red-500" title="Delete Booking">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "order": [[7, "desc"]],
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });

    /**
     * Cancel booking
     */
    function cancelBooking(bookingId) {
        Swal.fire({
            title: 'Cancel Booking?',
            html: `
                <div class="text-left">
                    <p class="text-gray-700 mb-3">Are you sure you want to cancel this booking? This will invalidate all tickets associated with this booking.</p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-3">
                        <p class="text-sm text-yellow-800">
                            <strong>Warning:</strong> This action will cancel the booking and invalidate all tickets. This cannot be undone.
                        </p>
                    </div>
                    <p class="text-sm text-gray-600">Are you sure you want to proceed?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#eab308',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, cancel booking',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal-wide',
                htmlContainer: 'text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Cancelling...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= base_url('auth/events/booking') ?>/${bookingId}/cancel`,
                    type: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Cancelled!',
                                text: response.message || 'Booking cancelled successfully',
                                icon: 'success',
                                confirmButtonColor: '#3b82f6',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                // Reload the table
                                bookingsDataTable.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to cancel booking',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        let errorMessage = 'An error occurred while cancelling the booking';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }

    /**
     * Delete booking
     */
    function deleteBooking(bookingId) {
        Swal.fire({
            title: 'Delete Booking?',
            html: `
                <div class="text-left">
                    <p class="text-gray-700 mb-3">Are you sure you want to delete this booking? This action cannot be undone.</p>
                    <div class="bg-red-50 border-l-4 border-red-400 p-3 mb-3">
                        <p class="text-sm text-red-800">
                            <strong>Warning:</strong> This will permanently delete the booking from the system. The booking will be soft-deleted and hidden from view.
                        </p>
                    </div>
                    <p class="text-sm text-gray-600">Are you sure you want to proceed?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete booking',
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
                    url: `<?= base_url('auth/events/booking') ?>/${bookingId}`,
                    type: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Booking deleted successfully',
                                icon: 'success',
                                confirmButtonColor: '#3b82f6',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                // Reload the table
                                bookingsDataTable.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to delete booking',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        let errorMessage = 'An error occurred while deleting the booking';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
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
