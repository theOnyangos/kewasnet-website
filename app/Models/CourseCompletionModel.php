<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseCompletionModel extends Model
{
    protected $table            = 'course_completions';
    protected $primaryKey       = 'ccompl_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ccompl_student_id',
        'ccompl_course_id',
        'ccompl_status',
        'ccompl_completed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'ccompl_created_at';
    protected $updatedField  = 'ccompl_updated_at';
    protected $deletedField  = 'ccompl_deleted_at';
}

