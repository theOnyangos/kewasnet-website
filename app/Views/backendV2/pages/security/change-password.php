<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => $isOwnPassword ? 'Change My Password' : 'Change Password',
        'pageDescription' => 'Update your account password',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => $isOwnPassword ? 'My Profile' : 'Admin Accounts', 'url' => $isOwnPassword ? base_url('auth/profile') : base_url('auth/account')],
            ['label' => 'Security', 'url' => base_url('auth/security')],
            ['label' => 'Change Password']
        ],
        'bannerActions' => '<a href="' . base_url('auth/security') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Security
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Change Password</h2>
                <p class="mt-1 text-sm text-slate-500"><?= $isOwnPassword ? 'Enter your current password and choose a new one' : 'Set a new password for this account' ?></p>
            </div>

            <form id="changePasswordForm">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Password Change -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Password Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 max-w-md">
                        <?php if ($isOwnPassword): ?>
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Enter current password">
                        </div>
                        <?php endif; ?>

                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="new_password"
                                   name="new_password"
                                   required
                                   minlength="8"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Enter new password">
                            <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="confirm_password"
                                   name="confirm_password"
                                   required
                                   minlength="8"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= base_url('auth/security') ?>"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            id="submitBtn"
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300 flex items-center">
                        <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                        <span id="submitText">Change Password</span>
                        <i data-lucide="loader" id="submitLoader" class="w-4 h-4 ml-2 hidden animate-spin"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    lucide.createIcons();

    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({icon: 'error', title: 'Error!', text: 'Passwords do not match', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
            } else {
                alert('Passwords do not match');
            }
            return;
        }

        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');

        submitBtn.disabled = true;
        submitText.textContent = 'Changing...';
        submitLoader.classList.remove('hidden');

        fetch('<?= base_url('auth/security/update-password/' . $user['id']) ?>', {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({icon: 'success', title: 'Success!', text: data.message || 'Password changed successfully', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } else {
                    alert(data.message || 'Password changed successfully');
                }
                setTimeout(() => window.location.href = '<?= base_url('auth/security') ?>', 1500);
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({icon: 'error', title: 'Error!', text: data.message || 'Failed to change password', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } else {
                    alert(data.message || 'Failed to change password');
                }
                submitBtn.disabled = false;
                submitText.textContent = 'Change Password';
                submitLoader.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({icon: 'error', title: 'Error!', text: 'An error occurred', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
            } else {
                alert('An error occurred');
            }
            submitBtn.disabled = false;
            submitText.textContent = 'Change Password';
            submitLoader.classList.add('hidden');
        });
    });
</script>
<?= $this->endSection() ?>