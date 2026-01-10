<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;

class AboutController extends BaseController
{
    /**
     * Static about page (or redirect to frontend about page)
     */
    public function index()
    {
        // Option 1: Show backend about page
        return view('backendV2/pages/about/index', [
            'title' => 'About KEWASNET - KEWASNET',
        ]);

        // Option 2: Redirect to frontend about page
        // return redirect()->to(base_url('about'));
    }
}