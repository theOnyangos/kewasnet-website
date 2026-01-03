<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class SitemapModel extends Model
{
    protected $table         = 'sitemaps';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = false;
    protected $returnType    = 'object';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'url', 'title', 'description', 'category', 'changefreq', 
        'priority', 'last_modified', 'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'url'           => 'required|max_length[500]',
        'category'      => 'required|max_length[100]',
        'changefreq'    => 'required|in_list[always,hourly,daily,weekly,monthly,yearly,never]',
        'priority'      => 'required|decimal|greater_than[0]|less_than_equal_to[1]',
        'last_modified' => 'required|valid_date',
        'is_active'     => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'url' => [
            'required'    => 'URL is required',
            'max_length'  => 'URL cannot exceed 500 characters'
        ],
        'priority' => [
            'decimal'              => 'Priority must be a decimal number',
            'greater_than'         => 'Priority must be greater than 0',
            'less_than_equal_to'   => 'Priority must be less than or equal to 1'
        ]
    ];

    protected $skipValidation = false;

    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = [];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get active sitemap URLs organized by category
     */
    public function getActiveSitemapsByCategory()
    {
        $sitemaps = $this->where('is_active', 1)
                        ->orderBy('category', 'ASC')
                        ->orderBy('priority', 'DESC')
                        ->orderBy('url', 'ASC')
                        ->findAll();

        $categorized = [];
        foreach ($sitemaps as $sitemap) {
            $categorized[$sitemap->category][] = $sitemap;
        }

        return $categorized;
    }

    /**
     * Get all active URLs for XML sitemap
     */
    public function getActiveUrlsForXml()
    {
        return $this->select('url, last_modified, changefreq, priority')
                   ->where('is_active', 1)
                   ->orderBy('priority', 'DESC')
                   ->findAll();
    }

    /**
     * Update or create sitemap entry
     */
    public function updateOrCreate($url, $data)
    {
        $existing = $this->where('url', $url)->first();
        
        if ($existing) {
            return $this->update($existing->id, $data);
        } else {
            $data['url'] = $url;
            return $this->insert($data);
        }
    }

    /**
     * Bulk update or create sitemap entries
     */
    public function bulkUpdateOrCreate($urlsData)
    {
        $results = [];
        
        $this->db->transStart();
        
        try {
            foreach ($urlsData as $urlData) {
                $url = $urlData['url'];
                unset($urlData['url']);
                
                $results[] = $this->updateOrCreate($url, $urlData);
            }
            
            $this->db->transComplete();
            
            return $this->db->transStatus() ? $results : false;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Sitemap bulk update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Deactivate URLs not in the provided list
     */
    public function deactivateUrlsNotInList($activeUrls)
    {
        return $this->whereNotIn('url', $activeUrls)
                   ->set('is_active', 0)
                   ->update();
    }

    /**
     * Get sitemap statistics
     */
    public function getStatistics()
    {
        $total = $this->countAllResults(false);
        $active = $this->where('is_active', 1)->countAllResults(false);
        $byCategory = $this->select('category, COUNT(*) as count')
                          ->where('is_active', 1)
                          ->groupBy('category')
                          ->findAll();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'by_category' => $byCategory
        ];
    }

    /**
     * Get last generation information
     */
    public function getLastGeneration()
    {
        $lastEntry = $this->select('MAX(updated_at) as last_updated')
                         ->first();
        
        return [
            'last_updated' => $lastEntry->last_updated ?? null,
            'generated_at' => $lastEntry->last_updated ? date('Y-m-d H:i:s', strtotime($lastEntry->last_updated)) : null
        ];
    }
}
