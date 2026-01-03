<?php
    $pillarSlugs   = array_column($pillars, 'slug');
    $nexusPillar = in_array('nexus', $pillarSlugs) ? 'nexus' : false;
?>

<!-- NEXUS Document -->
<section class="py-12 md:py-16" id="nexus">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Document Header -->
        <header class="text-left mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6">
                NEXUS Pillar
            </h1>
            <p class="text-lg text-slate-700 leading-relaxed mb-8">
                The NEXUS approach integrates water, food security, and multi-stakeholder collaboration to drive sustainable solutions in Kenya's WASH sector, promoting synergies and minimizing negative impacts.
            </p>
            <div class="flex justify-center">
                <a href="<?= base_url('ksp/pillar-articles/' . $nexusPillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View Articles
                </a>
            </div>
        </header>

        <!-- Document Content -->
        <article class="prose prose-lg max-w-none">
            
            <!-- Overview Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-link mr-3 text-2xl"></i>
                    Overview
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        The NEXUS approach recognizes the interconnections between water access, food security, and coordinated stakeholder efforts. KEWASNET leverages this framework to foster innovative, cross-sectoral solutions for sustainable development.
                    </p>
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        By promoting integrated approaches that address multiple sectors simultaneously, the NEXUS pillar ensures that interventions in one area support and enhance outcomes in others, creating synergies that maximize impact and sustainability.
                    </p>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 1. Multi-stakeholder Partnerships Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-handshake mr-3 text-2xl"></i>
                    1. Multi-stakeholder Partnerships
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Building effective partnerships across sectors requires strong frameworks for collaboration, shared accountability, and coordinated action towards common goals.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Key Partnership Areas</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Sector financing for collaborative projects</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Mobilizing stakeholders for effective engagement</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Legal and regulatory frameworks for partnerships</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Risk allocation and management in partnerships</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 2. Water Governance Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-balance-scale mr-3 text-2xl"></i>
                    2. Water Governance
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Strengthening governance systems ensures effective coordination, accountability, and sustainable management of water resources across all sectors and stakeholders.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Governance Focus Areas</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Strengthening policy and governance frameworks</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Promoting mutual accountability mechanisms</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Enhancing stakeholder participation</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 3. Integrated Water Management Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-water mr-3 text-2xl"></i>
                    3. Integrated Water Management
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Promoting integrated approaches to water management that consider multiple sectors, uses, and stakeholder needs to optimize resource allocation and service delivery.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Integration Strategies</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Cross-sectoral partnerships for water allocation</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Public-private partnerships for service delivery</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Innovative technologies for water management</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 4. Policies and Frameworks Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-gavel mr-3 text-2xl"></i>
                    4. Policies and Frameworks
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Developing and implementing comprehensive policy frameworks that support integrated approaches and facilitate effective partnerships across sectors.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Governance Frameworks</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Policy frameworks for water and food security</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Mutual accountability frameworks for stakeholders</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Collaboration frameworks at provincial levels</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Partnership Models</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Public-private partnership guidelines</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Risk management protocols for collaborations</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Monitoring and evaluation frameworks</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 5. Tools and Resources Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-toolbox mr-3 text-2xl"></i>
                    5. Tools and Resources
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Practical tools and resources to support integrated approaches, partnership development, and cross-sectoral collaboration in WASH programming.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Economic Growth Tools</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Water use efficiency tools for agriculture</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Industrial water management guidelines</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Partnership Resources</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Stakeholder engagement toolkits</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Best practices for cross-sectoral collaboration</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Gender Integration</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Gender-sensitive water policy guides</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Tools for addressing water-related inequalities</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Conclusion -->
            <section class="mt-16 p-8 bg-primary/5 rounded-lg border-l-4 border-primary bg-white">
                <h3 class="text-2xl font-bold text-primary mb-4">Integrated Solutions for Sustainable Impact</h3>
                <p class="text-slate-700 text-lg leading-relaxed mb-6">
                    The NEXUS approach enables KEWASNET to address complex water challenges through integrated solutions that consider interconnections between water, food security, and multi-stakeholder collaboration. By fostering partnerships and promoting cross-sectoral coordination, we work towards sustainable development outcomes that benefit all stakeholders.
                </p>
                <div class="flex justify-center">
                    <a href="<?= base_url('ksp/pillar-articles/' . $nexusPillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Explore NEXUS Resources
                    </a>
                </div>
            </section>

        </article>
    </div>
</section>
