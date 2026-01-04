<?php

namespace App\Models;

use CodeIgniter\Model;

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
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }
        return $data;
    }
}

