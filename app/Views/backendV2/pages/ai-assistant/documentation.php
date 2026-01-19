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
        'pageTitle' => 'AI Assistant Documentation',
        'pageDescription' => 'Learn about the AI Assistant capabilities and how it helps the system',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'AI Assistant', 'url' => base_url('auth/ai-assistant')],
            ['label' => 'Documentation']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="space-y-6">
            <!-- Overview Section -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="sparkles" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">AI Assistant Overview</h2>
                        <p class="text-sm text-slate-500">Intelligent assistance for KEWASNET platform</p>
                    </div>
                </div>
                <p class="text-slate-700 leading-relaxed">
                    The KEWASNET AI Assistant is an intelligent virtual assistant integrated into the platform to help both customers and system administrators. 
                    It uses OpenAI's GPT models to provide contextual, helpful responses based on the application's content and user data.
                </p>
            </div>

            <!-- What the AI Does Section -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="zap" class="w-5 h-5 text-primary"></i>
                    What the AI Assistant Does
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="message-circle" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 mb-1">Answers User Questions</h3>
                                <p class="text-sm text-slate-600">Responds to queries about courses, events, resources, programs, FAQs, and other platform content with accurate, contextual information.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="search" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 mb-1">Searches Application Content</h3>
                                <p class="text-sm text-slate-600">Automatically searches relevant content (courses, events, FAQs, pillars, etc.) to provide accurate, up-to-date answers.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 mb-1">Supports Multiple User Types</h3>
                                <p class="text-sm text-slate-600">
                                    <strong>Customers:</strong> Help with courses, events, resources, account inquiries, and general platform navigation.<br>
                                    <strong>Administrators:</strong> Assistance with content management, course creation, event management, and administrative tasks.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="history" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 mb-1">Maintains Conversation History</h3>
                                <p class="text-sm text-slate-600">Remembers previous messages in a conversation for coherent, context-aware responses that understand the full conversation context.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="navigation" class="w-5 h-5 text-pink-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 mb-1">Guides Users</h3>
                                <p class="text-sm text-slate-600">Directs users to appropriate pages (contact forms, help center, privacy policies, etc.) when needed, providing clear navigation assistance.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="target" class="w-5 h-5 text-indigo-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 mb-1">KEWASNET-Focused</h3>
                                <p class="text-sm text-slate-600">System prompts are configured to primarily answer questions related to KEWASNET and water management in Kenya, redirecting off-topic queries appropriately.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capabilities Section -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="star" class="w-5 h-5 text-primary"></i>
                    Key Capabilities
                </h2>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-slate-800">Course Information</h3>
                            <p class="text-sm text-slate-600">Provides detailed information about available courses, curriculum, enrollment, and course-related inquiries.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-slate-800">Event Management</h3>
                            <p class="text-sm text-slate-600">Helps with event information, bookings, schedules, and event-related questions for both customers and administrators.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-slate-800">Resource Discovery</h3>
                            <p class="text-sm text-slate-600">Assists in finding and accessing platform resources, documents, and educational materials.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-slate-800">Content Management</h3>
                            <p class="text-sm text-slate-600">Helps administrators with content creation, editing, and management tasks across the platform.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-slate-800">Account Support</h3>
                            <p class="text-sm text-slate-600">Provides assistance with account-related questions, profile management, and platform navigation.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-slate-800">FAQs & Help</h3>
                            <p class="text-sm text-slate-600">Answers frequently asked questions and provides general help and guidance about the platform.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Details Section -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="settings" class="w-5 h-5 text-primary"></i>
                    Technical Details
                </h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-slate-800 mb-2">AI Model</h3>
                        <p class="text-sm text-slate-600">Uses OpenAI's GPT models (default: GPT-3.5 Turbo) to generate intelligent, context-aware responses.</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-800 mb-2">Context Awareness</h3>
                        <p class="text-sm text-slate-600">The AI automatically searches and includes relevant content from the platform (courses, events, FAQs, etc.) to provide accurate answers.</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-800 mb-2">Rate Limiting</h3>
                        <p class="text-sm text-slate-600">Built-in rate limiting protects against abuse and ensures fair usage across all users.</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-800 mb-2">Conversation Management</h3>
                        <p class="text-sm text-slate-600">Conversations are stored and managed, allowing users to continue previous conversations and maintain context.</p>
                    </div>
                </div>
            </div>

            <!-- Usage Tips Section -->
            <div class="bg-gradient-to-br from-primary/10 to-purple-500/10 rounded-xl shadow-sm p-6 border border-primary/20">
                <h2 class="text-xl font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="lightbulb" class="w-5 h-5 text-primary"></i>
                    Usage Tips
                </h2>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex items-start gap-2">
                        <i data-lucide="arrow-right" class="w-4 h-4 text-primary flex-shrink-0 mt-1"></i>
                        <span>Ask specific questions for better, more accurate responses</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="arrow-right" class="w-4 h-4 text-primary flex-shrink-0 mt-1"></i>
                        <span>The AI remembers your conversation context, so you can ask follow-up questions</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="arrow-right" class="w-4 h-4 text-primary flex-shrink-0 mt-1"></i>
                        <span>For administrators, the AI can help with content creation and management tasks</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="arrow-right" class="w-4 h-4 text-primary flex-shrink-0 mt-1"></i>
                        <span>The AI focuses on KEWASNET-related topics and will redirect off-topic queries</span>
                    </li>
                </ul>
            </div>

            <!-- Back Button -->
            <div class="flex justify-end">
                <a 
                    href="<?= base_url('auth/ai-assistant') ?>" 
                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primaryShades-600 transition-colors flex items-center gap-2"
                >
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to AI Assistant
                </a>
            </div>
        </div>
    </div>
</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    lucide.createIcons();
</script>
<?= $this->endSection() ?>
