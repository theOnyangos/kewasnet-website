<?php

namespace App\Controllers\BackendV2;

use Carbon\Carbon;
use App\Libraries\CIAuth;
use App\Filters\AuthFilter;
use App\Libraries\ClientAuth;
use App\Libraries\GenerateIDs;
use App\Models\PasswordResetToken;
use App\Controllers\BaseController;
use App\Services\ResetPasswordService;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $authModel;
    protected $rolesModel;
    protected $passwordResetToken;

    public function __construct()
    {
        $this->authModel = model('AuthModel');
        $this->rolesModel = model('RolesModel');
        $this->passwordResetToken = new PasswordResetToken();
    }

    public function login()
    {
        if (CIAuth::isLoggedIn()) {
            return redirect()->to('/auth/dashboard');
        }

        session()->remove(['reset_token', 'reset_email']);
        return view('backendV2/layouts/login', ['title' => "Admin Login | Admin Portal"]);
    }

    public function forgetPassword()
    {
        session()->remove(['reset_token', 'reset_email']);
        return view('backendV2/layouts/forgot-password', ['title' => "Forgot Password | Admin Portal"]);
    }

    public function verifyResetCode()
    {
        $email = $this->request->getGet('email');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', $email ? 'Invalid email format' : 'Email is required to verify reset code');
        }

        // Store both email and reset flag in session
        session()->set([
            'reset_email' => $email,
            'reset_in_progress' => true
        ]);

        return view('backendV2/layouts/verify-otp', ['title' => "Verify Reset Code | Admin Portal"]);
    }

    public function changePassword()
    {
        // Check both the reset flag and email in session
        if (!session()->has('reset_in_progress') || !session()->has('reset_email')) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Invalid password reset request. Please start the process again.');
        }

        // Additional check for verified token if needed
        if (!session()->has('reset_token')) {
            return redirect()->to('/auth/verify-reset-code?email='.session()->get('reset_email'))
                ->with('error', 'You must verify your reset code first');
        }

        return view('backendV2/layouts/change-password', ['title' => "Change Password | Admin Portal"]);
    }

    public function handleLogin()
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
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password is required'
                ]
            ]
        ];

        try {
            if (!$this->validate($rules)) {
                return $this->ajaxErrorResponse($validation->getErrors(), 400);
            }
    
            $email      = $this->request->getPost('email');
            $password   = $this->request->getPost('password');

            $responseData = CIAuth::attempt($email, $password);

            $responseData['token'] = csrf_hash();

            return $this->response->setJSON($responseData);
        } catch (\Exception $e) {
            return $this->ajaxErrorResponse('An error occurred while processing your request: ' . $e->getMessage(), 500);
        }
    }

    public function logoutHandler()
    {
        CIAuth::logout();
        return redirect()->to('auth/login');
    }

    public function handleClientLogout()
    {
        ClientAuth::logout();
        return redirect()->to('ksp/login');
    }

    public function handleForgetPassword()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'email' => [
                'rules' => 'required|valid_email|is_not_unique[users.email]',
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

        return $this->ajaxSuccessResponse('Password reset link sent to your email');
    }

    public function handleVerifyResetCode()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'reset_code' => [
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

        $resetCode = $this->request->getPost('reset_code');
        $email = session()->get('reset_email');

        if (!$email) {
            return $this->ajaxErrorResponse('Invalid reset request', 400);
        }

        $resetToken = $this->passwordResetToken->where('email', $email)
            ->where('token', $resetCode)
            ->first();

        if (!$resetToken) {
            return $this->ajaxErrorResponse('Invalid reset code', 400);
        }

        if (Carbon::parse($resetToken['expires_at'])->isPast()) {
            $this->passwordResetToken->where('token', $resetCode)->delete();
            return $this->ajaxErrorResponse('Reset code has expired. Please request a new one.', 400);
        }

        // Set verification token and mark as verified
        session()->set([
            'reset_token' => GenerateIDs::generateToken(),
            'otp' => $resetCode,
            'code_verified' => true  // Add this flag
        ]);

        return $this->ajaxSuccessResponse('Reset code verified successfully', [
            'redirect_url' => base_url('auth/change-password')
        ]);
    }

    public function handleUpdateUserPassword()
    {
        if (!$this->isValidAjaxRequest()) return $this->ajaxErrorResponse('Method not allowed', 405);

        $validation = \Config\Services::validation();
        $rules = [
            'new_password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'New password is required',
                    'min_length' => 'Password must be at least 8 characters long'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Confirm password is required',
                    'matches' => 'Passwords do not match'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->ajaxErrorResponse($validation->getErrors(), 400);
        }

        // Check all required session data exists
        if (!session()->has('reset_email') || !session()->has('otp') || !session()->get('code_verified')) {
            return $this->ajaxErrorResponse('Invalid reset session', 400);
        }

        $newPassword = $this->request->getPost('new_password');
        $email = session()->get('reset_email');

        // Update password
        if (!ResetPasswordService::updatePassword($email, $newPassword)) {
            return $this->ajaxErrorResponse(
                'An error occurred while updating your password. Please try again.',
                500
            );
        }

        // Clear all reset-related session data
        session()->remove([
            'reset_token', 
            'reset_email', 
            'otp',
            'reset_in_progress',
            'code_verified'
        ]);

        return $this->ajaxSuccessResponse('Password updated successfully', [
            'redirect_url' => base_url('auth/login')
        ]);
    }

    protected function isValidResetSession($token = null, $email = null, $otp = null): bool
    {
        if ($token === null && $email === null && $otp === null) {
            $token = session()->get('reset_token');
            $email = session()->get('reset_email');
            $otp = session()->get('otp');
        }

        if (!$email || !$otp) {
            return false;
        }

        $tokenRecord = $this->passwordResetToken
            ->where('email', $email)
            ->where('token', $otp)
            ->where('expires_at >', Carbon::now())
            ->first();

        return $tokenRecord !== null;
    }

    protected function cleanupResetSession(): void
    {
        $otp = session()->get('otp');
        if ($otp) {
            $this->passwordResetToken->where('token', $otp)->delete();
        }
        
        session()->remove(['reset_token', 'reset_email', 'otp']);
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