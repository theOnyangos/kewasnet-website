<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Faq extends Model
{
    protected $table            = 'faqs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'question', 'answer', 'category', 'status', 'display_order', 
        'tags', 'is_featured', 'searchable'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id'            => 'permit_empty',
        'question'      => 'required|min_length[10]|max_length[255]',
        'answer'        => 'required|min_length[10]',
        'category'      => 'required|in_list[general,programs,membership,events,donations,technical]',
        'status'        => 'permit_empty|in_list[active,draft,archived]',
        'display_order' => 'permit_empty|integer',
        'tags'          => 'permit_empty|max_length[255]',
        'is_featured'   => 'permit_empty|in_list[0,1]',
        'searchable'    => 'permit_empty|in_list[0,1]',
    ];
    protected $validationMessages   = [
        'question' => [
            'required' => 'The question field is required.',
            'min_length' => 'The question must be at least 10 characters long.',
            'max_length' => 'The question cannot exceed 255 characters.',
        ],
        'answer' => [
            'required' => 'The answer field is required.',
            'min_length' => 'The answer must be at least 10 characters long.',
        ],
        'category' => [
            'required' => 'The category field is required.',
            'min_length' => 'The category must be at least 3 characters long.',
            'max_length' => 'The category cannot exceed 100 characters.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateUUID'];
    
    /**
     * Generate UUID for new records
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                // Fallback to database UUID function
                unset($data['data']['id']);
            }
        }
        
        return $data;
    }
    
    /**
     * Get FAQs by category
     *
     * @param string $category
     * @return array
     */
    public function getByCategory(string $category)
    {
        return $this->where('category', $category)->findAll();
    }
    
    /**
     * Get all unique categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->distinct()->select('category')->findAll();
    }
    
    /**
     * Search FAQs by keyword
     *
     * @param string $keyword
     * @return array
     */
    public function search(string $keyword)
    {
        return $this->like('question', $keyword)
                    ->orLike('answer', $keyword)
                    ->findAll();
    }
    
    /**
     * Find FAQ by UUID
     *
     * @param string $uuid
     * @return array|null
     */
    public function findByUuid(string $uuid)
    {
        return $this->where('id', $uuid)->first();
    }
}