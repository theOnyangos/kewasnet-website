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
        'pageTitle' => 'AI Assistant',
        'pageDescription' => 'Get help with content management, course creation, and administrative tasks',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'AI Assistant']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <!-- Mount point used to restore fullscreen elements -->
        <div id="aiAssistantMount"></div>

        <!-- Expand overlay -->
        <div id="aiAssistantOverlay" class="hidden fixed inset-0 bg-black/30 z-40"></div>

        <div id="aiAssistantCard" class="bg-white rounded-xl shadow-sm h-[calc(100vh-200px)] flex flex-col relative z-50">
            <!-- Chat Header -->
            <div class="border-b borderColor p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">AI Assistant</h3>
                        <p class="text-sm text-slate-500">Ask me anything about managing content, courses, events, and more</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="expandAssistantBtn" type="button" class="px-4 py-2 text-sm bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors flex items-center gap-2">
                            <i data-lucide="maximize-2" class="w-4 h-4"></i> Expand
                        </button>
                        <a href="<?= base_url('auth/ai-assistant/knowledge-base') ?>" class="px-4 py-2 text-sm bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors flex items-center gap-2">
                            <i data-lucide="database" class="w-4 h-4"></i> Knowledge Base
                        </a>
                        <a href="<?= base_url('auth/ai-assistant/documentation') ?>" class="px-4 py-2 text-sm bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors flex items-center gap-2">
                            <i data-lucide="book-open" class="w-4 h-4"></i> Documentation
                        </a>
                        <a href="<?= base_url('auth/ai-assistant/settings') ?>" class="px-4 py-2 text-sm bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors flex items-center gap-2">
                            <i data-lucide="settings" class="w-4 h-4"></i> Settings
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex-1 flex overflow-hidden">
                <!-- Conversations panel -->
                <aside class="w-80 border-r borderColor flex flex-col">
                    <div class="p-4 border-b borderColor">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-slate-800">Start a New Conversations</h4>
                            <div class="flex items-center gap-2">
                                <button id="newConversationBtn" type="button" class="p-2 rounded-lg bg-primary text-white hover:bg-primaryShades-600 transition-colors" aria-label="New conversation">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Select a conversation to continue.</p>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <div id="conversationEmptyState" class="p-4 text-sm text-slate-500 hidden">No conversations yet.</div>
                        <ul id="conversationList" class="divide-y divide-slate-100"></ul>
                    </div>
                </aside>

                <!-- Chat area -->
                <section class="flex-1 flex flex-col">
                    <!-- Chat Messages Area -->
                    <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4"></div>

                    <!-- Chat Input Area -->
                    <div class="border-t borderColor p-4">
                        <form id="chatForm" class="flex gap-2 items-end">
                            <textarea 
                                id="messageInput" 
                                placeholder="Type your message..." 
                                rows="1"
                                class="flex-1 px-4 py-2 border borderColor rounded-lg focus:none outline-none focus:ring-0 resize-none overflow-hidden"
                                style="outline: none !important; border: 1px solid #cbd5e1; min-height: 40px; max-height: 120px;"
                                required
                            ></textarea>
                            <button 
                                type="submit" 
                                id="sendButton"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primaryShades-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed h-fit flex items-center justify-center"
                                aria-label="Send message"
                            >
                                <span id="sendButtonText" class="flex items-center justify-center">
                                    <i data-lucide="send" class="w-5 h-5"></i>
                                </span>
                                <span id="sendButtonLoading" class="hidden flex items-center justify-center">
                                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                </span>
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Initialize icons
    function initIcons() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    initIcons();

    let currentConversationId = null;
    let isExpanded = false;
    let conversationsCache = [];

    const aiAssistantOverlay = document.getElementById('aiAssistantOverlay');
    const aiAssistantCard = document.getElementById('aiAssistantCard');
    const aiAssistantMount = document.getElementById('aiAssistantMount');
    const originalPositions = {
        cardParent: null,
        cardNextSibling: null,
        overlayParent: null,
        overlayNextSibling: null,
    };
    const conversationList = document.getElementById('conversationList');
    const conversationEmptyState = document.getElementById('conversationEmptyState');

    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const sendButtonText = document.getElementById('sendButtonText');
    const sendButtonLoading = document.getElementById('sendButtonLoading');

    // Show welcome message function
    function showWelcomeMessage() {
        chatMessages.innerHTML = `
            <div class="flex justify-start">
                <div class="max-w-[80%] bg-slate-100 rounded-lg p-4">
                    <p class="text-slate-800">Hello! I'm your AI assistant. I can help you with:</p>
                    <ul class="list-disc list-inside mt-2 text-sm text-slate-600">
                        <li>Creating and managing courses</li>
                        <li>Managing events and bookings</li>
                        <li>Finding resources and content</li>
                        <li>General questions about the platform</li>
                    </ul>
                    <p class="text-slate-800 mt-2">What would you like to know?</p>
                </div>
            </div>
        `;
    }

    // Load conversation history on page load
    loadConversationHistory();

    function loadConversationHistory() {
        fetch('<?= base_url('api/ai/conversations') ?>?status=active', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            conversationsCache = (data && data.success && Array.isArray(data.conversations)) ? data.conversations : [];
            renderConversationList(conversationsCache);

            if (conversationsCache.length > 0) {
                // Load the most recent conversation
                const latestConversation = conversationsCache[0];
                selectConversation(latestConversation.id);
            } else {
                startNewConversation();
            }
        })
        .catch(error => {
            console.error('Error loading conversation history:', error);
            conversationsCache = [];
            renderConversationList(conversationsCache);
            startNewConversation();
        });
    }

    function renderConversationList(conversations) {
        if (!conversationList) return;
        conversationList.innerHTML = '';

        if (!conversations || conversations.length === 0) {
            conversationEmptyState?.classList.remove('hidden');
            return;
        }

        conversationEmptyState?.classList.add('hidden');

        conversations.forEach((c) => {
            const li = document.createElement('li');
            li.className = 'p-3 hover:bg-slate-50 cursor-pointer flex items-center justify-between gap-2';
            li.dataset.conversationId = c.id;

            const left = document.createElement('div');
            left.className = 'min-w-0';
            const title = document.createElement('div');
            title.className = 'text-sm font-medium text-slate-800 truncate';
            title.dataset.role = 'conv-title';
            title.textContent = 'Conversation';

            const meta = document.createElement('div');
            meta.className = 'text-xs text-slate-500 truncate';
            meta.dataset.role = 'conv-meta';
            meta.textContent = formatConversationMeta(c);

            left.appendChild(title);
            left.appendChild(meta);

            const actions = document.createElement('div');
            actions.className = 'flex items-center gap-1';

            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'p-2 rounded-lg hover:bg-slate-100 text-slate-600';
            delBtn.dataset.role = 'conv-delete';
            delBtn.setAttribute('aria-label', 'Delete conversation');
            delBtn.innerHTML = `<i data-lucide="trash-2" class="w-4 h-4"></i>`;
            delBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                deleteConversation(c.id);
            });

            actions.appendChild(delBtn);
            li.appendChild(left);
            li.appendChild(actions);

            li.addEventListener('click', () => {
                selectConversation(c.id);
            });

            conversationList.appendChild(li);
        });

        initIcons();
        highlightSelectedConversation();
    }

    function formatConversationMeta(c) {
        const updatedAt = c.updated_at || c.created_at || '';
        if (!updatedAt) return '—';
        return `Updated: ${updatedAt}`;
    }

    function highlightSelectedConversation() {
        if (!conversationList) return;
        conversationList.querySelectorAll('li').forEach((li) => {
            const isActive = li.dataset.conversationId === currentConversationId;
            // Row background
            li.classList.toggle('bg-primary', isActive);
            li.classList.toggle('hover:bg-primaryShades-600', isActive);
            li.classList.toggle('hover:bg-slate-50', !isActive);

            // Row text colors
            const title = li.querySelector('[data-role="conv-title"]');
            const meta = li.querySelector('[data-role="conv-meta"]');
            const del = li.querySelector('[data-role="conv-delete"]');

            if (title) {
                title.classList.toggle('text-white', isActive);
                title.classList.toggle('text-slate-800', !isActive);
            }
            if (meta) {
                meta.classList.toggle('text-white/80', isActive);
                meta.classList.toggle('text-slate-500', !isActive);
            }
            if (del) {
                del.classList.toggle('text-white', isActive);
                del.classList.toggle('hover:bg-white/10', isActive);
                del.classList.toggle('text-slate-600', !isActive);
                del.classList.toggle('hover:bg-slate-100', !isActive);
            }
        });
    }

    function selectConversation(conversationId) {
        currentConversationId = conversationId;
        highlightSelectedConversation();
        loadConversation(conversationId);
    }

    function startNewConversation() {
        currentConversationId = null;
        chatMessages.innerHTML = '';
        showWelcomeMessage();
        highlightSelectedConversation();
    }

    function loadConversation(conversationId) {
        fetch(`<?= base_url('api/ai/conversations') ?>/${conversationId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.conversation && data.conversation.messages) {
                // Clear existing messages
                chatMessages.innerHTML = '';
                
                // Check if there are any non-system messages
                const nonSystemMessages = data.conversation.messages.filter(msg => msg.role !== 'system');
                
                // If no messages, show welcome message
                if (nonSystemMessages.length === 0) {
                    showWelcomeMessage();
                } else {
                    // Add all messages
                    data.conversation.messages.forEach(msg => {
                        if (msg.role !== 'system') {
                            addMessageToChat(msg.role, msg.content);
                        }
                    });
                }
                
                scrollToBottom();
            } else {
                // If conversation load fails, ensure welcome message is shown
                if (chatMessages.children.length === 0) {
                    showWelcomeMessage();
                }
            }
        })
        .catch(error => {
            console.error('Error loading conversation:', error);
            // If error, ensure welcome message is shown
            if (chatMessages.children.length === 0) {
                showWelcomeMessage();
            }
        });
    }

    function showWelcomeMessage() {
        chatMessages.innerHTML = `
            <div class="flex justify-start">
                <div class="max-w-[80%] bg-slate-100 rounded-lg p-4">
                    <p class="text-slate-800">Hello! I'm your AI assistant. I can help you with:</p>
                    <ul class="list-disc list-inside mt-2 text-sm text-slate-600">
                        <li>Creating and managing courses</li>
                        <li>Managing events and bookings</li>
                        <li>Finding resources and content</li>
                        <li>General questions about the platform</li>
                    </ul>
                    <p class="text-slate-800 mt-2">What would you like to know?</p>
                </div>
            </div>
        `;
    }

    function addMessageToChat(role, content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = `max-w-[80%] rounded-lg p-4 ${
            role === 'user' 
                ? 'bg-primary text-white' 
                : 'bg-slate-100 text-slate-800'
        }`;
        messageContent.textContent = content;
        
        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;
        
        // Disable form
        sendButton.disabled = true;
        sendButtonText.classList.add('hidden');
        sendButtonLoading.classList.remove('hidden');
        messageInput.disabled = true;
        
        // Add user message to chat
        addMessageToChat('user', message);
        messageInput.value = '';
        if (messageInput.tagName === 'TEXTAREA') {
            messageInput.style.height = 'auto';
        }
        
        // Show loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'flex justify-start';
        loadingDiv.id = 'loadingMessage';
        loadingDiv.innerHTML = `
            <div class="max-w-[80%] bg-slate-100 rounded-lg p-4">
                <p class="text-slate-800">Thinking...</p>
            </div>
        `;
        chatMessages.appendChild(loadingDiv);
        scrollToBottom();
        
        try {
            const response = await fetch('<?= base_url('api/ai/chat') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    message: message,
                    conversation_id: currentConversationId,
                }),
            });
            
            const data = await response.json();
            
            // Remove loading indicator
            loadingDiv.remove();
            
            if (data.success) {
                currentConversationId = data.conversation_id;
                addMessageToChat('assistant', data.message);
                loadConversationHistory();
            } else {
                addMessageToChat('assistant', 'Sorry, I encountered an error: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            loadingDiv.remove();
            addMessageToChat('assistant', 'Sorry, I encountered an error. Please try again.');
            console.error('Error sending message:', error);
        } finally {
            // Re-enable form
            sendButton.disabled = false;
            sendButtonText.classList.remove('hidden');
            sendButtonLoading.classList.add('hidden');
            messageInput.disabled = false;
            messageInput.focus();
        }
    });

    // Actions
    document.getElementById('newConversationBtn')?.addEventListener('click', () => {
        startNewConversation();
    });
    document.getElementById('refreshConversationsBtn')?.addEventListener('click', () => {
        loadConversationHistory();
    });

    async function deleteConversation(conversationId) {
        if (!conversationId) return;

        // Prefer SweetAlert2 (loaded globally in backend footer scripts)
        const hasSwal = typeof Swal !== 'undefined' && typeof Swal.fire === 'function';

        if (hasSwal) {
            const result = await Swal.fire({
                title: 'Archive conversation?',
                text: 'This will remove it from your active list (you can restore later if needed).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, archive',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0ea5e9',
            });

            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Archiving...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });
        } else {
            const ok = confirm('Delete (archive) this conversation?');
            if (!ok) return;
        }

        try {
            const resp = await fetch(`<?= base_url('api/ai/conversations') ?>/${conversationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
            });
            const data = await resp.json();

            if (data.success) {
                if (currentConversationId === conversationId) {
                    startNewConversation();
                }
                loadConversationHistory();

                if (hasSwal) {
                    Swal.fire({
                        title: 'Archived',
                        text: 'Conversation archived successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                }
            } else {
                const msg = data.error || 'Failed to archive conversation';
                if (hasSwal) {
                    Swal.fire({ title: 'Error', text: msg, icon: 'error' });
                } else {
                    alert(msg);
                }
            }
        } catch (err) {
            console.error('Delete conversation failed:', err);
            if (hasSwal) {
                Swal.fire({ title: 'Error', text: 'Failed to archive conversation.', icon: 'error' });
            } else {
                alert('Failed to delete conversation');
            }
        }
    }

    // Expand toggle
    const expandBtn = document.getElementById('expandAssistantBtn');
    expandBtn?.addEventListener('click', () => {
        isExpanded = !isExpanded;
        if (isExpanded) {
            // Move overlay + card to <body> so fixed truly covers viewport
            if (aiAssistantCard && !originalPositions.cardParent) {
                originalPositions.cardParent = aiAssistantCard.parentNode;
                originalPositions.cardNextSibling = aiAssistantCard.nextSibling;
            }
            if (aiAssistantOverlay && !originalPositions.overlayParent) {
                originalPositions.overlayParent = aiAssistantOverlay.parentNode;
                originalPositions.overlayNextSibling = aiAssistantOverlay.nextSibling;
            }

            if (aiAssistantOverlay) {
                document.body.appendChild(aiAssistantOverlay);
                aiAssistantOverlay.classList.remove('hidden');
                aiAssistantOverlay.style.zIndex = '99998';
            }

            if (aiAssistantCard) {
                document.body.appendChild(aiAssistantCard);
                aiAssistantCard.classList.remove('h-[calc(100vh-200px)]');
                aiAssistantCard.classList.add('rounded-none');
                aiAssistantCard.style.position = 'fixed';
                aiAssistantCard.style.top = '0';
                aiAssistantCard.style.left = '0';
                aiAssistantCard.style.width = '100vw';
                aiAssistantCard.style.height = '100vh';
                aiAssistantCard.style.zIndex = '99999';
            }

            // Lock background scroll
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';

            expandBtn.querySelector('span')?.remove?.();
            expandBtn.innerHTML = `<i data-lucide="minimize-2" class="w-4 h-4"></i> Minimize`;
        } else {
            if (aiAssistantOverlay) {
                aiAssistantOverlay.classList.add('hidden');
                aiAssistantOverlay.style.zIndex = '';
            }

            if (aiAssistantCard) {
                aiAssistantCard.classList.add('h-[calc(100vh-200px)]');
                aiAssistantCard.classList.remove('rounded-none');
                aiAssistantCard.style.position = '';
                aiAssistantCard.style.top = '';
                aiAssistantCard.style.left = '';
                aiAssistantCard.style.width = '';
                aiAssistantCard.style.height = '';
                aiAssistantCard.style.zIndex = '';
            }

            // Restore overlay + card to original location (or mount)
            if (aiAssistantMount) {
                // Prefer mounting back near where it was
                aiAssistantMount.after(aiAssistantOverlay);
                aiAssistantMount.after(aiAssistantCard);
            } else if (originalPositions.cardParent) {
                originalPositions.cardParent.insertBefore(aiAssistantCard, originalPositions.cardNextSibling);
                originalPositions.overlayParent.insertBefore(aiAssistantOverlay, originalPositions.overlayNextSibling);
            }

            // Unlock scroll
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';

            expandBtn.innerHTML = `<i data-lucide="maximize-2" class="w-4 h-4"></i> Expand`;
        }
        initIcons();
        setTimeout(() => scrollToBottom(), 50);
    });

    aiAssistantOverlay?.addEventListener('click', () => {
        if (!isExpanded) return;
        expandBtn?.click();
    });

    // Auto-resize textarea
    if (messageInput && messageInput.tagName === 'TEXTAREA') {
        messageInput.addEventListener('input', () => {
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
        });
    }

    // Auto-focus input on load
    messageInput.focus();
</script>
<?= $this->endSection() ?>
