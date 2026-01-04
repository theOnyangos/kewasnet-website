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
    <!-- Back Navigation -->
    <div class="mb-6">
        <nav class="flex items-center space-x-4">
            <button onclick="window.history.back();" 
                    class="inline-flex items-center px-4 py-2 text-slate-600 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                <span>Back</span>
            </button>
            <span class="text-slate-400">|</span>
            <a href="<?= base_url('ksp/learning-hub/courses') ?>" 
               class="inline-flex items-center px-4 py-2 text-slate-600 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors">
                <i data-lucide="book-open" class="w-5 h-5 mr-2"></i>
                <span>All Courses</span>
            </a>
        </nav>
    </div>
    
    <!-- Course Header -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8 mb-6">
        <?php if (!empty($course['image_url'])): ?>
            <img src="<?= base_url(esc($course['image_url'])) ?>" alt="<?= esc($course['title']) ?>" 
                 class="w-full h-64 object-cover rounded-lg mb-6">
        <?php endif; ?>
        
        <h1 class="text-3xl font-bold text-dark mb-4"><?= esc($course['title']) ?></h1>
        <p class="text-lg text-slate-600 mb-6"><?= esc($course['summary'] ?? '') ?></p>
        
        <div class="flex items-center gap-4 mb-6">
            <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">
                <?= esc($course['level'] ?? 'beginner') ?>
            </span>
            <span class="text-slate-600">
                <i data-lucide="clock" class="w-4 h-4 inline"></i> <?= esc($course['duration'] ?? 'N/A') ?>
            </span>
            <?php if ($course['certificate']): ?>
                <span class="text-slate-600">
                    <i data-lucide="award" class="w-4 h-4 inline"></i> Certificate Available
                </span>
            <?php endif; ?>
        </div>
        
        <?php 
        $isPaid = ($course['price'] > 0 || ($course['is_paid'] ?? 0) == 1);
        $price = $course['discount_price'] > 0 ? $course['discount_price'] : $course['price'];
        ?>
        
        <div class="flex items-center gap-4">
            <?php if ($is_enrolled): ?>
                <a href="<?= base_url('ksp/learning-hub/learn/' . $course['id']) ?>" 
                   class="gradient-btn px-6 py-3 rounded-lg text-white font-medium">
                    Continue Learning
                </a>
            <?php elseif ($user_id): ?>
                <?php if ($isPaid): ?>
                    <button id="enrollBtn" data-course-id="<?= $course['id'] ?>" data-is-paid="1"
                            class="gradient-btn px-6 py-3 rounded-lg text-white font-medium">
                        Enroll Now - <?= number_format($price, 2) ?> KES
                    </button>
                <?php else: ?>
                    <button id="enrollBtn" data-course-id="<?= $course['id'] ?>" data-is-paid="0"
                            class="gradient-btn px-6 py-3 rounded-lg text-white font-medium">
                        Enroll for Free
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <!-- Not logged in - show login prompt -->
                <a href="<?= base_url('ksp/login?redirect=' . urlencode(current_url())) ?>" 
                   class="gradient-btn px-6 py-3 rounded-lg text-white font-medium inline-flex items-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <?php if ($isPaid): ?>
                        Login to Enroll - <?= number_format($price, 2) ?> KES
                    <?php else: ?>
                        Login to Enroll for Free
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Course Description -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8 mb-6">
        <h2 class="text-2xl font-bold text-dark mb-4">About This Course</h2>
        <div class="prose max-w-none">
            <?= $course['description'] ?? '' ?>
        </div>
    </div>
    
    <!-- What You'll Learn -->
    <?php 
    // Parse goals - can be JSON array, newline-separated string, or already an array
    $goals = [];
    if (!empty($course['goals'])) {
        $goalsData = $course['goals'];
        
        // Try to decode as JSON first
        if (is_string($goalsData)) {
            $decoded = json_decode($goalsData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $goals = $decoded;
            } else {
                // If not JSON, try splitting by newlines
                $goals = array_filter(array_map('trim', explode("\n", $goalsData)));
            }
        } elseif (is_array($goalsData)) {
            $goals = $goalsData;
        }
    }
    
    if (!empty($goals)):
    ?>
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8 mb-6">
        <h2 class="text-2xl font-bold text-dark mb-6">What You'll Learn</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($goals as $goal): 
                $goalText = is_array($goal) ? ($goal['title'] ?? $goal['text'] ?? '') : $goal;
                if (empty(trim($goalText))) continue;
            ?>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-primary"></i>
                        </div>
                    </div>
                    <p class="text-slate-700 flex-1"><?= esc($goalText) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Course Curriculum (Overview only for non-enrolled) -->
    <?php if ($is_enrolled): ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8">
            <h2 class="text-2xl font-bold text-dark mb-4">Course Curriculum</h2>
            <p class="text-slate-600">You have access to <?= $course['sections_count'] ?? 0 ?> sections and <?= $course['lectures_count'] ?? 0 ?> lectures.</p>
            <a href="<?= base_url('ksp/learning-hub/learn/' . $course['id']) ?>" 
               class="text-primary hover:text-primary-dark font-medium mt-4 inline-block">
                View Full Curriculum →
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8">
            <h2 class="text-2xl font-bold text-dark mb-4">Course Curriculum</h2>
            <p class="text-slate-600 mb-4">This course includes:</p>
            <ul class="list-disc list-inside space-y-2 text-slate-600">
                <li><?= $course['sections_count'] ?? 0 ?> Sections</li>
                <li><?= $course['lectures_count'] ?? 0 ?> Lectures</li>
                <?php if ($course['certificate']): ?>
                    <li>Certificate of Completion</li>
                <?php endif; ?>
            </ul>
            <p class="text-slate-500 text-sm mt-4 italic">
                Enroll to see the full curriculum and access all course materials.
            </p>
        </div>
    <?php endif; ?>
