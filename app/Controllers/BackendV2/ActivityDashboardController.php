<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;

class ActivityDashboardController extends BaseController
{
    public function index()
    {
        // Check if user is authenticated (you may want to add proper auth checking here)
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        return view('admin/activity-dashboard');
    }
}
