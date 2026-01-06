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

<!-- Page Header -->
<section class="relative py-20 bg-gradient-to-r from-primary to-secondary">
    <div class="container mx-auto px-4 text-center text-white">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">
                <?php if (isset($course) && !empty($course['title'])): ?>
                    <?= esc($course['title']) ?>
                <?php else: ?>
                    Course Learning
                <?php endif; ?>
            </h1>
            <p class="text-xl leading-relaxed">
                <?php if (isset($course) && !empty($course['summary'])): ?>
                    <?= esc(substr($course['summary'], 0, 150)) ?><?= strlen($course['summary']) > 150 ? '...' : '' ?>
                <?php else: ?>
                    Continue your learning journey and master new skills.
                <?php endif; ?>
            </p>
        </div>
    </div>
</section>

<div class="pb-8">
    <!-- Breadcrumb -->
    <div class="bg-white border-b borderColor mb-6">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/ksp" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-primary">
                            <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <a href="<?= base_url('ksp/learning-hub') ?>" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2">Learning Hub</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <a href="<?= base_url('ksp/learning-hub/course/' . $course['id']) ?>" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2"><?= esc($course['title']) ?></a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <span class="ml-1 text-sm font-medium text-secondary md:ml-2">Learning</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 container mx-auto px-4 mb-8">
        <!-- Sidebar - Course Content -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sticky top-[120px]">
                <h3 class="text-lg font-bold text-dark mb-4">Course Content</h3>
                
                <!-- Progress Bar -->
                <div class="mb-4 border-b border-slate-200 pb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-600">Progress</span>
                        <span class="text-sm font-bold text-primary"><?= number_format($progress, 0) ?>%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full" style="width: <?= $progress ?>%"></div>
                    </div>
                </div>

                <!-- Generate Certificate Button -->
                <div class="mb-6">
                    <?php if ($progress == 100): ?>
                        <a href="<?= base_url('ksp/learning-hub/certificate/' . $course['id']) ?>" id="generateCertificateBtn"
                            class="w-full px-4 py-3 rounded-lg font-semibold transition-colors text-white text-center flex items-center justify-center gap-2 bg-[#27aae0] hover:bg-cyan-700">
                            <i data-lucide="award" class="w-5 h-5"></i>
                            Generate Certificate
                        </a>
                    <?php else: ?>
                        <button id="generateCertificateBtn"
                            class="w-full px-4 py-3 rounded-lg font-semibold transition-colors text-white text-center flex items-center justify-center gap-2 bg-slate-300 cursor-not-allowed" disabled>
                            <i data-lucide="award" class="w-5 h-5"></i>
                            Generate Certificate
                        </button>
                    <?php endif; ?>

                    <?php if ($progress < 100): ?>
                        <p class="text-xs text-slate-400 mt-2 text-center">Complete the course to unlock your certificate.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Sections List -->
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    <?php 
                    $sections = $course['sections'] ?? [];
                    $totalSections = count($sections);
                    foreach ($sections as $sectionIndex => $section): 
                        $isLastSection = ($sectionIndex === $totalSections - 1);
                    ?>
                        <div class="<?= $isLastSection ? '' : 'border-b border-slate-200' ?> pb-2">
                            <h4 class="font-semibold text-dark mb-2">
                                Section <?= $sectionIndex + 1 ?>: <?= esc($section['title']) ?>
                            </h4>
                            <ul class="space-y-1 ml-4">
                                <?php foreach ($section['lectures'] ?? [] as $lecture): ?>
                                    <li>
                                        <a href="<?= base_url('ksp/learning-hub/lecture/' . $course['id'] . '/' . $lecture['id']) ?>" 
                                           class="text-sm text-slate-600 hover:text-primary hover:bg-primary/10 flex items-center gap-3 border border-slate-200 p-2 rounded-md">
                                            <?php if (in_array($lecture['id'], $completed_lectures)): ?>
                                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                                            <?php else: ?>
                                                <i data-lucide="play-circle" class="w-4 h-4"></i>
                                            <?php endif; ?>
                                            <span class="<?= in_array($lecture['id'], $completed_lectures) ? 'line-through' : '' ?>">
                                                <?= esc($lecture['title']) ?>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                
                                <?php if (!empty($section['quiz_id'])): ?>
                                    <?php 
                                    // Check if all lectures in this section are completed
                                    $sectionLectures = $section['lectures'] ?? [];
                                    $sectionLectureIds = array_column($sectionLectures, 'id');
                                    $completedInSection = array_intersect($sectionLectureIds, $completed_lectures);
                                    $allLecturesCompleted = count($completedInSection) === count($sectionLectureIds);
                                    ?>
                                    
                                    <li>
                                        <?php if ($allLecturesCompleted): ?>
                                            <a href="<?= base_url('ksp/learning-hub/quiz/' . $course['id'] . '/' . $section['id']) ?>" 
                                               class="text-sm text-primary hover:text-primary-dark hover:bg-primary/10 flex items-center gap-3 font-medium border border-slate-200 p-2 rounded-md">
                                                <i data-lucide="file-question" class="w-4 h-4"></i>
                                                Section Quiz
                                            </a>
                                        <?php else: ?>
                                            <span class="text-sm text-slate-400 hover:bg-slate-200 flex items-center gap-3 border border-slate-200 p-2 rounded-md" 
                                                  title="Complete all lectures to unlock the quiz">
                                                <i data-lucide="lock" class="w-4 h-4"></i>
                                                Section Quiz (Locked)
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="lg:col-span-3">
            <!-- Instructions Card -->
            <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-lg border border-primary/20 p-6 mb-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="w-6 h-6 text-primary flex-shrink-0 mt-1"></i>
                    <div>
                        <h3 class="text-lg font-bold text-dark mb-3">How to Navigate This Course</h3>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li class="flex items-start gap-2">
                                <i data-lucide="play-circle" class="w-4 h-4 text-primary flex-shrink-0 mt-0.5"></i>
                                <span><strong>Watch Lectures:</strong> Click on any lecture from the sidebar or below to start learning</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Mark Complete:</strong> After watching a lecture, click "Mark as Complete" to track your progress</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="file-question" class="w-4 h-4 text-primary flex-shrink-0 mt-0.5"></i>
                                <span><strong>Take Quizzes:</strong> Complete section quizzes to test your understanding</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="award" class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Earn Certificate:</strong> Complete all lectures and quizzes to receive your certificate</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8">
                <h2 class="text-2xl font-bold text-dark mb-4"><?= esc($course['title']) ?></h2>
                <p class="text-slate-600 mb-6"><?= esc($course['summary'] ?? '') ?></p>
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-dark mb-4">Course Sections</h3>
                    <div class="space-y-4">
                        <!-- Display sections and lectures -->
                        <?php foreach ($course['sections'] ?? [] as $index => $section): 
                            // Calculate total duration for this section by summing all lecture durations
                            $sectionTotalSeconds = 0;
                            $lectures = $section['lectures'] ?? [];
                            foreach ($lectures as $lecture) {
                                // Duration is stored in seconds in the database
                                // Use same casting as individual lecture display for consistency
                                if (!empty($lecture['duration'])) {
                                    $duration = (int)($lecture['duration'] ?? 0);
                                    if ($duration > 0) {
                                        $sectionTotalSeconds += $duration;
                                    }
                                }
                            }

                            // Format duration for display
                            $sectionDuration = '';
                            if ($sectionTotalSeconds > 0) {
                                $hours = floor($sectionTotalSeconds / 3600);
                                $minutes = floor(($sectionTotalSeconds % 3600) / 60);
                                $seconds = $sectionTotalSeconds % 60;
                                
                                if ($hours > 0) {
                                    // Format with hours
                                    $sectionDuration = $hours . 'h';
                                    if ($minutes > 0) {
                                        $sectionDuration .= ' ' . $minutes . 'm';
                                    }
                                } elseif ($minutes > 0) {
                                    // Format with minutes only
                                    $sectionDuration = $minutes . 'm';
                                    if ($seconds > 0 && $minutes < 10) {
                                        // Show seconds if less than 10 minutes
                                        $sectionDuration .= ' ' . $seconds . 's';
                                    }
                                } else {
                                    // Less than a minute, show seconds
                                    $sectionDuration = $seconds . 's';
                                }
                            }
                            
                            $sectionId = 'section-' . ($section['id'] ?? $index);
                        ?>
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <button type="button" 
                                        class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition-colors section-toggle"
                                        data-section="<?= $sectionId ?>"
                                        aria-expanded="false">
                                    <div class="flex items-center gap-3 flex-1">
                                        <i data-lucide="chevron-right" class="w-5 h-5 text-slate-600 section-icon transition-transform" data-section="<?= $sectionId ?>" style="transform: rotate(0deg);"></i>
                                        <div class="text-left">
                                            <h4 class="font-semibold text-dark">
                                                Section <?= $index + 1 ?>: <?= esc($section['title']) ?>
                                            </h4>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 text-sm text-slate-500">
                                        <span><?= count($section['lectures'] ?? []) ?> lectures</span>
                                        <?php if ($sectionDuration): ?>
                                            <span class="px-2 py-1 bg-slate-100 rounded text-slate-700 font-medium">
                                                <i data-lucide="clock" class="w-3 h-3 inline-block mr-1"></i>
                                                <?= $sectionDuration ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </button>
                                
                                <div class="section-content px-4 pb-4" id="<?= $sectionId ?>-content" style="display: none;">
                                    <?php if (!empty($section['description'])): ?>
                                        <p class="text-sm text-slate-600 mb-3 pt-2"><?= esc($section['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="space-y-2">
                                        <?php foreach ($section['lectures'] ?? [] as $lecture): ?>
                                            <a href="<?= base_url('ksp/learning-hub/lecture/' . $course['id'] . '/' . $lecture['id']) ?>" 
                                               class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded border border-primary/10 transition-colors">
                                                <?php if (in_array($lecture['id'], $completed_lectures)): ?>
                                                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                                <?php else: ?>
                                                    <i data-lucide="play-circle" class="w-5 h-5 text-primary"></i>
                                                <?php endif; ?>
                                                <span class="text-sm text-slate-700 flex-1 <?= in_array($lecture['id'], $completed_lectures) ? 'line-through' : '' ?>">
                                                    <?= esc($lecture['title']) ?>
                                                </span>
                                                <?php if (in_array($lecture['id'], $completed_lectures)): ?>
                                                    <span class="text-xs text-green-600">Completed</span>
                                                <?php elseif (!empty($lecture['duration'])): ?>
                                                    <?php 
                                                    $lectureDuration = (int)($lecture['duration'] ?? 0);
                                                    $lectureMinutes = floor($lectureDuration / 60);
                                                    $lectureSeconds = $lectureDuration % 60;
                                                    ?>
                                                    <span class="text-xs text-slate-500">
                                                        <?php if ($lectureMinutes > 0): ?>
                                                            <?= $lectureMinutes ?>:<?= str_pad($lectureSeconds, 2, '0', STR_PAD_LEFT) ?>
                                                        <?php else: ?>
                                                            <?= $lectureSeconds ?>s
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (!empty($section['quiz_id'])): ?>
                                        <?php 
                                        // Check if all lectures in this section are completed
                                        $sectionLectures = $section['lectures'] ?? [];
                                        $sectionLectureIds = array_column($sectionLectures, 'id');
                                        $completedInSection = array_intersect($sectionLectureIds, $completed_lectures);
                                        $allLecturesCompleted = count($completedInSection) === count($sectionLectureIds);
                                        ?>
                                        
                                        <?php if ($allLecturesCompleted): ?>
                                            <a href="<?= base_url('ksp/learning-hub/quiz/' . $course['id'] . '/' . $section['id']) ?>" 
                                               class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark font-medium transition-colors">
                                                <i data-lucide="file-question" class="w-4 h-4"></i>
                                                Take Section Quiz
                                            </a>
                                        <?php else: ?>
                                            <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-slate-200 text-slate-500 rounded-lg cursor-not-allowed" 
                                                 title="Complete all lectures to unlock the quiz">
                                                <i data-lucide="lock" class="w-4 h-4"></i>
                                                <span>Complete all lectures to unlock quiz</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    // Section collapse/expand functionality
    $('.section-toggle').on('click', function() {
        const sectionId = $(this).data('section');
        const content = $('#' + sectionId + '-content');
        const icon = $('.section-icon[data-section="' + sectionId + '"]');
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        
        if (isExpanded) {
            // Collapse
            content.slideUp(300);
            icon.css('transform', 'rotate(0deg)');
            $(this).attr('aria-expanded', 'false');
        } else {
            // Expand
            content.slideDown(300);
            icon.css('transform', 'rotate(90deg)');
            $(this).attr('aria-expanded', 'true');
        }
        
        // Update icon
        lucide.createIcons();
    });
});
</script>
<style>
.section-icon {
    transition: transform 0.3s ease;
}
</style>
<?= $this->endSection() ?>

