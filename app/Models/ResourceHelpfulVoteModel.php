<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class ResourceHelpfulVoteModel extends Model
{
    protected $table            = 'resource_helpful_votes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'resource_id',
        'comment_id',
        'vote_type',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $beforeInsert = ['generateUUID'];

    // Validation
    protected $validationRules = [
        'user_id'    => 'required|integer',
        'vote_type'  => 'required|in_list[helpful,not_helpful]',
    ];

    protected $validationMessages = [
        'vote_type' => [
            'in_list' => 'Vote type must be either helpful or not_helpful.',
        ],
    ];

    protected $skipValidation = false;

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
     * Check if user has already voted on a resource
     */
    public function hasUserVotedOnResource(int $userId, string $resourceId): bool
    {
        return $this->where('user_id', $userId)
            ->where('resource_id', $resourceId)
            ->where('comment_id', null)
            ->countAllResults() > 0;
    }

    /**
     * Check if user has already voted on a comment
     */
    public function hasUserVotedOnComment(int $userId, string $commentId): bool
    {
        return $this->where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('resource_id', null)
            ->countAllResults() > 0;
    }

    /**
     * Get user's vote on a resource
     */
    public function getUserVoteOnResource(int $userId, string $resourceId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('resource_id', $resourceId)
            ->where('comment_id', null)
            ->first();
    }

    /**
     * Get user's vote on a comment
     */
    public function getUserVoteOnComment(int $userId, string $commentId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('resource_id', null)
            ->first();
    }

    /**
     * Vote on a resource
     */
    public function voteOnResource(int $userId, string $resourceId, string $voteType = 'helpful'): bool
    {
        // Check if user has already voted
        if ($this->hasUserVotedOnResource($userId, $resourceId)) {
            return false; // User has already voted
        }

        return $this->insert([
            'user_id'     => $userId,
            'resource_id' => $resourceId,
            'vote_type'   => $voteType,
        ]);
    }

    /**
     * Vote on a comment
     */
    public function voteOnComment(int $userId, string $commentId, string $voteType = 'helpful'): bool
    {
        // Check if user has already voted
        if ($this->hasUserVotedOnComment($userId, $commentId)) {
            return false; // User has already voted
        }

        return $this->insert([
            'user_id'    => $userId,
            'comment_id' => $commentId,
            'vote_type'  => $voteType,
        ]);
    }

    /**
     * Remove vote from resource
     */
    public function removeVoteFromResource(int $userId, string $resourceId): bool
    {
        return $this->where('user_id', $userId)
            ->where('resource_id', $resourceId)
            ->where('comment_id', null)
            ->delete();
    }

    /**
     * Remove vote from comment
     */
    public function removeVoteFromComment(int $userId, string $commentId): bool
    {
        return $this->where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('resource_id', null)
            ->delete();
    }

    /**
     * Get helpful vote count for resource
     */
    public function getResourceHelpfulCount(string $resourceId): int
    {
        return $this->where('resource_id', $resourceId)
            ->where('comment_id', null)
            ->where('vote_type', 'helpful')
            ->countAllResults();
    }

    /**
     * Get helpful vote count for comment
     */
    public function getCommentHelpfulCount(string $commentId): int
    {
        return $this->where('comment_id', $commentId)
            ->where('resource_id', null)
            ->where('vote_type', 'helpful')
            ->countAllResults();
    }
}
