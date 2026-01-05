<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?><?= $title ?><?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Edit Event',
        'pageDescription' => 'Update the details below to modify your event',
        'breadcrumbs' => [
            ['label' => 'Events', 'url' => base_url('auth/events')],
            ['label' => 'Edit']
        ],
        'bannerActions' => '<a href="' . base_url('auth/events') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Events
        </a>'
    ]) ?>

    <div class="bg-white rounded-b-xl shadow-sm max-w-full event-form-container mx-5">
        <form class="p-6 space-y-6" id="editEventForm" method="POST" action="<?= site_url('auth/events/update/' . $event['id']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Basic Information Section -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-dark mb-4">Basic Information</h3>

                <div>
                    <label for="title" class="block text-sm font-medium text-dark mb-1">Event Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="<?= esc($event['title']) ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter event title" required>
                    <p class="mt-1 text-xs text-gray-500">A clear and descriptive title for your event.</p>
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                    <div class="flex w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                            kewasnet.co.ke/events/
                        </span>
                        <input type="text" id="slug" name="slug" value="<?= esc($event['slug']) ?>" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Auto-generated from title. Edit if needed.</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-dark mb-1">Description</label>
                    <div class="rounded-lg">
                        <textarea id="description" name="description" class="summernote-editor w-full px-4 py-3 focus:outline-none" placeholder="Describe your event in detail"><?= esc($event['description'] ?? '') ?></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Provide a comprehensive overview of the event.</p>
                </div>
            </div>

            <!-- Event Details Section -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-dark mb-4">Event Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-dark mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" id="start_date" name="start_date" value="<?= esc($event['start_date']) ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" required>
                        <p class="mt-1 text-xs text-gray-500">The date the event begins.</p>
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-dark mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="<?= esc($event['end_date'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">The date the event ends (optional).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-dark mb-1">Start Time</label>
                        <input type="time" id="start_time" name="start_time" value="<?= esc($event['start_time'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">The time the event begins (optional).</p>
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-dark mb-1">End Time</label>
                        <input type="time" id="end_time" name="end_time" value="<?= esc($event['end_time'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">The time the event ends (optional).</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="venue" class="block text-sm font-medium text-dark mb-1">Venue</label>
                    <input type="text" id="venue" name="venue" value="<?= esc($event['venue'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Event venue">
                    <p class="mt-1 text-xs text-gray-500">The physical or virtual location of the event.</p>
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-dark mb-1">Address</label>
                    <textarea id="address" name="address" rows="2" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="Full address"><?= esc($event['address'] ?? '') ?></textarea>
                    <p class="mt-1 text-xs text-gray-500">The full address of the event venue.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-dark mb-1">City</label>
                        <input type="text" id="city" name="city" value="<?= esc($event['city'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="City">
                        <p class="mt-1 text-xs text-gray-500">The city where the event is held.</p>
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-dark mb-1">Country</label>
                        <input type="text" id="country" name="country" value="<?= esc($event['country'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Country">
                        <p class="mt-1 text-xs text-gray-500">The country where the event is held.</p>
                    </div>
                </div>
            </div>

            <!-- Event Images Section -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-dark mb-4">Event Images</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Event Image</label>
                        <?php if (!empty($event['image_url'])): ?>
                            <div class="mb-3">
                                <img src="<?= base_url($event['image_url']) ?>" alt="Current Event Image" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200">
                                <p class="text-xs text-gray-500 mt-1">Current image</p>
                            </div>
                        <?php endif; ?>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none">
                                        <span>Upload event image</span>
                                        <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, GIF up to 2MB</p>
                                <div id="image-preview" class="mt-4"></div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">This image will be displayed on the event listing.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Banner Image</label>
                        <?php if (!empty($event['banner_url'])): ?>
                            <div class="mb-3">
                                <img src="<?= base_url($event['banner_url']) ?>" alt="Current Banner Image" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200">
                                <p class="text-xs text-gray-500 mt-1">Current banner</p>
                            </div>
                        <?php endif; ?>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600">
                                    <label for="banner" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none">
                                        <span>Upload banner image</span>
                                        <input id="banner" name="banner" type="file" accept="image/*" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, GIF up to 5MB</p>
                                <div id="banner-preview" class="mt-4"></div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">This image will be used as a banner on the event details page.</p>
                    </div>
                </div>
            </div>

            <!-- Publishing Options -->
            <div class="pt-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-dark">Publishing Options</h3>
                    <p class="text-sm text-gray-500">Control the visibility and capacity of your event.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-3">Event Type</label>
                        <div class="flex flex-col space-y-3">
                            <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                <input type="radio" name="event_type" value="free" <?= $event['event_type'] === 'free' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-dark">Free Event</span>
                                    <p class="text-xs text-gray-500">Attendees can register without payment.</p>
                                </div>
                            </label>
                            <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                <input type="radio" name="event_type" value="paid" <?= $event['event_type'] === 'paid' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-dark">Paid Event</span>
                                    <p class="text-xs text-gray-500">Attendees must pay to register and receive a ticket.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark mb-3">Status</label>
                        <div class="flex flex-col space-y-3">
                            <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                <input type="radio" name="status" value="draft" <?= $event['status'] === 'draft' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-dark">Draft</span>
                                    <p class="text-xs text-gray-500">Save as draft for later editing.</p>
                                </div>
                            </label>
                            <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                <input type="radio" name="status" value="published" <?= $event['status'] === 'published' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-dark">Published</span>
                                    <p class="text-xs text-gray-500">Make the event live and visible to the public.</p>
                                </div>
                            </label>
                            <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                <input type="radio" name="status" value="cancelled" <?= $event['status'] === 'cancelled' ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-dark">Cancelled</span>
                                    <p class="text-xs text-gray-500">Mark the event as cancelled (will be hidden).</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="total_capacity" class="block text-sm font-medium text-dark mb-1">Total Capacity</label>
                    <input type="number" id="total_capacity" name="total_capacity" value="<?= esc($event['total_capacity'] ?? '') ?>" min="1" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Leave empty for unlimited">
                    <p class="mt-1 text-xs text-gray-500">Maximum number of attendees. Leave empty for unlimited capacity.</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-borderColor">
                <a href="<?= base_url('auth/events') ?>" class="px-8 py-3 rounded-[50px] text-primary bg-primaryShades-50 hover:bg-primaryShades-100">
                    Cancel
                </a>
                <button type="submit" class="gradient-btn rounded-[50px] px-8 text-white flex justify-center items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5 z-10"></i>
                    <span>Update Event</span>
                </button>
            </div>
        </form>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    lucide.createIcons();

    // Initialize Summernote
    $('.summernote-editor').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    // Helper function to generate slug
    function generateSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remove all non-word characters (except spaces and hyphens)
            .replace(/\s+/g, '-')     // Replace spaces with hyphens
            .replace(/--+/g, '-')     // Replace multiple hyphens with single hyphen
            .replace(/^-+|-+$/g, '')  // Remove leading/trailing hyphens
            .trim();
    }

    // Auto-generate slug from title (only if slug is empty or matches old title)
    const originalTitle = '<?= esc($event['title'], 'js') ?>';
    $('#title').on('input', function() {
        const $slugField = $('#slug');
        const currentSlug = $slugField.val();
        // Only auto-update if slug hasn't been manually changed or matches old title's slug
        if (!$slugField.data('manual') || currentSlug === generateSlug(originalTitle)) {
            const title = $(this).val();
            const slug = generateSlug(title);
            $slugField.val(slug);
        }
    });

    // Mark slug as manually edited when user types in it
    $('#slug').on('input', function() {
        $(this).data('manual', true);
    });

    // Image preview functionality
    function setupImageUpload(inputId, previewId, maxSizeMB) {
        const $input = $(`#${inputId}`);
        const $previewContainer = $(`#${previewId}`);

        $input.on('change', function(e) {
            const file = e.target.files[0];
            $previewContainer.empty();

            if (file && file.type.startsWith('image/')) {
                if (file.size > maxSizeMB * 1024 * 1024) {
                    showToast(`Image must be less than ${maxSizeMB}MB`, 'error');
                    $(this).val('');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = $(`
                        <div class="file-preview mt-2">
                            <img src="${e.target.result}" alt="Image Preview" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200">
                            <div class="remove-file cursor-pointer" data-input-id="${inputId}">×</div>
                        </div>
                    `);
                    $previewContainer.append(preview);
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle image removal
        $(document).on('click', `.remove-file[data-input-id="${inputId}"]`, function() {
            $input.val('');
            $previewContainer.empty();
        });
    }

    setupImageUpload('image', 'image-preview', 2); // 2MB limit for event image
    setupImageUpload('banner', 'banner-preview', 5); // 5MB limit for banner image

    // Form submission handler
    $('#editEventForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const form = this;
        const submitBtn = $(form).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();

        // Disable submit button
        submitBtn.prop('disabled', true);
        submitBtn.html(`
            <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
            <span class="z-10">Updating...</span>
        `);

        // Show loading alert
        Swal.fire({
            title: 'Updating Event...',
            text: 'Please wait while we update your event',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'Event updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            window.location.href = '<?= base_url('auth/events') ?>';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Failed to update event',
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK'
                    });
                    if (response.errors) {
                        showFormErrors(response.errors);
                    }
                    // Re-enable submit button
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalBtnText);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'An error occurred while updating the event',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
                if (response.errors) {
                    showFormErrors(response.errors);
                }
                // Re-enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalBtnText);
            }
        });
    });


    // Function to show form errors
    function showFormErrors(errors) {
        clearFormErrors();
        $.each(errors, function(fieldName, message) {
            const $field = $(`[name="${fieldName}"]`);
            if ($field.length) {
                $field.addClass('is-invalid border-red-500');
                const $errorDiv = $('<div>', {
                    class: 'invalid-feedback text-red-500 text-xs mt-1',
                    text: message
                });
                $field.after($errorDiv);
                if ($('.is-invalid').first().is($field)) {
                    $field.focus();
                    $('html, body').animate({
                        scrollTop: $field.offset().top - 100
                    }, 500);
                }
            }
        });
    }

    // Function to clear form errors
    function clearFormErrors() {
        $('.is-invalid').removeClass('is-invalid border-red-500');
        $('.invalid-feedback').remove();
    }

    // Clear field errors on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid border-red-500');
        $(this).next('.invalid-feedback').remove();
    });
});
</script>
<?= $this->endSection() ?>
