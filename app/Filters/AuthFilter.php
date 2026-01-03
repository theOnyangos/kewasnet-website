<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CIAuth;
use App\Libraries\ClientAuth;

class AuthFilter implements FilterInterface
{
    protected $response;
    
    public function __construct()
    {
        $this->response = service('response');
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // dd($arguments[1]);
        // If no arguments provided, skip filtering
        if (empty($arguments)) {
            return;
        }

        $authType       = $arguments[0] ?? null;
        $redirectRoute  = $arguments[1] ?? null;

        switch ($authType) {
            case 'auth': // For regular user auth
                if (!CIAuth::isLoggedIn()) {
                    return $this->redirectOrJsonResponse(
                        $request,
                        $redirectRoute ?? '/auth/login',
                        'Please login first',
                        false
                    );
                }
                break;
                
            case 'admin': // For admin auth
                if (!CIAuth::isAdmin()) {
                    return $this->redirectOrJsonResponse(
                        $request,
                        $redirectRoute ?? '/auth/login',
                        'Admin access required',
                        false
                    );
                }
                break;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }

    protected function redirectOrJsonResponse(
        RequestInterface $request,
        string $redirectUrl,
        string $message,
        bool $isAlreadyLoggedIn = true
    ) {
        if ($request->isAJAX()) {
            return $this->response->setStatusCode($isAlreadyLoggedIn ? 403 : 401)
                ->setJSON([
                    'success' => false,
                    'message' => $message,
                    'redirect' => $isAlreadyLoggedIn ? $redirectUrl : null
                ]);
        }

        // For non-AJAX requests, redirect to the specified URL
        return redirect()->to($redirectUrl)->with('error', $message);
    }

    /**
     * Get the redirect URL from session and remove it
     */
    public static function getRedirectUrl()
    {
        $session = session();
        $redirectUrl = $session->get('redirect_url');
        $session->remove('redirect_url');
        
        return $redirectUrl;
    }
}