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
        'pageTitle' => 'Edit Admin Account',
        'pageDescription' => 'Update admin account information and settings',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Admin Accounts', 'url' => base_url('auth/account')],
            ['label' => 'Edit']
        ],
        'bannerActions' => '<a href="' . base_url('auth/account/view/' . $user['id']) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors mr-2">
            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
            View Account
        </a>
        <a href="' . base_url('auth/account') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Accounts
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Account Information</h2>
                <p class="mt-1 text-sm text-slate-500">Update admin account information, permissions, and status</p>
            </div>

            <form id="editAccountForm" enctype="multipart/form-data">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Personal Information -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="<?= esc($user['first_name']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Enter first name">
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="<?= esc($user['last_name']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="Enter last name">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="<?= esc($user['email']) ?>"
                                   required
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="user@example.com">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="<?= esc($user['phone'] ?? '') ?>"
                                   class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                   placeholder="+254 700 000 000">
                        </div>

                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                                User Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role_id"
                                    name="role_id"
                                    required
                                    class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= esc($role['role_id']) ?>" <?= ($user['role_id'] ?? '') == $role['role_id'] ? 'selected' : '' ?>>
                                        <?= esc($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="account_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <select id="account_status"
                                    name="account_status"
                                    required
                                    class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="active" <?= ($user['account_status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="suspended" <?= ($user['account_status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                Bio
                            </label>
                            <textarea id="bio"
                                      name="bio"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                      placeholder="Enter user bio (optional)"><?= esc($user['bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?= base_url('auth/account') ?>"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            id="submitBtn"
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        <span id="submitText">Update User</span>
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

    document.getElementById('editAccountForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');

        submitBtn.disabled = true;
        submitText.textContent = 'Saving...';
        submitLoader.classList.remove('hidden');

        fetch('<?= base_url('auth/account/update/' . $user['id']) ?>', {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.json())
        .then(data => {
            // Update CSRF token if provided
            if (data.token) {
                const csrfTokenInput = form.querySelector('input[name="<?= csrf_token() ?>"]');
                if (csrfTokenInput) {
                    csrfTokenInput.value = data.token;
                }
            }
            
            if (data.status === 'success') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({icon: 'success', title: 'Success!', text: data.message || 'Account updated successfully', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } else {
                    alert(data.message || 'Account updated successfully');
                }
                setTimeout(() => window.location.href = '<?= base_url('auth/account/view/' . $user['id']) ?>', 1500);
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({icon: 'error', title: 'Error!', text: data.message || 'Failed to update account', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } else {
                    alert(data.message || 'Failed to update account');
                }
                submitBtn.disabled = false;
                submitText.textContent = 'Save Changes';
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
            submitText.textContent = 'Save Changes';
            submitLoader.classList.add('hidden');
        });
    });
</script>
<?= $this->endSection() ?>