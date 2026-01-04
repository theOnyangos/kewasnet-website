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
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="my-6">
            <a href="<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>" 
               class="text-primary hover:text-primary-dark font-medium mb-4 inline-block border border-primary/20 rounded-lg px-4 py-2 hover:bg-primary/10 transition-colors">
                ← Back to Course
            </a>
            
            <!-- Course & Section Info -->
            <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
                <span class="text-slate-500">Course:</span>
                <a href="<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>" 
                   class="text-primary hover:text-primary-dark font-medium">
                    <?= esc($course['title']) ?>
                </a>
                <span class="text-slate-400">•</span>
                <span class="text-slate-500">Section:</span>
                <span class="font-medium text-slate-700"><?= esc($section['title']) ?></span>
            </div>
            
            <h1 class="text-3xl font-bold text-dark mb-2"><?= esc($quiz['title']) ?></h1>
            <?php if (!empty($quiz['description'])): ?>
                <p class="text-slate-600"><?= esc($quiz['description']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Questions (2/3 width) -->
            <div class="lg:col-span-2">
                <?php if (!empty($latest_attempt)): ?>
                    <!-- Viewing Results Banner -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-1">Viewing Previous Attempt Results</h3>
                                <p class="text-sm text-blue-700">
                                    You are viewing the results of a previous quiz attempt. 
                                    Your answers are highlighted: <span class="text-green-600 font-semibold">Green = Correct</span>, 
                                    <span class="text-red-600 font-semibold">Red = Incorrect</span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php elseif (!empty($previous_attempts)): ?>
                    <!-- Previous Attempt Available Banner -->
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1">
                                <i data-lucide="history" class="w-5 h-5 text-amber-600 mt-0.5"></i>
                                <div>
                                    <h3 class="font-semibold text-amber-900 mb-1">You have previous attempt(s)</h3>
                                    <p class="text-sm text-amber-700">
                                        You can review your previous results or take a new attempt below.
                                    </p>
                                </div>
                            </div>
                            <button type="button" id="toggleResultsBtn" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium whitespace-nowrap">
                                Show Last Results (<?= number_format($previous_attempts[0]['percentage'], 1) ?>%)
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <form id="quizForm">
                        <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        
                        <div class="space-y-8">
                            <?php foreach ($quiz['questions'] ?? [] as $qIndex => $question): ?>
                                <?php 
                                    // Check if this question has an attempt answer
                                    $questionAttempt = $attempt_answers[$question['id']] ?? null;
                                ?>
                                <div class="border-b border-slate-200 pb-6 last:border-b-0" data-question-id="<?= $question['id'] ?>">
                                    <div class="mb-4 flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-semibold text-dark">
                                                    Question <?= $qIndex + 1 ?>
                                                </h3>
                                                <span class="text-sm italic text-primary">(<?= $question['points'] ?> points)</span>
                                                
                                                <?php if ($questionAttempt): ?>
                                                    <!-- Attempt Status Badge -->
                                                    <?php if ($questionAttempt['is_correct']): ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                            Correct
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                                            <i data-lucide="x-circle" class="w-3 h-3"></i>
                                                            Incorrect
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-slate-700"><?= esc($question['question_text']) ?></p>
                                        </div>
                                    </div>
                                    
                                    <?php if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false'): ?>
                                        <div class="space-y-2">
                                            <?php foreach ($question['options'] ?? [] as $option): ?>
                                                <?php
                                                    // Check if this option was selected in any attempt
                                                    $attemptSelections = [];
                                                    $hasWrongAnswer = false;
                                                    $hasCorrectAnswer = false;
                                                    if (!empty($all_attempts_answers)) {
                                                        foreach ($previous_attempts as $idx => $prevAttempt) {
                                                            if (isset($all_attempts_answers[$prevAttempt['id']][$question['id']])) {
                                                                $prevAnswer = $all_attempts_answers[$prevAttempt['id']][$question['id']];
                                                                if ((string)$prevAnswer['user_answer'] === (string)$option['id']) {
                                                                    $attemptNum = count($previous_attempts) - $idx;
                                                                    $attemptSelections[] = [
                                                                        'attempt_num' => $attemptNum,
                                                                        'is_correct' => $prevAnswer['is_correct']
                                                                    ];
                                                                    if ($prevAnswer['is_correct']) {
                                                                        $hasCorrectAnswer = true;
                                                                    } else {
                                                                        $hasWrongAnswer = true;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $hasSelections = !empty($attemptSelections);
                                                    // Determine border styling: red if any wrong answer, green if only correct
                                                    $borderStyle = $hasWrongAnswer ? 'border-2 border-red-500' : ($hasCorrectAnswer ? 'border-2 border-green-500' : 'border border-slate-200');
                                                    $bgStyle = $hasWrongAnswer ? 'bg-red-50' : ($hasCorrectAnswer ? 'bg-green-50' : ($hasSelections ? 'bg-slate-50' : ''));
                                                ?>
                                                <label class="quiz-option flex items-start gap-3 p-3 <?= $borderStyle ?> rounded-lg hover:bg-slate-50 cursor-pointer transition-colors <?= $bgStyle ?>">
                                                    <input type="radio" 
                                                           name="answers[<?= $question['id'] ?>]" 
                                                           value="<?= $option['id'] ?>"
                                                           class="w-4 h-4 text-primary cursor-pointer mt-0.5">
                                                    <div class="flex-1">
                                                        <span class="text-slate-700"><?= esc($option['option_text']) ?></span>
                                                        <?php if ($hasSelections): ?>
                                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                                <?php foreach ($attemptSelections as $selection): ?>
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full <?= $selection['is_correct'] ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' ?>">
                                                                        <i data-lucide="<?= $selection['is_correct'] ? 'check' : 'x' ?>" class="w-3 h-3"></i>
                                                                        Attempt #<?= $selection['attempt_num'] ?>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <textarea name="answers[<?= $question['id'] ?>]" 
                                                  rows="4"
                                                  class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                  placeholder="Type your answer here..."></textarea>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-8 flex items-center justify-between">
                            <a href="<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>" 
                               class="px-6 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                                <?= !empty($latest_attempt) ? 'Back to Course' : 'Cancel' ?>
                            </a>
                            <?php if (!empty($latest_attempt)): ?>
                                <?php if ($can_take_new_attempt): ?>
                                    <a href="<?= base_url('ksp/learning-hub/take-quiz/' . $course_id . '/' . $section['id']) ?>" 
                                       class="gradient-btn px-6 py-2 rounded-lg text-white font-medium">
                                        Take New Attempt
                                        <?php if ($attempts_remaining !== null): ?>
                                            <span class="text-xs">(<?= $attempts_remaining ?> left)</span>
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <button type="button" 
                                            disabled
                                            class="px-6 py-2 rounded-lg text-slate-400 bg-slate-200 cursor-not-allowed font-medium">
                                        No Attempts Remaining
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($can_take_new_attempt): ?>
                                    <button type="submit" 
                                            class="gradient-btn px-6 py-2 rounded-lg text-white font-medium">
                                        Submit Quiz
                                        <?php if ($attempts_remaining !== null): ?>
                                            <span class="text-xs ml-2">(<?= $attempts_remaining ?> attempts left)</span>
                                        <?php endif; ?>
                                    </button>
                                <?php else: ?>
                                    <button type="button" 
                                            disabled
                                            class="px-6 py-2 rounded-lg text-slate-400 bg-slate-200 cursor-not-allowed font-medium">
                                        No Attempts Remaining
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Performance & Info (1/3 width) -->
            <div class="lg:col-span-1">
                <div class="sticky top-[130px] space-y-6">
                    <?php if (!empty($latest_attempt) && !empty($attempt_answers)): ?>
                        <!-- Attempt Summary Card -->
                        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                            <h3 class="font-semibold text-dark mb-4 flex items-center gap-2">
                                <i data-lucide="clipboard-check" class="w-5 h-5 text-primary"></i>
                                Attempt Summary
                            </h3>
                            <div class="space-y-3">
                                <?php
                                    $correctCount = 0;
                                    $incorrectCount = 0;
                                    $unansweredCount = 0;
                                    
                                    foreach ($quiz['questions'] ?? [] as $q) {
                                        if (isset($attempt_answers[$q['id']])) {
                                            if ($attempt_answers[$q['id']]['is_correct']) {
                                                $correctCount++;
                                            } else {
                                                $incorrectCount++;
                                            }
                                        } else {
                                            $unansweredCount++;
                                        }
                                    }
                                ?>
                                
                                <!-- Correct Answers -->
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                                        <span class="text-sm font-medium text-green-900">Correct</span>
                                    </div>
                                    <span class="text-lg font-bold text-green-600"><?= $correctCount ?></span>
                                </div>
                                
                                <!-- Incorrect Answers -->
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                                        <span class="text-sm font-medium text-red-900">Incorrect</span>
                                    </div>
                                    <span class="text-lg font-bold text-red-600"><?= $incorrectCount ?></span>
                                </div>
                                
                                <?php if ($unansweredCount > 0): ?>
                                <!-- Unanswered -->
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="circle" class="w-4 h-4 text-slate-600"></i>
                                        <span class="text-sm font-medium text-slate-900">Unanswered</span>
                                    </div>
                                    <span class="text-lg font-bold text-slate-600"><?= $unansweredCount ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Score -->
                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-slate-700">Final Score</span>
                                        <span class="text-2xl font-bold <?= $latest_attempt['passed'] ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= number_format($latest_attempt['percentage'], 1) ?>%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Quiz Info Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                        <h3 class="font-semibold text-dark mb-4 flex items-center gap-2">
                            <i data-lucide="info" class="w-5 h-5 text-primary"></i>
                            Quiz Information
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Total Questions:</span>
                                <span class="font-semibold text-dark"><?= count($quiz['questions'] ?? []) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Total Points:</span>
                                <span class="font-semibold text-dark">
                                    <?php 
                                        $totalPoints = 0;
                                        foreach ($quiz['questions'] ?? [] as $q) {
                                            $totalPoints += $q['points'];
                                        }
                                        echo $totalPoints;
                                    ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Passing Score:</span>
                                <span class="font-semibold text-dark">70%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Statistics Card (All Attempts) -->
                    <?php if (!empty($previous_attempts)): ?>
                        <?php
                            // Calculate aggregate statistics from all attempts
                            $totalAttempts = count($previous_attempts);
                            $passedAttempts = 0;
                            $failedAttempts = 0;
                            $totalPercentage = 0;
                            $totalCorrect = 0;
                            $totalIncorrect = 0;
                            
                            foreach ($previous_attempts as $attempt) {
                                if ($attempt['passed']) {
                                    $passedAttempts++;
                                } else {
                                    $failedAttempts++;
                                }
                                $totalPercentage += $attempt['percentage'];
                            }
                            
                            $averageScore = $totalAttempts > 0 ? $totalPercentage / $totalAttempts : 0;
                        ?>
                        <div class="bg-primary/10 rounded-lg shadow-sm border border-blue-200 p-6">
                            <h3 class="font-semibold text-dark mb-4 flex items-center gap-2">
                                <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                                Overall Statistics
                            </h3>
                            <div class="space-y-3">
                                <!-- Average Score -->
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="target" class="w-4 h-4 text-blue-600"></i>
                                        <span class="text-sm font-medium text-slate-900">Average Score</span>
                                    </div>
                                    <span class="text-xl font-bold text-blue-600"><?= number_format($averageScore, 1) ?>%</span>
                                </div>
                                
                                <!-- Total Attempts -->
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="repeat" class="w-4 h-4 text-slate-600"></i>
                                        <span class="text-sm font-medium text-slate-900">Total Attempts</span>
                                    </div>
                                    <span class="text-lg font-bold text-slate-600"><?= $totalAttempts ?></span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Passed -->
                                    <div class="flex flex-col items-center justify-center p-3 bg-green-50 rounded-lg">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mb-1"></i>
                                        <span class="text-2xl font-bold text-green-600"><?= $passedAttempts ?></span>
                                        <span class="text-xs text-green-700">Passed</span>
                                    </div>
                                    
                                    <!-- Failed -->
                                    <div class="flex flex-col items-center justify-center p-3 bg-red-50 rounded-lg">
                                        <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mb-1"></i>
                                        <span class="text-2xl font-bold text-red-600"><?= $failedAttempts ?></span>
                                        <span class="text-xs text-red-700">Failed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Previous Attempts Card -->
                    <?php if (!empty($previous_attempts)): ?>
                        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                            <h3 class="font-semibold text-dark mb-4 flex items-center gap-2">
                                <i data-lucide="bar-chart-2" class="w-5 h-5 text-primary"></i>
                                Previous Attempts
                            </h3>
                            <div class="space-y-3">
                                <?php foreach ($previous_attempts as $index => $attempt): ?>
                                    <div class="p-3 bg-slate-50 rounded-lg">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-medium text-slate-600">Attempt #<?= count($previous_attempts) - $index ?></span>
                                            <span class="text-xs text-slate-500"><?= date('M d, Y', strtotime($attempt['completed_at'])) ?></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-slate-700">
                                                <?= number_format($attempt['score'], 1) ?> / <?= number_format($attempt['total_points'], 1) ?>
                                            </span>
                                            <span class="text-lg font-bold <?= $attempt['passed'] ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= number_format($attempt['percentage'], 1) ?>%
                                            </span>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded <?= $attempt['passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?php if ($attempt['passed']): ?>
                                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i> Passed
                                                <?php else: ?>
                                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i> Failed
                                                <?php endif; ?>
                                            </span>
                                            <a href="<?= base_url('ksp/learning-hub/take-quiz/' . $course_id . '/' . $section['id'] . '?view_attempt=' . $attempt['id']) ?>" 
                                               class="text-xs text-primary hover:text-primary-dark font-medium">
                                                View Results →
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tips Card -->
                    <div class="bg-gradient-to-br from-primary/10 to-primary/5 rounded-lg border border-primary/20 p-6">
                        <h3 class="font-semibold text-dark mb-3 flex items-center gap-2">
                            <i data-lucide="lightbulb" class="w-5 h-5 text-primary"></i>
                            Tips
                        </h3>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-4 h-4 text-primary mt-0.5 flex-shrink-0"></i>
                                <span>Read each question carefully</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-4 h-4 text-primary mt-0.5 flex-shrink-0"></i>
                                <span>Answer all questions before submitting</span>
                            </li>
                            <?php if ($max_attempts !== null): ?>
                                <li class="flex items-start gap-2">
                                    <i data-lucide="alert-circle" class="w-4 h-4 text-orange-600 mt-0.5 flex-shrink-0"></i>
                                    <span><strong>Limited to <?= $max_attempts ?> attempt<?= $max_attempts > 1 ? 's' : '' ?></strong>
                                    <?php if ($attempts_count > 0): ?>
                                        <br><span class="text-xs">(Used: <?= $attempts_count ?>, Remaining: <?= $attempts_remaining ?>)</span>
                                    <?php endif; ?>
                                    </span>
                                </li>
                            <?php else: ?>
                                <li class="flex items-start gap-2">
                                    <i data-lucide="refresh-cw" class="w-4 h-4 text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Unlimited attempts available</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="quizResultModal" class="hidden fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 relative z-[10000]">
        <div id="quizResultContent"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    // State management for showing/hiding results
    let resultsVisible = false;
    const previousAttempts = <?= json_encode($previous_attempts ?? []) ?>;
    const hasPreviousAttempts = previousAttempts && previousAttempts.length > 0;
    
    // Check if there's data from the server (when explicitly viewing an attempt)
    const serverAnswers = <?= json_encode($attempt_answers ?? []) ?>;
    const latestAttempt = <?= json_encode($latest_attempt ?? null) ?>;
    
    console.log('Previous attempts:', previousAttempts);
    console.log('Server answers:', serverAnswers);
    console.log('Latest attempt:', latestAttempt);
    
    // Check if there's a saved quiz result in sessionStorage
    const quizId = '<?= $quiz['id'] ?>';
    const storageKey = 'quiz_result_' + quizId;
    
    // Check if we're explicitly viewing an attempt (serverAnswers will be populated)
    const isExplicitlyViewing = serverAnswers && Object.keys(serverAnswers).length > 0;
    
    if (isExplicitlyViewing) {
        // We're viewing a specific attempt - show results immediately
        console.log('Viewing specific attempt - showing results');
        showResults(serverAnswers, latestAttempt);
    } else if (hasPreviousAttempts) {
        // We have previous attempts but not viewing - check sessionStorage for just-submitted results
        const savedResult = sessionStorage.getItem(storageKey);
        if (savedResult) {
            try {
                const result = JSON.parse(savedResult);
                console.log('Found saved quiz result in sessionStorage:', result);
                
                // Show results from sessionStorage
                if (result.answers) {
                    showResults(result.answers, {
                        score: result.score,
                        total_points: result.total_points,
                        percentage: result.percentage,
                        passed: result.passed
                    });
                }
            } catch (e) {
                console.error('Error parsing saved quiz result:', e);
                sessionStorage.removeItem(storageKey);
            }
        }
    }
    
    // Toggle Results Button Handler
    $('#toggleResultsBtn').on('click', function() {
        if (!resultsVisible) {
            // Show results from most recent attempt
            if (hasPreviousAttempts) {
                const mostRecent = previousAttempts[0];
                console.log('Loading results for attempt:', mostRecent.id);
                
                // Fetch the results for the most recent attempt
                loadAttemptResults(mostRecent.id);
            }
        } else {
            // Hide results - enable form for new attempt
            hideResults();
        }
    });
    
    function showResults(answers, attempt) {
        resultsVisible = true;
        
        // Update toggle button with score
        const scoreText = attempt ? ' (' + parseFloat(attempt.percentage).toFixed(1) + '%)' : '';
        $('#toggleResultsBtn').text('Hide Results' + scoreText).removeClass('bg-amber-600 hover:bg-amber-700').addClass('bg-slate-600 hover:bg-slate-700');
        
        // Highlight answers
        highlightAnswers(answers);
        
        // Disable form
        $('#quizForm').find('textarea').prop('disabled', true);
        $('#quizForm').find('button[type="submit"]').prop('disabled', true).text('View Results').off('click').on('click', function(e) {
            e.preventDefault();
            if (attempt) {
                showQuizResult({
                    status: 'success',
                    score: attempt.score,
                    total_points: attempt.total_points,
                    percentage: attempt.percentage,
                    passed: attempt.passed == 1 || attempt.passed === true,
                    message: (attempt.passed == 1 || attempt.passed === true) ? 'Congratulations! You passed the quiz.' : 'You did not pass. Please review and try again.',
                    answers: answers
                });
            }
        });
        
        // Disable radio inputs after delay
        setTimeout(function() {
            $('#quizForm').find('input[type="radio"]').prop('disabled', true);
            $('.quiz-option').removeClass('hover:bg-slate-50').addClass('cursor-not-allowed');
        }, 150);
    }
    
    function hideResults() {
        resultsVisible = false;
        
        // Update toggle button with most recent score
        const scoreText = hasPreviousAttempts && previousAttempts[0] ? ' (' + parseFloat(previousAttempts[0].percentage).toFixed(1) + '%)' : '';
        $('#toggleResultsBtn').text('Show Last Results' + scoreText).removeClass('bg-slate-600 hover:bg-slate-700').addClass('bg-amber-600 hover:bg-amber-700');
        
        // Clear all highlighting
        $('.quiz-option').each(function() {
            $(this).removeClass('border-green-500 bg-green-50 border-red-500 bg-red-50 cursor-not-allowed');
            $(this).addClass('border-slate-200 hover:bg-slate-50');
            $(this).find('span').removeClass('text-green-700 text-red-700 font-semibold');
            $(this).find('.check-icon, .x-icon').remove();
        });
        
        // Enable form for new attempt
        $('#quizForm').find('input[type="radio"], textarea').prop('disabled', false).prop('checked', false);
        $('#quizForm').find('button[type="submit"]').prop('disabled', false).text('Submit Quiz');
        $('.quiz-option').addClass('hover:bg-slate-50 cursor-pointer');
        
        // Clear sessionStorage
        sessionStorage.removeItem(storageKey);
        
        console.log('Results hidden - form enabled for new attempt');
    }
    
    function loadAttemptResults(attemptId) {
        console.log('Loading attempt results:', attemptId);
        
        // Make AJAX request to get attempt details
        $.ajax({
            url: '<?= base_url("ksp/learning-hub/get-attempt-details") ?>',
            method: 'POST',
            data: {
                attempt_id: attemptId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showResults(response.answers, response.attempt);
                } else {
                    alert('Failed to load attempt results: ' + response.message);
                }
            },
            error: function() {
                alert('Failed to load attempt results. Please try again.');
            }
        });
    }
    
    // Cleanup old else block and continue with rest of initialization
    
    function highlightAnswers(answers) {
        console.log('Highlighting answers:', answers);
        
        $.each(answers, function(questionId, result) {
            console.log('Processing question:', questionId, 'Result:', result);
            
            // Find all radio inputs for this question using attribute selector
            const radioInputs = $('input[type="radio"][name="answers[' + questionId + ']"]');
            console.log('Found radio inputs:', radioInputs.length);
            
            if (!radioInputs.length) {
                console.warn('No radio inputs found for question:', questionId);
                return;
            }
            
            // First, remove all highlighting from this question
            radioInputs.each(function() {
                const label = $(this).closest('label');
                label.removeClass('border-green-500 bg-green-50 border-red-500 bg-red-50');
                label.addClass('border-slate-200');
                label.find('span').removeClass('text-green-700 text-red-700 font-semibold');
                label.find('.check-icon, .x-icon').remove();
            });
            
            // Highlight user's selected answer
            if (result.user_answer) {
                // Find the selected option - use strict string comparison
                let selectedOption = null;
                radioInputs.each(function() {
                    const radioValue = String($(this).val());
                    const userAnswer = String(result.user_answer);
                    console.log('Comparing radio value:', radioValue, 'with user answer:', userAnswer, 'Match:', radioValue === userAnswer);
                    if (radioValue === userAnswer) {
                        selectedOption = $(this);
                        return false; // break loop
                    }
                });
                
                console.log('Selected option found:', selectedOption !== null, 'Option element:', selectedOption);
                
                if (!selectedOption || selectedOption.length === 0) {
                    console.warn('Selected option not found for question:', questionId, 'value:', result.user_answer);
                    // Log all available options for debugging
                    console.log('Available options for question', questionId, ':');
                    radioInputs.each(function() {
                        console.log('  - Option value:', $(this).val());
                    });
                    return;
                }
                
                const selectedLabel = selectedOption.closest('label');
                
                // Ensure the radio button stays checked
                selectedOption.prop('checked', true);
                console.log('Radio button checked:', selectedOption.is(':checked'), 'Value:', selectedOption.val());
                
                if (result.is_correct) {
                    // Correct answer - green with checkmark
                    selectedLabel.removeClass('border-slate-200 hover:bg-slate-50');
                    selectedLabel.addClass('border-green-500 bg-green-50');
                    const textSpan = selectedLabel.find('span').first();
                    textSpan.addClass('text-green-700 font-semibold');
                    textSpan.prepend('<i data-lucide="check-circle" class="w-5 h-5 text-green-600 inline-block mr-2 check-icon"></i>');
                    console.log('Highlighted correct answer green');
                } else {
                    // Wrong answer - red with X
                    selectedLabel.removeClass('border-slate-200 hover:bg-slate-50');
                    selectedLabel.addClass('border-red-500 bg-red-50');
                    const textSpan = selectedLabel.find('span').first();
                    textSpan.addClass('text-red-700 font-semibold');
                    textSpan.prepend('<i data-lucide="x-circle" class="w-5 h-5 text-red-600 inline-block mr-2 x-icon"></i>');
                    console.log('Highlighted wrong answer red');
                    
                    // Also highlight the correct answer in green
                    if (result.correct_answer) {
                        let correctOption = null;
                        radioInputs.each(function() {
                            const radioValue = String($(this).val());
                            const correctAnswer = String(result.correct_answer);
                            if (radioValue === correctAnswer) {
                                correctOption = $(this);
                                return false; // break loop
                            }
                        });
                        
                        if (correctOption) {
                            const correctLabel = correctOption.closest('label');
                            correctLabel.removeClass('border-slate-200 hover:bg-slate-50');
                            correctLabel.addClass('border-green-500 bg-green-50');
                            const correctTextSpan = correctLabel.find('span').first();
                            correctTextSpan.addClass('text-green-700 font-semibold');
                            correctTextSpan.prepend('<i data-lucide="check-circle" class="w-5 h-5 text-green-600 inline-block mr-2 check-icon"></i>');
                            console.log('Highlighted correct answer green (user was wrong)');
                        }
                    }
                }
            } else {
                console.warn('No user answer for question:', questionId);
            }
        });
        
        // Reinitialize Lucide icons for the newly added icons
        lucide.createIcons();
        console.log('Icons reinitialized');
    }
    
    $('#quizForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Validate that all questions are answered
        const totalQuestions = <?= count($quiz['questions'] ?? []) ?>;
        const answeredQuestions = form.find('input[type="radio"]:checked').length;
        
        if (answeredQuestions < totalQuestions) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Quiz',
                text: `Please answer all ${totalQuestions} questions before submitting. You have answered ${answeredQuestions} out of ${totalQuestions} questions.`,
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        submitBtn.prop('disabled', true).html('Submitting...');
        
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/quiz/submit') ?>',
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: form.serialize(),
            success: function(response) {
                console.log('Quiz response:', response);
                
                if (response.status === 'success') {
                    console.log('Answers to highlight:', response.answers);
                    
                    // Save result to sessionStorage for persistence across page reloads
                    sessionStorage.setItem(storageKey, JSON.stringify(response));
                    
                    // Highlight answers first
                    highlightAnswers(response.answers);
                    
                    // Disable textareas and submit button immediately
                    form.find('textarea').prop('disabled', true);
                    submitBtn.prop('disabled', true);
                    
                    // Disable radio inputs AFTER a short delay to ensure they stay visually checked
                    setTimeout(function() {
                        form.find('input[type="radio"]').prop('disabled', true);
                        // Remove hover effects from labels when disabled
                        $('.quiz-option').removeClass('hover:bg-slate-50').addClass('cursor-not-allowed');
                    }, 150);
                    
                    // Show result modal
                    showQuizResult(response);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: response.message || 'Failed to submit quiz',
                        confirmButtonColor: '#3085d6'
                    });
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg,
                    confirmButtonColor: '#3085d6'
                });
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
                <div class="flex gap-3 justify-center">
                    <button onclick="retakeQuiz()" 
                            class="px-6 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                        Retake Quiz
                    </button>
                    <button onclick="window.location.href='<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>'" 
                            class="gradient-btn px-6 py-2 rounded-lg text-white font-medium">
                        Continue Course
                    </button>
                </div>
            </div>
        `);
        
        lucide.createIcons();
        modal.removeClass('hidden');
    }
    
    // Function to retake quiz - clears saved result and reloads page
    window.retakeQuiz = function() {
        sessionStorage.removeItem(storageKey);
        window.location.reload();
    }
});
</script>
<?= $this->endSection() ?>

