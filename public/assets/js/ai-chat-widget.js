/**
 * AI Chat Widget
 * Provides a floating chat widget for customers to interact with the AI assistant
 */

class AIChatWidget {
    constructor() {
        this.currentConversationId = null;
        this.isOpen = false;
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        const toggleButton = document.getElementById('chatToggleButton');
        const closeButton = document.getElementById('closeChatButton');
        const chatWindow = document.getElementById('chatWindow');
        const chatForm = document.getElementById('widgetChatForm');
        const messageInput = document.getElementById('widgetMessageInput');
        const sendButton = document.getElementById('widgetSendButton');
        const sendButtonText = document.getElementById('widgetSendButtonText');
        const sendButtonLoading = document.getElementById('widgetSendButtonLoading');

        if (!toggleButton || !chatWindow || !chatForm) {
            return; // Widget not found
        }

        // Show promo image (slides in/out over the floating button)
        this.showPromo();

        // Toggle chat window
        toggleButton.addEventListener('click', () => {
            this.toggleChat();
        });

        closeButton?.addEventListener('click', () => {
            this.closeChat();
        });

        // Auto-resize textarea
        if (messageInput && messageInput.tagName === 'TEXTAREA') {
            messageInput.addEventListener('input', () => {
                messageInput.style.height = 'auto';
                messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            });
        }

        // Handle form submission
        chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage(messageInput.value.trim());
            messageInput.value = '';
            if (messageInput.tagName === 'TEXTAREA') {
                messageInput.style.height = 'auto';
            }
        });

        // Load conversation history when opening
        toggleButton.addEventListener('click', () => {
            if (!this.isOpen) {
                this.loadConversationHistory();
                // Animate welcome message if it exists
                setTimeout(() => {
                    const welcomeMessage = document.querySelector('#widgetChatMessages > div:first-child');
                    if (welcomeMessage && typeof gsap !== 'undefined') {
                        gsap.fromTo(welcomeMessage,
                            { opacity: 0, y: 10 },
                            { opacity: 1, y: 0, duration: 0.4, ease: "back.out(1.2)" }
                        );
                    }
                }, 100);
            }
        });
    }

    showPromo() {
        const promo = document.getElementById('aiAssistantPromo');
        if (!promo) return;

        // Prevent double-run in case setup is called twice
        if (promo.dataset.promoRan === '1') return;
        promo.dataset.promoRan = '1';

        const showDelayMs = 250;
        const visibleMs = 5000; // "a few seconds"

        const show = () => {
            const runCssFallback = () => {
                promo.classList.remove('ai-promo-hidden');
                promo.classList.add('ai-promo-visible');
                window.setTimeout(() => {
                    promo.classList.remove('ai-promo-visible');
                    promo.classList.add('ai-promo-hidden');
                }, visibleMs);
            };

            // Prefer GSAP if available for smoother xPercent animation
            if (typeof gsap !== 'undefined') {
                try {
                    gsap.killTweensOf(promo);
                    // End position should be slightly right-shifted so image is fully visible
                    gsap.set(promo, { opacity: 0, xPercent: -120, x: 0 });
                    gsap.to(promo, {
                        opacity: 1,
                        xPercent: 0,
                        x: 8,
                        duration: 0.6,
                        ease: 'power3.out',
                        onComplete: () => {
                            gsap.to(promo, {
                                opacity: 0,
                                xPercent: -120,
                                x: 0,
                                duration: 0.55,
                                delay: visibleMs / 1000,
                                ease: 'power3.in',
                            });
                        },
                    });
                    return;
                } catch (e) {
                    // If GSAP exists but isn't ready/compatible, fall back to CSS animation
                    console.warn('AI promo GSAP animation failed, falling back to CSS.', e);
                    runCssFallback();
                    return;
                }
            }

            runCssFallback();
        };

        window.setTimeout(show, showDelayMs);
    }

    toggleChat() {
        const chatWindow = document.getElementById('chatWindow');
        if (!chatWindow) return;

        this.isOpen = !this.isOpen;

        if (this.isOpen) {
            chatWindow.classList.remove('hidden');
            // Animate chat window opening with GSAP
            if (typeof gsap !== 'undefined') {
                gsap.fromTo(chatWindow,
                    { opacity: 0, y: 20, scale: 0.95 },
                    { opacity: 1, y: 0, scale: 1, duration: 0.3, ease: "back.out(1.2)" }
                );
            }
            const messageInput = document.getElementById('widgetMessageInput');
            if (messageInput) {
                setTimeout(() => messageInput.focus(), 100);
            }

            // Track chat opened event
            this.trackEvent('ai_chat', 'open', 'AI Assistant Chat Opened', null, 'AI Assistant');
        } else {
            // Animate chat window closing with GSAP
            if (typeof gsap !== 'undefined') {
                gsap.to(chatWindow, {
                    opacity: 0,
                    y: 20,
                    scale: 0.95,
                    duration: 0.2,
                    ease: "power2.in",
                    onComplete: () => {
                        chatWindow.classList.add('hidden');
                    }
                });
            } else {
                chatWindow.classList.add('hidden');
            }

            // Track chat closed event
            this.trackEvent('ai_chat', 'close', 'AI Assistant Chat Closed', null, 'AI Assistant');
        }
    }

    closeChat() {
        const chatWindow = document.getElementById('chatWindow');
        if (chatWindow) {
            chatWindow.classList.add('hidden');
            this.isOpen = false;

            // Track chat closed event
            this.trackEvent('ai_chat', 'close', 'AI Assistant Chat Closed', null, 'AI Assistant');
        }
    }

    async loadConversationHistory() {
        try {
            const response = await fetch(`${window.location.origin}/api/ai/conversations?user_type=customer&status=active`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (data.success && data.conversations && data.conversations.length > 0) {
                const latestConversation = data.conversations[0];
                this.currentConversationId = latestConversation.id;
                this.loadConversation(latestConversation.id);
            }
        } catch (error) {
            console.error('Error loading conversation history:', error);
        }
    }

    async loadConversation(conversationId) {
        try {
            const response = await fetch(`${window.location.origin}/api/ai/conversations/${conversationId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (data.success && data.conversation && data.conversation.messages) {
                const chatMessages = document.getElementById('widgetChatMessages');
                if (!chatMessages) return;

                // Clear existing messages except welcome message
                chatMessages.innerHTML = '';

                // Add all messages with staggered animation
                data.conversation.messages.forEach((msg, index) => {
                    if (msg.role !== 'system') {
                        // Add small delay for staggered effect when loading history
                        setTimeout(() => {
                            this.addMessageToChat(msg.role, msg.content);
                        }, index * 50);
                    }
                });

                // Scroll after all animations
                setTimeout(() => {
                    this.scrollToBottom();
                }, data.conversation.messages.length * 50);
            }
        } catch (error) {
            console.error('Error loading conversation:', error);
        }
    }

    async sendMessage(message) {
        if (!message) return;

        const messageInput = document.getElementById('widgetMessageInput');
        const sendButton = document.getElementById('widgetSendButton');
        const sendButtonText = document.getElementById('widgetSendButtonText');
        const sendButtonLoading = document.getElementById('widgetSendButtonLoading');

        // Disable form
        if (sendButton) {
            sendButton.disabled = true;
        }
        if (sendButtonText) {
            sendButtonText.classList.add('hidden');
        }
        if (sendButtonLoading) {
            sendButtonLoading.classList.remove('hidden');
        }
        if (messageInput) {
            messageInput.disabled = true;
        }

        // Add user message to chat
        this.addMessageToChat('user', message);

        // Track message sent event
        this.trackEvent('ai_chat', 'message_sent', 'AI Assistant Message Sent', message.length, 'AI Assistant');

        // Show animated typing indicator
        const chatMessages = document.getElementById('widgetChatMessages');
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'flex justify-start';
        loadingDiv.id = 'loadingMessage';
        loadingDiv.innerHTML = `
            <div class="max-w-[85%] bg-slate-100 rounded-lg p-3">
                <div class="flex items-center">
                    <span class="sr-only">AI is typing</span>
                    <div class="flex gap-1 ai-typing-dots" role="status" aria-live="polite" aria-label="AI is typing">
                        <span class="typing-dot ai-typing-dot w-2 h-2 bg-primary rounded-full"></span>
                        <span class="typing-dot ai-typing-dot w-2 h-2 bg-primary rounded-full"></span>
                        <span class="typing-dot ai-typing-dot w-2 h-2 bg-primary rounded-full"></span>
                    </div>
                </div>
            </div>
        `;

        let typingAnimation = null;

        if (chatMessages) {
            chatMessages.appendChild(loadingDiv);
            // Animate loading indicator appearance with GSAP
            if (typeof gsap !== 'undefined') {
                gsap.fromTo(loadingDiv,
                    { opacity: 0, y: 10 },
                    { opacity: 1, y: 0, duration: 0.3, ease: "power2.out" }
                );

                // Animate typing dots with staggered bounce
                const dots = loadingDiv.querySelectorAll('.typing-dot');
                if (dots.length > 0) {
                    typingAnimation = gsap.to(dots, {
                        y: -8,
                        duration: 0.4,
                        stagger: 0.15,
                        repeat: -1,
                        yoyo: true,
                        ease: "power2.inOut"
                    });
                }
            }
            this.scrollToBottom();
        }

        // Store animation reference for cleanup
        loadingDiv.typingAnimation = typingAnimation;

        try {
            // Get session ID (create if doesn't exist)
            let sessionId = sessionStorage.getItem('ai_session_id');
            if (!sessionId) {
                sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('ai_session_id', sessionId);
            }

            const response = await fetch(`${window.location.origin}/api/ai/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    message: message,
                    conversation_id: this.currentConversationId,
                    user_type: 'customer',
                }),
            });

            const data = await response.json();

            // Stop typing animation and remove loading indicator
            if (typeof gsap !== 'undefined') {
                // Kill the typing animation if it exists
                if (loadingDiv.typingAnimation) {
                    loadingDiv.typingAnimation.kill();
                }
                gsap.to(loadingDiv, {
                    opacity: 0,
                    y: -10,
                    duration: 0.2,
                    ease: "power2.in",
                    onComplete: () => {
                        loadingDiv.remove();
                    }
                });
            } else {
                loadingDiv.remove();
            }

            if (data.success) {
                this.currentConversationId = data.conversation_id;
                const sourcesUsed = data?.metadata?.sources_used;
                this.addMessageToChat('assistant', data.message, Array.isArray(sourcesUsed) ? sourcesUsed : []);

                // Track successful response received
                this.trackEvent('ai_chat', 'message_received', 'AI Assistant Response Received', data.message.length, 'AI Assistant');
            } else {
                this.addMessageToChat('assistant', 'Sorry, I encountered an error: ' + (data.error || 'Unknown error'));

                // Track error event
                this.trackEvent('ai_chat', 'error', 'AI Assistant Error', null, 'AI Assistant');
            }
        } catch (error) {
            // Stop typing animation and remove loading indicator
            if (typeof gsap !== 'undefined') {
                // Kill the typing animation if it exists
                if (loadingDiv.typingAnimation) {
                    loadingDiv.typingAnimation.kill();
                }
                gsap.to(loadingDiv, {
                    opacity: 0,
                    y: -10,
                    duration: 0.2,
                    ease: "power2.in",
                    onComplete: () => {
                        loadingDiv.remove();
                    }
                });
            } else {
                loadingDiv.remove();
            }
            this.addMessageToChat('assistant', 'Sorry, I encountered an error. Please try again.');
            console.error('Error sending message:', error);

            // Track error event
            this.trackEvent('ai_chat', 'error', 'AI Assistant Network Error', null, 'AI Assistant');
        } finally {
            // Re-enable form
            if (sendButton) {
                sendButton.disabled = false;
            }
            if (sendButtonText) {
                sendButtonText.classList.remove('hidden');
            }
            if (sendButtonLoading) {
                sendButtonLoading.classList.add('hidden');
            }
            if (messageInput) {
                messageInput.disabled = false;
                messageInput.focus();
                // Reset textarea height after clearing
                if (messageInput.tagName === 'TEXTAREA') {
                    messageInput.style.height = 'auto';
                }
            }
        }
    }

    addMessageToChat(role, content, sourcesUsed = []) {
        const chatMessages = document.getElementById('widgetChatMessages');
        if (!chatMessages) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;

        const messageContent = document.createElement('div');
        messageContent.className = `max-w-[85%] rounded-lg p-3 text-sm ${role === 'user'
            ? 'bg-primary text-white'
            : 'bg-slate-100 text-slate-800'
            }`;
        messageContent.textContent = content;

        // Optional citations for assistant messages
        if (role === 'assistant' && Array.isArray(sourcesUsed) && sourcesUsed.length > 0) {
            const sourcesWrap = document.createElement('div');
            sourcesWrap.className = 'mt-2 pt-2 border-t border-slate-200 text-xs text-slate-600';

            const label = document.createElement('div');
            label.className = 'font-medium text-slate-700 mb-1';
            label.textContent = 'Sources';
            sourcesWrap.appendChild(label);

            const list = document.createElement('ul');
            list.className = 'space-y-1';

            sourcesUsed.forEach((s) => {
                const li = document.createElement('li');
                const title = (s && s.title) ? String(s.title) : 'Source';
                const url = s && s.url ? String(s.url) : '';
                const filePath = s && s.file_path ? String(s.file_path) : '';

                if (url) {
                    const a = document.createElement('a');
                    a.href = url;
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                    a.className = 'underline hover:text-primary';
                    a.textContent = title;
                    li.appendChild(a);
                } else if (filePath) {
                    const span = document.createElement('span');
                    span.textContent = `${title} (${filePath})`;
                    li.appendChild(span);
                } else {
                    const span = document.createElement('span');
                    span.textContent = title;
                    li.appendChild(span);
                }

                list.appendChild(li);
            });

            sourcesWrap.appendChild(list);
            messageContent.appendChild(sourcesWrap);
        }

        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);

        // Animate message appearance with GSAP
        if (typeof gsap !== 'undefined') {
            const direction = role === 'user' ? 20 : -20;
            gsap.fromTo(messageDiv,
                {
                    opacity: 0,
                    y: direction,
                    scale: 0.9
                },
                {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 0.4,
                    ease: "back.out(1.2)"
                }
            );
        }

        this.scrollToBottom();
    }

    scrollToBottom() {
        const chatMessages = document.getElementById('widgetChatMessages');
        if (chatMessages) {
            // Smooth scroll with GSAP if available
            if (typeof gsap !== 'undefined') {
                gsap.to(chatMessages, {
                    scrollTop: chatMessages.scrollHeight,
                    duration: 0.3,
                    ease: "power2.out"
                });
            } else {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }
    }

    /**
     * Track event using KEWASNET tracker if available
     */
    trackEvent(eventType, eventAction, eventLabel = null, eventValue = null, eventCategory = null) {
        // Check if tracker is available and has consent
        if (window.kewasnetTracker && window.kewasnetTracker.hasAnalyticsConsent()) {
            window.kewasnetTracker.trackEvent(eventType, eventAction, eventLabel, eventValue, eventCategory);
        } else if (window.trackEvent) {
            // Fallback to global trackEvent function if available
            window.trackEvent(eventType, eventAction, eventLabel, eventValue, eventCategory);
        }
    }
}

// Initialize widget when script loads
if (typeof window !== 'undefined') {
    window.AIChatWidget = new AIChatWidget();
}
