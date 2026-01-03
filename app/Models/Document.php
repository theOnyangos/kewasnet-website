<?php

namespace App\Models;

use CodeIgniter\Model;

class Document extends Model
{
    protected $table            = 'docs';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'title',
        'description',
        'doc-url',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'created_at',
        'updated_at'
    ];
}
