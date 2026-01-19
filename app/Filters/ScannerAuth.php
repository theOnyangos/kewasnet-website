<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ScannerAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if ($session->get('scanner_is_authed') === true) {
            return;
        }

        // Allow login endpoints to be accessed without scanner session
        $path = trim((string) $request->getUri()->getPath(), '/');
        if ($path === 'scanner/login') {
            return;
        }

        // If this is an API call, return JSON instead of redirect.
        $accept = strtolower((string) $request->getHeaderLine('Accept'));
        $isJson = str_contains($accept, 'application/json') || $request->isAJAX();
        if ($isJson) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Scanner authentication required']);
        }

        // Otherwise redirect to scanner login (standalone auth)
        return redirect()->to(base_url('scanner/login'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}

