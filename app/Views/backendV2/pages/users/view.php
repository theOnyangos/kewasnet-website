<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?? 'View User Details' ?>
<?= $this->endSection(); ?>

<?= $this->section('styles') ?>
<style>
    .info-card {
        @apply bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6;
    }
    .info-label {
        @apply text-sm font-medium text-gray-500 mb-1;
    }
    .info-value {
        @apply text-base text-gray-900 font-medium;
    }
    .section-title {
        @apply text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200;
    }
    .status-badge {
        @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium;
    }
</style>
<?= $this->endSection() ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">

    <!-- Hero Banner with Gradient Overlay -->
    <div class="relative h-48 mb-6 overflow-hidden bg-cover bg-center" style="background-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%), url('<?= base_url('images/dashboard-breadcrumb.jpg') ?>');">
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
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                            <a href="<?= base_url('auth/users') ?>" class="ml-1 text-sm font-medium text-white/80 hover:text-white transition-colors md:ml-2">
                                Users
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                            <span class="ml-1 text-sm font-medium text-white md:ml-2">View User</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Title and Actions -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 drop-shadow-lg">
                        User Details
                    </h1>
                    <p class="text-lg text-white/90">
                        View comprehensive user information and activity
                    </p>
                </div>

                <div class="hidden md:flex gap-3">
                    <a href="<?= base_url('auth/users/edit/' . $user['id']) ?>" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                        Edit User
                    </a>
                    <a href="<?= base_url('auth/users') ?>" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 pb-6">
        <!-- Profile Overview Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <!-- Profile Picture -->
                <div class="flex-shrink-0">
                    <?php if (!empty($user['picture'])): ?>
                        <img src="<?= base_url('uploads/profiles/' . $user['picture']) ?>" alt="<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100">
                    <?php else: ?>
                        <div class="w-32 h-32 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center border-4 border-gray-100">
                            <span class="text-4xl font-bold text-white">
                                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- User Info -->
                <div class="flex-grow">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </h2>
                            <p class="text-gray-500">
                                <i data-lucide="mail" class="w-4 h-4 inline mr-1"></i>
                                <?= esc($user['email']) ?>
                            </p>
                        </div>
                        <div>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="status-badge bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                    Active
                                </span>
                            <?php else: ?>
                                <span class="status-badge bg-red-100 text-red-800">
                                    <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                    Inactive
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="info-label">Phone Number</p>
                            <p class="info-value">
                                <i data-lucide="phone" class="w-4 h-4 inline mr-1 text-gray-400"></i>
                                <?= esc($user['phone'] ?? 'Not provided') ?>
                            </p>
                        </div>
                        <div>
                            <p class="info-label">Employee Number</p>
                            <p class="info-value">
                                <i data-lucide="hash" class="w-4 h-4 inline mr-1 text-gray-400"></i>
                                <?= esc($user['employee_number'] ?? 'N/A') ?>
                            </p>
                        </div>
                        <div>
                            <p class="info-label">Role</p>
                            <p class="info-value">
                                <i data-lucide="shield" class="w-4 h-4 inline mr-1 text-gray-400"></i>
                                <?= esc($user['role'] ?? 'User') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 bg-white p-5 rounded-lg shadow">
            <!-- Left Column - Personal Information -->
            <div class="lg:col-span-2 space-y-10">
                <!-- Personal Information -->
                <div class="info-card">
                    <h3 class="section-title font-bold mb-2">
                        <i data-lucide="user" class="w-5 h-5 inline mr-2"></i>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="info-label">First Name</p>
                            <p class="info-value"><?= esc($user['first_name']) ?></p>
                        </div>
                        <div>
                            <p class="info-label">Last Name</p>
                            <p class="info-value"><?= esc($user['last_name']) ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="info-label">Bio</p>
                            <p class="info-value"><?= esc($user['bio'] ?? 'No bio provided') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="info-card">
                    <h3 class="section-title font-bold mb-2">
                        <i data-lucide="settings" class="w-5 h-5 inline mr-2"></i>
                        Account Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="info-label">Account Status</p>
                            <p class="info-value">
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="text-green-600">Active</span>
                                <?php else: ?>
                                    <span class="text-red-600">Inactive</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <p class="info-label">Email Verified</p>
                            <p class="info-value">
                                <?php if (!empty($user['email_verified_at'])): ?>
                                    <span class="text-green-600">
                                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                        Verified
                                    </span>
                                <?php else: ?>
                                    <span class="text-orange-600">
                                        <i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i>
                                        Not Verified
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <p class="info-label">Member Since</p>
                            <p class="info-value">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1 text-gray-400"></i>
                                <?= date('F j, Y', strtotime($user['created_at'])) ?>
                            </p>
                        </div>
                        <div>
                            <p class="info-label">Last Login</p>
                            <p class="info-value">
                                <i data-lucide="clock" class="w-4 h-4 inline mr-1 text-gray-400"></i>
                                <?= !empty($user['last_login']) ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never' ?>
                            </p>
                        </div>
                        <div>
                            <p class="info-label">Last Updated</p>
                            <p class="info-value">
                                <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-1 text-gray-400"></i>
                                <?= !empty($user['updated_at']) ? date('F j, Y g:i A', strtotime($user['updated_at'])) : 'Never' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Activity Statistics -->
                <div class="info-card">
                    <h3 class="section-title font-bold mb-2">
                        <i data-lucide="activity" class="w-5 h-5 inline mr-2"></i>
                        Activity Statistics
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="flex justify-center mb-2">
                                <i data-lucide="book-open" class="w-8 h-8 text-blue-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-blue-900"><?= $stats['enrollments'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Course Enrollments</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="flex justify-center mb-2">
                                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-green-900"><?= $stats['completions'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Completions</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="flex justify-center mb-2">
                                <i data-lucide="award" class="w-8 h-8 text-purple-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-purple-900"><?= $stats['certificates'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Certificates</p>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <div class="flex justify-center mb-2">
                                <i data-lucide="message-square" class="w-8 h-8 text-orange-600"></i>
                            </div>
                            <p class="text-2xl font-bold text-orange-900"><?= $stats['discussions'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Discussions</p>
                        </div>
                    </div>
                </div>

                <!-- Login Activities -->
                <div class="info-card">
                    <h3 class="section-title font-bold mb-4">
                        <i data-lucide="monitor" class="w-5 h-5 inline mr-2"></i>
                        Login Activities
                    </h3>
                    <div class="overflow-x-auto">
                        <table id="loginActivitiesTable" class="data-table stripe hover" style="width:100%">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col">
                                        Browser
                                    </th>
                                    <th scope="col">
                                        Platform
                                    </th>
                                    <th scope="col">
                                        IP Address
                                    </th>
                                    <th scope="col">
                                        Login Type
                                    </th>
                                    <th scope="col">
                                        Login Time
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions & Additional Info -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="info-card">
                    <h3 class="section-title font-bold mb-2">
                        <i data-lucide="zap" class="w-5 h-5 inline mr-2"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <a href="<?= base_url('auth/users/edit/' . $user['id']) ?>" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="flex items-center text-gray-700">
                                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                                Edit Profile
                            </span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </a>
                        <button onclick="resetPassword('<?= $user['id'] ?>')" class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="flex items-center text-gray-700">
                                <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                                Reset Password
                            </span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </button>
                        <button onclick="sendVerificationEmail('<?= $user['id'] ?>')" class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="flex items-center text-gray-700">
                                <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                Send Verification Email
                            </span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </button>
                        <?php if ($user['status'] === 'active'): ?>
                            <button onclick="suspendAccount('<?= $user['id'] ?>')" class="w-full flex items-center justify-between p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                <span class="flex items-center text-red-700">
                                    <i data-lucide="user-x" class="w-4 h-4 mr-2"></i>
                                    Suspend Account
                                </span>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-red-400"></i>
                            </button>
                        <?php else: ?>
                            <button onclick="activateAccount('<?= $user['id'] ?>')" class="w-full flex items-center justify-between p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                                <span class="flex items-center text-green-700">
                                    <i data-lucide="user-check" class="w-4 h-4 mr-2"></i>
                                    Activate Account
                                </span>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-green-400"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Enrollments -->
                <?php if (!empty($recentEnrollments)): ?>
                <div class="info-card">
                    <h3 class="section-title">
                        <i data-lucide="graduation-cap" class="w-5 h-5 inline mr-2"></i>
                        Recent Enrollments
                    </h3>
                    <div class="space-y-3">
                        <?php foreach (array_slice($recentEnrollments, 0, 5) as $enrollment): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-grow">
                                <p class="text-sm font-medium text-gray-900"><?= esc($enrollment['course_title']) ?></p>
                                <p class="text-xs text-gray-500"><?= date('M j, Y', strtotime($enrollment['enrolled_at'])) ?></p>
                            </div>
                            <?php if ($enrollment['status'] === 'completed'): ?>
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                            <?php else: ?>
                                <i data-lucide="clock" class="w-5 h-5 text-orange-500"></i>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- System Information -->
                <div class="info-card">
                    <h3 class="section-title font-bold mb-2">
                        <i data-lucide="info" class="w-5 h-5 inline mr-2"></i>
                        System Information
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">User ID:</span>
                            <span class="font-medium text-gray-900"><?= esc($user['id']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Account Created:</span>
                            <span class="font-medium text-gray-900"><?= date('M j, Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        <?php if (!empty($user['deleted_at'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Deleted At:</span>
                            <span class="font-medium text-red-600"><?= date('M j, Y', strtotime($user['deleted_at'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        // Initialize Login Activities DataTable
        $(document).ready(function() {
            $('#loginActivitiesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= base_url('auth/users/login-activities/' . $user['id']) ?>',
                    type: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                },
                columns: [
                    {
                        data: 'browser',
                        render: function(data, type, row) {
                            return `<div class="flex items-center">
                                <i data-lucide="globe" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span class="text-sm text-gray-900">${data || 'Unknown'}</span>
                            </div>`;
                        }
                    },
                    {
                        data: 'platform',
                        render: function(data, type, row) {
                            return `<div class="flex items-center">
                                <i data-lucide="smartphone" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span class="text-sm text-gray-900">${data || 'Unknown'}</span>
                            </div>`;
                        }
                    },
                    {
                        data: 'ip_address',
                        render: function(data, type, row) {
                            return `<span class="text-sm text-gray-900 font-mono">${data || 'N/A'}</span>`;
                        }
                    },
                    {
                        data: 'login_type',
                        render: function(data, type, row) {
                            const loginType = (data || 'standard').toLowerCase();
                            let badgeClass = 'bg-blue-100 text-blue-800';
                            let iconName = 'user';
                            
                            if (loginType === 'google') {
                                badgeClass = 'bg-red-100 text-red-800';
                                iconName = 'mail';
                            } else if (loginType === 'facebook') {
                                badgeClass = 'bg-indigo-100 text-indigo-800';
                                iconName = 'share-2';
                            }
                            
                            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">
                                <i data-lucide="${iconName}" class="w-3 h-3 mr-1"></i>
                                ${data ? data.charAt(0).toUpperCase() + data.slice(1) : 'Standard'}
                            </span>`;
                        }
                    },
                    {
                        data: 'login_time',
                        render: function(data, type, row) {
                            if (!data) return 'N/A';
                            const date = new Date(data);
                            const formatted = date.toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                year: 'numeric',
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            });
                            return `<div class="flex items-center">
                                <i data-lucide="clock" class="w-4 h-4 mr-2 text-gray-400"></i>
                                ${formatted}
                            </div>`;
                        }
                    }
                ],
                order: [[4, 'desc']], // Sort by login time descending
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    emptyTable: 'No login activities found',
                    zeroRecords: 'No matching login activities found'
                },
                drawCallback: function() {
                    // Reinitialize Lucide icons after table redraw
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        });

        function resetPassword(userId) {
            Swal.fire({
                title: 'Reset User Password',
                text: 'A new password will be generated and sent to the user\'s email',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset password'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Resetting password, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?= base_url('auth/users/reset-password/') ?>' + userId,
                        type: 'POST',
                        data: {
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Password Reset!',
                                    response.message,
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Failed to reset password',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while resetting the password';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function sendVerificationEmail(userId) {
            Swal.fire({
                title: 'Send Verification Email',
                text: 'Send a verification email to this user?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send email'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Sending verification email, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?= base_url('auth/users/send-verification/') ?>' + userId,
                        type: 'POST',
                        data: {
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Email Sent!',
                                    response.message,
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Failed to send verification email',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while sending the email';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function suspendAccount(userId) {
            Swal.fire({
                title: 'Suspend Account',
                text: 'Are you sure you want to suspend this account?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, suspend account'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Suspending account, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?= base_url('auth/users/toggle-status/') ?>' + userId,
                        type: 'POST',
                        data: {
                            action: 'suspend',
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Account Suspended!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Failed to suspend account',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while suspending the account';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function activateAccount(userId) {
            Swal.fire({
                title: 'Activate Account',
                text: 'Are you sure you want to activate this account?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, activate account'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Activating account, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?= base_url('auth/users/toggle-status/') ?>' + userId,
                        type: 'POST',
                        data: {
                            action: 'activate',
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Account Activated!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Failed to activate account',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while activating the account';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
<?= $this->endSection() ?>
