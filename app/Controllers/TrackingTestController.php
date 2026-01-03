<?php

namespace App\Controllers;

class TrackingTestController extends BaseController
{
    public function index()
    {
        return view('test/tracking-test');
    }

    public function apiTest()
    {
        return view('test/api-test');
    }
}
