<?php 
    // Ensure category is available
    if (!isset($category) || empty($category)) {
        throw new \RuntimeException('Category not found');
    }
    
    // Convert to array if object
    if (is_object($category)) {
        $category = (array) $category;
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
            'pageTitle' => 'Edit Resource Category',
            'pageDescription' => 'Update resource category information',
            'breadcrumbs' => [
                ['label' => 'Pillars', 'url' => base_url('auth/pillars')],
                ['label' => 'Resources', 'url' => base_url('auth/pillars/resources')],
                ['label' => 'Edit Category']
            ],
            'bannerActions' => '<a href="' . base_url('auth/pillars/resources') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Resources
            </a>'
        ]) ?>

        <!-- Pillar Article Form Section -->
        <div class="bg-white rounded-b-xl shadow-sm max-w-full pillar-form-container mx-5 mb-5">
            <div class="w-full flex justify-between items-center p-6 border-b border-gray-200">
                <div class="">
                    <h3 class="text-2xl font-bold text-primary">Edit Pillar Category Resource</h3>
                    <p class="text-gray-600">Update the details below to modify this pillar category resource</p>
                </div>

                <div class="mt-4 md:mt-0">
                    <button onclick="window.location.href='<?= site_url('auth/pillars/resources') ?>'" class="flex justify-center items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                        Back to Pillar Resource Categories
                    </button>
                </div>
            </div>

            <!-- Pillar Resource Category Form -->
            <div class="p-6">
                <form action="<?= site_url('auth/pillars/edit-resource-category/' . $category['id']) ?>" method="POST">
                    <input type="hidden" name="category_id" value="<?= esc($category['id']) ?>">
                    
                    <div class="pb-6">
                        <div class="mb-5 border-l-8 !border-secondary pl-3">
                            <h4 class="text-lg font-medium text-dark">Basic Information</h4>
                            <p class="text-sm text-gray-600">Please update the necessary information for this pillar category resource.</p>
                        </div>

                        <!-- Category Name Field -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-dark mb-1">Category Name <span class="text-red-500">*</span></label>
                            <input type="text" id="categoryName" name="name" maxlength="255" value="<?= esc($category['name'] ?? '') ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Enter category name">
                        </div>

                        <!-- Slug Field -->
                        <div class="mb-6">
                            <label for="articleSlug" class="block text-sm font-medium text-dark mb-1">URL Slug <span class="text-red-500">*</span></label>
                            <div class="flex w-full">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-borderColor bg-lightGray text-dark text-sm whitespace-nowrap">
                                    kewasnet.co.ke/resources/
                                </span>
                                <input type="text" id="articleSlug" name="slug" maxlength="100" value="<?= esc($category['slug'] ?? '') ?>" class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="url-friendly-slug">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">This will be the URL for your category. It should be unique and descriptive.</p>
                        </div>

                        <!-- Document Type -->
                        <div class="mb-6">
                            <label for="pillar_id" class="block text-sm font-medium text-dark mb-1">Pillars <span class="text-red-500">*</span></label>
                            <select id="pillar_id" name="pillar_id" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent select2">
                                <option value="">Choose a pillar...</option>
                                <?php if (!empty($pillars)): ?>
                                    <?php foreach ($pillars as $pillar): ?>
                                        <option value="<?= $pillar['id'] ?>" <?= (isset($category['pillar_id']) && $category['pillar_id'] == $pillar['id']) ? 'selected' : '' ?>>
                                            <?= esc($pillar['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-dark mb-1">Description</label>
                            <div class="relative">
                                <textarea id="description" name="description" rows="4" maxlength="1000" class="w-full px-4 py-3 pl-10 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="A brief description of this category"><?= esc($category['description'] ?? '') ?></textarea>
                                <div class="!absolute !top-3 !left-0 pl-3 flex items-start pointer-events-none">
                                    <i data-lucide="file-text" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Write a brief description that summarizes this category</span>
                                <span id="descriptionCount"><?= strlen($category['description'] ?? '') ?>/1000</span>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button id="submitBtn" type="submit" class="gradient-btn flex items-center px-8 py-2 rounded-full text-white hover:shadow-md transition-all duration-300">
                                <i data-lucide="check" class="w-4 h-4 mr-2 z-10"></i>
                                <span>Update Category</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const titleInput            = document.getElementById('categoryName');
        const slugInput             = document.getElementById('articleSlug');
        const $submitBtn            = $('#submitBtn');
        const descriptionTextarea   = document.getElementById('description');
        const descriptionCount      = document.getElementById('descriptionCount');

        // Auto-generate slug from title
        titleInput.addEventListener('input', function() {
            const title = this.value;
            const slug = title
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/-+/g, '-')      // Replace multiple hyphens with single
                .substring(0, 100);       // Limit to 100 characters
            slugInput.value = slug;
        });
        
        // Description character counter
        descriptionTextarea.addEventListener('input', function() {
            const count = this.value.length;
            descriptionCount.textContent = `${count}/1000`;
            
            if (count > 1000) {
                descriptionCount.classList.add('text-red-500');
            } else {
                descriptionCount.classList.remove('text-red-500');
            }
        });

        // Handle update resource category
        $('form').on('submit', function(e) {
            e.preventDefault();
            clearFormErrors();

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
                    $submitBtn.prop('disabled', false).html('<span>Update Category</span>');
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message || 'Category updated successfully',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '<?= base_url('auth/pillars/resources') ?>';
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
                    $submitBtn.prop('disabled', false).html('<span>Update Category</span>');
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
                            text: response.message || 'Failed to update category',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });

        // Function to show form errors
        function showFormErrors(errors) {
            clearFormErrors();
            
            $.each(errors, function(fieldName, message) {
                const $field = $('[name="' + fieldName + '"]');
                
                if ($field.length) {
                    $field.addClass('is-invalid');
                    
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

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }
    });
</script>
<?= $this->endSection() ?>

