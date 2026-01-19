<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class AIMessageModel extends Model
{
    protected $table            = 'ai_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'conversation_id',
        'role',
        'content',
        'metadata',
        'created_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    // Important: BaseModel only checks for '' (empty string), not null.
    // Setting null causes it to insert an empty column name, leading to SQL errors.
    protected $updatedField  = ''; // Messages are immutable (no updated_at column)

    protected $validationRules = [
        'conversation_id' => 'required',
        'role' => 'required|in_list[user,assistant,system]',
        'content' => 'required',
    ];

    protected $beforeInsert = ['generateUuid', 'handleMetadata'];

    /**
     * Generate UUID for new messages
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
     * Handle metadata field (convert array to JSON if needed)
     */
    protected function handleMetadata(array $data)
    {
        if (isset($data['data']['metadata']) && is_array($data['data']['metadata'])) {
            $data['data']['metadata'] = json_encode($data['data']['metadata']);
        }
        return $data;
    }

    /**
     * Get messages for a conversation
     */
    public function getConversationMessages($conversationId, $limit = null)
    {
        $builder = $this->where('conversation_id', $conversationId)
                       ->orderBy('created_at', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        $messages = $builder->findAll();
        
        // Decode metadata JSON
        foreach ($messages as &$message) {
            if (!empty($message['metadata']) && is_string($message['metadata'])) {
                $decoded = json_decode($message['metadata'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $message['metadata'] = $decoded;
                }
            }
        }
        
        return $messages;
    }

    /**
     * Get latest message in a conversation
     */
    public function getLatestMessage($conversationId)
    {
        $message = $this->where('conversation_id', $conversationId)
                       ->orderBy('created_at', 'DESC')
                       ->first();
        
        if ($message && !empty($message['metadata']) && is_string($message['metadata'])) {
            $decoded = json_decode($message['metadata'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $message['metadata'] = $decoded;
            }
        }
        
        return $message;
    }
}
