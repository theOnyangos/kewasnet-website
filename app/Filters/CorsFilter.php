<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CORS Filter
 * 
 * Adds CORS headers to allow cross-origin requests for development
 */
class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Allow CORS for debugbar and other development tools
        if (ENVIRONMENT === 'development') {
            $response = service('response');
            
            // Get the origin from the request
            $origin = $request->getHeaderLine('Origin');
            
            // If no origin, use the base URL
            if (empty($origin)) {
                $origin = config('App')->baseURL;
            }
            
            // Set CORS headers
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
            $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            
            // Handle preflight requests
            if ($request->getMethod() === 'options') {
                return $response;
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add CORS headers to response
        if (ENVIRONMENT === 'development') {
            $origin = $request->getHeaderLine('Origin');
            
            if (empty($origin)) {
                $origin = config('App')->baseURL;
            }
            
            $response->setHeader('Access-Control-Allow-Origin', rtrim($origin, '/'));
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
        }
        
        return $response;
    }
}

