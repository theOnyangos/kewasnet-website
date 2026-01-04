<?php

namespace App\Services;

use App\Models\CourseModel;
use App\Models\CourseEnrollmentModel;
use App\Models\CourseSectionModel;
use App\Models\CourseLectureModel;
use App\Models\CourseLectureProgressModel;
use App\Models\CourseCompletionModel;
use App\Libraries\ClientAuth;

class CourseService
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $sectionModel;
    protected $lectureModel;
    protected $progressModel;
    protected $completionModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new CourseEnrollmentModel();
        $this->sectionModel = new CourseSectionModel();
        $this->lectureModel = new CourseLectureModel();
        $this->progressModel = new CourseLectureProgressModel();
        $this->completionModel = new CourseCompletionModel();
    }

    /**
     * Create new course
     */
    public function createCourse($courseData, $files = [])
    {
        // Handle file upload for course image
        if (isset($files['image']) && $files['image']->isValid()) {
            $image = $files['image'];
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . '../public/uploads/courses', $newName);
            $courseData['image_url'] = 'uploads/courses/' . $newName;
        }

        // Generate slug if not provided
        if (empty($courseData['slug'])) {
            $courseData['slug'] = url_title($courseData['title'], '-', true);
        }

        // Map 'category' field to 'category_id' if present
        if (isset($courseData['category'])) {
            $courseData['category_id'] = $courseData['category'];
            unset($courseData['category']);
        }

        // Set defaults
        $courseData['user_id'] = session()->get('id') ?? null;
        $courseData['is_paid'] = isset($courseData['is_paid']) ? 1 : 0;
        $courseData['certificate'] = isset($courseData['certificate']) ? 1 : 0;
        $courseData['price'] = $courseData['price'] ?? 0;
        $courseData['discount_price'] = $courseData['discount_price'] ?? null;

        // Insert course (UUID will be generated automatically by model)
        $courseId = $this->courseModel->insert($courseData);

        if (!$courseId) {
            throw new \Exception('Failed to create course');
        }

        return [
            'id' => $courseId,
            'slug' => $courseData['slug']
        ];
    }

    /**
     * Update existing course
     */
    public function updateCourse($courseId, $courseData, $files = [])
    {
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            throw new \Exception('Course not found');
        }

        // Handle file upload for course image
        if (isset($files['image']) && $files['image']->isValid()) {
            // Delete old image if exists
            if (!empty($course['image_url']) && file_exists(WRITEPATH . '../public/' . $course['image_url'])) {
                @unlink(WRITEPATH . '../public/' . $course['image_url']);
            }

            $image = $files['image'];
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . '../public/uploads/courses', $newName);
            $courseData['image_url'] = 'uploads/courses/' . $newName;
        }

        // Map 'category' field to 'category_id' if present
        if (isset($courseData['category'])) {
            $courseData['category_id'] = $courseData['category'];
            unset($courseData['category']);
        }

        // Update boolean fields
        $courseData['is_paid'] = isset($courseData['is_paid']) ? 1 : 0;
        $courseData['certificate'] = isset($courseData['certificate']) ? 1 : 0;

        // Update course
        $updated = $this->courseModel->update($courseId, $courseData);

        if (!$updated) {
            throw new \Exception('Failed to update course');
        }

        return $this->courseModel->find($courseId);
    }

    /**
     * Enroll user in course
     */
    public function enrollUser($userId, $courseId)
    {
        // Check if already enrolled
        if ($this->enrollmentModel->isEnrolled($userId, $courseId)) {
            return [
                'status' => 'error',
                'message' => 'You are already enrolled in this course'
            ];
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return [
                'status' => 'error',
                'message' => 'Course not found'
            ];
        }

        // Check if course is paid
        $isPaid = ($course['price'] > 0 || ($course['is_paid'] ?? 0) == 1);
        
        if ($isPaid) {
            return [
                'status' => 'error',
                'message' => 'This course requires payment',
                'requires_payment' => true,
                'course_id' => $courseId
            ];
        }

        // Enroll user
        $enrolled = $this->enrollmentModel->enrollUser($userId, $courseId);
        
        if ($enrolled) {
            return [
                'status' => 'success',
                'message' => 'Successfully enrolled in course'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to enroll in course'
        ];
    }

    /**
     * Check if user can access course content
     */
    public function checkAccess($userId, $courseId)
    {
        // Debug logging
        log_message('debug', "checkAccess called - userId: {$userId}, courseId: {$courseId}");
        
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            log_message('debug', "checkAccess failed: Course not found");
            return false;
        }

        // Check if enrolled
        $isEnrolled = $this->enrollmentModel->isEnrolled($userId, $courseId);
        log_message('debug', "checkAccess - isEnrolled: " . ($isEnrolled ? 'true' : 'false'));
        
        if (!$isEnrolled) {
            log_message('debug', "checkAccess failed: User not enrolled");
            return false;
        }

        // For paid courses, check payment status
        // A course is considered paid only if it has a price > 0 AND is_paid flag is set
        $isPaid = ($course['price'] > 0 && ($course['is_paid'] ?? 0) == 1);
        log_message('debug', "checkAccess - isPaid: " . ($isPaid ? 'true' : 'false') . ", price: {$course['price']}, is_paid flag: " . ($course['is_paid'] ?? 'null'));
        
        if ($isPaid) {
            // Check if payment is completed
            $orderModel = new \App\Models\OrderModel();
            $order = $orderModel->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('status', 'completed')
                ->first();
            
            log_message('debug', "checkAccess - order found: " . ($order ? 'true' : 'false'));
            return !empty($order);
        }

        log_message('debug', "checkAccess - returning true (free course or enrolled)");
        return true;
    }

    /**
     * Get course content accessible to user
     */
    public function getCourseContent($courseId, $userId)
    {
        if (!$this->checkAccess($userId, $courseId)) {
            return null;
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return null;
        }

        // Get sections
        $sections = $this->sectionModel->getCourseSections($courseId);
        
        foreach ($sections as &$section) {
            // Get lectures for section
            $lectures = $this->lectureModel->getSectionLectures($section['id']);
            
            // Filter based on enrollment and preview settings
            foreach ($lectures as &$lecture) {
                $isFreePreview = ($lecture['is_free_preview'] ?? 0) == 1;
                
                // If not enrolled and not free preview, hide content
                if (!$isFreePreview && !$this->enrollmentModel->isEnrolled($userId, $courseId)) {
                    $lecture['content_hidden'] = true;
                } else {
                    $lecture = $this->lectureModel->getLectureWithResources($lecture['id']);
                }
            }
            
            $section['lectures'] = $lectures;
        }

        $course['sections'] = $sections;
        
        return $course;
    }

    /**
     * Calculate course progress
     */
    public function calculateProgress($userId, $courseId)
    {
        $sections = $this->sectionModel->getCourseSections($courseId);
        $totalLectures = 0;
        $completedLectures = 0;

        foreach ($sections as $section) {
            $lectures = $this->lectureModel->getSectionLectures($section['id']);
            $totalLectures += count($lectures);

            foreach ($lectures as $lecture) {
                $progress = $this->progressModel->where('student_id', $userId)
                    ->where('course_id', $courseId)
                    ->where('lecture_id', $lecture['id'])
                    ->where('status', 'completed')
                    ->first();

                if ($progress) {
                    $completedLectures++;
                }
            }
        }

        if ($totalLectures == 0) {
            return 0;
        }

        return round(($completedLectures / $totalLectures) * 100, 2);
    }

    /**
     * Check if course is completed
     */
    public function isCourseCompleted($userId, $courseId)
    {
        $progress = $this->calculateProgress($userId, $courseId);
        return $progress >= 100;
    }

    /**
     * Get course overview (for non-enrolled users)
     */
    public function getCourseOverview($courseId)
    {
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return null;
        }

        // Get sections count
        $sections = $this->sectionModel->getCourseSections($courseId);
        $course['sections_count'] = count($sections);

        // Get total lectures count
        $totalLectures = 0;
        foreach ($sections as $section) {
            $lectures = $this->lectureModel->getSectionLectures($section['id']);
            $totalLectures += count($lectures);
        }
        $course['lectures_count'] = $totalLectures;

        // Get instructors
        $instructorModel = new \App\Models\CourseInstructorModel();
        $course['instructors'] = $instructorModel->getCourseInstructors($courseId);

        return $course;
    }
}
