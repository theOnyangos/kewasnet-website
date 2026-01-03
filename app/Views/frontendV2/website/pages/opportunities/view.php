<?php
    // dd($attachments);
?>
<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Meta Tags  -->
<?= $this->section('meta') ?>
<meta name="description" content="<?= esc($description) ?>">
<meta name="keywords" content="KEWASNET, job opportunities, WASH sector, Kenya, <?= esc($opportunity['title']) ?>, career">
<meta name="author" content="KEWASNET">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?= esc($title) ?>">
<meta property="og:description" content="<?= esc($description) ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?= current_url() ?>">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= esc($title) ?>">
<meta name="twitter:description" content="<?= esc($description) ?>">
<?= $this->endSection() ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Breadcrumb Section -->
    <section class="bg-lightGray py-6">
        <div class="container mx-auto px-4">
            <nav class="text-sm">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <a href="<?= site_url('/') ?>" class="text-primary hover:text-primaryShades-700">Home</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.476 239.03c9.373 9.372 9.373 24.568 0 33.941z"/>
                        </svg>
                    </li>
                    <li class="flex items-center">
                        <a href="<?= site_url('opportunities') ?>" class="text-primary hover:text-primaryShades-700">Opportunities</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.476 239.03c9.373 9.372 9.373 24.568 0 33.941z"/>
                        </svg>
                    </li>
                    <li class="text-slate-500"><?= esc($opportunity['title']) ?></li>
                </ol>
            </nav>
            
            <!-- Back Button -->
            <div class="mt-4">
                <a href="<?= site_url('opportunities') ?>" 
                   class="inline-flex items-center text-primary hover:text-primaryShades-700 font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to All Opportunities
                </a>
            </div>
        </div>
    </section>

    <!-- Job Details Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Job Header -->
                    <div class="mb-8">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <?php
                                $badgeColors = [
                                    'full-time' => 'bg-primary',
                                    'part-time' => 'bg-secondary',
                                    'contract' => 'bg-primaryShades-700',
                                    'internship' => 'bg-secondaryShades-500',
                                    'freelance' => 'bg-primaryShades-600'
                                ];
                                $badgeColor = $badgeColors[$opportunity['opportunity_type']] ?? 'bg-primary';
                            ?>
                            <span class="<?= $badgeColor ?> text-white px-3 py-1 rounded-full text-sm font-medium">
                                <?= ucfirst(str_replace('-', ' ', $opportunity['opportunity_type'])) ?>
                            </span>
                            
                            <?php if (!empty($opportunity['is_remote']) && $opportunity['is_remote']): ?>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Remote</span>
                            <?php endif; ?>
                            
                            <?php if ($deadlinePassed): ?>
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">Deadline Passed</span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="text-3xl md:text-4xl font-bold text-primaryShades-800 mb-4"><?= esc($opportunity['title']) ?></h1>
                        
                        <div class="flex flex-wrap items-center gap-6 text-slate-600 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span><?= esc($opportunity['company'] ?? 'KEWASNET') ?></span>
                            </div>
                            
                            <?php if (!empty($opportunity['location'])): ?>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span><?= esc($opportunity['location']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($opportunity['salary_min']) && !empty($opportunity['salary_max'])): ?>
                                <div class="flex items-center">
                                    <span>
                                        <?= esc($opportunity['salary_currency'] ?? 'KES') ?> 
                                        <?= number_format($opportunity['salary_min']) ?> - 
                                        <?= number_format($opportunity['salary_max']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($opportunity['application_deadline'])): ?>
                            <?php
                                $deadline = new DateTime($opportunity['application_deadline']);
                                $now = new DateTime();
                                $interval = $now->diff($deadline);
                                $daysLeft = $deadlinePassed ? 0 : $interval->days;
                            ?>
                            <div class="bg-lightGray p-4 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">Application Deadline: </span>
                                    <span class="ml-2"><?= $deadline->format('F j, Y') ?></span>
                                    <?php if (!$deadlinePassed): ?>
                                        <span class="ml-2 text-orange-600 font-medium">(<?= $daysLeft ?> days left)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Job Description -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-primaryShades-800 mb-4">Job Description</h2>
                        <div class="prose max-w-none text-dark">
                            <?= $opportunity['description'] ?>
                        </div>
                    </div>

                    <!-- Job Attachments -->
                    <?php if (!empty($attachments) && is_array($attachments)): ?>
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-primaryShades-800 mb-4">Job Documents & Attachments</h2>
                            <div class="bg-lightGray p-6 rounded-lg">
                                <div class="space-y-3">
                                    <?php foreach ($attachments as $attachment): ?>
                                        <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-primary transition-colors">
                                            <div class="flex items-center space-x-3">
                                                <!-- File type icon -->
                                                <?php
                                                    $extension = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
                                                    $iconColor = 'text-gray-500';
                                                    $icon = 'file-text';
                                                    
                                                    switch ($extension) {
                                                        case 'pdf':
                                                            $iconColor = 'text-red-500';
                                                            $icon = 'file-text';
                                                            break;
                                                        case 'doc':
                                                        case 'docx':
                                                            $iconColor = 'text-blue-500';
                                                            $icon = 'file-text';
                                                            break;
                                                        case 'xls':
                                                        case 'xlsx':
                                                            $iconColor = 'text-green-500';
                                                            $icon = 'sheet';
                                                            break;
                                                        case 'ppt':
                                                        case 'pptx':
                                                            $iconColor = 'text-orange-500';
                                                            $icon = 'presentation';
                                                            break;
                                                        case 'jpg':
                                                        case 'jpeg':
                                                        case 'png':
                                                        case 'gif':
                                                            $iconColor = 'text-purple-500';
                                                            $icon = 'image';
                                                            break;
                                                    }
                                                ?>
                                                
                                                <div class="flex-shrink-0">
                                                    <i data-lucide="<?= $icon ?>" class="w-8 h-8 <?= $iconColor ?>"></i>
                                                </div>
                                                
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-dark truncate">
                                                        <?= esc($attachment->original_name ?? 'Document') ?>
                                                    </p>
                                                    <div class="flex items-center text-xs text-slate-500 space-x-2">
                                                        <span><?= strtoupper($extension) ?></span>
                                                        <?php if (isset($attachment->file_size)): ?>
                                                            <span>•</span>
                                                            <span><?= formatFileSize($attachment->file_size) ?></span>
                                                        <?php endif; ?>
                                                        <?php if (isset($attachment->created_at)): ?>
                                                            <span>•</span>
                                                            <span>
                                                                <?php
                                                                    $uploadDate = new DateTime($attachment->created_at);
                                                                    echo $uploadDate->format('M j, Y');
                                                                ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Download Actions -->
                                            <div class="flex items-center space-x-2">
                                                <!-- View/Preview button for supported file types -->
                                                <a href="<?= base_url('client/view/preview-attachment/' . $attachment->file_path) ?>" 
                                                    target="_blank" rel="noopener noreferrer"
                                                    class="flex items-center space-x-1 px-3 py-2 text-sm text-primary border border-primary rounded-md hover:bg-primary hover:text-white transition-colors"
                                                    title="View <?= esc($attachment->original_name) ?>">
                                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                                    <span>View</span>
                                                </a>
                                                
                                                <!-- Download button -->
                                                <a href="<?= base_url('client/download/download-attachment/' . $attachment->file_path) ?>" 
                                                   class="flex items-center space-x-1 px-3 py-2 text-sm text-white bg-primary rounded-md hover:bg-primaryShades-700 transition-colors"
                                                   title="Download <?= esc($attachment->original_name) ?>">
                                                    <i data-lucide="download" class="w-4 h-4"></i>
                                                    <span>Download</span>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="text-sm">
                                            <p class="text-blue-700 font-medium">Additional Documents</p>
                                            <p class="text-blue-600 mt-1">Review these documents for more details about the position, requirements, or application process.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Requirements -->
                    <?php if (!empty($opportunity['requirements']) && is_array($opportunity['requirements'])): ?>
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-primaryShades-800 mb-4">Requirements</h2>
                            <ul class="space-y-2">
                                <?php foreach ($opportunity['requirements'] as $requirement): ?>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-dark"><?= esc($requirement) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Benefits -->
                    <?php if (!empty($opportunity['benefits']) && is_array($opportunity['benefits'])): ?>
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-primaryShades-800 mb-4">Benefits</h2>
                            <ul class="space-y-2">
                                <?php foreach ($opportunity['benefits'] as $benefit): ?>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-primary mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                        </svg>
                                        <span class="text-dark"><?= esc($benefit) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Scope -->
                    <?php if ($opportunity['scope']): ?>
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-primaryShades-800 mb-4">Scope of Work</h2>
                            <div class="prose max-w-none text-dark">
                                <?= $opportunity['scope'] ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Application Form -->
                    <div class="bg-lightGray p-6 rounded-lg shadow-lg sticky top-8">
                        <?php if (!$deadlinePassed): ?>
                            <h3 class="text-xl font-bold text-primaryShades-800 mb-6">Apply for this Position</h3>
                            
                            <form id="jobApplicationForm" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="opportunity_slug" value="<?= esc($opportunity['slug']) ?>">
                                
                                <div class="space-y-4">
                                    <!-- Name Fields -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="first_name" class="block text-sm font-medium text-dark mb-1">First Name <span class="text-red-500">*</span></label>
                                            <input type="text" id="first_name" name="first_name"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                        </div>
                                        <div>
                                            <label for="last_name" class="block text-sm font-medium text-dark mb-1">Last Name <span class="text-red-500">*</span></label>
                                            <input type="text" id="last_name" name="last_name"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                        </div>
                                    </div>
                                    
                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-dark mb-1">Email Address <span class="text-red-500">*</span></label>
                                        <input type="email" id="email" name="application_email"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    
                                    <!-- Phone -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-dark mb-1">Phone Number</label>
                                        <input type="tel" id="phone" name="phone"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    
                                    <!-- Location -->
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-dark mb-1">Location</label>
                                        <input type="text" id="location" name="location" placeholder="City, Country"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>

                                    <!-- Current Job Title -->
                                    <div>
                                        <label for="current_job_title" class="block text-sm font-medium text-dark mb-1">Current Job Title</label>
                                        <input type="text" id="current_job_title" name="current_job_title"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>

                                    <!-- Current Company -->
                                    <div>
                                        <label for="current_company" class="block text-sm font-medium text-dark mb-1">Current Company</label>
                                        <input type="text" id="current_company" name="current_company"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>

                                    <!-- Experience -->
                                    <div>
                                        <label for="experience_years" class="block text-sm font-medium text-dark mb-1">Years of Experience</label>
                                        <select id="experience_years" name="experience_years"
                                                class="select2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                            <option value="">Select experience</option>
                                            <option value="0">Fresh Graduate</option>
                                            <option value="1">1 Year</option>
                                            <option value="2">2 Years</option>
                                            <option value="3">3 Years</option>
                                            <option value="4">4 Years</option>
                                            <option value="5">5+ Years</option>
                                            <option value="10">10+ Years</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Education -->
                                    <div>
                                        <label for="education_level" class="block text-sm font-medium text-dark mb-1">Education Level</label>
                                        <select id="education_level" name="education_level"
                                                class="select2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                            <option value="">Select education level</option>
                                            <option value="high-school">High School</option>
                                            <option value="diploma">Diploma</option>
                                            <option value="bachelors">Bachelor's Degree</option>
                                            <option value="masters">Master's Degree</option>
                                            <option value="phd">PhD</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Portfolio URL -->
                                    <div>
                                        <label for="portfolio_url" class="block text-sm font-medium text-dark mb-1">Portfolio URL</label>
                                        <input type="url" id="portfolio_url" name="portfolio_url" placeholder="https://your-portfolio.com"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>

                                    <!-- LinkedIn Profile -->
                                    <div>
                                        <label for="linkedin_profile" class="block text-sm font-medium text-dark mb-1">LinkedIn Profile</label>
                                        <input type="url" id="linkedin_profile" name="linkedin_profile" placeholder="https://www.linkedin.com/in/your-profile"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>

                                    <!-- Resume Upload -->
                                    <div>
                                        <label for="resume" class="block text-sm font-medium text-dark mb-1">Resume/CV * (PDF, DOC, DOCX - Max 5MB)</label>
                                        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    
                                    <!-- Cover Letter -->
                                    <div>
                                        <label for="cover_letter" class="block text-sm font-medium text-dark mb-1">Cover Letter <span class="text-red-500">*</span></label>
                                        <textarea id="cover_letter" name="cover_letter" rows="4"
                                                  placeholder="Tell us why you're the perfect fit for this role..."
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                                    </div>
                                    
                                    <!-- Availability -->
                                    <div>
                                        <label for="availability" class="block text-sm font-medium text-dark mb-1">Availability</label>
                                        <select id="availability" name="availability"
                                                class="select2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                            <option value="">Select availability</option>
                                            <option value="immediate">Immediate</option>
                                            <option value="2-weeks">2 Weeks Notice</option>
                                            <option value="1-month">1 Month Notice</option>
                                            <option value="2-months">2 Months Notice</option>
                                            <option value="3-months">3 Months Notice</option>
                                            <option value="negotiable">Negotiable</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Salary Expectation -->
                                    <div>
                                        <label for="salary_expectation" class="block text-sm font-medium text-dark mb-1">Salary Expectation</label>
                                        <input type="text" id="salary_expectation" name="salary_expectation" placeholder="e.g. KES 80,000 - 120,000"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <button type="submit" id="submitBtn"
                                        class="w-full bg-primary text-white py-3 px-4 rounded-md font-medium hover:bg-primaryShades-700 transition-colors mt-6">
                                    <span id="submitText">Submit Application</span>
                                    <span id="submitSpinner" class="hidden">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Submitting...
                                    </span>
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="text-lg font-bold text-dark mb-2">Application Deadline Passed</h3>
                                <p class="text-slate-600">The application deadline for this position has passed.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Job Summary -->
                    <div class="bg-white p-6 rounded-lg shadow-lg mt-6">
                        <h3 class="text-lg font-bold text-primaryShades-800 mb-4">Job Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Type:</span>
                                <span class="font-medium"><?= ucfirst(str_replace('-', ' ', $opportunity['opportunity_type'])) ?></span>
                            </div>
                            
                            <?php if (!empty($opportunity['contract_duration'])): ?>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Duration:</span>
                                    <span class="font-medium"><?= esc($opportunity['contract_duration']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($opportunity['hours_per_week'])): ?>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">Hours/Week:</span>
                                    <span class="font-medium"><?= esc($opportunity['hours_per_week']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between">
                                <span class="text-slate-600">Posted:</span>
                                <span class="font-medium">
                                    <?php
                                        $created = new DateTime($opportunity['created_at']);
                                        echo $created->format('M j, Y');
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Opportunities -->
    <?php if (!empty($relatedOpportunities)): ?>
        <section class="py-16 bg-lightGray">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center text-primaryShades-800 mb-12">Related Opportunities</h2>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <?php foreach ($relatedOpportunities as $related): ?>
                        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                            <div class="mb-4">
                                <span class="bg-primary text-white px-3 py-1 rounded-full text-sm">
                                    <?= ucfirst(str_replace('-', ' ', $related['opportunity_type'])) ?>
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-primaryShades-800 mb-2">
                                <a href="<?= site_url('opportunities/' . $related['slug']) ?>" class="hover:text-primary">
                                    <?= esc($related['title']) ?>
                                </a>
                            </h3>
                            <p class="text-slate-600 mb-3"><?= esc($related['company'] ?? 'KEWASNET') ?> • <?= esc($related['location'] ?? 'Kenya') ?></p>
                            <p class="text-dark text-sm line-clamp-3">
                                <?= esc(substr(strip_tags($related['description']), 0, 100)) ?>...
                            </p>
                            <div class="mt-4">
                                <a href="<?= site_url('opportunities/' . $related['slug']) ?>" 
                                   class="text-primary hover:text-primaryShades-700 font-medium text-sm">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Newsletter Subscription Section -->
    <?= view('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<!-- SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize Lucide icons
    lucide.createIcons();

    $('#jobApplicationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        var formData = new FormData(this);
        
        // Show loading state
        $('#submitBtn').prop('disabled', true);
        $('#submitText').addClass('hidden');
        $('#submitSpinner').removeClass('hidden');
        
        // Clear previous errors
        $('.error-message').remove();
        $('.border-red-500').removeClass('border-red-500');
        
        $.ajax({
            url: '<?= site_url('opportunities/apply/' . $opportunity['slug']) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Application Submitted!',
                        text: response.message,
                        confirmButtonColor: '#1e40af'
                    }).then(() => {
                        // Reset form
                        $('#jobApplicationForm')[0].reset();
                    });
                } else {
                    if (response.errors) {
                        // Display field-specific errors
                        $.each(response.errors, function(field, message) {
                            var input = $('[name="' + field + '"]');
                            input.addClass('border-red-500');
                            input.after('<div class="error-message text-red-600 text-sm mt-1">' + message + '</div>');
                        });
                    } else {
                        // Show general error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#1e40af'
                        });
                    }
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while submitting your application. Please try again.',
                    confirmButtonColor: '#1e40af'
                });
            },
            complete: function() {
                // Reset loading state
                $('#submitBtn').prop('disabled', false);
                $('#submitText').removeClass('hidden');
                $('#submitSpinner').addClass('hidden');
            }
        });
    });
    
    // File upload validation
    $('#resume').on('change', function() {
        var file = this.files[0];
        if (file) {
            var maxSize = 5 * 1024 * 1024; // 5MB
            var allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Please select a file smaller than 5MB.',
                    confirmButtonColor: '#1e40af'
                });
                this.value = '';
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a PDF, DOC, or DOCX file.',
                    confirmButtonColor: '#1e40af'
                });
                this.value = '';
                return;
            }
        }
    });

    // File preview function for attachments
    function previewFile(filePath, filename) {
        const extension = filename.split('.').pop().toLowerCase();
        
        if (extension === 'pdf') {
            // Open PDF in new window for preview
            window.open('<?= site_url("opportunities/preview-attachment/") ?>' + encodeURIComponent(filePath), '_blank');
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
            // Show image in modal/lightbox
            Swal.fire({
                title: filename,
                imageUrl: '<?= site_url("opportunities/preview-attachment/") ?>' + encodeURIComponent(filePath),
                imageAlt: filename,
                showCloseButton: true,
                showConfirmButton: false,
                width: 'auto',
                padding: '1rem',
                customClass: {
                    image: 'max-w-full h-auto'
                }
            });
        }
    }
});

<?php
// Helper function for file size formatting
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        if ($bytes == 0) return '0 B';
        
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
?>
</script>
<?= $this->endSection() ?>
