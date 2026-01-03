<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;
use App\Libraries\CIAuth;
use App\Libraries\ClientAuth;

class Discussion extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'discussions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id',
        'forum_id',
        'user_id',
        'title',
        'slug',
        'content',
        'tags',
        'is_pinned',
        'is_locked',
        'is_featured',
        'view_count',
        'reply_count',
        'like_count',
        'last_reply_at',
        'last_reply_by',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // JSON fields
    protected $jsonFields = ['tags'];

    // Validation rules
    protected $validationRules = [];
    protected $validationMessages = [];

    protected $beforeInsert = ['generateUUID', 'generateSlug', 'setInitialCounts', 'setCreatedBy'];
    protected $beforeUpdate = ['ensureSlugUniqueness'];

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

    /**
     * Add user_id field based on session data
     */
    protected function setCreatedBy(array $data)
    {
        if (empty($data['data']['user_id']) && (CIAuth::isLoggedIn() || ClientAuth::isLoggedIn())) {
            $data['data']['user_id'] = session()->get('id') ?? session()->get('user_id');
        }
        return $data;
    }

    /**
     * Generates slug from title if not provided
     */
    protected function generateSlug(array $data)
    {
        if (empty($data['data']['slug']) && !empty($data['data']['title'])) {
            $data['data']['slug'] = url_title($data['data']['title'], '-', true);
        }
        return $data;
    }

    /**
     * Sets initial counts for new discussions
     */
    protected function setInitialCounts(array $data)
    {
        $data['data']['view_count'] = 0;
        $data['data']['reply_count'] = 0;
        $data['data']['like_count'] = 0;
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
     * Increments view count for a discussion
     */
    public function incrementViews(string $discussionId)
    {
        return $this->builder()
            ->where('id', $discussionId)
            ->set('view_count', 'view_count + 1', false)
            ->update();
    }

    /**
     * Increments reply count and updates last reply info
     */
    public function incrementReplies(string $discussionId, string $userId)
    {
        return $this->builder()
            ->where('id', $discussionId)
            ->set('reply_count', 'reply_count + 1', false)
            ->set('last_reply_at', date('Y-m-d H:i:s'))
            ->set('last_reply_by', $userId)
            ->update();
    }

    /**
     * Updates like count (can be positive or negative)
     */
    public function updateLikes(string $discussionId, int $change)
    {
        return $this->builder()
            ->where('id', $discussionId)
            ->set('like_count', 'like_count + ' . $change, false)
            ->update();
    }

    /**
     * Toggles discussion pin status
     */
    public function togglePin(string $discussionId, bool $status)
    {
        return $this->update($discussionId, ['is_pinned' => $status]);
    }

    /**
     * Toggles discussion lock status
     */
    public function toggleLock(string $discussionId, bool $status)
    {
        return $this->update($discussionId, ['is_locked' => $status]);
    }

    /**
     * Changes discussion status
     */
    public function changeStatus(string $discussionId, string $status)
    {
        $allowedStatuses = ['active', 'hidden', 'reported', 'deleted'];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException('Invalid discussion status');
        }
        return $this->update($discussionId, ['status' => $status]);
    }

    /**
     * Gets discussions for a forum with pagination
     */
    public function getForumDiscussions(string $forumId, int $perPage = 20)
    {
        return $this->where('forum_id', $forumId)
                   ->where('status', 'active')
                   ->orderBy('is_pinned', 'DESC')
                   ->orderBy('last_reply_at', 'DESC')
                   ->paginate($perPage);
    }

    /**
     * Gets featured discussions
     */
    public function getFeaturedDiscussions(int $limit = 5)
    {
        return $this->where('is_featured', true)
                   ->where('status', 'active')
                   ->orderBy('created_at', 'DESC')
                   ->findAll($limit);
    }

    /**
     * Gets recent discussions by a user
     */
    public function getUserDiscussions(string $userId, int $limit = 10)
    {
        return $this->where('user_id', $userId)
                   ->where('status', 'active')
                   ->orderBy('created_at', 'DESC')
                   ->findAll($limit);
    }

    public function createDiscussion(array $formData): ?string
    {
        $this->insert($formData);
        return $this->getInsertID();
    }

    public function getDiscussions($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->builder()
                    ->select('discussions.*, system_users.picture as author_profile, system_users.first_name, system_users.last_name, COUNT(replies.id) as comment_count, discussions.created_at as added_at')
                    ->join('system_users', "system_users.id = discussions.user_id COLLATE utf8mb4_unicode_ci", 'left', false)
                    ->join('replies', "replies.discussion_id = discussions.id COLLATE utf8mb4_unicode_ci", 'left', false)
                    ->groupBy('discussions.id')
                    ->where('discussions.status', 'active');

        // Filter by forum_id if provided
        if ($forumId) {
            $builder->where('discussions.forum_id', $forumId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('discussions.title', $search)
                    ->orLike('discussions.content', $search)
                    ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                        ->limit($length, $start)
                        ->get()
                        ->getResult();
    }

    public function countDiscussions(string $forumId): int
    {
        return $this->where('forum_id', $forumId)
                   ->where('status', 'active')
                   ->countAllResults();
    }

    public function deleteDiscussionWithReplies(string $discussionId): bool
    {
        $this->db->transStart();
        
        try {
            // Delete all replies first
            $this->db->table('replies')
                    ->where('discussion_id', $discussionId)
                    ->delete();
            
            // Then delete the discussion
            $result = parent::delete($discussionId);
            
            $this->db->transComplete();
            return $result;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Discussion deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getForumDiscussionsByForumId(string $forumId): array
    {
        return $this->select('discussions.title, discussions.created_at, COUNT(replies.id) as comment_count')
                   ->join('replies', "replies.discussion_id = discussions.id COLLATE utf8mb4_unicode_ci", 'left', false)
                   ->where('discussions.forum_id', $forumId)
                   ->where('discussions.status', 'active')
                   ->groupBy('discussions.id, discussions.title, discussions.created_at, discussions.is_pinned, discussions.last_reply_at')
                   ->orderBy('discussions.is_pinned', 'DESC')
                   ->orderBy('discussions.last_reply_at', 'DESC')
                   ->findAll();
    }

    public function getForumClientDiscussionByForumId(string $forumId, $perPage = 5): array
    {
        $discussions = $this->select('discussions.*, COUNT(replies.id) as comment_count')
            ->join('replies', 'replies.discussion_id = discussions.id', 'left')
            ->where('discussions.forum_id', $forumId)
            ->where('discussions.status', 'active')
            ->groupBy('discussions.id')
            ->orderBy('discussions.is_pinned', 'DESC')
            ->orderBy('discussions.last_reply_at', 'DESC')
            ->paginate($perPage);
        
        return [
            'discussions' => $discussions,
            'pager'       => $this->pager
        ];
    }

    public function getDiscussionAuthorInformation(string $discussionId)
    {
        $result = $this->select('system_users.first_name, system_users.last_name, system_users.picture, system_users.username')
            ->join('system_users', 'system_users.id = discussions.user_id')
            ->where('discussions.id', $discussionId)
            ->first();

        return $result;
    }
}