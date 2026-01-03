<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Forum;
use App\Models\Discussion;
use App\Models\ForumModerator;
use App\Models\ForumMember;

class NetworkCornerService
{
    protected $forumModel;
    protected $discussionModel;
    protected $forumModeratorModel;
    protected $forumMemberModel;

    public function __construct()
    {
        $this->forumModel = new Forum();
        $this->discussionModel = new Discussion();
        $this->forumMemberModel = new ForumMember();
        $this->forumModeratorModel = new ForumModerator();
    }

    public function getRecentDiscussions($limit = 10)
    {
        return $this->discussionModel->orderBy('created_at', 'DESC')->findAll($limit);
    }

    public function getRecentForums($limit = 9)
    {
        return $this->forumModel->orderBy('created_at', 'DESC')->findAll($limit);
    }

    public function getForumBySlug($slug)
    {
        return $this->forumModel->getForumBySlug($slug);
    }

    public function getForumDiscussionCount($forumId)
    {
        return $this->discussionModel->where('forum_id', $forumId)->countAllResults();
    }

    public function getForumById($forumId)
    {
        return $this->forumModel->getForumById($forumId);
    }

    public function joinForum($forumId, $userId)
    {
        // Logic to join a forum
        $memberData = [
            'forum_id' => $forumId,
            'user_id'  => $userId,
            'joined_at' => Carbon::now()
        ];

        return $this->forumMemberModel->insert($memberData);
    }

    public function leaveForum($forumId, $userId)
    {
        // Get all discussion IDs in this forum
        $discussionModel = new \App\Models\Discussion();
        $discussions = $discussionModel->where('forum_id', $forumId)->findAll();
        
        // Delete user's bookmarks for discussions in this forum
        if (!empty($discussions)) {
            $discussionIds = array_column($discussions, 'id');
            $bookmarkModel = new \App\Models\Bookmark();
            $bookmarkModel->where('user_id', $userId)
                         ->whereIn('discussion_id', $discussionIds)
                         ->delete();
        }
        
        // Remove forum membership
        return $this->forumMemberModel->where('forum_id', $forumId)->where('user_id', $userId)->delete();
    }

    public function checkUserMembership($forumId, $userId): array
    {
        $result = $this->forumMemberModel->where('forum_id', $forumId)->where('user_id', $userId)->first();
        $isMember = $result !== null;

        return [
            'is_member' => $isMember,
            'joined_at' => $result ? $result->joined_at : null,
            'is_blocked' => $result ? ($result->is_blocked == 1) : false
        ];
    }

    public function getForumModerators($forumId): array
    {
        return $this->forumModeratorModel->getModeratorsByForumId($forumId);
    }

    public function checkUserIsModerator($forumId, $userId): array
    {
        $result = $this->forumModeratorModel->where('forum_id', $forumId)->where('user_id', $userId)->first();
        $isModerator = $result !== null;

        return [
            'is_moderator' => $isModerator,
            'assigned_at'  => $result ? $result->assigned_at : null
        ];
    }

    public function getForumDiscussions($forumId): array
    {
        return $this->discussionModel->getForumDiscussionsByForumId($forumId);
    }

    public function getForumClientDiscussionByForumId($forumId)
    {
        return $this->discussionModel->getForumClientDiscussionByForumId($forumId);
    }

    public function getAllForums(): array
    {
        return $this->forumModel->select('id, name')->findAll();
    }
}
