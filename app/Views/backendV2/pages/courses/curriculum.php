<?php ?>
<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
Course Curriculum - KEWASNET
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto main-content-area">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Course Curriculum',
            'pageDescription' => 'Manage course sections and lectures',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses/courses')],
                ['label' => esc($course['title'] ?? 'Course'), 'url' => site_url('auth/courses/edit/' . $course['id'])],
                ['label' => 'Curriculum']
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/edit/' . $course['id']) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Course
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
            <div class="bg-white rounded-b-xl shadow-sm max-w-full">
                <div class="p-6 space-y-6">
                    <!-- Sections List -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Course Curriculum</h3>
                            <p class="text-sm text-gray-500">Organize your course into sections and lectures</p>
                        </div>
                        <button type="button" onclick="showAddSectionModal()" class="gradient-btn flex items-center px-6 py-3 rounded-full text-white hover:shadow-lg transition-all duration-300 whitespace-nowrap">
                            <i data-lucide="plus" class="w-5 h-5 mr-2 z-10"></i>
                            <span class="font-medium">Add a Curriculum</span>
                        </button>
                    </div>

                    <div id="sections-container" class="space-y-4">
                        <?php if (empty($sections)): ?>
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <i data-lucide="layers" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-gray-600">No sections yet. Add your first section to start building the curriculum.</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($sections as $section): ?>
                            <div class="border border-gray-200 rounded-lg p-4 bg-white section-item" data-section-id="<?= $section['id'] ?? '' ?>">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800"><?= esc($section['title']) ?></h4>
                                        <p class="text-sm text-gray-600 mt-1"><?= esc($section['description']) ?></p>
                                        <?php
                                        $lectureModel = new \App\Models\CourseLectureModel();
                                        $lectureCount = $lectureModel->where('section_id', $section['id'])
                                            ->where('deleted_at', null)
                                            ->countAllResults();
                                        $totalDuration = $lectureModel->where('section_id', $section['id'])
                                            ->where('deleted_at', null)
                                            ->selectSum('duration')
                                            ->get()
                                            ->getRow()
                                            ->duration ?? 0;
                                        ?>
                                        <div class="flex gap-4 mt-2 text-xs text-gray-500">
                                            <span><i data-lucide="book-open" class="w-3 h-3 inline"></i> <?= $lectureCount ?> lecture<?= $lectureCount != 1 ? 's' : '' ?></span>
                                            <span><i data-lucide="clock" class="w-3 h-3 inline"></i> <?= $totalDuration ?> min</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="editSection(this)" data-section-id="<?= $section['id'] ?? '' ?>" class="text-blue-600 hover:text-blue-800" title="Section ID: <?= $section['id'] ?? 'NONE' ?>">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="deleteSection('<?= $section['id'] ?>')" class="text-red-600 hover:text-red-800">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="toggleLectures('<?= $section['id'] ?>')" class="text-gray-600 hover:text-gray-800">
                                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>

                                <div id="lectures-<?= $section['id'] ?>" class="mt-4 pl-4 border-l-2 border-[#27aae0] hidden">
                                    <a href="<?= site_url('auth/courses/lectures/create?section_id=' . $section['id']) ?>" class="bg-[#27aae0] py-2 px-4 rounded-md text-sm text-white hover:text-white mb-2 flex items-center gap-1 w-fit">
                                        <i data-lucide="plus" class="w-3 h-3"></i> Add Lecture
                                    </a>
                                    <div class="space-y-2" id="lecture-list-<?= $section['id'] ?>">
                                        <?php
                                        $lectureModel = new \App\Models\CourseLectureModel();
                                        $lectures = $lectureModel->where('section_id', $section['id'])
                                            ->where('deleted_at', null)
                                            ->orderBy('created_at', 'ASC')
                                            ->findAll();

                                        if (empty($lectures)): ?>
                                            <p class="text-sm text-gray-500">No lectures yet</p>
                                        <?php else: ?>
                                            <?php foreach ($lectures as $lecture): ?>
                                            <div class="flex items-center justify-between p-2 bg-secondary/10 rounded hover:bg-gray-100 border border-secondary/50">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="play-circle" class="w-4 h-4 text-secondary"></i>
                                                    <span class="text-sm"><?= esc($lecture['title']) ?></span>
                                                    <?php if (isset($lecture['is_preview']) && $lecture['is_preview']): ?>
                                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Preview</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="<?= site_url('auth/courses/lectures/edit/' . $lecture['id']) ?>" class="text-blue-600 hover:text-blue-800">
                                                        <i data-lucide="edit" class="w-3 h-3"></i>
                                                    </a>
                                                    <button onclick="deleteLecture('<?= $lecture['id'] ?>')" class="text-red-600 hover:text-red-800">
                                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <?= $this->include('backendV2/pages/courses/partials/section_modal') ?>
    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const courseId = '<?= $course['id'] ?>';

    $(document).ready(function() {
        lucide.createIcons();
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

    window.deleteSection = function(sectionId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "All lectures in this section will also be deleted. You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url("auth/courses/sections/delete") ?>/' + sectionId,
                    type: 'POST',
                    data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Section has been deleted.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete section'
                            });
                        }
                    },
                    error: (xhr) => {
                        const errorMsg = xhr.responseJSON?.message || 'An error occurred while deleting the section';
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
