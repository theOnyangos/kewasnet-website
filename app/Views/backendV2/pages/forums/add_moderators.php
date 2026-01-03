<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Forum Moderators',
            'pageDescription' => 'Manage users who can moderate this forum',
            'breadcrumbs' => [
                ['label' => 'Forums', 'url' => base_url('auth/forums')],
                ['label' => 'Forum Details', 'url' => base_url('auth/forums/' . $forumId)],
                ['label' => 'Add Moderators']
            ],
            'bannerActions' => '<a href="' . base_url('auth/forums/' . $forumId) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Forum
            </a>'
        ]) ?>

        <div class="px-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8 mt-6">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <i data-lucide="shield-check" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Forum Moderators</h1>
                    <p class="text-gray-600">Water Conservation Forum</p>
                </div>
            </div>
        </div>

        <!-- Add New Moderators Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center mb-2">
                    <i data-lucide="user-plus" class="w-5 h-5 mr-2"></i>
                    Add New Moderators
                </h2>
                <p class="text-gray-600">Search and select users to add as forum moderators</p>
            </div>
            
            <form id="addModeratorForm" class="space-y-6">
                <?= csrf_field() ?>
                
                <!-- User Search -->
                <div>
                    <label for="userSearch" class="block text-sm font-medium text-gray-700 mb-2">
                        Search Users <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="userSearch" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Search by name or email..."
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="error-message text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Search Results -->
                <div id="searchResults" class="space-y-2 max-h-[300px] overflow-y-auto border border-gray-200 rounded-lg p-3">
                    <div class="p-4 text-center text-gray-500">Search for users to add as moderators</div>
                </div>

                <!-- Selected Users -->
                <div id="selectedUsersContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Selected Users (<span id="selectedCount">0</span>)
                    </label>
                    <div id="selectedUsersList" class="space-y-2 max-h-[300px] overflow-y-auto">
                        <!-- Selected users will be dynamically added here -->
                    </div>
                </div>

                <!-- Hidden field -->
                <input type="hidden" name="forum_id" value="f47ac10b-58cc-4372-a567-0e02b2c3d479">

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" id="clearAllBtn" class="bg-gray-100 px-6 py-2 rounded-lg text-gray-700 hover:bg-gray-200 transition-colors">
                        Clear All
                    </button>
                    <button type="submit" class="gradient-btn px-6 py-2 rounded-lg text-white font-medium">
                        <span>Add Moderators</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Moderators -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="border-b border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i data-lucide="users" class="w-5 h-5 mr-2"></i>
                    Current Moderators (<span id="moderator-count">0</span>)
                </h2>
                <p class="text-gray-600 mt-1">Manage users who can moderate this forum</p>
            </div>
            
            <div class="p-6">
                <div id="moderators-container" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Dynamic moderator cards will be loaded here -->
                </div>
            </div>
        </div>
        </div>    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
