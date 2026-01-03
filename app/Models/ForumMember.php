<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class ForumMember extends Model
{
    protected $table            = 'forum_members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'user_id',
        'forum_id',
        'joined_at',
        'is_blocked',
    ];
    protected $useTimestamps = false;

    protected $beforeInsert = ['generateUUID'];

    /**
     * Generate UUID for the primary key if not set
     */
    protected function generateUUID(array $data)
    {
        if (empty($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get forum members for DataTables
     */
    public function getForumMembersTable($start, $length, $search = null, $orderBy = 'joined_at', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->builder()
                    ->select('forum_members.id,
                             forum_members.user_id,
                             forum_members.forum_id,
                             forum_members.joined_at,
                             COALESCE(forum_members.is_blocked, 0) as is_blocked,
                             system_users.picture as member_profile, 
                             CONCAT(COALESCE(system_users.first_name, ""), " ", COALESCE(system_users.last_name, "")) as member_name, 
                             system_users.email as email,
                             IF(forum_moderators.id IS NOT NULL AND forum_moderators.is_active = 1, "moderator", "member") as role')
                    ->join('system_users', "system_users.id = forum_members.user_id COLLATE utf8mb4_unicode_ci", 'left', false)
                    ->join('forum_moderators', 'forum_moderators.user_id = forum_members.user_id AND forum_moderators.forum_id = forum_members.forum_id AND forum_moderators.is_active = 1', 'left', false)
                    ->groupBy('forum_members.id');

        // Filter by forum if forumId is provided
        if ($forumId) {
            $builder->where('forum_members.forum_id', $forumId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('forum_members.id', $search)
                    ->orLike('system_users.username', $search)
                    ->orLike('system_users.first_name', $search)
                    ->orLike('system_users.last_name', $search)
                    ->orLike('system_users.email', $search)
                    ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                       ->limit($length, $start)
                       ->get()
                       ->getResult();
    }

    /**
     * Count forum members
     */
    public function countMembers(string $forumId = null): int
    {
        $builder = $this->builder();
        
        if ($forumId) {
            $builder->where('forum_id', $forumId);
        }
        
        return $builder->countAllResults();
    }

    /**
     * Get members by forum ID
     */
    public function getMembersByForumId(string $forumId)
    {
        return $this->select('forum_members.*, CONCAT(system_users.first_name, " ", system_users.last_name) as user_name, system_users.picture as user_image, system_users.email as user_email')
                    ->join('system_users', 'system_users.id = forum_members.user_id', 'left', false)
                    ->where('forum_id', $forumId)
                    ->orderBy('forum_members.joined_at', 'DESC')
                    ->findAll();
    }
}
