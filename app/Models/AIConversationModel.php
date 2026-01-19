<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class AIConversationModel extends Model
{
    protected $table            = 'ai_conversations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'user_id',
        'session_id',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'type' => 'permit_empty|in_list[customer,admin]',
        'status' => 'permit_empty|in_list[active,archived]',
    ];

    protected $beforeInsert = ['generateUuid'];

    /**
     * Generate UUID for new conversations
     */
    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                unset($data['data']['id']);
            }
        }
        return $data;
    }

    /**
     * Get conversations for a user
     */
    public function getUserConversations($userId, $type = null, $status = 'active')
    {
        $builder = $this->where('user_id', $userId);
        
        if ($type) {
            $builder->where('type', $type);
        }
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        return $builder->orderBy('updated_at', 'DESC')->findAll();
    }

    /**
     * Get conversations by session ID (for anonymous users)
     */
    public function getSessionConversations($sessionId, $type = 'customer', $status = 'active')
    {
        return $this->where('session_id', $sessionId)
                    ->where('type', $type)
                    ->where('status', $status)
                    ->orderBy('updated_at', 'DESC')
                    ->findAll();
    }

    /**
     * Archive a conversation
     */
    public function archiveConversation($conversationId)
    {
        return $this->update($conversationId, ['status' => 'archived']);
    }
}
