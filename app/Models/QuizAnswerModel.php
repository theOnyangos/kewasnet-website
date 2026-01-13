<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class QuizAnswerModel extends Model
{
    protected $table            = 'quiz_answers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'attempt_id',
        'question_id',
        'answer_text',
        'option_id',
        'is_correct',
        'points_earned',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Callbacks
    protected $beforeInsert = ['generateUUID'];

    /**
     * Generate UUID for primary key before insert
     *
     * @param array $data
     * @return array
     */
    protected function generateUUID(array $data): array
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

