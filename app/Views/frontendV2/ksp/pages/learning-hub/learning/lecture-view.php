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
<div class="container mx-auto px-4 py-8 pt-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sticky top-[120px]">
                <a href="<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>" 
                   class="bg-primary w-full py-2 px-4 text-center rounded-md text-white hover:bg-primary-dark font-medium mb-4 inline-block">
                    ← Back to Course
                </a>
                
                <h3 class="text-lg font-bold text-dark mb-4 pt-4 border-t border-slate-200">Course Content</h3>
                
                <!-- Sections List -->
                <div class="space-y-2 max-h-[calc(100vh-300px)] overflow-y-auto">
                    <?php 
                    $sections = $sections ?? [];
                    $totalSections = count($sections);
                    foreach ($sections as $sectionIndex => $sectionItem): 
                        $isLastSection = ($sectionIndex === $totalSections - 1);
                    ?>
                        <div class="<?= $isLastSection ? '' : 'border-b border-slate-200' ?> pb-2">
                            <h4 class="font-semibold text-dark mb-2 text-sm">
                                Section <?= $sectionIndex + 1 ?>: <?= esc($sectionItem['title']) ?>
                            </h4>
                            <ul class="space-y-1 ml-2">
                                <?php foreach ($sectionItem['lectures'] ?? [] as $lec): ?>
                                    <?php $isCurrentLecture = ($lec['id'] === $lecture['id']); ?>
                                    <li>
                                        <a href="<?= base_url('ksp/learning-hub/lecture/' . $course_id . '/' . $lec['id']) ?>" 
                                           class="text-sm flex items-center gap-2 py-1.5 px-2 rounded <?= $isCurrentLecture ? 'bg-primary/10 text-primary font-semibold border-l-2 border-primary' : 'text-slate-600 hover:text-primary hover:bg-slate-50' ?>">
                                            <?php if (in_array($lec['id'], $completed_lectures)): ?>
                                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 flex-shrink-0"></i>
                                            <?php else: ?>
                                                <i data-lucide="play-circle" class="w-4 h-4 flex-shrink-0"></i>
                                            <?php endif; ?>
                                            <span class="<?= in_array($lec['id'], $completed_lectures) ? 'line-through' : '' ?> flex-1">
                                                <?= esc($lec['title']) ?>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                
                                <?php if (!empty($sectionItem['quiz_id'])): ?>
                                    <?php 
                                    // Check if all lectures in this section are completed
                                    $sectionLectures = $sectionItem['lectures'] ?? [];
                                    $sectionLectureIds = array_column($sectionLectures, 'id');
                                    $completedInSection = array_intersect($sectionLectureIds, $completed_lectures);
                                    $allLecturesCompleted = count($completedInSection) === count($sectionLectureIds);
                                    ?>
                                    
                                    <li>
                                        <?php if ($allLecturesCompleted): ?>
                                            <a href="<?= base_url('ksp/learning-hub/quiz/' . $course_id . '/' . $sectionItem['id']) ?>" 
                                               class="text-sm text-primary hover:text-primary-dark flex items-center gap-2 py-1.5 px-2 rounded hover:bg-primary/5 font-medium">
                                                <i data-lucide="file-question" class="w-4 h-4 flex-shrink-0"></i>
                                                <span>Section Quiz</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-sm text-slate-400 flex items-center gap-2 py-1.5 px-2" 
                                                  title="Complete all lectures to unlock the quiz">
                                                <i data-lucide="lock" class="w-4 h-4 flex-shrink-0"></i>
                                                <span>Section Quiz (Locked)</span>
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
        
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-2xl font-bold text-primary"><?= esc($lecture['title']) ?></h1>
                    <?php if ($is_completed): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Completed
                        </span>
                    <?php else: ?>
                        <button id="markCompleteBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                            <i data-lucide="check" class="w-4 h-4"></i> Mark as Complete
                        </button>
                    <?php endif; ?>
                </div>
                
                <!-- Video Player -->
                <?php if (!empty($embed_code)): ?>
                    <div class="mb-6">
                        <div class="aspect-video bg-slate-900 rounded-lg overflow-hidden">
                            <?= $embed_code ?>
                        </div>
                    </div>
                <?php elseif (!empty($lecture['video_url'])): ?>
                    <div class="mb-6">
                        <div class="aspect-video bg-slate-900 rounded-lg overflow-hidden">
                            <video controls class="w-full h-full">
                                <source src="<?= esc($lecture['video_url']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Description -->
                <?php if (!empty($lecture['description'])): ?>
                    <div class="prose max-w-none mb-6">
                        <?= $lecture['description'] ?>
                    </div>
                <?php endif; ?>
                
                <!-- Attachments -->
                <?php if (!empty($lecture['attachments'])): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-dark mb-3">Attachments</h3>
                        <div class="space-y-2">
                            <?php foreach ($lecture['attachments'] as $attachment): ?>
                                <a href="<?= base_url('ksp/learning-hub/attachment/' . $attachment['id']) ?>" 
                                   class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                                    <i data-lucide="file" class="w-5 h-5 text-primary"></i>
                                    <div class="flex-1">
                                        <p class="font-medium text-dark"><?= esc($attachment['file_name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= esc($attachment['file_type']) ?></p>
                                    </div>
                                    <i data-lucide="download" class="w-5 h-5 text-slate-400"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Links -->
                <?php if (!empty($lecture['links'])): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-dark mb-3">Additional Resources</h3>
                        <div class="space-y-2">
                            <?php foreach ($lecture['links'] as $link): ?>
                                <a href="<?= esc($link['link_url']) ?>" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                    <i data-lucide="external-link" class="w-5 h-5 text-primary flex-shrink-0"></i>
                                    <span class="font-medium text-dark"><?= esc($link['link_title']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Resource URLs -->
                <?php 
                $resourceUrls = !empty($lecture['resource_urls']) ? json_decode($lecture['resource_urls'], true) : [];
                if (!empty($resourceUrls) && is_array($resourceUrls)): 
                ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-dark mb-3">Learning Resources</h3>
                        <div class="space-y-2">
                            <?php foreach ($resourceUrls as $index => $url): ?>
                                <?php if (!empty($url)): ?>
                                    <a href="<?= esc($url) ?>" target="_blank" rel="noopener noreferrer"
                                       class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors group">
                                        <i data-lucide="link" class="w-5 h-5 text-secondary flex-shrink-0"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-dark group-hover:text-primary truncate">Resource <?= $index + 1 ?></p>
                                            <p class="text-xs text-slate-500 truncate"><?= esc($url) ?></p>
                                        </div>
                                        <i data-lucide="external-link" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Course Review Section -->
                <div class="border-t border-slate-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-dark mb-4">Rate This Course</h3>

                    <div class="mb-4">
                        <p class="text-slate-600 text-sm bg-yellow-50 border-l-4 border-yellow-400 rounded px-4 py-3">
                            We would really appreciate your review! Your feedback helps us improve this course and assists other learners in making informed decisions.
                        </p>
                    </div>

                    <!-- Reviews List -->
                    <div id="courseReviewsSection" class="mb-6">
                        <h4 class="text-md font-semibold text-dark mb-2">Recent Reviews</h4>
                        <div id="reviewsList" class="space-y-4"></div>
                        <button id="loadMoreReviewsBtn" class="mt-4 px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 font-medium hidden">Read More</button>
                    </div>

                    <form id="courseReviewForm" class="p-6 mt-6 border border-slate-100 rounded-md">
                        <input type="hidden" name="course_id" value="<?= $course_id ?>">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        <input type="hidden" name="rating" id="ratingValue" value="0">
                        
                        <!-- Star Rating -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-dark mb-2">Your Rating</label>
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1" id="starRating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star-icon group cursor-pointer" data-rating="<?= $i ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-8 h-8 transition-colors text-slate-300 group-hover:text-yellow-400">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                            </svg>
                                        </span>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-sm font-medium text-slate-600" id="ratingText">Select a rating</span>
                            </div>
                        </div>
                        
                        <!-- Review Message -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-dark mb-2">Your Review (Optional)</label>
                            <textarea name="review" id="reviewMessage" rows="4" 
                                      class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none" 
                                      placeholder="Share your thoughts about this course..."></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" id="submitReviewBtn" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="send" class="w-4 h-4 inline mr-2"></i>
                            Submit Review
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Load dayjs for time formatting
    if (typeof dayjs === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js';
        script.onload = function() {
            var relScript = document.createElement('script');
            relScript.src = 'https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js';
            relScript.onload = function() {
                dayjs.extend(window.dayjs_plugin_relativeTime);
            };
            document.head.appendChild(relScript);
        };
        document.head.appendChild(script);
    } else if (typeof dayjs_plugin_relativeTime !== 'undefined') {
        dayjs.extend(dayjs_plugin_relativeTime);
    }
    lucide.createIcons();

    // --- Course Reviews Loading ---
    const courseId = $('input[name="course_id"]').val();
    let reviewsOffset = 0;
    const reviewsLimit = 5;
    let reviewsEnd = false;

    function renderReview(review) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${i <= review.rating ? '#facc15' : 'none'}" stroke="${i <= review.rating ? '#facc15' : 'currentColor'}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-5 h-5 inline-block align-middle ${i <= review.rating ? 'text-yellow-400' : 'text-slate-300'}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
        }
        // Get two initials from user_name
        let initials = 'AN';
        if (review.user_name && review.user_name.trim().length > 0) {
            const parts = review.user_name.trim().split(' ');
            if (parts.length === 1) {
                initials = parts[0].substring(0, 2).toUpperCase();
            } else {
                initials = (parts[0][0] || '') + (parts[1][0] || '');
                initials = initials.toUpperCase();
            }
        }
        let profileImg = review.user_picture 
            ? `<img src="${review.user_picture}" alt="Profile" class="w-14 h-14 rounded-full object-cover border border-slate-200">` 
            : `<span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-200 text-slate-500 font-bold text-xl">${initials}</span>`;
        // Format date as time ago if dayjs is loaded
        let timeAgo = review.created_at;
        if (typeof dayjs !== 'undefined' && dayjs(review.created_at).isValid()) {
            timeAgo = dayjs(review.created_at).fromNow();
        }
        return `<div class="bg-slate-50 rounded p-4 border border-slate-100">
            <div class="flex items-center gap-3 mb-1">
                ${profileImg}
                <div>
                    <span class="font-semibold text-slate-800 text-sm">${review.user_name || 'Anonymous'}</span><br>
                    <span class="text-xs text-slate-400">${timeAgo}</span>
                </div>
            </div>
            <div class="flex items-center gap-1 mb-2">${stars}</div>
            <div class="text-slate-700 text-sm">${review.review ? review.review.replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</div>
        </div>`;
    }

    function loadReviews(initial = false) {
        if (reviewsEnd) return;
        $('#loadMoreReviewsBtn').prop('disabled', true).text('Loading...');
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/course/reviews') ?>',
            type: 'GET',
            data: { course_id: courseId, limit: reviewsLimit, offset: reviewsOffset },
            dataType: 'json',
            success: function(res) {
                if (res.success && Array.isArray(res.reviews)) {
                    if (res.reviews.length > 0) {
                        res.reviews.forEach(r => {
                            $('#reviewsList').append(renderReview(r));
                        });
                        reviewsOffset += res.reviews.length;
                        if (res.reviews.length < reviewsLimit) {
                            reviewsEnd = true;
                            $('#loadMoreReviewsBtn').hide();
                        } else {
                            $('#loadMoreReviewsBtn').show().prop('disabled', false).text('Read More');
                        }
                    } else {
                        if (initial) {
                            $('#reviewsList').html('<div class="text-slate-400 text-sm">No reviews yet.</div>');
                        }
                        reviewsEnd = true;
                        $('#loadMoreReviewsBtn').hide();
                    }
                } else {
                    if (initial) {
                        $('#reviewsList').html('<div class="text-slate-400 text-sm">No reviews yet.</div>');
                    }
                    reviewsEnd = true;
                    $('#loadMoreReviewsBtn').hide();
                }
            },
            error: function() {
                if (initial) {
                    $('#reviewsList').html('<div class="text-slate-400 text-sm">Failed to load reviews.</div>');
                }
                $('#loadMoreReviewsBtn').hide();
            }
        });
    }

    // Initial load
    loadReviews(true);

    $('#loadMoreReviewsBtn').on('click', function() {
        loadReviews();
    });
    
    // Star Rating System
    let selectedRating = 0;
    const ratingText = $('#ratingText');
    const ratingValue = $('#ratingValue');
    
    // Use event delegation since lucide replaces the elements
    $('#starRating').on('mouseenter', '.star-icon', function() {
        const rating = parseInt($(this).data('rating'));
        updateStarDisplay(rating);
    });
    
    $('#starRating').on('mouseleave', function() {
        updateStarDisplay(selectedRating);
    });
    
    $('#starRating').on('click', '.star-icon', function() {
        selectedRating = parseInt($(this).data('rating'));
        ratingValue.val(selectedRating);
        updateStarDisplay(selectedRating);
        updateRatingText(selectedRating);
    });
    
    function updateStarDisplay(rating) {
        $('#starRating .star-icon').each(function() {
            const starRating = parseInt($(this).data('rating'));
            const svg = $(this).find('svg');
            if (svg.length > 0) {
                if (starRating <= rating) {
                    svg.attr('fill', '#facc15'); // solid yellow
                    svg.attr('stroke', '#facc15');
                    svg.removeClass('text-slate-300').addClass('text-yellow-400');
                } else {
                    svg.attr('fill', 'none');
                    svg.attr('stroke', 'currentColor');
                    svg.removeClass('text-yellow-400').addClass('text-slate-300');
                }
            }
        });
    }
    
    // On page load, ensure stars are reset
    updateStarDisplay(selectedRating);
    
    function updateRatingText(rating) {
        const ratingTexts = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };
        ratingText.text(ratingTexts[rating] || 'Select a rating');
    }
    
    // Submit Review Form
    $('#courseReviewForm').on('submit', function(e) {
        e.preventDefault();
        
        if (selectedRating === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Rating Required',
                text: 'Please select a star rating before submitting',
            });
            return;
        }
        
        const submitBtn = $('#submitReviewBtn');
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i> Submitting...');
        lucide.createIcons();
        
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/course/review') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thank You!',
                        text: response.message || 'Your review has been submitted successfully',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // Reset form
                        $('#reviewMessage').val('');
                        selectedRating = 0;
                        ratingValue.val(0);
                        updateStarDisplay(0);
                        updateRatingText(0);
                        submitBtn.prop('disabled', false).html(originalHtml);
                        lucide.createIcons();
                        // Reload reviews
                        reviewsOffset = 0;
                        reviewsEnd = false;
                        $('#reviewsList').empty();
                        loadReviews(true);
                    });
                } else {
                    throw new Error(response.message || 'Failed to submit review');
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while submitting your review';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
                
                submitBtn.prop('disabled', false).html(originalHtml);
                lucide.createIcons();
            }
        });
    });
    
    // Mark lecture as complete
    $('#markCompleteBtn').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Marking...');
        
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/lecture/' . $course_id . '/' . $lecture['id'] . '/complete') ?>',
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    btn.replaceWith('<span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Completed</span>');
                    lucide.createIcons();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Great job!',
                            text: 'Lecture marked as completed. Progress: ' + response.progress_percentage + '%',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                } else {
                    alert(response.message || 'Failed to mark as complete');
                    btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Mark as Complete');
                    lucide.createIcons();
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Mark as Complete');
                lucide.createIcons();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

