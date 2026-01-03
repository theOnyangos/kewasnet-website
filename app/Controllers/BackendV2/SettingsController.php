<?php

namespace App\Controllers\BackendV2;

use App\Models\YoutubeLinkModel;
use App\Services\DataTableService;
use App\Services\SettingsService;
use App\Services\SitemapService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SettingsController extends BaseController
{
    protected DataTableService $dataTableService;
    protected SettingsService $settingsService;
    protected SitemapService $sitemapService;

    public function __construct()
    {
        $this->dataTableService = new DataTableService();
        $this->settingsService = new SettingsService();
        $this->sitemapService = new SitemapService();
    }

    public function index()
    {
        $title = "Social Media Settings - KEWASNET";
        $data = ['title' => $title, 'dashboardTitle' => 'Social Media Settings'];
        return view('backendV2/pages/settings/index', $data);
    }

    public function socialMedia()
    {
        $title = "Social Media Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'social-media',
            'dashboardTitle' => 'Social Media Settings'
        ];
        return view('backendV2/pages/settings/social_media_panel', $data);
    }

    public function youtube()
    {
        $title = "YouTube Links Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'youtube',
            'dashboardTitle' => 'YouTube Links Settings'
        ];
        return view('backendV2/pages/settings/youtube_panel', $data);
    }

    public function partners()
    {
        $title = "Partners Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'partners',
            'dashboardTitle' => 'Partners Settings'
        ];
        return view('backendV2/pages/settings/partners_panel', $data);
    }

    public function payments()
    {
        $title = "Payment Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'payments',
            'dashboardTitle' => 'Payment Settings'
        ];
        return view('backendV2/pages/settings/payments_panel', $data);
    }

    public function email()
    {
        $title = "Email Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'email',
            'dashboardTitle' => 'Email Settings'
        ];
        return view('backendV2/pages/settings/email_settings', $data);
    }

    public function sms()
    {
        $title = "SMS Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'sms',
            'dashboardTitle' => 'SMS Settings'
        ];
        return view('backendV2/pages/settings/sms_panel', $data);
    }

    public function google()
    {
        $title = "Google Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'google',
            'dashboardTitle' => 'Google Settings'
        ];
        return view('backendV2/pages/settings/google_panel', $data);
    }

    public function sitemap()
    {
        $title = "Sitemap Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'sitemap',
            'dashboardTitle' => 'Sitemap Settings'
        ];
        return view('backendV2/pages/settings/sitemap_panel', $data);
    }

    public function faqs()
    {
        $title = "FAQs Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'faqs',
            'dashboardTitle' => 'FAQs Settings'
        ];
        return view('backendV2/pages/settings/faqs_panel', $data);
    }

    public function getSocialMedia(): ResponseInterface
    {
        $result = $this->settingsService->getSocialMediaSettings();
        return $this->response->setJSON($result);
    }

    public function saveSocialMedia(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->settingsService->saveSocialMediaSettings($data);
        return $this->response->setJSON($result);
    }

    /**
     * Get YouTube links for DataTables
     */
    public function getYouTubeLinks()
    {
        return $this->settingsService->getYouTubeLinksForDataTables();
    }

    /**
     * Get Partners for DataTables
     */
    public function getPartners()
    {
        return $this->settingsService->getPartnersForDataTables();
    }

    /**
     * Save Partner (create or update)
     */
    public function savePartner()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        // Handle file upload and form data
        $data = [
            'partner_name'     => $this->request->getPost('partner_name'),
            'partnership_type' => $this->request->getPost('partnership_type'),
            'partner_url'      => $this->request->getPost('partner_url'),
            'description'      => $this->request->getPost('description'),
            'id'               => $this->request->getPost('id')
        ];

        // Handle file upload
        $logoFile = $this->request->getFile('partner_logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $data['partner_logo'] = $logoFile;
        }

        $result = $this->settingsService->savePartner($data);
        
        $statusCode = $result['status'] === 'error' ? 400 : 200;
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    /**
     * Get single Partner
     */
    public function getPartner($id)
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $result = $this->settingsService->getPartnerById((int)$id);
        
        $statusCode = $result['status'] === 'error' ? 404 : 200;
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    /**
     * Delete Partner
     */
    public function deletePartner($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        $result = $this->settingsService->deletePartner((int)$id);
        
        // Determine status code based on result
        $statusCode = 200; // Default success
        if ($result['status'] === 'error') {
            if ($result['message'] === 'Partner not found') {
                $statusCode = 404;
            } else {
                $statusCode = 500;
            }
        }
        
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    /**
     * Save YouTube link (create or update)
     */
    public function saveYouTubeLink()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        $data = [
            'description' => $this->request->getPost('description'),
            'title' => $this->request->getPost('title'),
            'link' => $this->request->getPost('link'),
            'id' => $this->request->getPost('id')
        ];

        $result = $this->settingsService->saveYouTubeLink($data);
        
        $statusCode = $result['status'] === 'error' ? 400 : 200;
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    /**
     * Get single YouTube link
     */
    public function getYouTubeLink($id)
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $result = $this->settingsService->getYouTubeLinkById((int)$id);
        
        $statusCode = $result['status'] === 'error' ? 404 : 200;
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    /**
     * Delete YouTube link
     */
    public function deleteYouTubeLink($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        $result = $this->settingsService->deleteYouTubeLink((int)$id);
        
        $statusCode = match($result['status']) {
            'error' => $result['message'] === 'YouTube link not found' ? 404 : 500,
            default => 200
        };
        
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    // ==================== FAQ METHODS ====================

    /**
     * Get FAQs for DataTables
     */
    public function getFaqs()
    {
        return $this->settingsService->getFaqsForDataTables();
    }

    /**
     * Save FAQ (create or update)
     */
    public function saveFaq()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        // Handle form data
        $data = [
            'question'      => $this->request->getPost('question'),
            'answer'        => $this->request->getPost('answer'),
            'category'      => $this->request->getPost('category'),
            'status'        => $this->request->getPost('status') ?: 'active',
            'display_order' => $this->request->getPost('display_order') ?: 0,
            'tags'          => $this->request->getPost('tags'),
            'is_featured'   => $this->request->getPost('is_featured') ? 1 : 0,
            'searchable'    => $this->request->getPost('searchable') ? 1 : 0,
            'id'            => $this->request->getPost('id')
        ];

        $result = $this->settingsService->saveFaq($data);
        
        $statusCode = match($result['status']) {
            'error' => 400,
            default => 200
        };
        
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    /**
     * Get single FAQ
     */
    public function getFaq($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        $result = $this->settingsService->getFaqById($id);
        
        $statusCode = match($result['status']) {
            'error' => $result['message'] === 'FAQ not found' ? 404 : 500,
            default => 200
        };
        
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    /**
     * Delete FAQ
     */
    public function deleteFaq($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        $result = $this->settingsService->deleteFaq($id);
        
        $statusCode = match($result['status']) {
            'error' => $result['message'] === 'FAQ not found' ? 404 : 500,
            default => 200
        };
        
        return $this->response->setJSON($result)->setStatusCode($statusCode);
    }

    // ==================== SITEMAP METHODS ====================

    /**
     * Generate sitemap
     */
    public function generateSitemap()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        try {
            $contentTypes = $this->request->getPost('content_types') ?? [];
            
            if (empty($contentTypes)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please select at least one content type'
                ])->setStatusCode(400);
            }

            $result = $this->sitemapService->generateSitemap($contentTypes);
            
            $statusCode = $result['status'] === 'success' ? 200 : 500;
            return $this->response->setJSON($result)->setStatusCode($statusCode);

        } catch (\Exception $e) {
            log_message('error', 'Sitemap generation failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to generate sitemap: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get sitemap status and statistics
     */
    public function getSitemapStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        try {
            $result = $this->sitemapService->getSitemapStatus();
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Failed to get sitemap status: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get sitemap status',
                'data' => []
            ])->setStatusCode(500);
        }
    }

    /**
     * Save sitemap configuration
     */
    public function saveSitemapConfig()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        try {
            $config = [
                'auto_generate' => $this->request->getPost('auto_generate') ? 1 : 0,
                'include_images' => $this->request->getPost('include_images') ? 1 : 0,
                'max_urls' => (int)($this->request->getPost('max_urls') ?? 50000),
                'priority_default' => (float)($this->request->getPost('priority_default') ?? 0.5),
                'changefreq_default' => $this->request->getPost('changefreq_default') ?? 'monthly',
                'exclude_patterns' => $this->request->getPost('exclude_patterns') ?? '',
                'content_types' => $this->request->getPost('content_types') ?? []
            ];

            $result = $this->sitemapService->saveSitemapConfig($config);
            
            $statusCode = $result['status'] === 'success' ? 200 : 500;
            return $this->response->setJSON($result)->setStatusCode($statusCode);

        } catch (\Exception $e) {
            log_message('error', 'Failed to save sitemap config: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save configuration: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get sitemap configuration
     */
    public function getSitemapConfig()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }

        try {
            $result = $this->sitemapService->getSitemapConfig();
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Failed to get sitemap config: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get configuration',
                'data' => []
            ])->setStatusCode(500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if request is valid AJAX request
     */
    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }
}
