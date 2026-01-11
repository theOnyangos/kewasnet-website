<?php ?>
<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto main-content-area">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Edit Course',
            'pageDescription' => 'Update course details and manage content',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses/courses')],
                ['label' => esc($course['title'] ?? 'Edit')]
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/courses') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Courses
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
            <div class="bg-white rounded-b-xl shadow-sm max-w-full">

                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex px-6" aria-label="Tabs">
                        <button onclick="switchTab('details')" id="tab-details" class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-cyan-500 text-cyan-600">
                            <i data-lucide="info" class="w-4 h-4 inline mr-2"></i>
                            Course Details
                        </button>
                        
                        <a href="<?= site_url('auth/courses/curriculum/' . $course['id']) ?>" id="tab-curriculum" class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i data-lucide="layers" class="w-4 h-4 inline mr-2"></i>
                            Curriculum
                        </a>
                    </nav>
                </div>

                <!-- Course Details Tab -->
                <div id="content-details" class="course-tab-panel">
                    <form class="p-6 space-y-6" id="editCourseForm" method="POST" enctype="multipart/form-data">
                        <?= $this->include('backendV2/pages/courses/partials/basic_info') ?>
                        <?= $this->include('backendV2/pages/courses/partials/course_details') ?>
                        <?= $this->include('backendV2/pages/courses/partials/pricing') ?>
                        <?= $this->include('backendV2/pages/courses/partials/media') ?>
                        <?= $this->include('backendV2/pages/courses/partials/description') ?>
                        <?= $this->include('backendV2/pages/courses/partials/learning_goals') ?>
                        <?= $this->include('backendV2/pages/courses/partials/options') ?>
                        <?= $this->include('backendV2/pages/courses/partials/publishing') ?>

                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                        <div class="flex justify-end space-x-4">
                            <button type="submit" class="gradient-btn rounded-[50px] px-8 py-3 text-white flex justify-center items-center gap-2 hover:shadow-lg transition-all duration-300">
                                <i data-lucide="save" class="w-5 h-5 z-10"></i>
                                <span class="font-medium">Update Course</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- Modals -->
        <?= $this->include('backendV2/pages/courses/partials/section_modal') ?>

        <!-- Progress Modal -->
        <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4"></div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Updating Course</h3>
                    <p class="text-sm text-gray-500 mb-4">Please wait while we update your course...</p>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                        <div id="uploadProgress" class="bg-primary h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    
                    <p class="text-sm font-medium text-gray-700">
                        <span id="progressPercent">0</span>% complete
                    </p>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    const courseId = '<?= $course['id'] ?>';

    $(document).ready(function() {
        initializePage();
        setupFormHandlers();
        setupEventListeners();
    });

    // Initialize all page components
    function initializePage() {
        lucide.createIcons();

        // Initialize Select2
        $('.select2').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $(this).data('placeholder') || $(this).find('option:first').text(),
                minimumResultsForSearch: Infinity,
                allowClear: false
            });
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
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    }

    // Setup all event listeners
    function setupEventListeners() {
        updateSummaryCount();
        $('#summary').on('input', updateSummaryCount);

        // Auto-generate slug from title
        $('#title').on('input', function() {
            const title = $(this).val();
            const slug = title.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            $('#slug').val(slug);
        });

        $('input[name="is_paid"]').on('change', function() {
            if ($(this).val() === '1') {
                $('#price-field, #discount-field').show();
                $('#price').removeAttr('required');
            } else {
                $('#price-field, #discount-field').hide();
                $('#price, #discount_price').val('0');
                $('#price').removeAttr('required');
            }
        });

        // Image preview
        $('#image').on('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = $('#image-preview');
            previewContainer.empty();
            
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.html(`
                        <img src="${e.target.result}" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200">
                    `);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Setup all form handlers
    function setupFormHandlers() {
        // Remove image button handler
        $('#removeImageBtn').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will remove the current course image. You can upload a new image below.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#removeImageFlag').val('1');
                    $('#currentImage').closest('.mb-4').find('img').fadeOut(300, function() {
                        $(this).closest('.mb-4').find('> div').fadeOut(300);
                    });
                    $('#removeImageBtn').fadeOut(300);
                    Swal.fire({
                        icon: 'success',
                        title: 'Image marked for removal',
                        text: 'The image will be removed when you save the form.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Course edit form
        $('#editCourseForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const $progressBar = $('#uploadProgress');
            const $progressPercent = $('#progressPercent');
            const $progressModal = $('#progressModal');

            // Handle is_paid - set price to 0 if free course
            if ($('input[name="is_paid"]:checked').val() === '0') {
                formData.set('price', '0');
                formData.set('discount_price', '0');
            }

            // Reset progress bar
            $progressBar.css('width', '0%');
            $progressPercent.text('0');
            $progressModal.removeClass('hidden');

            $.ajax({
                url: '<?= site_url('auth/courses/edit/') ?>' + courseId,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 90); // Cap at 90% until complete
                            $progressBar.css('width', percent + '%');
                            $progressPercent.text(percent);
                        }
                    }, false);
                    
                    return xhr;
                },
                success: function(response) {
                    $progressBar.css('width', '100%');
                    $progressPercent.text('100');
                    
                    setTimeout(() => {
                        $progressModal.addClass('hidden');
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Course updated successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    }, 500);
                },
                error: function(xhr) {
                    $progressModal.addClass('hidden');
                    const errorMsg = xhr.responseJSON?.message || 'Failed to update course';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                    console.error('Failed to update course:', xhr.responseJSON?.message);
                }
            });
        });

        // Section form
        $('#addSectionForm').on('submit', function(e) {
            e.preventDefault();
            const sectionId = $('#section_id').val();
            const url = sectionId
                ? '<?= site_url("auth/courses/sections/update") ?>/' + sectionId
                : '<?= site_url("auth/courses/sections/create") ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    course_id: courseId,
                    title: $('#section_title').val(),
                    description: $('#section_description').val(),
                    quiz_id: $('#section_quiz_id').val(),
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Section saved successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            closeSectionModal();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save section'
                        });
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'An error occurred while saving the section';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                }
            });
        });
    }

    // Utility functions
    function updateSummaryCount() {
        $('#summary-count').text($('#summary').val().length + '/500');
    }

    // Tab functions
    window.switchTab = function(tab) {
        $('.tab-button').removeClass('active border-cyan-500 text-cyan-600').addClass('border-transparent text-gray-500');
        $('.course-tab-panel').addClass('hidden');
        $('#tab-' + tab).removeClass('border-transparent text-gray-500').addClass('active border-cyan-500 text-cyan-600');
        $('#content-' + tab).removeClass('hidden');
        lucide.createIcons();
    };
    
    // Re-initialize icons after image removal
    $(document).ready(function() {
        lucide.createIcons();
    });
    
    // Learning Goals Management
    <?php
    // Parse existing goals for JavaScript
    $existingGoalsForJs = [];
    if (!empty($course['goals'])) {
        if (is_array($course['goals'])) {
            $existingGoalsForJs = $course['goals'];
        } elseif (is_string($course['goals'])) {
            $decoded = json_decode($course['goals'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $existingGoalsForJs = $decoded;
            } else {
                $existingGoalsForJs = array_filter(array_map('trim', explode("\n", $course['goals'])));
            }
        }
    }
    if (empty($existingGoalsForJs)) {
        $existingGoalsForJs = [''];
    }
    ?>
    let goalCount = <?= count($existingGoalsForJs) ?>;
    
    // Add new goal field
    $('#addGoalBtn').on('click', function() {
        const goalHtml = `
            <div class="goal-item flex gap-2 items-start" data-index="${goalCount}">
                <div class="flex-1">
                    <input type="text" name="goals[]" value="" placeholder="Enter a learning goal (e.g., Understand the fundamentals of X)" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                </div>
                <button type="button" class="remove-goal-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors flex-shrink-0" title="Remove this goal">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        $('#goalsContainer').append(goalHtml);
        goalCount++;
        
        // Show all remove buttons if more than one goal
        if ($('#goalsContainer .goal-item').length > 1) {
            $('.remove-goal-btn').show();
        }
        
        lucide.createIcons();
    });
    
    // Remove goal field
    $(document).on('click', '.remove-goal-btn', function() {
        const $goalItem = $(this).closest('.goal-item');
        $goalItem.fadeOut(300, function() {
            $(this).remove();
            
            // Hide all remove buttons if only one goal remains
            if ($('#goalsContainer .goal-item').length <= 1) {
                $('.remove-goal-btn').hide();
            }
        });
    });
    
    // Resources/Requirements Management
    <?php
    // Parse existing resources for JavaScript
    $existingResourcesForJs = [];
    if (!empty($course['resources'])) {
        if (is_array($course['resources'])) {
            $existingResourcesForJs = $course['resources'];
        } elseif (is_string($course['resources'])) {
            $decoded = json_decode($course['resources'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $existingResourcesForJs = $decoded;
            } else {
                $existingResourcesForJs = array_filter(array_map('trim', preg_split('/[\n,]+/', $course['resources'])));
            }
        }
    }
    if (empty($existingResourcesForJs)) {
        $existingResourcesForJs = [''];
    }
    ?>
    let resourceCount = <?= count($existingResourcesForJs) ?>;
    
    // Add new resource field
    $('#addResourceBtn').on('click', function() {
        const resourceHtml = `
            <div class="resource-item flex gap-2 items-start" data-index="${resourceCount}">
                <div class="flex-1">
                    <input type="text" name="resources[]" value="" placeholder="Enter a resource or requirement (e.g., Internet connection, Laptop with X software)" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                </div>
                <button type="button" class="remove-resource-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors flex-shrink-0" title="Remove this resource">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        $('#resourcesContainer').append(resourceHtml);
        resourceCount++;
        
        // Show all remove buttons if more than one resource
        if ($('#resourcesContainer .resource-item').length > 1) {
            $('.remove-resource-btn').show();
        }
        
        lucide.createIcons();
    });
    
    // Remove resource field
    $(document).on('click', '.remove-resource-btn', function() {
        const $resourceItem = $(this).closest('.resource-item');
        $resourceItem.fadeOut(300, function() {
            $(this).remove();
            
            // Hide all remove buttons if only one resource remains
            if ($('#resourcesContainer .resource-item').length <= 1) {
                $('.remove-resource-btn').hide();
            }
        });
    });

    // Section modal functions
    window.showAddSectionModal = function() {
        $('#sectionModalTitle').text('Add Curriculum (Section)');
        $('#sectionSubmitText').text('Add Curriculum (Section)');
        $('#section_id').val('');
        $('#addSectionForm')[0].reset();
        loadQuizzes();
        $('#sectionModal').removeClass('hidden');
    };

    window.closeSectionModal = function() {
        $('#sectionModal').addClass('hidden');
        $('#addSectionForm')[0].reset();
    };

    window.editSection = function(element) {
        // Find the button element (in case the icon was clicked)
        const button = element.closest('button') || element;
        const sectionId = button.dataset.sectionId || button.getAttribute('data-section-id');
        
        if (!sectionId) {
            alert('Error: No section ID provided');
            return;
        }
        
        // Load section data and quizzes in parallel
        Promise.all([
            loadQuizzes(),
            $.ajax({
                url: '<?= site_url("auth/courses/sections/get") ?>/' + sectionId,
                type: 'GET',
                dataType: 'json'
            })
        ]).then(([quizzesResult, sectionResponse]) => {
            if (sectionResponse.success && sectionResponse.data) {
                $('#sectionModalTitle').text('Edit Curriculum (Section)');
                $('#sectionSubmitText').text('Update Curriculum (Section)');
                $('#section_id').val(sectionResponse.data.id);
                $('#section_title').val(sectionResponse.data.title);
                $('#section_description').val(sectionResponse.data.description);
                
                // Set quiz value and trigger Select2 update
                const quizId = sectionResponse.data.quiz_id || '';
                $('#section_quiz_id').val(quizId).trigger('change');
                
                $('#sectionModal').removeClass('hidden');
                setTimeout(() => lucide.createIcons(), 100);
            } else {
                console.error('Failed to load section data');
                alert('Failed to load section data');
            }
        }).catch((error) => {
            console.error('Error loading section:', error);
            alert('Failed to load section data. Please try again.');
        });
    };
    
    // Load available quizzes
    function loadQuizzes() {
        return $.ajax({
            url: '<?= site_url("auth/courses/quizzes/list") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const select = $('#section_quiz_id');
                    select.html('<option value="">No Quiz</option>');
                    response.data.forEach(quiz => {
                        select.append(`<option value="${quiz.id}">${quiz.title}</option>`);
                    });
                }
            },
            error: function(xhr) {
                console.error('Failed to load quizzes:', xhr);
            }
        });
    }

    window.deleteSection = function(sectionId) {
        if (!confirm('Are you sure you want to delete this section? All lectures in this section will also be deleted.')) return;

        $.ajax({
            url: '<?= site_url("auth/courses/sections/delete") ?>/' + sectionId,
            type: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: (xhr) => console.error('Failed to delete section:', xhr.responseJSON?.message)
        });
    };

    window.toggleLectures = function(sectionId) {
        $('#lectures-' + sectionId).toggleClass('hidden');
        lucide.createIcons();
    };

    window.deleteLecture = function(lectureId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url("auth/courses/lectures/delete") ?>/' + lectureId,
                    type: 'POST',
                    data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Lecture has been deleted.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete lecture'
                            });
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'An error occurred while deleting the lecture';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    };
</script>
<?= $this->endSection() ?>
