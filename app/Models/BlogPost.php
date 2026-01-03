<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class BlogPost extends Model
{
    protected $table = 'blog_posts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id', 'user_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'meta_title', 'meta_description', 'meta_keywords',
        'reading_time', 'views', 'is_featured', 'status', 'published_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = ['updateSlug'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    protected function setPublishedAt(array $data)
    {
        if ($data['data']['status'] === 'published' && empty($data['data']['published_at'])) {
            $data['data']['published_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    protected function updateSlug(array $data)
    {
        if (isset($data['data']['title']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = $this->createSlug($data['data']['title']);
        }
        return $data;
    }

    public function getFeaturedBlogPosts(int $limit = 3)
    {
        return $this->select('
                        blog_posts.id, 
                        blog_posts.slug, 
                        blog_posts.title, 
                        blog_posts.excerpt, 
                        blog_posts.featured_image, 
                        blog_posts.published_at,
                        blog_categories.name as category_name
                        ')
                    ->join('blog_categories', 'blog_categories.id = blog_posts.category_id', 'left')
                    ->where('blog_posts.is_featured', 1)
                    ->where('blog_posts.status', 'published')
                    ->groupBy('blog_posts.id')
                    ->orderBy('blog_posts.published_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function createSlug($title)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $posts = $this->like('slug', $slug, 'after')->findAll();
        
        if (!empty($posts)) {
            $slug .= '-' . (count($posts) + 1);
        }
        
        return $slug;
    }

    public function incrementViews($postId)
    {
        return $this->set('views', 'views+1', false)
                   ->where('id', $postId)
                   ->update();
    }

    public function getFeaturedPosts($limit = 2)
    {
        return $this->where('is_featured', 1)
                   ->where('status', 'published')
                   ->orderBy('published_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    public function getRecentPosts($limit = 6)
    {
        return $this->where('status', 'published')
                   ->orderBy('published_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    public function getBlogsTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->builder()
                        ->select('blog_posts.*, 
                                 blog_categories.name as category_name,
                                 CONCAT(system_users.first_name, system_users.last_name) as author_name,
                                 COALESCE(blog_posts.views, 0) as views,
                                 (SELECT COUNT(*) FROM blog_comments WHERE blog_comments.blog_post_id = blog_posts.id) as comments_count')
                        ->join('blog_categories', 'blog_categories.id = blog_posts.category_id', 'left', false)
                        ->join('blog_comments', 'blog_comments.blog_post_id = blog_posts.id', 'left', false)
                        ->join('system_users', 'system_users.id = blog_posts.user_id', 'left', false)
                        ->where('blog_posts.deleted_at', null); // Exclude soft-deleted records

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                    ->like('blog_posts.title', $search)
                    ->orLike('blog_posts.content', $search)
                    ->orLike('blog_categories.name', $search)
                    ->orLike('system_users.first_name', $search)
                    ->orLike('system_users.last_name', $search)
                    ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);
        // Apply pagination
        $builder->limit($length, $start);
        return $builder->get()->getResult();
    }

    public function countBlogPosts(): int
    {
        // Only count non-deleted records
        return $this->where('deleted_at', null)->countAllResults();
    }

    public function getBlogStats()
    {
        $builder = $this->db->table('blog_posts');
        
        $totalPosts = $builder->countAll();
        $publishedPosts = $builder->where('status', 'published')->countAllResults(false);
        $draftPosts = $builder->where('status', 'draft')->countAllResults(false);
        
        $totalViews = $this->db->table('blog_posts')
                              ->selectSum('views')
                              ->get()
                              ->getRow()
                              ->views ?? 0;
        
        $totalComments = $this->db->table('blog_comments')
                                 ->countAll();
        
        $totalCategories = $this->db->table('blog_categories')
                                   ->countAll();

        return (object) [
            'total_posts' => $totalPosts,
            'published_posts' => $publishedPosts,
            'draft_posts' => $draftPosts,
            'total_categories' => $totalCategories,
            'total_views' => $totalViews,
            'total_comments' => $totalComments
        ];
    }

    public function getDeletedBlogsTable($start, $length, $search = null, $orderBy = 'deleted_at', $orderDir = 'DESC')
    {
        $builder = $this->db->table('blog_posts')
            ->select('blog_posts.id, blog_posts.title, blog_posts.status, blog_posts.deleted_at, 
                     blog_categories.name as category_name, 
                     CONCAT(system_users.first_name, " ", system_users.last_name) as author_name')
            ->join('blog_categories', 'blog_categories.id = blog_posts.category_id', 'left')
            ->join('system_users', 'system_users.id = blog_posts.user_id', 'left')
            ->where('blog_posts.deleted_at IS NOT NULL');
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('blog_posts.title', $search)
                ->orLike('blog_categories.name', $search)
                ->orLike('system_users.first_name', $search)
                ->orLike('system_users.last_name', $search)
                ->groupEnd();
        }
        
        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);
        
        // Apply pagination
        $builder->limit($length, $start);
        
        return $builder->get()->getResultArray();
    }

    public function countDeletedBlogPosts($search = null)
    {
        $builder = $this->db->table('blog_posts')
            ->join('blog_categories', 'blog_categories.id = blog_posts.category_id', 'left')
            ->join('system_users', 'system_users.id = blog_posts.user_id', 'left')
            ->where('blog_posts.deleted_at IS NOT NULL');
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('blog_posts.title', $search)
                ->orLike('blog_categories.name', $search)
                ->orLike('system_users.first_name', $search)
                ->orLike('system_users.last_name', $search)
                ->groupEnd();
        }
        
        return $builder->countAllResults();
    }
}