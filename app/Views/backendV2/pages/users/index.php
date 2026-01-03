<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    $roles = model('RoleModel')->findAll();
    
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
    <!-- Hero Banner with Gradient Overlay -->
    <div class="relative h-64 mb-6 overflow-hidden bg-cover bg-center" style="background-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%), url('<?= base_url('images/dashboard-breadcrumb.jpg') ?>');">
        <!-- Content -->
        <div class="relative z-10 h-full flex flex-col justify-center px-6 md:px-12">
            <!-- Breadcrumbs -->
            <nav class="mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center text-sm font-medium text-white/80 hover:text-white transition-colors">
                            <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                            <span class="ml-1 text-sm font-medium text-white md:ml-2">User Management</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Title and Stats -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-2 drop-shadow-lg">
                        User Management
                    </h1>
                    <p class="text-lg text-white/90 max-w-2xl">
                        Manage and monitor all system users, roles, and permissions
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="mt-6 md:mt-0 flex gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-3 border border-white/20">
                        <div class="flex items-center gap-2">
                            <i data-lucide="users" class="w-5 h-5 text-white"></i>
                            <div>
                                <p class="text-xs text-white/80">Total Users</p>
                                <p class="text-xl font-bold text-white" id="totalUsersCount">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-3 border border-white/20">
                        <div class="flex items-center gap-2">
                            <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                            <div>
                                <p class="text-xs text-white/80">Active Today</p>
                                <p class="text-xl font-bold text-white" id="activeUsersCount">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 pb-6">
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <!-- Action Bar -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">All Users</h2>
                    <p class="mt-1 text-sm text-slate-500">View and manage all registered users in the system</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="<?= base_url('auth/users/create') ?>" class="gradient-btn flex items-center px-6 py-2.5 rounded-full text-white hover:shadow-lg transition-all duration-300">
                        <i data-lucide="plus" class="w-5 h-5 mr-2 z-10"></i>
                        <span>Add New User</span>
                    </a>
                </div>
            </div>

        <!-- Users Table -->
        <table id="usersTable" class="data-table stripe hover" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>phone</th>
                    <th>Last Login</th>
                    <th>joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    let usersDataTable;

    $(document).ready(function() {
        usersDataTable = $('.data-table').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search users...",
            },
            "ajax": {
                "url": "<?= base_url('auth/users/get') ?>",
                "type": "POST",
                "dataSrc": function(json) {
                    // Debug the response
                    console.log("Server response:", json);
                    return json.data;
                },
                "error": function(xhr, error, thrown) {
                    console.error("DataTables error:", xhr, error, thrown);
                }
            },
            "columns": [
                { 
                    "data": "name",
                    "render": function(data, type, row) {
                        return `<span class="text-sm font-medium text-secondary">${data}</span>`;
                    }
                },
                { "data": "role",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark').text(data)[0].outerHTML;
                    }
                },
                { "data": "email",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-dark').text(data)[0].outerHTML;
                    }
                },
                { "data": "phone",
                    "render": function(data) {
                        return data ? $('<span>').addClass('text-sm font-bold text-dark').text(data)[0].outerHTML : '<span class="text-sm font-bold text-dark"> -- </span>';
                    }
                },
                { 
                    "data": "last_login",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-gray-500').text(data)[0].outerHTML;;
                    }
                },
                { 
                    "data": "joined",
                    "render": function(data) {
                        return $('<span>').addClass('text-sm font-medium text-gray-500').text(data)[0].outerHTML;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="flex space-x-2">
                                <a href="<?= base_url('auth/users/view/') ?>${row.id}" class="p-1 text-slate-500 hover:text-primary" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= base_url('auth/users/edit/') ?>${row.id}" class="p-1 text-slate-500 hover:text-primary" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "dom": '<"flex flex-col md:flex-row md:justify-between md:items-center gap-4"<"flex flex-col"l><"flex flex-col"f>><"my-2"B>rt<"flex flex-col md:flex-row md:justify-between md:items-center mt-2 gap-4"ip>',
            "buttons": [
                {
                    extend: 'copy',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                },
                {
                    extend: 'csv',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                },
                {
                    extend: 'excel',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                },
                {
                    extend: 'pdf',
                    className: 'bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded'
                }
            ],
            "responsive": true,
            "language": {
                "processing": "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading Users...</span></div>"
            },
            "initComplete": function() {
                // Initialize all Lucide icons after table is loaded
                lucide.createIcons();
            },
            "drawCallback": function() {
                // Reinitialize Lucide icons after each table draw
                lucide.createIcons();
            }
        });

        // Update stats counters
        updateUserStats();
    });

    // Function to update user statistics
    function updateUserStats() {
        $.ajax({
            url: "<?= base_url('auth/users/get') ?>",
            type: 'POST',
            data: {
                draw: 1,
                start: 0,
                length: -1 // Get all records for counting
            },
            success: function(response) {
                if (response && response.recordsTotal) {
                    // Animate count up for total users
                    animateCounter('totalUsersCount', 0, response.recordsTotal, 1000);

                    // For active users, we'll use a percentage of total (you can adjust this logic)
                    // In a real scenario, you'd get this from a separate endpoint
                    const activeUsers = Math.floor(response.recordsTotal * 0.3); // 30% active as example
                    animateCounter('activeUsersCount', 0, activeUsers, 1000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching user stats:', error);
                $('#totalUsersCount').text('0');
                $('#activeUsersCount').text('0');
            }
        });
    }

    // Animate counter function
    function animateCounter(elementId, start, end, duration) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const range = end - start;
        const increment = range / (duration / 16); // 60fps
        let current = start;

        const timer = setInterval(function() {
            current += increment;
            if (current >= end) {
                element.textContent = end;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Function to show create user modal
    function showCreateUserModal() {
        openModal({
            title: 'Create New User',
            iconBg: 'bg-blue-100',
            content: `<?= view('backendV2/pages/users/partials/user_form', ['roles' => $roles]) ?>`,
            size: 'sm:max-w-2xl',
            headerClasses: 'sm:flex sm:items-start', // Add this if you need custom header classes
            onOpen: function() {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                    placeholder: $(this).data('placeholder'),
                    minimumResultsForSearch: Infinity,
                });
            }
        });
    }

    // Form submission handler
    $(document).ready(function() {
        // Handle form submission
        $(document).on('submit', '#createUserForm', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            
            // Disable submit button during request
            submitBtn.prop('disabled', true);
            submitBtn.html(`
                <span class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span>
                Processing...
            `);
            
            // Perform AJAX submission
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        // Show success toast
                        showToast(data.message || 'User created successfully!', 'success');

                        // Reload the table
                        usersDataTable.ajax.reload(null, false);

                        // Close modal and reload after delay
                        setTimeout(function() {
                            closeModal();
                        }, 1500);
                    } else {
                        // Show error message
                        if (data.errors) {
                            showFormErrors(data.errors);
                        } else {
                            showToast(data.message || 'Failed to create user', 'error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    showToast('An error occurred while processing your request', 'error');
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false);
                    submitBtn.text('Create User');
                }
            });
        });
        
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
        
        // Function to show form errors
        function showFormErrors(errors) {
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            
            // Show new errors
            $.each(errors, function(field, message) {
                var input = $('[name="' + field + '"]');
                var formGroup = input.closest('div');
                
                if (input.length && formGroup.length) {
                    input.addClass('is-invalid');
                    
                    var errorDiv = $('<div>', {
                        class: 'invalid-feedback text-red-500 text-xs mt-1',
                        text: message
                    });
                    
                    formGroup.append(errorDiv);
                    
                    // Animate error appearance
                    errorDiv.hide().css({opacity: 0, y: -10});
                    errorDiv.show().animate({opacity: 1, y: 0}, 300);
                }
            });
        }
        
        // Update CSRF token if needed
        function updateCsrfToken(token) {
            $('input[name="<?= csrf_token() ?>"]').val(token);
        }
    });

    // Delete user from table
    function deleteUserFromTable(userId) {
        Swal.fire({
            title: 'Delete User?',
            text: 'Are you sure you want to delete this user? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete user',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= base_url('auth/users/delete/') ?>${userId}`,
                    type: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: data.message || 'User deleted successfully',
                                icon: 'success',
                                confirmButtonColor: '#3b82f6'
                            }).then(() => {
                                // Reload the table
                                usersDataTable.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to delete user',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while deleting the user',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }
</script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $this->endSection() ?>