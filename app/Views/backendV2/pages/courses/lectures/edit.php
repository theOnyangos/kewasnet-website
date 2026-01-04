<?php ?>
<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
Edit Lecture - <?= $course['title'] ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto main-content-area">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Edit Lecture',
            'pageDescription' => 'Update lecture details for ' . $section['title'],
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses/courses')],
                ['label' => esc($course['title']), 'url' => site_url('auth/courses/edit/' . $course['id'])],
                ['label' => 'Edit Lecture']
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/edit/' . $course['id']) . '#curriculum" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Course
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
            <div class="bg-white rounded-xl shadow-sm max-w-full mx-auto">
                <form id="editLectureForm" class="p-8">
                    <input type="hidden" name="lecture_id" value="<?= $lecture['id'] ?>">
                    <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                    <div class="space-y-6">
                        <!-- Section Info -->
                        <div class="bg-secondary/20 border border-[#27aae0] rounded-lg p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i data-lucide="info" class="w-10 h-10 text-secondary mt-0.5"></i>
                                <div>
                                    <h4 class="font-semibold text-secondary mb-1">Section: <?= esc($section['title']) ?></h4>
                                    <?php if (!empty($section['description'])): ?>
                                        <p class="text-sm text-secondary"><?= esc($section['description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Lecture Title -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Lecture Title <span class="text-red-500">*</span></label>
                            <input type="text" id="lecture_title" name="title" value="<?= esc($lecture['title']) ?>" placeholder="e.g., Understanding HTML Tags and Attributes" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" required>
                            <p class="text-xs text-gray-500 mt-1">Enter a clear, descriptive title for this lecture</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Description</label>
                            <textarea id="lecture_description" name="description" rows="4" placeholder="What students will learn in this lecture..." class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary resize-none"><?= esc($lecture['description'] ?? '') ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Provide a brief overview of the lecture content</p>
                        </div>

                        <!-- Video URL & Duration -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-2">Video URL (YouTube/Vimeo)</label>
                                <input type="url" id="lecture_video_url" name="video_url" value="<?= esc($lecture['video_url'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                                <p class="text-xs text-gray-500 mt-1">Paste the full URL of your video</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-2">Duration (minutes)</label>
                                <input type="number" id="lecture_duration" name="duration" value="<?= $lecture['duration'] ?? '' ?>" min="1" placeholder="15" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                                <p class="text-xs text-gray-500 mt-1">Lecture length in minutes</p>
                            </div>
                        </div>

                        <!-- Resource URLs -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Resource URLs</label>
                            <div id="resourceUrlsContainer" class="space-y-3">
                                <?php 
                                $resourceUrls = !empty($lecture['resource_urls']) ? json_decode($lecture['resource_urls'], true) : [];
                                if (empty($resourceUrls)) {
                                    $resourceUrls = [''];
                                }
                                foreach ($resourceUrls as $index => $url): 
                                ?>
                                <div class="resource-url-item flex gap-2">
                                    <input type="url" name="resource_urls[]" value="<?= esc($url) ?>" placeholder="https://example.com/resource" class="flex-1 px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                                    <button type="button" class="remove-resource-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors" style="<?= count($resourceUrls) > 1 ? '' : 'display: none;' ?>">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="addResourceUrlBtn" class="mt-3 px-4 py-2 bg-secondary/10 border border-secondary rounded-lg text-secondary hover:bg-secondary/20 transition-colors text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Add Another Resource URL
                            </button>
                            <p class="text-xs text-gray-500 mt-2">Add links to downloadable resources, external references, or supplementary materials</p>
                        </div>

                        <!-- Order & Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-dark mb-2">Order Index</label>
                                <input type="number" id="lecture_order" name="order_index" value="<?= $lecture['order_index'] ?? 1 ?>" min="1" placeholder="1" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                                <p class="text-xs text-gray-500 mt-1">Position of this lecture within the section</p>
                            </div>
                            <div class="flex flex-col gap-3 justify-center">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="lecture_is_preview" name="is_preview" value="1" <?= !empty($lecture['is_preview']) ? 'checked' : '' ?> class="w-4 h-4 text-secondary border-gray-300 rounded focus:ring-2 focus:ring-secondary">
                                    <span class="ml-2 text-sm font-medium text-dark">Preview</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="lecture_is_free" name="is_free_preview" value="1" <?= !empty($lecture['is_free_preview']) ? 'checked' : '' ?> class="w-4 h-4 text-secondary border-gray-300 rounded focus:ring-2 focus:ring-secondary">
                                    <span class="ml-2 text-sm font-medium text-dark">Free Preview</span>
                                </label>
                                <p class="text-xs text-gray-500">Allow non-enrolled users to watch this lecture</p>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-between items-center pt-6 border-t">
                            <button type="button" id="deleteLectureBtn" class="px-6 py-3 bg-red-50 border border-red-300 rounded-full text-red-600 hover:bg-red-100 font-medium transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                                Delete Lecture
                            </button>
                            <div class="flex gap-3">
                                <a href="<?= site_url('auth/courses/edit/' . $course['id']) ?>#curriculum" class="px-6 py-3 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                                    Cancel
                                </a>
                                <button type="submit" class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium hover:shadow-lg transition-all">
                                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                                    Update Lecture
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();

    // Handle resource URL add/remove
    function updateRemoveButtons() {
        const items = $('.resource-url-item');
        items.find('.remove-resource-btn').toggle(items.length > 1);
        lucide.createIcons();
    }

    $('#addResourceUrlBtn').on('click', function() {
        const newItem = `
            <div class="resource-url-item flex gap-2">
                <input type="url" name="resource_urls[]" placeholder="https://example.com/resource" class="flex-1 px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                <button type="button" class="remove-resource-btn px-4 py-3 bg-red-50 border border-red-300 rounded-lg text-red-600 hover:bg-red-100 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        $('#resourceUrlsContainer').append(newItem);
        updateRemoveButtons();
    });

    $(document).on('click', '.remove-resource-btn', function() {
        $(this).closest('.resource-url-item').remove();
        updateRemoveButtons();
    });

    updateRemoveButtons();

    $('#editLectureForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const lectureId = $('input[name="lecture_id"]').val();
        
        submitBtn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i> Updating...');
        lucide.createIcons();

        $.ajax({
            url: '<?= site_url("auth/courses/lectures/update/") ?>' + lectureId,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Lecture updated successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '<?= site_url("auth/courses/edit/" . $course["id"]) ?>#curriculum';
                    });
                } else {
                    throw new Error(response.message || 'Failed to update lecture');
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while updating the lecture';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
                
                submitBtn.prop('disabled', false).html(originalText);
                lucide.createIcons();
            }
        });
    });

    $('#deleteLectureBtn').on('click', function() {
        const lectureId = $('input[name="lecture_id"]').val();
        
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
                    url: '<?= site_url("auth/courses/lectures/delete/") ?>' + lectureId,
                    method: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
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
                                window.location.href = '<?= site_url("auth/courses/edit/" . $course["id"]) ?>#curriculum';
                            });
                        } else {
                            throw new Error(response.message || 'Failed to delete lecture');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while deleting the lecture';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
