<?php

namespace App\Helpers;

use CodeIgniter\HTTP\RequestInterface;

class RedirectHelper
{
    /**
     * Store the current URL for redirect after login
     * Excludes login URLs to prevent redirect loops
     *
     * @param RequestInterface $request
     * @param string $excludePath Path to exclude (default: '/auth/login')
     * @return void
     */
    public static function rememberIntendedUrl(RequestInterface $request, string $excludePath = '/auth/login')
    {
        $session = session();
        $currentUrl = current_url();
        $excludeUrl = site_url($excludePath);
        
        // Don't store exclude URL or AJAX requests to avoid redirect loops
        if ($currentUrl !== $excludeUrl && !$request->isAJAX()) {
            $session->set('redirect_url', $currentUrl);
        }
    }
    
    /**
     * Get the intended redirect URL from session and remove it
     *
     * @param string $defaultUrl Default URL to return if no intended URL is stored
     * @return string
     */
    public static function getIntendedUrl(string $defaultUrl = '/'): string
    {
        $session = session();
        $redirectUrl = $session->get('redirect_url');
        $session->remove('redirect_url');
        
        return $redirectUrl ?: $defaultUrl;
    }
    
    /**
     * Check if there's an intended URL stored in session
     *
     * @return bool
     */
    public static function hasIntendedUrl(): bool
    {
        return !empty(session()->get('redirect_url'));
    }
    
    /**
     * Forget the intended URL (remove from session)
     *
     * @return void
     */
    public static function forgetIntendedUrl(): void
    {
        session()->remove('redirect_url');
    }
    
    /**
     * Redirect to the intended URL or a default URL
     * This is a convenience method that gets the URL and redirects in one call
     *
     * @param string $defaultUrl
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public static function redirectToIntended(string $defaultUrl = '/')
    {
        return redirect()->to(self::getIntendedUrl($defaultUrl));
    }
    
    /**
     * Store intended URL with additional query parameters
     *
     * @param RequestInterface $request
     * @param array $params Additional query parameters to include
     * @param string $excludePath
     * @return void
     */
    public static function rememberIntendedUrlWithParams(
        RequestInterface $request, 
        array $params = [], 
        string $excludePath = '/auth/login'
    ) {
        $session = session();
        $currentUrl = current_url();
        $excludeUrl = site_url($excludePath);
        
        if ($currentUrl !== $excludeUrl && !$request->isAJAX()) {
            // Add query parameters if provided
            if (!empty($params)) {
                $separator = (parse_url($currentUrl, PHP_URL_QUERY) ? '&' : '?');
                $currentUrl .= $separator . http_build_query($params);
            }
            
            $session->set('redirect_url', $currentUrl);
        }
    }
    
    /**
     * Get intended URL with a fallback to HTTP referrer
     *
     * @param RequestInterface $request
     * @param string $defaultUrl
     * @param string $excludePath Path to exclude from referrer
     * @return string
     */
    public static function getIntendedUrlWithReferrerFallback(
        RequestInterface $request, 
        string $defaultUrl = '/',
        string $excludePath = '/auth/login'
    ): string {
        $intendedUrl = self::getIntendedUrl();
        
        // If no intended URL, try using HTTP referrer
        if (!$intendedUrl) {
            $referrer = $request->getServer('HTTP_REFERER');
            $excludeUrl = site_url($excludePath);
            
            if ($referrer && $referrer !== $excludeUrl && strpos($referrer, $excludePath) === false) {
                $intendedUrl = $referrer;
            }
        }
        
        return $intendedUrl ?: $defaultUrl;
    }
}