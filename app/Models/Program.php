<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class Program extends Model
{
    protected $table          = 'programs';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $allowedFields  = [
        'id',
        'title',
        'slug',
        'description',
        'content',
        'icon_svg',
        'background_color',
        'image_url',
        'is_featured',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description'
    ];
    
    protected $useTimestamps   = true;
    protected $useSoftDeletes  = true;
    protected $dateFormat      = 'datetime';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'slug'  => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
    ];

    protected $beforeInsert = ['setDefaultValues'];
    protected $beforeUpdate = ['setDefaultValues', 'ensureSlugUniqueness'];

    protected function setDefaultValues(array $data)
    {
        if (empty($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        
        // Auto-generate slug if not provided
        if (empty($data['data']['slug']) && !empty($data['data']['title'])) {
            $data['data']['slug'] = $this->generateSlug($data['data']['title']);
        }
        
        return $data;
    }

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

    private function generateSlug(string $title): string
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check for uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while ($this->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    // Get all active programs ordered by sort_order
    public function getAllActivePrograms()
    {
        return $this->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->orderBy('title', 'ASC')
                   ->findAll();
    }

    // Get featured programs
    public function getFeaturedPrograms($limit = 6)
    {
        return $this->where('is_active', 1)
                   ->where('is_featured', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->limit($limit)
                   ->findAll();
    }

    // Get program by slug
    public function getProgramBySlug($slug)
    {
        return $this->where('slug', $slug)
                   ->where('is_active', 1)
                   ->first();
    }

    // Get programs for display with pagination support
    public function getProgramsForDisplay($limit = 9, $offset = 0)
    {
        return $this->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->orderBy('title', 'ASC')
                   ->limit($limit, $offset)
                   ->findAll();
    }

    // Get total count of active programs
    public function getTotalActivePrograms()
    {
        return $this->where('is_active', 1)->countAllResults();
    }

    // Get programs for DataTable with server-side processing
    public function getProgramsTable(int $start, int $length, $search = null, $orderBy = 'created_at', $orderDir = 'DESC')
    {
        $builder = $this->select('id, title, slug, description, background_color, icon_svg, is_featured, is_active, sort_order, created_at')
                        ->where('deleted_at IS NULL'); // Explicitly exclude soft-deleted records

        if (!empty($search)) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('slug', $search)
                ->groupEnd();
        }

        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        return $builder->get()->getResult();
    }

    // Get total count of programs
    public function getTotalProgramsCount()
    {
        return $this->where('deleted_at IS NULL')->countAllResults(false);
    }

    // Get program statistics
    public function getProgramStats()
    {
        $totalPrograms = $this->where('deleted_at IS NULL')->countAllResults(false);
        $activePrograms = $this->where('is_active', 1)->where('deleted_at IS NULL')->countAllResults(false);
        $featuredPrograms = $this->where('is_featured', 1)->where('deleted_at IS NULL')->countAllResults(false);
        $draftPrograms = $this->where('is_active', 0)->where('deleted_at IS NULL')->countAllResults(false);

        return (object) [
            'total_programs' => $totalPrograms,
            'active_programs' => $activePrograms,
            'featured_programs' => $featuredPrograms,
            'draft_programs' => $draftPrograms
        ];
    }
}
