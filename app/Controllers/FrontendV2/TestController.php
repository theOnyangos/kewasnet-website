<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;

class TestController extends BaseController
{
    public function pillarTest($slug)
    {
        $data = [
            'title' => 'Test Page',
            'slug' => $slug
        ];

        return view('frontendV2/ksp/test', $data);
    }
}
