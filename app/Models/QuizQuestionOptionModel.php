<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class QuizQuestionOptionModel extends Model
{
    protected $table            = 'quiz_question_options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Using UUIDs
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'question_id',
        'option_text',
        'is_correct',
        'order_index',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $beforeInsert = ['generateUUID'];
    
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
}