<script>
    $(document).ready(function() {        
        const getSystemUsersURL = '<?= site_url('auth/forums/system-users/get') ?>';
        const csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
        
        // Variables for AJAX request management
        let currentRequest = null;
        let fetchingUsers = false;
        
        // Array to store selected users
        let selectedUsers = [];
        
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Load moderators on page load
        fetchModerators();
        
        // Load users on page load
        fetchUsers();

        // Clear all selections
        $('#clearAllBtn').on('click', function() {
            selectedUsers = [];
            updateSelectedUsersDisplay();
            $('#userSearch').val('');
            fetchUsers('');
            clearAllErrors();
        });
        
        function fetchUsers(searchTerm = '') {
            const csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
            
            currentRequest = $.ajax({
                url: getSystemUsersURL,
                type: 'POST',
                data: { 
                    search: searchTerm,
                    '<?= csrf_token() ?>': csrfToken
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#searchResults').html('<div class="p-4 text-center text-gray-500">Loading users...</div>');
                },
                success: function(response) {                    
                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(user => {                            
                            const userImage = user.image || user.picture || '/assets/new/profile-avatar.jpg';
                            const userName = user.name || `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Unknown User';
                            const userRole = user.role || user.job_title || 'User';
                            
                            html += `
                                <div class="user-result flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" 
                                     data-user-id="${user.id}" 
                                     data-user-name="${userName}" 
                                     data-user-email="${user.email}"
                                     data-user-image="${userImage}">
                                    <div class="flex items-start">
                                        <img src="${userImage}" alt="${userName}" class="w-12 h-12 rounded-full mr-3" onerror="this.src='/assets/new/profile-avatar.jpg'">
                                        <div>
                                            <h4 class="font-medium text-gray-900">${userName}</h4>
                                            <p class="text-sm text-gray-600">${user.email}</p>
                                            <p class="text-xs text-gray-500">${userRole}</p>
                                        </div>
                                    </div>
                                    <button type="button" class="select-user-btn px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full hover:bg-blue-200 transition-colors">
                                        Select
                                    </button>
                                </div>
                            `;
                        });
                        $('#searchResults').html(html);
                        
                        // Rebind select user event
                        $('.select-user-btn').off('click').on('click', function(e) {
                            e.preventDefault();
                            const $userResult = $(this).closest('.user-result');
                            const userId = $userResult.data('user-id');
                            const userName = $userResult.data('user-name');
                            const userEmail = $userResult.data('user-email');
                            const userImage = $userResult.data('user-image');

                            // Check if user is already selected
                            const existingIndex = selectedUsers.findIndex(u => u.id === userId);
                            if (existingIndex === -1) {
                                // Add user to selected array
                                selectedUsers.push({
                                    id: userId,
                                    name: userName,
                                    email: userEmail,
                                    image: userImage
                                });
                                
                                // Update display
                                updateSelectedUsersDisplay();
                                
                                // Clear any user selection errors
                                clearUserSelectionError();
                            } else {
                                showToast('User already selected', 'error');
                            }
                        });
                    } else if (response.status === 'error') {
                        $('#searchResults').html(`<div class="p-4 text-center text-red-500">${response.message || 'Error loading users'}</div>`);
                    } else {
                        // Handle case where response.data is empty or undefined
                        const message = response.data && response.data.length === 0 ? 'No users found' : 'No users available';
                        $('#searchResults').html(`<div class="p-4 text-center text-gray-500">${message}</div>`);
                    }
                    fetchingUsers = false; // Reset flag
                    currentRequest = null; // Clear current request
                },
                error: function(xhr, status, error) {
                    // Don't show error if the request was aborted
                    if (status !== 'abort') {
                        let errorMessage = 'Error loading users';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error('Failed to parse error response:', e);
                        }
                        
                        $('#searchResults').html(`<div class="p-4 text-center text-red-500">${errorMessage}</div>`);
                    }
                    fetchingUsers = false; // Reset flag
                    currentRequest = null; // Clear current request
                }
            });
        }

        // Function to update selected users display
        function updateSelectedUsersDisplay() {
            if (selectedUsers.length === 0) {
                $('#selectedUsersContainer').addClass('hidden');
                $('#selectedCount').text('0');
                return;
            }
            
            $('#selectedUsersContainer').removeClass('hidden');
            $('#selectedCount').text(selectedUsers.length);
            
            let html = '';
            selectedUsers.forEach(user => {
                html += `
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg" data-user-id="${user.id}">
                        <div class="flex items-center mb-3">
                            <img src="${user.image}" alt="${user.name}" class="w-10 h-10 rounded-full mr-3" onerror="this.src='/assets/new/profile-avatar.jpg'">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">${user.name}</h4>
                                <p class="text-sm text-gray-600">${user.email}</p>
                            </div>
                            <button type="button" class="remove-user-btn text-gray-500 hover:text-red-600" data-user-id="${user.id}">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div>
                            <input type="text" 
                                   class="moderator-title-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Moderator title (e.g., Water Resource Specialist)"
                                   data-user-id="${user.id}"
                                   value="${user.title || ''}">
                        </div>
                    </div>
                `;
            });
            
            $('#selectedUsersList').html(html);
            
            // Initialize lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Bind remove buttons
            $('.remove-user-btn').on('click', function() {
                const userId = $(this).data('user-id');
                removeUser(userId);
            });
            
            // Bind title input changes
            $('.moderator-title-input').on('input', function() {
                const userId = $(this).data('user-id');
                const title = $(this).val();
                const userIndex = selectedUsers.findIndex(u => u.id === userId);
                if (userIndex !== -1) {
                    selectedUsers[userIndex].title = title;
                }
            });
        }

        // Function to remove a user from selection
        function removeUser(userId) {
            selectedUsers = selectedUsers.filter(u => u.id !== userId);
            updateSelectedUsersDisplay();
        }

        // Clear selection
        function clearSelection() {
            selectedUsers = [];
            updateSelectedUsersDisplay();
            $('#userSearch').val('');
            fetchUsers('');
        }

        // Search functionality with debounce
        let searchTimeout;
        $('#userSearch').on('input', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();
            
            searchTimeout = setTimeout(function() {
                fetchUsers(query);
            }, 500);
        });

        // Form submission
        $('#addModeratorForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearAllErrors();
            
            // Validate required fields
            let hasErrors = false;
            
            // Validate user selection
            if (selectedUsers.length === 0) {
                showFieldError('userSearch', 'Please select at least one user to add as moderator');
                hasErrors = true;
            }
            
            // If validation fails, stop submission
            if (hasErrors) {
                showToast('Please select at least one user', 'error');
                return;
            }
            
            // Prepare data for multiple users
            const moderators = selectedUsers.map(user => ({
                user_id: user.id,
                forum_id: '<?= $forumId ?>',
                moderator_title: user.title || 'Moderator'
            }));
            
            const formData = {
                moderators: moderators,
                '<?= csrf_token() ?>': csrfToken
            };

            // Submit via AJAX
            $.ajax({
                url: '<?= site_url('auth/forums/moderators/add') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    // Show loading state
                    const $submitBtn = $('#addModeratorForm button[type="submit"]');
                    $submitBtn.prop('disabled', true).html(`
                        <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                        Adding...
                    `);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Show success message
                        showToast(response.message || 'Moderators added successfully!', 'success');
                        
                        // Reset form
                        $('#addModeratorForm')[0].reset();
                        selectedUsers = [];
                        updateSelectedUsersDisplay();
                        $('#userSearch').val('');
                        clearAllErrors();
                        
                        // Fetch forum moderators
                        fetchModerators();
                        
                        // Reload user list
                        fetchUsers('');
                    } else {
                        showToast(response.message || 'Failed to add moderators', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error response:', xhr);
                    
                    let errorMsg = 'An error occurred while adding moderator';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        // Handle validation errors
                        if (response.errors && typeof response.errors === 'object') {
                            Object.keys(response.errors).forEach(fieldName => {
                                showFieldError(fieldName, response.errors[fieldName]);
                            });
                            showToast('Please fix the errors and try again', 'error');
                            return;
                        }
                        
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        console.error('Failed to parse error response:', e);
                    }
                    
                    showToast('Error: ' + errorMsg, 'error');
                },
                complete: function() {
                    // Reset button state
                    const $submitBtn = $('#addModeratorForm button[type="submit"]');
                    $submitBtn.prop('disabled', false).html('<span>Add Moderators</span>');
                }
            });
        });
        
        // Error handling functions
        function showFieldError(fieldName, message) {
            const $field = $('#' + fieldName);
            const $errorDiv = $field.siblings('.error-message');
            
            // Add error styling to field
            $field.removeClass('border-gray-300 focus:ring-blue-500').addClass('border-red-500 focus:ring-red-500');
            
            // Show error message
            if ($errorDiv.length) {
                $errorDiv.text(message).removeClass('hidden');
            } else {
                $field.after(`<div class="error-message text-red-500 text-sm mt-1">${message}</div>`);
            }
        }
        
        function clearFieldError(fieldName) {
            const $field = $('#' + fieldName);
            const $errorDiv = $field.siblings('.error-message');
            
            // Remove error styling
            $field.removeClass('border-red-500 focus:ring-red-500').addClass('border-gray-300 focus:ring-blue-500');
            
            // Hide error message
            $errorDiv.addClass('hidden').text('');
        }
        
        function clearAllErrors() {
            $('.error-message').addClass('hidden').text('');
            $('input').removeClass('border-red-500 focus:ring-red-500').addClass('border-gray-300 focus:ring-blue-500');
        }
        
        // Clear errors on input
        $('#userSearch').on('input', function() {
            if (selectedUsers.length > 0) {
                clearFieldError('userSearch');
            }
        });
        
        // Clear user selection error when user is selected
        function clearUserSelectionError() {
            if (selectedUsers.length > 0) {
                clearFieldError('userSearch');
            }
        }
        
        // Toast notification function
        function showToast(message, type = 'success') {
            // Remove existing toasts
            $('.toast-notification').remove();
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? 'check-circle' : 'alert-circle';
            
            const toast = $(`
                <div class="toast-notification fixed top-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg z-50 flex items-center">
                    <i data-lucide="${icon}" class="w-5 h-5 mr-2"></i>
                    <span>${message}</span>
                </div>
            `);
            
            $('body').append(toast);
            
            // Initialize lucide icons for the toast
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Function to fetch and display forum moderators
        function fetchModerators() {            
            $.ajax({
                url: '<?= site_url('auth/forums/' . $forumId . '/moderators/get') ?>',
                type: 'GET',
                data: {
                    '<?= csrf_token() ?>': csrfToken
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#moderators-container').html('<div class="col-span-full flex justify-center items-center py-8"><div class="text-gray-500">Loading moderators...</div></div>');
                },
                success: function(response) {
                    console.log('Moderators response:', response);
                    
                    if (response.status === 'success' && response.data) {
                        const moderators = response.data;
                        updateModeratorsDisplay(moderators);
                    } else {
                        $('#moderators-container').html('<div class="col-span-full text-center py-8 text-gray-500">No moderators found</div>');
                        $('#moderator-count').text('0');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching moderators:', error);
                    $('#moderators-container').html('<div class="col-span-full text-center py-8 text-red-500">Error loading moderators</div>');
                }
            });
        }

        // Function to update moderators display
        function updateModeratorsDisplay(moderators) {
            let html = '';
            
            if (moderators.length === 0) {
                html = '<div class="col-span-full text-center py-8 text-gray-500">No moderators assigned to this forum</div>';
                $('#moderator-count').text('0');
            } else {
                moderators.forEach(moderator => {
                    const statusClass       = moderator.is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                    const statusText        = moderator.is_active ? 'Active' : 'Inactive';
                    const userImage         = moderator.user_image || moderator.image || '/assets/new/profile-avatar.jpg';
                    const userName          = moderator.user_name || moderator.name || 'Unknown User';
                    const moderatorTitle    = moderator.moderator_title || moderator.role || 'Moderator';
                    const assignedDate      = new Date(moderator.assigned_at || moderator.created_at).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                    const assignedBy = moderator.assigned_by_name || 'System';

                    html += `
                        <div class="moderator-card bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <img src="${userImage}" alt="${userName}" class="w-12 h-12 rounded-full mr-4" onerror="this.src='/assets/new/profile-avatar.jpg'">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">${userName}</h3>
                                        <p class="text-sm text-gray-600">${moderatorTitle}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 ${statusClass} text-xs rounded-full">${statusText}</span>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <span>Assigned: ${assignedDate}</span>
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="user-check" class="w-4 h-4 mr-2"></i>
                                    <span>By: ${assignedBy}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <button class="p-2 text-gray-500 hover:text-blue-600 transition-colors" title="Send Message">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </button>
                                ${!moderator.is_active ? `
                                <button class="p-2 text-gray-500 hover:text-green-600 transition-colors" onclick="activateModerator('${moderator.user_id}')" title="Activate Moderator">
                                    <i data-lucide="user-check" class="w-4 h-4"></i>
                                </button>
                                ` : ''}
                                <button class="p-2 text-gray-500 hover:text-red-600 transition-colors" onclick="removeModerator('${moderator.user_id}')" title="Remove Moderator">
                                    <i data-lucide="user-minus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                $('#moderator-count').text(moderators.length);
            }
            
            $('#moderators-container').html(html);
            
            // Initialize Lucide icons for the new content
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
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

        // Function to clear field error
        function clearFieldError(field) {
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();
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

        // Function to clear form errors
        function clearFormErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }
    });

    // Moderator management functions
    function removeModerator(userId) {
        Swal.fire({
            title: 'Remove Moderator?',
            html: `
                <p class="mb-4">Are you sure you want to remove this moderator?</p>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for removal (optional)</label>
                    <textarea id="removal-notes" class="swal2-input w-full p-2 border border-gray-300 rounded-md" 
                              placeholder="Enter reason for removing this moderator..." 
                              rows="3"></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    notes: document.getElementById('removal-notes').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const notes = result.value.notes || '';
                const csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
                
                $.ajax({
                    url: '<?= site_url('auth/forums/moderators/remove') ?>',
                    type: 'POST',
                    data: { 
                        user_id: userId, 
                        forum_id: "f47ac10b-58cc-4372-a567-0e02b2c3d479",
                        notes: notes,
                        '<?= csrf_token() ?>': csrfToken
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: 'Moderator removed successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            fetchModerators(); // Refresh moderators list
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Failed to remove moderator'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while removing moderator';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMsg = response.message || errorMsg;
                        } catch (e) {
                            console.error('Failed to parse error response:', e);
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    }

    function activateModerator(userId) {
        const csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
        
        $.ajax({
            url: '<?= site_url('auth/forums/moderators/activate') ?>',
            type: 'POST',
            data: { 
                user_id: userId, 
                forum_id: "f47ac10b-58cc-4372-a567-0e02b2c3d479",
                '<?= csrf_token() ?>': csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showToast('Moderator activated successfully!', 'success');
                    fetchModerators(); // Refresh moderators list
                } else {
                    showToast('Error: ' + (response.message || 'Failed to activate moderator'), 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while activating moderator';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    console.error('Failed to parse error response:', e);
                }
                showToast('Error: ' + errorMsg, 'error');
            }
        });
    }
</script>
<?= $this->endSection() ?>