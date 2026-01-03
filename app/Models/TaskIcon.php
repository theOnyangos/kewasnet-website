<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskIcon extends Model
{
    protected $table            = 'task_icons';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'name', 'icon',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
