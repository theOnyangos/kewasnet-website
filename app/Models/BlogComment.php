<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class BlogComment extends Model
{
    protected $table = 'blog_comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id', 'blog_post_id', 'user_id', 'parent_comment_id', 'author_name', 'author_email',
        'content', 'status', 'comment_type', 'author_type', 'ip_address', 'user_agent',
        'is_featured', 'likes_count', 'replies_count'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $beforeInsert = ['generateUUID', 'setAuthorType'];
    protected $beforeUpdate = ['updateRepliesCount'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    protected function setAuthorType(array $data)
    {
        // Set author type based on whether user_id is provided
        if (!isset($data['data']['author_type'])) {
            $data['data']['author_type'] = !empty($data['data']['user_id']) ? 'registered' : 'anonymous';
        }
        
        // Set comment type based on parent_comment_id
        if (!isset($data['data']['comment_type'])) {
            $data['data']['comment_type'] = !empty($data['data']['parent_comment_id']) ? 'reply' : 'comment';
        }
        
        return $data;
    }

    protected function updateRepliesCount(array $data)
    {
        // This could be used to automatically update reply counts
        return $data;
    }

    // Get comments for a specific blog post
    public function getCommentsForPost($blogPostId, $status = 'approved', $includeReplies = true)
    {
        $builder = $this->select('blog_comments.*, system_users.name as user_name, system_users.profile_picture')
            ->join('system_users', 'system_users.id = blog_comments.user_id', 'left')
            ->where('blog_comments.blog_post_id', $blogPostId)
            ->where('blog_comments.status', $status)
            ->orderBy('blog_comments.created_at', 'ASC');

        if (!$includeReplies) {
            $builder->where('blog_comments.parent_comment_id IS NULL');
        }

        return $builder->findAll();
    }

    // Get replies for a specific comment
    public function getRepliesForComment($commentId, $status = 'approved')
    {
        return $this->select('blog_comments.*, system_users.name as user_name, system_users.profile_picture')
            ->join('system_users', 'system_users.id = blog_comments.user_id', 'left')
            ->where('blog_comments.parent_comment_id', $commentId)
            ->where('blog_comments.status', $status)
            ->orderBy('blog_comments.created_at', 'ASC')
            ->findAll();
    }

    // Get comment statistics
    public function getCommentStats($blogPostId = null)
    {
        $builder = $this->db->table($this->table);
        
        if ($blogPostId) {
            $builder->where('blog_post_id', $blogPostId);
        }

        $stats = [
            'total' => $builder->countAllResults(false),
            'approved' => $builder->where('status', 'approved')->countAllResults(false),
            'pending' => $builder->where('status', 'pending')->countAllResults(false),
            'rejected' => $builder->where('status', 'rejected')->countAllResults(false),
            'spam' => $builder->where('status', 'spam')->countAllResults(false),
        ];

        return $stats;
    }

    // Approve a comment
    public function approveComment($commentId)
    {
        return $this->update($commentId, ['status' => 'approved']);
    }

    // Reject a comment
    public function rejectComment($commentId)
    {
        return $this->update($commentId, ['status' => 'rejected']);
    }

    // Mark as spam
    public function markAsSpam($commentId)
    {
        return $this->update($commentId, ['status' => 'spam']);
    }

    // Get comments for DataTable (admin)
    public function getCommentsTable($request = null)
    {
        $builder = $this->select('
                blog_comments.*,
                system_users.name as user_name,
                system_users.email as user_email,
                blog_posts.title as blog_title
            ')
            ->join('system_users', 'system_users.id = blog_comments.user_id', 'left')
            ->join('blog_posts', 'blog_posts.id = blog_comments.blog_post_id', 'left');

        // Apply search
        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $builder->groupStart()
                ->like('blog_comments.content', $search)
                ->orLike('blog_comments.author_name', $search)
                ->orLike('blog_comments.author_email', $search)
                ->orLike('users.name', $search)
                ->orLike('blog_posts.title', $search)
                ->groupEnd();
        }

        // Apply ordering
        if (!empty($request['order'])) {
            $columnIndex = $request['order'][0]['column'];
            $columnDir = $request['order'][0]['dir'];
            
            $columns = ['content', 'author_name', 'status', 'created_at'];
            if (isset($columns[$columnIndex])) {
                $builder->orderBy('blog_comments.' . $columns[$columnIndex], $columnDir);
            }
        } else {
            $builder->orderBy('blog_comments.created_at', 'DESC');
        }

        return [
            'builder' => $builder,
            'total' => $this->countAllResults(false)
        ];
    }

    // Update replies count for parent comment
    public function updateParentRepliesCount($parentCommentId)
    {
        $repliesCount = $this->where('parent_comment_id', $parentCommentId)
            ->where('status', 'approved')
            ->countAllResults();
            
        return $this->update($parentCommentId, ['replies_count' => $repliesCount]);
    }
}
