<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Create New Quiz',
            'pageDescription' => 'Create a new quiz to assess student knowledge and track learning progress',
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses')],
                ['label' => 'Quizzes', 'url' => site_url('auth/courses/quizzes')],
                ['label' => 'Create']
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/quizzes') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Quizzes
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Quiz Form Card -->
        <div class="bg-white rounded-b-xl shadow-sm max-w-full p-8">
            <form id="createQuizForm">
                <div class="space-y-6">
                    <!-- Quiz Title -->
                    <div>
                        <label for="quiz_title" class="block text-sm font-medium text-dark mb-2">
                            Quiz Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="quiz_title" 
                               name="title" 
                               class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" 
                               placeholder="e.g., Section 1 Quiz: Introduction to Web Development"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Give your quiz a clear, descriptive title</p>
                    </div>

                    <!-- Quiz Description -->
                    <div>
                        <label for="quiz_description" class="block text-sm font-medium text-dark mb-2">
                            Description
                        </label>
                        <textarea id="quiz_description" 
                                  name="description" 
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary resize-none"
                                  placeholder="Describe what this quiz covers..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Provide instructions or context for students</p>
                    </div>

                    <!-- Quiz Settings Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Passing Score -->
                        <div>
                            <label for="passing_score" class="block text-sm font-medium text-dark mb-2">
                                Passing Score (%)
                            </label>
                            <input type="number" 
                                   id="passing_score" 
                                   name="passing_score" 
                                   value="70"
                                   min="0" 
                                   max="100"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                            <p class="text-xs text-gray-500 mt-1">Minimum score required to pass (0-100)</p>
                        </div>

                        <!-- Max Attempts -->
                        <div>
                            <label for="max_attempts" class="block text-sm font-medium text-dark mb-2">
                                Maximum Attempts
                            </label>
                            <input type="number" 
                                   id="max_attempts" 
                                   name="max_attempts" 
                                   min="1"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                                   placeholder="Unlimited">
                            <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited attempts</p>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex gap-3">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0"></i>
                            <div class="text-sm text-blue-900">
                                <p class="font-medium mb-1">Next Steps</p>
                                <p>After creating this quiz, you can:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li>Add questions and answer options</li>
                                    <li>Attach it to specific course sections</li>
                                    <li>Adjust settings anytime from the edit page</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-borderColor">
                        <a href="<?= base_url('auth/courses/quizzes') ?>" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="gradient-btn px-8 py-3 rounded-[50px] text-white font-medium hover:shadow-lg transition-all flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Create Quiz</span>
                        </button>
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

    $('#createQuizForm').on('submit', function(e) {
        e.preventDefault();

        const submitBtn = $(this).find('button[type="submit"]');
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Creating...');

        $.ajax({
            url: '<?= site_url("auth/courses/quizzes/create") ?>',
            type: 'POST',
            data: {
                title: $('#quiz_title').val(),
                description: $('#quiz_description').val(),
                passing_score: $('#passing_score').val(),
                max_attempts: $('#max_attempts').val(),
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Quiz created successfully',
                        showConfirmButton: true,
                        confirmButtonText: 'Add Questions'
                    }).then(() => {
                        // Redirect to edit page to add questions
                        window.location.href = '<?= base_url("auth/courses/quizzes/edit") ?>/' + response.data.quiz_id;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create quiz'
                    });
                    submitBtn.prop('disabled', false).html(originalHtml);
                    lucide.createIcons();
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
                submitBtn.prop('disabled', false).html(originalHtml);
                lucide.createIcons();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
