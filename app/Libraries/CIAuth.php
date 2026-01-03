<?php

namespace App\Libraries;

use App\Models\AccountDeletionRequest;
use App\Models\UserModel;
use Carbon\Carbon;
use CodeIgniter\I18n\Time;

class CIAuth 
{
    protected static $userModel;
    protected static $accountDeletionModel;

    public static function initialize()
    {
        if (!isset(self::$userModel)) {
            self::$userModel = model('UserModel');
        }
        if (!isset(self::$accountDeletionModel)) {
            self::$accountDeletionModel = new AccountDeletionRequest();
        }
    }

    public static function attempt(string $email, string $password): array
    {
        self::initialize();
        
        $hash = new Hash();
        $user = self::$userModel->where('email', $email)->first();

        if (!$user) {
            return self::buildResponse('error', 404, 'User with the provided credentials does not exist');
        }

        // Validate admin access
        if ($user['role_id'] != 1) {
            return self::buildResponse('error', 403, 'You are not authorized to access this resource');
        }

        // Check account status
        $accountStatusError = self::validateAccountStatus($user);
        if ($accountStatusError) {
            return $accountStatusError;
        }

        // Verify password
        if (!$hash::verify($password, $user['password'])) {
            return self::buildResponse('error', 403, 'Invalid credentials');
        }

        // Login successful
        self::setUserSession($user);
        return self::buildResponse(
            'success', 
            200, 
            'Login successful, Welcome back ' . $user['first_name'] . ' ' . $user['last_name']
        );
    }

    protected static function validateAccountStatus(array $user): ?array
    {
        if ($user['email_verified_at'] === null) {
            return self::buildResponse('error', 403, 
                'Your account is not verified. Please check your email for verification link.'
            );
        }

        if ($user['deleted_at'] !== null) {
            return self::buildResponse('error', 403, 
                'Your account has been suspended. Please contact the administrator.'
            );
        }

        $account = self::checkAccountStatus($user['id']);
        if ($account && $account['status'] === 'approved') {
            $daysSinceDeletion = Carbon::parse($account['created_at'])->diffInDays(Time::now());
            if ($daysSinceDeletion >= 30) {
                return self::buildResponse('error', 403,
                    'This account was closed more than 30 days ago. Please contact the administrator.'
                );
            }
        }

        return null;
    }

    protected static function setUserSession(array $user): void
    {
        $sessionData = [
            'id'              => $user['id'],
            'name'            => $user['first_name'] . ' ' . $user['last_name'],
            'first_name'      => $user['first_name'],
            'last_name'       => $user['last_name'],
            'employee_number' => $user['employee_number'],
            'phone'           => $user['phone'],
            'role_id'         => $user['role_id'],
            'username'        => $user['username'],
            'email'           => $user['email'],
            'isLoggedIn'      => true,
            'isAdmin'         => true,
        ];

        // Add account status if exists
        if ($account = self::checkAccountStatus($user['id'])) {
            $sessionData['account_status'] = $account['status'];
            $sessionData['account_status_date'] = $account['created_at'];
        }

        session()->set($sessionData);

        // Update login time
        self::$userModel->update($user['id'], [
            'updated_at' => Carbon::now()
        ]);
    }

    public static function checkAccountStatus(string $userId): ?array
    {
        self::initialize();
        return self::$accountDeletionModel->where('user_id', $userId)->first();
    }

    public static function isLoggedIn(): bool
    {
        $session = session();
        return $session->has('isLoggedIn') 
            && $session->get('isLoggedIn') === true;
    }

    public static function isAdmin(): bool
    {
        $session = session();
        return $session->has('isAdmin') 
            && $session->get('isAdmin') === true;
    }

    public static function user(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        self::initialize();
        return self::$userModel->find(session()->get('id'));
    }

    public static function logout(): bool
    {
        session()->destroy();
        return true;
    }

    protected static function buildResponse(string $status, string $code, string $message): array
    {
        return [
            'status' => $status,
            'code' => $code,
            'message' => $message
        ];
    }
}