<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class YoutubeLinkModel extends Model
{
    protected $table            = 'youtube_links';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'link',
        'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $beforeInsert = ['generateUUID'];

    // Get youtube links
    public function getYoutubeLinks()
    {
        return $this->findAll();
    }

    // Get a single youtube link by ID
    public function getYoutubeLinkById(int $id): ?array
    {
        return $this->find($id);
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

    public function getVideoLinks($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()->select('*');

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('link', $search)
                ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResultArray();
    }

    public function countAllVideoLinks(): int
    {
        return $this->countAll();
    }
}
