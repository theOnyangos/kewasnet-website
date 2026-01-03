<?php

namespace App\Controllers\BackendV2;

use App\Models\Program;
use App\Services\DataTableService;
use App\Services\ProgramService;
use App\Controllers\BaseController;
use App\Exceptions\ValidationException;
use CodeIgniter\HTTP\ResponseInterface;

class ProgramsController extends BaseController
{
    protected $programModel;
    protected $dataTableService;
    protected $programService;

    public function __construct()
    {
        $this->programModel = new Program();
        $this->dataTableService = new DataTableService();
        $this->programService = new ProgramService();
        helper(['text', 'url', 'form', 'filesystem']);
    }

    public function index()
    {
        $title = "Programs Management - KEWASNET";

        $data = [
            'title' => $title,
        ];

        return view('backendV2/pages/programs/index', $data);
    }

    public function createForm()
    {
        $title = "Create New Program - KEWASNET";

        $data = [
            'title' => $title,
        ];

        return view('backendV2/pages/programs/create', $data);
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('auth/programs'))->with('error', 'Program ID is required');
        }

        $program = $this->programModel->find($id);

        if (!$program) {
            return redirect()->to(base_url('auth/programs'))->with('error', 'Program not found');
        }

        // Convert to array if it's an object
        if (is_object($program)) {
            $program = (array) $program;
        }

        $data = [
            'title' => 'Edit Program - KEWASNET',
            'program' => $program
        ];

        return view('backendV2/pages/programs/edit', $data);
    }

    public function getPrograms()
    {
        try {
            $request = $this->request->getPost();
            
            // DataTable parameters
            $start = (int)($request['start'] ?? 0);
            $length = (int)($request['length'] ?? 10);
            $searchValue = $request['search']['value'] ?? '';
            $orderColumn = $request['order'][0]['column'] ?? 0;
            $orderDir = $request['order'][0]['dir'] ?? 'desc';
            
            // Column mapping
            $columns = ['title', 'description', 'is_active', 'is_featured', 'sort_order', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'created_at';
            
            // Get programs data
            $programs = $this->programModel->getProgramsTable($start, $length, $searchValue, $orderBy, $orderDir);
            $totalRecords = $this->programModel->getTotalProgramsCount();
            $filteredRecords = $totalRecords; // Simplified for now
            
            return $this->response->setJSON([
                'draw' => intval($request['draw'] ?? 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $programs
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getPrograms: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load programs data'
            ]);
        }
    }

    public function getStats()
    {
        try {
            $stats = $this->programModel->getProgramStats();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getStats: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load statistics'
            ]);
        }
    }

    public function create()
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $programData = $this->request->getPost();
            
            // Get files properly - check for image_url file
            $files = [];
            $imageFile = $this->request->getFile('image_url');
            
            if ($imageFile && $imageFile->isValid()) {
                $files['image_url'] = $imageFile;
                log_message('info', 'Image file received: ' . $imageFile->getName() . ' (' . $imageFile->getSize() . ' bytes)');
            } else {
                log_message('info', 'No valid image file in request');
            }

            // Debug log
            log_message('info', 'Program creation data: ' . json_encode($programData));

            $program = $this->programService->createProgram($programData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Program created successfully!',
                'data' => $program,
                'redirect_url' => site_url('auth/programs'),
                'view_url' => site_url('programs/' . $program['slug'])
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Program creation error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create program: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function update($id = null)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            if (!$id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Program ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if program exists
            $program = $this->programModel->find($id);
            if (!$program) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Program not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $programData = $this->request->getPost();
            
            // Get files properly - check for image_url file
            $files = [];
            $imageFile = $this->request->getFile('image_url');
            
            if ($imageFile && $imageFile->isValid()) {
                $files['image_url'] = $imageFile;
                log_message('info', 'Image file received for update: ' . $imageFile->getName() . ' (' . $imageFile->getSize() . ' bytes)');
            } else {
                log_message('info', 'No valid image file in update request');
            }

            // Debug log
            log_message('info', 'Program update data for ID ' . $id . ': ' . json_encode($programData));

            $updatedProgram = $this->programService->updateProgram($id, $programData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Program updated successfully!',
                'data' => $updatedProgram,
                'redirect_url' => site_url('auth/programs'),
                'view_url' => site_url('programs/' . $updatedProgram['slug'])
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Program update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update program: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus($id = null)
    {
        try {
            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Program ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Get the new status from POST data
            $status = $this->request->getPost('status');

            if ($status === null) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status value is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if program exists
            $program = $this->programModel->find($id);
            if (!$program) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Program not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Update the status
            $result = $this->programModel->update($id, ['is_active' => $status]);

            if ($result) {
                $action = $status == 1 ? 'activated' : 'deactivated';
                return $this->response->setJSON([
                    'success' => true,
                    'status' => 'success',
                    'message' => "Program {$action} successfully"
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update program status'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in toggleStatus: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating status: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Program ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if program exists (withDeleted to allow checking even if soft deleted)
            $program = $this->programModel->withDeleted()->find($id);
            if (!$program) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Program not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Delete associated image file if it exists
            if (!empty($program->image_url)) {
                $imagePath = FCPATH . $program->image_url;
                if (file_exists($imagePath)) {
                    if (@unlink($imagePath)) {
                        log_message('info', 'Deleted program image: ' . $imagePath);
                    } else {
                        log_message('warning', 'Failed to delete program image: ' . $imagePath);
                    }
                }
            }

            // Delete the program (soft delete - sets deleted_at timestamp)
            $result = $this->programModel->delete($id);

            if ($result) {
                log_message('info', 'Program soft deleted: ' . $id);
                return $this->response->setJSON([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Program deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete program'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in delete: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting the program: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //======== Helper Methods ==========

    protected function failValidationErrors(array $errors): ResponseInterface
    {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validation failed. Fix errors in inputs and try again.',
            'errors' => $errors,
            'error_type' => 'validation'
        ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }
}
