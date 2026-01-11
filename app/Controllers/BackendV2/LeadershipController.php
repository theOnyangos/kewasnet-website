<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Models\LeadershipModel;
use App\Services\DataTableService;
use App\Exceptions\ValidationException;
use CodeIgniter\HTTP\ResponseInterface;

class LeadershipController extends BaseController
{
    protected const PAGE_TITLE = 'Manage Leadership Team - KEWASNET';
    protected const PAGE_DESCRIPTION = 'Manage leadership team members displayed on the about page.';

    protected $leadershipModel;
    protected $dataTableService;

    public function __construct()
    {
        $this->leadershipModel = new LeadershipModel();
        $this->dataTableService = new DataTableService();
        helper(['text', 'url', 'form', 'filesystem']);
    }

    /**
     * List all leadership team members (using DataTable)
     */
    public function index()
    {
        return view('backendV2/pages/leadership/index', [
            'title' => self::PAGE_TITLE,
            'description' => self::PAGE_DESCRIPTION
        ]);
    }

    /**
     * Get leadership data for DataTables
     */
    public function getLeadershipData()
    {
        $model = $this->leadershipModel;
        $columns = ['id', 'image', 'name', 'position', 'experience', 'description', 'social_media', 'status', 'order_index', 'created_at'];

        return $this->dataTableService->handle(
            $model,
            $columns,
            'getLeadership',
            'countAllLeadership',
        );
    }

    /**
     * Create leadership member form
     */
    public function create()
    {
        // Get max order_index to suggest next position
        $maxOrder = $this->leadershipModel->getMaxOrder();
        $nextOrder = $maxOrder + 1;

        return view('backendV2/pages/leadership/create', [
            'title' => 'Add Leadership Team Member - KEWASNET',
            'description' => 'Add a new member to the leadership team',
            'nextOrder' => $nextOrder
        ]);
    }

