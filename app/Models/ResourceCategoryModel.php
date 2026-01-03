<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class ResourceCategoryModel extends Model
{
    protected $table = 'resource_categories';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id',
        'pillar_id',
        'name',
        'slug',
        'description'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateUUID'];
    
    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'slug' => 'required|max_length[100]|is_unique[resource_categories.slug,id,{id}]',
        'pillar_id' => 'required',
    ];
    
    /**
     * Get all categories with pillar information
     */
    public function getCategoriesWithPillars()
    {
        return $this->select('resource_categories.*, pillars.title as pillar_name')
            ->join('pillars', 'pillars.id = resource_categories.pillar_id', 'left')
            ->orderBy('resource_categories.name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get categories by pillar
     */
    public function getCategoriesByPillar($pillarId)
    {
        return $this->where('pillar_id', $pillarId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get categories with resource count
     */
    public function getCategoriesWithResourceCount()
    {        
        // Get all categories first
        $categories = $this->select('resource_categories.*')
            ->orderBy('resource_categories.name', 'ASC')
            ->findAll();
            
        // Get resource count data separately to avoid collation issues
        $resourceModel = model('App\Models\ResourceModel');
        
        foreach ($categories as &$category) {
            // Count resources for this category
            $category['resource_count'] = $resourceModel->where('category_id', $category['id'])
                                                      ->where('is_published', 1)
                                                      ->where('deleted_at', null)
                                                      ->countAllResults();
        }
        
        return $categories;
    }

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
}