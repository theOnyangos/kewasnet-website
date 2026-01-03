<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Create Opportunity',
            'pageDescription' => 'Fill in the details of the opportunity below',
            'breadcrumbs' => [
                ['label' => 'Opportunities', 'url' => base_url('auth/opportunities')],
                ['label' => 'Create']
            ],
            'bannerActions' => '<a href="' . base_url('auth/opportunities') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Opportunities
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
            <div class="bg-white rounded-b-xl shadow-sm max-w-full pillar-form-container">
                <!-- Edit Opportunity Form -->
                <form id="jobForm" class="space-y-6 p-6" action="<?= site_url('auth/opportunities/create') ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <!-- CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    
                    <!-- Basic Information Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Job Title <span class="text-red-600">*</span></label>
                                <input type="text" id="title" name="title" placeholder="Enter job title"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border">
                                <p class="mt-1 text-sm text-red-600 hidden" id="title-error"></p>
                            </div>
                            
                            <!-- Opportunity Type -->
                            <div>
                                <label for="opportunity_type" class="block text-sm font-medium text-gray-700">Job Type <span class="text-red-600">*</span></label>
                                <select id="opportunity_type" name="opportunity_type" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary select2">
                                    <option value="">Select a type</option>
                                    <option value="full-time">Full-time</option>
                                    <option value="part-time">Part-time</option>
                                    <option value="contract">Contract</option>
                                    <option value="internship">Internship</option>
                                    <option value="freelance">Freelance</option>
                                </select>
                                <p class="mt-1 text-sm text-red-600 hidden" id="opportunity_type-error"></p>
                            </div>
                        </div>
                        
                        <!-- Company and Application Deadline -->
                        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Company -->
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700">Company/Organization</label>
                                <input type="text" id="company" name="company" placeholder="Enter company/organization name"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border">
                                <p class="mt-1 text-sm text-red-600 hidden" id="company-error"></p>
                            </div>
                            
                            <!-- Application Deadline -->
                            <div>
                                <label for="datepicker" class="block text-sm font-medium text-dark mb-2">Publish Date</label>
                                <input id="datepicker" name="application_deadline" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">Select the date and time to publish this opportunity</p>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Job Description <span class="text-red-600">*</span></label>
                            <textarea id="description" name="description" rows="4" placeholder="Enter job description"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border"></textarea>
                            <p class="mt-1 text-sm text-red-600 hidden" id="description-error"></p>
                        </div>
                        
                        <!-- Scope -->
                        <div class="mt-6">
                            <label for="scope" class="block text-sm font-medium text-gray-700">Job Scope</label>
                            <div class="rounded-lg">
                                <textarea id="scope" name="scope" class="summernote-editor w-full px-4 py-3 focus:outline-none" placeholder="Enter detailed job scope and responsibilities..."></textarea>
                            </div>
                            <p class="mt-1 text-sm text-red-600 hidden" id="scope-error"></p>
                        </div>
                    </div>
                    
                    <!-- Location & Salary Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Location & Compensation</h2>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input type="text" id="location" name="location" placeholder="Enter job location"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border">
                            </div>
                            
                            <!-- Remote Work -->
                            <div class="flex items-center mt-6">
                                <input type="hidden" name="is_remote" value="0">
                                <input id="is_remote" name="is_remote" type="checkbox" value="1"
                                    class="h-4 w-4 rounded border-gray-300 text-secondary focus:outline-none focus:ring-2 focus:ring-secondary">
                                <label for="is_remote" class="ml-2 block text-sm text-gray-700">This is a remote position</label>
                            </div>
                        </div>
                        
                        <!-- Salary Range -->
                        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div>
                                <label for="salary_min" class="block text-sm font-medium text-gray-700">Minimum Salary</label>
                                <div class="mt-1 relative rounded-md">
                                    <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-bold">KES</span>
                                    </div>
                                    <input type="number" id="salary_min" name="salary_min" min="0" step="0.01" placeholder="Enter minimum salary"
                                        class="block w-full pl-12 pr-12 rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border">
                                </div>
                            </div>
                            
                            <div>
                                <label for="salary_max" class="block text-sm font-medium text-gray-700">Maximum Salary</label>
                                <div class="mt-1 relative rounded-md">
                                    <div class="!absolute !inset-y-0 !left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-bold">KES</span>
                                    </div>
                                    <input type="number" id="salary_max" name="salary_max" min="0" step="0.01" placeholder="Enter maximum salary"
                                        class="block w-full pl-12 pr-12 rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border">
                                </div>
                            </div>
                            
                            <div>
                                <label for="salary_currency" class="block text-sm font-medium text-gray-700">Currency</label>
                                <select id="salary_currency" name="salary_currency" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary select2">
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="KES">KES</option>
                                    <!-- Add more currencies as needed -->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Type-Specific Fields Section -->
                    <div class="border-b border-gray-200 pb-6" id="typeSpecificFields">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Job Specifics</h2>
                        
                        <!-- Contract Duration (shown for contract type) -->
                        <div class="mt-6 hidden" id="contractFields">
                            <label for="contract_duration" class="block text-sm font-medium text-gray-700">Contract Duration</label>
                            <input type="text" id="contract_duration" name="contract_duration"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border"
                                placeholder="e.g., 6 months">
                        </div>
                        
                        <!-- Hours Per Week (shown for part-time type) -->
                        <div class="mt-6 hidden" id="hoursFields">
                            <label for="hours_per_week" class="block text-sm font-medium text-gray-700">Hours Per Week</label>
                            <input type="number" id="hours_per_week" name="hours_per_week" min="1" max="40" placeholder="Enter hours per week"  
                                class="mt-1 block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border">
                        </div>
                        
                        <!-- Benefits -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Benefits</label>
                            <div class="mt-2 space-y-2" id="benefitsContainer">
                                <div class="flex items-center">
                                    <input type="text" name="benefits[]"
                                        class="block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border"
                                        placeholder="e.g., Health insurance">
                                    <button type="button" class="ml-2 text-red-500 remove-benefit hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" id="addBenefit" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-1"></i> Add Benefit
                            </button>
                        </div>
                        
                        <!-- Requirements -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Requirements</label>
                            <div class="mt-2 space-y-2" id="requirementsContainer">
                                <div class="flex items-center">
                                    <input type="text" name="requirements[]" 
                                        class="block w-full rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-3 border"
                                        placeholder="e.g., 3+ years experience">
                                    <button type="button" class="ml-2 text-red-500 remove-requirement hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" id="addRequirement" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-1"></i> Add Requirement
                            </button>
                        </div>
                    </div>
                    
                    <!-- Document Upload Section -->
                    <div class="">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Job Document</h2>
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="document" name="document" type="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, RTF up to 50MB</p>
                                <p class="text-sm text-red-600 hidden" id="document-error"></p>
                                <div id="filePreview" class="hidden mt-2 w-full bg-secondaryShades-50 p-5 rounded-md items-center justify-start">
                                    <i data-lucide="file-text" class="text-secondary w-6 h-6 mr-2"></i>
                                    <p class="text-sm text-gray-900">Selected file: <span id="fileName"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status & Submission -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-dark">Publishing Options</h3>
                            <p class="text-sm text-gray-500">Control the visibility and accessibility of your job opportunity.</p>
                        </div>

                        <div class="w-full">
                            <div class="">
                                <label class="block text-sm font-medium text-dark mb-3">Status</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                        <input type="radio" name="status" value="draft" checked class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-dark">Draft</span>
                                            <p class="text-xs text-gray-500">Save as draft for later editing</p>
                                        </div>
                                    </label>
                                    <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                        <input type="radio" name="status" value="published" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-dark">Published</span>
                                            <p class="text-xs text-gray-500">Make it live immediately</p>
                                        </div>
                                    </label>
                                    <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                        <input type="radio" name="status" value="closed" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-dark">Closed</span>
                                            <p class="text-xs text-gray-500">No longer accepting applications</p>
                                        </div>
                                    </label>
                                </div>
                                <div id="status-error" class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" onclick="window.location.href='<?= site_url('auth/opportunities') ?>'" class="px-8 py-3 rounded-[50px] text-primary bg-primaryShades-50 hover:bg-primaryShades-100">
                            Cancel
                        </button>
                        <button type="submit" class="gradient-btn rounded-[50px] px-8 text-white flex justify-center items-center gap-2">
                            <i data-lucide="send" class="w-5 h-5 z-10"></i>
                            <span>Publish Opportunity</span>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Progress Modal -->
            <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
                <div class="flex items-center justify-center min-h-full">
                    <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4">
                        <div class="text-center">
                            <div class="mb-4">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                                </div>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Creating Job Opportunity</h3>
                            <p class="text-sm text-gray-500 mb-4" id="progressText">Please wait while we create your job opportunity...</p>
                            
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 0%" id="progressBar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Progress modal elements
            const progressModal = document.getElementById('progressModal');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            
            // Show/hide type-specific fields
            const opportunityType = document.getElementById('opportunity_type');
            opportunityType.addEventListener('change', function() {
                document.getElementById('contractFields').classList.add('hidden');
                document.getElementById('hoursFields').classList.add('hidden');
                
                if (this.value === 'contract') {
                    document.getElementById('contractFields').classList.remove('hidden');
                } else if (this.value === 'part-time') {
                    document.getElementById('hoursFields').classList.remove('hidden');
                }
            });

            // Dynamic benefit fields
            document.getElementById('addBenefit').addEventListener('click', function() {
                const container = document.getElementById('benefitsContainer');
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="text" name="benefits[]" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-2 border"
                        placeholder="e.g., Health insurance">
                    <button type="button" class="ml-2 text-red-500 remove-benefit">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(div);
                
                // Show remove buttons when there are multiple benefits
                if (container.children.length > 1) {
                    document.querySelectorAll('.remove-benefit').forEach(btn => btn.classList.remove('hidden'));
                }
            });

            // Dynamic requirement fields
            document.getElementById('addRequirement').addEventListener('click', function() {
                const container = document.getElementById('requirementsContainer');
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="text" name="requirements[]" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent sm:text-sm p-2 border"
                        placeholder="e.g., 3+ years experience">
                    <button type="button" class="ml-2 text-red-500 remove-requirement">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(div);
                
                // Show remove buttons when there are multiple requirements
                if (container.children.length > 1) {
                    document.querySelectorAll('.remove-requirement').forEach(btn => btn.classList.remove('hidden'));
                }
            });

            // Remove benefit/requirement
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-benefit') || 
                    e.target.closest('.remove-benefit')) {
                    const btn = e.target.classList.contains('remove-benefit') ? e.target : e.target.closest('.remove-benefit');
                    btn.parentElement.remove();
                    
                    // Hide remove buttons if only one remains
                    const benefits = document.querySelectorAll('#benefitsContainer > div');
                    if (benefits.length <= 1) {
                        document.querySelectorAll('.remove-benefit').forEach(b => b.classList.add('hidden'));
                    }
                }
                
                if (e.target.classList.contains('remove-requirement') || 
                    e.target.closest('.remove-requirement')) {
                    const btn = e.target.classList.contains('remove-requirement') ? e.target : e.target.closest('.remove-requirement');
                    btn.parentElement.remove();
                    
                    // Hide remove buttons if only one remains
                    const requirements = document.querySelectorAll('#requirementsContainer > div');
                    if (requirements.length <= 1) {
                        document.querySelectorAll('.remove-requirement').forEach(r => r.classList.add('hidden'));
                    }
                }
            });

            // File upload preview
            document.getElementById('document').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const filePreview = document.getElementById('filePreview');
                    const fileName = document.getElementById('fileName');
                    
                    // Validate file type and size
                    const validTypes = [
                        'application/pdf', 
                        'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain',
                        'text/rtf',
                        'application/rtf'
                    ];
                    const maxSize = 50 * 1024 * 1024; // 50MB
                    
                    console.log('File type:', file.type, 'Size:', file.size);
                    
                    if (!validTypes.includes(file.type)) {
                        showError('document', 'Invalid file type. Please upload PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, or RTF.');
                        e.target.value = '';
                        return;
                    }
                    
                    if (file.size > maxSize) {
                        showError('document', 'File size exceeds 50MB limit.');
                        e.target.value = '';
                        return;
                    }
                    
                    // Show preview
                    fileName.textContent = file.name;
                    filePreview.classList.remove('hidden');
                    filePreview.classList.add('flex');
                    hideError('document');
                }
            });

            // Form validation and submission
            document.getElementById('jobForm').addEventListener('submit', function(e) {
                e.preventDefault();
                clearErrors();
                
                // Validate salary range if provided
                const salaryMin = parseFloat(document.getElementById('salary_min').value);
                const salaryMax = parseFloat(document.getElementById('salary_max').value);
                
                if (!isNaN(salaryMin) && !isNaN(salaryMax) && salaryMin > salaryMax) {
                    showError('salary_min', 'Minimum salary cannot be greater than maximum');
                    isValid = false;
                }
                
                // Validate application deadline
                const deadline = document.getElementById('datepicker').value;
                if (deadline) {
                    const deadlineDate = new Date(deadline);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (deadlineDate < today) {
                        showError('datepicker', 'Application deadline cannot be in the past');
                        isValid = false;
                    }
                }
                                
                // Show progress modal
                progressModal.classList.remove('hidden');
                progressModal.querySelector('.flex').style.display = 'flex';
                let progress = 0;
                
                // Simulate progress
                const progressInterval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90;
                    progressBar.style.width = progress + '%';
                }, 200);
                
                // Create FormData object
                const formData = new FormData(this);
                
                // Handle empty scope field - set to null if empty to avoid constraint issues
                const scopeValue = formData.get('scope');
                if (!scopeValue || scopeValue.trim() === '' || scopeValue === '<p><br></p>') {
                    formData.delete('scope');
                    formData.append('scope', '');
                }
                
                // Log form data
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                // Submit via AJAX
                fetch('<?= site_url('auth/opportunities/create') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        return response.json().then(err => {
                            console.error('Error response:', err);
                            return Promise.reject(err);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success response:', data);
                    clearInterval(progressInterval);
                    progressBar.style.width = '100%';
                    
                    setTimeout(() => {
                        progressModal.classList.add('hidden');
                        
                        if (data.status === 'success' || data.data) {
                            // Show success notification
                            if (typeof showNotification === 'function') {
                                showNotification('success', 'Job opportunity created successfully!');
                            } else {
                                showSuccessMessage('Job opportunity created successfully!');
                            }
                            
                            // Redirect after short delay
                            setTimeout(() => {
                                window.location.href = data.redirect || '<?= site_url('auth/opportunities') ?>';
                            }, 1500);
                        } else {
                            // Show error notification
                            if (typeof showNotification === 'function') {
                                showNotification('error', data.message || 'Failed to create job opportunity. Please try again.');
                            } else {
                                showErrorMessage(data.message || 'Failed to create job opportunity. Please try again.');
                            }
                        }
                    }, 500);
                })
                .catch(error => {
                    console.error('Error caught:', error);
                    clearInterval(progressInterval);
                    progressModal.classList.add('hidden');
                    
                    // Handle validation errors
                    if (error.errors) {
                        console.log('Validation errors:', error.errors);
                        Object.keys(error.errors).forEach(field => {
                            showError(field, error.errors[field]);
                        });
                    } else {
                        // Show general error message
                        if (typeof showNotification === 'function') {
                            showNotification('error', error.message || 'An error occurred while creating the job opportunity. Please try again.');
                        } else {
                            showErrorMessage(error.message || 'An error occurred while creating the job opportunity. Please try again.');
                        }
                    }
                });
            });
            
            // Notification functions if not already available
            function showSuccessMessage(message) {
                if (document.getElementById('notification-area')) {
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            ${message}
                        </div>
                    `;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 5000);
                } else {
                    alert(message);
                }
            }
            
            function showErrorMessage(message) {
                if (document.getElementById('notification-area')) {
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            ${message}
                        </div>
                    `;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 5000);
                } else {
                    alert(message);
                }
            }

            // Helper functions for error display
            function showError(fieldId, message) {
                const errorElement = document.getElementById(`${fieldId}-error`);
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.classList.add('border-red-500');
                    }
                }
            }

            function hideError(fieldId) {
                const errorElement = document.getElementById(`${fieldId}-error`);
                if (errorElement) {
                    errorElement.classList.add('hidden');
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.classList.remove('border-red-500');
                    }
                }
            }

            function clearErrors() {
                document.querySelectorAll('[id$="-error"]').forEach(el => {
                    el.classList.add('hidden');
                });
                document.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500');
                });
            }
            
            function showSuccessMessage(message) {
                // Create and show success toast/alert
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(successDiv);
                lucide.createIcons();
                
                // Remove after 3 seconds
                setTimeout(() => {
                    successDiv.remove();
                }, 3000);
            }
            
            function showErrorMessage(message) {
                // Create and show error toast/alert
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg';
                errorDiv.innerHTML = `
                    <div class="flex items-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(errorDiv);
                lucide.createIcons();
                
                // Remove after 5 seconds
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            }
        });
    </script>
<?= $this->endSection() ?>