<?php

namespace App\Controllers\BackendV2;

use Carbon\Carbon;
use App\Services\ResourceService;
use App\Services\DataTableService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ResourcesController extends BaseController
{    
    protected const PAGE_TITLE = "Manage Resources - KEWASNET";

    protected $dataTableService;
    protected $resourceService;

    public function __construct()
    {
        $this->dataTableService = new DataTableService();
        $this->resourceService = new ResourceService();
    }

    /**
     * Display the resources management page
     */
    public function index()
    {
        $categoryModel = model('App\Models\ResourceCategory');
        $categoriesObjects = $categoryModel->findAll();
        
        // Convert objects to arrays for consistent access in view
        $categories = [];
        if ($categoriesObjects) {
            foreach ($categoriesObjects as $cat) {
                $categories[] = (array) $cat;
            }
        }
        
        return view('backendV2/pages/resources/index', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => 'Document Resource(s)',
            'activeView' => 'resources',
            'categories' => $categories
        ]);
    }

    /**
     * Display the create new resource page
     */
    public function createPage()
    {
        $categoryModel = model('App\Models\ResourceCategory');
        $categoriesObjects = $categoryModel->findAll();
        
        // Convert objects to arrays for consistent access in view
        $categories = [];
        if ($categoriesObjects) {
            foreach ($categoriesObjects as $cat) {
                $categories[] = (object) $cat; // Keep as objects for the view
            }
        }
        
        return view('backendV2/pages/resources/create', [
            'title' => 'Create New Resource - KEWASNET',
            'dashboardTitle' => 'Create New Resource',
            'activeView' => 'resources',
            'categories' => $categories
        ]);
    }

    /**
     * Display the categories management page
     */
    public function categories()
    {
        $categoryModel = model('App\Models\ResourceCategory');
        $categoriesObjects = $categoryModel->findAll();
        
        // Convert objects to arrays for consistent access in view
        $categories = [];
        if ($categoriesObjects) {
            foreach ($categoriesObjects as $cat) {
                $categories[] = (array) $cat;
            }
        }
        
        return view('backendV2/pages/resources/index', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => 'Document Resource Categories',
            'activeView' => 'categories',
            'categories' => $categories
        ]);
    }

    /**
     * Display the edit resource page
     */
    public function edit($id)
    {
        $model = model('App\Models\DocumentResource');
        $categoryModel = model('App\Models\ResourceCategory');
        $attachmentModel = model('App\Models\FileAttachment');
        
        $resourceObject = $model->find($id);
        if (!$resourceObject) {
            return redirect()->to('/auth/resources')->with('error', 'Resource not found');
        }
        
        // Convert resource object to array
        $resource = (array) $resourceObject;
        
        // Get attachments - model returns objects, so convert to array for view
        $attachmentsObjects = $attachmentModel->where('attachable_type', 'document_resources')
            ->where('attachable_id', $id)
            ->findAll();
        
        // Convert objects to arrays
        $attachments = [];
        if ($attachmentsObjects) {
            foreach ($attachmentsObjects as $att) {
                $attachments[] = (array) $att;
            }
        }
        
        // Get categories for dropdown and convert to arrays
        $categoriesObjects = $categoryModel->findAll();
        $categories = [];
        if ($categoriesObjects) {
            foreach ($categoriesObjects as $cat) {
                $categories[] = (array) $cat;
            }
        }
        
        return view('backendV2/pages/resources/edit', [
            'title' => 'Edit Resource - KEWASNET',
            'dashboardTitle' => 'Edit Resource',
            'activeView' => 'resources',
            'resource' => $resource,
            'attachments' => $attachments,
            'categories' => $categories
        ]);
    }

    /**
     * Get resources data for DataTables
     */
    public function getResourcesData()
    {
        $model = model('App\Models\DocumentResource');
        $columns = ['id', 'document_category_id', 'title', 'description', 'view_count', 'is_featured', 'is_published', 'download_url', 'total_downloads', 'is_active', 'created_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getResources',
            'countAllResources',
        );
    }

    /**
     * Get resource categories for DataTables
     */
    public function getResourceCategories()
    {
        $model = model('App\Models\DocumentResourceCategory');
        $columns = ['id', 'name', 'description', 'created_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getCategoriesTable',
            'countAllCategories',
        );
    }

    /**
     * Create a new resource using ResourceService
     */
    public function create()
    {
        try {
            $postData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Validate the input data
            $rules = [
                'resource_title'        => 'required|min_length[3]|max_length[255]',
                'category_id'           => 'required|is_not_unique[resource_categories.id]',
                'resource_description'  => 'permit_empty|max_length[500]',
                'is_featured'           => 'permit_empty|in_list[0,1]',
                'is_published'          => 'permit_empty|in_list[0,1]'
            ];

            // Add validation for files if present
            if (!empty($files['resource_filename'])) {
                $rules['resource_filename.*'] = 'uploaded[resource_filename]|max_size[resource_filename,20480]|ext_in[resource_filename,pdf,doc,docx,xls,xlsx,ppt,pptx]';
            }

            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                log_message('error', 'ResourcesController: Resource validation failed: ' . json_encode($errors));
                
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $errors
                ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Handle multiple file uploads
            $validFiles = [];
            
            if (!empty($files['resource_filename'])) {
                foreach ($files['resource_filename'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $validFiles[] = $file;
                        log_message('debug', 'ResourcesController: Valid file received: ' . $file->getClientName());
                    } else {
                        $errorCode = $file->getError();
                        if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
                            return $this->response->setJSON([
                                'status' => 'error',
                                'message' => 'File too large. Please check your PHP configuration (upload_max_filesize and post_max_size).',
                                'errors' => ['resource_filename' => 'File size exceeds server limit']
                            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                        }
                        log_message('error', 'ResourcesController: Invalid file - Error: ' . $file->getErrorString());
                    }
                }
            }
            
            if (empty($validFiles)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No valid files uploaded',
                    'errors' => ['resource_filename' => 'Please upload at least one valid file']
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Prepare files array for service
            $filesArray = ['resource_filename' => $validFiles];

            // Use ResourceService to create the resource
            $result = $this->resourceService->createNewResource($postData, $filesArray);

            if ($result['status'] === 'success') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $result['message'],
                    'data' => $result['data']
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

        } catch (\RuntimeException $e) {
            log_message('error', 'ResourcesController: RuntimeException: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => []
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);

        } catch (DatabaseException $e) {
            log_message('error', 'ResourcesController: DatabaseException: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Database error occurred',
                'errors' => [$e->getMessage()]
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'ResourcesController: Exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => [$e->getMessage()]
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new resource category using ResourceService
     */
    public function createResourceCategory()
    {
        try {
            $postData = $this->request->getPost();

            // Validate the input data
            $rules = [
                'resource_category_name' => 'required|min_length[3]|max_length[255]',
                'resource_category_description' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                log_message('error', 'ResourcesController: Category validation failed: ' . json_encode($errors));
                
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $errors
                ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Use ResourceService to create the category
            $result = $this->resourceService->createResourceCategory($postData);

            if ($result['status'] === 'success') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $result['message'],
                    'data' => $result['data']
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $e) {
            log_message('error', 'ResourcesController: Exception creating category: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the category',
                'errors' => [$e->getMessage()]
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleFeatured($id)
    {
        $model = model('App\Models\DocumentResource');
        $featuredStatus = $this->request->getPost('is_featured');
        if ($model->update($id, ['is_featured' => $featuredStatus])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Updated']);
        }
        return $this->response->setJSON(['status' => 'error'])->setStatusCode(500);
    }

    public function deleteResource($id)
    {
        $model = model('App\Models\DocumentResource');
        if ($model->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Deleted']);
        }
        return $this->response->setJSON(['status' => 'error'])->setStatusCode(500);
    }

    public function getResource($id)
    {
        $model = model('App\Models\DocumentResource');
        $attachmentModel = model('App\Models\FileAttachment');
        
        $resource = $model->find($id);
        if (!$resource) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resource not found'])->setStatusCode(404);
        }
        
        // Get attachments and convert to array
        $attachments = $attachmentModel->where('attachable_type', 'document_resources')
            ->where('attachable_id', $id)
            ->findAll();
        
        // Convert attachment objects to arrays for JSON serialization
        $attachmentsArray = [];
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $attachmentsArray[] = [
                    'id' => $attachment->id,
                    'original_name' => $attachment->original_name,
                    'file_name' => $attachment->file_name,
                    'file_path' => $attachment->file_path,
                    'file_type' => $attachment->file_type,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                    'created_at' => $attachment->created_at,
                ];
            }
        }
        
        $resource['attachments'] = $attachmentsArray;
        
        return $this->response->setJSON(['status' => 'success', 'data' => $resource]);
    }

    public function updateResource($id)
    {
        try {
            // Check if resource exists
            $model = model('App\Models\DocumentResource');
            $existingResource = $model->find($id);
            
            if (!$existingResource) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Resource not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Get form data
            $data = $this->request->getPost();
            $files = $this->request->getFiles();
            
            // Log file info and handle file upload
            $validFiles = [];
            if (!empty($files['resource_filename'])) {
                foreach ($files['resource_filename'] as $file) {
                    if ($file && $file->isValid()) {
                        $validFiles[] = $file;
                        log_message('debug', 'ResourcesController: Valid file received - Name: ' . $file->getName() . ', Size: ' . $file->getSize() . ' bytes');
                    } else if ($file) {
                        $errorCode = $file->getError();
                        $errorMsg = $file->getErrorString();
                        log_message('debug', 'ResourcesController: Invalid file - Name: ' . $file->getName() . ', Error: ' . $errorMsg . ', Error Code: ' . $errorCode);
                        
                        // Return error for file upload issues (especially size issues)
                        if ($errorCode == UPLOAD_ERR_INI_SIZE || $errorCode == UPLOAD_ERR_FORM_SIZE) {
                            return $this->response->setJSON([
                                'status' => 'error',
                                'message' => 'File is too large. Maximum allowed size is ' . ini_get('upload_max_filesize') . '. Please restart your development server (php spark serve) if you recently changed PHP settings.',
                                'errors' => ['file' => $errorMsg]
                            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                        }
                    }
                }
            }
            
            if (empty($validFiles)) {
                log_message('debug', 'ResourcesController: No valid files received');
            }

            // Validate the data
            $validationRules = [
                'resource_name' => 'required|min_length[3]|max_length[255]',
                'category_id' => 'required|is_not_unique[resource_categories.id]',
                'resource_description' => 'permit_empty|max_length[1000]'
            ];

            if (!$this->validate($validationRules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Update resource using service (pass multiple files if valid)
            $result = $this->resourceService->updateResource($id, $data, $validFiles);

            if ($result['status'] === 'success') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Resource updated successfully',
                    'data' => $result['data']
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $e) {
            log_message('error', 'ResourcesController: Exception updating resource: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred while updating the resource',
                'errors' => [$e->getMessage()]
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
