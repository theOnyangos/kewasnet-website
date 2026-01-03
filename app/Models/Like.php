<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Like extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'likes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'user_id',
        'likeable_type',
        'likeable_id',
        'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field in the table

    // Validation rules
    protected $validationRules = [
        'user_id'        => 'required',
        'likeable_type'  => 'required|in_list[discussion,reply]',
        'likeable_id'    => 'required'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required'
        ],
        'likeable_type' => [
            'required' => 'Likeable type is required',
            'in_list'  => 'Likeable type must be either discussion or reply'
        ],
        'likeable_id' => [
            'required' => 'Likeable ID is required'
        ]
    ];

    protected $beforeInsert = ['generateUUID', 'ensureUniqueLike'];

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
     * Ensures a user can only like an item once
     */
    protected function ensureUniqueLike(array $data)
    {
        $existing = $this->where('user_id', $data['data']['user_id'])
                        ->where('likeable_type', $data['data']['likeable_type'])
                        ->where('likeable_id', $data['data']['likeable_id'])
                        ->first();
        
        if ($existing) {
            throw new \RuntimeException('User has already liked this item');
        }
        return $data;
    }

    /**
     * Adds a like to an item
     */
    public function addLike(string $userId, string $likeableType, string $likeableId): bool
    {
        return $this->insert([
            'user_id'       => $userId,
            'likeable_type' => $likeableType,
            'likeable_id'   => $likeableId
        ]);
    }

    /**
     * Removes a like from an item
     */
    public function removeLike(string $userId, string $likeableType, string $likeableId): bool
    {
        return $this->where('user_id', $userId)
                   ->where('likeable_type', $likeableType)
                   ->where('likeable_id', $likeableId)
                   ->delete();
    }

    /**
     * Checks if a user has liked an item
     */
    public function hasLiked(string $userId, string $likeableType, string $likeableId): bool
    {
        return $this->where('user_id', $userId)
                   ->where('likeable_type', $likeableType)
                   ->where('likeable_id', $likeableId)
                   ->countAllResults() > 0;
    }

    /**
     * Gets all likes for a specific item
     */
    public function getLikesFor(string $likeableType, string $likeableId)
    {
        return $this->where('likeable_type', $likeableType)
                   ->where('likeable_id', $likeableId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Gets like count for a specific item
     */
    public function getLikeCount(string $likeableType, string $likeableId): int
    {
        return $this->where('likeable_type', $likeableType)
                   ->where('likeable_id', $likeableId)
                   ->countAllResults();
    }

    /**
     * Gets all items liked by a user
     */
    public function getUserLikes(string $userId, string $likeableType = null, int $limit = 20)
    {
        $builder = $this->where('user_id', $userId);
        
        if ($likeableType) {
            $builder->where('likeable_type', $likeableType);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                      ->findAll($limit);
    }

    /**
     * Gets most liked items of a type
     */
    public function getMostLiked(string $likeableType, int $limit = 10)
    {
        return $this->select('likeable_id, COUNT(*) as like_count')
                   ->where('likeable_type', $likeableType)
                   ->groupBy('likeable_id')
                   ->orderBy('like_count', 'DESC')
                   ->findAll($limit);
    }
}