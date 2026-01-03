<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class DocumentResource extends Model
{
    protected $table            = 'resources';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Changed to false since we're using UUIDs
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'title',
        'slug',
        'description',
        'pillar_id',
        'category_id',
        'document_type_id',
        'publication_year',
        'image_url',
        'file_url',
        'file_size',
        'file_type',
        'is_featured',
        'is_published',
        'download_count',
        'view_count',
        'uploaded_by',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateUUID'];
    protected $afterInsert    = [];

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
     * Get resources with pagination and search for DataTables
     */
    public function getResources($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('resources.*, resource_categories.name as category_name, 
                     (SELECT COUNT(*) FROM file_attachments WHERE file_attachments.attachable_id = resources.id AND file_attachments.attachable_type = "document_resources") as attachment_count,
                     (SELECT SUM(download_count) FROM file_attachments WHERE file_attachments.attachable_id = resources.id AND file_attachments.attachable_type = "document_resources") as total_downloads,
                     (SELECT file_path FROM file_attachments WHERE file_attachments.attachable_id = resources.id AND file_attachments.attachable_type = "document_resources" ORDER BY created_at ASC LIMIT 1) as download_url')
            ->join('resource_categories', 'CAST(resource_categories.id AS CHAR) = CAST(resources.category_id AS CHAR)', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('resources.title', $search)
                ->orLike('resources.description', $search)
                ->orLike('resource_categories.name', $search)
                ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResult();
    }

    /**
     * Count all resources
     */
    public function countAllResources(): int
    {
        return $this->countAll();
    }

    // Add these methods to the DocumentResource model
    public function getFeaturedResourcesWithAttachments($limit = 6)
    {
        $resources = $this->select('resources.*, resource_categories.name as category_name, resource_categories.slug as category_slug')
                        ->join('resource_categories', 'resource_categories.id = resources.category_id', 'left')
                        ->where('resources.is_published', 1)
                        ->where('resources.is_featured', 1)
                        ->orderBy('resources.created_at', 'DESC')
                        ->limit($limit)
                        ->findAll();

        // Add file attachments to each resource
        $fileAttachmentModel = model('App\Models\FileAttachment');
        
        foreach ($resources as &$resource) {
            $resource['attachments'] = $fileAttachmentModel
                ->where('attachable_id', $resource['id'])
                ->where('attachable_type', 'resources')
                ->orderBy('created_at', 'ASC')
                ->findAll();
            
            // Calculate total downloads
            $resource['total_downloads'] = array_sum(array_column($resource['attachments'], 'download_count'));
        }

        return $resources;
    }

    public function getResourcesWithAttachments($resourceIds = [])
    {
        $builder = $this->select('resources.*, resource_categories.name as category_name, resource_categories.slug as category_slug')
                    ->join('resource_categories', 'resource_categories.id = resources.category_id', 'left')
                    ->where('resources.is_published', 1);

        if (!empty($resourceIds)) {
            $builder->whereIn('resources.id', $resourceIds);
        }

        $resources = $builder->orderBy('resources.created_at', 'DESC')->findAll();

        // Add file attachments
        $fileAttachmentModel = model('App\Models\FileAttachment');
        
        foreach ($resources as &$resource) {
            $resource['attachments'] = $fileAttachmentModel
                ->where('attachable_id', $resource['id'])
                ->where('attachable_type', 'resources')
                ->orderBy('created_at', 'ASC')
                ->findAll();
            
            $resource['total_downloads'] = array_sum(array_column($resource['attachments'], 'download_count'));
        }

        return $resources;
    }

    public function incrementViewCount($resourceId)
    {
        return $this->set('view_count', 'view_count + 1', false)
                    ->where('id', $resourceId)
                    ->update();
    }
}
