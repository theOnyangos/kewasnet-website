<?php

namespace App\Models;

use CodeIgniter\Model;

class LectureLinkModel extends Model
{
    protected $table            = 'lecture_links';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lecture_id',
        'link_title',
        'link_url',
        'link_type',
        'order_index',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}

