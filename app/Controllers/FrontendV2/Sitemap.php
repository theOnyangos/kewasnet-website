<?php

namespace App\Controllers\FrontendV2;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SitemapModel;
use App\Services\SitemapService;

class Sitemap extends BaseController
{
    use ResponseTrait;
    
    protected $sitemapModel;
    protected $sitemapService;
    
    public function __construct()
    {
        $this->sitemapModel = new SitemapModel();
        $this->sitemapService = new SitemapService();
    }
    
    // Generate XML sitemap for search engines
    public function index()
    {
        $urls = $this->sitemapModel->getActiveUrlsForXml();
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
        
        foreach ($urls as $url) {
            $urlElement = $xml->addChild('url');
            $urlElement->addChild('loc', base_url($url->url));
            $urlElement->addChild('lastmod', date('Y-m-d', strtotime($url->last_modified)));
            $urlElement->addChild('changefreq', $url->changefreq);
            $urlElement->addChild('priority', $url->priority);
        }
        
        return $this->response->setContentType('application/xml')->setBody($xml->asXML());
    }
    
    // Display HTML sitemap for users
    public function view()
    {
        $title = "Sitemap - KEWASNET";
        $description = "Explore the sitemap of KEWASNET to find the information you need.";

        $categories = $this->sitemapModel->getActiveSitemapsByCategory();
        $statistics = $this->sitemapService->getStatistics();

        $data = [
            'title' => $title,
            'description' => $description,
            'categories' => $categories,
            'statistics' => $statistics
        ];

        return view('frontendV2/website/pages/policies/sitemap', $data);
    }
    
    /**
     * Generate sitemap data and store in database
     * This method should be called periodically via cron job
     */
    public function generate()
    {
        // Only allow access from command line or admin
        if (!is_cli() && !$this->isAdmin()) {
            return $this->failUnauthorized('Access denied');
        }
        
        $result = $this->sitemapService->generateSitemap();
        
        if (is_cli()) {
            echo "Sitemap Generation: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
            echo "Message: " . $result['message'] . "\n";
            if (isset($result['count'])) {
                echo "URLs processed: " . $result['count'] . "\n";
            }
        } else {
            if ($result['success']) {
                return $this->respond($result);
            } else {
                return $this->fail($result['message']);
            }
        }
    }
    
    /**
     * API endpoint to get sitemap statistics
     */
    public function statistics()
    {
        try {
            $stats = $this->sitemapService->getStatistics();
            return $this->respond([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return $this->fail('Failed to fetch statistics: ' . $e->getMessage());
        }
    }
    
    /**
     * API endpoint to get sitemap data for admin interface
     */
    public function api()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $limit = $this->request->getGet('limit') ?? 50;
            $category = $this->request->getGet('category');
            $search = $this->request->getGet('search');
            
            $builder = $this->sitemapModel->builder();
            
            if ($category) {
                $builder->where('category', $category);
            }
            
            if ($search) {
                $builder->groupStart()
                       ->like('url', $search)
                       ->orLike('title', $search)
                       ->orLike('description', $search)
                       ->groupEnd();
            }
            
            $total = $builder->countAllResults(false);
            $sitemaps = $builder->orderBy('category', 'ASC')
                              ->orderBy('priority', 'DESC')
                              ->limit($limit, ($page - 1) * $limit)
                              ->get()
                              ->getResult();
            
            return $this->respond([
                'success' => true,
                'data' => $sitemaps,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->fail('Failed to fetch sitemap data: ' . $e->getMessage());
        }
    }
    
    /**
     * Update sitemap entry
     */
    public function update($id = null)
    {
        if (!$this->isAdmin()) {
            return $this->failUnauthorized('Access denied');
        }
        
        if (!$id) {
            return $this->fail('Sitemap ID is required');
        }
        
        $data = $this->request->getJSON(true);
        
        try {
            $result = $this->sitemapModel->update($id, $data);
            
            if ($result) {
                return $this->respond([
                    'success' => true,
                    'message' => 'Sitemap entry updated successfully'
                ]);
            } else {
                return $this->fail('Failed to update sitemap entry');
            }
        } catch (\Exception $e) {
            return $this->fail('Error updating sitemap: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete sitemap entry
     */
    public function delete($id = null)
    {
        if (!$this->isAdmin()) {
            return $this->failUnauthorized('Access denied');
        }
        
        if (!$id) {
            return $this->fail('Sitemap ID is required');
        }
        
        try {
            $result = $this->sitemapModel->delete($id);
            
            if ($result) {
                return $this->respond([
                    'success' => true,
                    'message' => 'Sitemap entry deleted successfully'
                ]);
            } else {
                return $this->fail('Failed to delete sitemap entry');
            }
        } catch (\Exception $e) {
            return $this->fail('Error deleting sitemap: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if user is admin (simplified - implement proper admin check)
     */
    private function isAdmin()
    {
        // Implement your admin authentication logic here
        // This is a simplified version
        $session = session();
        return $session->get('user_role') === 'admin' || $session->get('is_admin') === true;
    }
}
