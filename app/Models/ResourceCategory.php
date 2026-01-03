<?php namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class ResourceCategory extends Model
{
    protected $table = 'resource_categories';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id',
        'pillar_id',
        'name',
        'slug',
        'description',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'pillar_id' => 'required',
        'name' => 'required|max_length[255]',
        'slug' => 'required|max_length[100]|is_unique[resource_categories.slug,pillar_id,{pillar_id}]',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    protected $beforeInsert = ['generateUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                // Fallback to database-generated UUID
                unset($data['data']['id']);
            }
        }
        return $data;
    }
    
    /**
     * Get categories by pillar ID
     */
    public function getByPillar($pillarId)
    {
        return $this->where('pillar_id', $pillarId)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }
    
    /**
     * Find category by slug and pillar ID
     */
    public function findBySlug($slug, $pillarId)
    {
        return $this->where('slug', $slug)
                   ->where('pillar_id', $pillarId)
                   ->first();
    }

    /**
     * Get categories with pagination and search for DataTables
     */
    public function getCategories($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder();

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResult();
    }

    /**
     * Get categories for DataTable
     */
    public function getCategoriesTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->select('resource_categories.*, COUNT(pillars.id) as pillar_count, pillars.title as pillar_title')
            ->join('pillars', 'pillars.id = resource_categories.pillar_id', 'left', false);

        if ($search) {
            $builder->groupStart()
                ->like('resource_categories.name', $search)
                ->orLike('resource_categories.description', $search)
                ->orLike('pillars.title', $search)
                ->groupEnd();
        }

        $builder->groupBy('resource_categories.id');
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);
        return $builder->get()->getResult();
    }

    /**
     * Count all categories
     */
    public function countAllCategories(): int
    {
        return $this->countAll();
    }

    /**
     * Count total categories for DataTable
     */
    public function getTotalCategoriesCount()
    {
        return $this->countAllResults();
    }

    /**
     * Create Resource Category
     */
    public function createResourceCategory(array $data): bool
    {
        $this->insert($data);

        return $this->getInsertID();
    }
}