    /**
     * Create leadership member (AJAX with image upload)
     */
    public function store()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        try {
            $request = service('request');
            $postData = $request->getPost();
            $file = $request->getFile('image');

            // Validation
            $rules = [
                'name'       => 'required|min_length[2]|max_length[255]',
                'position'   => 'required|min_length[2]|max_length[255]',
                'description' => 'permit_empty|max_length[1000]',
                'experience' => 'permit_empty|max_length[100]',
                'order_index' => 'required|integer',
                'status'     => 'required|in_list[active,inactive]',
                'image'      => 'uploaded[image]|max_size[image,2048]|ext_in[image,png,jpg,jpeg,gif,webp]',
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Handle image upload
            $imagePath = null;
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/leadership/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newFileName = 'leadership_' . uniqid() . '_' . time() . '.' . $file->getExtension();
                
                if ($file->move($uploadPath, $newFileName)) {
                    $imagePath = 'uploads/leadership/' . $newFileName;
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'error',
                        'message' => 'Failed to upload image',
                    ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            // Process social media links
            $socialMediaLinks = [];
            if (isset($postData['social_media_platform']) && isset($postData['social_media_url'])) {
                $platforms = is_array($postData['social_media_platform']) ? $postData['social_media_platform'] : [];
                $urls = is_array($postData['social_media_url']) ? $postData['social_media_url'] : [];
                
                for ($i = 0; $i < count($platforms); $i++) {
                    $platform = trim($platforms[$i] ?? '');
                    $url = trim($urls[$i] ?? '');
                    
                    if (!empty($platform) && !empty($url)) {
                        $socialMediaLinks[] = [
                            'platform' => htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'),
                            'url' => htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
                        ];
                    }
                }
            }
            
            // Extract LinkedIn URL if present (for backward compatibility)
            $linkedinUrl = '';
            foreach ($socialMediaLinks as $link) {
                if (strtolower($link['platform']) === 'linkedin') {
                    $linkedinUrl = $link['url'];
                    break;
                }
            }

            // Prepare data for insertion
            $insertData = [
                'name'        => htmlspecialchars($postData['name'], ENT_QUOTES, 'UTF-8'),
                'position'    => htmlspecialchars($postData['position'], ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($postData['description'] ?? '', ENT_QUOTES, 'UTF-8'),
                'linkedin'    => $linkedinUrl, // Store LinkedIn URL for backward compatibility
                'experience'  => htmlspecialchars($postData['experience'] ?? '', ENT_QUOTES, 'UTF-8'),
                'order_index' => (int) ($postData['order_index'] ?? 0),
                'status'      => $postData['status'],
            ];
            
            // Store social media links as JSON if we have any
            if (!empty($socialMediaLinks)) {
                $insertData['social_media'] = json_encode($socialMediaLinks);
            }

            if ($imagePath) {
                $insertData['image'] = $imagePath;
            }

            // Insert into database
            $result = $this->leadershipModel->insert($insertData);

            if ($result === false) {
                // Delete uploaded image if insert failed
                if ($imagePath && file_exists(FCPATH . $imagePath)) {
                    @unlink(FCPATH . $imagePath);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Failed to create leadership member: ' . implode(', ', $this->leadershipModel->errors()),
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'message' => 'Leadership team member created successfully!',
                'data' => ['id' => $result]
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Leadership creation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'error',
                'message' => 'Failed to create leadership member: ' . $e->getMessage(),
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Edit leadership member form
     */
    public function edit($id)
    {
        if (!$id) {
            return redirect()->to(base_url('auth/leadership'))->with('error', 'Leadership member ID is required');
        }

        $member = $this->leadershipModel->find($id);

        if (!$member) {
            return redirect()->to(base_url('auth/leadership'))->with('error', 'Leadership member not found');
        }

        // Convert to array if object
        if (is_object($member)) {
            $member = (array) $member;
        }

        return view('backendV2/pages/leadership/edit', [
            'title' => 'Edit Leadership Team Member - KEWASNET',
            'description' => 'Update leadership team member information',
            'member' => $member
        ]);
    }

    /**
     * Update leadership member (AJAX)
     */
    public function update($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'status' => 'error',
                'message' => 'Leadership member ID is required',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $member = $this->leadershipModel->find($id);
            if (!$member) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Leadership member not found',
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $request = service('request');
            $postData = $request->getPost();
            $file = $request->getFile('image');

            // Validation
            $rules = [
                'name'       => 'required|min_length[2]|max_length[255]',
                'position'   => 'required|min_length[2]|max_length[255]',
                'description' => 'permit_empty|max_length[1000]',
                'linkedin'   => 'permit_empty|valid_url|max_length[500]',
                'experience' => 'permit_empty|max_length[100]',
                'order_index' => 'required|integer',
                'status'     => 'required|in_list[active,inactive]',
            ];

            // Only validate image if new file is uploaded
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $rules['image'] = 'uploaded[image]|max_size[image,2048]|ext_in[image,png,jpg,jpeg,gif,webp]';
            }

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Handle image upload if new file provided
            $oldImagePath = $member['image'] ?? null;
            $imagePath = $oldImagePath;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/leadership/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newFileName = 'leadership_' . $id . '_' . time() . '.' . $file->getExtension();
                
                if ($file->move($uploadPath, $newFileName)) {
                    // Delete old image if exists
                    if ($oldImagePath && file_exists(FCPATH . $oldImagePath)) {
                        @unlink(FCPATH . $oldImagePath);
                    }
                    $imagePath = 'uploads/leadership/' . $newFileName;
                }
            }

            // Handle image removal
            if (isset($postData['remove_image']) && $postData['remove_image'] == '1') {
                if ($oldImagePath && file_exists(FCPATH . $oldImagePath)) {
                    @unlink(FCPATH . $oldImagePath);
                }
                $imagePath = null;
            }

            // Process social media links
            $socialMediaLinks = [];
            if (isset($postData['social_media_platform']) && isset($postData['social_media_url'])) {
                $platforms = is_array($postData['social_media_platform']) ? $postData['social_media_platform'] : [];
                $urls = is_array($postData['social_media_url']) ? $postData['social_media_url'] : [];
                
                for ($i = 0; $i < count($platforms); $i++) {
                    $platform = trim($platforms[$i] ?? '');
                    $url = trim($urls[$i] ?? '');
                    
                    if (!empty($platform) && !empty($url)) {
                        $socialMediaLinks[] = [
                            'platform' => htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'),
                            'url' => htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
                        ];
                    }
                }
            }
            
            // Extract LinkedIn URL if present (for backward compatibility)
            $linkedinUrl = '';
            foreach ($socialMediaLinks as $link) {
                if (strtolower($link['platform']) === 'linkedin') {
                    $linkedinUrl = $link['url'];
                    break;
                }
            }

            // Prepare data for update
            $updateData = [
                'name'        => htmlspecialchars($postData['name'], ENT_QUOTES, 'UTF-8'),
                'position'    => htmlspecialchars($postData['position'], ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($postData['description'] ?? '', ENT_QUOTES, 'UTF-8'),
                'linkedin'    => $linkedinUrl, // Store LinkedIn URL for backward compatibility
                'experience'  => htmlspecialchars($postData['experience'] ?? '', ENT_QUOTES, 'UTF-8'),
                'order_index' => (int) ($postData['order_index'] ?? 0),
                'status'      => $postData['status'],
            ];
            
            // Store social media links as JSON if we have any
            if (!empty($socialMediaLinks)) {
                $updateData['social_media'] = json_encode($socialMediaLinks);
            } else {
                $updateData['social_media'] = null;
            }

            if ($imagePath !== $oldImagePath) {
                $updateData['image'] = $imagePath;
            }

            // Update database
            $result = $this->leadershipModel->update($id, $updateData);

            if ($result === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Failed to update leadership member: ' . implode(', ', $this->leadershipModel->errors()),
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'message' => 'Leadership team member updated successfully!',
                'data' => ['id' => $id]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Leadership update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'error',
                'message' => 'Failed to update leadership member: ' . $e->getMessage(),
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete leadership member (soft delete)
     */
    public function delete($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'status' => 'error',
                'message' => 'Leadership member ID is required',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $member = $this->leadershipModel->find($id);
            if (!$member) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Leadership member not found',
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Soft delete
            $result = $this->leadershipModel->delete($id);

            if ($result === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Failed to delete leadership member',
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Optionally delete image file
            if (!empty($member['image']) && file_exists(FCPATH . $member['image'])) {
                @unlink(FCPATH . $member['image']);
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'message' => 'Leadership team member deleted successfully!',
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            log_message('error', 'Leadership deletion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'error',
                'message' => 'Failed to delete leadership member: ' . $e->getMessage(),
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reorder leadership members (update order_index)
     */
    public function reorder()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        try {
            $request = service('request');
            $orderData = $request->getPost('order'); // Array of [id => order_index]

            if (!is_array($orderData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Invalid order data',
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            foreach ($orderData as $id => $orderIndex) {
                $this->leadershipModel->updateOrder($id, (int) $orderIndex);
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'message' => 'Leadership team order updated successfully!',
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            log_message('error', 'Leadership reorder error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'error',
                'message' => 'Failed to update order: ' . $e->getMessage(),
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }

    protected function respondToNonAjax($statusCode = 301, $message = 'Unauthorized request. Please try again'): ResponseInterface
    {
        $response = [
            'status' => 'error',
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => null,
        ];
        return $this->response->setJSON($response);
    }

    protected function failValidationErrors(array $errors): ResponseInterface
    {
        return $this->response->setJSON([
            'success' => false,
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors,
            'error_type' => 'validation'
        ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }
}