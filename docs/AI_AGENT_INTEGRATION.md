# AI Agent Integration Documentation

## Overview

The KEWASNET AI Agent is an intelligent assistant integrated into the platform to help both customers and system administrators. It uses OpenAI's GPT models to provide contextual, helpful responses based on the application's content and user data.

## What the AI Agent Does

The AI agent serves as a virtual assistant that:

1. **Answers User Questions**: Responds to queries about courses, events, resources, programs, FAQs, and other platform content
2. **Provides Contextual Assistance**: Uses application data to give relevant, accurate information
3. **Supports Multiple User Types**: 
   - **Customers**: Help with courses, events, resources, account inquiries, and general platform navigation
   - **Administrators**: Assistance with content management, course creation, event management, and administrative tasks
4. **Maintains Conversation History**: Remembers previous messages in a conversation for coherent, context-aware responses
5. **Searches Application Content**: Automatically searches relevant content (courses, events, FAQs, pillars, etc.) to provide accurate answers
6. **Guides Users**: Directs users to appropriate pages (contact forms, help center, privacy policies, etc.) when needed
7. **Focuses on KEWASNET**: System prompts are configured to primarily answer questions related to KEWASNET and water management in Kenya, redirecting off-topic queries appropriately

## Architecture

### Components

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (Chat Widget)                    │
│  - Customer-facing chat widget (bottom-left)                 │
│  - Admin AI Assistant interface                              │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    API Layer                                  │
│  - AIAgentController (handles HTTP requests)                 │
│  - Authentication & Authorization                             │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Service Layer                              │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │ AIAgentService   │  │ AIContextService │                │
│  │ - OpenAI API     │  │ - Content Search │                │
│  │ - Conversations  │  │ - User Context   │                │
│  │ - Rate Limiting  │  │ - Data Formatting│                │
│  └──────────────────┘  └──────────────────┘                │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Data Layer                                 │
│  - ai_conversations (conversation tracking)                   │
│  - ai_messages (message history)                              │
│  - ai_agent_settings (configuration)                         │
│  - ai_daily_usage (daily quota / anti-abuse)                  │
│  - ai_kb_sources (admin knowledge base sources)               │
│  - ai_kb_chunks (searchable chunks for retrieval)             │
└─────────────────────────────────────────────────────────────┘
```

### Key Files

- **Configuration**: `app/Config/AIAgent.php` - AI agent settings
- **Service**: `app/Services/AIAgentService.php` - Core AI logic
- **Context Service**: `app/Services/AIContextService.php` - Content search and context building
- **API Controller**: `app/Controllers/API/AIAgentController.php` - API endpoints
- **Admin Controller**: `app/Controllers/BackendV2/AIAssistantController.php` - Admin interface
- **Models**: 
  - `app/Models/AIConversationModel.php`
  - `app/Models/AIMessageModel.php`
  - `app/Models/AIAgentSettingsModel.php`
- **Frontend Widget**: `app/Views/components/ai-chat-widget.php`
- **JavaScript**: `public/assets/js/ai-chat-widget.js`

## Database Structure

### ai_conversations Table

Stores conversation sessions between users and the AI agent.

```sql
- id (VARCHAR 36) - UUID primary key
- user_id (VARCHAR 36) - Optional user ID if authenticated
- session_id (VARCHAR 255) - Session identifier for anonymous users
- type (ENUM) - 'customer' or 'admin'
- status (ENUM) - 'active' or 'archived'
- created_at (DATETIME)
- updated_at (DATETIME)
```

### ai_messages Table

Stores individual messages within conversations.

```sql
- id (VARCHAR 36) - UUID primary key
- conversation_id (VARCHAR 36) - Foreign key to ai_conversations
- role (ENUM) - 'user', 'assistant', or 'system'
- content (TEXT) - Message content
- metadata (JSON) - Additional metadata (tokens used, model, etc.)
- created_at (DATETIME)
```

### ai_agent_settings Table

Stores configurable AI agent settings (optional, can override config file).

```sql
- id (VARCHAR 36) - UUID primary key
- setting_key (VARCHAR 100) - Setting name
- setting_value (TEXT) - Setting value (can be JSON)
- description (TEXT) - Setting description
- created_at (DATETIME)
- updated_at (DATETIME)
```

### ai_daily_usage Table

Tracks **daily usage per identity** to enforce anti-abuse quotas (e.g., **5 customer questions/day**).

**Identity selection (in order):** `user_id` → `session_id` → `ip`

```sql
- id (VARCHAR 36) - UUID primary key
- usage_date (DATE) - Date bucket (server date)
- user_type (ENUM) - 'customer' or 'admin'
- identity_type (ENUM) - 'user', 'session', or 'ip'
- identity (VARCHAR 255) - Identity value (user_id/session_id/ip)
- message_count (INT) - Messages sent for that day bucket
- created_at (DATETIME)
- updated_at (DATETIME)
```

### ai_kb_sources Table

Stores admin-managed knowledge base sources that the AI can reference.

```sql
- id (CHAR 36) - UUID primary key
- type (ENUM) - 'text', 'url', 'file'
- title (VARCHAR 255)
- source_url (TEXT, nullable)
- file_path (TEXT, nullable)
- content_raw (LONGTEXT, nullable) - raw/extracted content used for chunking
- status (ENUM) - 'active' or 'disabled'
- created_by (CHAR 36, nullable) - admin user id
- last_ingested_at (DATETIME, nullable)
- ingest_error (TEXT, nullable)
- created_at (DATETIME)
- updated_at (DATETIME)
```

### ai_kb_chunks Table

Stores ingested chunks from `ai_kb_sources` for retrieval.

```sql
- id (CHAR 36) - UUID primary key
- source_id (CHAR 36) - FK to ai_kb_sources.id
- chunk_index (INT)
- content (LONGTEXT)
- metadata (JSON, nullable) - title/url/file reference, etc.
- created_at (DATETIME)
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# OpenAI API Configuration
OPENAI_API_KEY=sk-proj-your-api-key-here
OPENAI_MODEL=gpt-3.5-turbo
AI_AGENT_ENABLED=true

