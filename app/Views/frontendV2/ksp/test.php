<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Test Page</h1>
    <p>Testing pillar slug: <?= esc($slug) ?></p>
    <p>If you see this, the basic routing and view rendering is working.</p>

    <div class="bg-white rounded-lg shadow-sm border borderColor mb-6">
        <!-- Discussion Header -->
        <div class="p-6 border-b borderColor">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center">
                    <img src="https://randomuser.me/api/portraits/women/43.jpg" alt="User" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">Effective strategies for reducing non-revenue water</h2>
                        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                            <span>Posted by Jane Muthoni</span>
                            <span>•</span>
                            <span>2 hours ago</span>
                            <span>•</span>
                            <span class="flex items-center">
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                128 views
                            </span>
                        </div>
                    </div>
                </div>
                <button class="text-slate-400 hover:text-primary">
                    <i data-lucide="bookmark" class="w-5 h-5"></i>
                </button>
            </div>
            
            <!-- Discussion Tags -->
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-primaryShades-100 text-primary text-xs px-3 py-1 rounded-full">#water-management</span>
                <span class="bg-primaryShades-100 text-primary text-xs px-3 py-1 rounded-full">#conservation</span>
                <span class="bg-primaryShades-100 text-primary text-xs px-3 py-1 rounded-full">#best-practices</span>
            </div>
            
            <!-- Discussion Content -->
            <div class="prose max-w-none text-slate-700 mb-6">
                <p>Hello fellow water professionals,</p>
                <p>I'm currently working with a mid-sized water utility in Nakuru County, and we've been struggling with high non-revenue water (NRW) percentages (currently at 42%). We've implemented some basic leak detection programs and meter replacement initiatives, but the results have been modest.</p>
                <p>I'm particularly interested in hearing from utilities that have successfully reduced their NRW percentages below 25%. What strategies worked best in your context?</p>
                <ul>
                    <li>Which technologies provided the best ROI for leak detection?</li>
                    <li>How did you handle illegal connections and meter tampering?</li>
                    <li>What community engagement approaches worked for reducing water theft?</li>
                    <li>How did you structure your NRW reduction team?</li>
                </ul>
                <p>Any insights, case studies, or lessons learned would be greatly appreciated. I'm happy to share more details about our specific challenges if that would help with recommendations.</p>
                <p>Looking forward to the discussion!</p>
                <p>Jane</p>
            </div>
            
            <!-- Discussion Actions -->
            <div class="flex flex-wrap justify-between items-center pt-4 border-t borderColor">
                <div class="flex items-center space-x-4">
                    <button class="flex items-center text-slate-600 hover:text-primary">
                        <i data-lucide="thumbs-up" class="w-5 h-5 mr-1"></i>
                        <span>24</span>
                    </button>
                    <button class="flex items-center text-slate-600 hover:text-primary">
                        <i data-lucide="message-square" class="w-5 h-5 mr-1"></i>
                        <span>14 replies</span>
                    </button>
                    <button class="flex items-center text-slate-600 hover:text-primary">
                        <i data-lucide="share-2" class="w-5 h-5 mr-1"></i>
                        <span>Share</span>
                    </button>
                </div>
                <button class="text-primary font-medium flex items-center">
                    <i data-lucide="flag" class="w-5 h-5 mr-1"></i>
                    Report
                </button>
            </div>
        </div>
        
        <!-- Replies Section -->
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">14 Replies</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-slate-500">Sort by:</span>
                    <select class="bg-white border borderColor rounded-md px-3 py-1 text-sm">
                        <option>Newest first</option>
                        <option>Oldest first</option>
                        <option>Most liked</option>
                    </select>
                </div>
            </div>
            
            <!-- Reply Form -->
            <div class="bg-slate-50 rounded-lg p-4 mb-6 border borderColor">
                <h4 class="font-medium mb-3">Post your reply</h4>
                <textarea class="w-full border border-gray-300 rounded-md p-3 mb-3 focus:ring-2 focus:ring-primary focus:border-transparent" rows="4" placeholder="Share your thoughts..."></textarea>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <button class="text-slate-500 hover:text-primary">
                            <i data-lucide="paperclip" class="w-5 h-5"></i>
                        </button>
                        <button class="text-slate-500 hover:text-primary">
                            <i data-lucide="image" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <button class="gradient-btn px-6 py-2 rounded-md text-white font-medium">
                        <span>Post Reply</span> 
                    </button>
                </div>
            </div>
            
            <!-- Reply 1 -->
            <div class="reply-card bg-white rounded-lg p-6 mb-4 border borderColor">
                <div class="flex items-start mb-4">
                    <img src="https://randomuser.me/api/portraits/men/65.jpg" alt="User" class="w-10 h-10 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">James Kamau</h4>
                        <div class="flex items-center text-sm text-slate-500">
                            <span>Water Engineer at Nairobi Water</span>
                            <span class="mx-2">•</span>
                            <span>1 hour ago</span>
                        </div>
                    </div>
                </div>
                <div class="prose max-w-none text-slate-700 mb-4">
                    <p>Great topic, Jane. We reduced NRW from 38% to 22% over 3 years at Nairobi Water. The most effective strategy was implementing District Metered Areas (DMAs). This allowed us to:</p>
                    <ul>
                        <li>Pinpoint leakage hotspots quickly</li>
                        <li>Prioritize repairs based on severity</li>
                        <li>Measure improvements accurately</li>
                    </ul>
                    <p>We used a combination of acoustic loggers and manual surveys. The initial investment was significant but paid for itself within 18 months through reduced water losses.</p>
                </div>
                <div class="flex items-center justify-between pt-3 border-t borderColor">
                    <div class="flex items-center space-x-4">
                        <button class="flex items-center text-slate-600 hover:text-primary">
                            <i data-lucide="thumbs-up" class="w-5 h-5 mr-1"></i>
                            <span>8</span>
                        </button>
                        <button class="flex items-center text-slate-600 hover:text-primary">
                            <i data-lucide="message-square" class="w-5 h-5 mr-1"></i>
                            <span>Reply</span>
                        </button>
                    </div>
                    <button class="text-slate-500 hover:text-primary">
                        <i data-lucide="flag" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <!-- Nested Reply -->
                <div class="reply-card bg-slate-50 rounded-lg p-4 mt-4 border borderColor">
                    <div class="flex items-start mb-3">
                        <img src="https://randomuser.me/api/portraits/women/43.jpg" alt="User" class="w-8 h-8 rounded-full mr-3">
                        <div>
                            <h4 class="font-medium">Jane Muthoni</h4>
                            <div class="text-xs text-slate-500">
                                <span>Original poster</span>
                                <span class="mx-2">•</span>
                                <span>45 minutes ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="prose max-w-none text-sm text-slate-700 mb-2">
                        <p>Thanks James! How did you handle resistance from staff during the DMA implementation? We're facing some pushback from our field teams who see it as extra work.</p>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center space-x-4">
                            <button class="flex items-center text-slate-600 hover:text-primary text-sm">
                                <i data-lucide="thumbs-up" class="w-4 h-4 mr-1"></i>
                                <span>2</span>
                            </button>
                        </div>
                        <button class="text-slate-500 hover:text-primary text-sm">
                            <i data-lucide="flag" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Reply 2 -->
            <div class="reply-card bg-white rounded-lg p-6 mb-4 border borderColor">
                <div class="flex items-start mb-4">
                    <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="User" class="w-10 h-10 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Sarah Auma</h4>
                        <div class="flex items-center text-sm text-slate-500">
                            <span>WASH Consultant</span>
                            <span class="mx-2">•</span>
                            <span>30 minutes ago</span>
                        </div>
                    </div>
                </div>
                <div class="prose max-w-none text-slate-700 mb-4">
                    <p>In addition to technical solutions, don't underestimate the power of community engagement. We worked with a utility in Kisumu that reduced NRW by 15% points through:</p>
                    <ol>
                        <li>Public awareness campaigns about water conservation</li>
                        <li>Anonymous reporting systems for leaks and illegal connections</li>
                        <li>Incentives for neighborhoods with lowest losses</li>
                    </ol>
                    <p>Attaching a case study that might be helpful.</p>
                    <div class="bg-slate-100 rounded-md p-3 mt-3 flex items-center">
                        <i data-lucide="file-text" class="w-5 h-5 text-slate-500 mr-3"></i>
                        <span class="font-medium">Kisumu_NRW_Reduction_CaseStudy.pdf</span>
                        <span class="text-sm text-slate-500 ml-auto">2.4 MB</span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t borderColor">
                    <div class="flex items-center space-x-4">
                        <button class="flex items-center text-slate-600 hover:text-primary">
                            <i data-lucide="thumbs-up" class="w-5 h-5 mr-1"></i>
                            <span>5</span>
                        </button>
                        <button class="flex items-center text-slate-600 hover:text-primary">
                            <i data-lucide="message-square" class="w-5 h-5 mr-1"></i>
                            <span>Reply</span>
                        </button>
                    </div>
                    <button class="text-slate-500 hover:text-primary">
                        <i data-lucide="flag" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            
            <!-- View More Replies -->
            <div class="text-center">
                <button class="text-primary font-medium flex items-center justify-center mx-auto">
                    <i data-lucide="chevron-down" class="w-5 h-5 mr-1"></i>
                    View 9 more replies
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
