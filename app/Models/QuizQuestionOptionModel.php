<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizQuestionOptionModel extends Model
{
    protected $table            = 'quiz_question_options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
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
}

