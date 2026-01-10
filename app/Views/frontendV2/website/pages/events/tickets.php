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
    <!-- Tickets Page -->
    <article class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <!-- Breadcrumbs -->
            <nav class="mb-8" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                    <li>
                        <a href="<?= base_url() ?>" class="text-slate-600 hover:text-primary transition-colors whitespace-nowrap">Home</a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li>
                        <a href="<?= base_url('events') ?>" class="text-slate-600 hover:text-primary transition-colors whitespace-nowrap">Events</a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li class="min-w-0 flex-1 sm:flex-initial">
                        <a href="<?= base_url('events/' . esc($event['slug'])) ?>" class="text-slate-600 hover:text-primary transition-colors truncate block" title="<?= esc($event['title']) ?>"><?= esc($event['title']) ?></a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li class="text-primary flex-shrink-0" aria-current="page">
                        <span class="whitespace-nowrap">My Tickets</span>
                    </li>
                </ol>
            </nav>

            <!-- Booking Header -->
            <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Your Tickets</h1>
                        <p class="text-white/90">Booking Number: <span class="font-mono font-semibold"><?= esc($booking['booking_number']) ?></span></p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="<?= base_url('events/booking/' . $booking['id'] . '/success') ?>" 
                           class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                            Back to Booking
                        </a>
                    </div>
                </div>
            </div>

            <!-- Event Info Card -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-8">
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
                        <h2 class="text-xl font-bold text-slate-800 mb-3"><?= esc($event['title']) ?></h2>
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
                            <div>
                                <p class="text-slate-600 mb-1">Total Tickets</p>
                                <p class="font-semibold text-slate-800"><?= count($tickets) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 mb-4">Ticket Details</h2>
                <?php foreach ($tickets as $index => $ticket): ?>
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <i data-lucide="ticket" class="w-5 h-5 text-primary"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-slate-800">Ticket #<?= $index + 1 ?></h3>
                                        <p class="text-sm text-slate-600"><?= esc($ticket['ticket_number']) ?></p>
                                    </div>
                                </div>
                                <div class="ml-13 space-y-1 text-sm">
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
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="<?= base_url('events/ticket/' . $ticket['id'] . '/download') ?>" 
                                   class="gradient-btn px-6 py-2 rounded-lg text-white flex items-center justify-center">
                                    <i data-lucide="download" class="w-4 h-4 mr-2 z-10"></i>
                                    <span>Download PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Download All Button -->
            <?php if (count($tickets) > 1): ?>
                <div class="mt-8 text-center">
                    <p class="text-sm text-slate-600 mb-4">Download all tickets individually or contact support for a combined PDF</p>
                </div>
            <?php endif; ?>

            <!-- Important Information -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                    Important Information
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>Please bring a printed copy of your ticket or have it ready on your mobile device at the event.</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>Each ticket has a unique QR code that will be scanned at the venue.</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>If you have any questions, please contact us at the email address provided in your booking confirmation.</span>
                    </li>
                </ul>
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
</script>
<?= $this->endSection() ?>

