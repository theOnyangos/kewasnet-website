<?php

namespace App\Services;

use App\Models\AIConversationModel;
use App\Models\AIMessageModel;
use App\Config\AIAgent;
use OpenAI\Client as OpenAIClient;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class AIAgentService
{
    protected $config;
    protected $conversationModel;
    protected $messageModel;
    protected $contextService;
    protected $openai;
    protected $rateLimiter;
    protected $db;

    public function __construct()
    {
        $this->config = config('AIAgent');
        $this->conversationModel = new AIConversationModel();
        $this->messageModel = new AIMessageModel();
        $this->contextService = new AIContextService();
        $this->db = \Config\Database::connect();
        
        // Initialize OpenAI client
        if (!empty($this->config->apiKey)) {
            try {
                $this->openai = \OpenAI::client($this->config->apiKey);
            } catch (\Exception $e) {
                log_message('error', 'OpenAI client initialization failed: ' . $e->getMessage());
                $this->openai = null;
            }
        }
        
        // Simple in-memory rate limiter (consider using cache/database for production)
        $this->rateLimiter = [];
    }

    /**
     * Send message to AI agent
     */
    public function sendMessage(string $message, ?string $conversationId = null, ?string $userId = null, string $userType = 'customer', string $sessionId = null, ?string $ipAddress = null): array
    {
        // Check if AI agent is enabled
        if (!$this->config->enabled || empty($this->config->apiKey)) {
            // Log for debugging
            log_message('debug', 'AI Agent check failed - enabled: ' . ($this->config->enabled ? 'true' : 'false') . ', apiKey empty: ' . (empty($this->config->apiKey) ? 'true' : 'false'));
            log_message('debug', 'AI Agent config - enabled type: ' . gettype($this->config->enabled) . ', apiKey length: ' . strlen($this->config->apiKey ?? ''));
            
            return [
                'success' => false,
                'error' => 'AI agent is not enabled or configured',
            ];
        }

        // Ensure required tables exist (helps diagnose missing migrations)
        $dbReady = $this->ensureDatabaseReady();
        if (!$dbReady['success']) {
            return $dbReady;
        }

        // Daily limit (anti-abuse) - enforced on the server
        $dailyLimitResult = $this->enforceDailyLimit($userType, $userId, $sessionId, $ipAddress);
        if (!$dailyLimitResult['success']) {
            return $dailyLimitResult;
        }

        // Check rate limiting
        $rateLimitKey = $userId ? "user_$userId" : "ip_$sessionId";
        if (!$this->checkRateLimit($rateLimitKey)) {
            return [
                'success' => false,
                'error' => 'Rate limit exceeded. Please try again later.',
            ];
        }

        try {
            // Get or create conversation
            $conversation = $this->getOrCreateConversation($userId, $sessionId, $userType, $conversationId);
            
            // Save user message
            $userMessage = $this->saveMessage($conversation['id'], 'user', $message);
            
            // Get conversation history
            $history = $this->messageModel->getConversationMessages($conversation['id'], $this->config->maxHistoryMessages);
            
            // Get relevant context
            $context = $this->getContextForUser($userId, $userType, $message);

            // If we have no usable sources, avoid calling the model (grounded behavior)
            if (!$this->hasGroundingSources($context)) {
                $content = $this->buildNoSourceResponse();
                $responseMeta = [
                    'model' => $this->config->defaultModel,
                    'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
                    'finish_reason' => 'no_sources',
                    'sources_used' => [],
                ];

                $this->saveMessage($conversation['id'], 'assistant', $content, $responseMeta);
                $this->conversationModel->update($conversation['id'], ['updated_at' => date('Y-m-d H:i:s')]);

                return [
                    'success' => true,
                    'conversation_id' => $conversation['id'],
                    'message' => $content,
                    'metadata' => $responseMeta,
                ];
            }
            
            // Build messages array for OpenAI
            $messages = $this->buildMessagesArray($history, $userType, $context);
            
            // Call OpenAI API
            $response = $this->callOpenAI($messages);
            
            if (!$response['success']) {
                return $response;
            }

            // Attach citations metadata for the UI
            $response['metadata']['sources_used'] = $this->extractSourcesUsed($context);
            
            // Save assistant response
            $assistantMessage = $this->saveMessage(
                $conversation['id'],
                'assistant',
                $response['content'],
                $response['metadata']
            );
            
            // Update conversation timestamp
            $this->conversationModel->update($conversation['id'], ['updated_at' => date('Y-m-d H:i:s')]);
            
            return [
                'success' => true,
                'conversation_id' => $conversation['id'],
                'message' => $response['content'],
                'metadata' => $response['metadata'],
            ];
            
        } catch (\Exception $e) {
            $errorId = $this->generateErrorId();
            log_message('error', 'AI Agent Service Error [' . $errorId . ']: ' . $e->getMessage());
            log_message('error', 'AI Agent Service Stack Trace [' . $errorId . ']: ' . $e->getTraceAsString());

            $errorMessage = 'An error occurred while processing your request. Please try again later.';
            // In non-production, include exception message to speed up debugging
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                $errorMessage .= ' (Debug: ' . $e->getMessage() . ')';
            }

            return [
                'success' => false,
                'error' => $errorMessage,
                'error_id' => $errorId,
            ];
        }
    }

    /**
     * Get or create conversation
     */
    public function getOrCreateConversation(?string $userId, ?string $sessionId, string $type, ?string $conversationId = null)
    {
        // If conversation ID provided, try to get it
        if ($conversationId) {
            $conversation = $this->conversationModel->find($conversationId);
            if ($conversation && $conversation['status'] === 'active') {
                // Verify ownership
                if (($userId && $conversation['user_id'] === $userId) || 
                    ($sessionId && $conversation['session_id'] === $sessionId)) {
                    return $conversation;
                }
            }
        }
        
        // Create new conversation
        $data = [
            'user_id' => $userId,
            'session_id' => $sessionId,
            'type' => $type,
            'status' => 'active',
        ];
        
        $conversationId = $this->conversationModel->insert($data);
        
        if (!$conversationId) {
            $details = $this->getModelFailureDetails($this->conversationModel);
            throw new \Exception('Failed to create conversation. ' . $details);
        }
        
        return $this->conversationModel->find($conversationId);
    }

    /**
     * Build system prompt based on user type and context
     */
    protected function buildSystemPrompt(string $userType, array $context = []): string
    {
        $basePrompt = $this->config->systemPrompts[$userType] ?? $this->config->systemPrompts['customer'];

        // Grounding rules: use only provided context/sources
        $basePrompt .= "\n\nGrounding rules:\n"
            . "- Use ONLY the provided context and sources below when answering.\n"
            . "- If the answer cannot be determined from the provided context/sources, say you don't have enough information and suggest the most relevant KEWASNET page (Help Center/Contact Us).\n"
            . "- Do not guess, invent details, or cite sources that are not provided.\n";
        
        if (!empty($context)) {
            $contextText = $this->contextService->formatContextForPrompt($context);
            if (!empty($contextText)) {
                $basePrompt .= "\n\nRelevant context:\n" . $contextText;
            }
        }
        
        return $basePrompt;
    }

    /**
     * Build messages array for OpenAI API
     */
    protected function buildMessagesArray(array $history, string $userType, array $context = []): array
    {
        $messages = [];
        
        // Add system message
        $systemPrompt = $this->buildSystemPrompt($userType, $context);
        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt,
        ];
        
        // Add conversation history
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }
        
        return $messages;
    }

    /**
     * Call OpenAI API
     */
    protected function callOpenAI(array $messages): array
    {
        try {
            if (!$this->openai) {
                return [
                    'success' => false,
                    'error' => 'OpenAI client not initialized',
                ];
            }
            
            $response = $this->openai->chat()->create([
                'model' => $this->config->defaultModel,
                'messages' => $messages,
                'max_tokens' => $this->config->maxTokens,
                'temperature' => $this->config->temperature,
            ]);
            
            $content = $response->choices[0]->message->content ?? '';
            $usage = [
                'prompt_tokens' => $response->usage->promptTokens ?? 0,
                'completion_tokens' => $response->usage->completionTokens ?? 0,
                'total_tokens' => $response->usage->totalTokens ?? 0,
            ];
            
            return [
                'success' => true,
                'content' => $content,
                'metadata' => [
                    'model' => $this->config->defaultModel,
                    'usage' => $usage,
                    'finish_reason' => $response->choices[0]->finishReason ?? 'stop',
                ],
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'OpenAI API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Failed to get AI response: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Save message to database
     */
    public function saveMessage(string $conversationId, string $role, string $content, array $metadata = []): array
    {
        $data = [
            'conversation_id' => $conversationId,
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata,
        ];
        
        $messageId = $this->messageModel->insert($data);
        
        if (!$messageId) {
            $details = $this->getModelFailureDetails($this->messageModel);
            throw new \Exception('Failed to save message. ' . $details);
        }
        
        $message = $this->messageModel->find($messageId);
        return $message;
    }

    /**
     * Ensure AI database tables exist
     */
    protected function ensureDatabaseReady(): array
    {
        try {
            $requiredTables = [
                'ai_conversations',
                'ai_messages',
                'ai_agent_settings',
                'ai_daily_usage',
                'ai_kb_sources',
                'ai_kb_chunks',
            ];
            foreach ($requiredTables as $table) {
                if (!$this->db->tableExists($table)) {
                    return [
                        'success' => false,
                        'error' => 'AI agent database tables are not available. Please run migrations (missing table: ' . $table . ').',
                    ];
                }
            }

            return ['success' => true];
        } catch (\Exception $e) {
            log_message('error', 'AI Agent DB readiness check failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'AI agent database connection failed.',
            ];
        }
    }

    /**
     * Enforce daily message limits per identity (user/session/ip)
     */
    protected function enforceDailyLimit(string $userType, ?string $userId, ?string $sessionId, ?string $ipAddress): array
    {
        $limit = 0;
        if ($userType === 'customer') {
            $limit = (int) ($this->config->dailyLimitCustomer ?? 5);
        } elseif ($userType === 'admin') {
            $limit = (int) ($this->config->dailyLimitAdmin ?? 0);
        }

        // 0 or negative means unlimited
        if ($limit <= 0) {
            return ['success' => true];
        }

        [$identityType, $identity] = $this->resolveDailyIdentity($userId, $sessionId, $ipAddress);
        if (empty($identityType) || empty($identity)) {
            // Fail open if we cannot identify (should be rare)
            return ['success' => true];
        }

        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        try {
            $this->db->transBegin();

            // Lock existing row (if present) to avoid races
            $row = $this->db->query(
                'SELECT message_count FROM ai_daily_usage WHERE usage_date = ? AND user_type = ? AND identity_type = ? AND identity = ? FOR UPDATE',
                [$today, $userType, $identityType, $identity]
            )->getRowArray();

            if ($row) {
                $current = (int) ($row['message_count'] ?? 0);
                if ($current >= $limit) {
                    $this->db->transRollback();
                    return [
                        'success' => false,
                        'error' => 'Daily limit reached. You can ask up to ' . $limit . ' questions per day. Please try again tomorrow.',
                        'code' => 'DAILY_LIMIT_REACHED',
                        'limit' => $limit,
                        'remaining' => 0,
                    ];
                }

                $this->db->query(
                    'UPDATE ai_daily_usage SET message_count = message_count + 1, updated_at = ? WHERE usage_date = ? AND user_type = ? AND identity_type = ? AND identity = ?',
                    [$now, $today, $userType, $identityType, $identity]
                );
                $remaining = max(0, $limit - ($current + 1));
            } else {
                $id = Uuid::uuid4()->toString();
                $this->db->query(
                    'INSERT INTO ai_daily_usage (id, usage_date, user_type, identity_type, identity, message_count, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, ?, ?)',
                    [$id, $today, $userType, $identityType, $identity, $now, $now]
                );
                $remaining = max(0, $limit - 1);
            }

            $this->db->transCommit();

            return [
                'success' => true,
                'daily_limit' => $limit,
                'daily_remaining' => $remaining,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'AI daily limit check failed: ' . $e->getMessage());

            // Fail open if limiter storage fails (avoid blocking legitimate users)
            return ['success' => true];
        }
    }

    /**
     * Prefer user id, then session id, then IP
     *
     * @return array{0:string,1:string}
     */
    protected function resolveDailyIdentity(?string $userId, ?string $sessionId, ?string $ipAddress): array
    {
        if (!empty($userId)) {
            return ['user', $userId];
        }
        if (!empty($sessionId)) {
            return ['session', $sessionId];
        }
        if (!empty($ipAddress)) {
            return ['ip', $ipAddress];
        }
        return ['', ''];
    }

    protected function hasGroundingSources(array $context): bool
    {
        $keys = [
            'kb_sources',
            'courses',
            'events',
            'resources',
            'blog_posts',
            'discussions',
            'programs',
            'pillars',
            'faqs',
            'sitemap',
            // These are always available via initial context injection and are
            // valid grounding for basic help/navigation answers.
            'static_pages',
            'feedback_channels',
            'enrolled_courses',
            'booked_events',
            'statistics',
            'user',
            'admin',
        ];

        foreach ($keys as $k) {
            if (!empty($context[$k])) {
                return true;
            }
        }

        return false;
    }

    protected function buildNoSourceResponse(): string
    {
        return "I don’t have enough information in my KEWASNET sources to answer that confidently.\n\n"
            . "You can try:\n"
            . "- Checking the Help Center\n"
            . "- Using the Contact Us page to reach support\n\n"
            . "If you can share more details (what page/feature you’re referring to), I can try again.";
    }

    protected function extractSourcesUsed(array $context): array
    {
        $sources = [];

        foreach (($context['kb_sources'] ?? []) as $kb) {
            $citation = $kb['citation'] ?? [];
            $key = ($kb['source_id'] ?? '') ?: ($citation['title'] ?? uniqid('kb_', true));
            $sources[$key] = [
                'type' => 'kb',
                'title' => $citation['title'] ?? '',
                'url' => $citation['url'] ?? null,
                'file_path' => $citation['file_path'] ?? null,
                'source_id' => $kb['source_id'] ?? null,
            ];
        }

        return array_values($sources);
    }

    /**
     * Collect useful diagnostics when a model insert fails
     */
    protected function getModelFailureDetails(Model $model): string
    {
        $parts = [];

        $errors = method_exists($model, 'errors') ? $model->errors() : [];
        if (!empty($errors) && is_array($errors)) {
            $parts[] = 'Validation errors: ' . json_encode($errors);
        }

        try {
            $db = $model->db ?? null;
            if ($db) {
                $dbError = $db->error();
                if (!empty($dbError['message'])) {
                    $parts[] = 'DB error: ' . $dbError['message'];
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        return empty($parts) ? 'No additional details.' : implode(' | ', $parts);
    }

    /**
     * Generate an error correlation id for logs
     */
    protected function generateErrorId(): string
    {
        try {
            return bin2hex(random_bytes(6));
        } catch (\Exception $e) {
            return (string) time();
        }
    }

    /**
     * Get context for user
     */
    protected function getContextForUser(?string $userId, string $userType, string $query = ''): array
    {
        $context = [];
        
        if ($userType === 'admin' && $userId) {
            $context = array_merge($context, $this->contextService->getAdminContext($userId, $query));
        } elseif ($userId) {
            $context = array_merge($context, $this->contextService->getUserContext($userId));
        }
        
        // Add content context based on query
        if (!empty($query)) {
            $contentContext = $this->contextService->getContentContext($query, 3);
            $context = array_merge($context, $contentContext);
        }
        
        return $context;
    }

    /**
     * Check rate limiting
     */
    protected function checkRateLimit(string $key): bool
    {
        $limit = strpos($key, 'user_') === 0 ? $this->config->rateLimitPerUser : $this->config->rateLimitPerIP;
        
        if (!isset($this->rateLimiter[$key])) {
            $this->rateLimiter[$key] = ['count' => 1, 'window' => time()];
            return true;
        }
        
        $current = $this->rateLimiter[$key];
        
        // Reset window if minute has passed
        if (time() - $current['window'] >= 60) {
            $this->rateLimiter[$key] = ['count' => 1, 'window' => time()];
            return true;
        }
        
        // Check if limit exceeded
        if ($current['count'] >= $limit) {
            return false;
        }
        
        // Increment count
        $this->rateLimiter[$key]['count']++;
        return true;
    }

    /**
     * Get conversation with messages
     */
    public function getConversationWithMessages(string $conversationId, ?string $userId = null, ?string $sessionId = null): ?array
    {
        $conversation = $this->conversationModel->find($conversationId);
        
        if (!$conversation) {
            return null;
        }
        
        // Verify ownership
        if (($userId && $conversation['user_id'] !== $userId) && 
            ($sessionId && $conversation['session_id'] !== $sessionId)) {
            return null;
        }
        
        $messages = $this->messageModel->getConversationMessages($conversationId);
        $conversation['messages'] = $messages;
        
        return $conversation;
    }
}
