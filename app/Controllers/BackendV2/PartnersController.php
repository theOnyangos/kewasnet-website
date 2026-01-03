<?php

namespace App\Controllers\BackendV2;

use App\Models\PartnerModel;
use App\Services\DataTableService;
use App\Controllers\BaseController;
use App\Exceptions\ValidationException;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\PartnerService;

class PartnersController extends BaseController
{    
    protected const PAGE_TITLE = 'Manage Partners - KEWASNET';
    protected const PAGE_DESCRIPTION = 'Manage partners and their details.';
    protected const ALLOWED_ORDER_COLUMNS = ['id', 'partner_name', 'partner_logo', 'partner_url', 'created_at'];
    protected const MAX_ORDER_COLUMN_INDEX = 4; // Should match count of ALLOWED_ORDER_COLUMNS - 1

    protected $dataTableService;
    protected $PartnerService;

    public function __construct()
    {
        $this->dataTableService = new DataTableService();
        $this->partnerService = new PartnerService();
    }

    /**
     * Display the partners management page
     */
    public function index()
    {
        return view('backendV2/pages/partners/index', [
            'title' => self::PAGE_TITLE,
            'description' => self::PAGE_DESCRIPTION
        ]);
    }

    /**
     * Display the create partner page
     */
    public function create()
    {
        return view('backendV2/pages/partners/create', [
            'title' => 'Create New Partner - KEWASNET',
            'description' => 'Add a new partner organization to the network'
        ]);
    }

    /**
     * Get partners data for DataTables
     */
    public function getPartnersData()
    {
        $model = model(PartnerModel::class);
        $columns = ['id', 'partner_name', 'partner_logo', 'partner_url', 'created_at'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getPartners',
            'countAllPartners',
        );
    }

    /**
     * Create a new partner
     */
    public function createPartner()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $partnerData = $this->request->getPost();
            $file = $this->request->getFile('partner_logo');

            log_message('info', 'File object: ' . print_r($file, true));

            $result = $this->partnerService->handlePartnerCreation($partnerData, $file);

            $response = [
                'success'  => true,
                'status'   => 'success',
                'message'  => 'Partner registered successfully! Thank you for joining us.',
                'data'     => $result,
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Partner creation error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success'    => false,
                'status'     => 'error',
                'message'    => 'Failed to create partner: ' . $e->getMessage(),
                'data'       => [],
                'error_type' => 'server_error'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the edit partner page
     */
    public function edit($id)
    {
        $partnerModel = new PartnerModel();
        $partner = $partnerModel->find($id);

        if (!$partner) {
            return redirect()->to(base_url('auth/partners'))->with('error', 'Partner not found');
        }

        return view('backendV2/pages/partners/edit', [
            'title' => 'Edit Partner - KEWASNET',
            'description' => 'Update partner information and settings',
            'partner' => $partner
        ]);
    }

    /**
     * Update partner information
     */
    public function updatePartner($id)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $partnerData = $this->request->getPost();
            $file = $this->request->getFile('partner_logo');

            log_message('info', 'Updating partner ID: ' . $id);
            log_message('info', 'File object: ' . print_r($file, true));

            $result = $this->partnerService->handlePartnerUpdate($id, $partnerData, $file);

            $response = [
                'success'  => true,
                'status'   => 'success',
                'message'  => 'Partner updated successfully!',
                'data'     => $result,
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Partner update error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success'    => false,
                'status'     => 'error',
                'message'    => 'Failed to update partner: ' . $e->getMessage(),
                'data'       => [],
                'error_type' => 'server_error'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove partner logo
     */
    public function removeLogo($id)
    {
        try {
            log_message('info', 'Removing logo for partner ID: ' . $id);

            $result = $this->partnerService->handleLogoRemoval($id);

            $response = [
                'success'  => true,
                'status'   => 'success',
                'message'  => 'Partner logo removed successfully!',
                'data'     => $result,
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            log_message('error', 'Partner logo removal error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success'    => false,
                'status'     => 'error',
                'message'    => 'Failed to remove partner logo: ' . $e->getMessage(),
                'data'       => [],
                'error_type' => 'server_error'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete partner
     */
    public function deletePartner($id)
    {
        try {
            log_message('info', 'Deleting partner ID: ' . $id);

            $result = $this->partnerService->handlePartnerDeletion($id);

            $response = [
                'success'  => true,
                'status'   => 'success',
                'message'  => 'Partner deleted successfully!',
                'data'     => $result,
            ];

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            log_message('error', 'Partner deletion error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success'    => false,
                'status'     => 'error',
                'message'    => 'Failed to delete partner: ' . $e->getMessage(),
                'data'       => [],
                'error_type' => 'server_error'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handleErrorResponse($message, $statusCode, $error = null)
    {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $message,
            'data' => $error
        ])->setStatusCode($statusCode);
    }

    public function handleSuccessResponse($message, $statusCode, $data = null)
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ])->setStatusCode($statusCode);
    }

    protected function failValidationErrors(array $errors): ResponseInterface
    {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validation failed. Fix errors in inputs and try again.',
            'errors' => $errors,
            'error_type' => 'validation'
        ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }
}