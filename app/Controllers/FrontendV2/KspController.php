<?php

namespace App\Controllers\FrontendV2;

use Carbon\Carbon;
use App\Models\UserModel;
use App\Filters\AuthFilter;
use App\Services\HomeService;
use App\Libraries\ClientAuth;
use App\Libraries\GenerateIDs;
use App\Models\GoogleSettings;
use App\Models\PasswordResetToken;
use App\Controllers\BaseController;
use App\Services\ResetPasswordService;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\AuthService as AuthenticationService;

class KspController extends BaseController
{
    protected $db;
    protected $rolesTable;
    protected $homeService;
    protected $passwordResetToken;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->rolesTable = $this->db->table('roles');
        $this->passwordResetToken = new PasswordResetToken();
        $this->homeService = new HomeService();
    }

    public function index()
    {
        $stats      = $this->homeService->getKnowledgeHubStats();

        $data = [
            'stats' => $stats,  
            'title' => "KEWASNET - Welcome to Knowledge Sharing Hub",
            'description' => "KEWASNET is a platform for knowledge sharing and collaboration among water service providers in Kenya."
        ];

        return view('frontendV2/ksp/pages/landing/index', $data);
    }

    public function login()
    {
        if (ClientAuth::isLoggedIn()) {
            return redirect()->to(base_url('ksp'));
        }

        // Get Google client ID for Sign-In button
        $googleSettings = (new GoogleSettings())->find(1);
        $googleClientId = $googleSettings ? ($googleSettings['client_id'] ?? '') : '';

        return view('frontendV2/ksp/layouts/login', [
            'title' => "Login - KEWASNET",
            'googleClientId' => $googleClientId
        ]);
    }

    public function signup()
    {
        // Get Google client ID for Sign-In button
        $googleSettings = (new GoogleSettings())->find(1);
        $googleClientId = $googleSettings ? ($googleSettings['client_id'] ?? '') : '';

        return view('frontendV2/ksp/layouts/signup', [
            'title' => "Register - KEWASNET",
            'googleClientId' => $googleClientId
        ]);
    }

    public function forgetPassword()
    {
        return view('frontendV2/ksp/layouts/forget-password', ['title' => "Forget password - KEWASNET"]);
    }

    public function verifyResetCode()
    {
        if (!session()->has('reset_email')) {
            return redirect()->to(base_url('ksp/forget-password'))->with('error', 'Please request a password reset first');
        }

        return view('frontendV2/ksp/layouts/verify-code', ['title' => "Verify code - KEWASNET"]);
    }

    public function updateUserPassword()
    {
        $resetToken = session()->get('reset_email');
        $otp = session()->get('otp');

        if (!$resetToken || !$otp) {
            return redirect()->to(base_url('ksp/forget-password'))
                ->with('error', 'Invalid password reset request. Please start the process again.');
        }

        // Verify the OTP is still valid
        $tokenValid = $this->passwordResetToken->where('email', $resetToken)
            ->where('token', $otp)
            ->where('expires_at >', Carbon::now())
            ->first();

        if (!$tokenValid) {
            session()->remove(['reset_email', 'otp', 'reset_token']);
            return redirect()->to(base_url('ksp/forget-password'))
                ->with('error', 'Your reset token has expired. Please request a new one.');
        }

        return view('frontendV2/ksp/layouts/reset-password', ['title' => "Update password - KEWASNET"]);
    }

    public function handleClientLogin()
    {
        // Always return JSON for AJAX requests, even if validation fails
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'This endpoint only accepts AJAX requests',
                    'token' => csrf_hash()
                ]);
        }
        
        // Check CSRF token
        $csrfToken = $this->request->getPost(csrf_token());
        if (!$csrfToken) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'CSRF token is missing or invalid. Please refresh the page.',
                    'token' => csrf_hash()
                ]);
        }

        $session = session();

        $validation = \Config\Services::validation();
        $rules = [
            'email' => [
                'rules' => 'required|valid_email|is_not_unique[system_users.email]',
                'errors' => [
                    'required' => 'Email address is required',
                    'valid_email' => 'Please enter a valid email address',
                    'is_not_unique' => 'Email address does not exist in our system'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password is required'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($validation->getErrors(), 400);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $responseData = ClientAuth::attempt($email, $password);
        $responseData['token'] = csrf_hash();

        $redirectURL = $session->get('redirect_url');
        
        // Remove the redirect URL from session
        $session->remove('redirect_url');
        
        // For AJAX requests, return JSON with redirect URL instead of redirecting
        if ($redirectURL) {
            $responseData['redirect'] = $redirectURL;
        } else {
            $responseData['redirect'] = base_url('ksp');
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON($responseData);
    }

    public function handleSendResetCode()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'email' => [
                'rules' => 'required|valid_email|is_not_unique[system_users.email]',
                'errors' => [
                    'required' => 'Email address is required',
                    'valid_email' => 'Please enter a valid email address',
                    'is_not_unique' => 'Email address does not exist in our system'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($validation->getErrors(), 400);
        }

        $email = $this->request->getPost('email');

        if (!ResetPasswordService::sendResetPasswordEmail($email)) {
            return $this->ajaxErrorResponse(
                'An error occurred while sending the reset password email. Please try again.',
                500
            );
        }

        session()->set('reset_email', $email);

        return $this->ajaxSuccessResponse(
            'Password reset link sent to your email',
            ['token' => csrf_hash()]
        );
    }

    public function handleVerifyResetCode()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'otp' => [
                'rules' => 'required|exact_length[6]',
                'errors' => [
                    'required' => 'Reset code is required',
                    'exact_length' => 'Reset code must be exactly 6 characters long'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($validation->getErrors(), 400);
        }

        if (!session()->has('reset_email')) {
            return $this->ajaxErrorResponse('Invalid reset request', 400);
        }

        $resetCode = $this->request->getPost('otp');
        $result = $this->verifyCode($resetCode);

        if ($result['status'] === 'error') {
            return $this->ajaxErrorResponse($result['message'], $result['status_code']);
        }

        $verificationToken = GenerateIDs::generateToken();
        session()->set(['reset_token' => $verificationToken, 'otp' => $resetCode]);

        return $this->ajaxSuccessResponse('Reset code verified successfully', [
            'token' => $verificationToken,
            'csrf_token' => csrf_hash(),
            'redirect_url' => base_url('ksp/change-password')
        ]);
    }

    public function handleUpdateUserPassword()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'New password is required',
                    'min_length' => 'Password must be at least 8 characters long'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Confirm password is required',
                    'matches' => 'Passwords do not match'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($validation->getErrors(), 400);
        }

        $password = $this->request->getPost('password');
        $email = session()->get('reset_email');
        $otp = session()->get('otp');

        if (!$email || !$otp) {
            return $this->ajaxErrorResponse('Invalid reset session', 400);
        }

        $resetToken = $this->passwordResetToken->where('token', $otp)->first();

        if (!$resetToken) {
            return $this->ajaxErrorResponse(
                'Unauthorized password reset. Please try again using a proper channel.',
                403
            );
        }

        if (!ResetPasswordService::updatePassword($email, $password)) {
            return $this->ajaxErrorResponse(
                'An error occurred while updating your password. Please try again.',
                500
            );
        }

        // Clean up session and database
        session()->remove(['reset_token', 'reset_email', 'otp']);
        $this->passwordResetToken->where('token', $otp)->delete();

        return $this->ajaxSuccessResponse('Password updated successfully', [
            'token' => csrf_hash(),
            'redirect_url' => base_url('auth/login')
        ]);
    }

    public function handleClientRegistration()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $validation = \Config\Services::validation();
        $rules = [
            'full_name' => [
                'rules' => 'required|min_length[3]|validateFullName',
                'errors' => [
                    'required' => 'Full name is required',
                    'min_length' => 'Full name must be at least 3 characters long',
                    'validateFullName' => 'Please provide both first and last name'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[system_users.email]',
                'errors' => [
                    'required' => 'Email address is required',
                    'valid_email' => 'Please enter a valid email address',
                    'is_unique' => 'Email address already exists'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must be at least 8 characters long'
                ]
            ],
            'terms' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must accept the terms and conditions'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $db->transRollback();
            return $this->ajaxErrorResponse($validation->getErrors(), 400);
        }

        try {
            $postData = $this->request->getPost();
            $role = $this->rolesTable->getWhere(['role_name' => 'User'])->getRowArray();
            
            $postData['role_id'] = $role['role_id'];
            $postData['employee_number'] = (new GenerateIDs())->generateEmployeeNumber();

            $client = new UserModel();
            if ($insertId = $client->createUser($postData)) {
                $postData['verification_url'] = base_url('ksp/verify_account?code=' . $insertId);
                $postData['join_date'] = Carbon::now()->toDateTimeString();

                $authService = new AuthenticationService();
                
                // Try to send email
                try {
                    $emailSent = $authService->sendEmailToUser($postData);
                    
                    if ($emailSent) {
                        // Everything succeeded, commit the transaction
                        $db->transComplete();
                        
                        // Notify admins about new user registration
                        try {
                            $userModel = model('UserModel');
                            $adminUsers = $userModel->getAdministrators();
                            $userName = $postData['full_name'];
                            
                            if (!empty($adminUsers)) {
                                $notificationService = new \App\Services\NotificationService();
                                $adminIds = array_column($adminUsers, 'id');
                                $notificationService->notifyNewUserRegistration($adminIds, $userName, $insertId);
                            }
                            
                            // Notify user with welcome message
                            $notificationService->notifyUserWelcome($insertId, $userName);
                        } catch (\Exception $notificationError) {
                            log_message('error', "Error sending notification for new user registration: " . $notificationError->getMessage());
                            // Don't fail registration if notification fails
                        }
                        
                        return $this->ajaxSuccessResponse(
                            'Registration successful. Please check your email for a verification link'
                        );
                    } else {
                        // Email sending failed but user was created
                        $db->transComplete();
                        log_message('warning', 'User registered but verification email not sent for: ' . $postData['email']);
                        
                        // Notify admins about new user registration even if email failed
                        try {
                            $userModel = model('UserModel');
                            $adminUsers = $userModel->getAdministrators();
                            $userName = $postData['full_name'];
                            
                            if (!empty($adminUsers)) {
                                $notificationService = new \App\Services\NotificationService();
                                $adminIds = array_column($adminUsers, 'id');
                                $notificationService->notifyNewUserRegistration($adminIds, $userName, $insertId);
                            }
                            
                            // Notify user with welcome message
                            $notificationService->notifyUserWelcome($insertId, $userName);
                        } catch (\Exception $notificationError) {
                            log_message('error', "Error sending notification for new user registration: " . $notificationError->getMessage());
                        }
                        
                        return $this->ajaxSuccessResponse(
                            'Registration successful. However, we could not send a verification email. Please contact support to verify your account.'
                        );
                    }
                } catch (\Exception $emailError) {
                    // Email configuration error
                    $db->transComplete();
                    log_message('error', 'Email service error during registration: ' . $emailError->getMessage());
                    
                    return $this->ajaxSuccessResponse(
                        'Registration successful. However, email service is currently unavailable. Please contact support to verify your account.'
                    );
                }
            } else {
                // User creation failed
                throw new \RuntimeException('Failed to create user account');
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Registration error: ' . $e->getMessage());
            return $this->ajaxErrorResponse('An error occurred while processing your request: ' . $e->getMessage(), 500);
        }
    }

    public function verifyAccount()
    {
        $code = (int)$this->request->getGet('code');
        $user = (new UserModel())->verifyAccount($code);
        $session = session();

        switch ($user['status']) {
            case 'success':
                $session->setFlashdata('success', 'Account verified successfully. You can now login');
                break;
            case 'error':
                $message = $user['status_code'] === 404 
                    ? 'An error occurred while verifying your account. Please try again'
                    : 'Account has already been verified, please proceed to login';
                $session->setFlashdata($user['status_code'] === 403 ? 'warning' : 'error', $message);
                break;
        }

        return redirect()->to(base_url('ksp/login'));
    }

    protected function verifyCode($resetCode)
    {
        $resetToken = $this->passwordResetToken->where('token', $resetCode)->first();

        if (!$resetToken) {
            return [
                'status' => 'error',
                'status_code' => ResponseInterface::HTTP_FORBIDDEN,
                'message' => 'Invalid reset code'
            ];
        }

        if (Carbon::parse($resetToken['expires_at'])->isPast()) {
            $this->passwordResetToken->where('token', $resetCode)->delete();
            return [
                'status' => 'error',
                'status_code' => ResponseInterface::HTTP_FORBIDDEN,
                'message' => 'Reset code has expired. Please request a new one.'
            ];
        }

        return [
            'status' => 'success',
            'status_code' => ResponseInterface::HTTP_OK,
            'message' => 'Reset code is valid.',
            'data' => $resetToken
        ];
    }

    protected function isValidAjaxRequest(): bool
    {
        // Check if it's an AJAX request
        $isAjax = $this->request->isAJAX();
        
        // Check if CSRF token exists in POST data
        $csrfToken = $this->request->getPost(csrf_token());
        
        // Log for debugging (remove in production)
        if (!$isAjax || !$csrfToken) {
            log_message('debug', 'AJAX validation failed: isAJAX=' . ($isAjax ? 'true' : 'false') . ', hasCSRF=' . ($csrfToken ? 'true' : 'false'));
        }
        
        return $isAjax && $csrfToken;
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
            ->setContentType('application/json')
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