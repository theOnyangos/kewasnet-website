<?php namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class DocumentType extends Model
{
    protected $table = 'document_types';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id',
        'name',
        'slug',
        'color',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[50]|is_unique[document_types.slug]',
        'color' => 'permit_empty|max_length[20]'
    ];
    
    protected $validationMessages = [
        'slug' => [
            'is_unique' => 'This document type slug is already in use.'
        ]
    ];
    
    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = ['ensureUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                unset($data['data']['id']); // Let database handle it
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
    
    /**
     * Get document type by slug
     */
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }
    
    /**
     * Get all document types as options for dropdown
     */
    public function getDropdownOptions()
    {
        $types = $this->orderBy('name', 'ASC')->findAll();
        $options = [];
        
        foreach ($types as $type) {
            $options[$type->id] = $type->name;
        }
        
        return $options;
    }

    public function getDocumentTypesTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->select('document_types.*, COUNT(resources.id) as resources_count')
            ->join('resources', 'resources.document_type_id = document_types.id', 'left');

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('document_types.name', $search)
                ->orLike('document_types.slug', $search)
                ->groupEnd();
        }

        $builder->groupBy('document_types.id');
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        return $builder->get()->getResult();
    }

    public function getTotalDocumentTypesCount()
    {
        return $this->countAllResults();
    }

    public function createDocumentType(array $data): bool
    {
        $this->insert($data);

        return $this->getInsertID();
    }

    public function getActiveDocumentTypes()
    {
        return $this->orderBy('name', 'ASC')
                    ->findAll();
    }
}