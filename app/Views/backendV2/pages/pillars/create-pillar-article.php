<?php 
    //
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
            'pageTitle' => 'Create Pillar Article',
            'pageDescription' => 'Create a new article for a knowledge pillar',
            'breadcrumbs' => [
                ['label' => 'Pillars', 'url' => base_url('auth/pillars')],
                ['label' => 'Articles', 'url' => base_url('auth/pillars/articles')],
                ['label' => 'Create']
            ],
            'bannerActions' => '<a href="' . base_url('auth/pillars/articles') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Articles
            </a>'
        ]) ?>

        <!-- Pillar Article Form Section -->
        <div class="bg-white rounded-b-xl shadow-sm max-w-full pillar-form-container mx-5 mb-5">
            <div class="w-full flex justify-between items-center p-6 border-b border-gray-200">
                <div class="">
                    <h3 class="text-2xl font-bold text-primary">Create New Pillar Article</h3>
                    <p class="text-gray-600">Fill in the details below to publish a new pillar article</p>
                </div>

                <div class="mt-4 md:mt-0">
                    <button onclick="window.location.href='<?= site_url('auth/pillars/articles') ?>'" class="flex justify-center items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                        Back to Resources
                    </button>
                </div>
            </div>

            <!-- Main Form -->
            <form class="p-6 space-y-6" id="createPillarArticleForm" method="POST" action="<?= site_url('auth/pillars/create-pillar-article') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- Basic Information Section -->
                <div class="pb-6">
                    <div class="mb-5 border-l-8 border-primary pl-3">
                        <h4 class="text-lg font-medium text-dark">Basic Information</h4>
                        <p class="text-sm text-gray-600">Please provide the necessary information to create a new pillar article.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pillar Selection -->
                        <div>
                            <label for="pillar_id" class="block text-sm font-medium text-dark mb-1">Select Pillar <span class="text-red-500">*</span></label>
                            <select id="pillar_id" name="pillar_id" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent select2">
                                <option value="">Choose a pillar...</option>
                                <?php if (!empty($pillars)): ?>
                                    <?php foreach ($pillars as $pillar): ?>
                                        <option value="<?= $pillar['id'] ?>"><?= esc($pillar['title']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Resource Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-dark mb-1">Resource Category</label>
                            <select id="category_id" name="category_id" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent select2">
                                <option value="">Select category...</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category->id ?>"><?= esc($category->name) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Document Type -->
                        <div>
                            <label for="document_type_id" class="block text-sm font-medium text-dark mb-1">Document Type <span class="text-red-500">*</span></label>
                            <select id="document_type_id" name="document_type_id" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent select2">
                                <option value="">Choose document type...</option>
                                <?php if (!empty($documentTypes)): ?>
                                    <?php foreach ($documentTypes as $docType): ?>
                                        <option value="<?= $docType->id ?>" data-color="<?= esc($docType->color) ?>"><?= esc($docType->name) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Publication Year -->
                        <div>
                            <label for="publication_year" class="block text-sm font-medium text-dark mb-1">Publication Year</label>
                            <input type="number" id="publication_year" name="publication_year" min="1900" max="<?= date('Y') + 1 ?>" value="<?= date('Y') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="e.g., <?= date('Y') ?>">
                        </div>
                    </div>
                </div>

                <!-- Article Content Section -->
                <div class="pb-6">
                    <div class="mb-5 border-l-8 border-primary pl-3">
                        <h4 class="text-lg font-medium text-dark">Article Content</h4>
                        <p class="text-sm text-gray-600">Please provide the content for the article below.</p>
                    </div>

                    <!-- Title Field -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-dark mb-1">Article Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" maxlength="255" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter article title">
                    </div>

                    <!-- Slug Field -->
                    <div class="mb-6">
                        <label for="articleSlug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                        <div class="flex w-full">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                                kewasnet.co.ke/resources/
                            </span>
                            <input type="text" id="articleSlug" name="slug" maxlength="100" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">This will be the URL for your article. It should be unique and descriptive.</p>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-dark mb-1">Description</label>
                        <div class="relative">
                            <textarea id="description" name="description" rows="4" maxlength="1000" class="w-full px-4 py-3 pl-10 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="A brief description of this article"></textarea>
                            <div class="!absolute !top-3 !left-0 pl-3 flex items-start pointer-events-none">
                                <i data-lucide="file-text" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Write a brief description that summarizes this article</span>
                            <span id="descriptionCount">0/1000</span>
                        </div>
                    </div>

                    <!-- Content Field -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-dark mb-1">Content</label>
                        <div class="rounded-lg">
                            <textarea id="content" name="content" class="summernote-editor w-full px-4 py-3 focus:outline-none" placeholder="Write detailed content about this pillar..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Media Upload Section -->
                <div class="pb-6">
                    <div class="mb-5 border-l-8 border-primary pl-3">
                        <h4 class="text-lg font-medium text-dark">Media & Files</h4>
                        <p class="text-sm text-gray-600">Upload any relevant media files for this article.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Featured Image Upload -->
                        <div>
                            <label for="featured_image" class="block text-sm font-medium text-dark mb-1">Featured Image</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-lg hover:border-secondary transition-colors">
                                <div class="space-y-1 text-center">
                                    <div id="imagePreviewContainer" class="hidden">
                                        <img id="imagePreview" src="" alt="Preview" class="mx-auto h-32 w-auto rounded-lg shadow-sm">
                                        <button type="button" id="removeImage" class="mt-2 text-sm text-red-600 hover:text-red-500">Remove</button>
                                    </div>
                                    <div id="imageUploadPlaceholder">
                                        <i data-lucide="image" class="mx-auto h-12 w-12 text-gray-400"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="featured_image" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-secondary">
                                                <span>Upload a file</span>
                                                <input id="featured_image" name="image_file" type="file" accept="image/*" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resource File Upload -->
                        <div>
                            <label for="resource_file" class="block text-sm font-medium text-dark mb-1">Resource File</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-lg hover:border-secondary transition-colors">
                                <div class="space-y-1 text-center">
                                    <div id="filePreviewContainer" class="hidden">
                                        <div id="filePreview" class="mx-auto p-4 bg-gray-50 rounded-lg">
                                            <i data-lucide="file" class="mx-auto h-8 w-8 text-gray-600"></i>
                                            <p id="fileName" class="text-sm text-gray-600 mt-2"></p>
                                            <p id="fileSize" class="text-xs text-gray-500"></p>
                                        </div>
                                        <button type="button" id="removeFile" class="mt-2 text-sm text-red-600 hover:text-red-500">Remove</button>
                                    </div>
                                    <div id="fileUploadPlaceholder">
                                        <i data-lucide="upload" class="mx-auto h-12 w-12 text-gray-400"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="resource_file" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-secondary">
                                                <span>Upload resource file</span>
                                                <input id="resource_file" name="resource_file" type="file" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, DOC, XLS, PPT up to 50MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contributors Section -->
                <div class="pb-6">
                    <div class="mb-5 border-l-8 border-primary pl-3">
                        <h4 class="text-lg font-medium text-dark">Contributors</h4>
                        <p class="text-sm text-gray-600">Add contributors to this article.</p>
                    </div>

                    <div id="contributorsContainer">
                        <div class="contributor-item grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-dashed border-borderColor rounded-lg">
                            <div>
                                <label for="contributor_name_0" class="block text-sm font-medium text-dark mb-1">Name</label>
                                <input type="text" id="contributor_name_0" name="contributors[0][name]" class="w-full px-3 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Contributor name">
                            </div>
                            <div>
                                <label for="contributor_organization_0" class="block text-sm font-medium text-dark mb-1">Organization</label>
                                <input type="text" id="contributor_organization_0" name="contributors[0][organization]" class="w-full px-3 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Organization name">
                            </div>
                            <div>
                                <label for="contributor_email_0" class="block text-sm font-medium text-dark mb-1">Email</label>
                                <input type="email" id="contributor_email_0" name="contributors[0][email]" class="w-full px-3 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="email@example.com">
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="addContributor" class="mt-4 flex items-center px-4 py-2 border border-secondary text-secondary rounded-lg hover:bg-secondaryShades-50 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Another Contributor
                    </button>
                </div>

                <!-- SEO Section -->
                <div class="pb-6">
                    <div class="mb-5 border-l-8 border-primary pl-3">
                        <h4 class="text-lg font-medium text-dark">SEO Settings</h4>
                        <p class="text-sm text-gray-600">Configure the SEO settings for this article.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-dark mb-1">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" maxlength="60" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Title for search engines">
                            <p class="mt-1 text-xs text-gray-500">Recommended: 50-60 characters</p>
                        </div>
                        
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-dark mb-1">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" rows="3" maxlength="160" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Description for search engines"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters</p>
                        </div>
                    </div>
                </div>

                <!-- Publication Settings Section -->
                <div class="pt-6">
                    <div class="mb-5 border-l-8 border-primary pl-3">
                        <h4 class="text-lg font-medium text-dark">Publication Settings</h4>
                        <p class="text-sm text-gray-600">Configure the publication settings for this article.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Featured Status -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Featured Article</label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_featured" value="0" checked class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Normal</span>
                                        <p class="text-xs text-gray-500">Regular article</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_featured" value="1" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Featured</span>
                                        <p class="text-xs text-gray-500">Highlight on homepage</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Publication Status -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Publication Status</label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_published" value="0" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Draft</span>
                                        <p class="text-xs text-gray-500">Save as draft</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="is_published" value="1" checked class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Published</span>
                                        <p class="text-xs text-gray-500">Article is live</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="gradient-btn flex justify-center items-center gap-2 px-8 py-3 bg-primary text-white rounded-[50px] font-medium hover:bg-primaryShades-600 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors">
                        <i data-lucide="save" class="w-5 h-5 z-10"></i>
                        <span>Create Article</span>
                    </button>
                    
                    <button type="button" id="saveDraft" class="bg-primaryShades-50 hover:bg-primaryShades-100 flex-1 sm:flex-none px-8 py-3 text-primary rounded-[50px] font-medium focus:outline-none transition-colors">
                        <i data-lucide="file-text" class="w-5 h-5 mr-2 inline"></i>
                        Save as Draft
                    </button>
                    
                    <button type="button" onclick="window.location.href='<?= site_url('auth/pillars') ?>'" class="bg-primaryShades-50 hover:bg-primaryShades-100 flex-1 sm:flex-none px-8 py-3 text-primary rounded-[50px] font-medium focus:outline-none transition-colors">
                        <i data-lucide="x" class="w-5 h-5 mr-2 inline"></i>
                        Cancel
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
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Creating Article</h3>
                    <p class="text-sm text-gray-500 mb-4" id="progressText">Please wait while we create your pillar article...</p>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 0%" id="progressBar"></div>
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
        // Form elements
        const form                  = document.getElementById('createPillarArticleForm');
        const titleInput            = document.getElementById('title');
        const slugInput             = document.getElementById('articleSlug');
        const descriptionTextarea   = document.getElementById('description');
        const descriptionCount      = document.getElementById('descriptionCount');
        const progressModal         = document.getElementById('progressModal');
        const progressBar           = document.getElementById('progressBar');
        const progressText          = document.getElementById('progressText');

        // Auto-generate slug from title
        titleInput.addEventListener('input', function() {
            const title = this.value;
            const slug = title
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/-+/g, '-')      // Replace multiple hyphens with single
                .substring(0, 100);       // Limit to 100 characters
            slugInput.value = slug;
        });
        
        // Description character counter
        descriptionTextarea.addEventListener('input', function() {
            const count = this.value.length;
            descriptionCount.textContent = `${count}/1000`;
            
            if (count > 1000) {
                descriptionCount.classList.add('text-red-500');
            } else {
                descriptionCount.classList.remove('text-red-500');
            }
        });
        
        // Image upload preview
        const imageInput             = document.getElementById('featured_image');
        const imagePreview           = document.getElementById('imagePreview');
        const imagePreviewContainer  = document.getElementById('imagePreviewContainer');
        const imageUploadPlaceholder = document.getElementById('imageUploadPlaceholder');
        const removeImageBtn         = document.getElementById('removeImage');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('hidden');
                    imageUploadPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
        
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreviewContainer.classList.add('hidden');
            imageUploadPlaceholder.classList.remove('hidden');
        });
        
        // File upload preview
        const fileInput              = document.getElementById('resource_file');
        const filePreview            = document.getElementById('filePreview');
        const filePreviewContainer   = document.getElementById('filePreviewContainer');
        const fileUploadPlaceholder  = document.getElementById('fileUploadPlaceholder');
        const fileName               = document.getElementById('fileName');
        const fileSize               = document.getElementById('fileSize');
        const removeFileBtn          = document.getElementById('removeFile');
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreviewContainer.classList.remove('hidden');
                fileUploadPlaceholder.classList.add('hidden');
            }
        });
        
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreviewContainer.classList.add('hidden');
            fileUploadPlaceholder.classList.remove('hidden');
        });
        
        // Contributors management
        let contributorCount = 1;
        const contributorsContainer = document.getElementById('contributorsContainer');
        const addContributorBtn = document.getElementById('addContributor');
        
        addContributorBtn.addEventListener('click', function() {
            const contributorHtml = `
                <div class="contributor-item grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-dashed border-borderColor rounded-lg mt-4">
                    <div>
                        <label for="contributor_name_${contributorCount}" class="block text-sm font-medium text-dark mb-1">Name</label>
                        <input type="text" id="contributor_name_${contributorCount}" name="contributors[${contributorCount}][name]" class="w-full px-3 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Contributor name">
                    </div>
                    <div>
                        <label for="contributor_organization_${contributorCount}" class="block text-sm font-medium text-dark mb-1">Organization</label>
                        <input type="text" id="contributor_organization_${contributorCount}" name="contributors[${contributorCount}][organization]" class="w-full px-3 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Organization name">
                    </div>
                    <div class="flex">
                        <div class="flex-1">
                            <label for="contributor_email_${contributorCount}" class="block text-sm font-medium text-dark mb-1">Email</label>
                            <input type="email" id="contributor_email_${contributorCount}" name="contributors[${contributorCount}][email]" class="w-full px-3 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="email@example.com">
                        </div>
                        <div class="ml-2 flex items-end">
                            <button type="button" class="remove-contributor px-3 py-2 text-red-600 hover:text-red-500" title="Remove contributor">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            contributorsContainer.insertAdjacentHTML('beforeend', contributorHtml);
            contributorCount++;
            
            // Re-initialize Lucide icons
            lucide.createIcons();
        });
        
        // Remove contributor functionality
        contributorsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-contributor')) {
                const contributorItem = e.target.closest('.contributor-item');
                if (contributorsContainer.children.length > 1) {
                    contributorItem.remove();
                }
            }
        });
        
        // Save as draft functionality
        const saveDraftBtn = document.getElementById('saveDraft');
        saveDraftBtn.addEventListener('click', function() {
            // Set publication status to draft
            document.querySelector('input[name="is_published"][value="0"]').checked = true;
            form.submit();
        });
        
        // Form submission
        $(form).on('submit', function(e) {
            e.preventDefault();
            
            // Show progress modal with fadeIn
            $(progressModal).removeClass('hidden').hide().fadeIn(300);
            $(progressModal).find('.flex').css('display', 'flex');
            let progress = 0;
            
            // Simulate progress
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                $(progressBar).animate({ width: progress + '%' }, 200);
            }, 200);
            
            // Create FormData object
            const formData = new FormData(this);
            
            // Submit via AJAX using jQuery
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    clearInterval(progressInterval);
                    if (data.status === 'success') {
                        $(progressBar).animate({ width: '100%' }, 300);
                        setTimeout(() => {
                            $(progressModal).fadeOut(400, function() {
                                $(this).addClass('hidden');
                            });
                            showNotification('success', 'Article created successfully!');
                            setTimeout(() => {
                                window.location.href = data.redirect || '<?= site_url('auth/pillars') ?>';
                            }, 1500);
                        }, 500);
                    } else if (data.errors) {
                        $(progressModal).fadeOut(400, function() {
                            $(this).addClass('hidden');
                        });
                        showFormErrors(data.errors);
                        showNotification('error', 'Please fix the errors and try again');
                    } else {
                        $(progressModal).fadeOut(400, function() {
                            $(this).addClass('hidden');
                        });
                        showNotification('error', data.message || 'An unexpected error occurred. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    clearInterval(progressInterval);
                    $(progressModal).fadeOut(400, function() {
                        $(this).addClass('hidden');
                    });

                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    if (Object.keys(errors).length > 0) {
                        showFormErrors(errors);
                        showNotification('error', 'Please fix the errors and try again');
                    } else {
                        showNotification('error', response.message || 'Failed to create category');
                    }
                }
            });
        });

        function showFormErrors(errors) {
            clearFormErrors();
            
            $.each(errors, function(fieldName, message) {
                const $field = $('[name="' + fieldName + '"]');
                
                if ($field.length) {
                    $field.addClass('is-invalid');
                    
                    const $errorDiv = $('<div>', {
                        class: 'invalid-feedback text-red-500 text-xs mt-1',
                        text: message
                    });
                    
                    $field.after($errorDiv);
                    
                    if ($('.is-invalid').first().is($field)) {
                        $field.focus();
                    }
                }
            });
        }

        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }
    });
</script>
<?= $this->endSection() ?>