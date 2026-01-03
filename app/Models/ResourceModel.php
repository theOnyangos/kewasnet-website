<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class ResourceModel extends Model
{
    protected $table = 'resources';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'id',
        'pillar_id',
        'category_id',
        'document_type_id',
        'title',
        'slug',
        'description',
        'content',
        'image_url',
        'file_url',
        'file_path',
        'file_size',
        'file_type',
        'publication_year',
        'download_count',
        'view_count',
        'is_featured',
        'is_published',
        'meta_title',
        'meta_description',
        'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'title'     => 'required|min_length[3]|max_length[255]',
        'slug'      => 'required|min_length[3]|max_length[255]|is_unique[resources.slug,id,{id}]',
        'description' => 'required|min_length[10]',
        'is_published'    => 'required|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'title' => [
            'required' => 'Resource title is required.',
            'min_length' => 'Resource title must be at least 3 characters long.',
            'max_length' => 'Resource title cannot exceed 255 characters.'
        ],
        'slug' => [
            'required' => 'Resource slug is required.',
            'min_length' => 'Resource slug must be at least 3 characters long.',
            'max_length' => 'Resource slug cannot exceed 255 characters.',
            'is_unique' => 'This slug is already in use.'
        ],
        'description' => [
            'required' => 'Resource description is required.',
            'min_length' => 'Resource description must be at least 10 characters long.'
        ],
        'is_published' => [
            'required' => 'Resource publication status is required.',
            'in_list' => 'Resource must be either published (1) or unpublished (0).'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Generate UUID for new records
     */
    protected $beforeInsert = ['generateUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            helper('text');
            $data['data']['id'] = (string) service('uuid')->uuid4();
        }
        return $data;
    }
    
    /**
     * Get all published resources with basic info
     */
    public function getPublishedResources($limit = null, $offset = 0)
    {
        $builder = $this->select('resources.*')
            ->where('resources.is_published', 1)
            ->where('resources.deleted_at', null)
            ->orderBy('resources.created_at', 'DESC');
            
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get featured resources
     */
    public function getFeaturedResources($limit = 5)
    {
        return $this->select('resources.*')
                    ->where('resources.is_published', 1)
                    ->where('resources.is_featured', 1)
                    ->orderBy('resources.created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }
    
    /**
     * Search resources
     */
    public function searchResources($search, $categoryId = null, $documentTypeId = null, $pillarId = null, $limit = null, $offset = 0)
    {
        $builder = $this->select('resources.*')
            ->where('resources.is_published', 1)
            ->where('resources.deleted_at', null);
            
        if (!empty($search)) {
            $builder->groupStart()
                ->like('resources.title', $search)
                ->orLike('resources.description', $search)
                ->orLike('resources.content', $search)
                ->groupEnd();
        }
        
        if ($categoryId) {
            $builder->where('resources.category_id', $categoryId);
        }
        
        if ($documentTypeId) {
            $builder->where('resources.document_type_id', $documentTypeId);
        }
        
        if ($pillarId) {
            $builder->where('resources.pillar_id', $pillarId);
        }
        
        $builder->orderBy('resources.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get resources by category
     */
    public function getResourcesByCategory($categoryId, $limit = null, $offset = 0)
    {
        $builder = $this->select('resources.*')
            ->where('resources.category_id', $categoryId)
            ->where('resources.is_published', 1)
            ->where('resources.deleted_at', null)
            ->orderBy('resources.created_at', 'DESC');
            
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get published resources count for pagination
     */
    public function getPublishedResourcesCount($criteria = [], $search = '')
    {
        $builder = $this->where('is_published', 1);
        
        if (!empty($criteria['category_id'])) {
            $builder->where('category_id', $criteria['category_id']);
        }
        
        if (!empty($criteria['document_type_id'])) {
            $builder->where('document_type_id', $criteria['document_type_id']);
        }
        
        if (!empty($criteria['pillar_id'])) {
            $builder->where('pillar_id', $criteria['pillar_id']);
        }
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('title', $search)
                    ->orLike('description', $search)
                    ->orLike('content', $search)
                    ->groupEnd();
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get resource by slug
     */
    public function getBySlug($slug)
    {
        return $this->select('resources.*')
            ->where('resources.slug', $slug)
            ->where('resources.is_published', 1)
            ->where('resources.deleted_at', null)
            ->first();
    }
    
    /**
     * Increment download count
     */
    public function incrementDownloadCount($id)
    {
        return $this->set('download_count', 'download_count + 1', false)
                    ->where('id', $id)
                    ->update();
    }
    
    /**
     * Increment view count
     */
    public function incrementViewCount($id)
    {
        return $this->set('view_count', 'view_count + 1', false)
                    ->where('id', $id)
                    ->update();
    }
    
    /**
     * Get most downloaded resources
     */
    public function getMostDownloaded($limit = 10)
    {
        return $this->select('resources.*')
            ->where('resources.is_published', 1)
            ->where('resources.deleted_at', null)
            ->orderBy('resources.download_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get recently published resources
     */
    public function getRecentlyPublished($limit = 10)
    {
        return $this->select('resources.*')
            ->where('resources.is_published', 1)
            ->where('resources.deleted_at', null)
            ->orderBy('resources.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get resources with advanced filtering
     */
    public function getFilteredResources($criteria = [], $search = null, $limit = 10, $page = 1)
    {
        $builder = $this->select('resources.*')
                        ->where('resources.is_published', 1);

        // Apply criteria filters
        if (!empty($criteria['category_id'])) {
            $builder->where('resources.category_id', $criteria['category_id']);
        }
        
        if (!empty($criteria['document_type_id'])) {
            $builder->where('resources.document_type_id', $criteria['document_type_id']);
        }
        
        if (!empty($criteria['pillar_id'])) {
            $builder->where('resources.pillar_id', $criteria['pillar_id']);
        }

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('resources.title', $search)
                    ->orLike('resources.description', $search)
                    ->orLike('resources.content', $search)
                    ->groupEnd();
        }

        $offset = ($page - 1) * $limit;
        return $builder->orderBy('resources.created_at', 'DESC')
                      ->limit($limit, $offset)
                      ->find();
    }
    
    /**
     * Get resource with related data
     */
    public function getResourceWithRelations($id)
    {
        $resource = $this->find($id);
        if (!$resource) return null;
        
        // Set default values for missing fields
        $resource['category_name'] = '';
        $resource['document_type_name'] = '';
        $resource['document_type_color'] = '#2563eb';
        $resource['pillar_name'] = '';
        
        // Get related data separately to avoid collation issues
        if (!empty($resource['category_id'])) {
            $categoryModel = model('App\Models\ResourceCategoryModel');
            $category = $categoryModel->find($resource['category_id']);
            $resource['category_name'] = $category ? $category['name'] : '';
        }
        
        if (!empty($resource['document_type_id'])) {
            $documentTypeModel = model('App\Models\DocumentTypeModel');
            $docType = $documentTypeModel->find($resource['document_type_id']);
            $resource['document_type_name'] = $docType ? $docType['name'] : '';
            $resource['document_type_color'] = $docType ? $docType['color'] : '#2563eb';
        }
        
        if (!empty($resource['pillar_id'])) {
            $pillarModel = model('App\Models\PillarModel');
            $pillar = $pillarModel->find($resource['pillar_id']);
            $resource['pillar_name'] = $pillar ? $pillar['title'] : '';
        }
        
        // Ensure numeric fields have default values
        $resource['download_count'] = $resource['download_count'] ?? 0;
        $resource['view_count'] = $resource['view_count'] ?? 0;
        
        return $resource;
    }
    
    /**
     * Get resources with relations (for multiple resources)
     */
    public function getResourcesWithRelations($resources)
    {
        if (empty($resources)) return [];
        
        $categoryModel = model('App\Models\ResourceCategoryModel');
        $documentTypeModel = model('App\Models\DocumentTypeModel');
        
        foreach ($resources as &$resource) {
            // Set default values for missing fields
            $resource['category_name'] = '';
            $resource['document_type_name'] = '';
            $resource['document_type_color'] = '#2563eb';
            
            // Get category name
            if (!empty($resource['category_id'])) {
                $category = $categoryModel->find($resource['category_id']);
                $resource['category_name'] = $category ? $category['name'] : '';
            }
            
            // Get document type name and color
            if (!empty($resource['document_type_id'])) {
                $docType = $documentTypeModel->find($resource['document_type_id']);
                $resource['document_type_name'] = $docType ? $docType['name'] : '';
                $resource['document_type_color'] = $docType ? $docType['color'] : '#2563eb';
            }
            
            // Ensure numeric fields have default values
            $resource['download_count'] = $resource['download_count'] ?? 0;
            $resource['view_count'] = $resource['view_count'] ?? 0;
        }
        
        return $resources;
    }

    /**
     * Get resource count by category type (for static categories)
     */
    public function getResourceCountByCategory($categoryType)
    {
        // Map category types to search patterns or specific logic
        $searchPatterns = [
            'policy' => ['policy', 'advocacy', 'governance', 'strategic'],
            'training' => ['training', 'capacity', 'workshop', 'education', 'learning'],
            'research' => ['research', 'study', 'report', 'analysis', 'survey'],
            'guidelines' => ['guideline', 'standard', 'framework', 'protocol', 'best practice'],
            'tools' => ['tool', 'template', 'checklist', 'form', 'toolkit']
        ];

        if (!isset($searchPatterns[$categoryType])) {
            return 0;
        }

        $patterns = $searchPatterns[$categoryType];
        $builder = $this->where('is_published', 1);

        // Build OR condition for title and description containing any of the patterns
        $builder->groupStart();
        foreach ($patterns as $index => $pattern) {
            if ($index === 0) {
                $builder->like('title', $pattern, 'both', null, true);
                $builder->orLike('description', $pattern, 'both', null, true);
            } else {
                $builder->orLike('title', $pattern, 'both', null, true);
                $builder->orLike('description', $pattern, 'both', null, true);
            }
        }
        $builder->groupEnd();

        return $builder->countAllResults();
    }

    /**
     * Get filtered resources by category type with pagination
     */
    public function getFilteredResourcesByType($criteria = [], $search = '', $perPage = 10, $page = 1)
    {
        $builder = $this->where('is_published', 1);

        // Apply category type filtering
        if (isset($criteria['category_type'])) {
            $this->applyCategoryTypeFilter($builder, $criteria['category_type']);
        }

        // Apply document type filtering
        if (isset($criteria['document_type_id']) && !empty($criteria['document_type_id'])) {
            $builder->where('document_type_id', $criteria['document_type_id']);
        }

        // Apply search filtering
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('title', $search, 'both', null, true)
                    ->orLike('description', $search, 'both', null, true)
                    ->orLike('content', $search, 'both', null, true)
                    ->groupEnd();
        }

        $offset = ($page - 1) * $perPage;
        return $builder->orderBy('created_at', 'DESC')
                      ->limit($perPage, $offset)
                      ->findAll();
    }

    /**
     * Get count of published resources by type with filtering
     */
    public function getPublishedResourcesCountByType($criteria = [], $search = '')
    {
        $builder = $this->where('is_published', 1);

        // Apply category type filtering
        if (isset($criteria['category_type'])) {
            $this->applyCategoryTypeFilter($builder, $criteria['category_type']);
        }

        // Apply document type filtering
        if (isset($criteria['document_type_id']) && !empty($criteria['document_type_id'])) {
            $builder->where('document_type_id', $criteria['document_type_id']);
        }

        // Apply search filtering
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('title', $search, 'both', null, true)
                    ->orLike('description', $search, 'both', null, true)
                    ->orLike('content', $search, 'both', null, true)
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Apply category type filter to builder
     */
    private function applyCategoryTypeFilter($builder, $categoryType)
    {
        $searchPatterns = [
            'policy' => ['policy', 'advocacy', 'governance', 'strategic'],
            'training' => ['training', 'capacity', 'workshop', 'education', 'learning'],
            'research' => ['research', 'study', 'report', 'analysis', 'survey'],
            'guidelines' => ['guideline', 'standard', 'framework', 'protocol', 'best practice'],
            'tools' => ['tool', 'template', 'checklist', 'form', 'toolkit']
        ];

        if (isset($searchPatterns[$categoryType])) {
            $patterns = $searchPatterns[$categoryType];
            $builder->groupStart();
            foreach ($patterns as $index => $pattern) {
                if ($index === 0) {
                    $builder->like('title', $pattern, 'both', null, true);
                    $builder->orLike('description', $pattern, 'both', null, true);
                } else {
                    $builder->orLike('title', $pattern, 'both', null, true);
                    $builder->orLike('description', $pattern, 'both', null, true);
                }
            }
            $builder->groupEnd();
        }
    }
}
