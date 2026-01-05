<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class EventModel extends Model
{
    protected $table            = 'events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'title',
        'slug',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue',
        'address',
        'city',
        'country',
        'image_url',
        'banner_url',
        'total_capacity',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'title' => 'required|max_length[255]',
        'slug' => 'required|max_length[255]|is_unique[events.slug,id,{id}]',
        'event_type' => 'required|in_list[paid,free]',
        'start_date' => 'required|valid_date',
        'status' => 'required|in_list[draft,published,cancelled]',
    ];

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get published upcoming events
     */
    public function getUpcomingEvents($limit = null)
    {
        $builder = $this->where('status', 'published')
            ->where('start_date >=', date('Y-m-d'))
            ->orderBy('start_date', 'ASC')
            ->orderBy('start_time', 'ASC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get event by slug
     */
    public function findBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }
}

