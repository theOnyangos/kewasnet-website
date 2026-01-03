<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;
use App\Libraries\CIAuth;
use App\Libraries\ClientAuth;

class Pillar extends Model
{
    protected $table          = 'pillars';
    protected $primaryKey     = 'id';
    protected $allowedFields  = [
        'id',
        'user_id',
        'slug', 
        'title', 
        'icon', 
        'description', 
        'content', 
        'image_path',
        'button_text',
        'button_link',
        'is_private',
        'meta_title',
        'meta_description',
        'is_active'
    ];
    protected $useTimestamps   = true;
    protected $useSoftDeletes  = true;
    protected $dateFormat      = 'datetime';

    protected $validationRules = [];

    protected $beforeInsert = ['setDefaultValues'];
    protected $beforeUpdate = ['setDefaultValues', 'ensureSlugUniqueness'];

    protected function setDefaultValues(array $data)
    {
        if (empty($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    protected function setUserId(array $data)
    {
        if (empty($data['data']['user_id']) && (CIAuth::isLoggedIn() || ClientAuth::isLoggedIn())) {
            $data['data']['user_id'] = session()->get('id') ?? session()->get('user_id');
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

    public function getAllActivePillars()
    {
        return $this->where('is_active', 1)
                   ->orderBy('title', 'ASC')
                   ->findAll();
    }

    public function getPillarBySlug($slug)
    {
        return $this->where('slug', $slug)
                   ->where('is_active', 1)
                   ->first();
    }

    public function getPillarStats()
    {
        $builder = $this->builder();
        
        $totalPillars = $builder->countAllResults(false);
        $activePillars = $builder->where('is_active', 1)->countAllResults(false);
        $inactivePillars = $totalPillars - $activePillars;

        return (object) [
            'total_pillars' => $totalPillars,
            'active_pillars' => $activePillars,
            'inactive_pillars' => $inactivePillars
        ];
    }

    public function getPillarsTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->select('pillars.*, COUNT(resources.id) as articles_count')
                        ->join('resources', 'resources.pillar_id COLLATE utf8mb4_unicode_ci = pillars.id COLLATE utf8mb4_unicode_ci', 'left', false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('pillars.title', $search)
                ->orLike('pillars.description', $search)
                ->groupEnd();
        }

        $builder->groupBy('pillars.id');
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        return $builder->get()
                       ->getResult();
    }

    public function getTotalPillarsCount()
    {
        return $this->countAllResults();
    }

    public function createPillar(array $data): bool
    {
        $this->insert($data);
        return $this->getInsertID();
    }
}