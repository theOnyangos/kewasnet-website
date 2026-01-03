<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Faq extends BaseController
{
    protected const PAGE_TITLE = "KEWASNET - FAQ";
    protected const META_DESCRIPTION = "Frequently Asked Questions about KEWASNET";

    protected $faqModel;

    public function __construct()
    {
        $this->faqModel = model('App\Models\Faq');
    }

    public function index()
    {
        return view('frontendV2/website/pages/faq/index', [
            'title' => self::PAGE_TITLE,
            'description' => self::META_DESCRIPTION
        ]);
    }

    public function handleGetFaqs()
    {
        try {
            $faqs = $this->faqModel->findAll();
            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'FAQs retrieved successfully.',
                $faqs
            );
        } catch (\Exception $e) {
            log_message('error', 'Blog creation error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create blog post: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    //======== Helper Methods ==========

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
