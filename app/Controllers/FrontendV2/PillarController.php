<?php

namespace App\Controllers\FrontendV2;

use App\Models\Pillar;
use App\Libraries\ClientAuth;
use App\Controllers\BaseController;
use App\Services\PillarArticlesService;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\ResourceCommentService;

class PillarController extends BaseController
{
    protected $pillarModel;
    protected $commentService;
    protected $pillarArticlesService;

    /**
     * Constructor to initialize models and services
     */
    public function __construct()
    {
        $this->pillarModel           = new Pillar();
        $this->commentService        = new ResourceCommentService();
        $this->pillarArticlesService = new PillarArticlesService();
    }

    /**
     * Display the main pillars landing page
     */
    public function index()
    {
        $title       = "KEWASNET - Pillars of Water and Sanitation";
        $description = "Explore KEWASNET's pillars of water and sanitation, including governance, service delivery, and community engagement. Learn how we promote sustainable practices and improve access to clean water and sanitation across Kenya.";

        // Get Pillars
        $pillars = $this->pillarModel->findAll();

        $data = [
            'title'       => $title,
            'pillars'     => $pillars,
            'description' => $description,
            'activeView'  => 'all'
        ];

        return view('frontendV2/ksp/pages/pillars/landing/index', $data);
    }

    /**
     * Display individual pillar view
     */
    public function pillarView($slug)
    {
        // Get all pillars for navigation
        $pillars = $this->pillarModel->findAll();
        
        // Find the specific pillar by slug
        $activePillar = null;
        foreach ($pillars as $pillar) {
            if ($pillar['slug'] === $slug) {
                $activePillar = $pillar;
                break;
            }
        }

        // If pillar not found, redirect to main pillars page
        if (!$activePillar) {
            return redirect()->to(base_url('ksp/pillars'));
        }

        $title = "KEWASNET - " . $activePillar['title'] . " Pillar";
        $description = $activePillar['description'] ?? "Explore the " . $activePillar['title'] . " pillar of KEWASNET's water and sanitation work.";

        $data = [
            'title'        => $title,
            'pillars'      => $pillars,
            'description'  => $description,
            'activeView'   => $slug,
            'activePillar' => $activePillar
        ];

        return view('frontendV2/ksp/pages/pillars/landing/index', $data);
    }

    /**
     * Display the main articles landing page with all pillars
     */
    public function articles()
    {
        $title = "KEWASNET - Pillar Articles";
        $description = "Explore comprehensive resources, articles, and publications across all pillars of water and sanitation from KEWASNET's knowledge repository.";

        // Get all pillars for navigation
        $pillars = $this->pillarModel->findAll();

        $data = [
            'title' => $title,
            'description' => $description,
            'pillars' => $pillars
        ];

        return view('frontendV2/ksp/pages/pillars/articles/landing', $data);
    }

    /**
     * Display articles for a specific pillar with pagination and filters
     */
    public function pillarArticles($slug)
    {
        try {
            // Get pillar by slug
            $pillar = $this->pillarArticlesService->getPillarBySlug($slug);
            
            if (!$pillar) {
                throw new \Exception('Pillar not found');
            }
            
            // Get request parameters
            $page          = max(1, (int)$this->request->getGet('page') ?: 1);
            $search        = $this->request->getGet('search') ?: '';
            $category      = $this->request->getGet('category') ?: '';
            $documentType  = $this->request->getGet('document_type') ?: '';
            $sort          = $this->request->getGet('sort') ?: 'newest';
            
            // Validate pagination parameters
            $page = max(1, $page);
            $perPage = 6;
            
            // Get categories for this pillar
            $categories = $this->pillarArticlesService->getCategoriesForPillar($pillar['id']);
            
            // Get pillar statistics
            $statistics = $this->pillarArticlesService->getPillarStatistics($pillar['id']);
            
            // Get resources with pagination and filters
            $resources = $this->pillarArticlesService->getResourcesForPillar(
                $pillar['id'],
                [
                    'search'        => $search,
                    'category'      => $category,
                    'document_type' => $documentType,
                    'sort'          => $sort
                ],
                $page,
                $perPage
            );
            
            // Document types (static for now)
            $documentTypes = [
                ['value' => 'pdf', 'label' => 'PDF'],
                ['value' => 'doc', 'label' => 'Word Document'],
                ['value' => 'xls', 'label' => 'Excel'],
                ['value' => 'ppt', 'label' => 'PowerPoint']
            ];
            
            $data = [
                'title'             => "KEWASNET - {$pillar['title']} Articles",
                'description'       => $pillar['meta_description'] ?? "Explore resources and articles for {$pillar['title']} pillar",
                'pillar'            => $pillar,
                'resources'         => $resources['data'],
                'categories'        => $categories,
                'documentTypes'     => $documentTypes,
                'totalResources'    => $statistics['totalResources'],
                'totalCategories'   => $statistics['totalCategories'],
                'totalContributors' => $statistics['totalContributors'],
                'currentPage'       => $resources['pagination']['current_page'],
                'totalPages'        => $resources['pagination']['last_page'],
                'perPage'           => $resources['pagination']['per_page'],
                'totalItems'        => $resources['pagination']['total'],
                'filters' => [
                    'category' => $category,
                    'document_type' => $documentType,
                    'search' => $search,
                    'sort' => $sort
                ]
            ];

            // Now try with real service data (converted to arrays)
            return view('frontendV2/ksp/pages/pillars/articles/index_clean', $data);
            
        } catch (\Exception $e) {
            // Return minimal safe data on error
            $data = $this->pillarArticlesService->getDefaultViewData($slug);
            return view('frontendV2/ksp/pages/pillars/articles/index_clean', $data);
        }
    }

