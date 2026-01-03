<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!-- Section Title Block -->
<?= $this->section('title'); ?>
Edit Forum - <?= esc($forum->name) ?>
<?= $this->endSection(); ?>

<!-- Section Content Block -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Edit Forum',
            'pageDescription' => 'Update the details and settings of ' . esc($forum->name),
            'breadcrumbs' => [
                ['label' => 'Forums', 'url' => base_url('auth/forums')],
                ['label' => esc($forum->name), 'url' => base_url('auth/forums/' . $forum->id)],
                ['label' => 'Edit']
            ],
            'bannerActions' => '<a href="' . base_url('auth/forums/' . $forum->id) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Forum
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Title and Description -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <!-- Edit Forum Form -->
            <form id="editForumForm" class="p-8 space-y-8" action="<?= site_url('auth/forums/' . $forum->id . '/update') ?>">
                <!-- Basic Information Section -->
                <div class="space-y-6">
                    <div class="border-l-4 border-blue-500 pl-4 rounded">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="info" class="w-5 h-5 mr-2 text-gray-800"></i>
                            Basic Information
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Update the fundamental details of your forum</p>
                    </div>

                    <!-- Forum Name -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="forumName" class="block text-sm font-medium text-gray-700 mb-2">
                                Forum Name *
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="forumName" 
                                    name="name"
                                    maxlength="255"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="e.g., Water Conservation"
                                    value="<?= esc($forum->name) ?>"
                                    required
                                >
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="type" class="w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Maximum 255 characters</p>
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="forumSlug" class="block text-sm font-medium text-gray-700 mb-2">
                                URL Slug
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="forumSlug" 
                                    name="slug"
                                    pattern="^[a-z0-9-]+$"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="water-conservation"
                                    value="<?= esc($forum->slug) ?>"
                                >
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="link" class="w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Use lowercase letters, numbers, and hyphens only</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="forumDescription" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <div class="relative">
                            <textarea 
                                id="forumDescription" 
                                name="description"
                                rows="4"
                                maxlength="1000"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                placeholder="Describe what this forum is about..."
                            ><?= esc($forum->description) ?></textarea>
                            <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                <i data-lucide="file-text" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Help users understand the forum's purpose</span>
                            <span id="descriptionCount"><?= strlen($forum->description) ?>/1000</span>
                        </div>
                    </div>
                </div>

                <!-- Appearance Section -->
                <div class="space-y-6 border-t border-gray-200 pt-8">
                    <div class="border-l-4 border-purple-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="palette" class="w-5 h-5 mr-2"></i>
                            Appearance & Branding
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Customize the visual identity of your forum</p>
                    </div>

                    <!-- Icon Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Forum Icon
                        </label>
                        <div class="grid grid-cols-8 md:grid-cols-12 gap-3">
                            <?php 
                            $icons = ['droplets', 'shield-check', 'gavel', 'cloud-rain', 'users', 'cpu', 'leaf', 'globe', 'zap', 'book', 'message-square', 'settings'];
                            foreach ($icons as $icon): ?>
                            <div class="icon-option cursor-pointer p-3 border-2 <?= $forum->icon == $icon ? 'border-blue-500 bg-blue-100/20' : 'border-gray-200' ?> rounded-lg hover:border-blue-500 transition-all duration-200" 
                                 data-icon="<?= $icon ?>">
                                <i data-lucide="<?= $icon ?>" class="w-6 h-6 mx-auto" style="color: <?= $forum->icon == $icon ? $forum->color : '#6B7280' ?>"></i>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selectedIcon" name="icon" value="<?= $forum->icon ?>">
                    </div>

                    <!-- Color Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Theme Color
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <?php 
                            $colors = [
                                '#3B82F6' => 'Blue',
                                '#059669' => 'Green',
                                '#7C3AED' => 'Purple',
                                '#DC2626' => 'Red',
                                '#D97706' => 'Yellow',
                                '#DB2777' => 'Pink',
                                '#4F46E5' => 'Indigo',
                                '#0D9488' => 'Teal'
                            ];
                            foreach ($colors as $hex => $name): ?>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full border-4 <?= $forum->color == $hex ? 'border-gray-300' : 'border-transparent' ?>" 
                                 style="background-color: <?= $hex ?>" 
                                 data-color="<?= $hex ?>" 
                                 title="<?= $name ?>"></div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selectedColor" name="color" value="<?= $forum->color ?>">
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="space-y-6 border-t border-gray-200 pt-8">
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="cog" class="w-5 h-5 mr-2"></i>
                            Forum Settings
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Configure forum behavior and display preferences</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Sort Order -->
                        <div>
                            <label for="sortOrder" class="block text-sm font-medium text-gray-700 mb-2">
                                Sort Order
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    id="sortOrder" 
                                    name="sort_order"
                                    min="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    value="<?= $forum->sort_order ?>"
                                >
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="list-ordered" class="w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                        </div>

                        <!-- Status Toggle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Forum Status
                            </label>
                            <div class="flex items-center">
                                <!-- Hidden input to ensure value is always sent -->
                                <input type="hidden" name="is_active" value="0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="isActive" name="is_active" value="1" class="sr-only peer" <?= $forum->is_active ? 'checked' : '' ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-700"><?= $forum->is_active ? 'Active' : 'Inactive' ?></span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Only active forums are visible to users</p>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="space-y-6 border-t border-gray-200 pt-8">
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="eye" class="w-5 h-5 mr-2"></i>
                            Forum Preview
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">See how your forum will appear to users</p>
                    </div>

                    <div id="forumPreview" class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div id="previewIconContainer" class="p-3 rounded-full mr-4" style="background-color: <?= hex2rgba($forum->color, 0.1) ?>">
                                <i id="previewIcon" data-lucide="<?= $forum->icon ?>" class="w-6 h-6" style="color: <?= $forum->color ?>"></i>
                            </div>
                            <div class="flex-1">
                                <h4 id="previewName" class="text-xl font-bold text-gray-800"><?= esc($forum->name) ?></h4>
                                <p id="previewDescription" class="text-gray-600 text-sm"><?= esc($forum->description) ?: 'Forum description will appear here...' ?></p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 border-t border-gray-200 pt-4">
                            <span class="flex items-center mr-4">
                                <i data-lucide="message-square" class="w-4 h-4 mr-1"></i>
                                <?= $forum->total_discussions ?> discussions
                            </span>
                            <span class="flex items-center">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                1 moderators
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CSRF Token for security -->
                <input id="csrfToken" type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t border-gray-200">
                    <button type="button" onclick="window.location.href='<?= site_url('auth/forums') ?>'" class="w-full bg-primaryShades-50 text-primary sm:w-auto px-6 py-3 rounded-[50px] font-medium hover:bg-primaryShades-100 transition-all duration-200 flex items-center justify-center">
                        <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                        Cancel
                    </button>
                    
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button type="submit" class="gradient-btn w-full sm:w-auto px-8 py-3 text-white rounded-[50px] font-medium hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-lg">
                            <i data-lucide="save" class="w-5 h-5 mr-2 z-10"></i>
                            <span class="hidden"></span>  <span>Update Forum</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