# Anti-abuse daily limits
# Customer limit defaults to 5/day. Admin default is unlimited (0).
AI_DAILY_LIMIT_CUSTOMER=5
AI_DAILY_LIMIT_ADMIN=0
```

### Configuration File

The main configuration is in `app/Config/AIAgent.php`:

- **apiKey**: OpenAI API key (from environment)
- **defaultModel**: Model to use (gpt-3.5-turbo, gpt-4, etc.)
- **maxTokens**: Maximum response length (default: 1000)
- **temperature**: Creativity level 0.0-2.0 (default: 0.7)
- **rateLimitPerUser**: Requests per minute per user (default: 30)
- **rateLimitPerIP**: Requests per minute per IP (default: 60)
- **maxHistoryMessages**: Conversation history to include (default: 10)
- **dailyLimitCustomer**: Customer daily message limit (default: 5)
- **dailyLimitAdmin**: Admin daily message limit (0 = unlimited)
- **enabled**: Master switch to enable/disable AI agent

## API Endpoints

### POST /api/ai/chat

Send a message to the AI agent.

**Request:**
```json
{
  "message": "What courses are available?",
  "conversation_id": null,
  "user_type": "customer"
}
```

**Response:**
```json
{
  "success": true,
  "conversation_id": "uuid-here",
  "message": "AI response text",
  "metadata": {
    "tokens_used": 150,
    "model": "gpt-3.5-turbo"
  }
}
```

**Daily Limit (Anti-abuse)**:

- Customers are limited to **5 messages/day** (configurable)
- When exceeded, the API returns **HTTP 429**

**429 Response:**
```json
{
  "success": false,
  "error": "Daily limit reached. You can ask up to 5 questions per day. Please try again tomorrow."
}
```

**Grounded Answers + Sources**

When the assistant answers using **Knowledge Base** sources, the API includes citations in `metadata.sources_used`:

```json
{
  "success": true,
  "message": "…",
  "metadata": {
    "sources_used": [
      {
        "type": "kb",
        "title": "About KEWASNET",
        "url": "https://kewasnet.org/about",
        "file_path": null,
        "source_id": "uuid"
      }
    ]
  }
}
```

If no suitable sources are found, the assistant returns a safe “not enough information” response and `sources_used` will be empty.

**Authentication**: Endpoints can work for both authenticated users and anonymous sessions (identity is tracked via user/session/ip).

### GET /api/ai/conversations

Get user's conversation history.

**Query Parameters:**
- `user_type`: 'customer' or 'admin'
- `status`: 'active' or 'archived' (default: 'active')

**Response:**
```json
{
  "success": true,
  "conversations": [
    {
      "id": "uuid",
      "created_at": "2026-01-17 10:00:00",
      "message_count": 5
    }
  ]
}
```

### GET /api/ai/conversations/:id

Get a specific conversation with all messages.

**Response:**
```json
{
  "success": true,
  "conversation": {
    "id": "uuid",
    "type": "customer",
    "messages": [
      {
        "role": "user",
        "content": "User message",
        "created_at": "2026-01-17 10:00:00"
      },
      {
        "role": "assistant",
        "content": "AI response",
        "created_at": "2026-01-17 10:00:01"
      }
    ]
  }
}
```

### DELETE /api/ai/conversations/:id

Archive a conversation.

### POST /api/ai/conversations/:id/regenerate

Regenerate the last AI response in a conversation.

## Context Service

The `AIContextService` provides relevant application data to the AI agent for context-aware responses.

### Content Types Searched

1. **Courses**: Title, summary, description
2. **Events**: Title, description, dates, venues
3. **Resources**: Title, description
4. **Blog Posts**: Title, excerpt
5. **Discussions**: Title, content
6. **Programs**: Title, description
7. **Pillars**: Title, description, content
8. **FAQs**: Question, answer, tags
9. **Sitemap**: Title, description, URLs

### Static Page References

- About Us (`/about`)
- Contact Us (`/contact-us`)
- Privacy and Policies (`/privacy-and-policies`)
- Terms of Service (`/terms-of-service`)
- Help Center (`/help-center`)
- Support (`/contact-us`)

### Feedback Channels

- General Feedback
- Suggestions
- Complaints
- Grievances

All feedback channels route to the contact form.

## Frontend Integration

### Customer Chat Widget

A floating chat widget appears in the bottom-left corner of customer-facing pages.

**Features:**
- Animated AI icon with badge
- Expandable textarea input
- GSAP animations for messages
- Typing indicator animation
- Conversation history loading
- Responsive design with max-height scrolling

**Location**: `app/Views/components/ai-chat-widget.php`

**JavaScript**: `public/assets/js/ai-chat-widget.js`

**Included in**: 
- `app/Views/frontendV2/website/layouts/main.php`
- `app/Views/frontendV2/ksp/layouts/main.php`

### Admin AI Assistant

Admin users can access the AI assistant from the sidebar menu.

**Location**: `/auth/ai-assistant`

**Features:**
- Full conversation interface
- Settings management
- Conversation history

## How It Works

### Important: Model Training vs Context Injection

**The AI agent does NOT train or fine-tune the OpenAI model.** Instead, it uses a technique called **context injection**:

- **No Training**: The underlying GPT model (e.g., gpt-3.5-turbo) is pre-trained by OpenAI and remains unchanged
- **Context Injection**: Application-specific information is dynamically injected into each API request via the system prompt
- **Per-Request Context**: Relevant KEWASNET data (courses, events, FAQs, etc.) is searched and included in the prompt for each conversation
- **No Persistent Learning**: The model doesn't "remember" KEWASNET information between requests - it's provided fresh each time

This approach means:
- ✅ No need for expensive fine-tuning or training infrastructure
- ✅ Always uses the latest application data (searches database in real-time)
- ✅ Can update context without retraining
- ⚠️ Each request includes context, which uses tokens and affects API costs

### Message Flow

1. **User sends message** → Frontend widget sends POST to `/api/ai/chat`
2. **Controller validates** → Checks authentication and message content
3. **Service processes**:
   - Gets or creates conversation
   - Saves user message
   - Retrieves conversation history
   - Searches relevant content via `AIContextService` (context injection)
   - Builds system prompt with context
   - Calls OpenAI API with context in the prompt
   - Saves assistant response
4. **Response returned** → JSON response sent to frontend
5. **Frontend displays** → Message animated and displayed in chat

### Context Building

When a user asks a question:

1. **Content Search**: `AIContextService` searches relevant content types from the database
2. **User Context**: If authenticated, retrieves user's enrolled courses, booked events
3. **Context Formatting**: Formats all context into a structured prompt
4. **System Prompt**: Combines base system prompt with context (injected into each request)
5. **Message History**: Includes recent conversation messages for continuity
6. **API Call**: Sends everything to OpenAI API (model uses injected context, not training)

### Example Context

```
User Information:
- Name: John Doe
- Email: john@example.com

