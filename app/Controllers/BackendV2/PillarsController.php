<?php

namespace App\Controllers\BackendV2;

use App\Models\Pillar;
use App\Models\DocumentType;
use App\Models\Resource;
use App\Models\Contributor;
use App\Models\ResourceContributor;
use App\Models\FileAttachment;
use App\Services\PillarService;
use App\Models\ResourceCategory;
use App\Services\DataTableService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Exceptions\ValidationException;

class PillarsController extends BaseController
{
    protected const PAGE_TITLE = "Manage Pillars - KEWASNET";
    protected const BLOGS_PAGE_TITLE = "Pillars Management";
    protected const CREATE_PILLAR_PAGE_TITLE = "Create New Pillar - KEWASNET";
    protected const CREATE_PILLAR_DASH_TITLE = "Create New Pillar - Dashboard";
    protected const CREATE_PILLAR_ARTICLE_PAGE_TITLE = "Create New Pillar Article - KEWASNET";
    protected const CREATE_PILLAR_ARTICLE_DASH_TITLE = "Create New Pillar Article - Dashboard";

    protected Pillar $pillarModel;
    protected PillarService $pillarService;
    protected DocumentType $documentTypeModel;
    protected ResourceCategory $resourceCategoryModel;
    protected Resource $resourceModel;
    protected Contributor $contributorModel;
    protected ResourceContributor $resourceContributorModel;
    protected FileAttachment $fileAttachmentModel;
    protected DataTableService $dataTableService;

    public function __construct()
    {
        $this->pillarModel = new Pillar();
        $this->pillarService = new PillarService();
        $this->documentTypeModel = new DocumentType();
        $this->resourceCategoryModel = new ResourceCategory();
        $this->resourceModel = new Resource();
        $this->contributorModel = new Contributor();
        $this->resourceContributorModel = new ResourceContributor();
        $this->fileAttachmentModel = new FileAttachment();
        $this->dataTableService = new DataTableService();
    }