<?= $this->endSection() ?>

<!-- Section Scripts Block -->
<?= $this->section('scripts') ?>
<style>
    /* Error input styling */
    .is-invalid {
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
    }
    
    .is-invalid:focus {
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
    }
    
    .invalid-feedback {
        display: block;
        color: #DC2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        line-height: 1.25;
    }
</style>
<script>
    $(document).ready(function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Auto-generate slug from name
        $('#forumName').on('input', function() {
            if (!$('#forumSlug').data('manual')) {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                $('#forumSlug').val(slug);
            }
            updatePreview();
        });

        // Mark slug as manually edited
        $('#forumSlug').on('input', function() {
            $(this).data('manual', true);
        });

        // Update description character count
        $('#forumDescription').on('input', function() {
            const count = $(this).val().length;
            $('#descriptionCount').text(`${count}/1000`);
            updatePreview();
        });

        // Icon selection
        $('.icon-option').on('click', function() {
            $('.icon-option').removeClass('border-blue-500 bg-blue-100/20').addClass('border-gray-200');
            $(this).removeClass('border-gray-200').addClass('border-blue-500 bg-blue-100/20');
            
            const iconName = $(this).data('icon');
            $('#selectedIcon').val(iconName);
            $('#previewIcon').attr('data-lucide', iconName);
            lucide.createIcons();
        });

        // Color selection
        $('.color-option').on('click', function() {
            $('.color-option').removeClass('border-gray-300').addClass('border-transparent');
            $(this).removeClass('border-transparent').addClass('border-gray-300');
            
            const color = $(this).data('color');
            $('#selectedColor').val(color);
            $('#previewIcon').css('color', color);
            $('#previewIconContainer').css('background-color', hexToRgba(color, 0.1));
        });

        // Update preview function
        function updatePreview() {
            $('#previewName').text($('#forumName').val() || 'Forum Name');
            $('#previewDescription').text($('#forumDescription').val() || 'Forum description will appear here...');
        }

        // Form submission
        $('#editForumForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Ensure is_active is properly set as 1 or 0
            const isActive = $('#isActive').is(':checked') ? '1' : '0';
            formData.set('is_active', isActive);
            
            // Add HTTP method override for PATCH
            formData.append('_method', 'PATCH');

            console.log('Submitting forum update with data:');
            // Debug: Log all form data
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            // Show loading state
            submitBtn.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
            `);
            
            // Clear previous errors
            clearFormErrors();
            
            // AJAX request
            $.ajax({
                url: form.attr('action'),
                method: 'POST', // Use POST with _method override
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.status === 'success') {
                        showToast('Forum updated successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = response.redirect_url || '<?= site_url("auth/forums") ?>';
                        }, 1500);
                    } else {
                        showToast(response.message || 'An error occurred', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error response:', xhr);
                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    
                    if (Object.keys(errors).length > 0) {
                        showFormErrors(errors);
                        showToast('Please fix the errors and try again', 'error');
                    } else {
                        showToast(response.message || 'Failed to update forum', 'error');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

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

        // Helper function to show toast notifications
        function showToast(message, type = 'success') {
            // Remove existing toasts
            $('.custom-toast').remove();
            
            // Create toast element
            const toast = $(`
                <div class="custom-toast fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg text-white z-50 ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }">
                    <div class="flex items-center">
                        <i data-lucide="${
                            type === 'success' ? 'check-circle' : 'alert-circle'
                        }" class="w-5 h-5 mr-2"></i>
                        ${message}
                    </div>
                </div>
            `);
            
            // Append to body and show
            $('body').append(toast);
            lucide.createIcons();
            toast.hide().fadeIn(300);
            
            // Auto-remove after delay
            setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
        }

        // Helper function to convert hex to rgba
        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        // Initialize preview
        updatePreview();
    });
</script>
        </div>
        </div>
    </main>
<?= $this->endSection() ?>