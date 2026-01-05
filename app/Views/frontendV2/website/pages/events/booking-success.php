<?php 
    use App\Helpers\UrlHelper;
    use App\Libraries\ClientAuth;
    
    $currentUrl = new UrlHelper();
    $userId = ClientAuth::getId();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Booking Success Page -->
    <article class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Breadcrumbs -->
            <nav class="mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="<?= base_url() ?>" class="text-slate-600 hover:text-primary transition-colors">Home</a>
                    </li>
                    <li>
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li>
                        <a href="<?= base_url('events') ?>" class="text-slate-600 hover:text-primary transition-colors">Events</a>
                    </li>
                    <li>
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li>
                        <a href="<?= base_url('events/' . esc($event['slug'])) ?>" class="text-slate-600 hover:text-primary transition-colors"><?= esc($event['title']) ?></a>
                    </li>
                    <li>
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li class="text-primary" aria-current="page">
                        <span>Booking Confirmed</span>
                    </li>
                </ol>
            </nav>

            <!-- Success Message -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8 text-center mb-8">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="check-circle" class="w-12 h-12 text-green-600"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-slate-800 mb-2">Booking Confirmed!</h1>
                    <p class="text-slate-600">Your tickets have been reserved successfully</p>
                </div>

                <!-- Booking Details -->
                <div class="bg-slate-50 rounded-lg p-6 mb-6 text-left">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Booking Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Booking Number</p>
                            <p class="text-base font-mono font-semibold text-slate-800"><?= esc($booking['booking_number']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Event</p>
                            <p class="text-base font-semibold text-slate-800"><?= esc($event['title']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Date</p>
                            <p class="text-base font-semibold text-slate-800"><?= date('F j, Y', strtotime($event['start_date'])) ?></p>
                        </div>
                        <?php if (!empty($event['start_time'])): ?>
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Time</p>
                            <p class="text-base font-semibold text-slate-800"><?= date('g:i A', strtotime($event['start_time'])) ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Total Amount</p>
                            <p class="text-base font-semibold text-slate-800">KES <?= number_format($booking['total_amount'], 2) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Payment Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $booking['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                <?= ucfirst($booking['payment_status']) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Email Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i data-lucide="mail" class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                        <div class="text-left">
                            <p class="text-sm font-medium text-blue-900 mb-1">Tickets Sent to Email</p>
                            <p class="text-sm text-blue-700">Your tickets have been sent to <strong><?= esc($booking['email']) ?></strong>. Please check your inbox and spam folder.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?= base_url('events/booking/' . $booking['id'] . '/tickets') ?>" 
                       class="gradient-btn px-8 py-3 rounded-[50px] text-white flex items-center justify-center">
                        <i data-lucide="ticket" class="w-5 h-5 mr-2 z-10"></i>
                        <span>View & Download Tickets</span>
                    </a>
                    <a href="<?= base_url('events') ?>" 
                       class="px-8 py-3 rounded-[50px] border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center">
                        <i data-lucide="calendar" class="w-5 h-5 mr-2"></i>
                        <span>Browse More Events</span>
                    </a>
                </div>

                <!-- Resend Tickets -->
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm text-slate-600 mb-3">Didn't receive your tickets?</p>
                    <button onclick="resendTickets('<?= esc($booking['id']) ?>')" 
                            id="resendBtn"
                            class="text-sm text-primary hover:text-primaryShades-700 font-medium">
                        <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-1"></i>
                        Resend Tickets to Email
                    </button>
                </div>
            </div>

            <!-- Event Summary -->
            <div class="bg-slate-50 rounded-xl p-6">
                <h2 class="text-xl font-bold text-slate-800 mb-4">Event Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    $eventImage = !empty($event['image_url']) ? $event['image_url'] : (!empty($event['banner_url']) ? $event['banner_url'] : base_url('hero.png'));
                    ?>
                    <div class="rounded-lg overflow-hidden">
                        <img src="<?= $eventImage ?>" 
                             alt="<?= esc($event['title']) ?>" 
                             class="w-full h-48 object-cover"
                             onerror="this.src='<?= base_url('hero.png') ?>'">
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-3"><?= esc($event['title']) ?></h3>
                        <div class="space-y-2 text-sm text-slate-600">
                            <?php if (!empty($event['venue'])): ?>
                                <div class="flex items-center">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-primary"></i>
                                    <span><?= esc($event['venue']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($event['address'])): ?>
                                <div class="flex items-center">
                                    <i data-lucide="navigation" class="w-4 h-4 mr-2 text-primary"></i>
                                    <span><?= esc($event['address']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($event['city']) || !empty($event['country'])): ?>
                                <div class="flex items-center">
                                    <i data-lucide="globe" class="w-4 h-4 mr-2 text-primary"></i>
                                    <span><?= esc(trim(($event['city'] ?? '') . ', ' . ($event['country'] ?? ''), ', ')) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
});

function resendTickets(bookingId) {
    const btn = $('#resendBtn');
    const originalHtml = btn.html();
    
    btn.prop('disabled', true);
    btn.html('<i data-lucide="loader" class="w-4 h-4 inline mr-1 animate-spin"></i> Sending...');
    lucide.createIcons();
    
    $.ajax({
        url: '<?= base_url('events/booking') ?>/' + bookingId + '/resend-tickets',
        type: 'POST',
        data: {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.status === 'success') {
                showToast('Tickets resent successfully! Please check your email.', 'success');
            } else {
                showToast(response.message || 'Failed to resend tickets', 'error');
            }
            btn.prop('disabled', false);
            btn.html(originalHtml);
            lucide.createIcons();
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            showToast(response.message || 'An error occurred', 'error');
            btn.prop('disabled', false);
            btn.html(originalHtml);
            lucide.createIcons();
        }
    });
}

function showToast(message, type = 'success') {
    $('.custom-toast').remove();
    let bgColor, iconName;
    switch(type) {
        case 'success': bgColor = 'bg-green-500'; iconName = 'check-circle'; break;
        case 'error': bgColor = 'bg-red-500'; iconName = 'alert-circle'; break;
        case 'warning': bgColor = 'bg-yellow-500'; iconName = 'alert-triangle'; break;
        default: bgColor = 'bg-blue-500'; iconName = 'info';
    }
    const toast = $(`
        <div class="custom-toast fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 ${bgColor} max-w-sm">
            <div class="flex items-center">
                <i data-lucide="${iconName}" class="w-5 h-5 mr-2 flex-shrink-0"></i>
                <span class="text-sm">${message}</span>
            </div>
        </div>
    `);
    $('body').append(toast);
    lucide.createIcons();
    toast.hide().fadeIn(300);
    setTimeout(() => toast.fadeOut(300, () => toast.remove()), type === 'error' ? 5000 : 3000);
}
</script>
<?= $this->endSection() ?>

