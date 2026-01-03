<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseQuestionReplyModel extends Model
{
    protected $table            = 'course_question_replies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'question_id',
        'instructor_id',
        'student_id',
        'message',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'delete_at';
}
