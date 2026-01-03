<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Program;

class Programs extends BaseController
{
    protected $programModel;

    public function __construct()
    {
        $this->programModel = new Program();
        helper('text'); // Load the text helper for character_limiter function
    }

    public function index()
    {
        $data = [
            'title' => 'Our Programs - KEWASNET',
            'description' => 'Explore KEWASNET\'s comprehensive programs focused on water, sanitation, and hygiene solutions across Kenya.',
            'programs' => $this->programModel->getAllActivePrograms(),
            'featuredPrograms' => $this->programModel->getFeaturedPrograms(6)
        ];

        return view('frontendV2/website/pages/programs/index', $data);
    }

    public function detail($slug)
    {
        $program = $this->programModel->getProgramBySlug($slug);
        
        if (!$program) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $program->title . ' - KEWASNET Programs',
            'description' => $program->meta_description ?: $program->description,
            'program' => $program,
            'relatedPrograms' => $this->programModel->getFeaturedPrograms(3)
        ];

        return view('frontendV2/website/pages/programs/detail', $data);
    }
}
