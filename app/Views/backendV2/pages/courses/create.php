<?php ?>
<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Create New Course',
            'pageDescription' => 'Fill in the details below to add a new course to the learning hub',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses/courses')],
                ['label' => 'Create']
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/courses') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Courses
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
        <div class="bg-white rounded-b-xl shadow-sm max-w-full">

            <!-- Main Form -->
            <form class="p-6 space-y-6" id="createCourseForm" method="POST" action="<?= site_url('auth/courses/create') ?>" enctype="multipart/form-data">

                <!-- Basic Information Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Basic Information</h3>

                    <!-- Course Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-dark mb-1">Course Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter course title" required>
                        <p class="mt-1 text-xs text-gray-500">A clear and descriptive title for your course</p>
                    </div>

                    <!-- Slug Field -->
                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                        <div class="flex w-full">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                                kewasnet.co.ke/learning-hub/courses/
                            </span>
                            <input type="text" id="slug" name="slug" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-generated from title. Edit if needed.</p>
                    </div>

                    <!-- Course Summary -->
                    <div class="mb-4">
                        <label for="summary" class="block text-sm font-medium text-dark mb-1">Course Summary <span class="text-red-500">*</span></label>
                        <textarea id="summary" name="summary" rows="3" maxlength="500" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="A brief summary of what students will learn" required></textarea>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>A compelling summary for course cards</span>
                            <span id="summary-count">0/500</span>
                        </div>
                    </div>
                </div>

                <!-- Course Details Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Course Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Category <span class="text-red-500">*</span></label>
                            <select name="category" id="category" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Level -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Course Level <span class="text-red-500">*</span></label>
                            <select name="level" id="level" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" required>
                                <option value="">Select Level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration" class="block text-sm font-medium text-dark mb-1">Duration (hours) <span class="text-red-500">*</span></label>
                            <input type="number" id="duration" name="duration" min="1" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="10" required>
                            <p class="mt-1 text-xs text-gray-500">Estimated course completion time</p>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Pricing</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Is Paid -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_paid" name="is_paid" value="1" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor rounded">
                            <label for="is_paid" class="ml-2 block text-sm text-dark font-medium">Paid Course</label>
                        </div>

                        <!-- Price -->
                        <div id="price-field" style="display: none;">
                            <label for="price" class="block text-sm font-medium text-dark mb-1">Price (KES) <span class="text-red-500">*</span></label>
                            <input type="number" id="price" name="price" min="0" step="0.01" value="0" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        </div>

                        <!-- Discount Price -->
                        <div id="discount-field" style="display: none;">
                            <label for="discount_price" class="block text-sm font-medium text-dark mb-1">Discount Price (KES)</label>
                            <input type="number" id="discount_price" name="discount_price" min="0" step="0.01" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Course Media</h3>

                    <!-- Course Image -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-dark mb-1">Course Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600">
                                        <span>Upload course image</span>
                                        <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                                <div id="image-preview" class="mt-4"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Video URL -->
                    <div>
                        <label for="preview_video_url" class="block text-sm font-medium text-dark mb-1">Preview Video URL (Optional)</label>
                        <input type="url" id="preview_video_url" name="preview_video_url" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="https://vimeo.com/...">
                        <p class="mt-1 text-xs text-gray-500">Vimeo URL for course preview video</p>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Course Description</h3>

                    <div>
                        <label for="description" class="block text-sm font-medium text-dark mb-1">Full Description <span class="text-red-500">*</span></label>
                        <textarea id="description" name="description" class="summernote-editor w-full px-4 py-3" placeholder="Write detailed course description..."></textarea>
                    </div>
                </div>

                <!-- Learning Goals Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Learning Goals</h3>

                    <div>
                        <label for="goals" class="block text-sm font-medium text-dark mb-1">What will students learn?</label>
                        <textarea id="goals" name="goals" rows="5" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="Enter learning goals (one per line)"></textarea>
                        <p class="mt-1 text-xs text-gray-500">List key learning outcomes, one per line</p>
                    </div>
                </div>

                <!-- Certificate & Resources -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Additional Options</h3>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="certificate" name="certificate" value="1" checked class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor rounded">
                            <label for="certificate" class="ml-2 block text-sm text-dark">Offer Certificate upon completion</label>
                        </div>

                        <div>
                            <label for="resources" class="block text-sm font-medium text-dark mb-1">Resources/Requirements</label>
                            <textarea id="resources" name="resources" rows="3" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="List any required materials or prerequisites"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Publishing Options -->
                <div class="pt-6">
                    <h3 class="text-lg font-medium text-dark mb-4">Publishing Options</h3>

                    <div class="flex flex-col space-y-3">
                        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                            <input type="radio" name="status" value="0" checked class="h-4 w-4 text-secondary focus:ring-secondary">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-dark">Draft</span>
                                <p class="text-xs text-gray-500">Save for later editing</p>
                            </div>
                        </label>
                        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                            <input type="radio" name="status" value="1" class="h-4 w-4 text-secondary focus:ring-secondary">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-dark">Published</span>
                                <p class="text-xs text-gray-500">Make it live immediately</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- CSRF Token -->
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-borderColor">
                    <button type="button" onclick="window.location.href='<?= site_url('auth/courses/courses') ?>'" class="px-8 py-3 rounded-[50px] text-gray-700 bg-gray-100 hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="gradient-btn rounded-[50px] px-8 text-white flex justify-center items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5 z-10"></i>
                        <span>Create Course</span>
                    </button>
                </div>
            </form>
        </div>
        </div>

        <!-- Progress Modal -->
        <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4"></div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Creating Course</h3>
                    <p class="text-sm text-gray-500 mb-4">Please wait while we process your course...</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                        <div id="uploadProgress" class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Select2 with proper dropdown configuration
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            minimumResultsForSearch: Infinity
        });

        // Initialize Summernote
        $('.summernote-editor').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Auto-generate slug
        $('#title').on('input', function() {
            const title = $(this).val();
            const slug = title.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            $('#slug').val(slug);
        });

        // Summary character counter
        $('#summary').on('input', function() {
            const length = $(this).val().length;
            $('#summary-count').text(`${length}/500`);
        });

        // Toggle pricing fields
        $('#is_paid').on('change', function() {
            if ($(this).is(':checked')) {
                $('#price-field, #discount-field').show();
                $('#price').prop('required', true);
            } else {
                $('#price-field, #discount-field').hide();
                $('#price').prop('required', false);
                $('#price, #discount_price').val('0');
            }
        });

        // Image preview
        $('#image').on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').html(`
                        <img src="${e.target.result}" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200">
                    `);
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        $('#createCourseForm').on('submit', function(e) {
            e.preventDefault();

            // Show progress modal
            $('#progressModal').show();

            const formData = new FormData(this);

            $.ajax({
                url: '<?= site_url('auth/courses/create') ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#progressModal').hide();
                    if (response.status === 'success') {
                        window.location.href = response.redirect_url || '<?= site_url('auth/courses/courses') ?>';
                    }
                },
                error: function(xhr) {
                    $('#progressModal').hide();
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        console.error('Validation errors:', response.errors);
                    } else {
                        console.error('Failed to create course:', response?.message);
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
