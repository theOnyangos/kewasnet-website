<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class DiscussionView extends Model
{
    protected $table = 'discussion_views';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id',
        'discussion_id',
        'user_id',
        'ip_address',
        'user_agent',
        'viewed_at'
    ];
    
    protected $useTimestamps = false;
    protected $createdField = 'viewed_at';
    protected $updatedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'discussion_id' => 'required',
        'user_id' => 'permit_empty',
        'ip_address' => 'permit_empty|max_length[45]',
        'user_agent' => 'permit_empty',
        'viewed_at' => 'required|valid_date'
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    // Callbacks
    protected $beforeInsert = ['generateUUID', 'setViewTimestamp'];
    
    /**
     * Generate UUID for primary key
     */
    protected function generateUUID(array $data)
    {
        try {
            if (!isset($data['data']['id'])) {
                $data['data']['id'] = Uuid::uuid4()->toString();
            }
            
            // Validate discussion_id UUID if provided
            if (isset($data['data']['discussion_id']) && !Uuid::isValid($data['data']['discussion_id'])) {
                throw new \InvalidArgumentException('Invalid discussion_id UUID format');
            }
            
            // Validate user_id UUID if provided
            if (isset($data['data']['user_id']) && !empty($data['data']['user_id']) && 
                !Uuid::isValid($data['data']['user_id'])) {
                throw new \InvalidArgumentException('Invalid user_id UUID format');
            }
            
        } catch (UnsatisfiedDependencyException $e) {
            log_message('error', 'UUID generation failed: ' . $e->getMessage());
            throw new \RuntimeException('UUID generation failed');
        }
        
        return $data;
    }
    
    /**
     * Set viewed_at timestamp if not provided
     */
    protected function setViewTimestamp(array $data)
    {
        if (!isset($data['data']['viewed_at'])) {
            $data['data']['viewed_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }
    
    /**
     * Record a view for a discussion
     */
    public function recordView($discussionId, $userId = null, $ipAddress = null, $userAgent = null)
    {
        // Check if this is a unique view (user or IP hasn't viewed recently)
        if (!$this->isUniqueView($discussionId, $userId, $ipAddress)) {
            return false;
        }
        
        $data = [
            'discussion_id' => $discussionId,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Check if this is a unique view (within last 24 hours)
     */
    public function isUniqueView($discussionId, $userId = null, $ipAddress = null)
    {
        $builder = $this->builder();
        $builder->where('discussion_id', $discussionId);
        $builder->where('viewed_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')));
        
        if ($userId) {
            $builder->where('user_id', $userId);
        } else if ($ipAddress) {
            $builder->where('ip_address', $ipAddress);
        } else {
            // No way to identify user, count as unique
            return true;
        }
        
        return $builder->countAllResults() === 0;
    }
    
    /**
     * Get view count for a discussion
     */
    public function getViewCount($discussionId)
    {
        return $this->where('discussion_id', $discussionId)->countAllResults();
    }
    
    /**
     * Get recent views for a discussion
     */
    public function getRecentViews($discussionId, $limit = 10)
    {
        return $this->where('discussion_id', $discussionId)
                   ->orderBy('viewed_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Get views with user information
     */
    public function getViewsWithUsers($discussionId, $limit = 10)
    {
        $builder = $this->db->table('discussion_views');
        $builder->select('discussion_views.*, system_users.username, system_users.avatar');
        $builder->join('system_users', 'system_users.id = discussion_views.user_id', 'left');
        $builder->where('discussion_views.discussion_id', $discussionId);
        $builder->orderBy('discussion_views.viewed_at', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResult();
    }
    
    /**
     * Get popular discussions (most viewed)
     */
    public function getPopularDiscussions($limit = 5, $timePeriod = '7 days')
    {
        $builder = $this->db->table('discussion_views');
        $builder->select('discussion_id, COUNT(*) as view_count');
        $builder->select('discussions.title, discussions.slug, system_users.username as author_name');
        $builder->join('discussions', 'discussions.id = discussion_views.discussion_id');
        $builder->join('system_users', 'system_users.id = discussions.user_id');
        $builder->where('discussion_views.viewed_at >=', date('Y-m-d H:i:s', strtotime("-$timePeriod")));
        $builder->groupBy('discussion_id');
        $builder->orderBy('view_count', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResult();
    }
    
    /**
     * Clean up old view records
     */
    public function cleanupOldViews($daysToKeep = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-$daysToKeep days"));
        return $this->where('viewed_at <', $cutoffDate)->delete();
    }
}