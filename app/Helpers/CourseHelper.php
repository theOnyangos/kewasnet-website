<?php 

namespace App\Helpers;

use App\Models\CourseLectures;
use App\Models\Course;
use App\Models\QuizModel;
use App\Models\CourseWishlist;
use App\Libraries\ClientAuth;
use App\Models\UserModel;
use App\Models\CoursePurchase;
use App\Models\UserDetail;
use App\Models\CourseRequirementModel;
use Carbon\Carbon;
use App\Models\CourseSectionModel;
use App\Models\CourseCartModel;

class CourseHelper 
{
    protected $courseLecturesModel;
    protected $courseModel;
    protected $quizModel;
    protected $courseWishlistModel;
    protected $userModel;
    protected $courseRequirementModel;
    protected $courseSectionsModel;
    protected $cartModel;
    protected $coursePurchaseModel;

    // Magic function
    public function __construct()
    {
        $this->courseLecturesModel = new CourseLectures();
        $this->courseModel = new Course();
        $this->quizModel = new QuizModel();
        $this->courseWishlistModel = new CourseWishlist();
        $this->userModel = new UserModel();
        $this->courseRequirementModel = new CourseRequirementModel();
        $this->courseSectionsModel = new CourseSectionModel();
        $this->cartModel = new CourseCartModel();
        $this->coursePurchaseModel = new CoursePurchase();
    }

    // Count course lectures
    public function countCourseLectures($courseId)
    {
        return $this->courseLecturesModel->where('course_id', $courseId)->countAllResults();
    }

    // Count course quizzes (Incomplete)
    public function countCourseQuizzes($courseId)
    {
        return 0;
    }

    // Check if course is in wishlist
    public function isCourseInWishlist($courseId)
    {
        if (ClientAuth::isLoggedIn() == false) return false;

        $userId = ClientAuth::getId();

        $courseWishlist = $this->courseWishlistModel->where('course_id', $courseId)->where('user_id', $userId)->first();

        return $courseWishlist ? true : false;
    }

    // Get course instructors from array of ids
    public function getCourseInstructors($instructorIds)
    {
        $instructors = [];

        foreach ($instructorIds as $instructorId) {
            $instructor = $this->userModel->find($instructorId);

            if ($instructor) {
                $instructors[] = [
                    'name' => $instructor['first_name'] . ' ' . $instructor['last_name'],
                    'picture' => $instructor['picture'],
                    'bio' => $instructor['bio'],
                    'designation' => (new UserDetail())->where('user_id', $instructorId)->first()['designation'] ?? NULL
                ];
            }
        }

        return $instructors;
    }

    // Get course requirements
    public function getCourseRequirements($courseId)
    {
        return $this->courseRequirementModel->where('course_id', $courseId)->findAll();
    }

    // Get course goals
    public function getCourseGoals($courseId)
    {
        $goalsModel = model('CourseGoalModel');
        return $goalsModel->where('course_id', $courseId)->findAll();
    }

    // Get section total time
    public function getSectionTotalTime($sectionId)
    {
        $sectionLectures = $this->courseLecturesModel->where('section_id', $sectionId)->findAll();

        $totalTime = 0;

        foreach ($sectionLectures as $lecture) {
            // Check if duration is in format 4 mins or 4 hours then calculate total time
            if (strpos($lecture['duration'], 'mins') !== false) {
                $lecture['duration'] = (int) $lecture['duration'];
            } else if (strpos($lecture['duration'], 'hours') !== false) {
                $lecture['duration'] = (int) $lecture['duration'] * 60;
            }
            $totalTime += $lecture['duration'];
        }

        // Convert minutes to hours and minutes
        $hours = floor($totalTime / 60);
        $minutes = $totalTime % 60;

        if ($hours == 0) $totalTime = $minutes . ' minutes';
        else if ($minutes == 0) $totalTime = $hours . ' hours';
        else if ($hours > 0 && $minutes > 0) $totalTime = $hours . ' hours ' . $minutes . ' minutes';

        return $totalTime;
    }

    // Get course content data
    public function getCourseContentCount($courseId)
    {
        $totalSections = 0;
        $totalLectures = 0;
        $totalTime = 0;

        $courseSections = $this->courseSectionsModel->findCourseSections($courseId);

        foreach ($courseSections as $section) {
            $sectionLectures = $this->courseLecturesModel->where('section_id', $section['id'])->findAll();

            $totalSections++;
            $totalLectures += count($sectionLectures);

            foreach ($sectionLectures as $lecture) {
                // Check if duration is in format 4 mins or 4 hours then calculate total time
                if (strpos($lecture['duration'], 'mins') !== false) {
                    $lecture['duration'] = (int) $lecture['duration'];
                } else if (strpos($lecture['duration'], 'hours') !== false) {
                    $lecture['duration'] = (int) $lecture['duration'] * 60;
                }

                $totalTime += $lecture['duration'];
            }
        }

        // Convert minutes to hours and minutes
        $hours = floor($totalTime / 60);
        $minutes = $totalTime % 60;

        if ($hours == 0) $totalTime = $minutes . ' minutes';
        else if ($minutes == 0) $totalTime = $hours . ' hours';
        else if ($hours > 0 && $minutes > 0) $totalTime = $hours . ' hours ' . $minutes . ' minutes';

        return [
            'total_sections' => $totalSections,
            'total_lectures' => $totalLectures,
            'total_time' => $totalTime
        ];
    }

    // Get checkout summary
    public function getCheckoutSummary()
    {
        $cartItems = ClientAuth::isLoggedIn() ? $this->cartModel->getCartItemsByUserId(ClientAuth::getId()) : $this->cartModel->getCartItemsByIdentifier(get_cookie('guest_identifier'));

        $totalPrice = 0;
        $totalDiscount = 0;
        $totalSubtotal = 0;

        foreach ($cartItems as $cartItem) {
            $course = $this->courseModel->find($cartItem['course_id']);

            $totalPrice += $course['discount_price'];
            $totalDiscount += $course['price'];
            $totalSubtotal += $course['discount_price'];
        }

        return [
            'total_price' => $totalPrice,
            'total_discount' => $totalDiscount - $totalPrice,
            'subtotals' => $totalSubtotal
        ];
    }

    // Get the first lecture of the given course
    public function getFirstLecture($courseId)
    {
        return $this->courseLecturesModel->where('course_id', $courseId)->first();
    }

    // Get course announcements
    public function getCourseAnnouncements($courseId)
    {
        $announcementsModel = model('CourseAnnouncementModel');
        return $announcementsModel->where('course_id', $courseId)->findAll();
    }

    // Get Instructor Name
    public function getInstructorName($instructorId)
    {
        $instructor = $this->userModel->find($instructorId);
        return  $instructor ? $instructor['first_name'] . ' ' . $instructor['last_name'] : "No Instractor!";
    }
}