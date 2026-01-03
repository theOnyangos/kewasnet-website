<?php

namespace App\Controllers\BackendV2;

use Carbon\Carbon;
use App\Services\DataTableService;
use App\Services\OpportunityService;
use App\Models\JobApplicantModel;
use App\Models\JobOpportunityModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\Exceptions\DatabaseException;

class OpportunitiesController extends BaseController
{

    protected $db;
    protected $model;
    protected $dataTableService;
    protected $opportunityService;
    protected $helpers = ['form', 'url'];
    protected const PAGE_TITLE = "Manage Opportunities - KEWASNET";
    protected const CREATE_PAGE_TITLE = "Create Job Opportunity - KEWASNET";
    protected const OPPORTUNITY_PAGE_TITLE = "Manage Opportunities";

    public function __construct()
    {
        $this->model = new JobOpportunityModel();
        $this->db = \Config\Database::connect();
        $this->dataTableService = new DataTableService();
        $this->opportunityService = new OpportunityService();
    }

    public function index()
    {
        return view('backendV2/pages/opportunities/index', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => self::OPPORTUNITY_PAGE_TITLE,
        ]);
    }

    /**
     * Applications page
     */
    public function applications()
    {
        return view('backendV2/pages/opportunities/applications', [
            'title' => 'Job Applications - KEWASNET',
            'dashboardTitle' => 'Job Applications',
        ]);
    }

    /**
     * Deleted opportunities page (admin only)
     */
    public function deleted()
    {
        // Check if user is admin
        if (session()->get('role_id') != 1) {
            return redirect()->to('auth/opportunities')->with('error', 'Access denied. Admin privileges required.');
        }

        return view('backendV2/pages/opportunities/deleted', [
            'title' => 'Deleted Opportunities - KEWASNET',
            'dashboardTitle' => 'Deleted Opportunities',
        ]);
    }

    /**
     * Create Job Opportunity
     */
    public function create()
    {
        return view('backendV2/pages/opportunities/create', [
            'title' => self::CREATE_PAGE_TITLE,
            'dashboardTitle' => self::OPPORTUNITY_PAGE_TITLE,
        ]);
    }

    /**
     * Get Job Opportunities
     */
    public function getJobOpportunities()
    {
        $model = model(JobOpportunityModel::class);
        $columns = ['id', 'slug', 'title', 'company', 'location', 'salary_min', 'salary_max', 'status', 'application_count', 'opportunity_type', 'created_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getJobOpportunities',   // Model method to get data
            'countJobOpportunities', // Model method to get count
            function($item) {        // Optional data formatter
                // Format or modify data before sending to client
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                return $item;
            }
        );
    }

    /**
     * Get Deleted Job Opportunities (admin only)
     */
    public function getDeletedOpportunities()
    {
        // Verify admin access
        if (session()->get('role_id') != 1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }

        $model = model(JobOpportunityModel::class);
        $columns = ['id', 'slug', 'title', 'company', 'location', 'salary_min', 'salary_max', 'status', 'application_count', 'opportunity_type', 'deleted_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getDeletedOpportunities',   // Model method to get data
            'countDeletedOpportunities', // Model method to get count
            function($item) {        // Optional data formatter
                // Format or modify data before sending to client
                $item['deleted_at'] = Carbon::parse($item['deleted_at'])->format('Y-m-d H:i:s');
                return $item;
            }
        );
    }

    /**
     * Get Job Applications
     */
    public function getJobApplications()
    {
        $model = model(JobApplicantModel::class);
        $columns = ['id', 'job_title', 'applicant_name', 'email', 'phone', 'status', 'created_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getJobApplications',   // Model method to get data
            'countJobApplications', // Model method to get count
            function($item) {        // Optional data formatter
                // Format or modify data before sending to client
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                return $item;
            }
        );
    }

    /**
     * Create Job Applications
     */
    public function createOpportunity()
    {        
        try {
            // Log incoming data
            $postData = $this->request->getPost();
            $files = $this->request->getFiles();
            
            // Validate the input data
            $rules = [
                'title'             => 'required|max_length[255]',
                'description'       => 'required',
                'opportunity_type'  => 'required|in_list[full-time,part-time,contract,internship,freelance]',
                'status'            => 'required|in_list[draft,published,closed]',
                'location'          => 'permit_empty|max_length[255]',
                'salary_min'        => 'permit_empty|decimal',
                'salary_max'        => 'permit_empty|decimal',
                'salary_currency'   => 'permit_empty|max_length[10]',
                'contract_duration' => 'permit_empty|max_length[100]',
                'hours_per_week'    => 'permit_empty|integer|greater_than[0]|less_than_equal_to[168]',
                'is_remote'         => 'permit_empty|in_list[0,1]',
                'company'           => 'permit_empty|max_length[255]',
                'application_deadline' => 'permit_empty|valid_date',
                'document'          => 'permit_empty|uploaded[document]|max_size[document,51200]|ext_in[document,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf]'
            ];

            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                log_message('error', 'OpportunitiesController: Validation failed: ' . json_encode($errors));
                
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $errors
                ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Prepare files array for service
            $filesArray = [];
            $file = $this->request->getFile('document');
            if ($file && $file->isValid()) {
                $filesArray['document'] = $file;
                log_message('debug', 'OpportunitiesController: Document file prepared for upload - Name: ' . $file->getClientName() . ', Size: ' . $file->getSize());
            } else {
                log_message('debug', 'OpportunitiesController: No valid document file uploaded');
            }

            // Create opportunity using service
            $result = $this->opportunityService->createOpportunity($postData, $filesArray);
            // log_message('info', 'OpportunitiesController: Opportunity creation result: ' . json_encode($result));

            if ($result['status'] === 'success') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'redirect' => site_url('auth/opportunities')
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON($result)->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

        } catch (\RuntimeException $e) {
            log_message('error', 'OpportunitiesController: RuntimeException: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => []
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);

        } catch (DatabaseException $e) {
            log_message('error', 'OpportunitiesController: DatabaseException: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Database error occurred',
                'errors' => [$e->getMessage()]
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'OpportunitiesController: Exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => [$e->getMessage()]
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Job Applications with proper endpoint
     */
    public function getApplications()
    {
        $model = model(JobApplicantModel::class);
        $columns = ['id', 'applicant_name', 'job_title', 'email', 'phone', 'status', 'created_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getJobApplications',   // Model method to get data
            'countJobApplications', // Model method to get count
            function($item) {        // Optional data formatter
                // Format or modify data before sending to client
                $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                return $item;
            }
        );
    }

    /**
     * View single opportunity
     */
    public function view($id = null)
    {
        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Opportunity not found');
        }

        $opportunity = $this->model->find($id);
        if (!$opportunity) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Opportunity not found');
        }

        return view('backendV2/pages/opportunities/view', [
            'title' => 'View Opportunity - KEWASNET',
            'dashboardTitle' => 'View Opportunity',
            'opportunity' => $opportunity
        ]);
    }

    /**
     * Edit opportunity
     */
    public function edit($id = null)
    {
        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Opportunity not found');
        }

        $opportunity = $this->model->find($id);
        if (!$opportunity) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Opportunity not found');
        }

        return view('backendV2/pages/opportunities/edit', [
            'title' => 'Edit Opportunity - KEWASNET',
            'dashboardTitle' => 'Edit Opportunity',
            'opportunity' => $opportunity
        ]);
    }

    /**
     * Update opportunity
     */
    public function update($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Opportunity ID is required'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            // Check if opportunity exists
            $existingOpportunity = $this->model->find($id);
            if (!$existingOpportunity) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Opportunity not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Validate the input data
            $rules = [
                'title'             => 'required|max_length[255]',
                'description'       => 'required',
                'opportunity_type'  => 'required|in_list[full-time,part-time,contract,internship,freelance]',
                'status'            => 'required|in_list[draft,published,closed]',
                'company'           => 'permit_empty|max_length[255]',
                'location'          => 'permit_empty|max_length[255]',
                'salary_min'        => 'permit_empty|decimal',
                'salary_max'        => 'permit_empty|decimal',
                'salary_currency'   => 'permit_empty|max_length[10]',
                'contract_duration' => 'permit_empty|max_length[100]',
                'hours_per_week'    => 'permit_empty|integer|greater_than[0]|less_than_equal_to[168]',
                'is_remote'         => 'permit_empty|in_list[0,1]',
                'application_deadline' => 'permit_empty|valid_date',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $this->validator->getErrors()
                ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
            }

            $postData = $this->request->getPost();

            // Handle file upload
            $file = $this->request->getFile('document');
            $uploadedFile = null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Validate file
                $validationRule = [
                    'document' => [
                        'uploaded[document]',
                        'max_size[document,51200]',  // 50MB
                        'ext_in[document,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf]',
                    ],
                ];

                if (!$this->validate($validationRule)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'File validation failed',
                        'errors'  => $this->validator->getErrors()
                    ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
                }

                $uploadedFile = $file;
            }

            // Use service to update opportunity
            $result = $this->opportunityService->updateOpportunity($id, $postData, $uploadedFile);

            if ($result['success']) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $result['message'] ?? 'Opportunity updated successfully',
                    'data' => $result['data'] ?? null,
                    'redirect' => base_url('auth/opportunities')
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to update opportunity',
                    'errors' => $result['errors'] ?? []
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            log_message('error', 'OpportunitiesController::update - Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update job opportunity: ' . json_encode([$e->getMessage()])
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete opportunity
     */
    public function delete($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Opportunity ID is required'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            // Check if opportunity exists
            $opportunity = $this->model->find($id);
            if (!$opportunity) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Opportunity not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Use service to delete opportunity (handles file cleanup)
            $result = $this->opportunityService->deleteOpportunity($id);

            if ($result['success']) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $result['message'] ?? 'Opportunity deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to delete opportunity'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            log_message('error', 'OpportunitiesController::delete - Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting the opportunity: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Application management methods
     */

    /**
     * View single application
     */
    public function viewApplication($id = null)
    {
        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Application not found');
        }

        $applicantModel = new JobApplicantModel();
        $application = $applicantModel->find($id);
        
        if (!$application) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Application not found');
        }

        return view('backendV2/pages/opportunities/application_view', [
            'title' => 'View Application - KEWASNET',
            'dashboardTitle' => 'View Application',
            'application' => $application
        ]);
    }

    /**
     * Review application
     */
    public function reviewApplication($id = null)
    {
        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Application not found');
        }

        $applicantModel = new JobApplicantModel();
        $application = $applicantModel->find($id);
        
        if (!$application) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Application not found');
        }

        return view('backendV2/pages/opportunities/application_review', [
            'title' => 'Review Application - KEWASNET',
            'dashboardTitle' => 'Review Application',
            'application' => $application
        ]);
    }

    /**
     * Edit application
     */
    public function editApplication($id = null)
    {
        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Application not found');
        }

        $applicantModel = new JobApplicantModel();
        $application = $applicantModel->find($id);
        
        if (!$application) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Application not found');
        }

        return view('backendV2/pages/opportunities/application_edit', [
            'title' => 'Edit Application - KEWASNET',
            'dashboardTitle' => 'Edit Application',
            'application' => $application
        ]);
    }

    /**
     * Update application status
     */
    public function updateApplicationStatus($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Application ID is required'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $rules = [
                'status' => 'required|in_list[pending,reviewed,interviewed,rejected,hired]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $this->validator->getErrors()
                ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
            }

            $applicantModel = new JobApplicantModel();
            $updateData = [
                'status' => $this->request->getPost('status'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($applicantModel->update($id, $updateData)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Application status updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update application status'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete application
     */
    public function deleteApplication($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Application ID is required'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $applicantModel = new JobApplicantModel();
            
            if ($applicantModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Application deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete application'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Restore deleted opportunity (admin only)
     */
    public function restore($id = null)
    {
        // Verify AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        // Verify admin access
        if (session()->get('role_id') != 1) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_FORBIDDEN,
                'Access denied. Admin privileges required.'
            );
        }

        if (!$id) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_BAD_REQUEST,
                'Opportunity ID is required'
            );
        }

        try {
            // Find the deleted opportunity
            $opportunity = $this->model->onlyDeleted()->find($id);
            
            if (!$opportunity) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Opportunity not found'
                );
            }

            // Restore the opportunity by setting deleted_at to null
            $db = \Config\Database::connect();
            $db->table('job_opportunities')
                ->where('id', $id)
                ->update(['deleted_at' => null]);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Opportunity restored successfully'
            );

        } catch (\Exception $e) {
            log_message('error', 'Error restoring opportunity: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'An error occurred while restoring the opportunity'
            );
        }
    }

    /**
     * Permanently delete an opportunity (admin only)
     */
    public function permanentDelete($id = null)
    {
        // Verify AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        // Verify admin access
        if (session()->get('role_id') != 1) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_FORBIDDEN,
                'Access denied. Admin privileges required.'
            );
        }

        if (!$id) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_BAD_REQUEST,
                'Opportunity ID is required'
            );
        }

        try {
            // Find the deleted opportunity
            $opportunity = $this->model->onlyDeleted()->find($id);
            
            if (!$opportunity) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Opportunity not found'
                );
            }

            // Permanently delete the opportunity (force delete)
            $db = \Config\Database::connect();
            $db->table('job_opportunities')
                ->where('id', $id)
                ->delete();

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Opportunity permanently deleted'
            );

        } catch (\Exception $e) {
            log_message('error', 'Error permanently deleting opportunity: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'An error occurred while permanently deleting the opportunity'
            );
        }
    }

    /**
     * Handle validation errors
     */
    protected function failValidationErrors(array $errors): ResponseInterface
    {
        return $this->respond([
            'status' => 'error',
            'errors' => $errors,
            'message' => 'Validation failed'
        ], ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }
}
