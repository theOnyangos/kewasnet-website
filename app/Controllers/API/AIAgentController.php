<?php

namespace App\Controllers\API;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Services\AIAgentService;
use App\Libraries\CIAuth;
use App\Libraries\ClientAuth;

class AIAgentController extends BaseController
{
    use ResponseTrait;

    protected $aiAgentService;

    public function __construct()
    {
        $this->aiAgentService = new AIAgentService();
    }

    /**
     * Send message to AI agent
     * POST /api/ai/chat
     */
    public function chat()
    {
        try {
            $input = $this->request->getJSON(true);
            
            $message = $input['message'] ?? '';
            $conversationId = $input['conversation_id'] ?? null;
            
            if (empty($message)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Message is required',
                ])->setStatusCode(400);
            }
            
            // Get user info
            $userId = null;
            // Ensure CI session is started, then use native session_id()
            $sessionId = session_id();
            if (empty($sessionId)) {
                session();
                $sessionId = session_id();
            }
            
            // Determine user type server-side (do not trust client-provided user_type)
            $userType = 'customer';
            if (CIAuth::isLoggedIn() && CIAuth::isAdmin()) {
                $userType = 'admin';
                $userId = session()->get('id');
            } elseif (ClientAuth::isLoggedIn()) {
                $userType = 'customer';
                $userId = session()->get('id');
            }
            
