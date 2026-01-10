<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\Hash;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['text', 'url', 'form', 'filesystem']);
    }

    /**
     * Display own profile (current logged-in admin)
     */
    public function index()
    {
        $userId = session()->get('id');
        
        if (!$userId) {
            return redirect()->to(base_url('auth/login'));
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('auth/login'))->with('error', 'User not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        return view('backendV2/pages/profile/index', [
            'title' => 'My Profile - KEWASNET',
            'user' => $user
        ]);
    }

    /**
     * Display edit profile form
     */
    public function edit()
    {
        $userId = session()->get('id');
        
        if (!$userId) {
            return redirect()->to(base_url('auth/login'));
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('auth/login'))->with('error', 'User not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        return view('backendV2/pages/profile/edit', [
            'title' => 'Edit Profile - KEWASNET',
            'user' => $user
        ]);
    }

    /**
     * Update own profile (AJAX)
     */
    public function update()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $userId = session()->get('id');
        
        if (!$userId) {
            return $this->ajaxErrorResponse('User not authenticated', 401);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->ajaxErrorResponse('User not found', 404);
        }

        // Validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[255]',
            'last_name'  => 'required|min_length[2]|max_length[255]',
            'email'      => "required|valid_email|is_unique[system_users.email,id,{$userId}]",
            'phone'      => 'permit_empty|max_length[255]',
            'bio'        => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($this->validator->getErrors(), 400);
        }

        try {
            $updateData = [
                'first_name' => htmlspecialchars($this->request->getPost('first_name'), ENT_QUOTES, 'UTF-8'),
                'last_name'  => htmlspecialchars($this->request->getPost('last_name'), ENT_QUOTES, 'UTF-8'),
                'email'      => htmlspecialchars($this->request->getPost('email'), ENT_QUOTES, 'UTF-8'),
                'phone'      => htmlspecialchars($this->request->getPost('phone') ?? '', ENT_QUOTES, 'UTF-8'),
                'bio'        => htmlspecialchars($this->request->getPost('bio') ?? '', ENT_QUOTES, 'UTF-8'),
            ];

            // Handle profile picture upload
            $file = $this->request->getFile('picture');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/profiles/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newFileName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();
                
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

                $newCoverFileName = 'cover_' . $userId . '_' . time() . '.' . $coverFile->getExtension();
                
                if ($coverFile->move($uploadPath, $newCoverFileName)) {
                    // Delete old cover if exists
                    if (!empty($user['profile_cover_image']) && file_exists(FCPATH . $user['profile_cover_image'])) {
                        @unlink(FCPATH . $user['profile_cover_image']);
                    }
                    $updateData['profile_cover_image'] = 'uploads/covers/' . $newCoverFileName;
                }
            }

            // Update user
            $this->userModel->update($userId, $updateData);

            // Update session with new name
            session()->set([
                'first_name' => $updateData['first_name'],
                'last_name' => $updateData['last_name'],
                'email' => $updateData['email']
            ]);

            return $this->ajaxSuccessResponse('Profile updated successfully');
        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to update profile: ' . $e->getMessage(), 500);
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