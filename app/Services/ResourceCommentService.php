<?php

namespace App\Services;

use App\Models\ResourceModel;
use App\Libraries\ClientAuth;
use App\Models\ResourceCommentModel;
use App\Models\ResourceHelpfulVoteModel;

class ResourceCommentService
{
    protected $voteModel;
    protected $commentModel;
    protected $resourceModel;

    public function __construct()
    {
        $this->resourceModel  = new ResourceModel();
        $this->commentModel   = new ResourceCommentModel();
        $this->voteModel      = new ResourceHelpfulVoteModel();
    }

    /**
     * Check if user is logged in
     */
    public function isUserLoggedIn(): bool
    {
        return ClientAuth::isLoggedIn();
    }

    /**
     * Get current user ID
     */
    public function getCurrentUserId(): ?int
    {
        $userId = ClientAuth::getId();
        return $userId ? (int) $userId : null;
    }

    /**
     * Check if user can access private resources
     */
    public function canAccessPrivateResources(): bool
    {
        if (!$this->isUserLoggedIn()) {
            return false;
        }

        $user = ClientAuth::user();
        if (!$user) {
            return false;
        }

        // Employees (role_id 3), Administrators (role_id 1), and Moderators (role_id 5) can access private resources
        return in_array($user['role_id'], [1, 3, 5]);
    }

    /**
     * Check if resource is accessible to current user
     */
    public function canAccessResource(array $resource): bool
    {
        // If resource is public, everyone can access
        if (empty($resource['is_private']) || $resource['is_private'] == 0) {
            return true;
        }

        // If resource is private, only authorized users can access
        return $this->canAccessPrivateResources();
    }

    /**
     * Get comments for a resource with user voting status
     */
    public function getCommentsForResource(string $resourceId, int $page = 1, int $perPage = 10): array
    {
        $comments = $this->commentModel->getCommentsForResource($resourceId, $page, $perPage);
        $userId = $this->getCurrentUserId();

        // Add user voting status to comments and replies
        foreach ($comments as &$comment) {
            $comment['user_has_voted'] = false;
            if ($userId) {
                $comment['user_has_voted'] = $this->voteModel->hasUserVotedOnComment($userId, $comment['id']);
            }

            // Add voting status to replies
            foreach ($comment['replies'] as &$reply) {
                $reply['user_has_voted'] = false;
                if ($userId) {
                    $reply['user_has_voted'] = $this->voteModel->hasUserVotedOnComment($userId, $reply['id']);
                }
            }
        }

        return $comments;
    }

    /**
     * Get comment count for a resource
     */
    public function getCommentCount(string $resourceId): int
    {
        return $this->commentModel->getCommentCount($resourceId);
    }

    /**
     * Add a comment to a resource
     */
    public function addComment(string $resourceId, string $content, ?string $parentId = null): array
    {
        if (!$this->isUserLoggedIn()) {
            return [
                'success' => false,
                'message' => 'You must be logged in to post comments.',
                'redirect' => base_url('ksp/login')
            ];
        }

        $userId = session()->get('id') ?? null;
        
        // Debug logging to check user ID
        log_message('debug', 'AddComment: Retrieved user ID: ' . ($userId ?? 'null'));
        
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Unable to identify user. Please log in again.',
                'redirect' => base_url('ksp/login')
            ];
        }
        
        // Validate content
        if (empty(trim($content))) {
            return [
                'success' => false,
                'message' => 'Comment content cannot be empty.'
            ];
        }

        if (strlen($content) > 1000) {
            return [
                'success' => false,
                'message' => 'Comment cannot exceed 1000 characters.'
            ];
        }

        try {
            $data = [
                'resource_id'   => $resourceId,
                'user_id'       => $userId,
                'content'       => trim($content),
                'parent_id'     => $parentId,
                'is_approved'   => 1, // Auto-approve for now
            ];

            // Debug logging for comment data
            log_message('debug', 'AddComment: Comment data: ' . json_encode($data));

            $commentId = $this->commentModel->createComment($data);

            // Debug logging for insert result
            log_message('debug', 'AddComment: Insert result: ' . ($commentId ?? 'null'));
            
            // Check for validation errors
            $errors = $this->commentModel->errors();
            if (!empty($errors)) {
                log_message('error', 'AddComment validation errors: ' . json_encode($errors));
                return [
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $errors)
                ];
            }

            if ($commentId) {
                return [
                    'success' => true,
                    'message' => 'Comment posted successfully.',
                    'comment_id' => $commentId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to save comment. Please try again.'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error adding comment: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while posting your comment.'
            ];
        }
    }

    /**
     * Vote on a resource as helpful
     */
    public function voteResourceHelpful(string $resourceId): array
    {
        if (!$this->isUserLoggedIn()) {
            return [
                'success' => false,
                'message' => 'You must be logged in to vote.',
                'redirect' => base_url('ksp/login')
            ];
        }

        $userId = $this->getCurrentUserId();

        try {
            // Check if user has already voted
            if ($this->voteModel->hasUserVotedOnResource($userId, $resourceId)) {
                return [
                    'success' => false,
                    'message' => 'You have already voted on this resource.'
                ];
            }

            // Add vote
            $voteSuccess = $this->voteModel->voteOnResource($userId, $resourceId, 'helpful');
            
            if ($voteSuccess) {
                // Update resource helpful count
                $this->resourceModel->set('helpful_count', 'helpful_count + 1', false)
                    ->where('id', $resourceId)
                    ->update();

                $newCount = $this->voteModel->getResourceHelpfulCount($resourceId);
                
                return [
                    'success' => true,
                    'message' => 'Thank you for your feedback!',
                    'helpful_count' => $newCount
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to record your vote. Please try again.'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error voting on resource: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while recording your vote.'
            ];
        }
    }

    /**
     * Vote on a comment as helpful
     */
    public function voteCommentHelpful(string $commentId): array
    {
        if (!$this->isUserLoggedIn()) {
            return [
                'success' => false,
                'message' => 'You must be logged in to vote.',
                'redirect' => base_url('ksp/login')
            ];
        }

        $userId = $this->getCurrentUserId();

        try {
            // Check if user has already voted
            if ($this->voteModel->hasUserVotedOnComment($userId, $commentId)) {
                return [
                    'success' => false,
                    'message' => 'You have already voted on this comment.'
                ];
            }

            // Add vote
            $voteSuccess = $this->voteModel->voteOnComment($userId, $commentId, 'helpful');
            
            if ($voteSuccess) {
                // Update comment helpful count
                $this->commentModel->incrementHelpfulCount($commentId);
                
                $newCount = $this->voteModel->getCommentHelpfulCount($commentId);
                
                return [
                    'success' => true,
                    'message' => 'Thank you for your feedback!',
                    'helpful_count' => $newCount
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to record your vote. Please try again.'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error voting on comment: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while recording your vote.'
            ];
        }
    }

    /**
     * Get resource helpful status for current user
     */
    public function getResourceHelpfulStatus(string $resourceId): array
    {
        $userId = $this->getCurrentUserId();
        $helpfulCount = $this->voteModel->getResourceHelpfulCount($resourceId);
        
        $userHasVoted = false;
        if ($userId) {
            $userHasVoted = $this->voteModel->hasUserVotedOnResource($userId, $resourceId);
        }

        return [
            'helpful_count' => $helpfulCount,
            'user_has_voted' => $userHasVoted,
            'is_logged_in' => $this->isUserLoggedIn()
        ];
    }
}