            // Send message
            $ipAddress = $this->request->getIPAddress();
            $result = $this->aiAgentService->sendMessage($message, $conversationId, $userId, $userType, $sessionId, $ipAddress);
            
            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'conversation_id' => $result['conversation_id'],
                    'message' => $result['message'],
                    'metadata' => $result['metadata'] ?? [],
                ])->setStatusCode(200);
            } else {
                $statusCode = 400;
                if (($result['code'] ?? null) === 'DAILY_LIMIT_REACHED') {
                    $statusCode = 429;
                }
                return $this->response->setJSON([
                    'success' => false,
                    'error' => $result['error'] ?? 'Failed to process message',
                ])->setStatusCode($statusCode);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'AI Agent chat error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'An error occurred while processing your message',
            ])->setStatusCode(500);
        }
    }

    /**
     * Get user's conversations
     * GET /api/ai/conversations
     */
    public function getConversations()
    {
        try {
            $status = $this->request->getGet('status') ?? 'active';
            
            // Get user info
            $userId = null;
            $sessionId = session_id();
            if (empty($sessionId)) {
                session();
                $sessionId = session_id();
            }
            
            // Determine user type server-side (do not trust query param user_type)
            $userType = 'customer';
            if (CIAuth::isLoggedIn() && CIAuth::isAdmin()) {
                $userType = 'admin';
                $userId = session()->get('id');
            } elseif (ClientAuth::isLoggedIn()) {
                $userType = 'customer';
                $userId = session()->get('id');
            }
            
            $conversationModel = new \App\Models\AIConversationModel();
            
            if ($userId) {
                $conversations = $conversationModel->getUserConversations($userId, $userType, $status);
            } else {
                $conversations = $conversationModel->getSessionConversations($sessionId, $userType, $status);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'conversations' => $conversations,
            ])->setStatusCode(200);
            
        } catch (\Exception $e) {
            log_message('error', 'AI Agent getConversations error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'An error occurred while fetching conversations',
            ])->setStatusCode(500);
        }
    }

    /**
     * Get specific conversation with messages
     * GET /api/ai/conversations/:id
     */
    public function getConversation($conversationId = null)
    {
        try {
            // If conversationId not passed as parameter, extract from URI
            if (empty($conversationId)) {
                $segments = $this->request->getUri()->getSegments();
                // Find the conversation ID in the segments (should be after 'conversations')
                $conversationsIndex = array_search('conversations', $segments);
                if ($conversationsIndex !== false && isset($segments[$conversationsIndex + 1])) {
                    $conversationId = $segments[$conversationsIndex + 1];
                }
            }
            
            if (empty($conversationId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Conversation ID is required',
                ])->setStatusCode(400);
            }
            
            // Get user info
            $userId = null;
            $sessionId = session_id();
            if (empty($sessionId)) {
                session();
                $sessionId = session_id();
            }
            
            // Determine user type server-side
            $userType = 'customer';
            if (CIAuth::isLoggedIn() && CIAuth::isAdmin()) {
                $userType = 'admin';
                $userId = session()->get('id');
            } elseif (ClientAuth::isLoggedIn()) {
                $userType = 'customer';
                $userId = session()->get('id');
            }
            
            $conversation = $this->aiAgentService->getConversationWithMessages($conversationId, $userId, $sessionId);
            
            if (!$conversation) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Conversation not found or access denied',
                ])->setStatusCode(404);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'conversation' => $conversation,
            ])->setStatusCode(200);
            
        } catch (\Exception $e) {
            log_message('error', 'AI Agent getConversation error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'An error occurred while fetching conversation',
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete/archive conversation
     * DELETE /api/ai/conversations/:id
     */
    public function deleteConversation($conversationId = null)
    {
        try {
            // If conversationId not passed as parameter, extract from URI
            if (empty($conversationId)) {
                $segments = $this->request->getUri()->getSegments();
                // Find the conversation ID in the segments (should be after 'conversations')
                $conversationsIndex = array_search('conversations', $segments);
                if ($conversationsIndex !== false && isset($segments[$conversationsIndex + 1])) {
                    $conversationId = $segments[$conversationsIndex + 1];
                }
            }
            
            if (empty($conversationId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Conversation ID is required',
                ])->setStatusCode(400);
            }
            
            // Get user info
            $userId = null;
            $sessionId = session_id();
            if (empty($sessionId)) {
                session();
                $sessionId = session_id();
            }
            
            // Determine user type server-side
            if (CIAuth::isLoggedIn() && CIAuth::isAdmin()) {
                $userId = session()->get('id');
            } elseif (ClientAuth::isLoggedIn()) {
                $userId = session()->get('id');
            }
            
            $conversationModel = new \App\Models\AIConversationModel();
            $conversation = $conversationModel->find($conversationId);
            
            if (!$conversation) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Conversation not found',
                ])->setStatusCode(404);
            }
            
            // Verify ownership
            if (($userId && $conversation['user_id'] !== $userId) && 
                ($sessionId && $conversation['session_id'] !== $sessionId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Access denied',
                ])->setStatusCode(403);
            }
            
            // Archive conversation
            $result = $conversationModel->archiveConversation($conversationId);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Conversation archived successfully',
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Failed to archive conversation',
                ])->setStatusCode(400);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'AI Agent deleteConversation error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'An error occurred while archiving conversation',
            ])->setStatusCode(500);
        }
    }

    /**
     * Regenerate last response
     * POST /api/ai/conversations/:id/regenerate
     */
    public function regenerate($conversationId = null)
    {
        try {
            // If conversationId not passed as parameter, extract from URI
            if (empty($conversationId)) {
                $segments = $this->request->getUri()->getSegments();
                // Find the conversation ID in the segments (should be after 'conversations')
                $conversationsIndex = array_search('conversations', $segments);
                if ($conversationsIndex !== false && isset($segments[$conversationsIndex + 1])) {
                    $conversationId = $segments[$conversationsIndex + 1];
                }
            }
            
            if (empty($conversationId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Conversation ID is required',
                ])->setStatusCode(400);
            }
            
            // Get user info
            $userId = null;
            $sessionId = session_id();
            if (empty($sessionId)) {
                session();
                $sessionId = session_id();
            }
            
            // Determine user type server-side
            if (CIAuth::isLoggedIn() && CIAuth::isAdmin()) {
                $userId = session()->get('id');
            } elseif (ClientAuth::isLoggedIn()) {
                $userId = session()->get('id');
            }
            
            // Get conversation
            $conversation = $this->aiAgentService->getConversationWithMessages($conversationId, $userId, $sessionId);
            
            if (!$conversation) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Conversation not found or access denied',
                ])->setStatusCode(404);
            }
            
            // Get last user message
            $messages = $conversation['messages'] ?? [];
            $lastUserMessage = null;
            
            for ($i = count($messages) - 1; $i >= 0; $i--) {
                if ($messages[$i]['role'] === 'user') {
                    $lastUserMessage = $messages[$i];
                    break;
                }
            }
            
            if (!$lastUserMessage) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'No user message found to regenerate',
                ])->setStatusCode(400);
            }
            
            // Remove last assistant message if exists
            $messageModel = new \App\Models\AIMessageModel();
            $lastMessage = $messageModel->getLatestMessage($conversationId);
            if ($lastMessage && $lastMessage['role'] === 'assistant') {
                $messageModel->delete($lastMessage['id']);
            }
            
            // Resend message
            $ipAddress = $this->request->getIPAddress();
            $result = $this->aiAgentService->sendMessage(
                $lastUserMessage['content'],
                $conversationId,
                $userId,
                $conversation['type'] ?? 'customer',
                $sessionId,
                $ipAddress
            );
            
            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'conversation_id' => $result['conversation_id'],
                    'message' => $result['message'],
                    'metadata' => $result['metadata'] ?? [],
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => $result['error'] ?? 'Failed to regenerate message',
                ])->setStatusCode(400);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'AI Agent regenerate error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'An error occurred while regenerating message',
            ])->setStatusCode(500);
        }
    }
}
