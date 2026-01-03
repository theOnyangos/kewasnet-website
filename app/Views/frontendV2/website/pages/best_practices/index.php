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
                <h1 class="text-4xl md:text-6xl font-bold text-primary mb-6">Best Practices</h1>
                <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                    KEWASNET's comprehensive framework to enhance capacity building, policy advocacy, CSO coordination, and institutional sustainability in Kenya's WASH sector.
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
                    Discover how KEWASNET's Best Practices empower communities, strengthen partnerships, and ensure long-term impact in Water, Sanitation, and Hygiene (WASH) and Water Resource Management (WRM) across Kenya. Our approach combines innovative strategies with practical implementation for sustainable solutions.
                </p>
            </div>
        </div>
    </section>

    <!-- Capacity Building Section -->
    <section class="magazine-section">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Capacity Building</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Empowering individuals and organizations with the skills and knowledge needed for effective WASH/WRM implementation through targeted training and resource sharing.
                </p>
            </div>

            <div class="magazine-grid">
                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="users" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Training Workshops</h3>
                    <p class="text-gray-600 mb-4">
                        Conduct comprehensive workshops on WASH matters, targeting local CSOs and government officials to build essential skills and knowledge.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Hands-on training sessions for practical skill development</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Specialized curriculum for different stakeholder groups</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Ongoing support and follow-up sessions</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="book-open" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Knowledge Sharing</h3>
                    <p class="text-gray-600 mb-4">
                        Maintain an online Resource Centre with toolkits, case studies, and manuals, updated monthly to reflect the latest WASH/WRM innovations.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Comprehensive digital library accessible to all partners</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular webinars showcasing best practices</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Interactive platforms for community knowledge exchange</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="graduation-cap" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Mentorship Programs</h3>
                    <p class="text-gray-600 mb-4">
                        Forge partnerships that foster mentorship opportunities promoting WASH Advocacy and professional development across the sector.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>One-on-one pairing of experienced and emerging professionals</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Structured programs with clear learning objectives</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Networking events to connect mentors and mentees</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#LinkToKSP" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <span>Explore Capacity Building</span>
                    <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                </a>
            </div>
        </div>
    </section>

    <div class="magazine-divider"></div>

    <!-- Policy Advocacy Section -->
    <section class="magazine-section">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Policy Advocacy</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Shaping sustainable WASH/WRM policies through evidence-based advocacy and strategic stakeholder engagement at national and county levels.
                </p>
            </div>

            <div class="magazine-highlight mb-12">
                <p class="magazine-pullquote">
                    "Effective policy advocacy transforms community needs into actionable government strategies, creating lasting change in the WASH sector."
                </p>
            </div>

            <div class="magazine-grid">
                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="bar-chart" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Data-Driven Advocacy</h3>
                    <p class="text-gray-600 mb-4">
                        Compile and present annual CSO reports to influence policy reforms in the WASH sector with evidence-based recommendations.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Comprehensive data collection from diverse sources</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Visualization tools to communicate complex information</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Strategic dissemination to key decision-makers</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="handshake" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Stakeholder Collaboration</h3>
                    <p class="text-gray-600 mb-4">
                        Organize forums with government officials and sector stakeholders to align policies with community needs and priorities.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular multi-stakeholder dialogue platforms</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Consensus-building workshops</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Joint action planning sessions</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="file-text" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Policy Briefs</h3>
                    <p class="text-gray-600 mb-4">
                        Publish and share impact reports to be distributed to sector stakeholders, highlighting key findings and recommendations.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Concise, actionable policy recommendations</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Evidence-based analysis of current challenges</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Strategic distribution to influence policy decisions</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#LinkToPolicyBriefs" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <span>Explore Policy Resources</span>
                    <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                </a>
            </div>
        </div>
    </section>

    <div class="magazine-divider"></div>

    <!-- CSO Coordination Section -->
    <section class="magazine-section">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">CSO Coordination</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Fostering collaboration among Civil Society Organizations to amplify impact and ensure cohesive sector engagement across Kenya.
                </p>
            </div>

            <div class="magazine-grid">
                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="map-pin" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Regional Hubs</h3>
                    <p class="text-gray-600 mb-4">
                        Establish regional hubs to coordinate CSO activities, hosting regular meetings with organizations per region to align efforts.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Decentralized coordination for better regional coverage</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular regional assemblies and networking events</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Localized response to region-specific challenges</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="users" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Thematic Working Groups</h3>
                    <p class="text-gray-600 mb-4">
                        Form TWGs (Water Access, Sanitation, Hygiene) to analyze sector trends and develop detailed action plans for coordinated response.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Specialized focus on key WASH components</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Expert-led analysis and recommendation development</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Cross-organizational collaboration on shared challenges</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="megaphone" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Joint Campaigns</h3>
                    <p class="text-gray-600 mb-4">
                        Launch annual joint campaigns with sector partners to raise awareness, mobilize resources, and drive collective action on WASH issues.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Unified messaging across multiple organizations</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Coordinated outreach and advocacy efforts</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Shared resources for greater impact</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <span>Explore CSO Coordination</span>
                    <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                </a>
            </div>
        </div>
    </section>

    <div class="magazine-divider"></div>

    <!-- Institutional Sustainability Section -->
    <section class="magazine-section">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Institutional Sustainability</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Ensuring KEWASNET's long-term impact through transparent operations, diverse funding, and robust governance structures.
                </p>
            </div>

            <div class="magazine-highlight mb-12">
                <p class="magazine-pullquote">
                    "Sustainable institutions create lasting impact. Our commitment to transparency and good governance ensures we can continue serving Kenya's WASH sector for years to come."
                </p>
            </div>

            <div class="magazine-grid">
                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="shield" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Transparent Reporting</h3>
                    <p class="text-gray-600 mb-4">
                        Publish annual financial and impact reports online, audited by independent bodies, and shared with stakeholders to maintain accountability.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Comprehensive disclosure of financial operations</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular impact assessments and reporting</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Accessible formats for diverse stakeholders</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="dollar-sign" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Diverse Funding</h3>
                    <p class="text-gray-600 mb-4">
                        Secure funding from international donors, local grants, and membership fees, with a diversified portfolio reviewed annually for sustainability.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Multiple revenue streams to ensure financial stability</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Strategic partnerships with diverse funders</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular financial health assessments</span>
                        </li>
                    </ul>
                </div>

                <div class="magazine-feature p-6 rounded-lg">
                    <div class="magazine-icon">
                        <i data-lucide="settings" class="w-8 h-8"></i>
                    </div>
                    <h3 class="magazine-title text-xl font-semibold mb-4">Governance Framework</h3>
                    <p class="text-gray-600 mb-4">
                        Implement a governance board with regular reviews, ensuring compliance with national regulations and incorporating stakeholder input.
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Clear governance structures and processes</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Regular evaluation and improvement of governance practices</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Stakeholder representation in decision-making</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#LinkToResearchReports" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <span>Explore Sustainability</span>
                    <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                </a>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<?= $this->include('frontendV2/website/pages/best_practices/partials/styles') ?>
    
<?= $this->endSection() ?>