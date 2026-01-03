<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Reply extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'replies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id',
        'discussion_id',
        'user_id',
        'parent_id',
        'content',
        'like_count',
        'is_best_answer',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'discussion_id' => 'required',
        'user_id'       => 'required',
        'content'       => 'required|min_length[10]',
        'status'        => 'permit_empty|in_list[active,hidden,reported,deleted]'
    ];

    protected $validationMessages = [
        'discussion_id' => [
            'required' => 'Discussion ID is required'
        ],
        'user_id' => [
            'required' => 'User ID is required'
        ],
        'content' => [
            'required'    => 'Reply content is required',
            'min_length'  => 'Reply must be at least 10 characters long'
        ]
    ];

    protected $beforeInsert = ['generateUUID', 'setInitialCounts'];
    protected $beforeUpdate = ['validateBestAnswer'];

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
     * Sets initial counts for new replies
     */
    protected function setInitialCounts(array $data)
    {
        $data['data']['like_count'] = 0;
        $data['data']['is_best_answer'] = false;
        return $data;
    }

    /**
     * Validates best answer status changes
     */
    protected function validateBestAnswer(array $data)
    {
        if (isset($data['data']['is_best_answer']) && $data['data']['is_best_answer']) {
            // Check if another reply is already marked as best answer
            $existingBest = $this->where('discussion_id', $data['data']['discussion_id'] ?? $this->find($data['id'][0])->discussion_id)
                               ->where('is_best_answer', true)
                               ->where('id !=', $data['id'][0])
                               ->first();
            
            if ($existingBest) {
                throw new \RuntimeException('Only one reply can be marked as best answer');
            }
        }
        return $data;
    }

    /**
     * Increments like count for a reply
     */
    public function incrementLikes(string $replyId)
    {
        return $this->builder()
            ->where('id', $replyId)
            ->set('like_count', 'like_count + 1', false)
            ->update();
    }

    /**
     * Decrements like count for a reply
     */
    public function decrementLikes(string $replyId)
    {
        return $this->builder()
            ->where('id', $replyId)
            ->set('like_count', 'GREATEST(0, like_count - 1)', false)
            ->update();
    }

    /**
     * Marks a reply as best answer
     */
    public function markAsBestAnswer(string $replyId)
    {
        $reply = $this->find($replyId);
        if (!$reply) {
            throw new \RuntimeException('Reply not found');
        }

        // First unmark any existing best answer
        $this->builder()
            ->where('discussion_id', $reply->discussion_id)
            ->where('is_best_answer', true)
            ->update(['is_best_answer' => false]);

        // Then mark the new best answer
        return $this->update($replyId, ['is_best_answer' => true]);
    }

    /**
     * Gets replies for a discussion
     */
    public function getDiscussionReplies(string $discussionId, bool $includeNested = true)
    {
        $builder = $this->where('discussion_id', $discussionId)
                       ->where('status', 'active')
                       ->orderBy('is_best_answer', 'DESC')
                       ->orderBy('created_at', 'ASC');

        if (!$includeNested) {
            $builder->where('parent_id IS NULL');
        }

        return $builder->findAll();
    }

    /**
     * Gets nested replies for a parent reply
     */
    public function getNestedReplies(string $parentId)
    {
        return $this->where('parent_id', $parentId)
                   ->where('status', 'active')
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    /**
     * Changes reply status
     */
    public function changeStatus(string $replyId, string $status)
    {
        $allowedStatuses = ['active', 'hidden', 'reported', 'deleted'];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException('Invalid reply status');
        }
        return $this->update($replyId, ['status' => $status]);
    }

    /**
     * Gets user's replies
     */
    public function getUserReplies(string $userId, int $limit = 10)
    {
        return $this->where('user_id', $userId)
                   ->where('status', 'active')
                   ->orderBy('created_at', 'DESC')
                   ->findAll($limit);
    }

    public function delete($replyId = null, bool $purge = false): bool
    {
        return parent::delete($replyId, $purge);
    }

    public function getRepliesWithUserInfo(string $discussionId): array
    {
        // Get top-level replies with user info
        $replies = $this->select('replies.*, system_users.first_name, system_users.last_name, system_users.picture, system_users.username')
            ->join('system_users', 'system_users.id = replies.user_id')
            ->where('replies.discussion_id', $discussionId)
            ->where('replies.status', 'active')
            ->where('replies.parent_id IS NULL')
            ->orderBy('replies.is_best_answer', 'DESC')
            ->orderBy('replies.created_at', 'ASC')
            ->findAll();

        // Get all reply IDs to fetch attachments in one query
        $allReplyIds = array_map(function($reply) {
            return is_object($reply) ? $reply->id : $reply['id'];
        }, $replies);

        // Get nested replies
        $nestedReplies = [];
        if (!empty($allReplyIds)) {
            $nestedResults = $this->select('replies.*, system_users.first_name, system_users.last_name, system_users.picture, system_users.username')
                ->join('system_users', 'system_users.id = replies.user_id')
                ->where('replies.discussion_id', $discussionId)
                ->where('replies.status', 'active')
                ->whereIn('replies.parent_id', $allReplyIds)
                ->orderBy('replies.parent_id', 'ASC')
                ->orderBy('replies.created_at', 'ASC')
                ->findAll();

            // Add nested reply IDs to the list for attachment fetching
            foreach ($nestedResults as $nestedReply) {
                $nestedReplyId = is_object($nestedReply) ? $nestedReply->id : $nestedReply['id'];
                $allReplyIds[] = $nestedReplyId;
                $parentId = is_object($nestedReply) ? $nestedReply->parent_id : $nestedReply['parent_id'];
                $nestedReplies[$parentId][] = $nestedReply;
            }
        }

        // Get all attachments for all replies in one query
        $attachments = [];
        if (!empty($allReplyIds)) {
            $fileAttachmentModel = new \App\Models\FileAttachment();
            $allAttachments = $fileAttachmentModel->where('attachable_type', 'reply')
                ->whereIn('attachable_id', $allReplyIds)
                ->findAll();

            // Group attachments by reply ID
            foreach ($allAttachments as $attachment) {
                $replyId = is_object($attachment) ? $attachment->attachable_id : $attachment['attachable_id'];
                $attachments[$replyId][] = [
                    'id'            => is_object($attachment) ? $attachment->id : $attachment['id'],
                    'original_name' => is_object($attachment) ? $attachment->original_name : $attachment['original_name'],
                    'file_name'     => is_object($attachment) ? $attachment->file_name : $attachment['file_name'],
                    'file_path'     => is_object($attachment) ? $attachment->file_path : $attachment['file_path'],
                    'file_type'     => is_object($attachment) ? $attachment->file_type : $attachment['file_type'],
                    'file_size'     => is_object($attachment) ? $attachment->file_size : $attachment['file_size'],
                    'mime_type'     => is_object($attachment) ? $attachment->mime_type : $attachment['mime_type'],
                    'is_image'      => is_object($attachment) ? $attachment->is_image : $attachment['is_image'],
                    'download_url'  => base_url("ksp/discussion/download-attachment/" . (is_object($attachment) ? $attachment->file_path : $attachment['file_path'])),
                    'view_url'      => base_url("ksp/discussion/view-attachment/" . (is_object($attachment) ? $attachment->file_path : $attachment['file_path']))
                ];
            }
        }

        // Add attachments to replies
        foreach ($replies as &$reply) {
            $replyId = is_object($reply) ? $reply->id : $reply['id'];
            $reply->attachments = $attachments[$replyId] ?? [];
        }

        // Add attachments to nested replies
        foreach ($nestedReplies as &$nestedReplyGroup) {
            foreach ($nestedReplyGroup as &$nestedReply) {
                $nestedReplyId = is_object($nestedReply) ? $nestedReply->id : $nestedReply['id'];
                $nestedReply->attachments = $attachments[$nestedReplyId] ?? [];
            }
        }

        return [
            'replies'       => $replies, 
            'nestedReplies' => $nestedReplies
        ];
    }
}