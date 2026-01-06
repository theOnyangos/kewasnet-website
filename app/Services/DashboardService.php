<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\UserModel;
use App\Models\BlogPost;
use App\Models\EventModel;
use App\Models\EventBookingModel;
use App\Models\DocumentResource;
use App\Models\Discussion;
use App\Models\CourseModel;
use App\Models\PartnerModel;
use App\Models\Forum;
use App\Models\Reply;
use App\Models\CourseEnrollmentModel;
use Carbon\Carbon;

class DashboardService
{
    protected $orderModel;
    protected $userModel;
    protected $blogPostModel;
    protected $eventModel;
    protected $bookingModel;
    protected $resourceModel;
    protected $discussionModel;
    protected $courseModel;
    protected $partnerModel;
    protected $forumModel;
    protected $replyModel;
    protected $enrollmentModel;
    protected $db;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
        $this->blogPostModel = new BlogPost();
        $this->eventModel = new EventModel();
        $this->bookingModel = new EventBookingModel();
        $this->resourceModel = new DocumentResource();
        $this->discussionModel = new Discussion();
        $this->courseModel = new CourseModel();
        $this->partnerModel = new PartnerModel();
        $this->forumModel = new Forum();
        $this->replyModel = new Reply();
        $this->enrollmentModel = new CourseEnrollmentModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all dashboard statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_revenue' => $this->getTotalRevenue(),
            'revenue_change' => $this->getRevenueChange(),
            'new_users' => $this->getNewUsers(),
            'users_change' => $this->getUsersChange(),
            'blog_articles' => $this->getBlogArticles(),
            'blog_change' => $this->getBlogChange(),
            'upcoming_events' => $this->getUpcomingEvents(),
            'events_this_week' => $this->getEventsThisWeek(),
            'resources' => $this->getResources(),
            'downloads_today' => $this->getDownloadsToday(),
            'forum_discussions' => $this->getForumDiscussions(),
            'active_today' => $this->getActiveDiscussionsToday(),
            'online_courses' => $this->getOnlineCourses(),
            'enrollments' => $this->getEnrollments(),
            'strategic_partners' => $this->getStrategicPartners(),
            'new_partnerships' => $this->getNewPartnerships(),
        ];
    }

    /**
     * Get total revenue from completed orders (courses + events)
     */
    protected function getTotalRevenue(): float
    {
        // Revenue from course orders - use model to handle soft deletes automatically
        $courseRevenue = $this->orderModel
            ->selectSum('amount')
            ->where('status', 'completed')
            ->where('course_id IS NOT NULL', null, false)
            ->get()
            ->getRow();
        $courseRevenue = $courseRevenue ? (float)($courseRevenue->amount ?? 0) : 0;

        // Revenue from event bookings - use model to handle soft deletes automatically
        $eventRevenue = $this->bookingModel
            ->selectSum('total_amount')
            ->where('payment_status', 'paid')
            ->get()
            ->getRowArray();
        $eventRevenue = $eventRevenue['total_amount'] ?? 0;

        return $courseRevenue + $eventRevenue;
    }

    /**
     * Calculate revenue change percentage from last month
     */
    protected function getRevenueChange(): float
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month revenue - use model to handle soft deletes automatically
        $currentRevenue = $this->orderModel
            ->selectSum('amount')
            ->where('status', 'completed')
            ->where('created_at >=', $currentMonth->toDateTimeString())
            ->get()
            ->getRow();
        $currentRevenue = $currentRevenue ? (float)($currentRevenue->amount ?? 0) : 0;

        $currentEventRevenue = $this->bookingModel
            ->selectSum('total_amount')
            ->where('payment_status', 'paid')
            ->where('created_at >=', $currentMonth->toDateTimeString())
            ->get()
            ->getRowArray();
        $currentEventRevenue = $currentEventRevenue['total_amount'] ?? 0;
        $currentRevenue += $currentEventRevenue;

        // Last month revenue - use model to handle soft deletes automatically
        $lastRevenue = $this->orderModel
            ->selectSum('amount')
            ->where('status', 'completed')
            ->where('created_at >=', $lastMonth->toDateTimeString())
            ->where('created_at <=', $lastMonthEnd->toDateTimeString())
            ->get()
            ->getRow();
        $lastRevenue = $lastRevenue ? (float)($lastRevenue->amount ?? 0) : 0;

        $lastEventRevenue = $this->bookingModel
            ->selectSum('total_amount')
            ->where('payment_status', 'paid')
            ->where('created_at >=', $lastMonth->toDateTimeString())
            ->where('created_at <=', $lastMonthEnd->toDateTimeString())
            ->get()
            ->getRowArray();
        $lastEventRevenue = $lastEventRevenue['total_amount'] ?? 0;
        $lastRevenue += $lastEventRevenue;

        if ($lastRevenue == 0) {
            return $currentRevenue > 0 ? 100 : 0;
        }

        return round((($currentRevenue - $lastRevenue) / $lastRevenue) * 100, 1);
    }

    /**
     * Get total new users
     */
    protected function getNewUsers(): int
    {
        return $this->userModel->where('deleted_at', null)->countAllResults(false);
    }

    /**
     * Calculate users change percentage from last month
     */
    protected function getUsersChange(): float
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentUsers = $this->userModel
            ->where('created_at >=', $currentMonth->toDateTimeString())
            ->where('deleted_at', null)
            ->countAllResults(false);

        $lastUsers = $this->userModel
            ->where('created_at >=', $lastMonth->toDateTimeString())
            ->where('created_at <=', $lastMonthEnd->toDateTimeString())
            ->where('deleted_at', null)
            ->countAllResults(false);

        if ($lastUsers == 0) {
            return $currentUsers > 0 ? 100 : 0;
        }

        return round((($currentUsers - $lastUsers) / $lastUsers) * 100, 1);
    }

    /**
     * Get total published blog articles
     */
    protected function getBlogArticles(): int
    {
        return $this->blogPostModel
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->countAllResults(false);
    }

    /**
     * Get new blog articles this month
     */
    protected function getBlogChange(): int
    {
        $currentMonth = Carbon::now()->startOfMonth();
        return $this->blogPostModel
            ->where('status', 'published')
            ->where('created_at >=', $currentMonth->toDateTimeString())
            ->where('deleted_at', null)
            ->countAllResults(false);
    }

    /**
     * Get upcoming events count
     */
    protected function getUpcomingEvents(): int
    {
        return $this->eventModel
            ->where('start_date >=', date('Y-m-d'))
            ->where('deleted_at', null)
            ->countAllResults(false);
    }

    /**
     * Get events happening this week
     */
    protected function getEventsThisWeek(): int
    {
        $today = Carbon::now()->startOfDay();
        $weekEnd = Carbon::now()->addWeek()->endOfDay();

        return $this->eventModel
            ->where('start_date >=', $today->toDateString())
            ->where('start_date <=', $weekEnd->toDateString())
            ->where('deleted_at', null)
            ->countAllResults(false);
    }

    /**
     * Get total resources
     */
    protected function getResources(): int
    {
        // DocumentResource doesn't use soft deletes, so no deleted_at column
        return $this->resourceModel->countAllResults(false);
    }

    /**
     * Get downloads today
     */
    protected function getDownloadsToday(): int
    {
        $today = Carbon::now()->startOfDay();
        
        // Check both 'resources' and 'document_resources' attachable types
        $result = $this->db->table('file_attachments')
            ->selectSum('download_count')
            ->where('created_at >=', $today->toDateTimeString())
            ->groupStart()
                ->where('attachable_type', 'resources')
                ->orWhere('attachable_type', 'document_resources')
            ->groupEnd()
            ->get()
            ->getRow();

        return $result ? (int)($result->download_count ?? 0) : 0;
    }

    /**
     * Get total forum discussions
     */
    protected function getForumDiscussions(): int
    {
        return $this->discussionModel
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->countAllResults(false);
    }

    /**
     * Get active discussions today
     */
    protected function getActiveDiscussionsToday(): int
    {
        $today = Carbon::now()->startOfDay();
        
        return $this->discussionModel
            ->where('status', 'active')
            ->where('created_at >=', $today->toDateTimeString())
            ->where('deleted_at', null)
            ->countAllResults(false);
    }

    /**
     * Get total published courses
     */
    protected function getOnlineCourses(): int
    {
        return $this->courseModel
            ->where('status', 1)
            ->where('deleted_at', null)
            ->countAllResults(false);
    }

    /**
     * Get total enrollments
     */
    protected function getEnrollments(): int
    {
        $result = $this->db->query("
            SELECT COUNT(*) as count 
            FROM (
                SELECT DISTINCT user_id, course_id 
                FROM user_progress
            ) as distinct_enrollments
        ")->getRow();
        
        return $result ? (int)$result->count : 0;
    }

    /**
     * Get total strategic partners
     */
    protected function getStrategicPartners(): int
    {
        // Partners table doesn't have deleted_at column
        return $this->partnerModel->countAllResults(false);
    }

    /**
     * Get new partnerships this month
     */
    protected function getNewPartnerships(): int
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        // Partners table doesn't have deleted_at column
        return $this->partnerModel
            ->where('created_at >=', $currentMonth->toDateTimeString())
            ->countAllResults(false);
    }

    /**
     * Get revenue chart data for last 6 months
     */
    public function getRevenueChartData(): array
    {
        $months = [];
        $revenues = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $months[] = $date->format('M');
            
            // Course revenue - use model to handle soft deletes automatically
            $courseRevenue = $this->orderModel
                ->selectSum('amount')
                ->where('status', 'completed')
                ->where('course_id IS NOT NULL', null, false)
                ->where('created_at >=', $monthStart->toDateTimeString())
                ->where('created_at <=', $monthEnd->toDateTimeString())
                ->get()
                ->getRow();
            $courseRevenue = $courseRevenue ? (float)($courseRevenue->amount ?? 0) : 0;
            
            // Event revenue - use model to handle soft deletes automatically
            $eventRevenue = $this->bookingModel
                ->selectSum('total_amount')
                ->where('payment_status', 'paid')
                ->where('created_at >=', $monthStart->toDateTimeString())
                ->where('created_at <=', $monthEnd->toDateTimeString())
                ->get()
                ->getRowArray();
            $eventRevenue = $eventRevenue['total_amount'] ?? 0;
            
            $revenues[] = round($courseRevenue + $eventRevenue, 2);
        }
        
        return [
            'labels' => $months,
            'data' => $revenues
        ];
    }

    /**
     * Get content distribution chart data
     */
    public function getContentDistributionData(): array
    {
        $blogs = $this->getBlogArticles();
        $courses = $this->getOnlineCourses();
        $events = $this->eventModel->where('deleted_at', null)->countAllResults(false);
        $resources = $this->getResources();
        $forums = $this->getForumDiscussions();
        
        return [
            'labels' => ['Blogs', 'Courses', 'Events', 'Resources', 'Forums'],
            'data' => [$blogs, $courses, $events, $resources, $forums]
        ];
    }

    /**
     * Get recent discussions (4 most recent)
     */
    public function getRecentDiscussions(int $limit = 4): array
    {
        $discussions = $this->db->query("
            SELECT d.id, d.title, d.slug, d.last_reply_at, d.created_at, 
                   f.name as forum_name,
                   (SELECT COUNT(*) FROM replies WHERE discussion_id = d.id AND deleted_at IS NULL) as reply_count
            FROM discussions d
            LEFT JOIN forums f ON f.id = d.forum_id AND f.deleted_at IS NULL
            WHERE d.status = 'active'
              AND d.deleted_at IS NULL
            ORDER BY COALESCE(d.last_reply_at, d.created_at) DESC
            LIMIT ?
        ", [$limit])->getResultArray();
        
        $formatted = [];
        foreach ($discussions as $discussion) {
            $lastActivity = $discussion['last_reply_at'] ?? $discussion['created_at'];
            $timeAgo = $this->formatTimeAgo($lastActivity);
            
            $formatted[] = [
                'id' => $discussion['id'],
                'title' => $discussion['title'],
                'slug' => $discussion['slug'],
                'forum_name' => $discussion['forum_name'] ?? 'General',
                'reply_count' => (int)($discussion['reply_count'] ?? 0),
                'last_activity' => $timeAgo
            ];
        }
        
        return $formatted;
    }

    /**
     * Get recent activities (mixed feed)
     */
    public function getRecentActivities(int $limit = 4): array
    {
        $activities = [];
        
        // Recent user registrations
        $users = $this->userModel
            ->select('id, first_name, last_name, created_at, role_id')
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->limit(2)
            ->findAll();
        
        foreach ($users as $user) {
            $activities[] = [
                'type' => 'user_registration',
                'icon' => 'user-plus',
                'title' => 'New user registered',
                'description' => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') . ' joined',
                'time' => $this->formatTimeAgo($user['created_at']),
                'color' => 'primary',
                'created_at' => $user['created_at']
            ];
        }
        
        // Recent blog posts - use query builder directly to avoid ambiguous column issues
        $blogs = $this->db->table('blog_posts')
            ->select('blog_posts.id, blog_posts.title, blog_posts.created_at, 
                     CONCAT(system_users.first_name, " ", system_users.last_name) as author_name')
            ->join('system_users', 'system_users.id = blog_posts.user_id', 'left')
            ->where('blog_posts.status', 'published')
            ->where('blog_posts.deleted_at', null)
            ->orderBy('blog_posts.created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getResultArray();
        
        foreach ($blogs as $blog) {
            $activities[] = [
                'type' => 'blog_post',
                'icon' => 'file-text',
                'title' => 'New blog published',
                'description' => '"' . ($blog['title'] ?? 'Untitled') . '" by ' . ($blog['author_name'] ?? 'Unknown'),
                'time' => $this->formatTimeAgo($blog['created_at']),
                'color' => 'secondary',
                'created_at' => $blog['created_at']
            ];
        }
        
        // Recent events
        $events = $this->eventModel
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->findAll();
        
        foreach ($events as $event) {
            $activities[] = [
                'type' => 'event_created',
                'icon' => 'calendar-check',
                'title' => 'Event created',
                'description' => '"' . ($event['title'] ?? 'Untitled') . '" on ' . ($event['start_date'] ?? 'TBD'),
                'time' => $this->formatTimeAgo($event['created_at']),
                'color' => 'amber',
                'created_at' => $event['created_at']
            ];
        }
        
        // Recent discussions - use query builder directly to avoid ambiguous column issues
        $discussions = $this->db->table('discussions')
            ->select('discussions.id, discussions.title, discussions.created_at,
                     CONCAT(system_users.first_name, " ", system_users.last_name) as author_name')
            ->join('system_users', 'system_users.id = discussions.user_id', 'left')
            ->where('discussions.status', 'active')
            ->where('discussions.deleted_at', null)
            ->orderBy('discussions.created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getResultArray();
        
        foreach ($discussions as $discussion) {
            $activities[] = [
                'type' => 'discussion',
                'icon' => 'message-square',
                'title' => 'New discussion',
                'description' => '"' . ($discussion['title'] ?? 'Untitled') . '" started by ' . ($discussion['author_name'] ?? 'Unknown'),
                'time' => $this->formatTimeAgo($discussion['created_at']),
                'color' => 'emerald',
                'created_at' => $discussion['created_at']
            ];
        }
        
        // Sort by created_at descending and limit
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, $limit);
    }

    /**
     * Format time ago
     */
    protected function formatTimeAgo(string $dateString): string
    {
        $date = Carbon::parse($dateString);
        $now = Carbon::now();
        $diff = $date->diffInSeconds($now);
        
        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' ' . ($minutes == 1 ? 'minute' : 'minutes') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' ' . ($hours == 1 ? 'hour' : 'hours') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' ' . ($days == 1 ? 'day' : 'days') . ' ago';
        } else {
            return $date->format('M j, Y');
        }
    }
}

