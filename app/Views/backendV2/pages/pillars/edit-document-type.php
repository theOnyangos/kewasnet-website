<?php 
    // Ensure document type is available
    if (!isset($documentType) || empty($documentType)) {
        throw new \RuntimeException('Document type not found');
    }
    
    // Convert to array if object
    if (is_object($documentType)) {
        $documentType = (array) $documentType;
    }
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Edit Document Type',
            'pageDescription' => 'Update document type information',
            'breadcrumbs' => [
                ['label' => 'Pillars', 'url' => base_url('auth/pillars')],
                ['label' => 'Document Types', 'url' => base_url('auth/pillars/document-types')],
                ['label' => 'Edit Document Type']
            ],
            'bannerActions' => '<a href="' . base_url('auth/pillars/document-types') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Document Types
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Document Type Form Section -->
        <div class="bg-white rounded-b-xl shadow-sm max-w-full mx-5 mb-5">
            <div class="w-full flex justify-between items-center p-6 border-b border-gray-200">
                <div class="">
                    <h3 class="text-2xl font-bold text-primary">Edit Document Type</h3>
                    <p class="text-gray-600">Update the details below to modify this document type</p>
                </div>

                <div class="mt-4 md:mt-0">
                    <button onclick="window.location.href='<?= site_url('auth/pillars/document-types') ?>'" class="flex justify-center items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                        Back to Document Types
                    </button>
                </div>
            </div>

            <!-- Document Type Form -->
            <div class="p-6">
                <form id="editDocumentTypeForm" action="<?= site_url('auth/pillars/edit-document-type/' . $documentType['id']) ?>" method="POST" autocomplete="off">
                    <input type="hidden" name="document_type_id" value="<?= esc($documentType['id']) ?>">
                    
                    <div class="pb-6">
                        <div class="mb-5 border-l-8 !border-secondary pl-3">
                            <h4 class="text-lg font-medium text-dark">Basic Information</h4>
                            <p class="text-sm text-gray-600">Please update the necessary information for this document type.</p>
                        </div>

                        <!-- Document Type Name Field -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-dark mb-1">Document Type Name <span class="text-red-500">*</span></label>
                            <input type="text" id="documentTypeName" name="name" maxlength="255" value="<?= esc($documentType['name'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter document type name">
                        </div>

                        <!-- Slug Field -->
                        <div class="mb-6">
                            <label for="articleSlug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                            <div class="flex w-full">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                                    <?= base_url() ?>
                                </span>
                                <input type="text" id="articleSlug" name="slug" maxlength="100" value="<?= esc($documentType['slug'] ?? '') ?>" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">This will be the URL for your document type. It should be unique and descriptive.</p>
                        </div>

                        <!-- Color Field -->
                        <div class="mb-6">
                            <label for="color" class="block text-sm font-medium text-dark mb-1">Color <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-4">
                                <input type="color" id="colorPicker" name="color" value="<?= esc($documentType['color'] ?? '#6B7280') ?>" class="w-20 h-12 border border-borderColor rounded-lg cursor-pointer">
                                <input type="text" id="color" name="color" maxlength="7" value="<?= esc($documentType['color'] ?? '#6B7280') ?>" class="flex-1 px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="#RRGGBB">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">This color will be used to categorize your document types.</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="window.location.href='<?= site_url('auth/pillars/document-types') ?>'" class="bg-primaryShades-50 hover:bg-primaryShades-100 text-primary hover:text-gray-700 px-8 py-2 rounded-full flex gap-2 items-center transition-all duration-300">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                <span>Cancel</span>
                            </button>

                            <button id="submitBtn" type="submit" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                                <i data-lucide="check" class="w-4 h-4 mr-2 z-10"></i>
                                <span>Update Document Type</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        lucide.createIcons();

        // Auto-generate slug from name
        const nameInput = document.getElementById('documentTypeName');
        const slugInput = document.getElementById('articleSlug');
        const originalSlug = slugInput.value || '';
        let slugManuallyEdited = false;
        let lastAutoGeneratedSlug = '';

        // Track if slug is manually edited
        slugInput.addEventListener('input', function() {
            // If user is typing and it's different from what we auto-generated, mark as manually edited
            if (this.value !== lastAutoGeneratedSlug && lastAutoGeneratedSlug !== '') {
                slugManuallyEdited = true;
            }
        });

        nameInput.addEventListener('input', function() {
            // Only auto-generate if slug hasn't been manually edited
            if (!slugManuallyEdited) {
                const title = this.value;
                const slug = title
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-')     // Replace spaces with hyphens
                    .replace(/-+/g, '-')      // Replace multiple hyphens with single
                    .substring(0, 100);       // Limit to 100 characters
                
                // Only update if we have a valid slug
                if (slug && slug.length > 0) {
                    slugInput.value = slug;
                    lastAutoGeneratedSlug = slug;
                }
            }
        });

        // Reset manual edit flag if user clears the slug field
        slugInput.addEventListener('blur', function() {
            if (this.value === '' || this.value === originalSlug) {
                slugManuallyEdited = false;
            }
        });

        // Sync color picker with text input
        const colorPicker = document.getElementById('colorPicker');
        const colorInput = document.getElementById('color');
        
        colorPicker.addEventListener('input', function() {
            colorInput.value = this.value;
        });
        
        colorInput.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                colorPicker.value = this.value;
            }
        });

        // Form submission
        $('#editDocumentTypeForm').on('submit', function(e) {
            e.preventDefault();
            clearFormErrors();

            const $submitBtn = $('#submitBtn');
            $submitBtn.prop('disabled', true).html('<span class="flex items-center"><i data-lucide="loader" class="w-4 h-4 animate-spin mr-2"></i> Updating...</span>');
            lucide.createIcons();

            const form = this;
            const formData = new FormData(form);
            formData.append('csrf_test_name', '<?= csrf_hash() ?>');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $submitBtn.prop('disabled', false).html('<span>Update Document Type</span>');
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message || 'Document type updated successfully',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '<?= site_url('auth/pillars/document-types') ?>';
                        });
                    } else if (response.errors) {
                        showFormErrors(response.errors);
                        Swal.fire({
                            title: 'Validation Error',
                            html: '<p class="text-gray-700">Please fix the errors and try again.</p>',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'An unexpected error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false).html('<span>Update Document Type</span>');
                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    if (Object.keys(errors).length > 0) {
                        showFormErrors(errors);
                        Swal.fire({
                            title: 'Validation Error',
                            html: '<p class="text-gray-700">Please fix the errors and try again.</p>',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to update document type',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });

        function showFormErrors(errors) {
            clearFormErrors();
            
            $.each(errors, function(fieldName, message) {
                const $field = $('[name="' + fieldName + '"]');
                
                if ($field.length) {
                    $field.addClass('is-invalid border-red-500');
                    
                    const $errorDiv = $('<div>', {
                        class: 'invalid-feedback text-red-500 text-xs mt-1',
                        text: message
                    });
                    
                    $field.after($errorDiv);
                    
                    if ($('.is-invalid').first().is($field)) {
                        $field.focus();
                    }
                }
            });
        }

        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid border-red-500');
            $('.invalid-feedback').remove();
        }
    });
</script>
<?= $this->endSection() ?>

