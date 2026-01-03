<?php 
    $pillarSlugs      = array_column($pillars, 'slug');
    $governancePillar = in_array('governance', $pillarSlugs) ? 'governance' : false;
?>

<!-- Governance Document -->
<section class="py-12 md:py-16" id="governance">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Document Header -->
        <header class="text-left mb-12">
            <h3 class="text-2xl md:text-4xl font-bold text-primary mb-6">
                Governance Pillar
            </h3>
            <p class="text-lg text-slate-700 leading-relaxed mb-8">
                Effective water governance involves a range of stakeholders working together to ensure that water resources are managed in an integrated and participatory manner. Key elements include transparent decision-making, stakeholder involvement, clear regulations, adequate financing, and data-driven decisions.
            </p>
            <div class="flex justify-center">
                <a href="<?= base_url("ksp/pillar-articles/". $governancePillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View Articles
                </a>
            </div>
        </header>

        <!-- Document Content -->
        <article class="prose prose-lg max-w-none">
            
            <!-- 1. Integrity Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-balance-scale mr-3 text-2xl"></i>
                    1. Integrity
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Integrity in water and Water Resources Management (WRM) sectors ensures transparency, accountability, and ethical conduct, promoting efficient, equitable, and sustainable water use. It builds trust through clear regulations, transparent decision-making, and anti-corruption measures.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Policies, Laws, Regulations, and Guidelines</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-4 text-slate-700">
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Policies:</span>
                                <span>National Water Policy 2021, Kajiado and Kiambu Water Policies, county policy development guide</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Laws:</span>
                                <span>Water Act 2002 & 2016, CSO position paper, citizen versions</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Regulations:</span>
                                <span>Water services, resources, and harvesting & storage</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Guidelines:</span>
                                <span>To be specified</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Integrity Tools</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Integrity Management Toolbox</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Water Stewardship Integrity Framework</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Integrity Quality & Compliance</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Water Integrity Scan</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 2. Accountability Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-3 text-2xl"></i>
                    2. Accountability
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Accountability in water and WRM sectors ensures stakeholders are answerable for their actions, promoting transparency, compliance, and equitable access to water resources for sustainable management.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Policies, Laws, Regulations, and Guidelines</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-4 text-slate-700">
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Policies:</span>
                                <span>To be specified</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Laws:</span>
                                <span>To be specified</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Regulations:</span>
                                <span>To be specified</span>
                            </li>
                            <li class="flex items-start">
                                <span class="font-semibold text-primary mr-2 min-w-[80px]">Guidelines:</span>
                                <span>To be specified</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Accountability Tools</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Election Promises Monitoring Framework (RSR)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Kenya National Human Rights Commission Framework for monitoring the right to water and sanitation (adopted for SDG 6)</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Sector Reporting</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>SWA Commitments</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Contributions to Sustainable Development Goals (SDGs)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Citizen Score Cards</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>CSO Performance Report (The Voice)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Customer Satisfaction Surveys (NAWASCO & HOMAWASCO)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Annual Sector CSO Reports (from Members and Sector partners)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 3. Participation Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-users mr-3 text-2xl"></i>
                    3. Participation
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Participation in water governance ensures that all stakeholders, including communities, civil society, private sector, and government, are actively involved in decision-making processes related to water resource management.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Stakeholder Engagement Mechanisms</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Community Water Committees</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Water Resource Users Associations (WRUAs)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Public participation in water policy development</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Multi-stakeholder platforms and forums</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <hr class="my-12 border-slate-200">

            <!-- 4. Transparency Section -->
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-primary mb-6 flex items-center">
                    <i class="fas fa-eye mr-3 text-2xl"></i>
                    4. Transparency
                </h2>
                
                <div class="mb-8">
                    <p class="text-slate-700 text-lg leading-relaxed mb-6">
                        Transparency in water governance involves open access to information, clear communication of decisions, and public disclosure of water-related data and processes to build trust and enable informed participation.
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Information Access and Disclosure</h3>
                    <div class="bg-slate-50 p-6 rounded-lg">
                        <ul class="space-y-3 text-slate-700">
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Water quality monitoring data publication</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Budget allocation and expenditure reports</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Water services performance indicators</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-primary mr-3 mt-1">•</span>
                                <span>Public access to information mechanisms</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Conclusion -->
            <section class="mt-16 p-8 bg-primary/5 rounded-lg border-l-4 border-primary bg-white">
                <h3 class="text-2xl font-bold text-primary mb-4">Moving Forward</h3>
                <p class="text-slate-700 text-lg leading-relaxed mb-6">
                    KEWASNET continues to advocate for strengthened governance frameworks that promote integrity, accountability, participation, and transparency in Kenya's water sector. Through collaborative efforts with government, civil society, and development partners, we work towards ensuring sustainable and equitable water resource management for all.
                </p>
                <div class="flex justify-center">
                    <a href="<?= base_url("ksp/pillar-articles/". $governancePillar) ?>" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Explore Governance Resources
                    </a>
                </div>
            </section>

        </article>
    </div>
</section>