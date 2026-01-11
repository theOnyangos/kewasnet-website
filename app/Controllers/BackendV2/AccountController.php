<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\DataTableService;
use CodeIgniter\HTTP\ResponseInterface;

class AccountController extends BaseController
{
    protected $userModel;
    protected $dataTableService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->dataTableService = new DataTableService();
        helper(['text', 'url', 'form', 'filesystem']);
    }

    /**
     * List all admin accounts (using DataTable)
     */
    public function index()
    {
        return view('backendV2/pages/account/index', [
            'title' => 'Manage Admin Accounts - KEWASNET',
            'dashboardTitle' => 'Admin Accounts Management'
        ]);
    }

    /**
     * Get admin accounts data for DataTables
     */
    public function getAccountsData()
    {
        $model = $this->userModel;
        $columns = ['id', 'name', 'email', 'phone', 'role', 'status', 'account_status', 'created_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getAdminUsers',
            'countAdminUsers',
        );
    }

    /**
     * View specific admin account details
     */
    public function view($id)
    {
        if (!$id) {
            return redirect()->to(base_url('auth/account'))->with('error', 'Account ID is required');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('auth/account'))->with('error', 'Account not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        // Get role information
        $roleModel = model('RoleModel');
        $role = $roleModel->find($user['role_id'] ?? null);
        if ($role) {
            $user['role_name'] = $role['role_name'] ?? 'N/A';
        }

        return view('backendV2/pages/account/view', [
            'title' => 'View Admin Account - KEWASNET',
            'user' => $user
        ]);
    }

    /**
     * Edit admin account form
     */
    public function edit($id)
    {
        if (!$id) {
            return redirect()->to(base_url('auth/account'))->with('error', 'Account ID is required');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('auth/account'))->with('error', 'Account not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        // Get all roles
        $roleModel = model('RoleModel');
        $roles = $roleModel->findAll();

        return view('backendV2/pages/account/edit', [
            'title' => 'Edit Admin Account - KEWASNET',
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update admin account (AJAX)
     */
    public function update($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        if (!$id) {
            return $this->ajaxErrorResponse('Account ID is required', 400);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->ajaxErrorResponse('Account not found', 404);
        }

        // Validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[255]',
            'last_name'  => 'required|min_length[2]|max_length[255]',
            'email'      => "required|valid_email|is_unique[system_users.email,id,{$id}]",
            'phone'      => 'permit_empty|max_length[255]',
            'bio'        => 'permit_empty|max_length[1000]',
            'role_id'    => 'required|numeric',
            'account_status' => 'required|in_list[active,suspended,blocked]',
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($this->validator->getErrors(), 400);
        }

        try {
            $accountStatus = $this->request->getPost('account_status');
            $updateData = [
                'first_name'     => htmlspecialchars($this->request->getPost('first_name'), ENT_QUOTES, 'UTF-8'),
                'last_name'      => htmlspecialchars($this->request->getPost('last_name'), ENT_QUOTES, 'UTF-8'),
                'email'          => htmlspecialchars($this->request->getPost('email'), ENT_QUOTES, 'UTF-8'),
                'phone'          => htmlspecialchars($this->request->getPost('phone') ?? '', ENT_QUOTES, 'UTF-8'),
                'bio'            => htmlspecialchars($this->request->getPost('bio') ?? '', ENT_QUOTES, 'UTF-8'),
                'role_id'        => $this->request->getPost('role_id'),
                'account_status' => htmlspecialchars($accountStatus, ENT_QUOTES, 'UTF-8'),
                'status'         => $accountStatus === 'active' ? 'active' : 'inactive',
            ];

            // Handle profile picture upload
            $file = $this->request->getFile('picture');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/profiles/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newFileName = 'profile_' . $id . '_' . time() . '.' . $file->getExtension();
                
                if ($file->move($uploadPath, $newFileName)) {
                    // Delete old picture if exists
                    if (!empty($user['picture']) && file_exists(FCPATH . $user['picture'])) {
                        @unlink(FCPATH . $user['picture']);
                    }
                    $updateData['picture'] = 'uploads/profiles/' . $newFileName;
                }
            }

            // Handle profile cover image upload
            $coverFile = $this->request->getFile('profile_cover_image');
            if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/covers/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newCoverFileName = 'cover_' . $id . '_' . time() . '.' . $coverFile->getExtension();
                
                if ($coverFile->move($uploadPath, $newCoverFileName)) {
                    // Delete old cover if exists
                    if (!empty($user['profile_cover_image']) && file_exists(FCPATH . $user['profile_cover_image'])) {
                        @unlink(FCPATH . $user['profile_cover_image']);
                    }
                    $updateData['profile_cover_image'] = 'uploads/covers/' . $newCoverFileName;
                }
            }

            // Update user
            $this->userModel->update($id, $updateData);

            return $this->ajaxSuccessResponse('Account updated successfully');
        } catch (\Exception $e) {
            log_message('error', 'Account update error: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to update account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete admin account (soft delete)
     */
    public function delete($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        if (!$id) {
            return $this->ajaxErrorResponse('Account ID is required', 400);
        }

        // Prevent deleting own account
        $currentUserId = session()->get('id');
        if ($id === $currentUserId) {
            return $this->ajaxErrorResponse('You cannot delete your own account', 400);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->ajaxErrorResponse('Account not found', 404);
        }

        try {
            // Soft delete
            $this->userModel->delete($id);
            return $this->ajaxSuccessResponse('Account deleted successfully');
        } catch (\Exception $e) {
            log_message('error', 'Account deletion error: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to delete account: ' . $e->getMessage(), 500);
        }
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }

    protected function ajaxErrorResponse($message, int $statusCode = 400, array $additionalData = [])
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'token' => csrf_hash()
        ];

        if (is_array($message)) {
            $response['errors'] = $message;
            unset($response['message']);
        }

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON(array_merge($response, $additionalData));
    }

    protected function ajaxSuccessResponse(string $message, array $additionalData = [])
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'token' => csrf_hash(),
            ...$additionalData
        ]);
    }
}