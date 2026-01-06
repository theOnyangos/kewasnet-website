<?php

namespace App\Controllers\BackendV2;

use App\Libraries\CIAuth;
use App\Controllers\BaseController;
use App\Services\DashboardService;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function index()
    {
        // Check if the user is logged in
        if (!CIAuth::isLoggedIn()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }

        // Check if the user is an admin
        if (!CIAuth::isAdmin()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }
        
        // Get all dashboard data
        $statistics = $this->dashboardService->getStatistics();
        $revenueChartData = $this->dashboardService->getRevenueChartData();
        $contentDistributionData = $this->dashboardService->getContentDistributionData();
        $recentDiscussions = $this->dashboardService->getRecentDiscussions(4);
        $recentActivities = $this->dashboardService->getRecentActivities(4);
        
        // Set the title for the dashboard page 
        $title = "Welcome to your dashboard - KEWASNET";
        
        $data = [
            'title' => $title,
            'statistics' => $statistics,
            'revenueChartData' => $revenueChartData,
            'contentDistributionData' => $contentDistributionData,
            'recentDiscussions' => $recentDiscussions,
            'recentActivities' => $recentActivities,
        ];
        
        return view('backendV2/pages/dashboard/index', $data);
    }
}
