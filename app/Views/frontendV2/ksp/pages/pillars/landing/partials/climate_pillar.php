<?php
    $pillarSlugs   = array_column($pillars, 'slug');
    $climatePillar = in_array('climate', $pillarSlugs) ? 'climate' : false;
?>

<!-- Climate Change Document -->
<section class="py-12 md:py-16" id="climate">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Document Header -->
        <header class="text-left mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6">
                Climate Change Pillar
            </h1>
            <p class="text-lg text-slate-700 leading-relaxed mb-8">
                Climate change poses significant challenges to water, sanitation, and hygiene (WASH) services, 
                exacerbating water scarcity, flooding, and ecosystem degradation. Rising temperatures and changing 
                precipitation patterns are altering the timing and amount of water available for human use, 
                agriculture, and ecosystems.
            </p>
            <div class="flex justify-center">
                <a href="<?= base_url("ksp/pillar-articles/". $climatePillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
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
                    <i class="fas fa-globe mr-3 text-2xl"></i>
                    Overview
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Climate change affects water availability and quality, increases the frequency of extreme weather events, 
                        and disrupts WASH infrastructure. Effective climate governance requires integrating climate resilience 
                        into water management, engaging diverse stakeholders, and leveraging data-driven approaches to mitigate 
                        risks and adapt to changing conditions.
                    </p>
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        At KEWASNET, we work to strengthen the resilience of WASH systems through capacity building, policy advocacy, 
                        and knowledge sharing to ensure sustainable water and sanitation services for all Kenyans, even in the face 
                        of climate challenges.
                    </p>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 1. Climate Resilience Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-leaf mr-3 text-2xl"></i>
                    1. Climate Resilience
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Building resilient WASH systems that can withstand and adapt to climate variability and change through innovative approaches and adaptive management strategies.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Key Strategies</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Developing adaptive water management strategies</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Protecting ecosystems from climate-induced degradation</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Implementing infrastructure resilient to floods and droughts</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Adaptation Technologies</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Mitigation Technologies</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 2. Mitigation Strategies Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-cloud-rain mr-3 text-2xl"></i>
                    2. Mitigation Strategies
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Reducing greenhouse gas emissions from WASH systems while promoting sustainable practices that contribute to global climate action goals.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Key Approaches</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Reducing greenhouse gas emissions from WASH systems</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Promoting renewable energy in water treatment</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Encouraging sustainable water use practices</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 3. Community Adaptation & Water Management Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-users mr-3 text-2xl"></i>
                    3. Community Adaptation & Water Management
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Empowering communities to build local resilience through education, participation, and community-led initiatives that enhance adaptive capacity.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Community Initiatives</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Empowering local communities with climate education</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Supporting community-led water conservation initiatives</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Facilitating stakeholder dialogues on climate action</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Improving Water Use Efficiency</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Promoting Water Conservation</span>
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
                        Key national policies and legal frameworks guiding climate action in Kenya's WASH sector provide the foundation for coordinated climate response.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">National Policies</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>National Climate Change Response Strategy (NCCRS)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>National Adaptation Plan (NAP) 2015-2030</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Knowledge products: Climate policy briefs</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Legal Frameworks</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Climate Change Act 2016</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Environmental Management and Coordination Act (EMCA) 1999</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Knowledge products: Citizen guides to climate laws</span>
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
                        Practical resources to support climate resilience in WASH programming, providing stakeholders with the tools needed for effective climate action.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Assessment Tools</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Climate Risk Assessment Toolkit</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Vulnerability Mapping Tools</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Reporting Mechanisms</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>National Climate Change Action Plan (NCCAP) Reports</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>CSO Climate Impact Assessments</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Climate Financing</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Adaptation Finance</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Financing Loss and Damage</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Conclusion -->
            <section class="mt-16 p-8 bg-primary/5 rounded-lg border-l-4 border-primary bg-white">
                <h3 class="text-2xl font-bold text-primary mb-4">Climate Action for WASH</h3>
                <p class="text-slate-700 text-lg leading-relaxed mb-6">
                    KEWASNET is committed to addressing climate change impacts on WASH systems through comprehensive strategies that build resilience, reduce emissions, and empower communities. Our work focuses on integrating climate considerations into all aspects of water and sanitation programming to ensure sustainable services for all Kenyans.
                </p>
                <div class="flex justify-center">
                    <a href="<?= base_url("ksp/pillar-articles/". $climatePillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Explore Climate Resources
                    </a>
                </div>
            </section>

        </article>
    </div>
</section>
