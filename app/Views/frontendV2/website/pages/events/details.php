<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  SEO Meta Tags  -->
<?= $this->section('seo_title'); ?>
<?= esc($event['title']) ?>
<?= $this->endSection(); ?>

<?= $this->section('description'); ?>
<?= substr(strip_tags($event['description'] ?? ''), 0, 155) ?>
<?= $this->endSection(); ?>

<?= $this->section('image_url'); ?>
<?= !empty($event['banner_url']) ? $event['banner_url'] : (!empty($event['image_url']) ? $event['image_url'] : base_url('hero.png')) ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Event Content -->
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
                    <li class="text-primary" aria-current="page">
                        <span><?= esc($event['title']) ?></span>
                    </li>
                </ol>
            </nav>

            <!-- Event Header -->
            <header class="mb-12">
                <?php if (!empty($event['banner_url']) || !empty($event['image_url'])): ?>
                    <div class="relative h-64 md:h-96 rounded-xl overflow-hidden mb-8">
                        <img src="<?= !empty($event['banner_url']) ? $event['banner_url'] : $event['image_url'] ?>" 
                             alt="<?= esc($event['title']) ?>" 
                             class="w-full h-full object-contain bg-secondary/10 rounded-xl"
                             onerror="this.src='<?= base_url('hero.png') ?>'">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <span class="inline-block bg-secondary text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                                <?= esc($event['event_type'] === 'paid' ? 'Paid Event' : 'Free Event') ?>
                            </span>
                            <h1 class="text-3xl md:text-4xl font-bold mb-2"><?= esc($event['title']) ?></h1>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mb-8">
                        <span class="inline-block bg-secondary text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                            <?= esc($event['event_type'] === 'paid' ? 'Paid Event' : 'Free Event') ?>
                        </span>
                        <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4"><?= esc($event['title']) ?></h1>
                    </div>
                <?php endif; ?>

                <!-- Event Meta Information -->
                <div class="flex flex-wrap items-center gap-6 text-slate-600 mb-6">
                    <div class="flex items-center">
                        <i data-lucide="calendar" class="icon-sm mr-2 text-primary"></i>
                        <span class="font-medium"><?= date('F j, Y', strtotime($event['start_date'])) ?></span>
                        <?php if (!empty($event['end_date']) && $event['end_date'] !== $event['start_date']): ?>
                            <span class="mx-2">-</span>
                            <span><?= date('F j, Y', strtotime($event['end_date'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($event['start_time'])): ?>
                        <div class="flex items-center">
                            <i data-lucide="clock" class="icon-sm mr-2 text-primary"></i>
                            <span><?= date('g:i A', strtotime($event['start_time'])) ?></span>
                            <?php if (!empty($event['end_time'])): ?>
                                <span class="mx-2">-</span>
                                <span><?= date('g:i A', strtotime($event['end_time'])) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($event['venue'])): ?>
                        <div class="flex items-center">
                            <i data-lucide="map-pin" class="icon-sm mr-2 text-primary"></i>
                            <span><?= esc($event['venue']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Location Details -->
                <?php if (!empty($event['address']) || !empty($event['city']) || !empty($event['country'])): ?>
                    <div class="bg-slate-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-slate-700 mb-2">Location</h3>
                        <p class="text-slate-600">
                            <?php if (!empty($event['address'])): ?>
                                <?= esc($event['address']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($event['city'])): ?>
                                <?= esc($event['city']) ?><?= !empty($event['country']) ? ', ' . esc($event['country']) : '' ?>
                            <?php elseif (!empty($event['country'])): ?>
                                <?= esc($event['country']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </header>

            <!-- Event Description -->
            <div class="prose max-w-none mb-12">
                <div class="text-slate-700 leading-relaxed">
                    <?= $event['description'] ?? 'No description available.' ?>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-8 text-white text-center mb-12">
                <h2 class="text-2xl font-bold mb-4">Ready to Join Us?</h2>
                <p class="text-lg mb-6 text-white/90">
                    <?php if ($event['event_type'] === 'paid'): ?>
                        Book your ticket now to secure your spot at this event.
                    <?php else: ?>
                        Register now to secure your spot at this free event.
                    <?php endif; ?>
                </p>
                <a href="<?= base_url('events/' . esc($event['slug']) . '/book') ?>" 
                   class="inline-flex items-center px-8 py-4 bg-white text-primary rounded-[50px] font-semibold hover:bg-slate-100 transition-colors">
                    <?php if ($event['event_type'] === 'paid'): ?>
                        Book Tickets
                    <?php else: ?>
                        Register Now
                    <?php endif; ?>
                    <i data-lucide="arrow-right" class="ml-2 icon"></i>
                </a>
            </div>

            <!-- Event Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <?php if (!empty($event['total_capacity'])): ?>
                    <div class="bg-slate-50 rounded-lg p-6">
                        <div class="flex items-center mb-2">
                            <i data-lucide="users" class="icon-lg text-primary mr-3"></i>
                            <h3 class="text-lg font-semibold text-slate-800">Capacity</h3>
                        </div>
                        <p class="text-slate-600"><?= number_format($event['total_capacity']) ?> attendees</p>
                    </div>
                <?php endif; ?>
                
                <div class="bg-slate-50 rounded-lg p-6">
                    <div class="flex items-center mb-2">
                        <i data-lucide="tag" class="icon-lg text-primary mr-3"></i>
                        <h3 class="text-lg font-semibold text-slate-800">Event Type</h3>
                    </div>
                    <p class="text-slate-600"><?= esc(ucfirst($event['event_type'])) ?> Event</p>
                </div>
            </div>
        </div>
    </article>

    <!-- Newsletter Signup -->
    <?= $this->include('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>

