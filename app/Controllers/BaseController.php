<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use PHPUnit\Util\Json;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }


    protected function respondToNonAjax($statusCode = 301, $message = 'Unauthorized request. Please try again'): ResponseInterface
    {
        $response = [
            'status' => 'error',
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => null,
        ];
        return $this->response->setJSON($response);
    }

    public function runValidation($validation)
    {
        if (!$validation->withRequest($this->request)->run()) {
            $response = [
                'status' => 'error',
                'statusCode' => ResponseInterface::HTTP_BAD_REQUEST,
                'message' => $validation->getErrors(),
            ];
            return $this->response->setJSON($response);
        }
    }

    protected function generateJsonResponse($status, $statusCode, $message, $data = [], $exception = null)
    {
        $response = [
            'status' => $status,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data
        ];

        if ($exception) {
            $response['errors'] = [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ];
        }

        return $this->response->setJSON($response);
    }

    public function redirectToLogin()
    {
        $session = session();
        
        // Store the current URL (excluding login URLs to avoid loops)
        $currentURL = current_url();
        $loginURL = site_url('auth/login'); // Adjust to your login route
        
        if ($currentURL !== $loginURL) {
            $session->set('redirect_url', $currentURL);
        }

        return redirect()->to('auth/login');
    }
}
