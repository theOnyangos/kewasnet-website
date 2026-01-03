<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Models\JobOpportunityModel;
use App\Services\JobApplicationService;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\FileAttachment;

class OpportunitiesController extends BaseController
{
    protected $jobOpportunityModel;
    protected $fileAttachmentModel;
    protected $jobApplicationService;
    
    public function __construct()
    {
        $this->fileAttachmentModel = new FileAttachment();
        $this->jobOpportunityModel = new JobOpportunityModel();
        $this->jobApplicationService = new JobApplicationService();
    }

    /**
     * Display a specific job opportunity
     */
    public function view($slug)
    {
        // Find the opportunity by slug
        $opportunity = $this->jobOpportunityModel
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->first();

        // Get opportunity attachments
        $attachments = $this->fileAttachmentModel->select('file_name, file_path, file_type, file_size, original_name')
            ->where('attachable_id', $opportunity['id'] ?? null)
            ->where('attachable_type', 'job_opportunity')
            ->findAll();

        if (!$opportunity) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Parse JSON fields
        if (!empty($opportunity['requirements'])) {
            $opportunity['requirements'] = json_decode($opportunity['requirements'], true);
        }
        if (!empty($opportunity['benefits'])) {
            $opportunity['benefits'] = json_decode($opportunity['benefits'], true);
        }

        // Check if application deadline has passed
        $deadlinePassed = false;
        if (!empty($opportunity['application_deadline'])) {
            $deadline = new \DateTime($opportunity['application_deadline']);
            $now = new \DateTime();
            $deadlinePassed = $now > $deadline;
        }

        // Get related opportunities (same type, excluding current)
        $relatedOpportunities = $this->jobOpportunityModel
            ->where('opportunity_type', $opportunity['opportunity_type'])
            ->where('slug !=', $slug)
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->limit(3)
            ->findAll();

        $title = $opportunity['title'] . " - KEWASNET Opportunities";
        $description = substr(strip_tags($opportunity['description']), 0, 160);

        $data = [
            'title' => $title,
            'attachments' => $attachments,
            'description' => $description,
            'opportunity' => $opportunity,
            'deadlinePassed' => $deadlinePassed,
            'relatedOpportunities' => $relatedOpportunities,
            'newsletterTitle' => 'Stay Updated on New Opportunities',
            'newsletterDescription' => 'Subscribe to our newsletter to receive notifications about new job postings and career opportunities in the WASH sector.',
        ];

        return view('frontendV2/website/pages/opportunities/view', $data);
    }

    /**
     * Explore more opportunities with filtering and pagination
     */
    public function explore()
    {
        // Get filter parameters
        $type = $this->request->getGet('type');
        $location = $this->request->getGet('location');
        $search = $this->request->getGet('search');
        $sortBy = $this->request->getGet('sort') ?? 'created_at';
        $sortOrder = $this->request->getGet('order') ?? 'DESC';
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 12;

        // Build query
        $builder = $this->jobOpportunityModel
            ->where('status', 'published')
            ->where('deleted_at', null);

        // Apply filters
        if (!empty($type) && $type !== 'all') {
            $builder->where('opportunity_type', $type);
        }

        if (!empty($location)) {
            $builder->groupStart()
                ->like('location', $location)
                ->orWhere('is_remote', 1)
                ->groupEnd();
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('company', $search)
                ->groupEnd();
        }

        // Get total count for pagination
        $total = $builder->countAllResults(false);

        // Apply sorting and pagination
        $opportunities = $builder
            ->orderBy($sortBy, $sortOrder)
            ->limit($perPage, ($page - 1) * $perPage)
            ->findAll();

        // Get filter options
        $opportunityTypes = $this->jobOpportunityModel
            ->select('opportunity_type')
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->groupBy('opportunity_type')
            ->findAll();

        $locations = $this->jobOpportunityModel
            ->select('location')
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->where('location IS NOT NULL')
            ->where('location !=', '')
            ->groupBy('location')
            ->findAll();

        // Calculate pagination
        $totalPages = ceil($total / $perPage);

        $title = "Explore All Opportunities - KEWASNET";
        $description = "Browse all available job opportunities in Kenya's WASH sector. Filter by type, location, and search for your perfect career opportunity.";

        $data = [
            'title' => $title,
            'description' => $description,
            'opportunities' => $opportunities,
            'opportunityTypes' => $opportunityTypes,
            'locations' => $locations,
            'filters' => [
                'type' => $type,
                'location' => $location,
                'search' => $search,
                'sort' => $sortBy,
                'order' => $sortOrder
            ],
            'pagination' => [
                'currentPage' => (int)$page,
                'totalPages' => $totalPages,
                'perPage' => $perPage,
                'total' => $total
            ],
            'newsletterTitle' => 'Never Miss an Opportunity',
            'newsletterDescription' => 'Subscribe to our newsletter to receive instant notifications about new job openings and career opportunities in the WASH sector.',
        ];

        return view('frontendV2/website/pages/opportunities/explore', $data);
    }

    /**
     * Handle job application submission
     */
    public function apply($slug)
    {
        // Validate CSRF token
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid security token. Please refresh the page and try again.'
            ]);
        }

        // Find the opportunity
        $opportunity = $this->jobOpportunityModel
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->first();

        if (!$opportunity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Job opportunity not found.'
            ]);
        }

        // Check if application deadline has passed
        if ($this->jobApplicationService->isDeadlinePassed($opportunity['application_deadline'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Application deadline has passed.'
            ]);
        }

        // Get application data from request
        $applicationData = [
            'first_name'            => $this->request->getPost('first_name'),
            'last_name'             => $this->request->getPost('last_name'),
            'application_email'     => $this->request->getPost('application_email'),
            'phone'                 => $this->request->getPost('phone'),
            'location'              => $this->request->getPost('location'),
            'experience_years'      => $this->request->getPost('experience_years'),
            'education_level'       => $this->request->getPost('education_level'),
            'cover_letter'          => $this->request->getPost('cover_letter'),
            'availability'          => $this->request->getPost('availability'),
            'salary_expectation'    => $this->request->getPost('salary_expectation')
        ];

        // Validate application data
        $validationResult = $this->jobApplicationService->validateApplicationData($applicationData);
        if (!$validationResult['success']) {
            return $this->response->setJSON($validationResult);
        }

        // Get and validate resume file
        $resumeFile = $this->request->getFile('resume');
        $fileValidationResult = $this->jobApplicationService->validateResumeFile($resumeFile);
        if (!$fileValidationResult['success']) {
            return $this->response->setJSON($fileValidationResult);
        }

        // Submit the application through the service
        $result = $this->jobApplicationService->submitApplication($applicationData, $opportunity, $resumeFile);
        
        return $this->response->setJSON($result);
    }
}
