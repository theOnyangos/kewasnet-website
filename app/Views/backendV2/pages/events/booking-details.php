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
            'pageTitle' => 'Booking Details',
            'pageDescription' => 'View complete booking information and ticket details',
            'breadcrumbs' => [
                ['label' => 'Events', 'url' => base_url('auth/events')],
                ['label' => 'Bookings', 'url' => base_url('auth/events/bookings')],
                ['label' => 'Booking Details']
            ],
            'bannerActions' => '
                <div class="flex items-center gap-3">
                    <a href="' . base_url('auth/events/bookings') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Back to Bookings
                    </a>
                </div>
            '
        ]) ?>

        <div class="px-6 pb-6">
            <!-- Booking Information Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                    <i data-lucide="receipt" class="w-5 h-5 mr-2 text-primary"></i>
                    Booking Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Booking Number</p>
                        <p class="text-base font-mono font-semibold text-slate-800"><?= esc($booking['booking_number']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Payment Status</p>
                        <?php
                        $paymentBadgeClass = 'bg-gray-100 text-gray-800';
                        if ($booking['payment_status'] === 'paid') $paymentBadgeClass = 'bg-green-100 text-green-800';
                        if ($booking['payment_status'] === 'pending') $paymentBadgeClass = 'bg-yellow-100 text-yellow-800';
                        if ($booking['payment_status'] === 'failed') $paymentBadgeClass = 'bg-red-100 text-red-800';
                        ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium <?= $paymentBadgeClass ?>">
                            <?= ucfirst($booking['payment_status']) ?>
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Booking Status</p>
                        <?php
                        $statusBadgeClass = 'bg-gray-100 text-gray-800';
                        if ($booking['status'] === 'confirmed') $statusBadgeClass = 'bg-green-100 text-green-800';
                        if ($booking['status'] === 'cancelled') $statusBadgeClass = 'bg-red-100 text-red-800';
                        ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusBadgeClass ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Total Amount</p>
                        <p class="text-base font-semibold text-slate-800">KES <?= number_format($booking['total_amount'] ?? 0, 2) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Email</p>
                        <p class="text-base font-medium text-slate-800"><?= esc($booking['email']) ?></p>
                    </div>
                    <?php if (!empty($booking['phone'])): ?>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Phone</p>
                        <p class="text-base font-medium text-slate-800"><?= esc($booking['phone']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($booking['payment_reference'])): ?>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Payment Reference</p>
                        <p class="text-base font-mono font-medium text-slate-800"><?= esc($booking['payment_reference']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Created At</p>
                        <p class="text-base font-medium text-slate-800"><?= date('F j, Y g:i A', strtotime($booking['created_at'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Customer Information Card -->
            <?php if ($user): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                    <i data-lucide="user" class="w-5 h-5 mr-2 text-primary"></i>
                    Customer Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Name</p>
                        <p class="text-base font-semibold text-slate-800"><?= esc($user['full_name']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Email</p>
                        <p class="text-base font-medium text-slate-800"><?= esc($user['email']) ?></p>
                    </div>
                    <?php if (!empty($user['phone'])): ?>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Phone</p>
                        <p class="text-base font-medium text-slate-800"><?= esc($user['phone']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Event Information Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                    <i data-lucide="calendar" class="w-5 h-5 mr-2 text-primary"></i>
                    Event Information
                </h2>
                <div class="flex flex-col md:flex-row gap-6">
                    <?php if (!empty($event['image_url'])): ?>
                        <div class="w-full md:w-48 flex-shrink-0">
                            <img src="<?= $event['image_url'] ?>" 
                                 alt="<?= esc($event['title']) ?>" 
                                 class="w-full h-48 object-cover rounded-lg"
                                 onerror="this.src='<?= base_url('hero.png') ?>'">
                        </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800 mb-4"><?= esc($event['title']) ?></h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-slate-600 mb-1">Date</p>
                                <p class="font-semibold text-slate-800"><?= date('F j, Y', strtotime($event['start_date'])) ?></p>
                            </div>
                            <?php if (!empty($event['start_time'])): ?>
                                <div>
                                    <p class="text-slate-600 mb-1">Time</p>
                                    <p class="font-semibold text-slate-800"><?= date('g:i A', strtotime($event['start_time'])) ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($event['venue'])): ?>
                                <div>
                                    <p class="text-slate-600 mb-1">Venue</p>
                                    <p class="font-semibold text-slate-800"><?= esc($event['venue']) ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($event['address'])): ?>
                                <div>
                                    <p class="text-slate-600 mb-1">Address</p>
                                    <p class="font-semibold text-slate-800"><?= esc($event['address']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                    <i data-lucide="ticket" class="w-5 h-5 mr-2 text-primary"></i>
                    Tickets (<?= count($tickets) ?>)
                </h2>
                <?php if (empty($tickets)): ?>
                    <p class="text-slate-600 text-center py-8">No tickets found for this booking.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($tickets as $index => $ticket): ?>
                            <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                                <i data-lucide="ticket" class="w-5 h-5 text-primary"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-slate-800">Ticket #<?= $index + 1 ?></h3>
                                                <p class="text-sm text-slate-600 font-mono"><?= esc($ticket['ticket_number']) ?></p>
                                            </div>
                                        </div>
                                        <div class="ml-13 space-y-1 text-sm">
                                            <div class="flex items-center gap-2 mb-2">
                                                <p class="text-slate-600">
                                                    <span class="font-medium">Status:</span>
                                                </p>
                                                <?php
                                                $statusBadgeClass = 'bg-gray-100 text-gray-800';
                                                $statusText = 'Active';
                                                if ($ticket['status'] === 'used') {
                                                    $statusBadgeClass = 'bg-blue-100 text-blue-800';
                                                    $statusText = 'Used';
                                                } elseif ($ticket['status'] === 'cancelled') {
                                                    $statusBadgeClass = 'bg-red-100 text-red-800';
                                                    $statusText = 'Cancelled';
                                                }
                                                ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusBadgeClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </div>
                                            <p class="text-slate-600">
                                                <span class="font-medium">Attendee:</span> <?= esc($ticket['attendee_name']) ?>
                                            </p>
                                            <p class="text-slate-600">
                                                <span class="font-medium">Email:</span> <?= esc($ticket['attendee_email']) ?>
                                            </p>
                                            <?php if (!empty($ticket['ticket_type_name'])): ?>
                                                <p class="text-slate-600">
                                                    <span class="font-medium">Type:</span> <?= esc($ticket['ticket_type_name']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($ticket['checked_in_at'])): ?>
                                                <p class="text-green-600">
                                                    <span class="font-medium">Checked In:</span> <?= date('F j, Y g:i A', strtotime($ticket['checked_in_at'])) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <a href="<?= base_url('events/ticket/' . $ticket['id'] . '/download') ?>" 
                                           class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
                                           target="_blank">
                                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                            Download PDF
                                        </a>
                                        <?php if ($ticket['status'] !== 'cancelled'): ?>
                                        <button onclick="invalidateTicket('<?= $ticket['id'] ?>')" 
                                                class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                                                title="Invalidate Ticket">
                                            <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                            Invalidate
                                        </button>
                                        <?php else: ?>
                                        <span class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed">
                                            <i data-lucide="ban" class="w-4 h-4 mr-2"></i>
                                            Invalidated
                                        </span>
                                        <?php endif; ?>
                                        <button onclick="deleteTicket('<?= $ticket['id'] ?>')" 
                                                class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                                                title="Delete Ticket">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        lucide.createIcons();
    });

    /**
     * Invalidate ticket
     */
    function invalidateTicket(ticketId) {
        Swal.fire({
            title: 'Invalidate Ticket?',
            text: 'Are you sure you want to invalidate this ticket? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#eab308',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, invalidate ticket',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Invalidating...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= base_url('auth/events/ticket') ?>/${ticketId}/invalidate`,
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
                                title: 'Invalidated!',
                                text: response.message || 'Ticket invalidated successfully',
                                icon: 'success',
                                confirmButtonColor: '#3b82f6',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                // Reload the page to show updated status
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to invalidate ticket',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        let errorMessage = 'An error occurred while invalidating the ticket';
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
     * Delete ticket
     */
    function deleteTicket(ticketId) {
        Swal.fire({
            title: 'Delete Ticket?',
            html: `
                <div class="text-left">
                    <p class="text-gray-700 mb-3">Are you sure you want to delete this ticket? This action cannot be undone.</p>
                    <div class="bg-red-50 border-l-4 border-red-400 p-3 mb-3">
                        <p class="text-sm text-red-800">
                            <strong>Warning:</strong> This will permanently delete the ticket from the system.
                        </p>
                    </div>
                    <p class="text-sm text-gray-600">Are you sure you want to proceed?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete ticket',
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
                    url: `<?= base_url('auth/events/ticket') ?>/${ticketId}`,
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
                                text: response.message || 'Ticket deleted successfully',
                                icon: 'success',
                                confirmButtonColor: '#3b82f6',
                                timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                // Reload the page to show updated list
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to delete ticket',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        let errorMessage = 'An error occurred while deleting the ticket';
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
<?= $this->endSection() ?>

