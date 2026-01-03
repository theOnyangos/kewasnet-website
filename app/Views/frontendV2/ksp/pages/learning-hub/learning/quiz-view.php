<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8">
            <div class="mb-6">
                <a href="<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>" 
                   class="text-primary hover:text-primary-dark font-medium mb-4 inline-block">
                    ← Back to Course
                </a>
                <h1 class="text-2xl font-bold text-dark mb-2"><?= esc($quiz['title']) ?></h1>
                <?php if (!empty($quiz['description'])): ?>
                    <p class="text-slate-600"><?= esc($quiz['description']) ?></p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($previous_attempts)): ?>
                <div class="mb-6 p-4 bg-slate-50 rounded-lg">
                    <h3 class="font-semibold text-dark mb-2">Previous Attempts</h3>
                    <?php foreach ($previous_attempts as $attempt): ?>
                        <div class="flex items-center justify-between text-sm">
                            <span>Score: <?= number_format($attempt['score'], 1) ?> / <?= number_format($attempt['total_points'], 1) ?></span>
                            <span class="<?= $attempt['passed'] ? 'text-green-600' : 'text-red-600' ?>">
                                <?= number_format($attempt['percentage'], 1) ?>% - <?= $attempt['passed'] ? 'Passed' : 'Failed' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form id="quizForm">
                <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                
                <div class="space-y-8">
                    <?php foreach ($quiz['questions'] ?? [] as $qIndex => $question): ?>
                        <div class="border-b border-slate-200 pb-6">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-dark mb-2">
                                    Question <?= $qIndex + 1 ?> (<?= $question['points'] ?> points)
                                </h3>
                                <p class="text-slate-700"><?= esc($question['question_text']) ?></p>
                            </div>
                            
                            <?php if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false'): ?>
                                <div class="space-y-2">
                                    <?php foreach ($question['options'] ?? [] as $option): ?>
                                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                                            <input type="radio" 
                                                   name="answers[<?= $question['id'] ?>]" 
                                                   value="<?= $option['id'] ?>"
                                                   class="w-4 h-4 text-primary">
                                            <span class="flex-1 text-slate-700"><?= esc($option['option_text']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <textarea name="answers[<?= $question['id'] ?>]" 
                                          rows="4"
                                          class="w-full px-4 py-2 border border-slate-300 rounded-lg"
                                          placeholder="Type your answer here..."></textarea>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-8 flex items-center justify-between">
                    <a href="<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>" 
                       class="px-6 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="gradient-btn px-6 py-2 rounded-lg text-white font-medium">
                        Submit Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="quizResultModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <div id="quizResultContent"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    $('#quizForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('Submitting...');
        
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/quiz/submit') ?>',
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    showQuizResult(response);
                } else {
                    alert(response.message || 'Failed to submit quiz');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    function showQuizResult(result) {
        const modal = $('#quizResultModal');
        const content = $('#quizResultContent');
        
        const resultClass = result.passed ? 'text-green-600' : 'text-red-600';
        const resultIcon = result.passed ? 'check-circle' : 'x-circle';
        
        content.html(`
            <div class="text-center">
                <i data-lucide="${resultIcon}" class="w-16 h-16 ${resultClass} mx-auto mb-4"></i>
                <h3 class="text-2xl font-bold text-dark mb-2">${result.passed ? 'Congratulations!' : 'Try Again'}</h3>
                <p class="text-slate-600 mb-4">${result.message}</p>
                <div class="bg-slate-50 rounded-lg p-4 mb-4">
                    <div class="text-3xl font-bold ${resultClass} mb-2">
                        ${result.percentage}%
                    </div>
                    <div class="text-sm text-slate-600">
                        Score: ${result.score} / ${result.total_points} points
                    </div>
                </div>
                <button onclick="window.location.href='<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>'" 
                        class="gradient-btn px-6 py-2 rounded-lg text-white font-medium">
                    Continue Course
                </button>
            </div>
        `);
        
        lucide.createIcons();
        modal.removeClass('hidden');
    }
});
</script>
<?= $this->endSection() ?>