User's Enrolled Courses:
- Water Management Basics (Level: beginner)
- Advanced Sanitation (Level: intermediate)

Available Courses:
- Introduction to WASH (Free)
- Water Quality Testing ($50)

Frequently Asked Questions:
- Q: How do I enroll in a course?
  A: You can enroll by clicking the "Enroll" button on any course page...

Available Pages:
- About Us: /about - Learn about KEWASNET...
- Contact Us: /contact-us - Get in touch...
```

## System Prompts

The system prompts are designed to focus the AI on KEWASNET-related topics and redirect off-topic queries.

### Customer Prompt

```
You are a helpful assistant for KEWASNET, a water management platform in Kenya. 
ONLY answer questions related to KEWASNET, including courses, events, resources, 
FAQs, account inquiries, platform navigation, and KEWASNET services. If asked 
about topics outside KEWASNET, politely redirect users to KEWASNET-related topics 
or suggest they contact support for other inquiries. Be friendly, professional, 
and concise. Do not provide information about topics unrelated to KEWASNET or 
water management in Kenya.
```

### Admin Prompt

```
You are an administrative assistant for KEWASNET. Help system users with content 
management, course creation, event management, and other administrative tasks 
related to the KEWASNET platform. You have access to application data to provide 
contextual assistance. Focus on KEWASNET platform operations and administration. 
If asked about topics outside KEWASNET administration, politely redirect to 
relevant platform features or suggest appropriate resources.
```

### Scope Limitations

**Important Note**: While the system prompts guide the AI to focus on KEWASNET topics, the underlying GPT model is a general-purpose AI that has knowledge about many topics. The prompts are instructions, not hard constraints, so:

- ✅ The AI will primarily answer KEWASNET-related questions
- ✅ It will attempt to redirect off-topic queries
- ⚠️ It may still answer general knowledge questions if asked directly
- ⚠️ The model's general knowledge is not disabled

For stricter control, consider:
- Adding validation logic to detect and reject off-topic queries
- Using a more restrictive model or custom fine-tuned model
- Implementing post-processing filters on responses

## Rate Limiting

The AI agent implements rate limiting to prevent abuse:

- **Per User**: 30 requests per minute (authenticated users)
- **Per IP**: 60 requests per minute (anonymous users)

Rate limits are tracked in-memory (consider using cache/database for production scaling).

## Daily Quota (Anti-Abuse)

In addition to per-minute rate limiting, the AI agent enforces a **daily quota** (DB-backed so it works across restarts and multiple servers):

- **Customers**: default **5 messages/day**
- **Admins**: default **unlimited** (`AI_DAILY_LIMIT_ADMIN=0`)

## Knowledge Base (Admin-managed Sources)

Admins can add new sources that the AI can reference:

- **Text**: paste content directly
- **URL**: provide a KEWASNET page URL to fetch and extract text
- **File**: upload PDF/TXT/MD to extract and ingest

### Admin UI

- `auth/ai-assistant/knowledge-base` (list)
- `auth/ai-assistant/knowledge-base/create` (create)
- `auth/ai-assistant/knowledge-base/edit/:id` (edit)
- `auth/ai-assistant/knowledge-base/ingest/:id` (re-ingest)

### Ingestion commands

```bash
# Ingest all active sources
php spark ai:kb:ingest

