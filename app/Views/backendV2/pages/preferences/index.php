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
        'pageTitle' => 'My Preferences',
        'pageDescription' => 'Manage your personal preferences and settings',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Preferences']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Preferences</h2>
                <p class="mt-1 text-sm text-slate-500">Customize your experience and notification settings</p>
            </div>

            <form id="preferencesForm">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- General Settings -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">General Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                Language
                            </label>
                            <select id="language"
                                    name="language"
                                    class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="en" selected>English</option>
                            </select>
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                Timezone
                            </label>
                            <select id="timezone"
                                    name="timezone"
                                    class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="Africa/Nairobi" selected>Africa/Nairobi (EAT)</option>
                            </select>
                        </div>

                        <div>
                            <label for="theme" class="block text-sm font-medium text-gray-700 mb-2">
                                Theme
                            </label>
                            <select id="theme"
                                    name="theme"
                                    class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="light" selected>Light</option>
                                <option value="dark">Dark</option>
                                <option value="auto">Auto</option>
                            </select>
                        </div>

                        <div>
                            <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                Items Per Page
                            </label>
                            <select id="items_per_page"
                                    name="items_per_page"
                                    class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="border-b pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Notifications</h3>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="notifications_email"
                                   value="1"
                                   class="w-4 h-4 text-primary border-borderColor rounded focus:ring-primary">
                            <span class="ml-3 text-sm text-gray-700">Email Notifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="notifications_sms"
                                   value="1"
                                   class="w-4 h-4 text-primary border-borderColor rounded focus:ring-primary">
                            <span class="ml-3 text-sm text-gray-700">SMS Notifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="notifications_push"
                                   value="1"
                                   class="w-4 h-4 text-primary border-borderColor rounded focus:ring-primary">
                            <span class="ml-3 text-sm text-gray-700">Push Notifications</span>
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <button type="submit"
                            id="submitBtn"
                            class="gradient-btn px-6 py-2 rounded-lg text-white hover:shadow-lg transition-all duration-300 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        <span id="submitText">Save Preferences</span>
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

    document.getElementById('preferencesForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');

        submitBtn.disabled = true;
        submitText.textContent = 'Saving...';
        submitLoader.classList.remove('hidden');

        fetch('<?= base_url('auth/preferences/update') ?>', {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({icon: 'success', title: 'Success!', text: data.message || 'Preferences saved successfully', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } else {
                    alert(data.message || 'Preferences saved successfully');
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({icon: 'error', title: 'Error!', text: data.message || 'Failed to save preferences', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } else {
                    alert(data.message || 'Failed to save preferences');
                }
            }
            submitBtn.disabled = false;
            submitText.textContent = 'Save Preferences';
            submitLoader.classList.add('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({icon: 'error', title: 'Error!', text: 'An error occurred', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
            } else {
                alert('An error occurred');
            }
            submitBtn.disabled = false;
            submitText.textContent = 'Save Preferences';
            submitLoader.classList.add('hidden');
        });
    });
</script>
<?= $this->endSection() ?>