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
    <!-- Magazine Header Section -->
    <section class="py-16 bg-gradient-to-b from-primary/5 to-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6">Policy Briefs</h1>
                <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                    Evidence-based insights and recommendations to shape water, sanitation, and hygiene (WASH) policies in Kenya.
                </p>
                <div class="flex justify-center">
                    <div class="w-24 h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <p class="text-lg text-gray-700 text-center leading-relaxed">
                    KEWASNET's Policy Briefs provide evidence-based insights and recommendations to shape water, sanitation, and hygiene (WASH) policies in Kenya. Designed to inform policymakers, stakeholders, and communities, our briefs address critical challenges in water governance, sustainable service delivery, and integrity, ensuring equitable access to WASH services for all.
                </p>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="py-8 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-semibold text-primary">Browse by Category</h2>
            </div>
            <div class="magazine-filter">
                <button class="magazine-filter-btn active" data-filter="all">All Briefs</button>
                <button class="magazine-filter-btn" data-filter="governance">Water Governance</button>
                <button class="magazine-filter-btn" data-filter="sustainable">Sustainable WASH</button>
                <button class="magazine-filter-btn" data-filter="integrity">Integrity & Anti-Corruption</button>
            </div>
        </div>
    </section>

    <!-- Water Governance Section -->
    <section class="magazine-section brief-section" data-category="governance">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Water Governance</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Strengthening water governance through transparent policies, stakeholder engagement, and accountability mechanisms to ensure equitable water resource management.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="scale" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Enhancing Accountability in Water Governance</h3>
                    <p class="text-gray-600 mb-4">
                        This brief examines gaps in Kenya's water governance frameworks, highlighting issues such as weak regulatory oversight and limited community involvement.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Establishing clear accountability mechanisms</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Strengthening county-level water boards</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Promoting transparent reporting</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="users" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Community-Led Water Policy Reforms</h3>
                    <p class="text-gray-600 mb-4">
                        This brief advocates for inclusive policy-making by integrating community feedback into national WASH strategies.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Participatory forums for community engagement</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Community representation in policy dialogues</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Capacity building for local leaders</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div class="magazine-divider"></div>

    <!-- Sustainable WASH Services Section -->
    <section class="magazine-section brief-section" data-category="sustainable">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Sustainable WASH Services</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Promoting sustainable WASH service delivery through innovative financing, infrastructure development, and climate-resilient solutions.
                </p>
            </div>

            <div class="magazine-highlight mb-12">
                <p class="magazine-pullquote">
                    "Sustainable WASH services require innovative financing, climate resilience, and community engagement to ensure long-term impact."
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="credit-card" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Public Financial Management for WASH</h3>
                    <p class="text-gray-600 mb-4">
                        This brief outlines strategies to optimize public funding for WASH services, addressing challenges like high non-revenue water.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Performance-based budgeting approaches</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Public-private partnerships for efficiency</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular financial audits for transparency</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="cloud-rain" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Climate Finance for WASH Resilience</h3>
                    <p class="text-gray-600 mb-4">
                        This brief recommends integrating climate finance into WASH projects to address water scarcity exacerbated by climate change.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Leveraging international climate funds</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Promoting water-efficient technologies</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Building resilient infrastructure</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div class="magazine-divider"></div>

    <!-- Integrity and Anti-Corruption Section -->
    <section class="magazine-section brief-section" data-category="integrity">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Integrity & Anti-Corruption</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Addressing corruption and integrity challenges, such as sextortion, in the WASH sector to ensure equitable access and resource allocation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="shield" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Combating Sextortion in WASH</h3>
                    <p class="text-gray-600 mb-4">
                        This brief highlights the impact of sextortion in water access, where vulnerable populations, especially women, face exploitation.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Legislative reforms to address exploitation</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Community awareness campaigns</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Whistleblower protection mechanisms</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="handshake" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Promoting Integrity in Water Utilities</h3>
                    <p class="text-gray-600 mb-4">
                        This brief advocates for adopting the Integrity Management Toolbox (IMT-SWSS) by small water utilities.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Transparent procurement processes</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular audits for accountability</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Stakeholder engagement for trust building</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<?= $this->include('frontendV2/website/pages/policy_briefs/partials/styles') ?>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Filter and animation functionality
        document.addEventListener('DOMContentLoaded', () => {
            gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

            const header = document.querySelector('header');
            const backToTopButton = document.getElementById('back-to-top');
            const whatsappButton = document.getElementById('whatsapp-chat');
            const backToMainButtons = document.querySelectorAll('.back-to-main-btn');
            const filterButtons = document.querySelectorAll('.magazine-filter-btn');
            const briefSections = document.querySelectorAll('.brief-section');

            // Ensure back-to-main buttons are visible
            backToMainButtons.forEach((button) => {
                if (button) {
                    button.style.display = button.closest('#mobile-menu') ? 'block' : 'inline-flex';
                    button.style.visibility = 'visible';
                    button.style.opacity = '1';
                    button.style.position = 'relative';
                    button.style.zIndex = '1000';
                }
            });

            backToTopButton.addEventListener('click', (e) => {
                e.preventDefault();
                gsap.to(window, { scrollTo: 0, duration: 0.8, ease: "power2.inOut" });
            });
        });
    </script>
<?= $this->endSection() ?>