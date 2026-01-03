<?php
    $pillarSlugs = array_column($pillars, 'slug');
    $washPillar  = in_array('wash', $pillarSlugs) ? 'wash' : false;
?>

<!-- WASH Document -->
<section class="py-12 md:py-16" id="wash">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Document Header -->
        <header class="text-left mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6">
                Water, Sanitation & Hygiene (WASH) Pillar
            </h1>
            <p class="text-lg text-slate-700 leading-relaxed mb-8">
                Our WASH pillar focuses on improving access to safe water, adequate sanitation, and proper hygiene practices across Kenya. We work with communities, governments, and partners to implement sustainable solutions that address the country's most pressing WASH challenges through evidence-based approaches and community engagement.
            </p>
            <div class="flex justify-center">
                <a href="<?= base_url('ksp/pillar-articles/' . $washPillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View Articles
                </a>
            </div>
        </header>

        <!-- Document Content -->
        <article class="prose prose-lg max-w-none">
            
            <!-- 1. Water Access and Quality Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-tint mr-3 text-2xl"></i>
                    1. Water Access and Quality
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Ensuring universal access to safe, affordable, and sustainable water services through infrastructure development, quality monitoring, and community-based management systems.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Water Access Components</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Rural Water Supply:</strong> Boreholes, wells, and community water systems</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Urban Water Systems:</strong> Municipal water infrastructure and distribution</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Water Quality Monitoring:</strong> Testing protocols and safety standards</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Water Treatment Technologies:</strong> Point-of-use and community-scale solutions</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 2. Sanitation Systems Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-toilet mr-3 text-2xl"></i>
                    2. Sanitation Systems
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Developing comprehensive sanitation solutions from household to community level, including waste management, treatment facilities, and sustainable sanitation technologies.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Sanitation Approaches</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Community-Led Total Sanitation (CLTS):</strong> Community-driven sanitation initiatives</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>School WASH Programs:</strong> Sanitation facilities and hygiene education in schools</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Urban Sanitation Solutions:</strong> Sewerage systems and waste management</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Ecological Sanitation:</strong> Sustainable and resource-recovery approaches</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 3. Hygiene Promotion Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-hands-wash mr-3 text-2xl"></i>
                    3. Hygiene Promotion
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Promoting healthy hygiene behaviors through education, community engagement, and behavior change communication strategies to prevent disease and improve health outcomes.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Hygiene Interventions</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Behavior Change Communication:</strong> Targeted messaging and community campaigns</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Handwashing Promotion:</strong> Critical times and proper techniques</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Menstrual Hygiene Management:</strong> Education and access to sanitary products</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Food Safety and Hygiene:</strong> Safe food handling and preparation practices</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 4. Policy and Advocacy Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-balance-scale mr-3 text-2xl"></i>
                    4. Policy and Advocacy
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Developing and advocating for enabling policies, regulatory frameworks, and institutional arrangements that support sustainable WASH service delivery.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Policy Framework</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>National WASH Policies:</strong> Policy development and implementation guidelines</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Regulatory Frameworks:</strong> Standards and compliance mechanisms</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Institutional Strengthening:</strong> Capacity building for WASH institutions</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Advocacy Strategies:</strong> Promoting WASH sector development</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 5. Research and Innovation Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-flask mr-3 text-2xl"></i>
                    5. Research and Innovation
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Advancing WASH through research, innovation, and technology development to address emerging challenges and improve service delivery effectiveness.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Research Areas</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Innovative Technologies:</strong> New WASH technologies and approaches</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Implementation Research:</strong> Evidence-based program design and delivery</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Health Impact Studies:</strong> WASH interventions and health outcomes</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Economic Analysis:</strong> Cost-effectiveness and sustainability studies</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 6. Community Programs Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-users mr-3 text-2xl"></i>
                    6. Community Programs
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Implementing community-based WASH programs that are locally owned, culturally appropriate, and sustainable through participatory approaches and capacity building.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Program Components</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Community Mobilization:</strong> Engaging communities in WASH planning and implementation</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Capacity Building:</strong> Training local leaders and water committees</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Participatory Monitoring:</strong> Community-led monitoring and evaluation</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Sustainability Mechanisms:</strong> Long-term operation and maintenance systems</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Conclusion -->
            <section class="mt-16 p-8 bg-primary/5 rounded-lg border-l-4 border-primary bg-white">
                <h3 class="text-2xl font-bold text-primary mb-4">Comprehensive WASH Development</h3>
                <p class="text-slate-700 text-lg leading-relaxed mb-6">
                    KEWASNET's WASH pillar takes a holistic approach to water, sanitation, and hygiene challenges in Kenya. By combining technical expertise with community engagement, policy advocacy, and innovative research, we work towards universal access to sustainable WASH services that improve health, dignity, and quality of life for all Kenyans.
                </p>
                <div class="flex justify-center">
                    <a href="<?= base_url('ksp/pillar-articles/' . $washPillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Explore WASH Resources
                    </a>
                </div>
            </section>

        </article>
    </div>
</section>