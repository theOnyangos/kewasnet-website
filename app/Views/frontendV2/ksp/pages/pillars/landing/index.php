<?php 
 use App\Helpers\UrlHelper;
 $currentUrl = new UrlHelper();
 $activeView = $activeView ?? 'all'; // Default to show all pillars

 session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);

 // Sort pillars by title for consistent ordering (or use a sort_order field if available)
 $orderedPillars = $pillars;
 usort($orderedPillars, function($a, $b) {
 return strcmp($a['title'] ?? '', $b['title'] ?? '');
 });
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!-- Section Title Block -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!-- Section Content Block -->
<?= $this->section('content') ?>
 <!-- Pillar Hero Section -->
 <section class="relative gradient-bg text-white pt-24 pb-32 overflow-hidden">
 <div class="absolute top-0 left-0 w-full h-full opacity-10 z-10">
 <div class="absolute top-1/4 left-1/4 w-64 h-64 rounded-full bg-white opacity-20"></div>
 <div class="absolute bottom-0 right-0 w-48 h-48 rounded-full bg-white opacity-10"></div>
 </div>

 <div class="container mx-auto px-4 text-center text-white">
 <h1 class="text-4xl md:text-5xl font-bold mb-6">Our Strategic Pillars</h1>
 <p class="text-xl max-w-3xl mx-auto leading-relaxed">
 Explore the foundational pillars that guide KEWASNET's work in transforming Kenya's water, sanitation, and hygiene sector.
 </p>
 </div>

 <div class="water-wave">
 <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
 <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
 </svg>
 </div>
 </section>

 <!-- Pillar Navigation -->
 <section class="sticky top-[90px] z-40 bg-white shadow-sm">
 <div class="container mx-auto px-4">
 <div class="flex flex-wrap justify-center gap-2 md:gap-4 py-4">
 <!-- All Pillars Link -->
 <a href="<?= base_url('ksp/pillars') ?>" class="pillar-nav-link <?= ($activeView === 'all') ? 'pillar_active' : '' ?> group" data-pillar="all">
 <div class="pillar-nav-icon">
 <i data-lucide="layout-grid" class="w-5 h-5"></i>
 </div>
 <span class="pillar-nav-text">All Pillars</span>
 <div class="pillar-nav-indicator"></div>
 </a>

 <?php foreach ($orderedPillars as $pillar): ?>
 <!-- Individual Pillar Link -->
 <a href="<?= base_url('ksp/pillars/' . esc($pillar['slug'])) ?>" class="pillar-nav-link <?= ($activeView === $pillar['slug']) ? 'pillar_active' : '' ?> group" data-pillar="<?= esc($pillar['slug']) ?>">
 <div class="pillar-nav-icon">
 <i data-lucide="<?= esc($pillar['icon']) ?>" class="w-5 h-5"></i>
 </div>
 <span class="pillar-nav-text"><?= esc($pillar['title']) ?></span>
 <div class="pillar-nav-indicator"></div>
 </a>
 <?php endforeach; ?>
 </div>
 </div>
 </section>

 <style>
 .events-page-pattern-bg {
 position: absolute;
 top: 0;
 left: 0;
 right: 0;
 bottom: 0;
 z-index: -1;
 pointer-events: none;
 background-image: 
 repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px),
 repeating-linear-gradient(-45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px);
 background-size: 40px 40px;
 }
 </style>

 <!-- Pillar Content Sections -->
 <section class="pb-16 bg-lightGray py-8">
 <!-- Diagonal Grid Pattern -->
 <div class="events-page-pattern-bg"></div>

 <div class="px-4">
 <?php if ($activeView === 'all'): ?>
 <!-- All Pillars Overview -->
 <div class="container mx-auto pt-16">
 <div class="text-center mb-12">
 <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Strategic Pillars</h2>
 <p class="text-lg text-gray-600 max-w-3xl mx-auto">
 KEWASNET's work is organized around strategic pillars that guide our approach to water, sanitation, and hygiene.
 </p>
 </div>
 
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
 <?php foreach ($orderedPillars as $pillar): ?>
 <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group z-10">
 <div class="p-6">
 <div class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
 <i data-lucide="<?= esc($pillar['icon']) ?>" class="w-8 h-8 text-white"></i>
 </div>
 <h3 class="text-xl font-bold text-gray-900 mb-3"><?= esc($pillar['title']) ?></h3>
 <p class="text-gray-600 mb-4 line-clamp-3"><?= esc($pillar['description']) ?></p>
 <a href="<?= base_url('ksp/pillars/' . esc($pillar['slug'])) ?>" 
 class="inline-flex items-center text-primary hover:text-secondary font-medium transition-colors">
 Learn More 
 <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
 </a>
 </div>
 </div>
 <?php endforeach; ?>
 </div>
 </div>
 <?php else: ?>
 <!-- Individual Pillar Content - Dynamic for all pillars -->
 <?php if (isset($activePillar) && !empty($activePillar)): ?>
 <div id="<?= esc($activePillar['slug']) ?>" class="pillar-section pillar_active bg-white rounded-xl shadow-lg max-w-5xl mx-auto">
 <?= $this->include('frontendV2/ksp/pages/pillars/landing/partials/pillar_content') ?>
 </div>
 <?php else: ?>
 <div class="container mx-auto pt-16 text-center">
 <div class="bg-white rounded-xl shadow-lg p-12 max-w-2xl mx-auto">
 <i data-lucide="alert-circle" class="w-16 h-16 text-slate-400 mx-auto mb-4"></i>
 <h2 class="text-2xl font-bold text-gray-900 mb-4">Pillar Not Found</h2>
 <p class="text-gray-600 mb-6">The requested pillar could not be found.</p>
 <a href="<?= base_url('ksp/pillars') ?>" class="inline-flex items-center text-primary hover:text-secondary font-medium">
 <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
 Back to All Pillars
 </a>
 </div>
 </div>
 <?php endif; ?>
 <?php endif; ?>
 </div>
 </section>
<?= $this->endSection() ?>

<!-- Section Scripts Block -->
<?= $this->section('scripts') ?>
<script>
 // Initialize Lucide icons
 lucide.createIcons();
 
 // Add smooth hover animations for pillar cards
 document.addEventListener('DOMContentLoaded', () => {
 const pillarCards = document.querySelectorAll('.group');
 
 pillarCards.forEach(card => {
 card.addEventListener('mouseenter', function() {
 // Add hover animation with GSAP if available
 if (typeof gsap !== 'undefined') {
 gsap.to(card, { y: -5, duration: 0.3, ease: "back.out(1.5)" });
 }
 });
 
 card.addEventListener('mouseleave', function() {
 // Remove hover animation with GSAP if available
 if (typeof gsap !== 'undefined') {
 gsap.to(card, { y: 0, duration: 0.3, ease: "back.out(1.5)" });
 }
 });
 });
 });

 // Add GSAP animation for the active navigation state
 document.addEventListener('DOMContentLoaded', () => {
 const activeLink = document.querySelector('.pillar-nav-link.pillar_active');
 
 if (activeLink && typeof gsap !== 'undefined') {
 // Animate the active state
 gsap.fromTo(activeLink.querySelector('.pillar-nav-icon'), 
 { y: 0 },
 { y: -3, duration: 0.3, ease: "back.out(1.5)" }
 );
 }
 });
</script>
<?= $this->endSection() ?>