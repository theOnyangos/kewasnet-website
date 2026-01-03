<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizAnswerModel extends Model
{
    protected $table            = 'quiz_answers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
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
}

