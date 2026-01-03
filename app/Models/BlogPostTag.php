<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class BlogPostTag extends Model
{
    protected $table = 'blog_post_tags';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id', 'post_id', 'tag_id'];
    protected $useTimestamps = false;
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    public function getTagsForPost($postId)
    {
        return $this->select('blog_tags.*')
                   ->join('blog_tags', 'blog_tags.id = blog_post_tags.tag_id')
                   ->where('blog_post_tags.post_id', $postId)
                   ->findAll();
    }

    public function getPostsForTag($tagId, $limit = 10)
    {
        return $this->select('blog_posts.*')
                   ->join('blog_posts', 'blog_posts.id = blog_post_tags.post_id')
                   ->where('blog_post_tags.tag_id', $tagId)
                   ->where('blog_posts.status', 'published')
                   ->orderBy('blog_posts.published_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}