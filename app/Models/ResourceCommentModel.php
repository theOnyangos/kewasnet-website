<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class ResourceCommentModel extends Model
{
    protected $table            = 'resource_comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'resource_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
        'helpful_count',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'resource_id' => 'required|max_length[36]',
        'user_id'     => 'required|string',
        'content'     => 'required|min_length[3]|max_length[1000]',
        'parent_id'   => 'permit_empty|max_length[36]',
    ];

    protected $validationMessages = [
        'content' => [
            'required'   => 'Comment content is required.',
            'min_length' => 'Comment must be at least 3 characters long.',
            'max_length' => 'Comment cannot exceed 1000 characters.',
        ],
    ];

    protected $skipValidation = false;
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
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
     * Get comments for a specific resource with user information
     */
    public function getCommentsForResource(string $resourceId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $comments = $this->db->table('resource_comments rc')
            ->select('
                rc.id, rc.resource_id, rc.user_id, rc.parent_id, rc.content, 
                rc.helpful_count, rc.created_at, rc.updated_at,
                u.first_name, u.last_name, u.username, u.picture, u.role_id,
                r.role_name
            ')
            ->join('system_users u', 'u.id = rc.user_id')
            ->join('roles r', 'r.role_id = u.role_id')
            ->where('rc.resource_id', $resourceId)
            ->where('rc.is_approved', 1)
            ->where('rc.deleted_at', null)
            ->where('rc.parent_id', null) // Only top-level comments
            ->orderBy('rc.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // Get replies for each comment
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getRepliesForComment($comment['id']);
        }

        return $comments;
    }

    /**
     * Get replies for a specific comment
     */
    public function getRepliesForComment(string $commentId): array
    {
        return $this->db->table('resource_comments rc')
            ->select('
                rc.id, rc.resource_id, rc.user_id, rc.parent_id, rc.content, 
                rc.helpful_count, rc.created_at, rc.updated_at,
                u.first_name, u.last_name, u.username, u.picture, u.role_id,
                r.role_name
            ')
            ->join('system_users u', 'u.id = rc.user_id')
            ->join('roles r', 'r.role_id = u.role_id')
            ->where('rc.parent_id', $commentId)
            ->where('rc.is_approved', 1)
            ->where('rc.deleted_at', null)
            ->orderBy('rc.created_at', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get total comment count for a resource
     */
    public function getCommentCount(string $resourceId): int
    {
        return $this->where('resource_id', $resourceId)
            ->where('is_approved', 1)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    /**
     * Mark comment as helpful
     */
    public function incrementHelpfulCount(string $commentId): bool
    {
        return $this->set('helpful_count', 'helpful_count + 1', false)
            ->where('id', $commentId)
            ->update();
    }

    /**
     * Decrease helpful count
     */
    public function decrementHelpfulCount(string $commentId): bool
    {
        return $this->set('helpful_count', 'helpful_count - 1', false)
            ->where('id', $commentId)
            ->where('helpful_count >', 0)
            ->update();
    }

    /**
     * Create a new comment
     */
    public function createComment($data): ?string
    {
        // Generate UUID if not provided
        if (!isset($data['id'])) {
            try {
                $data['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                return null;
            }
        }
        
        log_message('debug', 'Creating comment with data: ' . json_encode($data));
        
        if ($this->insert($data)) {
            log_message('debug', 'Comment inserted successfully with ID: ' . $data['id']);
            return $data['id'];
        }
        
        log_message('error', 'Failed to insert comment');
        return null;
    }
}