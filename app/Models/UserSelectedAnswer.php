<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSelectedAnswer extends Model
{
    protected $table            = 'user_selected_answers';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'question_id',
        'answer_id',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
