<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class BlogPostView extends Model
{
    protected $table = 'blog_post_views';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id', 'post_id', 'ip_address', 'user_agent'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    // Important: BaseModel only checks for '' (empty string), not null.
    // Setting null causes it to insert an empty column name, leading to SQL errors.
    protected $updatedField = ''; // Table doesn't have updated_at column
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Record a view for a post
     */
    public function recordView($postId, $ipAddress, $userAgent, $userId = null)
    {
        // Check if this IP already viewed the post today
        $today = date('Y-m-d');
        $existing = $this->where('post_id', $postId)
                       ->where('ip_address', $ipAddress)
                       ->where('DATE(created_at)', $today)
                       ->first();

        if (!$existing) {
            // Generate UUID for the view record
            try {
                $viewId = \Ramsey\Uuid\Uuid::uuid4()->toString();
            } catch (\Exception $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                return false;
            }
            
            // Use query builder directly to avoid model callback issues
            $db = \Config\Database::connect();
            $now = date('Y-m-d H:i:s');
            
            $data = [
                'id' => $viewId,
                'post_id' => $postId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'created_at' => $now
            ];
            
            return $db->table('blog_post_views')->insert($data);
        }

        return false;
    }

    /**
     * Get total views for a post
     */
    public function getTotalViews($postId)
    {
        return $this->where('post_id', $postId)->countAllResults();
    }

    /**
     * Get unique visitor count for a post
     */
    public function getUniqueVisitors($postId)
    {
        return $this->select('COUNT(DISTINCT ip_address) as unique_visitors')
                   ->where('post_id', $postId)
                   ->get()
                   ->getRow()
                   ->unique_visitors;
    }

    /**
     * Get popular posts
     */
    public function getPopularPosts($limit = 5, $timeframe = '30 DAY')
    {
        return $this->select('post_id, COUNT(*) as view_count')
                   ->select('blog_posts.title, blog_posts.slug, blog_posts.featured_image')
                   ->join('blog_posts', 'blog_posts.id = blog_post_views.post_id')
                   ->where('blog_posts.status', 'published')
                   ->where("blog_post_views.created_at >= DATE_SUB(NOW(), INTERVAL {$timeframe})")
                   ->groupBy('post_id')
                   ->orderBy('view_count', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}