<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;

class HelpController extends BaseController
{
    /**
     * Static help & support page
     */
    public function index()
    {
        return view('backendV2/pages/help/index', [
            'title' => 'Help & Support - KEWASNET',
        ]);
    }
}