    public function index()
    {
        try {
            // Get pillar statistics with fallback values
            $pillarStats = $this->pillarModel->getPillarStats();
            $resourceStats = $this->resourceModel->getResourceStats();
            $documentTypeCount = $this->documentTypeModel->countAllResults();

            $combinedStats = [
                'total_pillars'         => $pillarStats->total_pillars ?? 0,
                'active_pillars'        => $pillarStats->active_pillars ?? 0,
                'inactive_pillars'      => $pillarStats->inactive_pillars ?? 0,
                'total_articles'        => $resourceStats->total_resources ?? 0,
                'published_articles'    => $resourceStats->published_resources ?? 0,
                'draft_articles'        => $resourceStats->draft_resources ?? 0,
                'total_downloads'       => $resourceStats->total_downloads ?? 0,
                'total_document_types'  => $documentTypeCount ?? 0
            ];
        } catch (\Exception $e) {
            // Log the error and provide fallback stats
            log_message('error', 'Error getting pillar stats: ' . $e->getMessage());
            
            $combinedStats = [
                'total_pillars'         => 0,
                'active_pillars'        => 0,
                'inactive_pillars'      => 0,
                'total_articles'        => 0,
                'published_articles'    => 0,
                'draft_articles'        => 0,
                'total_downloads'       => 0,
                'total_document_types'  => 0
            ];
        }

        return view('backendV2/pages/pillars/index', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => self::BLOGS_PAGE_TITLE,
            'pillarStats' => $combinedStats
        ]);
    }

    public function resources()
    {
        return view('backendV2/pages/pillars/resources', [
            'title' => 'Pillars Resources - KEWASNET',
            'dashboardTitle' => 'Pillars Resources Management'
        ]);
    }

    public function articles()
    {
        return view('backendV2/pages/pillars/articles', [
            'title' => 'Pillar Articles - KEWASNET',
            'dashboardTitle' => 'Articles Management'
        ]);
    }

    public function documentTypes()
    {
        return view('backendV2/pages/pillars/document-types', [
            'title' => 'Document Types - KEWASNET',
            'dashboardTitle' => 'Document Types Management'
        ]);
    }

    public function create()
    {
        return view('backendV2/pages/pillars/create', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => self::BLOGS_PAGE_TITLE
        ]);
    }

    public function edit($id)
    {
        // Use where() for UUID lookup to ensure it works correctly
        $pillar = $this->pillarModel->where('id', $id)->first();
        
        if (!$pillar) {
            return redirect()->to(site_url('auth/pillars'))
                ->with('error', 'Pillar not found');
        }

        // Convert to array if it's an object
        if (is_object($pillar)) {
            $pillar = (array) $pillar;
        }

        // Ensure image_path is properly formatted
        if (!empty($pillar['image_path'])) {
            // If it's already a full URL, use it as is
            // If it's a relative path, ensure it's accessible
            if (strpos($pillar['image_path'], 'http') !== 0 && strpos($pillar['image_path'], '/') !== 0) {
                // It's a relative path, make it a full URL
                $pillar['image_path'] = base_url($pillar['image_path']);
            }
        }

        return view('backendV2/pages/pillars/edit', [
            'title' => 'Edit Pillar - KEWASNET',
            'dashboardTitle' => 'Edit Pillar',
            'pillar' => $pillar
        ]);
    }

    public function createResourceCategory()
    {
        $pillars = $this->pillarModel->select('id, title')->findAll();

        return view('backendV2/pages/pillars/create-resource-category', [
            'title'          => 'Create Resource Category - KEWASNET',
            'dashboardTitle' => 'Create Resource Category',
            'pillars'        => $pillars
        ]);
    }

    public function editResourceCategory($id)
    {
        try {
            // Find the category
            $category = $this->resourceCategoryModel->where('id', $id)->first();
            
            if (!$category) {
                return redirect()->to(base_url('auth/pillars/resources'))
                    ->with('error', 'Resource category not found');
            }

            // Convert to array if object
            if (is_object($category)) {
                $category = (array) $category;
            }

            $pillars = $this->pillarModel->select('id, title')->findAll();

            return view('backendV2/pages/pillars/edit-resource-category', [
                'title'    => 'Edit Resource Category - KEWASNET',
                'category' => $category,
                'pillars'  => $pillars
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error loading edit resource category: ' . $e->getMessage());
            return redirect()->to(base_url('auth/pillars/resources'))
                ->with('error', 'Error loading resource category');
        }
    }

    public function handleEditResourceCategory($id)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $categoryData = $this->request->getPost();
            $categoryId = $categoryData['category_id'] ?? $id;

            // Debug log
            log_message('info', 'Resource category update data: ' . json_encode($categoryData));

            $category = $this->pillarService->updateResourceCategory($categoryId, $categoryData);

            $response = [
                'status'        => 'success',
                'message'       => 'Resource category updated successfully!',
                'data'          => $category,
                'redirect_url'  => site_url('auth/pillars/resources')
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Resource category update error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update resource category: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function createPillarArticle()
    {
        $pillars = $this->pillarModel->findAll();
        $documentTypes = $this->documentTypeModel->findAll();
        $categories = $this->resourceCategoryModel->findAll();

        return view('backendV2/pages/pillars/create-pillar-article', [
            'title' => self::CREATE_PILLAR_ARTICLE_PAGE_TITLE,
            'dashboardTitle' => self::CREATE_PILLAR_ARTICLE_DASH_TITLE,
            'pillars' => $pillars,
            'documentTypes' => $documentTypes,
            'categories' => $categories
        ]);
    }

    public function editPillarArticle($id)
    {
        try {
            // Get the article
            $article = $this->resourceModel->where('id', $id)->first();
            
            if (!$article) {
                return redirect()->to(site_url('auth/pillars/articles'))
                    ->with('error', 'Article not found');
            }
            
            // Convert to array if object
            if (is_object($article)) {
                if (method_exists($article, 'toArray')) {
                    $article = $article->toArray();
                } else {
                    $article = (array) $article;
                }
            }
            
            // Get related data
            $pillars = $this->pillarModel->findAll();
            $documentTypes = $this->documentTypeModel->findAll();
            $categories = $this->resourceCategoryModel->findAll();
            
            // Get contributors
            $contributors = $this->pillarService->getContributorsForArticle($id);
            
            // Get attachments
            $attachments = $this->fileAttachmentModel
                ->where('attachable_id', $id)
                ->where('attachable_type', 'resources')
                ->findAll();
            
            // Convert attachments to array
            if (!empty($attachments)) {
                foreach ($attachments as &$attachment) {
                    if (is_object($attachment)) {
                        if (method_exists($attachment, 'toArray')) {
                            $attachment = $attachment->toArray();
                        } else {
                            $attachment = (array) $attachment;
                        }
                    }
                }
                unset($attachment);
            }
            
            return view('backendV2/pages/pillars/edit-pillar-article', [
                'title' => 'Edit Pillar Article - KEWASNET',
                'dashboardTitle' => 'Edit Article',
                'article' => $article,
                'pillars' => $pillars,
                'documentTypes' => $documentTypes,
                'categories' => $categories,
                'contributors' => $contributors,
                'attachments' => $attachments ?? []
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error loading edit article page: ' . $e->getMessage());
            return redirect()->to(site_url('auth/pillars/articles'))
                ->with('error', 'Failed to load article for editing');
        }
    }

    public function handleEditPillarArticle($id)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $articleId = $this->request->getPost('article_id') ?? $id;
            $formData = $this->request->getPost();
            $files = $this->request->getFiles();
            
            // Get files to delete
            $filesToDelete = $this->request->getPost('files_to_delete') ?? [];
            
            log_message('info', 'Article update data: ' . json_encode($formData));
            log_message('info', 'Files to delete: ' . json_encode($filesToDelete));
            
            $article = $this->pillarService->updatePillarArticle($articleId, $formData, $files, $filesToDelete);

            $response = [
                'status' => 'success',
                'message' => 'Article updated successfully!',
                'data' => $article,
                'redirect_url' => site_url('auth/pillars/articles')
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_OK);
                                 
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Article update error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update article: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function handleCreatePillar()
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $pillarData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Debug log
            log_message('info', 'Pillar creation data: ' . json_encode($pillarData));
            log_message('info', 'Pillar creation files: ' . json_encode(array_keys($files)));
            
            $pillar = $this->pillarService->createPillar($pillarData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Pillar created successfully!',
                'data' => $pillar,
                'redirect_url' => site_url('auth/pillars')
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_CREATED);
                                 
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Pillar creation error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create pillar: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function handleEdit($id = null)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $pillarData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Use pillar_id from POST as primary source, fallback to route parameter
            $pillarId = !empty($pillarData['pillar_id']) ? $pillarData['pillar_id'] : $id;
            
            if (empty($pillarId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Pillar ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
            
            log_message('info', 'Pillar update - Route ID: ' . ($id ?? 'null') . ', POST ID: ' . ($pillarData['pillar_id'] ?? 'none') . ', Using ID: ' . $pillarId);
            log_message('info', 'Pillar update data: ' . json_encode($pillarData));
            log_message('info', 'Pillar update files: ' . json_encode(array_keys($files)));
            
            $updatedPillar = $this->pillarService->updatePillar($pillarId, $pillarData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Pillar updated successfully!',
                'data' => $updatedPillar,
                'redirect_url' => site_url('auth/pillars')
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_OK);
                                 
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Pillar update error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update pillar: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function removePillarImage($id)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $result = $this->pillarService->removePillarImage($id);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pillar image removed successfully'
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to remove pillar image'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            log_message('error', 'Pillar image removal error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while removing the image: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handleCreateResourceCategory()
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $categoryData = $this->request->getPost();

            // Debug log
            log_message('info', 'Resource category creation data: ' . json_encode($categoryData));

            $category = $this->pillarService->createResourceCategory($categoryData);

            $response = [
                'status'        => 'success',
                'message'       => 'Resource category created successfully!',
                'data'          => $category,
                'redirect_url'  => site_url('auth/pillars/resources')
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Resource category creation error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create resource category: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function handleDeleteResourceCategory($id)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $this->pillarService->deleteResourceCategory($id);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Resource category deleted successfully!',
                'data'    => ['category_id' => $id]
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            log_message('error', 'Resource category deletion error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to delete resource category: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function handleCreateDocumentType()
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $data = $this->request->getPost();

            $document = $this->pillarService->createDocumentType($data);

            $response = [
                'status'  => 'success',
                'message' => 'Document type created successfully!',
                'data'    => $document,
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Document type creation error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create document type: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function editDocumentType($id)
    {
        try {
            $documentType = $this->documentTypeModel->where('id', $id)->first();
            
            if (!$documentType) {
                return redirect()->to(site_url('auth/pillars/document-types'))
                    ->with('error', 'Document type not found');
            }

            // Convert to array if object
            if (is_object($documentType)) {
                $documentType = (array) $documentType;
            }

            return view('backendV2/pages/pillars/edit-document-type', [
                'title' => 'Edit Document Type - KEWASNET',
                'documentType' => $documentType
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error loading document type for edit: ' . $e->getMessage());
            return redirect()->to(site_url('auth/pillars/document-types'))
                ->with('error', 'Failed to load document type for editing');
        }
    }

    public function handleEditDocumentType($id)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $documentTypeId = $this->request->getPost('document_type_id') ?? $id;
            
            // Validate UUID format
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $documentTypeId)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid document type ID format'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $formData = $this->request->getPost();
            $result = $this->pillarService->updateDocumentType($documentTypeId, $formData);

            return $this->response->setJSON($result)->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Document type update error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update document type: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function handleDeleteDocumentType($id)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $this->pillarService->deleteDocumentType($id);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Document type deleted successfully!',
                'data'    => ['category_id' => $id]
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            log_message('error', 'Document type deletion error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to delete Document type: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function handleCreatePillarArticle()
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $articleData = $this->request->getPost();
            $files = $this->request->getFiles();
            $contributors = $this->request->getPost('contributors') ?? [];

            // Check image is attached
            if (!empty($files['image_file']) && !$files['image_file']->isValid()) {
                throw new ValidationException(['image_file' => 'Invalid main image file upload.']);
            }

            // Check resource files (can be single file or array of files)
            if (!empty($files['resource_file'])) {
                $resourceFiles = $files['resource_file'];
                // Handle both single file and array of files
                if (is_array($resourceFiles)) {
                    foreach ($resourceFiles as $file) {
                        if (!$file->isValid()) {
                            throw new ValidationException(['resource_file' => 'One or more resource files are invalid.']);
                        }
                    }
                } else {
                    if (!$resourceFiles->isValid()) {
                        throw new ValidationException(['resource_file' => 'Invalid resource file file upload.']);
                    }
                }
            }
            
            $resource = $this->pillarService->createPillarArticle($articleData, $files, $contributors);

            $response = [
                'success'  => true,
                'status'   => 'success',
                'message'  => 'Pillar article created successfully!',
                'data'     => $resource,
                'redirect' => site_url('auth/pillars/articles')
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_CREATED);
                                 
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Pillar article creation error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success'    => false,
                'status'     => 'error',
                'message'    => 'Failed to create pillar article: ' . $e->getMessage(),
                'data'       => [],
                'error_type' => 'server_error'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteArticle($articleId)
    {
        try {            
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();
            
            // Delete the article
            $result = $this->pillarService->deletePillarArticle($articleId);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'success' => true,
                    'message' => 'Article deleted successfully',
                    'redirect' => site_url('auth/pillars/articles')
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Failed to delete article'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting article: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to delete article: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    /**
     * API endpoint for pillars DataTable
     */
    public function getPillars()
    {
        $columns = ['id', 'title', 'description', 'is_active', 'articles_count', 'created_at'];

        return $this->dataTableService->handle(
            $this->pillarModel,
            $columns,
            'getPillarsTable',
            'getTotalPillarsCount'
        );
    }

    /**
     * API endpoint for articles DataTable
     */
    public function getArticles()
    {
        $columns = ['id', 'title', 'pillar_name', 'document_type_name', 'category_name', 'is_published', 'download_count', 'created_at'];

        return $this->dataTableService->handle(
            $this->resourceModel,
            $columns,
            'getResourcesTable',
            'getTotalResourcesCount'
        );
    }

    /**
     * API endpoint for document types DataTable
     */
    public function getDocumentTypes()
    {
        $columns = ['id', 'name', 'slug', 'color', 'resources_count', 'created_at'];

        return $this->dataTableService->handle(
            $this->documentTypeModel,
            $columns,
            'getDocumentTypesTable',
            'getTotalDocumentTypesCount'
        );
    }

    /**
     * API endpoint for resource categories DataTable
     */
    public function getCategories()
    {
        $columns = ['id', 'name', 'description', 'resource_count', 'pillar_title', 'created_at'];

        return $this->dataTableService->handle(
            $this->resourceCategoryModel,
            $columns,
            'getCategoriesTable',
            'getTotalCategoriesCount'
        );
    }

    /**
     * Update pillar status (activate/deactivate)
     */
    public function updatePillarStatus($id)
    {
        try {
            $status = $this->request->getPost('status');
            
            $result = $this->pillarModel->update($id, ['is_active' => $status]);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pillar status updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update pillar status'
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating pillar status: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while updating status'
            ])->setStatusCode(500);
        }
    }

    /**
     * Update article status (publish/unpublish)
     */
    public function updateArticleStatus($id)
    {
        try {
            $isPublished = $this->request->getPost('is_published');
            
            $result = $this->resourceModel->update($id, ['is_published' => $isPublished]);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Article status updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update article status'
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating article status: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while updating status'
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete a pillar
     */
    public function deletePillar($id)
    {
        try {
            $result = $this->pillarModel->delete($id);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pillar deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete pillar'
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting pillar: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting pillar'
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete a document type
     */
    public function deleteDocumentType($id)
    {
        try {
            $result = $this->documentTypeModel->delete($id);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Document type deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete document type'
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting document type: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting document type'
            ])->setStatusCode(500);
        }
    }

    /**
     * Duplicate a document type
     */
    public function duplicateDocumentType($id)
    {
        try {
            if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

            $originalType = $this->documentTypeModel->find($id);
            
            if (!$originalType) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Document type not found'
                ])->setStatusCode(404);
            }

            // Convert to array if object
            if (is_object($originalType)) {
                $originalType = (array) $originalType;
            }

            $newTypeData = [
                'name' => $originalType['name'] . ' (Copy)',
                'slug' => $originalType['slug'] . '-copy-' . time(),
                'color' => $originalType['color'] ?? '#6B7280'
            ];

            $result = $this->documentTypeModel->insert($newTypeData);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Document type duplicated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to duplicate document type'
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error duplicating document type: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while duplicating document type'
            ])->setStatusCode(500);
        }
    }

    /**
     * Download an article
     */
    public function downloadArticle($id)
    {
        try {
            $resource = $this->resourceModel->find($id);

            if (!$resource || !$resource->file_url) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found');
            }

            // Increment download count
            $this->resourceModel->update($id, [
                'download_count' => ($resource->download_count ?? 0) + 1
            ]);

            // Get file path
            $filePath = WRITEPATH . 'uploads/' . $resource->file_url;
            
            if (!file_exists($filePath)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Physical file not found');
            }

            // Force download
            return $this->response->download($filePath, null);
            
        } catch (\Exception $e) {
            log_message('error', 'Error downloading article: ' . $e->getMessage());
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File download failed');
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
