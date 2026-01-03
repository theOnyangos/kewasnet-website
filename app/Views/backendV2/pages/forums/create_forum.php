<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
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
            'pageTitle' => 'Create New Forum',
            'pageDescription' => 'Set up a new discussion space for your community',
            'breadcrumbs' => [
                ['label' => 'Forums', 'url' => 'auth/forums'],
                ['label' => 'Create Forum']
            ],
            'bannerActions' => '<a href="' . base_url('auth/forums') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Forums
            </a>'
        ]) ?>

        <div class="px-6 pb-6">
        <!-- Create Forum Form -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form id="createForumForm" class="space-y-8" action="<?= site_url('auth/forums/create') ?>" method="POST">
                <!-- Basic Information Section -->
                <div class="space-y-6">
                    <div class="border-l-4 border-blue-500 pl-4 rounded">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="info" class="w-5 h-5 mr-2 text-gray-800"></i>
                            Basic Information
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Configure the fundamental details of your forum</p>
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
                                URL Slug *
                            </label>
                            <div class="flex w-full">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-700 text-sm whitespace-nowrap">
                                    kewasnet.co.ke/forums/
                                </span>
                                <input 
                                    type="text" 
                                    id="forumSlug" 
                                    name="slug"
                                    pattern="^[a-z0-9-]+$"
                                    class="flex-1 min-w-0 px-4 py-3 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="water-conservation"
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Auto-generated from forum name. Use lowercase letters, numbers, and hyphens only.</p>
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
                                placeholder="Describe what this forum is about, what topics will be discussed, and any guidelines..."
                            ></textarea>
                            <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                <i data-lucide="file-text" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Help users understand the forum's purpose</span>
                            <span id="descriptionCount">0/1000</span>
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
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="droplets">
                                <i data-lucide="droplets" class="w-6 h-6 text-blue-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="shield-check">
                                <i data-lucide="shield-check" class="w-6 h-6 text-green-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="gavel">
                                <i data-lucide="gavel" class="w-6 h-6 text-purple-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="cloud-rain">
                                <i data-lucide="cloud-rain" class="w-6 h-6 text-blue-400 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="users">
                                <i data-lucide="users" class="w-6 h-6 text-orange-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="cpu">
                                <i data-lucide="cpu" class="w-6 h-6 text-indigo-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="leaf">
                                <i data-lucide="leaf" class="w-6 h-6 text-green-400 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="globe">
                                <i data-lucide="globe" class="w-6 h-6 text-teal-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="zap">
                                <i data-lucide="zap" class="w-6 h-6 text-yellow-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="book">
                                <i data-lucide="book" class="w-6 h-6 text-red-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="message-square">
                                <i data-lucide="message-square" class="w-6 h-6 text-pink-500 mx-auto"></i>
                            </div>
                            <div class="icon-option cursor-pointer p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200" data-icon="settings">
                                <i data-lucide="settings" class="w-6 h-6 text-gray-500 mx-auto"></i>
                            </div>
                        </div>
                        <input type="hidden" id="selectedIcon" name="icon" value="droplets">
                        <p class="text-xs text-gray-500 mt-2">Select an icon that represents your forum's theme</p>
                    </div>

                    <!-- Color Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Theme Color
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-blue-500 border-4 border-blue-200 shadow-md" data-color="#3B82F6" title="Blue"></div>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-green-500 border-4 border-transparent shadow-md" data-color="#059669" title="Green"></div>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-purple-500 border-4 border-transparent shadow-md" data-color="#7C3AED" title="Purple"></div>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-red-500 border-4 border-transparent shadow-md" data-color="#DC2626" title="Red"></div>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-yellow-500 border-4 border-transparent shadow-md" data-color="#D97706" title="Yellow"></div>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-pink-500 border-4 border-transparent shadow-md" data-color="#DB2777" title="Pink"></div>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-indigo-500 border-4 border-transparent shadow-md" data-color="#4F46E5" title="Indigo"></div>
                            <div class="color-option cursor-pointer w-10 h-10 rounded-full bg-teal-500 border-4 border-transparent shadow-md" data-color="#0D9488" title="Teal"></div>
                        </div>
                        <input type="hidden" id="selectedColor" name="color" value="#3B82F6">
                        <p class="text-xs text-gray-500 mt-2">Choose a color that will be used for forum highlights and accents</p>
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
                                    value="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-all duration-200"
                                    placeholder="1"
                                >
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="list-ordered" class="w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first (0 = highest priority)</p>
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
                                    <input type="checkbox" id="isActive" name="is_active" value="1" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
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
                            <div id="previewIconContainer" class="p-3 rounded-full mr-4" style="background-color: rgba(59, 130, 246, 0.1);">
                                <i id="previewIcon" data-lucide="droplets" class="w-6 h-6" style="color: #3B82F6;"></i>
                            </div>
                            <div class="flex-1">
                                <h4 id="previewName" class="text-xl font-bold text-gray-800">Forum Name</h4>
                                <p id="previewDescription" class="text-gray-600 text-sm">Forum description will appear here...</p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 border-t border-gray-200 pt-4">
                            <span class="flex items-center mr-4">
                                <i data-lucide="message-square" class="w-4 h-4 mr-1"></i>
                                0 discussions
                            </span>
                            <span class="flex items-center">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                0 members
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CSRF Token for security -->
                <input id="csrfToken" type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                
                <!-- Forum ID for draft updates -->
                <input type="hidden" id="forumId" name="forum_id" value="">

                <!-- Form Errors -->
                <div id="formErrors" class="mt-4"></div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t border-gray-200">
                    <button 
                        type="button"
                        onclick="window.location.href='<?= site_url('auth/forums') ?>'"
                        class="w-full sm:w-auto px-6 py-3 border-2 border-secondary rounded-[50px] text-secondary font-medium hover:bg-gray-50 transition-all duration-200 flex items-center justify-center"
                    >
                        <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                        Cancel
                    </button>
                    
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button 
                            id="saveDraftBtn"
                            type="button" 
                            class="flex-1 sm:flex-none px-6 py-3 bg-primaryShades-100 text-primary rounded-[50px] font-medium hover:bg-primary hover:text-white transition-all duration-200 flex items-center justify-center"
                        >
                            <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                            Save Draft
                        </button>
                        
                        <button 
                            type="submit" 
                            class="gradient-btn flex-1 sm:flex-none px-8 py-3 rounded-[50px] text-white font-medium flex items-center justify-center shadow-lg"
                        >
                            <i data-lucide="plus-circle" class="w-5 h-5 mr-2 z-10"></i>
                            <span>Create Forum</span>
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
<style>
    /* Error input styling */
    .is-invalid {
        border-color: #DC2626 !important; /* red-600 */
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
    
    /* Success input styling for when errors are cleared */
    .is-valid {
        border-color: #059669 !important; /* green-600 */
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1) !important;
    }
    
    .is-valid:focus {
        border-color: #059669 !important;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2) !important;
    }
