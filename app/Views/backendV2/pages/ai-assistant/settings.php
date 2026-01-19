<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);

    // Resolve persisted settings (DB) as primary source for what is "selected" in the UI.
    // The config values can reflect .env defaults; admins expect the saved DB value to be shown.
    $settingsMap = [];
    if (isset($settings) && is_array($settings)) {
        foreach ($settings as $row) {
            if (is_array($row) && isset($row['setting_key'])) {
                $settingsMap[$row['setting_key']] = $row['setting_value'] ?? null;
            }
        }
    }

    $selectedModel = $settingsMap['defaultModel'] ?? ($config->defaultModel ?? 'gpt-3.5-turbo');
    $selectedAssistantId = $settingsMap['assistantId'] ?? ($config->assistantId ?? '');
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'AI Assistant Settings',
        'pageDescription' => 'Configure AI assistant behavior and preferences',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'AI Assistant', 'url' => base_url('auth/ai-assistant')],
            ['label' => 'Settings']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form id="aiSettingsForm" class="space-y-6">
                <!-- AI Agent Enabled -->
                <div class="flex items-center justify-between p-4 border border-slate-200 rounded-lg">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Enable AI Assistant</h3>
                        <p class="text-sm text-slate-500 mt-1">Enable or disable the AI assistant for all users</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="enabled" 
                            id="aiEnabled"
                            class="sr-only peer"
                            <?= ($config->enabled ?? false) ? 'checked' : '' ?>
                        >
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>

                <!-- API Key -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <label for="apiKey" class="block text-sm font-medium text-slate-700 mb-2">
                        OpenAI API Key
                    </label>
                    <input 
                        type="password" 
                        id="apiKey" 
                        name="apiKey"
                        value="<?= esc($config->apiKey ?? '') ?>"
                        placeholder="sk-..."
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    />
                    <p class="text-xs text-slate-500 mt-1">Your OpenAI API key (stored securely)</p>
                </div>

                <!-- Default Model -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <label for="defaultModel" class="block text-sm font-medium text-slate-700">
                            Default Model
                        </label>
                        <button type="button" id="refreshModelsBtn" class="px-3 py-2 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors">
                            Refresh list
                        </button>
                    </div>
                    <select 
                        id="defaultModel" 
                        name="defaultModel"
                        class="select2 w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                        <option value="gpt-3.5-turbo" <?= ($selectedModel ?? 'gpt-3.5-turbo') === 'gpt-3.5-turbo' ? 'selected' : '' ?>>GPT-3.5 Turbo</option>
                        <option value="gpt-4" <?= ($selectedModel ?? 'gpt-3.5-turbo') === 'gpt-4' ? 'selected' : '' ?>>GPT-4</option>
                        <option value="gpt-4-turbo" <?= ($selectedModel ?? 'gpt-3.5-turbo') === 'gpt-4-turbo' ? 'selected' : '' ?>>GPT-4 Turbo</option>
                        <?php if (!empty($selectedModel) && !in_array($selectedModel, ['gpt-3.5-turbo', 'gpt-4', 'gpt-4-turbo'], true)): ?>
                            <option value="<?= esc($selectedModel) ?>" selected><?= esc($selectedModel) ?> (current)</option>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">The default model to use. Use “Refresh list” to load available models from OpenAI.</p>
                </div>

                <!-- OpenAI Assistant (Agents) -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <label for="assistantId" class="block text-sm font-medium text-slate-700">
                            OpenAI Assistant (Agent)
                        </label>
                        <button type="button" id="refreshAssistantsBtn" class="px-3 py-2 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors">
                            Refresh list
                        </button>
                    </div>
                    <select 
                        id="assistantId" 
                        name="assistantId"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                        <option value="" <?= empty($selectedAssistantId ?? '') ? 'selected' : '' ?>>None (use current chat model)</option>
                        <?php if (!empty($selectedAssistantId ?? '')): ?>
                            <option value="<?= esc($selectedAssistantId) ?>" selected><?= esc($selectedAssistantId) ?> (current)</option>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Fetches your Assistants from OpenAI so you can select one to use (and save its ID).</p>
                </div>

                <!-- Max Tokens -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <label for="maxTokens" class="block text-sm font-medium text-slate-700 mb-2">
                        Max Tokens
                    </label>
                    <input 
                        type="number" 
                        id="maxTokens" 
                        name="maxTokens"
                        value="<?= esc($config->maxTokens ?? 1000) ?>"
                        min="100"
                        max="4000"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    />
                    <p class="text-xs text-slate-500 mt-1">Maximum tokens per response (100-4000)</p>
                </div>

                <!-- Temperature -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <label for="temperature" class="block text-sm font-medium text-slate-700 mb-2">
                        Temperature
                    </label>
                    <input 
                        type="number" 
                        id="temperature" 
                        name="temperature"
                        value="<?= esc($config->temperature ?? 0.7) ?>"
                        min="0"
                        max="2"
                        step="0.1"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    />
                    <p class="text-xs text-slate-500 mt-1">Controls randomness (0-2, lower = more focused)</p>
                </div>

                <!-- Rate Limits -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 border border-slate-200 rounded-lg">
                        <label for="rateLimitPerUser" class="block text-sm font-medium text-slate-700 mb-2">
                            Rate Limit Per User
                        </label>
                        <input 
                            type="number" 
                            id="rateLimitPerUser" 
                            name="rateLimitPerUser"
                            value="<?= esc($config->rateLimitPerUser ?? 30) ?>"
                            min="1"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p class="text-xs text-slate-500 mt-1">Messages per hour per user</p>
                    </div>

                    <div class="p-4 border border-slate-200 rounded-lg">
                        <label for="rateLimitPerIP" class="block text-sm font-medium text-slate-700 mb-2">
                            Rate Limit Per IP
                        </label>
                        <input 
                            type="number" 
                            id="rateLimitPerIP" 
                            name="rateLimitPerIP"
                            value="<?= esc($config->rateLimitPerIP ?? 60) ?>"
                            min="1"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p class="text-xs text-slate-500 mt-1">Messages per hour per IP address</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 pt-4 border-t border-slate-200">
                    <a 
                        href="<?= base_url('auth/ai-assistant') ?>" 
                        class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primaryShades-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span id="saveButtonText">Save Settings</span>
                        <span id="saveButtonLoading" class="hidden">Saving...</span>
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

    const settingsForm = document.getElementById('aiSettingsForm');
    const saveButtonText = document.getElementById('saveButtonText');
    const saveButtonLoading = document.getElementById('saveButtonLoading');

    settingsForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(settingsForm);
        const data = {
            enabled: document.getElementById('aiEnabled').checked,
            apiKey: formData.get('apiKey'),
            defaultModel: formData.get('defaultModel'),
            assistantId: formData.get('assistantId'),
            maxTokens: parseInt(formData.get('maxTokens')),
            temperature: parseFloat(formData.get('temperature')),
            rateLimitPerUser: parseInt(formData.get('rateLimitPerUser')),
            rateLimitPerIP: parseInt(formData.get('rateLimitPerIP')),
        };

        // Disable form
        settingsForm.querySelector('button[type="submit"]').disabled = true;
        saveButtonText.classList.add('hidden');
        saveButtonLoading.classList.remove('hidden');

        try {
            const response = await fetch('<?= base_url('auth/ai-assistant/settings') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    settings: data,
                }),
            });

            const result = await response.json();

            if (result.success) {
                // Show success message with SweetAlert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Settings saved successfully!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert('Settings saved successfully!');
                }
                // Optionally reload the page to reflect changes
                // window.location.reload();
            } else {
                // Show error message with SweetAlert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message || 'Failed to save settings',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert('Error: ' + (result.message || 'Failed to save settings'));
                }
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            // Show error message with SweetAlert
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error saving settings. Please try again.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert('Error saving settings. Please try again.');
            }
        } finally {
            // Re-enable form
            settingsForm.querySelector('button[type="submit"]').disabled = false;
            saveButtonText.classList.remove('hidden');
            saveButtonLoading.classList.add('hidden');
        }
    });

    // Load OpenAI assistants list
    const assistantSelect = document.getElementById('assistantId');
    const refreshAssistantsBtn = document.getElementById('refreshAssistantsBtn');
    const modelSelect = document.getElementById('defaultModel');
    const refreshModelsBtn = document.getElementById('refreshModelsBtn');

    async function loadAssistants() {
        if (!assistantSelect) return;
        try {
            refreshAssistantsBtn && (refreshAssistantsBtn.disabled = true);
            const resp = await fetch('<?= base_url('auth/ai-assistant/openai/assistants') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            const data = await resp.json();

            if (!data.success) {
                const msg = data.message || 'Failed to fetch assistants';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                }
                return;
            }

            const saved = String(assistantSelect.value || '');
            assistantSelect.innerHTML = '';
            const noneOpt = document.createElement('option');
            noneOpt.value = '';
            noneOpt.textContent = 'None (use current chat model)';
            assistantSelect.appendChild(noneOpt);

            (data.assistants || []).forEach((a) => {
                const opt = document.createElement('option');
                opt.value = a.id;
                const label = a.name ? `${a.name} (${a.id})` : a.id;
                opt.textContent = a.model ? `${label} — ${a.model}` : label;
                assistantSelect.appendChild(opt);
            });

            // Restore selection if possible
            assistantSelect.value = saved;
        } catch (e) {
            console.error('Failed to load assistants', e);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load assistants.', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
            }
        } finally {
            refreshAssistantsBtn && (refreshAssistantsBtn.disabled = false);
        }
    }

    refreshAssistantsBtn?.addEventListener('click', loadAssistants);
    // Auto-load on page open
    loadAssistants();

    async function loadModels() {
        if (!modelSelect) return;
        try {
            refreshModelsBtn && (refreshModelsBtn.disabled = true);
            const resp = await fetch('<?= base_url('auth/ai-assistant/openai/models') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            const data = await resp.json();

            if (!data.success) {
                const msg = data.message || 'Failed to fetch models';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                }
                return;
            }

            const saved = String(modelSelect.value || '');
            modelSelect.innerHTML = '';
            const ids = new Set();
            (data.models || []).forEach((m) => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.id;
                modelSelect.appendChild(opt);
                ids.add(String(m.id));
            });

            // If the currently selected model isn't in the filtered list,
            // keep it visible (so admins can see what's configured).
            if (saved && !ids.has(saved)) {
                const opt = document.createElement('option');
                opt.value = saved;
                opt.textContent = `${saved} (current)`;
                modelSelect.insertBefore(opt, modelSelect.firstChild);
            }
            modelSelect.value = saved || (modelSelect.options[0]?.value ?? '');

            // If select2 is active, refresh it
            if (window.$ && $(modelSelect).hasClass('select2-hidden-accessible')) {
                $(modelSelect).trigger('change.select2');
            }
        } catch (e) {
            console.error('Failed to load models', e);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load models.', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
            }
        } finally {
            refreshModelsBtn && (refreshModelsBtn.disabled = false);
        }
    }

    refreshModelsBtn?.addEventListener('click', loadModels);
    loadModels();
</script>
<?= $this->endSection() ?>
