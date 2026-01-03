<?php

namespace App\Controllers\BackendV2;

use Carbon\Carbon;
use App\Libraries\GenerateIDs;
use App\Services\DataTableService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UsersController extends BaseController
{

    protected $dataTableService;

    public function __construct()
    {
        $this->dataTableService = new DataTableService();
    }
    
    public function index()
    {
        $title = "Manage System Users - Admin Portal";
        return view('backendV2/pages/users/index', ['title' => $title]);
    }

    public function getUsersData()
    {
        $model   = model('App\Models\UserModel');
        $columns = ['id', 'name', 'email', 'phone', 'role', 'last_login', 'joined'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getUsers',
            'countAllUsers',
        );
    }

    public function create()
    {
        $roleModel = model('RoleModel');
        $roles = $roleModel->findAll();

        $title = "Create New User - Admin Portal";
        return view('backendV2/pages/users/create', [
            'title' => $title,
            'roles' => $roles
        ]);
    }

    public function createUser()
    {
        if (!$this->isValidAjaxRequest()) return $this->ajaxErrorResponse('Method not allowed', 405);

        $request = service('request');
        $userService = new \App\Services\UserService();
        $userModel = model('App\Models\UserModel');

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'email'      => 'required|valid_email|is_unique[system_users.email]',
            'role_id'    => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($this->validator->getErrors(), 400);
        }

        // Get and sanitize input
        $postData = [
            'first_name' => htmlspecialchars($request->getPost('first_name'), ENT_QUOTES, 'UTF-8'),
            'last_name'  => htmlspecialchars($request->getPost('last_name'), ENT_QUOTES, 'UTF-8'),
            'email'      => htmlspecialchars($request->getPost('email'), ENT_QUOTES, 'UTF-8'),
            'phone'      => htmlspecialchars($request->getPost('phone'), ENT_QUOTES, 'UTF-8'),
            'role_id'    => htmlspecialchars($request->getPost('role_id'), ENT_QUOTES, 'UTF-8'),
            'bio'        => htmlspecialchars($request->getPost('bio'), ENT_QUOTES, 'UTF-8')
        ];

        // Create user using service
        $result = $userService->createSystemUser($postData);

        if (!$result['success']) {
            return $this->ajaxErrorResponse($result['message'], $result['statusCode'] ?? 500);
        }

        // Handle profile image upload if provided
        $file = $this->request->getFile('profile_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            try {
                // Create uploads directory if it doesn't exist
                $uploadPath = FCPATH . 'uploads/profiles/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $userId = $result['data']['userId'];
                $newFileName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();

                // Move file to uploads directory
                $file->move($uploadPath, $newFileName);

                // Update user record with profile image
                $userModel->update($userId, [
                    'picture' => $newFileName,
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Error uploading profile image during user creation: ' . $e->getMessage());
                // Don't fail the user creation if image upload fails
            }
        }

        return $this->ajaxSuccessResponse($result['message']);
    }

    public function view($userId)
    {
        $userModel = model('App\Models\UserModel');
        $db = \Config\Database::connect();

        // Get user details
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('auth/users'))->with('error', 'User not found');
        }

        // Get user statistics
        $stats = [
            'enrollments' => 0,
            'completions' => 0,
            'certificates' => 0,
            'discussions' => 0
        ];

        // Get enrollment count
        if ($db->tableExists('course_enrollments')) {
            $stats['enrollments'] = $db->table('course_enrollments')
                ->where('user_id', $userId)
                ->where('deleted_at', null)
                ->countAllResults();
        }

        // Get completion count
        if ($db->tableExists('course_completions')) {
            $stats['completions'] = $db->table('course_completions')
                ->where('user_id', $userId)
                ->where('deleted_at', null)
                ->countAllResults();
        }

        // Get certificate count
        if ($db->tableExists('certificates')) {
            $stats['certificates'] = $db->table('certificates')
                ->where('user_id', $userId)
                ->where('deleted_at', null)
                ->countAllResults();
        }

        // Get discussion count
        if ($db->tableExists('discussions')) {
            $stats['discussions'] = $db->table('discussions')
                ->where('user_id', $userId)
                ->where('deleted_at', null)
                ->countAllResults();
        }

        // Get recent enrollments
        $recentEnrollments = [];
        if ($db->tableExists('course_enrollments') && $db->tableExists('courses')) {
            $recentEnrollments = $db->table('course_enrollments')
                ->select('course_enrollments.*, courses.title as course_title, course_enrollments.created_at as enrolled_at')
                ->join('courses', 'courses.id = course_enrollments.course_id')
                ->where('course_enrollments.user_id', $userId)
                ->where('course_enrollments.deleted_at', null)
                ->orderBy('course_enrollments.created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        // Get last login from user_browsers table
        $lastLogin = null;
        if ($db->tableExists('user_browsers')) {
            $lastLoginResult = $db->table('user_browsers')
                ->select('login_time')
                ->where('user_id', $userId)
                ->orderBy('login_time', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
            
            if ($lastLoginResult) {
                $lastLogin = $lastLoginResult['login_time'];
            }
        }
        
        // Add last login to user array
        $user['last_login'] = $lastLogin;

        $title = "View User - Admin Portal";
        return view('backendV2/pages/users/view', [
            'title' => $title,
            'user' => $user,
            'stats' => $stats,
            'recentEnrollments' => $recentEnrollments
        ]);
    }

    public function getLoginActivities($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);
        }

        $db = \Config\Database::connect();
        
        if (!$db->tableExists('user_browsers')) {
            return $this->response->setJSON([
                'draw' => intval($this->request->getPost('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        $builder = $db->table('user_browsers');
        $builder->where('user_id', $userId);

        // Get total records count
        $totalRecords = $builder->countAllResults(false);

        // Handle search
        $search = $this->request->getPost('search')['value'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->like('browser', $search)
                ->orLike('platform', $search)
                ->orLike('ip_address', $search)
                ->orLike('login_type', $search)
                ->groupEnd();
        }

        // Get filtered records count
        $filteredRecords = $builder->countAllResults(false);

        // Handle ordering
        $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 4;
        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';
        $columns = ['browser', 'platform', 'ip_address', 'login_type', 'login_time'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'login_time';
        
        $builder->orderBy($orderColumn, $orderDir);

        // Handle pagination
        $start = intval($this->request->getPost('start') ?? 0);
        $length = intval($this->request->getPost('length') ?? 10);
        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function edit($userId)
    {
        $userModel = model('App\Models\UserModel');
        $roleModel = model('RoleModel');

        // Get user details
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('auth/users'))->with('error', 'User not found');
        }

        // Get all roles
        $roles = $roleModel->findAll();

        $title = "Edit User - Admin Portal";
        return view('backendV2/pages/users/edit', [
            'title' => $title,
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function updateUser($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $userModel = model('App\Models\UserModel');
        $request = service('request');

        // Validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'role_id'    => 'required|numeric',
            'account_status' => 'required|in_list[active,suspended]'
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($this->validator->getErrors(), 400);
        }

        // Prepare update data
        $updateData = [
            'first_name' => htmlspecialchars($request->getPost('first_name'), ENT_QUOTES, 'UTF-8'),
            'last_name'  => htmlspecialchars($request->getPost('last_name'), ENT_QUOTES, 'UTF-8'),
            'phone'      => htmlspecialchars($request->getPost('phone'), ENT_QUOTES, 'UTF-8'),
            'role_id'    => htmlspecialchars($request->getPost('role_id'), ENT_QUOTES, 'UTF-8'),
            'bio'        => htmlspecialchars($request->getPost('bio'), ENT_QUOTES, 'UTF-8'),
            'account_status' => htmlspecialchars($request->getPost('account_status'), ENT_QUOTES, 'UTF-8'),
            'status'     => $request->getPost('account_status') === 'active' ? 'active' : 'inactive',
            'updated_at' => Carbon::now()->toDateTimeString()
        ];

        // Update user
        if ($userModel->update($userId, $updateData)) {
            return $this->ajaxSuccessResponse('User updated successfully');
        }

        return $this->ajaxErrorResponse('Failed to update user', 500);
    }

    public function changePassword($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $userModel = model('App\Models\UserModel');
        $request = service('request');

        // Validation rules
        $rules = [
            'new_password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($this->validator->getErrors(), 400);
        }

        // Get user
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->ajaxErrorResponse('User not found', 404);
        }

        $newPassword = $request->getPost('new_password');

        // Update password
        if ($userModel->updatePassword($user['email'], $newPassword)) {
            // Send email with new password
            $emailSent = $this->sendPasswordChangeEmail($user['email'], $user['first_name'], $newPassword);

            if ($emailSent) {
                return $this->ajaxSuccessResponse('Password changed successfully and email sent to user');
            } else {
                return $this->ajaxSuccessResponse('Password changed successfully but failed to send email notification');
            }
        }

        return $this->ajaxErrorResponse('Failed to change password', 500);
    }

    public function uploadProfileImage($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $userModel = model('App\Models\UserModel');

        // Check if user exists
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->ajaxErrorResponse('User not found', 404);
        }

        // Validate file upload
        $validationRules = [
            'profile_image' => [
                'rules' => 'uploaded[profile_image]|is_image[profile_image]|max_size[profile_image,5120]',
                'errors' => [
                    'uploaded' => 'Please select an image to upload',
                    'is_image' => 'The file must be a valid image',
                    'max_size' => 'Image size must not exceed 5MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return $this->ajaxErrorResponse($this->validator->getErrors(), 400);
        }

        $file = $this->request->getFile('profile_image');

        if (!$file->isValid()) {
            return $this->ajaxErrorResponse('Invalid file upload', 400);
        }

        try {
            // Create uploads directory if it doesn't exist
            $uploadPath = FCPATH . 'uploads/profiles/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete old profile image if exists
            if (!empty($user['picture']) && file_exists($uploadPath . $user['picture'])) {
                unlink($uploadPath . $user['picture']);
            }

            // Generate unique filename
            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();

            // Move file to uploads directory
            $file->move($uploadPath, $newFileName);

            // Update user record
            $userModel->update($userId, [
                'picture' => $newFileName,
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);

            return $this->ajaxSuccessResponse('Profile picture uploaded successfully', [
                'fileName' => $newFileName,
                'fileUrl' => base_url('uploads/profiles/' . $newFileName)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error uploading profile image: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to upload profile picture', 500);
        }
    }

    public function removeProfileImage($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $userModel = model('App\Models\UserModel');

        // Check if user exists
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->ajaxErrorResponse('User not found', 404);
        }

        if (empty($user['picture'])) {
            return $this->ajaxErrorResponse('No profile picture to remove', 400);
        }

        try {
            // Delete file from filesystem
            $filePath = FCPATH . 'uploads/profiles/' . $user['picture'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update user record
            $userModel->update($userId, [
                'picture' => null,
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);

            return $this->ajaxSuccessResponse('Profile picture removed successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error removing profile image: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to remove profile picture', 500);
        }
    }

    public function deleteUser($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $userService = new \App\Services\UserService();

        // Delete user using service
        $result = $userService->deleteSystemUser($userId);

        if (!$result['success']) {
            return $this->ajaxErrorResponse($result['message'], $result['statusCode'] ?? 500);
        }

        return $this->ajaxSuccessResponse($result['message'], $result['data'] ?? []);
    }

    public function resetPassword($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        try {
            $userModel = model('App\Models\UserModel');
            $user = $userModel->find($userId);

            if (!$user) {
                return $this->ajaxErrorResponse('User not found', 404);
            }

            // Generate new random password
            $newPassword = bin2hex(random_bytes(8)); // 16 character password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update user password
            $userModel->update($userId, [
                'password' => $hashedPassword,
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            // Prepare email content
            $message = view('backend/emails/password_reset_notification', [
                'firstName' => $user['first_name'],
                'newPassword' => $newPassword,
                'loginUrl' => base_url('auth/login')
            ]);

            // Queue email
            $emailQueueModel = new \App\Models\EmailQueue();
            $emailQueued = $emailQueueModel->queueEmail(
                $user['email'],
                'Your Password Has Been Reset - KEWASNET',
                $message,
                null,
                getenv('EMAIL_FROM_ADDRESS'),
                'KEWASNET'
            );

            if (!$emailQueued) {
                log_message('error', 'Failed to queue password reset email for user: ' . $userId);
            }

            log_message('info', 'Password reset for user: ' . $userId . ' by admin: ' . session('user_id'));

            return $this->ajaxSuccessResponse('Password has been reset successfully. The new password has been sent to the user\'s email.');
        } catch (\Exception $e) {
            log_message('error', 'Error resetting password: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to reset password. Please try again.', 500);
        }
    }

    public function sendVerificationEmail($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        try {
            $userModel = model('App\Models\UserModel');
            $user = $userModel->find($userId);

            if (!$user) {
                return $this->ajaxErrorResponse('User not found', 404);
            }

            // Check if already verified
            if (!empty($user['email_verified_at'])) {
                return $this->ajaxErrorResponse('This email address is already verified.', 400);
            }

            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            $verificationUrl = base_url('auth/verify-email/' . $verificationToken);

            // Store token in database (you may need to add this field or use a separate table)
            $userModel->update($userId, [
                'verification_token' => $verificationToken,
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            // Prepare email content
            $message = view('backend/emails/email_verification', [
                'firstName' => $user['first_name'],
                'verificationUrl' => $verificationUrl,
                'token' => $verificationToken
            ]);

            // Queue email
            $emailQueueModel = new \App\Models\EmailQueue();
            $emailQueued = $emailQueueModel->queueEmail(
                $user['email'],
                'Verify Your Email Address - KEWASNET',
                $message,
                null,
                getenv('EMAIL_FROM_ADDRESS'),
                'KEWASNET'
            );

            if (!$emailQueued) {
                log_message('error', 'Failed to queue verification email for user: ' . $userId);
                return $this->ajaxErrorResponse('Failed to send verification email. Please try again.', 500);
            }

            log_message('info', 'Verification email sent to user: ' . $userId . ' by admin: ' . session('user_id'));

            return $this->ajaxSuccessResponse('Verification email has been sent successfully.');
        } catch (\Exception $e) {
            log_message('error', 'Error sending verification email: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to send verification email. Please try again.', 500);
        }
    }

    public function toggleAccountStatus($userId)
    {
        if (!$this->request->isAJAX()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        try {
            $userModel = model('App\Models\UserModel');
            $user = $userModel->find($userId);

            if (!$user) {
                return $this->ajaxErrorResponse('User not found', 404);
            }

            $action = $this->request->getPost('action'); // 'suspend' or 'activate'
            
            if (!in_array($action, ['suspend', 'activate'])) {
                return $this->ajaxErrorResponse('Invalid action specified', 400);
            }

            $newStatus = $action === 'suspend' ? 'inactive' : 'active';
            $actionText = $action === 'suspend' ? 'suspended' : 'activated';

            // Update user status
            $userModel->update($userId, [
                'status' => $newStatus,
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);

            // Prepare email notification
            $message = view('backend/emails/account_status_notification', [
                'firstName' => $user['first_name'],
                'action' => $actionText,
                'status' => $newStatus,
                'loginUrl' => base_url('auth/login')
            ]);

            // Queue email notification
            $emailQueueModel = new \App\Models\EmailQueue();
            $emailQueued = $emailQueueModel->queueEmail(
                $user['email'],
                'Account Status Update - KEWASNET',
                $message,
                null,
                getenv('EMAIL_FROM_ADDRESS'),
                'KEWASNET'
            );

            if (!$emailQueued) {
                log_message('error', 'Failed to queue account status notification email for user: ' . $userId);
            }

            log_message('info', 'Account ' . $actionText . ' for user: ' . $userId . ' by admin: ' . session('user_id'));

            return $this->ajaxSuccessResponse('Account has been ' . $actionText . ' successfully.');
        } catch (\Exception $e) {
            log_message('error', 'Error toggling account status: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to update account status. Please try again.', 500);
        }
    }

    private function sendPasswordChangeEmail($email, $firstName, $newPassword)
    {
        try {
            $message = view('backend/emails/password_change_template', [
                'firstName' => $firstName,
                'newPassword' => $newPassword,
                'loginUrl' => base_url('auth/login')
            ]);

            $emailQueueModel = new \App\Models\EmailQueue();
            return $emailQueueModel->queueEmail(
                $email,
                'Your Password Has Been Changed',
                $message,
                null,
                'noreply@kewasnet.org',
                'KEWASNET'
            );
        } catch (\Exception $e) {
            log_message('error', 'Failed to queue password change email: ' . $e->getMessage());
            return false;
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
