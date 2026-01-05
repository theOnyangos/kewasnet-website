<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?><?= $title ?><?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Create New Event',
        'pageDescription' => 'Fill in the details below to create a new event',
        'breadcrumbs' => [
            ['label' => 'Events', 'url' => base_url('auth/events')],
            ['label' => 'Create']
        ],
        'bannerActions' => '<a href="' . base_url('auth/events') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Events
        </a>'
    ]) ?>

    <div class="bg-white rounded-b-xl shadow-sm max-w-full mx-5">
        <form class="p-6 space-y-6" id="createEventForm" method="POST" action="<?= site_url('auth/events/store') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <!-- Title Field -->
            <div>
                <label for="title" class="block text-sm font-medium text-dark mb-1">Event Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter event title" required>
                <p class="mt-1 text-xs text-gray-500">A clear and descriptive title for your event</p>
            </div>

            <!-- Event Type Selection -->
            <div>
                <label class="block text-sm font-medium text-dark mb-3">Event Type <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                        <input type="radio" name="event_type" value="free" checked class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-dark">Free Event</span>
                            <p class="text-xs text-gray-500">No payment required</p>
                        </div>
                    </label>
                    <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                        <input type="radio" name="event_type" value="paid" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-dark">Paid Event</span>
                            <p class="text-xs text-gray-500">Requires payment for tickets</p>
                        </div>
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">Select whether this event requires payment or is free to attend</p>
            </div>

            <!-- Date and Time Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-dark mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" id="start_date" name="start_date" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" required>
                    <p class="mt-1 text-xs text-gray-500">The date when the event starts</p>
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-dark mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Optional end date for multi-day events</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-dark mb-1">Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">The time when the event starts</p>
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-dark mb-1">End Time</label>
                    <input type="time" id="end_time" name="end_time" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">The time when the event ends</p>
                </div>
            </div>

            <!-- Description Field -->
            <div>
                <label for="description" class="block text-sm font-medium text-dark mb-1">Description</label>
                <div class="rounded-lg">
                    <textarea id="description" name="description" class="summernote-editor w-full px-4 py-3 focus:outline-none" placeholder="Describe your event in detail"></textarea>
                </div>
                <p class="mt-1 text-xs text-gray-500">Provide a detailed description of your event</p>
            </div>

            <!-- Venue and Address -->
            <div>
                <label for="venue" class="block text-sm font-medium text-dark mb-1">Venue</label>
                <input type="text" id="venue" name="venue" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Event venue name">
                <p class="mt-1 text-xs text-gray-500">Name of the venue or location</p>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-dark mb-1">Address</label>
                <textarea id="address" name="address" rows="2" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="Full address of the event location"></textarea>
                <p class="mt-1 text-xs text-gray-500">Complete address including street, area, etc.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-dark mb-1">City</label>
                    <input type="text" id="city" name="city" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="City">
                    <p class="mt-1 text-xs text-gray-500">City where the event is located</p>
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-dark mb-1">Country</label>
                    <input type="text" id="country" name="country" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Country">
                    <p class="mt-1 text-xs text-gray-500">Country where the event is located</p>
                </div>
            </div>

            <!-- Capacity -->
            <div>
                <label for="total_capacity" class="block text-sm font-medium text-dark mb-1">Total Capacity</label>
                <input type="number" id="total_capacity" name="total_capacity" min="1" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter maximum number of attendees">
                <p class="mt-1 text-xs text-gray-500">Maximum number of attendees. Leave empty for unlimited capacity</p>
            </div>

            <!-- Image Upload Section -->
            <div class="pt-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-dark">Event Images</h3>
                    <p class="text-sm text-gray-500">Upload images to represent your event.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Event Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Event Image</label>
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
                        <p class="mt-1 text-xs text-gray-500">Main image displayed on event listings</p>
                    </div>

                    <!-- Banner Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Banner Image</label>
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
                                <p class="text-xs text-slate-500">PNG, JPG, GIF up to 2MB</p>
                                <div id="banner-preview" class="mt-4"></div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Large banner image for event details page</p>
                    </div>
                </div>
            </div>

            <!-- Status and Publishing Options -->
            <div class="pt-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-dark">Publishing Options</h3>
                    <p class="text-sm text-gray-500">Control the visibility and accessibility of your event.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-3">Status <span class="text-red-500">*</span></label>
                    <div class="flex flex-col space-y-3">
                        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                            <input type="radio" name="status" value="draft" checked class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-dark">Draft</span>
                                <p class="text-xs text-gray-500">Save as draft for later editing</p>
                            </div>
                        </label>
                        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                            <input type="radio" name="status" value="published" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-dark">Published</span>
                                <p class="text-xs text-gray-500">Make it visible to the public immediately</p>
                            </div>
                        </label>
                        <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                            <input type="radio" name="status" value="cancelled" class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-dark">Cancelled</span>
                                <p class="text-xs text-gray-500">Mark event as cancelled</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- CSRF Token -->
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-borderColor">
                <a href="<?= base_url('auth/events') ?>" class="px-8 py-3 rounded-[50px] text-primary bg-primaryShades-50 hover:bg-primaryShades-100">
                    Cancel
                </a>
                <button type="submit" class="gradient-btn rounded-[50px] px-8 text-white flex justify-center items-center gap-2">
                    <i data-lucide="calendar-check" class="w-5 h-5 z-10"></i>
                    <span>Create Event</span>
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

    // Image preview functionality
    $('#image').on('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = $('#image-preview');
        previewContainer.empty();
        
        if (file && file.type.startsWith('image/')) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Event image must be less than 2MB',
                    confirmButtonColor: '#ef4444'
                });
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`
                    <img src="${e.target.result}" alt="Event Image Preview" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200 mx-auto">
                `);
                previewContainer.append(preview);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#banner').on('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = $('#banner-preview');
        previewContainer.empty();
        
        if (file && file.type.startsWith('image/')) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Banner image must be less than 2MB',
                    confirmButtonColor: '#ef4444'
                });
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`
                    <img src="${e.target.result}" alt="Banner Image Preview" class="max-w-48 max-h-32 rounded-lg border-2 border-gray-200 mx-auto">
                `);
                previewContainer.append(preview);
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submission
    $('#createEventForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const form = this;
        const submitBtn = $(form).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();

        // Disable submit button
        submitBtn.prop('disabled', true);
        submitBtn.html(`
            <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
            <span class="z-10">Creating...</span>
        `);

        // Show loading alert
        Swal.fire({
            title: 'Creating Event...',
            text: 'Please wait while we create your event',
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
                        text: response.message || 'Event created successfully',
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
                        text: response.message || 'Failed to create event',
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
                    text: response.message || 'An error occurred while creating the event',
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

    // Clear errors on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid border-red-500');
        $(this).next('.invalid-feedback').remove();
    });
});
</script>
<?= $this->endSection() ?>
