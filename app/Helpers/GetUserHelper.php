<?php 

namespace App\Helpers;

use App\Models\UserModel;
use App\Models\Blog;
use App\Models\EventModel;

class GetUserHelper
{
    protected $blog;
    protected $userModel;
    protected $eventModel;

    // Magic function
    public function __construct()
    {
        $this->blog = new Blog();
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
    }

    // Get user full name by user id
    public function getUserFullName($userId)
    {
        $user = $this->userModel->where("id", $userId)->first();
        return $userId;
    }

    public function getRecentSixUsers($userId)
    {
        return $this->userModel
        ->select('users.first_name, users.last_name, users.phone, users.picture, users.employee_number, roles.role_name')
        ->join('roles', 'roles.role_id = users.role_id')
        ->where('users.id !=', $userId)
        ->orderBy('users.id', 'DESC')
        ->findAll(6);
    }

    public function getAccountStatistics()
    {
        $totalRevenue = 0; 
        $totalUsers = $this->userModel->countAll();
        $totalBlogs = $this->blog->countAll();
        $totalEvents = $this->eventModel->countAll();

        return [
            'total_revenue' => $totalRevenue,
            'total_users' => $totalUsers,
            'total_blogs' => $totalBlogs,
            'total_events' => $totalEvents,
        ];
    }

}