</style>
<script>
    $(document).ready(function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Color mapping for icons based on their text classes
        const iconColors = {
            'droplets': '#3B82F6',      // blue-500
            'shield-check': '#10B981',  // green-500
            'gavel': '#8B5CF6',         // purple-500
            'cloud-rain': '#60A5FA',    // blue-400
            'users': '#F97316',         // orange-500
            'cpu': '#6366F1',           // indigo-500
            'leaf': '#4ADE80',          // green-400
            'globe': '#14B8A6',         // teal-500
            'zap': '#EAB308',           // yellow-500
            'book': '#EF4444',          // red-500
            'message-square': '#EC4899', // pink-500
            'settings': '#6B7280'       // gray-500
        };

        // Helper function to generate slug
        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove all non-word characters (except spaces and hyphens)
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/--+/g, '-')     // Replace multiple hyphens with single hyphen
                .replace(/^-+|-+$/g, '')  // Remove leading/trailing hyphens
                .trim();
        }

        // Auto-generate slug from name
        $('#forumName').on('input', function() {
            // Clear error state when user starts typing
            clearFieldError($(this));
            
            // Only auto-generate slug if the slug field is empty or hasn't been manually edited
            const $slugField = $('#forumSlug');
            if (!$slugField.val() || !$slugField.data('manual')) {
                const name = $(this).val();
                const slug = generateSlug(name);
                $slugField.val(slug);
            }
            updatePreview();
        });

        // Mark slug as manually edited and clear errors
        $('#forumSlug').on('input', function() {
            clearFieldError($(this));
            $(this).data('manual', true);
        });

        // Update description character count and clear errors
        $('#forumDescription').on('input', function() {
            clearFieldError($(this));
            
            const count = $(this).val().length;
            $('#descriptionCount').text(`${count}/1000`);
            
            $('#descriptionCount').removeClass('text-orange-500 text-red-500');
            if (count > 800) {
                $('#descriptionCount').addClass('text-orange-500');
            }
            if (count > 950) {
                $('#descriptionCount').removeClass('text-orange-500').addClass('text-red-500');
            }
            updatePreview();
        });

        // Clear errors on other inputs
        $('#sortOrder').on('input', function() {
            clearFieldError($(this));
        });

        // Icon selection
        $('.icon-option').on('click', function() {
            // Remove active styling from all options
            $('.icon-option').each(function() {
                $(this).css({
                    'border-color': '#E5E7EB', // gray-200
                    'border-width': '2px',
                    'background-color': 'transparent'
                });
            });
            
            // Get the icon name and its color
            const iconName = $(this).data('icon');
            const iconColor = iconColors[iconName] || '#6B7280';
            
            // Add active styling to selected option with icon's color
            $(this).css({
                'border-color': iconColor,
                'border-width': '2px',
                'background-color': iconColor + '20' // 20% opacity
            });
            
            // Update hidden input
            $('#selectedIcon').val(iconName);
            
            // Update preview
            $('#previewIcon').attr('data-lucide', iconName);
            lucide.createIcons();
        });

        // Color selection
        $('.color-option').on('click', function() {
            // Remove active class from all options
            $('.color-option').removeClass('border-4').addClass('border-4 border-transparent');
            
            // Add active class to selected option
            $(this).removeClass('border-transparent').addClass('border-gray-300');
            
            // Update hidden input
            const color = $(this).data('color');
            $('#selectedColor').val(color);
            
            // Update preview
            $('#previewIcon').css('color', color);
            $('#previewIconContainer').css('background-color', color + '20');
        });

        // Update preview function
        function updatePreview() {
            const name = $('#forumName').val() || 'Forum Name';
            const description = $('#forumDescription').val() || 'Forum description will appear here...';
            
            $('#previewName').text(name);
            $('#previewDescription').text(description);
        }

        // Save Draft functionality
        $('#saveDraftBtn').on('click', function(e) {
            e.preventDefault();
            saveForum(true); // Save as draft
        });

        // Form submission for creating forum
        $('#createForumForm').on('submit', function(e) {
            e.preventDefault();
            saveForum(false); // Create forum (not draft)
        });

        // Common function to handle both draft and create
        function saveForum(isDraft = false) {
            const form = $('#createForumForm');
            const formData = new FormData(form[0]);
            
            // Basic validation for create action (drafts can be incomplete)
            if (!isDraft) {
                const forumName = $('#forumName').val().trim();
                if (!forumName) {
                    showToast('Forum name is required to create a forum', 'error');
                    $('#forumName').focus();
                    return;
                }
            }
            
            // Ensure is_active is properly set as 1 or 0
            const isActive = $('#isActive').is(':checked') ? '1' : '0';
            formData.set('is_active', isActive);
            
            // Add draft status to form data
            formData.append('is_draft', isDraft ? '1' : '0');
            
            // Check if we're updating an existing draft
            const forumId = $('#forumId').val();
            if (forumId && isDraft) {
                formData.append('update_draft', '1');
            }
            
            // Determine the URL based on action type
            let url = form.attr('action');
            
            // Show loading state
            const submitBtn = isDraft ? $('#saveDraftBtn') : $('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${isDraft ? 'Saving Draft...' : 'Creating Forum...'}
            `);
            
            // Clear previous errors
            clearFormErrors();
            
            // AJAX request
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        if (isDraft) {
                            // For draft, update the forum ID and show success
                            if (response.forum_id) {
                                $('#forumId').val(response.forum_id);
                            }
                            showToast('Draft saved successfully!', 'success');
                            
                            // Update the Save Draft button text to indicate it's saved
                            $('#saveDraftBtn').find('span').text('Draft Saved');
                            setTimeout(function() {
                                $('#saveDraftBtn').find('span').text('Save Draft');
                            }, 2000);
                        } else {
                            // For create action, show success and redirect
                            showToast(response.message || 'Forum created successfully!', 'success');
                            setTimeout(function() {
                                window.location.href = response.redirect_url || '<?= site_url("auth/forums") ?>';
                            }, 1500);
                        }
                    } else {
                        showToast(response.message || 'An error occurred', 'error');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    
                    if (Object.keys(errors).length > 0 || response.error_type === 'validation') {
                         // Clear previous errors
                        $('.error-message').remove();
                        
                        // Display errors for each field
                        Object.entries(response.errors).forEach(([fieldName, errorMsg]) => {
                            const $field = $(`[name="${fieldName}"]`);
                            $field.addClass('is-invalid');
                            $field.after(`<div class="invalid-feedback">${errorMsg}</div>`);
                        });
                        showToast(response.message, 'error');
                    } else {
                        showToast(response.message || 'An error occurred while saving', 'error');
                    }
                },
                complete: function() {
                    // Restore button state
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        }

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback, .error-message').remove();
            $('#formErrors').empty();
        }

        // Function to clear error for a specific field
        function clearFieldError($field) {
            $field.removeClass('is-invalid');
            $field.next('.invalid-feedback').remove();
            $field.closest('div').find('.invalid-feedback').remove();
        }

        // Function to show toast notifications
        function showToast(message, type = 'success') {
            // Remove existing toasts
            $('.custom-toast').remove();
            
            // Create toast element
            var toast = $(`
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
            
            // Append to body
            $('body').append(toast);
            lucide.createIcons();
            
            // Animate in
            toast.hide().css({opacity: 0, x: 100});
            toast.show().animate({opacity: 1, x: 0}, 300);
            
            // Auto-remove after delay
            setTimeout(function() {
                toast.animate({opacity: 0, y: -20}, 300, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        // Function to show form errors with proper styling
        function showFormErrors(errors) {
            // Clear all previous errors first
            clearFormErrors();
            
            // Show new errors
            $.each(errors, function(fieldName, message) {
                const $field = $('[name="' + fieldName + '"]');
                
                if ($field.length) {
                    // Add error class to input
                    $field.addClass('is-invalid');
                    
                    // Create error message element
                    const $errorDiv = $('<div>', {
                        class: 'invalid-feedback',
                        text: message
                    });
                    
                    // Insert error message after the input
                    if ($field.next('.invalid-feedback').length === 0) {
                        $field.after($errorDiv);
                    }
                    
                    // Animate error appearance
                    $errorDiv.hide().fadeIn(300);
                    
                    // Focus the first error field
                    if ($('.is-invalid').first().is($field)) {
                        $field.focus();
                    }
                }
            });
        }
        
        // Update CSRF token if needed
        function updateCsrfToken(token) {
            $('input[name="<?= csrf_token() ?>"]').val(token);
        }

        // Initialize first icon as selected
        $('.icon-option').first().trigger('click');
        
        // Initialize preview
        updatePreview();
    });
</script>
<?= $this->endSection() ?>