<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class DiscussionTag extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'discussion_tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'name',
        'slug',
        'color',
        'usage_count',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'name'  => 'required|max_length[50]',
        'slug'  => 'required|max_length[50]|is_unique[discussion_tags.slug,id,{id}]',
        'color' => 'permit_empty|valid_color'
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Tag name is required',
            'max_length' => 'Tag name cannot exceed 50 characters'
        ],
        'slug' => [
            'required'   => 'Tag slug is required',
            'max_length' => 'Tag slug cannot exceed 50 characters',
            'is_unique'  => 'This tag slug is already in use'
        ],
        'color' => [
            'valid_color' => 'Please provide a valid hex color code'
        ]
    ];

    protected $beforeInsert = ['generateUUID', 'generateSlug'];
    protected $beforeUpdate = ['ensureSlugUniqueness'];

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

    /**
     * Generates slug from name if not provided
     */
    protected function generateSlug(array $data)
    {
        if (empty($data['data']['slug']) && !empty($data['data']['name'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }

    /**
     * Ensures slug uniqueness on update
     */
    protected function ensureSlugUniqueness(array $data)
    {
        if (isset($data['data']['slug'])) {
            $existing = $this->where('slug', $data['data']['slug'])
                           ->where('id !=', $data['id'][0])
                           ->first();
            
            if ($existing) {
                $data['data']['slug'] = $data['data']['slug'] . '-' . substr($data['id'][0], 0, 8);
            }
        }
        return $data;
    }

    /**
     * Custom validation rule for color field
     */
    public function valid_color(string $color = null)
    {
        if (empty($color)) return true;
        return preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color);
    }

    /**
     * Increments usage count for a tag
     */
    public function incrementUsage(string $tagId)
    {
        return $this->builder()
            ->where('id', $tagId)
            ->set('usage_count', 'usage_count + 1', false)
            ->update();
    }

    /**
     * Decrements usage count for a tag
     */
    public function decrementUsage(string $tagId)
    {
        return $this->builder()
            ->where('id', $tagId)
            ->set('usage_count', 'GREATEST(0, usage_count - 1)', false)
            ->update();
    }

    /**
     * Gets popular tags ordered by usage count
     */
    public function getPopularTags(int $limit = 10)
    {
        return $this->orderBy('usage_count', 'DESC')
                   ->findAll($limit);
    }

    /**
     * Finds or creates a tag by name
     */
    public function findOrCreate(string $name, string $color = null)
    {
        $slug = url_title($name, '-', true);
        
        $existing = $this->where('slug', $slug)->first();
        
        if ($existing) {
            return $existing->id;
        }
        
        $newTag = [
            'name'  => $name,
            'slug'  => $slug,
            'color' => $color ?? '#6B7280'
        ];
        
        return $this->insert($newTag);
    }

    /**
     * Searches tags by name
     */
    public function search(string $query, int $limit = 10)
    {
        return $this->like('name', $query)
                   ->orderBy('usage_count', 'DESC')
                   ->findAll($limit);
    }
}