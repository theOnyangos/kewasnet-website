<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\CourseSectionModel;
use App\Models\CourseLectureModel;
use App\Models\CourseEnrollmentModel;
use App\Models\QuizModel;
use App\Models\CertificateModel;
use App\Models\BlogPostCategory;
use App\Services\DataTableService;
use App\Services\CourseService;
use CodeIgniter\HTTP\ResponseInterface;

class CoursesController extends BaseController
{
    protected const PAGE_TITLE = "Manage Courses - KEWASNET";
    protected const COURSES_PAGE_TITLE = "Learning Hub Course Management";
    protected const CREATE_COURSE_PAGE_TITLE = "Create New Course";
    protected const CREATE_COURSE_DASH_TITLE = "Create New Course - Dashboard";
    protected const EDIT_COURSE_PAGE_TITLE = "Edit Course";
    protected const EDIT_COURSE_DASH_TITLE = "Edit Course - Dashboard";

    protected $courseModel;
    protected $sectionModel;
    protected $lectureModel;
    protected $enrollmentModel;
    protected $quizModel;
    protected $certificateModel;
    protected $categoryModel;
    protected $dataTableService;
    protected $courseService;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->sectionModel = new CourseSectionModel();
        $this->lectureModel = new CourseLectureModel();
        $this->enrollmentModel = new CourseEnrollmentModel();
        $this->quizModel = new QuizModel();
        $this->certificateModel = new CertificateModel();
        $this->categoryModel = new BlogPostCategory();
        $this->dataTableService = new DataTableService();
        $this->courseService = new CourseService();
    }

    public function index()
    {
        // Get course statistics
        $courseStats = $this->getCourseStatistics();

        return view('backendV2/pages/courses/index', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => self::COURSES_PAGE_TITLE,
            'courseStats' => $courseStats
        ]);
    }

    public function courses()
    {
        return view('backendV2/pages/courses/courses', [
            'title' => 'All Courses - KEWASNET',
            'dashboardTitle' => 'All Courses Management'
        ]);
    }

    public function sections()
    {
        return view('backendV2/pages/courses/sections', [
            'title' => 'Course Sections - KEWASNET',
            'dashboardTitle' => 'Course Sections Management'
        ]);
    }

    public function lectures()
    {
        return view('backendV2/pages/courses/lectures', [
            'title' => 'Course Lectures - KEWASNET',
            'dashboardTitle' => 'Course Lectures Management'
        ]);
    }

    public function enrollments()
    {
        return view('backendV2/pages/courses/enrollments', [
            'title' => 'Course Enrollments - KEWASNET',
            'dashboardTitle' => 'Course Enrollments Management'
        ]);
    }

    /**
     * API endpoint for courses DataTable
     */
    public function getCourses()
    {
        $columns = ['id', 'title', 'category_name', 'level', 'price', 'status', 'enrollments_count', 'created_at'];

        return $this->dataTableService->handle(
            $this->courseModel,
            $columns,
            'getCoursesTable',
            'countCourses',
        );
    }

    /**
     * API endpoint for sections DataTable
     */
    public function getSections()
    {
        $columns = ['id', 'course_title', 'title', 'lectures_count', 'status', 'order_index', 'created_at'];

        return $this->dataTableService->handle(
            $this->sectionModel,
            $columns,
            'getSectionsTable',
            'countSections',
        );
    }

    /**
     * API endpoint for lectures DataTable
     */
    public function getLectures()
    {
        $columns = ['id', 'section_title', 'title', 'duration', 'is_preview', 'order_index', 'created_at'];

        return $this->dataTableService->handle(
            $this->lectureModel,
            $columns,
            'getLecturesTable',
            'countLectures',
        );
    }

    /**
     * API endpoint for enrollments DataTable
     */
    public function getEnrollments()
    {
        $columns = ['id', 'student_name', 'course_title', 'progress_percentage', 'last_accessed_at', 'completed_at'];

        return $this->dataTableService->handle(
            $this->enrollmentModel,
            $columns,
            'getEnrollmentsTable',
            'countEnrollments',
        );
    }

    /**
     * Show create course form
     */
    public function create()
    {
        // Try to get categories from both blog_categories and course_categories
        $blogCategories = $this->categoryModel->where('deleted_at', null)->findAll();

        // Also try course_categories table
        $db = \Config\Database::connect();
        $courseCategories = $db->table('course_categories')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        // Merge both category sources
        $categories = array_merge($blogCategories, $courseCategories);

        // Remove duplicates by name if any
        $uniqueCategories = [];
        $seen = [];
        foreach ($categories as $category) {
            if (!isset($seen[$category['name']])) {
                $uniqueCategories[] = $category;
                $seen[$category['name']] = true;
            }
        }

        return view('backendV2/pages/courses/create', [
            'title' => self::CREATE_COURSE_PAGE_TITLE,
            'dashboardTitle' => self::CREATE_COURSE_DASH_TITLE,
            'categories' => $uniqueCategories
        ]);
    }

    /**
     * Handle course creation
     */
    public function handleCreateCourse()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $courseData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Debug log
            log_message('info', 'Course creation data: ' . json_encode($courseData));

            // Validate required fields
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'category' => 'required',
                'level' => 'required|in_list[beginner,intermediate,advanced]',
                'price' => 'required|decimal',
                'duration' => 'required|integer',
                'status' => 'required|in_list[draft,published,archived]'
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            $course = $this->courseService->createCourse($courseData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Course created successfully!',
                'data' => ['course_id' => $course['id']],
                'redirect_url' => site_url('auth/courses/edit/' . $course['id'])
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            log_message('error', 'Course creation error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create course: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    /**
     * Show edit course form
     */
    public function edit($courseId)
    {
        log_message('debug', 'Edit method called for course ID: ' . $courseId);

        $course = $this->courseModel->find($courseId);

        if (!$course) {
            log_message('error', 'Course not found: ' . $courseId);
            return redirect()->to('auth/courses')->with('error', 'Course not found.');
        }

        log_message('debug', 'Course found: ' . json_encode($course));

        // Try to get categories from both blog_categories and course_categories
        $blogCategories = $this->categoryModel->where('deleted_at', null)->findAll();

        // Also try course_categories table
        $db = \Config\Database::connect();
        $courseCategories = $db->table('course_categories')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        // Merge both category sources
        $categories = array_merge($blogCategories, $courseCategories);

        // Remove duplicates by name if any
        $uniqueCategories = [];
        $seen = [];
        foreach ($categories as $category) {
            if (!isset($seen[$category['name']])) {
                $uniqueCategories[] = $category;
                $seen[$category['name']] = true;
            }
        }

        $sections = $this->sectionModel
            ->where('course_id', $courseId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('backendV2/pages/courses/edit', [
            'title' => self::EDIT_COURSE_PAGE_TITLE,
            'dashboardTitle' => self::EDIT_COURSE_DASH_TITLE,
            'course' => $course,
            'categories' => $uniqueCategories,
            'sections' => $sections
        ]);
    }

    /**
     * Handle course update
     */
    public function handleUpdateCourse($courseId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $course = $this->courseModel->find($courseId);

            if (!$course) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Course not found.'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $courseData = $this->request->getPost();
            $files = $this->request->getFiles();

            $updatedCourse = $this->courseService->updateCourse($courseId, $courseData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Course updated successfully!',
                'data' => $updatedCourse
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            log_message('error', 'Course update error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update course: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    /**
     * Delete course
     */
    public function deleteCourse($courseId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $course = $this->courseModel->find($courseId);

            if (!$course) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Course not found.'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if course has enrollments
            $enrollmentsCount = $this->enrollmentModel->where('course_id', $courseId)->countAllResults();

            if ($enrollmentsCount > 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot delete course with existing enrollments. Please archive it instead.'
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            $this->courseModel->delete($courseId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Course deleted successfully!'
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            log_message('error', 'Course deletion error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to delete course: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    /**
     * Section Management Methods
     */

    /**
     * Get section data
     */
    public function getSection($sectionId)
    {
        try {
            $section = $this->sectionModel->find($sectionId);

            if (!$section) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Section not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $section
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving section: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new section
     */
    public function createSection()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $data = $this->request->getPost();

            $sectionData = [
                'course_id' => $data['course_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'status' => 1,
                'order_index' => $data['order_index'] ?? ($this->sectionModel->where('course_id', $data['course_id'])->countAllResults() + 1)
            ];

            $sectionId = $this->sectionModel->insert($sectionData);

            if (!$sectionId) {
                throw new \Exception('Failed to create section');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Section created successfully',
                'data' => ['section_id' => $sectionId]
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            log_message('error', 'Section creation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create section: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update section
     */
    public function updateSection($sectionId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $section = $this->sectionModel->find($sectionId);
            if (!$section) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Section not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $data = $this->request->getPost();
            $updateData = [
                'title' => $data['title'],
                'description' => $data['description'] ?? ''
            ];

            if (isset($data['order_index'])) {
                $updateData['order_index'] = $data['order_index'];
            }

            $updated = $this->sectionModel->update($sectionId, $updateData);

            if (!$updated) {
                throw new \Exception('Failed to update section');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Section updated successfully'
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete section
     */
    public function deleteSection($sectionId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $section = $this->sectionModel->find($sectionId);
            if (!$section) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Section not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Delete section and its lectures (soft delete will handle it)
            $this->sectionModel->delete($sectionId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Section deleted successfully'
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Lecture Management Methods
     */

    /**
     * Get lecture data
     */
    public function getLecture($lectureId)
    {
        try {
            $lecture = $this->lectureModel->find($lectureId);

            if (!$lecture) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Lecture not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $lecture
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving lecture: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new lecture
     */
    public function createLecture()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $data = $this->request->getPost();

            $lectureData = [
                'section_id' => $data['section_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'video_url' => $data['video_url'] ?? '',
                'duration' => $data['duration'] ?? 0,
                'is_preview' => isset($data['is_preview']) && $data['is_preview'] ? 1 : 0,
                'is_free_preview' => isset($data['is_free_preview']) && $data['is_free_preview'] ? 1 : 0,
                'order_index' => $data['order_index'] ?? ($this->lectureModel->where('section_id', $data['section_id'])->countAllResults() + 1)
            ];

            $lectureId = $this->lectureModel->insert($lectureData);

            if (!$lectureId) {
                throw new \Exception('Failed to create lecture');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lecture created successfully',
                'data' => ['lecture_id' => $lectureId]
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            log_message('error', 'Lecture creation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create lecture: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update lecture
     */
    public function updateLecture($lectureId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $lecture = $this->lectureModel->find($lectureId);
            if (!$lecture) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Lecture not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $data = $this->request->getPost();
            $updateData = [
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'video_url' => $data['video_url'] ?? '',
                'duration' => $data['duration'] ?? 0,
                'is_preview' => isset($data['is_preview']) && $data['is_preview'] ? 1 : 0,
                'is_free_preview' => isset($data['is_free_preview']) && $data['is_free_preview'] ? 1 : 0
            ];

            if (isset($data['order_index'])) {
                $updateData['order_index'] = $data['order_index'];
            }

            $updated = $this->lectureModel->update($lectureId, $updateData);

            if (!$updated) {
                throw new \Exception('Failed to update lecture');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lecture updated successfully'
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete lecture
     */
    public function deleteLecture($lectureId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $lecture = $this->lectureModel->find($lectureId);
            if (!$lecture) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Lecture not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $this->lectureModel->delete($lectureId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lecture deleted successfully'
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**     * Show enrolled students page for a specific course
     */
    public function enrolledStudents($courseId)
    {
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->to('auth/courses')->with('error', 'Course not found');
        }

        return view('backendV2/pages/courses/enrolled-students', [
            'title' => 'Enrolled Students - ' . $course['title'],
            'dashboardTitle' => 'Course Enrollments',
            'course' => $course,
            'courseId' => $courseId
        ]);
    }

    /**
     * Get enrolled students data for DataTable
     */
    public function getEnrolledStudents($courseId)
    {
        $columns = ['id', 'student_name', 'email', 'phone', 'progress_percentage', 'last_accessed_at', 'completed_at'];

        // Custom handling for enrolled students
        $request = $this->request;
        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? null;
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'DESC';
        $orderColumn = $columns[$orderColumnIndex] ?? 'last_accessed_at';

        // Get enrolled students with user details
        $db = \Config\Database::connect();
        $builder = $db->table('user_progress')
            ->select('user_progress.id, 
                     user_progress.user_id,
                     user_progress.progress_percentage,
                     user_progress.last_accessed_at,
                     user_progress.completed_at,
                     user_progress.created_at,
                     system_users.first_name,
                     system_users.last_name,
                     system_users.email,
                     system_users.phone,
                     CONCAT(system_users.first_name, " ", system_users.last_name) as student_name')
            ->join('system_users', 'system_users.id = user_progress.user_id', 'left')
            ->where('user_progress.course_id', $courseId)
            ->groupBy('user_progress.user_id'); // Group by user to avoid duplicates

        // Count total records
        $totalRecords = $builder->countAllResults(false);

        // Apply search
        if ($searchValue) {
            $builder->groupStart()
                ->like('system_users.first_name', $searchValue)
                ->orLike('system_users.last_name', $searchValue)
                ->orLike('system_users.email', $searchValue)
                ->orLike('system_users.phone', $searchValue)
                ->groupEnd();
        }

        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Apply ordering
        $builder->orderBy($orderColumn, $orderDir);

        // Apply pagination
        $builder->limit($length, $start);

        // Get data
        $data = $builder->get()->getResult();

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**     * Get course statistics
     */
    private function getCourseStatistics(): array
    {
        $totalCourses = $this->courseModel->countAll();
        $publishedCourses = $this->courseModel->where('status', 'published')->countAllResults();
        $draftCourses = $this->courseModel->where('status', 'draft')->countAllResults();
        $totalEnrollments = $this->enrollmentModel->countAllResults();
        $totalSections = $this->sectionModel->countAll();
        $totalLectures = $this->lectureModel->countAll();
        $certificatesIssued = $this->certificateModel->countAll();

        // Calculate total revenue from completed orders
        $db = \Config\Database::connect();
        $totalRevenue = $db->table('orders')
            ->selectSum('amount')
            ->where('status', 'completed')
            ->where('deleted_at', null)
            ->get()
            ->getRow()
            ->amount ?? 0;

        return [
            'total_courses' => $totalCourses,
            'published_courses' => $publishedCourses,
            'draft_courses' => $draftCourses,
            'total_enrollments' => $totalEnrollments,
            'total_sections' => $totalSections,
            'total_lectures' => $totalLectures,
            'certificates_issued' => $certificatesIssued,
            'total_revenue' => $totalRevenue
        ];
    }

    /**
     * Get recent courses
     */
    public function getRecentCourses()
    {
        try {
            $courses = $this->courseModel
                ->select('id, title, slug, price, is_paid, status, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $courses
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get recent courses error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get top enrolled courses
     */
    public function getTopEnrolledCourses()
    {
        try {
            $db = \Config\Database::connect();
            $courses = $db->table('courses')
                ->select('courses.id, 
                         courses.title, 
                         courses.slug, 
                         courses.price, 
                         courses.is_paid,
                         COUNT(user_progress.id) as enrollment_count')
                ->join('user_progress', 'user_progress.course_id = courses.id', 'left')
                ->where('courses.deleted_at', null)
                ->groupBy('courses.id')
                ->orderBy('enrollment_count', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $courses
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get top enrolled courses error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Helper Methods
     */
    protected function failValidationErrors(array $errors): ResponseInterface
    {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validation failed. Fix errors in inputs and try again.',
            'errors' => $errors,
            'error_type' => 'validation'
        ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }
}
