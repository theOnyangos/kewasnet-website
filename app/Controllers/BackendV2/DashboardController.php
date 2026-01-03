<?php

namespace App\Controllers\BackendV2;

use App\Libraries\CIAuth;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
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
        
        // Set the title for the dashboard page 
        $title = "Welcome to your dashboard - KEWASNET";
        return view('backendV2/pages/dashboard/index', ['title' => $title ]);
    }
}
