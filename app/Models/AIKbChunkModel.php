<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class AIKbChunkModel extends Model
{
    protected $table            = 'ai_kb_chunks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'id',
        'source_id',
        'chunk_index',
        'content',
        'metadata',
        'created_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'source_id' => 'required',
        'chunk_index' => 'required|integer',
        'content' => 'required',
    ];

    protected $beforeInsert = ['generateUuid', 'handleMetadata'];

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

    protected function handleMetadata(array $data)
    {
        if (isset($data['data']['metadata']) && is_array($data['data']['metadata'])) {
            $data['data']['metadata'] = json_encode($data['data']['metadata']);
        }
        return $data;
    }
}

