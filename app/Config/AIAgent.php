<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AIAgent extends BaseConfig
{
    /**
     * OpenAI API Key
     * Set in .env file as OPENAI_API_KEY
     */
    public string $apiKey = '';

    /**
     * Default OpenAI model to use
     * Options: gpt-3.5-turbo, gpt-4, gpt-4-turbo-preview
     * Set in .env file as OPENAI_MODEL
     */
    public string $defaultModel = 'gpt-3.5-turbo';

    /**
     * Maximum tokens per response
     */
    public int $maxTokens = 1000;

    /**
     * Temperature setting (0.0 to 2.0)
     * Higher = more creative, Lower = more focused
     */
    public float $temperature = 0.7;

    /**
     * Maximum context window size (in tokens)
     */
    public int $contextWindowSize = 4000;

    /**
     * Rate limiting per user (requests per minute)
     */
    public int $rateLimitPerUser = 30;

    /**
     * Rate limiting per IP (requests per minute)
     */
    public int $rateLimitPerIP = 60;

    /**
     * Maximum conversation history messages to include in context
     */
    public int $maxHistoryMessages = 10;

    /**
     * Daily question limit for customers (per identity)
     * Set in .env as AI_DAILY_LIMIT_CUSTOMER
     */
    public int $dailyLimitCustomer = 5;

    /**
     * Daily question limit for admins (0 = unlimited)
     * Set in .env as AI_DAILY_LIMIT_ADMIN
     */
    public int $dailyLimitAdmin = 0;

    /**
     * Optional OpenAI Assistant ID (Assistants API)
     * Set in .env as OPENAI_ASSISTANT_ID (or via admin settings)
     */
    public string $assistantId = '';

    /**
     * Enable/disable AI agent
     */
    public bool $enabled = true;

    /**
     * System prompts for different user types
     */
    public array $systemPrompts = [
        'customer' => 'You are a helpful assistant for KEWASNET, a water management platform in Kenya. ONLY answer questions related to KEWASNET, including courses, events, resources, FAQs, account inquiries, platform navigation, and KEWASNET services. If asked about topics outside KEWASNET, politely redirect users to KEWASNET-related topics or suggest they contact support for other inquiries. Be friendly, professional, and concise. Do not provide information about topics unrelated to KEWASNET or water management in Kenya.',
        'admin' => 'You are an administrative assistant for KEWASNET. Help system users with content management, course creation, event management, and other administrative tasks related to the KEWASNET platform. You have access to application data to provide contextual assistance. Focus on KEWASNET platform operations and administration. If asked about topics outside KEWASNET administration, politely redirect to relevant platform features or suggest appropriate resources.',
    ];

    public function __construct()
    {
        parent::__construct();

        // Load from environment variables
        $apiKey = env('OPENAI_API_KEY', '');
        // Remove quotes if present (sometimes .env files have quotes)
        $this->apiKey = trim($apiKey, " \t\n\r\0\x0B'\"");
        $this->defaultModel = env('OPENAI_MODEL', 'gpt-3.5-turbo');
        $this->assistantId = trim((string) env('OPENAI_ASSISTANT_ID', ''), " \t\n\r\0\x0B'\"");
        $enabledEnv = env('AI_AGENT_ENABLED', 'true');
        $this->enabled = ($enabledEnv === 'true' || $enabledEnv === true || $enabledEnv === '1' || $enabledEnv === 1);

        // Daily limits from env
        $this->dailyLimitCustomer = (int) env('AI_DAILY_LIMIT_CUSTOMER', (string) $this->dailyLimitCustomer);
        $this->dailyLimitAdmin = (int) env('AI_DAILY_LIMIT_ADMIN', (string) $this->dailyLimitAdmin);
        
        // Load additional settings from database if available
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('ai_agent_settings');
            $settings = $builder->get()->getResultArray();
            
            foreach ($settings as $setting) {
                $key = $setting['setting_key'];
                $value = $setting['setting_value'];
                
                // Handle JSON values
                if (in_array($key, ['systemPrompts'])) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $this->$key = array_merge($this->$key ?? [], $decoded);
                    }
                } elseif (property_exists($this, $key)) {
                    // Type casting based on current value
                    $currentValue = $this->$key;
                    if (is_int($currentValue)) {
                        $this->$key = (int) $value;
                    } elseif (is_float($currentValue)) {
                        $this->$key = (float) $value;
                    } elseif (is_bool($currentValue)) {
                        $this->$key = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $this->$key = $value;
                    }
                }
            }
        } catch (\Exception $e) {
            // Settings table might not exist yet, ignore
            log_message('debug', 'AI Agent settings not loaded: ' . $e->getMessage());
        }
    }
}
