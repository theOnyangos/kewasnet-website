<!-- AI Chat Widget -->
<div id="aiChatWidget" class="fixed bottom-4 left-4 z-50 focus:outline-none" style="position: fixed !important; bottom: 1rem !important; left: 1rem !important; right: auto !important; top: auto !important;">
    <style>
        /* AI typing indicator: animated 3 dots (CSS fallback if GSAP isn't available) */
        @keyframes aiTypingDot {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
            40% { transform: translateY(-6px); opacity: 1; }
        }
        .ai-typing-dot { animation: aiTypingDot 1s infinite ease-in-out; }
        .ai-typing-dot:nth-child(2) { animation-delay: 0.15s; }
        .ai-typing-dot:nth-child(3) { animation-delay: 0.3s; }

        /* AI promo image (slides in/out over the floating button) */
        #aiAssistantPromo {
            will-change: transform, opacity;
            pointer-events: none;
        }
        #aiAssistantPromo.ai-promo-hidden {
            opacity: 0;
            transform: translateX(-120%);
        }
        #aiAssistantPromo.ai-promo-visible {
            opacity: 1;
            /* slightly more to the left (still visible on left edge) */
            transform: translateX(8px);
        }

        /* Ensure visible shadow even without Tailwind build */
        #aiAssistantPromo img {
            filter: drop-shadow(0 14px 22px rgba(0, 0, 0, 0.28));
        }
    </style>

    <!-- Promo image (shown briefly on page load) -->
    <div
        id="aiAssistantPromo"
        class="ai-promo-hidden absolute bottom-10 left-0 transition-all duration-500 ease-out"
        style="z-index: 9999;"
        aria-hidden="true"
    >
        <img
            src="<?= base_url('images/ai-assistant.png') ?>"
            alt=""
            class="block h-auto drop-shadow-xl select-none"
            style="width: 200px; max-width: calc(100vw - 2rem);"
            loading="lazy"
            decoding="async"
        />
    </div>

    <!-- Chat Toggle Button -->
    <button 
        id="chatToggleButton" 
        class="w-14 h-14 bg-primary text-white rounded-full shadow-lg hover:bg-primaryShades-600 transition-all duration-300 flex items-center justify-center relative"
        aria-label="Open AI Chat"
    >
        <i data-lucide="sparkles" class="w-6 h-6"></i>
        <span class="absolute -top-1 -right-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-md">AI</span>
    </button>

    <!-- Chat Window -->
    <div 
        id="chatWindow" 
        class="hidden fixed bottom-20 left-4 w-96 max-h-[80vh] bg-white rounded-lg shadow-2xl flex flex-col"
        style="position: fixed !important; bottom: 5rem !important; left: 1rem !important; right: auto !important; top: auto !important; max-height: 80vh;"
    >
        <!-- Chat Header -->
        <div class="bg-primary text-white p-4 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="sparkles" class="w-5 h-5 text-white"></i>
                <div>
                    <h3 class="font-semibold">KEWASNET AI Assistant</h3>
                    <p class="text-xs text-white/80">Ask me anything</p>
                </div>
            </div>
            <button 
                id="closeChatButton" 
                class="text-white hover:text-white/80 transition-colors"
                aria-label="Close Chat"
            >
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div id="widgetChatMessages" class="flex-1 overflow-y-auto p-4 space-y-3" style="max-height: calc(80vh - 180px);">
            <div class="flex justify-start">
                <div class="max-w-[85%] bg-slate-100 rounded-lg p-3">
                    <p class="text-slate-800 text-sm">Hello! I'm your AI assistant. How can I help you today?</p>
                </div>
            </div>
        </div>

        <!-- Chat Input Area -->
        <div class="border-t border-slate-200 p-4">
            <form id="widgetChatForm" class="flex gap-2 items-end">
                <textarea 
                    id="widgetMessageInput" 
                    placeholder="Type your message..." 
                    rows="1"
                    class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:none outline-none focus:ring-0 resize-none overflow-hidden"
                    style="outline: none !important; border: 1px solid #cbd5e1;"
                    required
                ></textarea>
                <button 
                    type="submit" 
                    id="widgetSendButton"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primaryShades-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed h-fit flex items-center justify-center"
                    aria-label="Send message"
                >
                    <span id="widgetSendButtonText" class="flex items-center justify-center">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </span>
                    <span id="widgetSendButtonLoading" class="hidden flex items-center justify-center">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    // Initialize icons when DOM is ready
    function initIcons() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIcons);
    } else {
        initIcons();
    }
    
    // Re-initialize icons when chat window opens (in case icons weren't loaded initially)
    const chatToggleButton = document.getElementById('chatToggleButton');
    if (chatToggleButton) {
        chatToggleButton.addEventListener('click', () => {
            setTimeout(initIcons, 100);
        });
    }
})();
</script>
