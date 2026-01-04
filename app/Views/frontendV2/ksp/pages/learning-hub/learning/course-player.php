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
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar - Course Content -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sticky top-4">
                <h3 class="text-lg font-bold text-dark mb-4">Course Content</h3>
                
                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-600">Progress</span>
                        <span class="text-sm font-bold text-primary"><?= number_format($progress, 0) ?>%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full" style="width: <?= $progress ?>%"></div>
                    </div>
                </div>
                
                <!-- Sections List -->
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    <?php foreach ($course['sections'] ?? [] as $section): ?>
                        <div class="border-b border-slate-200 pb-2">
                            <h4 class="font-semibold text-dark mb-2"><?= esc($section['title']) ?></h4>
                            <ul class="space-y-1 ml-4">
                                <?php foreach ($section['lectures'] ?? [] as $lecture): ?>
                                    <li>
                                        <a href="<?= base_url('ksp/learning-hub/lecture/' . $course['id'] . '/' . $lecture['id']) ?>" 
                                           class="text-sm text-slate-600 hover:text-primary flex items-center gap-2">
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
                                               class="text-sm text-primary hover:text-primary-dark flex items-center gap-2 font-medium">
                                                <i data-lucide="file-question" class="w-4 h-4"></i>
                                                Section Quiz
                                            </a>
                                        <?php else: ?>
                                            <span class="text-sm text-slate-400 flex items-center gap-2" 
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
                        <?php foreach ($course['sections'] ?? [] as $index => $section): ?>
                            <div class="border border-slate-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-dark">
                                        Section <?= $index + 1 ?>: <?= esc($section['title']) ?>
                                    </h4>
                                    <span class="text-sm text-slate-500">
                                        <?= count($section['lectures'] ?? []) ?> lectures
                                    </span>
                                </div>
                                <?php if (!empty($section['description'])): ?>
                                    <p class="text-sm text-slate-600 mb-3"><?= esc($section['description']) ?></p>
                                <?php endif; ?>
                                
                                <div class="space-y-2">
                                    <?php foreach ($section['lectures'] ?? [] as $lecture): ?>
                                        <a href="<?= base_url('ksp/learning-hub/lecture/' . $course['id'] . '/' . $lecture['id']) ?>" 
                                           class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded">
                                            <?php if (in_array($lecture['id'], $completed_lectures)): ?>
                                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                            <?php else: ?>
                                                <i data-lucide="play-circle" class="w-5 h-5 text-primary"></i>
                                            <?php endif; ?>
                                            <span class="text-sm text-slate-700 <?= in_array($lecture['id'], $completed_lectures) ? 'line-through' : '' ?>">
                                                <?= esc($lecture['title']) ?>
                                            </span>
                                            <?php if (in_array($lecture['id'], $completed_lectures)): ?>
                                                <span class="text-xs text-green-600 ml-auto">Completed</span>
                                            <?php elseif (!empty($lecture['duration'])): ?>
                                                <span class="text-xs text-slate-500 ml-auto">
                                                    <?= gmdate("i:s", $lecture['duration']) ?>
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
});
</script>
<?= $this->endSection() ?>

