<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class ForumModerator extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'forum_moderators';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'forum_id',
        'user_id',
        'assigned_by',
        'moderator_title',
        'assigned_at',
        'is_active',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [];

    protected $validationMessages = [];

    protected $beforeInsert = ['generateUUID', 'setAssignedAt', 'setAssignedBy'];
    protected $beforeUpdate = ['ensureUniqueModerator'];

    /**
     * Generates UUID using Ramsey's UUID library
     */
    protected function generateUUID(array $data)
    {
        if (empty($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    protected function setAssignedBy(array $data)
    {
        if (empty($data['data']['id']) && CIAuth::isLoggedIn()) {
            $data['data']['id'] = session()->get('id') ?? session()->get('id');
        }
        return $data;
    }

    /**
     * Sets assigned_at timestamp if not set
     */
    protected function setAssignedAt(array $data)
    {
        if (empty($data['data']['assigned_at'])) {
            $data['data']['assigned_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Ensures unique moderator assignment
     */
    protected function ensureUniqueModerator(array $data)
    {
        if (isset($data['data']['forum_id']) && isset($data['data']['user_id'])) {
            $existing = $this->where('forum_id', $data['data']['forum_id'])
                            ->where('user_id', $data['data']['user_id'])
                            ->where('id !=', $data['id'][0])
                            ->first();
            
            if ($existing) {
                throw new \RuntimeException('This user is already a moderator for this forum');
            }
        }
        return $data;
    }

    /**
     * Assigns a new moderator to a forum
     */
    public function assignModerator(string $forumId, string $userId, string $moderatorTitle): bool
    {
        // Check if already a moderator
        $existing = $this->where('forum_id', $forumId)
                        ->where('user_id', $userId)
                        ->first();
        
        if ($existing) {
            // If exists but inactive, reactivate
            if (!$existing->is_active) {
                return $this->update($existing->id, [
                    'is_active' => true,
                    'moderator_title' => $moderatorTitle
                ]);
            }
            // Already active moderator
            return false;
        }

        return $this->insert([
            'forum_id'          => $forumId,
            'user_id'           => $userId,
            'moderator_title'   => $moderatorTitle,
            'is_active'         => true
        ]);
    }

    /**
     * Revokes moderator status (sets is_active to false)
     */
    public function revokeModerator(string $moderatorId): bool
    {
        return $this->update($moderatorId, ['is_active' => false]);
    }

    /**
     * Gets active moderators for a forum
     */
    public function getForumModerators(string $forumId)
    {
        return $this->where('forum_id', $forumId)
                   ->where('is_active', true)
                   ->findAll();
    }

    /**
     * Checks if a user is an active moderator for a forum
     */
    public function isModerator(string $forumId, string $userId): bool
    {
        return $this->where('forum_id', $forumId)
                   ->where('user_id', $userId)
                   ->where('is_active', true)
                   ->countAllResults() > 0;
    }

    /**
     * Gets forums moderated by a specific user
     */
    public function getUserModeratedForums(string $userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_active', true)
                   ->findAll();
    }

    public function getForumModeratorsTable($start, $length, $search = null, $orderBy = 'assigned_at', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->builder()
                    ->select('forum_moderators.*, 
                             system_users.picture as moderator_profile, 
                             CONCAT(system_users.first_name, " ", system_users.last_name) as moderator_name, 
                             forum_moderators.created_at as assigned_at,
                             forum_moderators.moderator_title')
                    ->join('system_users', "system_users.id = forum_moderators.user_id COLLATE utf8mb4_unicode_ci", 'left', false)
                    ->groupBy('forum_moderators.id');

        // Filter by forum if forumId is provided
        if ($forumId) {
            $builder->where('forum_moderators.forum_id', $forumId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('forum_moderators.id', $search)
                    ->orLike('system_users.username', $search)
                    ->orLike('system_users.first_name', $search)
                    ->orLike('system_users.last_name', $search)
                    ->orLike('forum_moderators.moderator_title', $search)
                    ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                       ->limit($length, $start)
                       ->get()
                       ->getResult();
    }

    public function countModerators(string $forumId = null): int
    {
        $builder = $this->builder();
        
        if ($forumId) {
            $builder->where('forum_id', $forumId);
        }
        
        return $builder->countAllResults();
    }

    public function getModeratorsByForumId(string $forumId)
    {
        return $this->select('forum_moderators.*, CONCAT(system_users.first_name, " ", system_users.last_name) as user_name, system_users.picture as user_image, system_users.email as user_email')
                    ->join('system_users', 'system_users.id = forum_moderators.user_id', 'left', false)
                    ->where('forum_id', $forumId)
                    ->where('forum_moderators.is_active', true)
                    ->orderBy('forum_moderators.assigned_at', 'DESC')
                    ->findAll();
    }
}