<?php namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class UserBookmark extends Model
{
    protected $table = 'user_bookmarks';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id',
        'user_id',
        'resource_id',
        'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // No updated_at field
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'user_id' => 'required',
        'resource_id' => 'required'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required'
        ],
        'resource_id' => [
            'required' => 'Resource ID is required'
        ]
    ];
    
    protected $beforeInsert = ['generateUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                unset($data['data']['id']); // Let database handle it
            }
        }
        return $data;
    }
    
    /**
     * Check if a resource is bookmarked by user
     */
    public function isBookmarked($userId, $resourceId)
    {
        return $this->where('user_id', $userId)
                  ->where('resource_id', $resourceId)
                  ->countAllResults() > 0;
    }
    
    /**
     * Get all bookmarks for a user with resource details
     */
    public function getUserBookmarks($userId)
    {
        return $this->select('user_bookmarks.*, resources.title, resources.slug, resources.description, resources.image_url')
                  ->join('resources', 'resources.id = user_bookmarks.resource_id')
                  ->where('user_bookmarks.user_id', $userId)
                  ->where('resources.is_published', 1)
                  ->orderBy('user_bookmarks.created_at', 'DESC')
                  ->findAll();
    }
    
    /**
     * Add a bookmark
     */
    public function addBookmark($userId, $resourceId)
    {
        // Double-check it's not already bookmarked (race condition protection)
        if ($this->isBookmarked($userId, $resourceId)) {
            log_message('debug', "Bookmark already exists: User {$userId}, Resource {$resourceId}");
            return false; // Already bookmarked
        }
        
        $data = [
            'user_id' => $userId,
            'resource_id' => $resourceId
        ];
        
        log_message('debug', "Attempting to insert bookmark: " . json_encode($data));
        
        // Insert the bookmark
        $result = $this->insert($data);
        
        if (!$result) {
            $errors = $this->errors();
            log_message('error', 'UserBookmark insert failed: ' . json_encode($errors));
            log_message('error', 'Attempted data: ' . json_encode($data));
            log_message('error', 'Model validation: ' . ($this->skipValidation ? 'skipped' : 'enabled'));
        } else {
            log_message('info', "Bookmark inserted successfully. Insert ID: {$result}");
        }
        
        return $result;
    }
    
    /**
     * Remove a bookmark
     */
    public function removeBookmark($userId, $resourceId)
    {
        return $this->where('user_id', $userId)
                  ->where('resource_id', $resourceId)
                  ->delete();
    }
    
    /**
     * Toggle bookmark status
     */
    public function toggleBookmark($userId, $resourceId)
    {
        if ($this->isBookmarked($userId, $resourceId)) {
            return $this->removeBookmark($userId, $resourceId) ? 'removed' : false;
        } else {
            return $this->addBookmark($userId, $resourceId) ? 'added' : false;
        }
    }
    
    /**
     * Count bookmarks for a resource
     */
    public function countResourceBookmarks($resourceId)
    {
        return $this->where('resource_id', $resourceId)
                  ->countAllResults();
    }
}