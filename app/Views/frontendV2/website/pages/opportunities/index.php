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

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Opportunities</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Discover career opportunities, funding prospects, and partnership possibilities in Kenya's WASH sector
            </p>
        </div>
    </section>

    <!-- Opportunities Categories -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <!-- Job Opportunities -->
                <div class="bg-lightGray p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-primaryShades-800 mb-4">Job Opportunities</h3>
                        <?php 
                            $jobCount = (isset($groupedOpportunities['full-time']) ? count($groupedOpportunities['full-time']) : 0) + 
                                       (isset($groupedOpportunities['part-time']) ? count($groupedOpportunities['part-time']) : 0);
                        ?>
                        <div class="text-3xl font-bold text-primary mb-2"><?= $jobCount ?></div>
                        <p class="text-dark text-center mb-6">
                            Find career opportunities with KEWASNET and partner organizations in the WASH sector
                        </p>
                        <div class="text-center">
                            <a href="<?= site_url('opportunities/explore?type=full-time') ?>" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primaryShades-700 transition-colors inline-block">
                                Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Internship Opportunities -->
                <div class="bg-lightGray p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-secondaryShades-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-primaryShades-800 mb-4">Internship Programs</h3>
                        <?php $internshipCount = isset($groupedOpportunities['internship']) ? count($groupedOpportunities['internship']) : 0; ?>
                        <div class="text-3xl font-bold text-secondaryShades-500 mb-2"><?= $internshipCount ?></div>
                        <p class="text-dark text-center mb-6">
                            Gain hands-on experience in Kenya's WASH sector through our structured internship opportunities
                        </p>
                        <div class="text-center">
                            <a href="<?= site_url('opportunities/explore?type=internship') ?>" class="bg-secondaryShades-500 text-white px-6 py-3 rounded-lg hover:bg-secondaryShades-600 transition-colors inline-block">
                                Browse Internships
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contract & Freelance -->
                <div class="bg-lightGray p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-primaryShades-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-primaryShades-800 mb-4">Contract & Freelance</h3>
                        <?php 
                            $contractCount = (isset($groupedOpportunities['contract']) ? count($groupedOpportunities['contract']) : 0) + 
                                           (isset($groupedOpportunities['freelance']) ? count($groupedOpportunities['freelance']) : 0);
                        ?>
                        <div class="text-3xl font-bold text-primaryShades-700 mb-2"><?= $contractCount ?></div>
                        <p class="text-dark text-center mb-6">
                            Explore contract and freelance opportunities for flexible engagement with our network
                        </p>
                        <div class="text-center">
                            <a href="<?= site_url('opportunities/explore?type=contract') ?>" class="bg-primaryShades-700 text-white px-6 py-3 rounded-lg hover:bg-primaryShades-800 transition-colors inline-block">
                                Browse Contracts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Opportunities -->
    <section id="current-opportunities" class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-primaryShades-800 mb-12">Current Opportunities</h2>
            
            <?php if (isset($opportunities) && !empty($opportunities)): ?>
                <div class="space-y-6">
                    <?php foreach ($opportunities as $opportunity): ?>
                        <?php
                            // Determine border color and badge color based on opportunity type
                            $borderColors = [
                                'full-time' => 'border-primary',
                                'part-time' => 'border-secondary',
                                'contract' => 'border-primaryShades-700',
                                'internship' => 'border-secondaryShades-500',
                                'freelance' => 'border-primaryShades-600'
                            ];
                            
                            $badgeColors = [
                                'full-time' => 'bg-primary',
                                'part-time' => 'bg-secondary',
                                'contract' => 'bg-primaryShades-700',
                                'internship' => 'bg-secondaryShades-500',
                                'freelance' => 'bg-primaryShades-600'
                            ];
                            
                            $borderColor = $borderColors[$opportunity['opportunity_type']] ?? 'border-primary';
                            $badgeColor = $badgeColors[$opportunity['opportunity_type']] ?? 'bg-primary';
                            
                            // Format salary
                            $salaryText = '';
                            if (!empty($opportunity['salary_min']) && !empty($opportunity['salary_max'])) {
                                $currency = $opportunity['salary_currency'] ?? 'KES';
                                $salaryText = $currency . ' ' . number_format($opportunity['salary_min']) . ' - ' . number_format($opportunity['salary_max']);
                            }
                            
                            // Format deadline
                            $deadlineText = '';
                            if (!empty($opportunity['application_deadline'])) {
                                $deadline = new DateTime($opportunity['application_deadline']);
                                $deadlineText = 'Deadline: ' . $deadline->format('M j, Y');
                            }
                            
                            // Format location
                            $locationText = $opportunity['location'] ?? 'Kenya';
                            if ($opportunity['is_remote']) {
                                $locationText .= ' (Remote)';
                            }
                            
                            // Format company
                            $company = $opportunity['company'] ?? 'KEWASNET';
                        ?>
                        
                        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 <?= $borderColor ?> hover:shadow-xl transition-shadow">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0 flex-1">
                                    <h3 class="text-xl font-bold text-primaryShades-800 mb-2">
                                        <a href="<?= site_url('opportunities/' . $opportunity['slug']) ?>" 
                                           class="hover:text-primary transition-colors">
                                            <?= esc($opportunity['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-slate-600 mb-2">
                                        <?= esc($company) ?> • <?= esc($locationText) ?> • <?= ucfirst(str_replace('-', ' ', $opportunity['opportunity_type'])) ?>
                                        <?php if ($salaryText): ?>
                                            • <?= esc($salaryText) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-dark line-clamp-2 mb-3">
                                        <?= esc(substr(strip_tags($opportunity['description']), 0, 150)) ?>
                                        <?php if (strlen(strip_tags($opportunity['description'])) > 150): ?>...<?php endif; ?>
                                    </p>
                                    <a href="<?= site_url('opportunities/' . $opportunity['slug']) ?>" 
                                       class="inline-flex items-center text-primary hover:text-primaryShades-700 font-medium">
                                        View Details & Apply
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="flex flex-col space-y-2 md:items-end">
                                    <span class="<?= $badgeColor ?> text-white px-3 py-1 rounded-full text-sm w-fit">
                                        <?= ucfirst(str_replace('-', ' ', $opportunity['opportunity_type'])) ?>
                                    </span>
                                    <?php if ($deadlineText): ?>
                                        <span class="text-sm text-slate-500"><?= esc($deadlineText) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($opportunity['contract_duration'])): ?>
                                        <span class="text-sm text-slate-500">Duration: <?= esc($opportunity['contract_duration']) ?></span>
                                    <?php endif; ?>
                                    <a href="<?= site_url('opportunities/' . $opportunity['slug']) ?>" 
                                       class="bg-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primaryShades-700 transition-colors inline-flex items-center">
                                        Apply Now
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Call to Action -->
                <div class="text-center mt-12">
                    <a href="<?= site_url('opportunities/explore') ?>" class="bg-primary text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-primaryShades-700 transition-colors inline-flex items-center">
                        Explore More Opportunities
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            <?php else: ?>
                <!-- No opportunities message -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-lightGray rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-primaryShades-800 mb-4">No Current Opportunities</h3>
                    <p class="text-dark mb-6">We don't have any active opportunities at the moment, but new ones are posted regularly.</p>
                    <p class="text-slate-600">Subscribe to our newsletter to be notified when new opportunities become available.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- How to Apply Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-primaryShades-800 mb-12">How to Apply</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-primaryShades-800 mb-4">Review Requirements</h3>
                    <p class="text-dark">Carefully read through the opportunity requirements and eligibility criteria</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-primaryShades-800 mb-4">Prepare Application</h3>
                    <p class="text-dark">Gather all required documents and complete the application form</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-primaryShades-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-primaryShades-800 mb-4">Submit & Follow Up</h3>
                    <p class="text-dark">Submit your application before the deadline and track your progress</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Subscription Section -->
    <?= view('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
    
    // Opportunity card hover effects
    $('.opportunity-card').hover(
        function() {
            $(this).find('.apply-btn').addClass('scale-105');
        },
        function() {
            $(this).find('.apply-btn').removeClass('scale-105');
        }
    );
});
</script>
<?= $this->endSection() ?>