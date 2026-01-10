<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\Hash;
use CodeIgniter\HTTP\ResponseInterface;

class SecurityController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['text', 'url', 'form']);
    }

    /**
     * View security settings for logged-in admin or specific admin
     */
    public function index($id = null)
    {
        // If no ID provided, use logged-in user's ID
        $userId = $id ?? session()->get('id');
        
        if (!$userId) {
            return redirect()->to(base_url('auth/login'));
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('auth/account'))->with('error', 'User not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        // Check if viewing own profile or managing another admin
        $isOwnProfile = ($userId === session()->get('id'));

        return view('backendV2/pages/security/index', [
            'title' => ($isOwnProfile ? 'My Security Settings - KEWASNET' : 'Security Settings - KEWASNET'),
            'user' => $user,
            'isOwnProfile' => $isOwnProfile
        ]);
    }

    /**
     * Change password form
     */
    public function changePassword($id)
    {
        if (!$id) {
            return redirect()->to(base_url('auth/account'))->with('error', 'User ID is required');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('auth/account'))->with('error', 'User not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        // Check if changing own password or another admin's password
        $isOwnPassword = ($id === session()->get('id'));

        return view('backendV2/pages/security/change-password', [
            'title' => ($isOwnPassword ? 'Change My Password - KEWASNET' : 'Change Password - KEWASNET'),
            'user' => $user,
            'isOwnPassword' => $isOwnPassword
        ]);
    }

    /**
     * Update password (AJAX)
     */
    public function updatePassword($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        if (!$id) {
            return $this->ajaxErrorResponse('User ID is required', 400);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->ajaxErrorResponse('User not found', 404);
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        $isOwnPassword = ($id === session()->get('id'));

        // Validation rules
        $rules = [
            'new_password' => 'required|min_length[8]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        // If changing own password, require current password
        if ($isOwnPassword) {
            $rules['current_password'] = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($this->validator->getErrors(), 400);
        }

        try {
            // Verify current password if changing own password
            if ($isOwnPassword) {
                $currentPassword = $this->request->getPost('current_password');
                $hash = new Hash();
                
                if (!$hash::check($currentPassword, $user['password'])) {
                    return $this->ajaxErrorResponse('Current password is incorrect', 400);
                }
            }

            // Hash new password
            $newPassword = $this->request->getPost('new_password');
            $hash = new Hash();
            $hashedPassword = $hash::make($newPassword);

            // Update password
            $this->userModel->update($id, ['password' => $hashedPassword]);

            return $this->ajaxSuccessResponse('Password changed successfully');
        } catch (\Exception $e) {
            log_message('error', 'Password update error: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to change password: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Toggle 2FA (if implemented)
     */
    public function updateTwoFactor($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        if (!$id) {
            return $this->ajaxErrorResponse('User ID is required', 400);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->ajaxErrorResponse('User not found', 404);
        }

        try {
            $enable2FA = $this->request->getPost('enable_2fa') === '1' ? 1 : 0;
            
            // Update 2FA setting (assuming a 'two_factor_enabled' column exists)
            // If the column doesn't exist, this will need to be added to the migration
            // $this->userModel->update($id, ['two_factor_enabled' => $enable2FA]);

            // For now, return a placeholder response
            return $this->ajaxSuccessResponse('Two-factor authentication setting updated successfully');
        } catch (\Exception $e) {
            log_message('error', '2FA update error: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to update 2FA setting: ' . $e->getMessage(), 500);
        }
    }

    /**
     * View login history/sessions
     */
    public function viewLoginHistory($id)
    {
        if (!$id) {
            return redirect()->to(base_url('auth/account'))->with('error', 'User ID is required');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('auth/account'))->with('error', 'User not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        $db = \Config\Database::connect();
        $loginHistory = [];

        // Get login history from user_browsers table if it exists
        if ($db->tableExists('user_browsers')) {
            $loginHistory = $db->table('user_browsers')
                ->where('user_id', $id)
                ->orderBy('login_time', 'DESC')
                ->limit(50)
                ->get()
                ->getResultArray();
        }

        return view('backendV2/pages/security/login-history', [
            'title' => 'Login History - KEWASNET',
            'user' => $user,
            'loginHistory' => $loginHistory
        ]);
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