    /**
     * Display details for a specific pillar article
     */
    public function pillarArticleDetails($article_slug)
    {
        try {
            // Set reasonable time and memory limits
            set_time_limit(30);
            ini_set('memory_limit', '256M');
            
            // Get resource by slug
            $resource = $this->pillarArticlesService->getResourceBySlug($article_slug);
            
            if (!$resource) {
                throw new \Exception('Article not found');
            }
            
            // Check if user can access this resource
            if (!$this->commentService->canAccessResource($resource)) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Resource not found or access denied');
            }

            // Get the pillar information for this resource
            $pillar = $this->pillarArticlesService->getPillarBySlug($resource['pillar_slug'] ?? '');
            
            // If we don't have pillar slug in resource, get pillar by ID
            if (!$pillar && !empty($resource['pillar_id'])) {
                $pillar = $this->pillarModel->find($resource['pillar_id']);
                if (is_object($pillar)) {
                    // Handle both Entity objects and stdClass objects
                    if (method_exists($pillar, 'toArray')) {
                        $pillar = $pillar->toArray();
                    } else {
                        // Convert stdClass to array
                        $pillar = (array) $pillar;
                    }
                }
            }
            
            // Get related resources from the same category (excluding current article)
            $relatedResources = [];
            if (!empty($resource['category_id'])) {
                try {
                    $relatedResult = $this->pillarArticlesService->getResourcesForPillar(
                        $resource['pillar_id'],
                        ['category' => $resource['category_id']],
                        1,
                        4 // Limit to 4 related articles
                    );
                    
                    if (isset($relatedResult['data']) && is_array($relatedResult['data'])) {
                        // Remove current article from related resources
                        $relatedResources = array_filter($relatedResult['data'], function($item) use ($resource) {
                            return isset($item['id']) && $item['id'] !== $resource['id'];
                        });
                        
                        // Re-index array and limit to 3
                        $relatedResources = array_slice(array_values($relatedResources), 0, 3);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error fetching related resources: ' . $e->getMessage());
                    $relatedResources = [];
                }
            }
            
            // Get contributors for this resource
            $contributors = $this->pillarArticlesService->getContributorsForResource($resource['id']);
            
            // Get comments for this resource
            $comments = $this->commentService->getCommentsForResource($resource['id'], 1, 20);
            $commentCount = $this->commentService->getCommentCount($resource['id']);
            
            // Get helpful vote status for this resource
            $helpfulStatus = $this->commentService->getResourceHelpfulStatus($resource['id']);

            // Increment view count
            $this->pillarArticlesService->incrementViewCount($resource['id']);
            
            $data = [
                'title'             => "KEWASNET - {$resource['title']}",
                'description'       => $resource['meta_description'] ?? substr(strip_tags($resource['description']), 0, 160),
                'resource'          => $resource,
                'pillar'            => $pillar,
                'contributors'      => $contributors,
                'relatedResources'  => $relatedResources,
                'comments'          => $comments,
                'commentCount'      => $commentCount,
                'helpfulStatus'     => $helpfulStatus,
                'isLoggedIn'        => ClientAuth::isLoggedIn(),
                'currentUser'       => ClientAuth::user(),
                'breadcrumb' => [
                    'pillar_title'  => $pillar['title'] ?? $pillar['name'] ?? 'Unknown Pillar',
                    'pillar_slug'   => $pillar['slug'] ?? '',
                    'article_title' => $resource['title']
                ]
            ];
            
            // log_message('info', 'Article details data: ' . json_encode($data, JSON_PRETTY_PRINT));
            
            return view('frontendV2/ksp/pages/pillars/articles/details', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error fetching article details: ' . $e->getMessage());
            
            // Return 404 or error page
            $data = [
                'title'       => "KEWASNET - Article Not Found",
                'description' => "The requested article could not be found.",
                'error'       => 'Article not found'
            ];
            
            return view('frontendV2/ksp/pages/pillars/articles/details', $data);
        }
    }

    /**
     * Handle comment submission via AJAX
     */
    public function addComment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $resourceId = $this->request->getPost('resource_id');
        $content    = $this->request->getPost('content');
        $parentId   = $this->request->getPost('parent_id');

        if (empty($resourceId) || empty($content)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required fields.'
            ]);
        }

        $result = $this->commentService->addComment($resourceId, $content, $parentId);
        
        return $this->response->setJSON($result);
    }

    /**
     * Handle voting on resource as helpful
     */
    public function voteResourceHelpful()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $resourceId = $this->request->getPost('resource_id');

        if (empty($resourceId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Resource ID is required.'
            ]);
        }

        $result = $this->commentService->voteResourceHelpful($resourceId);
        
        return $this->response->setJSON($result);
    }

    /**
     * Handle voting on comment as helpful
     */
    public function voteCommentHelpful()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $commentId = $this->request->getPost('comment_id');

        if (empty($commentId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Comment ID is required.'
            ]);
        }

        $result = $this->commentService->voteCommentHelpful($commentId);
        
        return $this->response->setJSON($result);
    }
}
