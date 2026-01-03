<?php

namespace App\Models;

use CodeIgniter\Model;

class UserQuizModel extends Model
{
    protected $table            = 'user_quizzes';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'quiz_id',
        'score',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
