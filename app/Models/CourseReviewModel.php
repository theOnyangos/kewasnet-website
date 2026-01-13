<?php
namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class CourseReviewModel extends Model
{
    protected $table            = 'course_reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_id',
        'user_id',
        'rating',
        'review',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                // Fallback to database UUID function
                unset($data['data']['id']);
            }
        }
        return $data;
    }

    public function getUserReview($userId, $courseId)
    {
        return $this->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->first();
    }

    public function getCourseReviews($courseId, $limit = 10)
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
