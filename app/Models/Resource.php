<?php namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Resource extends Model
{
    protected $table = 'resources';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
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
        'file_size',
        'file_type',
        'publication_year',
        'download_count',
        'view_count',
        'is_featured',
        'is_published',
        'meta_title',
        'meta_description',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'pillar_id'         => 'required',
        'document_type_id'  => 'required',
        'title'             => 'required|max_length[255]',
        'slug'              => 'required|max_length[255]|is_unique[resources.slug,id,{id}]',
        'publication_year'  => 'permit_empty|valid_date[Y]',
        'is_featured'       => 'permit_empty|in_list[0,1]',
        'is_published'      => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'slug' => [
            'is_unique' => 'This resource slug is already in use.'
        ]
    ];
    
    protected $beforeInsert = ['generateUUID', 'setCreatedBy'];
    protected $beforeUpdate = ['ensureUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                unset($data['data']['id']);
            }
        }
        return $data;
    }
    
    protected function ensureUUID(array $data)
    {
        if (isset($data['data']['id']) && !Uuid::isValid($data['data']['id'])) {
            unset($data['data']['id']);
        }
        return $data;
    }
    
    protected function setCreatedBy(array $data)
    {
        if (!isset($data['data']['created_by']) && auth()->loggedIn()) {
            $data['data']['created_by'] = auth()->id();
        }
        return $data;
    }
    
    /**
     * Get published resources with relations
     */
    public function getPublishedResources($limit = null, $offset = 0)
    {
        $builder = $this->where('is_published', 1)
                      ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get featured resources
     */
    public function getFeaturedResources($limit = 4)
    {
        return $this->where('is_published', 1)
                  ->where('is_featured', 1)
                  ->orderBy('created_at', 'DESC')
                  ->limit($limit)
                  ->findAll();
    }
    
    /**
     * Get resources by pillar
     */
    public function getByPillar($pillarId, $limit = null)
    {
        $builder = $this->where('pillar_id', $pillarId)
                      ->where('is_published', 1)
                      ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get resources by category
     */
    public function getByCategory($categoryId, $limit = null)
    {
        $builder = $this->where('category_id', $categoryId)
                      ->where('is_published', 1)
                      ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get resources by document type
     */
    public function getByDocumentType($typeId, $limit = null)
    {
        $builder = $this->where('document_type_id', $typeId)
                      ->where('is_published', 1)
                      ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Increment view count
     */
    public function incrementViews($resourceId)
    {
        return $this->set('view_count', 'view_count+1', false)
                  ->where('id', $resourceId)
                  ->update();
    }
    
    /**
     * Increment download count
     */
    public function incrementDownloads($resourceId)
    {
        return $this->set('download_count', 'download_count+1', false)
                  ->where('id', $resourceId)
                  ->update();
    }
    
    /**
     * Search resources
     */
    public function search($query, $pillarId = null, $categoryId = null, $typeId = null, $year = null)
    {
        $builder = $this->groupStart()
                      ->like('title', $query)
                      ->orLike('description', $query)
                      ->orLike('content', $query)
                      ->groupEnd()
                      ->where('is_published', 1);
        
        if ($pillarId) {
            $builder->where('pillar_id', $pillarId);
        }
        
        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }
        
        if ($typeId) {
            $builder->where('document_type_id', $typeId);
        }
        
        if ($year) {
            $builder->where('publication_year', $year);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                     ->findAll();
    }

    /**
     * Get resources with pagination and search for DataTables
     */
    public function getResources($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('resources.*, resource_categories.name as category_name, resources.title as file_name, resources.file_url as file_path, resources.download_count as downloads')
            ->join('resource_categories', 'CAST(resource_categories.id AS CHAR) = CAST(resources.category_id AS CHAR)', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('resources.title', $search)
                ->orLike('resources.description', $search)
                ->orLike('resources.content', $search)
                ->orLike('resource_categories.name', $search)
                ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResult();
    }

    /**
     * Get resource statistics
     */
    public function getResourceStats()
    {
        $builder = $this->builder();
        
        $totalResources = $builder->countAllResults(false);
        $publishedResources = $builder->where('is_published', 1)->countAllResults(false);
        $draftResources = $totalResources - $publishedResources;
        
        // Get total downloads
        $totalDownloads = $this->selectSum('download_count')->get()->getRow()->download_count ?? 0;

        return (object) [
            'total_resources' => $totalResources,
            'published_resources' => $publishedResources,
            'draft_resources' => $draftResources,
            'total_downloads' => $totalDownloads
        ];
    }

    public function getResourcesTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC', $forumId = null)
    {
        $db = \Config\Database::connect();
        
        $sql = "SELECT 
                    resources.*,
                    COALESCE(pillars.title, '') as pillar_name,
                    COALESCE(document_types.name, '') as document_type_name,
                    COALESCE(document_types.color, '') as document_type_color,
                    COALESCE(resource_categories.name, '') as category_name,
                    (
                        SELECT file_path 
                        FROM file_attachments 
                        WHERE attachable_id = resources.id 
                        AND attachable_type = 'resources' 
                        AND is_image = 0 
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ) as file_path,
                    (
                        SELECT download_count 
                        FROM file_attachments 
                        WHERE attachable_id = resources.id 
                        AND attachable_type = 'resources' 
                        AND is_image = 0 
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ) as downloaded_count
                FROM resources
                LEFT JOIN pillars ON CAST(pillars.id AS CHAR) = CAST(resources.pillar_id AS CHAR)
                LEFT JOIN document_types ON CAST(document_types.id AS CHAR) = CAST(resources.document_type_id AS CHAR)
                LEFT JOIN resource_categories ON CAST(resource_categories.id AS CHAR) = CAST(resources.category_id AS CHAR)";
        
        $whereClause = "";
        $params = [];
        
        // Apply search
        if ($search) {
            $whereClause = " WHERE (
                resources.title LIKE ? OR 
                resources.description LIKE ? OR 
                pillars.title LIKE ? OR 
                document_types.name LIKE ? OR 
                resource_categories.name LIKE ?
            )";
            $searchParam = "%{$search}%";
            $params = [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam];
        }
        
        $sql .= $whereClause;
        $sql .= " ORDER BY {$orderBy} {$orderDir}";
        $sql .= " LIMIT {$start}, {$length}";
        
        $query = $db->query($sql, $params);
        $results = $query->getResult();
        
        // Add download URLs to each result
        foreach ($results as $result) {
            if (!empty($result->file_path)) {
                $result->download_url = site_url("auth/pillars/download-attachment/{$result->file_path}");
            } else {
                $result->download_url = null;
            }
            // Remove the temporary file_path field if you don't want it in the output
            unset($result->file_path);
        }
        
        return $results;
    }

    public function getTotalResourcesCount()
    {
        return $this->countAllResults();
    }

    /**
     * Count all resources
     */
    public function countAllResources(): int
    {
        return $this->countAll();
    }
}