<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class BlogPostCategory extends Model
{
    protected $table = 'blog_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'deleted_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    // Helper method to create slug
    public function createSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        return $slug;
    }

    // Get all active categories
    public function getActiveCategories()
    {
        return $this->where('deleted_at', null)->findAll();
    }

    public function getCategoriesTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
                        ->select('blog_categories.*, 
                                 (SELECT COUNT(*) FROM blog_posts WHERE blog_posts.category_id = blog_categories.id AND blog_posts.deleted_at IS NULL) as post_count')
                        ->where('blog_categories.deleted_at', null);

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('description', $search)
                    ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);
        // Apply pagination
        $builder->limit($length, $start);
        return $builder->get()->getResult();
    }

    public function countCategories(): int
    {
        return $this->where('deleted_at', null)->countAllResults();
    }
}