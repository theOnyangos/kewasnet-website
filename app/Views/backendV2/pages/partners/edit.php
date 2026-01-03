<?php ?>
<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Edit Partner',
            'pageDescription' => 'Update partner information and settings',
            'breadcrumbs' => [
                ['label' => 'Partners', 'url' => base_url('auth/partners')],
                ['label' => 'Edit']
            ],
            'bannerActions' => '<a href="' . base_url('auth/partners') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Partners
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
            <!-- Edit Form -->
            <div class="bg-white rounded-xl shadow-sm p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Partner Information</h2>
                    <p class="mt-1 text-sm text-slate-500">Update the details of the partner organization</p>
                </div>

                <form id="partnerForm" action="<?= base_url('auth/partners/update/' . $partner['id']) ?>" method="POST" enctype="multipart/form-data">
                    <!-- CSRF Protection -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Partner Name -->
                        <div class="md:col-span-2">
                            <label for="partner_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Partner Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="partner_name"
                                   name="partner_name"
                                   required
                                   value="<?= esc($partner['partner_name']) ?>"
                                   placeholder="Kenya Water & Sanitation Civil Society Network"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Please enter the official name of the partner organization</p>
                        </div>

                        <!-- Partnership Type -->
                        <div>
                            <label for="partnership_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Partnership Type <span class="text-red-500">*</span>
                            </label>
                            <select id="partnership_type"
                                    name="partnership_type"
                                    required
                                    class="select2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Select partnership type</option>
                                <option value="strategic_partner" <?= $partner['partnership_type'] == 'strategic_partner' ? 'selected' : '' ?>>Strategic Partner</option>
                                <option value="implementation_partner" <?= $partner['partnership_type'] == 'implementation_partner' ? 'selected' : '' ?>>Implementation Partner</option>
                                <option value="funding_partner" <?= $partner['partnership_type'] == 'funding_partner' ? 'selected' : '' ?>>Funding Partner</option>
                                <option value="technical_partner" <?= $partner['partnership_type'] == 'technical_partner' ? 'selected' : '' ?>>Technical Partner</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Choose the type of partnership that best describes the organization</p>
                        </div>

                        <!-- Website URL -->
                        <div>
                            <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">
                                Website URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url"
                                   id="website_url"
                                   name="website_url"
                                   required
                                   value="<?= esc($partner['partner_url'] ?? '') ?>"
                                   placeholder="https://example.com"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Enter the full URL of the partner's official website</p>
                        </div>

                        <!-- Logo Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Partner Logo <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>

                            <!-- Current Logo Display -->
                            <?php if (!empty($partner['partner_logo'])): ?>
                                <div id="current-logo" class="mb-4">
                                    <p class="text-xs text-gray-500 mb-2">Current Logo:</p>
                                    <div class="relative inline-block group">
                                        <div class="w-32 h-32 rounded-lg overflow-hidden border-2 border-gray-200 shadow-sm">
                                            <img src="<?= base_url($partner['partner_logo']) ?>" class="w-full h-full object-contain bg-gray-50" alt="Current logo">
                                        </div>
                                        <button type="button"
                                                onclick="removeCurrentLogo()"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-all z-10 flex items-center justify-center"
                                                title="Remove current logo">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 italic">Click the X button above to remove this logo</p>
                                </div>
                            <?php endif; ?>

                            <!-- Upload Container -->
                            <div id="upload-container" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-primary transition-colors">
                                <div class="dz-message">
                                    <i data-lucide="upload" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                                    <p class="text-sm text-gray-600 mb-1">Drag & drop your logo here or click to browse</p>
                                    <p class="text-xs text-gray-400">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div id="upload-progress" class="mt-4 hidden">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Uploading...</span>
                                    <span id="progress-percent">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="progress-bar" class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Preview Container -->
                            <div id="preview-container" class="mt-4 flex flex-wrap gap-4"></div>

                            <!-- Hidden input to track if logo should be removed -->
                            <input type="hidden" id="remove_logo" name="remove_logo" value="0">
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button"
                                onclick="window.location.href='<?= base_url('auth/partners') ?>'"
                                class="px-8 py-3 rounded-full text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="gradient-btn rounded-full px-8 text-white flex justify-center items-center gap-2">
                            <i data-lucide="save" class="w-5 h-5 z-10"></i>
                            <span>Update Partner</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    let selectedFile = null;
    let removeLogo = false;

    $(document).ready(function() {
        lucide.createIcons();

        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            minimumResultsForSearch: Infinity
        });

        // Initialize file upload
        initFileUpload();

        // Form submission
        $('#partnerForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalBtnText = submitBtn.html();

            submitBtn.prop('disabled', true);
            submitBtn.html(`
                <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                Processing...
            `);

            // Show progress bar
            $('#upload-progress').removeClass('hidden');
            $('#progress-bar').css('width', '0%');
            $('#progress-percent').text('0%');

            // Prepare form data
            const formData = new FormData(form[0]);

            // Add the selected file
            if (selectedFile) {
                formData.append('partner_logo', selectedFile);
            }

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $('#progress-bar').css('width', percent + '%');
                            $('#progress-percent').text(percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(data) {
                    if (data.status === 'success') {
                        showToast(data.message || 'Partner updated successfully!', 'success');
                        setTimeout(function() {
                            window.location.href = '<?= base_url('auth/partners') ?>';
                        }, 1500);
                    } else {
                        if (data.errors) {
                            showFormErrors(data.errors);
                        } else {
                            showToast(data.message || 'Failed to update partner', 'error');
                        }
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while saving';
                    showToast(errorMessage, 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                    $('#upload-progress').addClass('hidden');
                }
            });
        });
    });

    function initFileUpload() {
        const uploadContainer = $('#upload-container');

        // Handle click to select file
        uploadContainer.on('click', function() {
            $('<input>').attr({
                type: 'file',
                accept: 'image/*',
                class: 'hidden'
            }).on('change', function(e) {
                handleFileSelect(e.target.files);
            }).click();
        });

        // Handle drag and drop
        uploadContainer.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('border-primary bg-gray-50');
        }).on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('border-primary bg-gray-50');
        }).on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('border-primary bg-gray-50');
            if (e.originalEvent.dataTransfer.files.length) {
                handleFileSelect(e.originalEvent.dataTransfer.files);
            }
        });
    }

    function handleFileSelect(files) {
        if (files.length === 0) return;

        const file = files[0];

        // Validate file type
        if (!file.type.match('image.*')) {
            showToast('Please upload an image file (JPEG, PNG, GIF)', 'error');
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showToast('File size exceeds 5MB limit', 'error');
            return;
        }

        // Store the selected file
        selectedFile = file;

        // If uploading a new file, reset the remove_logo flag
        $('#remove_logo').val('0');
        removeLogo = false;

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-container').html(`
                <div class="relative group">
                    <div class="w-32 h-32 rounded-lg overflow-hidden border-2 border-gray-200 shadow-sm">
                        <img src="${e.target.result}" class="w-full h-full object-contain bg-gray-50">
                    </div>
                    <button type="button" onclick="removeFile()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `);

            lucide.createIcons();
        };
        reader.readAsDataURL(file);
    }

    function removeFile() {
        $('#preview-container').empty();
        selectedFile = null;

        // If current logo was already removed, keep that flag
        // Otherwise, if there's no new file, we're not removing anything
        if (!removeLogo) {
            $('#remove_logo').val('0');
        }
    }

    function removeCurrentLogo() {
        Swal.fire({
            title: 'Remove Logo?',
            text: "This action will permanently delete the logo from the system.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Removing Logo...',
                    text: 'Please wait while we remove the logo',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Make DELETE request to remove logo
                $.ajax({
                    url: '<?= base_url('auth/partners/remove-logo/' . $partner['id']) ?>',
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === 'success') {
                            $('#current-logo').hide().remove();
                            $('#remove_logo').val('1');
                            removeLogo = true;

                            // Clear any new file selection
                            selectedFile = null;
                            $('#preview-container').empty();

                            Swal.fire({
                                title: 'Removed!',
                                text: data.message || 'Logo has been removed successfully.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to remove logo',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to remove logo',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }

    function showToast(message, type = 'success') {
        $('.custom-toast').remove();

        const icon = type === 'success' ? 'check-circle' :
                    type === 'error' ? 'alert-circle' : 'info';

        const toast = $(`
            <div class="custom-toast fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }">
                <div class="flex items-center">
                    <i data-lucide="${icon}" class="w-5 h-5 mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `).appendTo('body');

        lucide.createIcons();

        toast.hide().fadeIn(300);

        setTimeout(function() {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    function showFormErrors(errors) {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.each(errors, function(field, message) {
            const input = $(`[name="${field}"]`);

            if (input.length) {
                input.addClass('is-invalid border-red-500');

                $('<div>')
                    .addClass('invalid-feedback text-red-500 text-xs mt-1')
                    .text(message)
                    .insertAfter(input);
            }
        });
    }
</script>
<?= $this->endSection() ?>
