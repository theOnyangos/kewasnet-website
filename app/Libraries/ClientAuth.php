<?php

namespace App\Libraries;

use Carbon\Carbon;
use App\Models\UserBrowser;
use App\Models\RoleModel;
use App\Libraries\Mailer;
use App\Libraries\SmsHandler;
use CodeIgniter\Session\Session;

class ClientAuth 
{
    protected static $userModel = 'UserModel';
    protected static $defaultRedirect = 'client/dashboard';

    /**
     * Attempt to log in a user
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    public static function attempt(string $username, string $password): array
    {
        $user = self::findUser($username);
        
        if (!$user) {
            return self::errorResponse('User does not exist. Please try again.', 404);
        }

        if ($user['deleted_at'] !== null) {
            return self::errorResponse('No account found. Please contact our support team for assistance', 403);
        }

        if ($user['status'] === 'inactive') {
            return self::errorResponse('Your account has been suspended. Please contact our support team at info@kewasnet.co.ke for assistance.', 403);
        }

        if ($user['email_verified_at'] === null) {
            return self::errorResponse('Your account has not been verified. Please check your email for the verification link', 403);
        }

        if (!self::hasValidRole($user)) {
            return self::errorResponse('You are not authorized to access this resource. Get permissions or create an account to continue.', 403);
        }
        
        if (!self::verifyPassword($password, $user['password'])) {
            return self::errorResponse('Username or password provided is incorrect. Please try again.', 401);
        }

        return self::handleSuccessfulLogin($user);
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        $session = session();
        return $session->has('isLoggedIn') 
            && $session->get('isLoggedIn') === true 
            && $session->get('isAdmin') === false;
    }

    public static function getId()
    {
        return self::getSessionData('id');
    }

    public static function getRole()
    {
        return self::getSessionData('role');
    }

    public static function getRoleName()
    {
        $roleId = self::getRole();
        if (!$roleId) return false;

        $roleModel = new RoleModel();
        $role = $roleModel->find($roleId);
        return $role['role_name'] ?? false;
    }

    public static function getName()
    {
        return self::getSessionData('name');
    }

    public static function getImage()
    {
        $userId = self::getId();
        if (!$userId) return false;

        $user = model(self::$userModel)->find($userId);
        return $user['picture'] ?? false;
    }

    public static function user()
    {
        $userId = self::getId();
        if (!$userId) return false;

        return model(self::$userModel)->find($userId);
    }

    public static function logout(): bool
    {
        $userId = self::getId();
        if ($userId) {
            self::removeLoginActivity($userId);
        }

        session()->destroy();
        return true;
    }

    public static function getBrowserInfo(): array
    {
        $request = service('request');
        $userAgent = $request->getUserAgent();

        return [
            'browser' => $userAgent->getBrowser(),
            'ip' => $request->getIPAddress(),
            'platform' => $userAgent->getPlatform()
        ];
    }

    public static function storeLoginInfo(string $userId, string $browser, string $ip, string $platform, string $type): bool
    {
        $userBrowserModel = new UserBrowser();
        $userBrowserModel->insert([
            'user_id' => $userId,
            'browser' => $browser,
            'ip_address' => $ip,
            'platform' => $platform,
            'login_type' => $type,
            'login_time' => Carbon::now()
        ]);
        return true;
    }

    public static function removeLoginActivity(string $userId): bool
    {
        (new UserBrowser())->where('user_id', $userId)->delete();
        return true;
    }

    public static function getLoginActivity(string $userId): array
    {
        return (new UserBrowser())
            ->where('user_id', $userId)
            ->orderBy('login_time', 'DESC')
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Protected Helper Methods
    // -------------------------------------------------------------------------

    protected static function findUser(string $username)
    {
        $field = is_numeric($username) ? 'phone' : 'email';
        return model(self::$userModel)->where($field, $username)->first();
    }

    protected static function hasValidRole(array $user): bool
    {
        return in_array($user['role_id'], [2, 3]);
    }

    protected static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    protected static function handleSuccessfulLogin(array $user): array
    {
        $browserInfo = self::getBrowserInfo();
        self::storeLoginInfo(
            $user['id'], 
            $browserInfo['browser'], 
            $browserInfo['ip'], 
            $browserInfo['platform'], 
            "username_&_password"
        );

        self::setUserSession($user);

        $redirectUrl = session()->get('redirect_url') ?? base_url(self::$defaultRedirect);
        if (session()->has('redirect_url')) {
            session()->remove('redirect_url');
        }

        return self::successResponse(
            'Welcome back ' . $user['first_name'] . ' ' . $user['last_name'] . '! Have a productive day.',
            $user,
            $redirectUrl
        );
    }

    protected static function setUserSession(array $user): void
    {
        $sessionData = [
            'id'         => $user['id'],
            'role'       => $user['role_id'],
            'name'       => $user['first_name'] . ' ' . $user['last_name'],
            'phone'      => $user['phone'],
            'email'      => $user['email'],
            'isLoggedIn' => true,
            'isAdmin'    => false
        ];

        session()->set($sessionData);
    }

    protected static function getSessionData(string $key)
    {
        if (!self::isLoggedIn()) return false;
        return session()->get($key);
    }

    protected static function errorResponse(string $message, int $statusCode): array
    {
        return [
            'status'        => 'error',
            'status_code'   => $statusCode,
            'message'       => $message,
        ];
    }

    protected static function successResponse(string $message, array $user, string $redirectUrl): array
    {
        unset($user['password']);
        
        return [
            'status'        => 'success',
            'status_code'   => 200,
            'message'       => $message,
            'user'          => $user,
            'redirect_url'  => $redirectUrl
        ];
    }
}