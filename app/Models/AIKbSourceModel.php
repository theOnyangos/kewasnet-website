<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class AIKbSourceModel extends Model
{
    protected $table            = 'ai_kb_sources';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'id',
        'type',
        'title',
        'source_url',
        'file_path',
        'content_raw',
        'status',
        'created_by',
        'last_ingested_at',
        'ingest_error',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'type' => 'required|in_list[text,url,file]',
        'title' => 'required|min_length[2]|max_length[255]',
        'status' => 'required|in_list[active,disabled]',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                unset($data['data']['id']);
            }
        }

        return $data;
    }
}

