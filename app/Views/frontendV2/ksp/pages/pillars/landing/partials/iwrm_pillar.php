<?php
    $pillarSlugs = array_column($pillars, 'slug');
    $iwrmPillar  = in_array('iwrm', $pillarSlugs) ? 'iwrm' : false;
?>

<!-- IWRM Document -->
<section class="py-12 md:py-16" id="iwrm">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Document Header -->
        <header class="text-left mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6">
                Integrated Water Resources Management (IWRM) Pillar
            </h1>
            <p class="text-lg text-slate-700 leading-relaxed mb-8">
                Integrated Water Resources Management (IWRM) is a collaborative approach to address water-related challenges at the regional level by integrating management across sectors and stakeholders. It fosters coordination among government agencies, water districts, NGOs, and communities to develop sustainable water management plans, enhancing water supply reliability, quality, ecosystem health, and climate resilience.
            </p>
            <div class="flex justify-center">
                <a href="<?= base_url('ksp/pillar-articles/' . $iwrmPillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View Articles
                </a>
            </div>
        </header>

        <!-- Document Content -->
        <article class="prose prose-lg max-w-none">
            
            <!-- 1. Regulatory Framework Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-gavel mr-3 text-2xl"></i>
                    1. Regulatory Framework
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Laws and policies that guide integrated water resource management, ensuring sustainable and equitable water use across all sectors and stakeholders.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Laws and Policies</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Water Act 2016: WRMA mandate to manage, regulate, and conserve all water resources</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Policy gap review on Water Resources Management (WRM)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 2. Water Availability and Access Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-water mr-3 text-2xl"></i>
                    2. Water Availability and Access
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Ensuring reliable access to surface and groundwater resources to meet regional demands through comprehensive assessment and management strategies.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Water Resources</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Surface Water Availability:</strong> Monitoring and managing surface water resources to ensure sustainable supply</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Groundwater Availability:</strong> Assessing and managing groundwater resources for equitable access</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 3. Water Storage and Distribution Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-database mr-3 text-2xl"></i>
                    3. Water Storage and Distribution
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Developing infrastructure and technologies to store, distribute, and monitor water quality efficiently while minimizing losses and ensuring reliability.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Infrastructure Components</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Storage Systems:</strong> Reservoirs, tanks, dams for effective water storage</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Distribution Networks:</strong> Infrastructure for efficient water distribution</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Treatment Technologies:</strong> Advanced methods for water treatment</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Quality Monitoring:</strong> Systems for water quality testing and monitoring</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Leak Detection:</strong> Methods to detect and reduce water loss</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 4. Watershed Management Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-leaf mr-3 text-2xl"></i>
                    4. Watershed Management
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Managing watersheds to protect ecosystems and ensure sustainable water resources through collaborative approaches and ecosystem restoration.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Management Components</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Watershed Characteristics:</strong> Understanding watershed functions and features</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Degradation and Restoration:</strong> Strategies for watershed restoration</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Ecosystem Services:</strong> Benefits provided by healthy watersheds</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Stakeholder Engagement:</strong> Collaboration with communities and stakeholders</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>KEWASNET-IWaSP Products:</strong> Policy and regulatory frameworks for watershed management</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 5. Irrigation Management Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-seedling mr-3 text-2xl"></i>
                    5. Irrigation Management
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Optimizing irrigation systems for efficient and sustainable agricultural water use through advanced technologies and water conservation practices.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Irrigation Components</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Irrigation Systems:</strong> Drip, sprinkler, and flood irrigation systems</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Crop Water Requirements:</strong> Scheduling water needs for crops</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Water Sources:</strong> Surface water, groundwater, reclaimed water for irrigation</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Irrigation Efficiency:</strong> Techniques for water conservation in irrigation</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Water Quality Management:</strong> Ensuring water quality in irrigation systems</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 6. Groundwater Management Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-tint mr-3 text-2xl"></i>
                    6. Groundwater Management
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Strategies and tools for sustainable groundwater management and monitoring through comprehensive planning and research-based approaches.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Management Tools and Resources</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Management Plans:</strong> Plans and strategies for regional aquifers</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Monitoring Protocols:</strong> Tools for groundwater monitoring and data analysis</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Technical Reports:</strong> Groundwater modeling and simulation studies</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Webinars and Workshops:</strong> Sessions on groundwater policies and regulations</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Research Papers:</strong> Innovative groundwater management practices</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 7. Pollution Control Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-shield-alt mr-3 text-2xl"></i>
                    7. Pollution Control
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Measures to prevent and mitigate water pollution, ensuring compliance with quality standards through comprehensive monitoring and treatment approaches.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Pollution Control Measures</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Pollutant Fate and Transport:</strong> Understanding pollutant movement in the environment</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Prevention and Mitigation:</strong> Strategies to reduce water pollution</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Quality Standards:</strong> Regulations for maintaining water quality</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Risk Assessment:</strong> Managing risks at contaminated sites</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Wastewater Treatment:</strong> Technologies for wastewater treatment and disposal</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 8. Stormwater Management Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-cloud-rain mr-3 text-2xl"></i>
                    8. Stormwater Management
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Designing and managing infrastructure to handle stormwater effectively while ensuring quality and compliance with environmental regulations.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Stormwater Management Components</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Infrastructure Design:</strong> Designing effective stormwater infrastructure</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Quality Monitoring:</strong> Monitoring and analyzing stormwater quality</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span><strong>Regulations and Policies:</strong> Policies for stormwater management compliance</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Conclusion -->
            <section class="mt-16 p-8 bg-primary/5 rounded-lg border-l-4 border-primary">
                <h3 class="text-2xl font-bold text-primary mb-4">Comprehensive Water Resources Management</h3>
                <p class="text-slate-700 text-lg leading-relaxed mb-6">
                    KEWASNET's IWRM approach integrates all aspects of water resources management from regulatory frameworks to practical implementation. Through collaborative efforts across sectors and stakeholders, we work towards sustainable, equitable, and efficient water resource management that addresses current needs while preserving resources for future generations.
                </p>
                <div class="flex justify-center">
                    <a href="<?= base_url('ksp/pillar-articles/' . $iwrmPillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Explore IWRM Resources
                    </a>
                </div>
            </section>

        </article>
    </div>
</section>
