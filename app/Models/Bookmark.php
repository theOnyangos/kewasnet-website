<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Bookmark extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bookmarks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'user_id',
        'discussion_id',
        'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field in the table

    // Validation rules
    protected $validationRules = [
        'user_id'       => 'required',
        'discussion_id' => 'required'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required'
        ],
        'discussion_id' => [
            'required' => 'Discussion ID is required'
        ]
    ];

    protected $beforeInsert = ['generateUUID', 'ensureUniqueBookmark'];

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
     * Ensures a user can only bookmark a discussion once
     */
    protected function ensureUniqueBookmark(array $data)
    {
        $existing = $this->where('user_id', $data['data']['user_id'])
                        ->where('discussion_id', $data['data']['discussion_id'])
                        ->first();
        
        if ($existing) {
            throw new \RuntimeException('Discussion is already bookmarked by this user');
        }
        return $data;
    }

    /**
     * Adds a bookmark for a discussion
     */
    public function addBookmark(string $userId, string $discussionId): bool
    {
        return $this->insert([
            'user_id'       => $userId,
            'discussion_id' => $discussionId
        ]);
    }

    /**
     * Removes a bookmark
     */
    public function removeBookmark(string $userId, string $discussionId): bool
    {
        return $this->where('user_id', $userId)
                   ->where('discussion_id', $discussionId)
                   ->delete();
    }

    /**
     * Checks if a discussion is bookmarked by a user
     */
    public function isBookmarked(string $userId, string $discussionId): bool
    {
        return $this->where('user_id', $userId)
                   ->where('discussion_id', $discussionId)
                   ->countAllResults() > 0;
    }

    /**
     * Gets all bookmarks for a user
     */
    public function getUserBookmarks(string $userId, int $limit = 20)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll($limit);
    }

    /**
     * Gets bookmark count for a discussion
     */
    public function getBookmarkCount(string $discussionId): int
    {
        return $this->where('discussion_id', $discussionId)
                   ->countAllResults();
    }

    /**
     * Gets paginated bookmarks for a user with discussion data
     */
    public function getUserBookmarksWithDiscussions(string $userId, int $perPage = 15)
    {
        return $this->select('bookmarks.*, discussions.title, discussions.slug, forums.name as forum_name, forums.slug as forum_slug')
                   ->join('discussions', 'discussions.id = bookmarks.discussion_id')
                   ->join('forums', 'forums.id = discussions.forum_id')
                   ->where('bookmarks.user_id', $userId)
                   ->orderBy('bookmarks.created_at', 'DESC')
                   ->paginate($perPage);
    }

    /**
     * Removes all bookmarks for a discussion (when discussion is deleted)
     */
    public function removeAllForDiscussion(string $discussionId): bool
    {
        return $this->where('discussion_id', $discussionId)
                   ->delete();
    }

    /**
     * Removes all bookmarks by a user
     */
    public function removeAllForUser(string $userId): bool
    {
        return $this->where('user_id', $userId)
                   ->delete();
    }
}