</div>

<div id="statusMessage" class="fixed top-4 right-4 z-50 hidden"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Paystack Inline JavaScript SDK -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
$(document).ready(function() {
    lucide.createIcons();

    $('#enrollBtn').on('click', function() {
        const courseId = $(this).data('course-id');
        const isPaid = $(this).data('is-paid');
        const btn = $(this);
        const originalText = btn.html();

        btn.prop('disabled', true).html('Processing...');

        // If it's a paid course, initiate payment directly
        if (isPaid) {
            initiatePayment(courseId, btn, originalText);
        } else {
            // For free courses, enroll directly
            enrollInCourse(courseId, btn, originalText);
        }
    });

    function enrollInCourse(courseId, btn, originalText) {
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/enroll') ?>',
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                course_id: courseId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    showStatus(response.message || 'Enrollment failed', 'alert-circle', 'bg-red-100 text-red-800');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                showStatus(errorMsg, 'alert-circle', 'bg-red-100 text-red-800');
                btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function initiatePayment(courseId, btn, originalText) {
        console.log('Initiating payment for course:', courseId);
        $.ajax({
            url: '<?= base_url('ksp/payment/initiate') ?>',
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                course_id: courseId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                console.log('Payment initiation response:', response);
                if (response.status === 'success') {
                    // Use Paystack Inline (Popup) instead of redirect
                    openPaystackPopup(response, btn, originalText);
                } else {
                    console.error('Payment initiation failed:', response.message);
                    showStatus(response.message || 'Payment initialization failed', 'alert-circle', 'bg-red-100 text-red-800');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                console.error('Payment AJAX error:', xhr);
                const errorMsg = xhr.responseJSON?.message || 'Payment initialization failed. Please try again.';
                showStatus(errorMsg, 'alert-circle', 'bg-red-100 text-red-800');
                btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function openPaystackPopup(paymentData, btn, originalText) {
        console.log('Opening Paystack popup with data:', paymentData);
        
        try {
            const handler = PaystackPop.setup({
                key: paymentData.public_key,
                email: paymentData.email,
                amount: paymentData.amount, // Amount in kobo (already converted)
                currency: paymentData.currency || 'KES',
                ref: paymentData.reference,
                metadata: paymentData.metadata,
                onClose: function() {
                    // User closed the popup without completing payment
                    console.log('Payment popup closed');
                    showStatus('Payment cancelled', 'x-circle', 'bg-yellow-100 text-yellow-800');
                    btn.prop('disabled', false).html(originalText);
                },
                callback: function(response) {
                    // Payment successful, verify the transaction
                    console.log('Payment successful, verifying:', response.reference);
                    verifyPayment(response.reference, btn, originalText);
                }
            });

            handler.openIframe();
        } catch (error) {
            console.error('Error opening Paystack popup:', error);
            showStatus('Failed to open payment window', 'alert-circle', 'bg-red-100 text-red-800');
            btn.prop('disabled', false).html(originalText);
        }
    }

    function verifyPayment(reference, btn, originalText) {
        btn.html('Verifying payment...');

        $.ajax({
            url: '<?= base_url('ksp/payment/verify') ?>',
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                reference: reference,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    showStatus('Payment successful! Redirecting...', 'check-circle', 'bg-green-100 text-green-800');
                    setTimeout(function() {
                        window.location.href = '<?= base_url('ksp/learning-hub/learn/') ?>' + response.course_id;
                    }, 1500);
                } else {
                    showStatus(response.message || 'Payment verification failed', 'alert-circle', 'bg-red-100 text-red-800');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Payment verification failed. Please contact support.';
                showStatus(errorMsg, 'alert-circle', 'bg-red-100 text-red-800');
                btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function showStatus(message, icon, classes) {
        $('#statusMessage').removeClass('hidden')
            .html(`<div class="${classes} px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
                <i data-lucide="${icon}" class="w-5 h-5"></i>
                <span>${message}</span>
            </div>`);
        lucide.createIcons();
        setTimeout(() => $('#statusMessage').addClass('hidden'), 5000);
    }
});
</script>
<?= $this->endSection() ?>

