<?php ?>
<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto p-6 main-content-area">
        <div class="bg-white rounded-b-xl shadow-sm max-w-full">
            <!-- Header -->
            <div class="w-full flex justify-between items-center p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-2xl font-bold text-primary">Edit Course</h3>
                    <p class="text-gray-600">Update course details and manage content</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.location.href='<?= site_url('auth/courses/courses') ?>'" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        Back to Courses
                    </button>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex px-6" aria-label="Tabs">
                    <button onclick="switchTab('details')" id="tab-details" class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-cyan-500 text-cyan-600">
                        <i data-lucide="info" class="w-4 h-4 inline mr-2"></i>
                        Course Details
                    </button>
                    <button onclick="switchTab('curriculum')" id="tab-curriculum" class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i data-lucide="layers" class="w-4 h-4 inline mr-2"></i>
                        Curriculum
                    </button>
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

            <!-- Curriculum Tab -->
            <?= $this->include('backendV2/pages/courses/partials/curriculum_tab') ?>
        </div>

        <!-- Modals -->
        <?= $this->include('backendV2/pages/courses/partials/section_modal') ?>
        <?= $this->include('backendV2/pages/courses/partials/lecture_modal') ?>

        <!-- Progress Modal -->
        <div id="progressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-4"></div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Updating Course</h3>
                    <p class="text-sm text-gray-500">Please wait...</p>
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
    }

    // Setup all form handlers
    function setupFormHandlers() {
        // Course edit form
        $('#editCourseForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Handle is_paid - set price to 0 if free course
            if ($('input[name="is_paid"]:checked').val() === '0') {
                formData.set('price', '0');
                formData.set('discount_price', '0');
            }

            $('#progressModal').removeClass('hidden');

            $.ajax({
                url: '<?= site_url('auth/courses/edit/') ?>' + courseId,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#progressModal').addClass('hidden');
                    if (response.status === 'success') {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    $('#progressModal').addClass('hidden');
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
                    order_index: $('#section_order').val(),
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        closeSectionModal();
                        location.reload();
                    }
                },
                error: function(xhr) {
                    console.error('Failed to save section:', xhr.responseJSON?.message);
                }
            });
        });

        // Lecture form
        $('#addLectureForm').on('submit', function(e) {
            e.preventDefault();
            const lectureId = $('#lecture_id').val();
            const url = lectureId
                ? '<?= site_url("auth/courses/lectures/update") ?>/' + lectureId
                : '<?= site_url("auth/courses/lectures/create") ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    section_id: $('#lecture_section_id').val(),
                    title: $('#lecture_title').val(),
                    description: $('#lecture_description').val(),
                    video_url: $('#lecture_video_url').val(),
                    duration: $('#lecture_duration').val(),
                    order_index: $('#lecture_order').val(),
                    is_preview: $('#lecture_is_preview').is(':checked') ? 1 : 0,
                    is_free_preview: $('#lecture_is_free').is(':checked') ? 1 : 0,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        closeLectureModal();
                        location.reload();
                    }
                },
                error: function(xhr) {
                    console.error('Failed to save lecture:', xhr.responseJSON?.message);
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

    // Section modal functions
    window.showAddSectionModal = function() {
        $('#sectionModalTitle').text('Add Section');
        $('#sectionSubmitText').text('Add Section');
        $('#section_id').val('');
        $('#addSectionForm')[0].reset();
        $('#sectionModal').removeClass('hidden');
    };

    window.closeSectionModal = function() {
        $('#sectionModal').addClass('hidden');
        $('#addSectionForm')[0].reset();
    };

    window.editSection = function(sectionId) {
        console.log('Edit section called with ID:', sectionId);
        $.ajax({
            url: '<?= site_url("auth/courses/sections/get") ?>/' + sectionId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Section data received:', response);
                if (response.success && response.data) {
                    $('#sectionModalTitle').text('Edit Section');
                    $('#sectionSubmitText').text('Update Section');
                    $('#section_id').val(response.data.id);
                    $('#section_title').val(response.data.title);
                    $('#section_description').val(response.data.description);
                    $('#section_order').val(response.data.order_index);
                    $('#sectionModal').removeClass('hidden');
                    setTimeout(() => lucide.createIcons(), 100);
                } else {
                    console.error('Failed to load section data - no data in response');
                }
            },
            error: (xhr) => {
                console.error('AJAX error loading section data:', xhr);
                console.error('Status:', xhr.status, 'Response:', xhr.responseJSON);
            }
        });
    };

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

    // Lecture modal functions
    window.showAddLectureModal = function(sectionId) {
        $('#lectureModalTitle').text('Add Lecture');
        $('#lectureSubmitText').text('Add Lecture');
        $('#lecture_id').val('');
        $('#lecture_section_id').val(sectionId);
        $('#addLectureForm')[0].reset();
        $('#lecture_section_id').val(sectionId);
        $('#lectureModal').removeClass('hidden');
    };

    window.closeLectureModal = function() {
        $('#lectureModal').addClass('hidden');
        $('#addLectureForm')[0].reset();
    };

    window.editLecture = function(lectureId) {
        $.ajax({
            url: '<?= site_url("auth/courses/lectures/get") ?>/' + lectureId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    $('#lectureModalTitle').text('Edit Lecture');
                    $('#lectureSubmitText').text('Update Lecture');
                    $('#lecture_id').val(response.data.id);
                    $('#lecture_section_id').val(response.data.section_id);
                    $('#lecture_title').val(response.data.title);
                    $('#lecture_description').val(response.data.description);
                    $('#lecture_video_url').val(response.data.video_url);
                    $('#lecture_duration').val(response.data.duration);
                    $('#lecture_order').val(response.data.order_index);
                    $('#lecture_is_preview').prop('checked', response.data.is_preview == 1);
                    $('#lecture_is_free').prop('checked', response.data.is_free_preview == 1);
                    $('#lectureModal').removeClass('hidden');
                } else {
                    console.error('Failed to load lecture data');
                }
            },
            error: () => console.error('Failed to load lecture data')
        });
    };

    window.deleteLecture = function(lectureId) {
        if (!confirm('Are you sure you want to delete this lecture?')) return;

        $.ajax({
            url: '<?= site_url("auth/courses/lectures/delete") ?>/' + lectureId,
            type: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: (xhr) => console.error('Failed to delete lecture:', xhr.responseJSON?.message)
        });
    };
</script>
<?= $this->endSection() ?>
