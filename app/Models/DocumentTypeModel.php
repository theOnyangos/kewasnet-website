<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class DocumentTypeModel extends Model
{
    protected $table = 'document_types';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id',
        'name',
        'slug',
        'color'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[50]|is_unique[document_types.slug,id,{id}]',
        'color' => 'max_length[20]',
    ];

    protected $beforeInsert = ['generateUUID'];
    
    /**
     * Get all document types
     */
    public function getAllDocumentTypes()
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
    
    /**
     * Get document type with resource count
     */
    public function getDocumentTypesWithResourceCount()
    {
        return $this->select('document_types.*, COUNT(resources.id) as resource_count')
            ->join('resources', 'resources.document_type_id = document_types.id AND resources.is_published = 1 AND resources.deleted_at IS NULL', 'left')
            ->groupBy('document_types.id')
            ->orderBy('document_types.name', 'ASC')
            ->findAll();
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
