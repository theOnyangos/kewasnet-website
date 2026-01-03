<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;
use App\Libraries\CIAuth;

class Forum extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'forums';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Important for UUIDs
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'is_draft',
        'sort_order',
        'total_discussions',
        'total_replies',
        'last_activity_at',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation rules
    protected $validationRules = [];
    protected $validationMessages = [];

    protected $beforeInsert = ['generateUUID', 'setCreatedBy'];
    protected $beforeUpdate = ['ensureSlugUniqueness'];

    /**
     * Generates UUID for new records
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Sets created_by field if not set
     */
    protected function setCreatedBy(array $data)
    {
        if (empty($data['data']['created_by']) && CIAuth::isLoggedIn()) {
            $data['data']['created_by'] = session()->get('id');
        }
        return $data;
    }

    /**
     * Ensures slug uniqueness on update
     */
    protected function ensureSlugUniqueness(array $data)
    {
        if (isset($data['data']['slug'])) {
            $existing = $this->where('slug', $data['data']['slug'])
                           ->where('id !=', $data['id'][0])
                           ->first();
            
            if ($existing) {
                $data['data']['slug'] = $data['data']['slug'] . '-' . substr($data['id'][0], 0, 8);
            }
        }
        return $data;
    }

    /**
     * Custom validation rule for color field
     */
    public function valid_color(string $color = null)
    {
        if (empty($color)) return true;
        return preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color);
    }

    /**
     * Increments discussion count
     */
    public function incrementDiscussionCount(string $forumId)
    {
        return $this->builder()
            ->where('id', $forumId)
            ->set('total_discussions', 'total_discussions + 1', false)
            ->update();
    }

    /**
     * Increments reply count
     */
    public function incrementReplyCount(string $forumId)
    {
        return $this->builder()
            ->where('id', $forumId)
            ->set('total_replies', 'total_replies + 1', false)
            ->update();
    }

    /**
     * Updates last activity timestamp
     */
    public function updateLastActivity(string $forumId)
    {
        return $this->builder()
            ->where('id', $forumId)
            ->set('last_activity_at', date('Y-m-d H:i:s'))
            ->update();
    }

    /**
     * Gets active forums ordered by sort order
     */
    public function getActiveForums()
    {
        return $this->where('is_active', true)
                   ->orderBy('sort_order', 'ASC')
                   ->findAll();
    }

    public function getForumTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
                        ->select('forums.*, COUNT(system_users.id) as members, COUNT(discussions.id) as discussion_count, COUNT(replies.id) as reply_count, forums.is_active as status, forums.created_at as added_at')
                        ->join('discussions', "discussions.forum_id = forums.id COLLATE utf8mb4_unicode_ci", 'left', false)
                        ->join('replies', "replies.discussion_id = discussions.id COLLATE utf8mb4_unicode_ci", 'left', false)
                        ->join('system_users', "system_users.id = replies.user_id COLLATE utf8mb4_unicode_ci", 'left', false)
                        ->groupBy('forums.id');

        // Apply search filter
        if ($search) {
            $builder->like('title', $search);
        }

        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);
        // Apply pagination
        $builder->limit($length, $start);
        return $builder->get()->getResult();
    }

    public function countForums(): int
    {
        return $this->where('is_active', true)->countAll();
    }

    public function getForumDetails(string $forumId)
    {
        return $this->db->table('forums')
                    ->select('forums.*, 
                             COUNT(DISTINCT discussions.id) as discussion_count, 
                             COUNT(DISTINCT replies.id) as total_replies, 
                             COUNT(DISTINCT forum_moderators.id) as moderator_count,
                             COUNT(DISTINCT CASE WHEN forum_moderators.is_active = 1 THEN forum_moderators.id END) as active_moderator_count,
                             MAX(GREATEST(
                                 COALESCE(discussions.updated_at, discussions.created_at),
                                 COALESCE(replies.updated_at, replies.created_at)
                             )) as last_activity_at')
                    ->join('discussions', 'discussions.forum_id = forums.id', 'left')
                    ->join('replies', 'replies.discussion_id = discussions.id', 'left')
                    ->join('forum_moderators', 'forum_moderators.forum_id = forums.id', 'left')
                    ->where('forums.id', $forumId)
                    ->groupBy('forums.id')
                    ->get()
                    ->getRow();
    }

    public function getForumStats()
    {
        // Get forum count (using model instance, no table prefix needed)
        $forumCount = $this->where('is_active', true)->countAllResults(false);
        
        // Get discussion count from discussions table
        $discussionCount = $this->db->table('discussions')
                                   ->join('forums', 'forums.id = discussions.forum_id', 'inner')
                                   ->where('forums.is_active', true)
                                   ->countAllResults();
        
        // Get moderator count from forum_moderators table
        $moderatorCount = $this->db->table('forum_moderators')
                                  ->join('forums', 'forums.id = forum_moderators.forum_id', 'inner')
                                  ->where('forums.is_active', true)
                                  ->where('forum_moderators.is_active', true)
                                  ->countAllResults();
        
        // Return as object to match expected structure
        return (object) [
            'forum_count' => $forumCount,
            'discussion_count' => $discussionCount,
            'moderator_count' => $moderatorCount
        ];
    }

    public function deleteForumWithRelations(string $forumId): bool
    {
        // Start transaction
        $this->db->transStart();
        
        try {
            $fileAttachmentModel = new \App\Models\FileAttachment();
            
            // Get all discussion IDs for this forum
            $discussionIds = $this->db->table('discussions')
                ->select('id')
                ->where('forum_id', $forumId)
                ->get()
                ->getResultArray();
            
            $discussionIds = array_column($discussionIds, 'id');
            
            // Get all reply IDs for these discussions
            $replyIds = [];
            if (!empty($discussionIds)) {
                $replyIds = $this->db->table('replies')
                    ->select('id')
                    ->whereIn('discussion_id', $discussionIds)
                    ->get()
                    ->getResultArray();
                $replyIds = array_column($replyIds, 'id');
            }
            
            // 1. Delete all reply attachments (both files and database records)
            if (!empty($replyIds)) {
                $replyAttachments = $fileAttachmentModel
                    ->where('attachable_type', 'reply')
                    ->whereIn('attachable_id', $replyIds)
                    ->findAll();
                
                foreach ($replyAttachments as $attachment) {
                    // Delete physical file
                    $filePath = FCPATH . $attachment['file_path'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
                
                // Delete database records
                $fileAttachmentModel
                    ->where('attachable_type', 'reply')
                    ->whereIn('attachable_id', $replyIds)
                    ->delete();
            }
            
            // 2. Delete all discussion attachments (both files and database records)
            if (!empty($discussionIds)) {
                $discussionAttachments = $fileAttachmentModel
                    ->where('attachable_type', 'discussion')
                    ->whereIn('attachable_id', $discussionIds)
                    ->findAll();
                
                foreach ($discussionAttachments as $attachment) {
                    // Delete physical file
                    $filePath = FCPATH . $attachment['file_path'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
                
                // Delete database records
                $fileAttachmentModel
                    ->where('attachable_type', 'discussion')
                    ->whereIn('attachable_id', $discussionIds)
                    ->delete();
            }
            
            // 3. Delete all forum attachments (both files and database records)
            $forumAttachments = $fileAttachmentModel
                ->where('attachable_type', 'forum')
                ->where('attachable_id', $forumId)
                ->findAll();
            
            foreach ($forumAttachments as $attachment) {
                // Delete physical file
                $filePath = FCPATH . $attachment['file_path'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            
            $fileAttachmentModel
                ->where('attachable_type', 'forum')
                ->where('attachable_id', $forumId)
                ->delete();
            
            // 4. Delete all reply likes (if table exists)
            if (!empty($replyIds) && $this->db->tableExists('reply_likes')) {
                $this->db->table('reply_likes')
                    ->whereIn('reply_id', $replyIds)
                    ->delete();
            }
            
            // 5. Delete all discussion likes (if table exists)
            if (!empty($discussionIds) && $this->db->tableExists('discussion_likes')) {
                $this->db->table('discussion_likes')
                    ->whereIn('discussion_id', $discussionIds)
                    ->delete();
            }
            
            // 6. Delete all replies in this forum's discussions
            if (!empty($discussionIds)) {
                $this->db->table('replies')
                    ->whereIn('discussion_id', $discussionIds)
                    ->delete();
            }
            
            // 7. Delete all discussions in this forum
            $this->db->table('discussions')
                ->where('forum_id', $forumId)
                ->delete();
            
            // 8. Delete all moderators for this forum
            $this->db->table('forum_moderators')
                ->where('forum_id', $forumId)
                ->delete();
            
            // 9. Delete all forum members
            $this->db->table('forum_members')
                ->where('forum_id', $forumId)
                ->delete();
            
            // 10. Delete the forum itself (force permanent delete, not soft delete)
            $this->db->table('forums')
                ->where('id', $forumId)
                ->delete();
            
            // Complete transaction
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                log_message('error', 'Transaction failed during forum deletion');
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->transRollback();
            log_message('error', 'Forum deletion failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function getSystemUsers(string|null $searchTerm, int $limit): array
    {
        $builder = $this->db->table('system_users');
        $builder->select('id, first_name, last_name, email, roles.role_name as role, picture as profile_image')
                ->join('roles', 'roles.role_id = system_users.role_id', 'left', false);

        if ($searchTerm) {
            $builder->like('first_name', $searchTerm)
                    ->orLike('last_name', $searchTerm)
                    ->orLike('email', $searchTerm);
        }

        $builder->limit($limit);
        return $builder->get()->getResultArray();
    }

    public function getForumById($forumId)
    {
        return $this->select('forums.*, COUNT(discussions.id) as discussion_count, COUNT(forum_members.id) as member_count')
                    ->join('discussions', 'discussions.forum_id = forums.id', 'left', false)
                    ->join('forum_members', 'forum_members.forum_id = forums.id', 'left', false)
                    ->where('forums.id', $forumId)
                    ->first();
    }

    public function getForumBySlug($slug)
    {
        return $this->select('forums.*, COUNT(discussions.id) as discussion_count, COUNT(forum_members.id) as member_count')
                    ->join('discussions', 'discussions.forum_id = forums.id', 'left', false)
                    ->join('forum_members', 'forum_members.forum_id = forums.id', 'left', false)
                    ->where('forums.slug', $slug)
                    ->first();
    }
}