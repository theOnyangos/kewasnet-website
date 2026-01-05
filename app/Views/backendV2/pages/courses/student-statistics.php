<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    session()->set(['redirect_url' => $currentUrl::currentUrl()]);
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
            'pageTitle' => 'Student Statistics',
            'pageDescription' => esc($student['student_name']) . ' - ' . esc($course['title']),
            'breadcrumbs' => [
                ['label' => 'Courses', 'url' => site_url('auth/courses/courses')],
                ['label' => esc($course['title']), 'url' => site_url('auth/courses/edit/' . $courseId)],
                ['label' => 'Enrolled Students', 'url' => site_url('auth/courses/enrolled-students/' . $courseId)],
                ['label' => esc($student['student_name'])]
            ],
            'bannerActions' => '<a href="' . site_url('auth/courses/enrolled-students/' . $courseId) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Enrolled Students
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
            <div class="bg-white rounded-b-xl shadow-sm p-6">
                <!-- Student Info Header -->
                <div class="border-b border-gray-200 pb-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16">
                                <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold text-2xl">
                                    <?= strtoupper(substr($student['first_name'] ?? 'U', 0, 1)) ?>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-2xl font-bold text-gray-900"><?= esc($student['student_name']) ?></h2>
                                <p class="text-sm text-gray-500 mt-1"><?= esc($student['email']) ?></p>
                                <?php if (!empty($student['phone'])): ?>
                                    <p class="text-sm text-gray-500"><?= esc($student['phone']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Progress Overview -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Course Progress</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-sm text-blue-600 font-medium mb-1">Overall Progress</div>
                            <div class="text-2xl font-bold text-blue-900"><?= number_format($progress_percentage, 1) ?>%</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-sm text-green-600 font-medium mb-1">Lectures Completed</div>
                            <div class="text-2xl font-bold text-green-900"><?= $lectures_completed ?></div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-sm text-purple-600 font-medium mb-1">Quizzes Attempted</div>
                            <div class="text-2xl font-bold text-purple-900"><?= $total_quiz_attempts ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Quiz Scores by Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Quiz Scores by Section</h4>
                    <div class="space-y-4">
                        <?php if (!empty($quiz_scores_by_section)): ?>
                            <?php foreach ($quiz_scores_by_section as $section): ?>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-semibold text-gray-900"><?= esc($section['section_title'] ?? 'Unknown Section') ?></h5>
                                        <?php if ($section['passed']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Passed</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Not Passed</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mt-3">
                                        <div>
                                            <div class="text-sm text-gray-600">Best Score</div>
                                            <div class="text-lg font-bold text-gray-900"><?= number_format($section['best_score'], 1) ?>%</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-600">Attempts</div>
                                            <div class="text-lg font-bold text-gray-900"><?= $section['attempts'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">No quiz attempts found for this student.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- All Quiz Attempts -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">All Quiz Attempts</h4>
                    <div class="overflow-x-auto">
                        <table id="allQuizAttemptsTable" class="data-table stripe hover" style="width:100%">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th>Section</th>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable will populate this via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        lucide.createIcons();
        
        // Initialize Quiz Attempts DataTable
        $('#allQuizAttemptsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('auth/courses/get-student-quiz-attempts/' . $courseId . '/' . $userId) ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "section_title",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-900">${data || 'N/A'}</span>`;
                    }
                },
                {
                    "data": "quiz_title",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-900">${data || 'N/A'}</span>`;
                    }
                },
                {
                    "data": "score_display",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-900">${data}</span>`;
                    }
                },
                {
                    "data": "percentage_display",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-900">${data}%</span>`;
                    }
                },
                {
                    "data": "passed",
                    "render": function(data) {
                        if (data) {
                            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Passed</span>`;
                        } else {
                            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>`;
                        }
                    }
                },
                {
                    "data": "completed_at",
                    "render": function(data) {
                        return `<span class="text-sm text-gray-500">${data || 'N/A'}</span>`;
                    }
                }
            ],
            "order": [[5, 'desc']], // Order by date descending
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search quiz attempts...",
            },
            "drawCallback": function() {
                lucide.createIcons();
            }
        });
    });
</script>
<?= $this->endSection() ?>