# Ingest a single source by id
php spark ai:kb:ingest --id=<uuid>
```

Notes:
- URL ingestion requires server outbound internet access.
- PDF ingestion uses the `smalot/pdfparser` dependency.

## Security

1. **Authentication Required**: All API endpoints require user authentication
2. **Input Validation**: Messages are validated before processing
3. **Error Handling**: Comprehensive error handling prevents information leakage
4. **Rate Limiting**: Prevents abuse and excessive API usage
5. **Context Filtering**: Only relevant, safe content is included in context

## Setup Instructions

### 1. Install Dependencies

```bash
composer require openai-php/client
composer require smalot/pdfparser
```

### 2. Run Migration

```bash
php spark migrate
```

This creates the `ai_conversations`, `ai_messages`, `ai_agent_settings`, `ai_daily_usage`, `ai_kb_sources`, and `ai_kb_chunks` tables.

### 3. Configure Environment

Add to `.env`:
```env
OPENAI_API_KEY=your-api-key-here
OPENAI_MODEL=gpt-3.5-turbo
AI_AGENT_ENABLED=true
```

### 4. Enable AI Agent

The agent is enabled by default if `AI_AGENT_ENABLED=true` in `.env`.

### 5. Test the Integration

1. Visit any customer-facing page
2. Click the AI chat widget (bottom-left)
3. Send a test message
4. Verify response

## Usage Examples

### Customer Queries

**Example 1: Course Inquiry**
```
User: "What courses do you offer?"
AI: Searches courses → Lists available courses with descriptions
```

**Example 2: Event Information**
```
User: "When is the next event?"
AI: Searches events → Provides upcoming event details
```

**Example 3: FAQ**
```
User: "How do I reset my password?"
AI: Searches FAQs → Provides answer from FAQ database
```

**Example 4: Navigation**
```
User: "Where can I find your privacy policy?"
AI: References static pages → Directs to /privacy-and-policies
```

### Admin Queries

**Example 1: Content Management**
```
Admin: "How many courses are published?"
AI: Accesses admin context → Provides statistics
```

**Example 2: Help with Features**
```
Admin: "How do I create a new course?"
AI: Provides guidance on course creation process
```

## Technical Details

### How Context Injection Works

The AI agent uses **context injection** rather than model training:

1. **User Query Received**: When a user asks a question
2. **Content Search**: `AIContextService` searches the database for relevant content
3. **Context Assembly**: Relevant data is formatted into a text prompt
4. **System Prompt Building**: Base system prompt + context data = complete prompt
5. **API Request**: The complete prompt is sent to OpenAI API
6. **Model Processing**: GPT model processes the prompt with injected context
7. **Response Generated**: Model generates response based on injected context + its training

**Key Point**: The model itself is never modified. Context is provided fresh for each request.

### Scope and Limitations

**What the AI Can Do:**
- Answer questions about KEWASNET content (courses, events, FAQs, etc.)
- Provide information from the application database
- Guide users to relevant pages and resources
- Help with account and platform navigation
- Assist admins with platform management tasks

**What the AI Cannot Do (by design):**
- The model is not trained specifically on KEWASNET data
- It doesn't have persistent memory of KEWASNET information (relies on context injection)
- It may answer general knowledge questions outside KEWASNET scope (though prompts try to limit this)

**Scope Control:**
- System prompts instruct the AI to focus on KEWASNET topics
- Prompts include instructions to redirect off-topic queries
- However, as a general-purpose model, it may still answer non-KEWASNET questions if asked directly

## Troubleshooting

### AI Agent Not Responding

1. Check `AI_AGENT_ENABLED` in `.env` is set to `true`
2. Verify `OPENAI_API_KEY` is valid
3. Check application logs for errors
4. Verify database tables exist (run migrations)

### Rate Limit Errors

- Reduce request frequency
- Check rate limit settings in `app/Config/AIAgent.php`
- Consider increasing limits for production

### Daily Limit Errors (HTTP 429)

- Customer daily quota reached (default: 5/day)
- Adjust via `.env`:
  - `AI_DAILY_LIMIT_CUSTOMER=5`
  - `AI_DAILY_LIMIT_ADMIN=0`
- If you want quotas to apply only to authenticated customers (not session/ip), adjust identity resolution in `AIAgentService`.

### Knowledge Base Ingestion Issues

- **URL ingestion failing**: confirm the server can reach the URL and the page is publicly accessible.
- **PDF ingestion failing**: ensure `smalot/pdfparser` is installed and the PDF contains extractable text.
- **Not retrieving sources**: confirm the source is `active` and has been ingested (check `last_ingested_at` and `ingest_error`).

### Context Not Working

- Verify models are properly initialized
- Check database tables have data
- Review `AIContextService` logs for search errors
- Verify content search is finding relevant results

### AI Answering Off-Topic Questions

- Review and strengthen system prompts in `app/Config/AIAgent.php`
- Consider adding validation logic to detect off-topic queries
- Monitor conversations and adjust prompts based on behavior
- Consider implementing response filtering

### Frontend Widget Not Appearing

- Check widget is included in layout files
- Verify JavaScript file is loaded
- Check browser console for errors
- Ensure GSAP library is loaded (for animations)

## Best Practices

1. **Monitor API Usage**: Track OpenAI API usage and costs
2. **Review Conversations**: Periodically review conversations for quality
3. **Update System Prompts**: Refine prompts based on user feedback
4. **Content Maintenance**: Keep FAQs and content up-to-date for better responses
5. **Rate Limiting**: Adjust rate limits based on usage patterns
6. **Error Handling**: Monitor logs for errors and edge cases

## Future Enhancements

Potential improvements:

- Conversation export functionality
- Analytics dashboard for AI usage
- Custom training on KEWASNET-specific content
- Multi-language support
- Voice input/output
- Integration with help desk system
- Sentiment analysis
- Conversation quality scoring

## Support

For issues or questions about the AI agent integration:

1. Check application logs: `writable/logs/`
2. Review this documentation
3. Contact the development team

## License

This AI agent integration is part of the KEWASNET platform.
