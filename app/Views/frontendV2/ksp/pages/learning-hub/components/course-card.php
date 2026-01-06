<?php
/**
 * Course Card Component
 * 
 * This component receives $course as a variable passed from the parent view.
 * When using view() helper, variables are extracted automatically.
 */


// When using view() helper, variables are extracted automatically
// Check if course is set directly (standard CI4 behavior)
if (!isset($course)) {
    // Try extracting from any available data
    if (isset($data) && is_array($data)) {
        if (isset($data['course'])) {
            $course = $data['course'];
        }
    }
}

// Final validation
if (!isset($course) || !is_array($course) || empty($course)) {
    return;
}

$isPaid = (($course['price'] ?? 0) > 0 || ($course['is_paid'] ?? 0) == 1);
$price = ($course['discount_price'] ?? 0) > 0 ? ($course['discount_price'] ?? 0) : ($course['price'] ?? 0);
$isEnrolled = $course['is_enrolled'] ?? false;
$progress = $course['progress'] ?? 0;

?>

<div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden hover:shadow-2xl transition-shadow z-10 relative">
    <?php if (!empty($course['image_url'])): ?>
        <img src="<?= base_url(esc($course['image_url'])) ?>" alt="<?= esc($course['title']) ?>" 
             class="w-full h-48 object-cover">
    <?php endif; ?>
    
    <div class="p-6">
        <div class="flex items-center justify-between mb-2">
            <?php if ($isEnrolled): ?>
                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                    Enrolled
                </span>
            <?php else: ?>
                <span class="px-2 py-1 text-xs font-semibold rounded <?= $isPaid ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                    <?= $isPaid ? 'Paid' : 'Free' ?>
                </span>
            <?php endif; ?>
            <span class="text-xs text-slate-500"><?= esc($course['level'] ?? 'beginner') ?></span>
        </div>
        
        <h3 class="text-xl font-bold text-dark mb-2">
            <a href="<?= base_url('ksp/learning-hub/course/' . $course['id']) ?>" 
               class="hover:text-primary">
                <?= esc($course['title']) ?>
            </a>
        </h3>
        
        <p class="text-slate-600 text-sm mb-4 line-clamp-2">
            <?= esc($course['summary'] ?? substr($course['description'] ?? '', 0, 100)) ?>
        </p>
        
        <?php if ($isEnrolled): ?>
            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-xs text-slate-600 mb-1">
                    <span>Progress</span>
                    <span><?= number_format($progress, 0) ?>%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: <?= $progress ?>%"></div>
                </div>
            </div>
            
            <a href="<?= base_url('ksp/learning-hub/learn/' . $course['id']) ?>" 
               class="block w-full text-center gradient-btn px-4 py-2 rounded-lg text-white font-medium hover:shadow-md transition-shadow">
                Continue Learning
            </a>
        <?php else: ?>
            <div class="flex items-center justify-between">
                <?php if ($isPaid): ?>
                    <span class="text-lg font-bold text-primary">
                        <?= number_format($price, 2) ?> <?= 'KES' ?>
                    </span>
                <?php else: ?>
                    <span class="text-lg font-bold text-green-600">Free</span>
                <?php endif; ?>
                
                <a href="<?= base_url('ksp/learning-hub/course/' . $course['id']) ?>" 
                   class="text-primary hover:text-primary-dark font-medium">
                    View Details →
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

