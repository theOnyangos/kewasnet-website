<?php

namespace App\Controllers\FrontendV2;

use App\Models\Pillar;
use App\Models\Resource;
use App\Libraries\ClientAuth;
use App\Controllers\BaseController;
use App\Services\PillarArticlesService;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\ResourceCommentService;

class PillarController extends BaseController
{
    protected $pillarModel;
    protected $resourceModel;
    protected $commentService;
    protected $pillarArticlesService;

    /**
     * Constructor to initialize models and services
     */
    public function __construct()
    {
        $this->pillarModel           = new Pillar();
        $this->resourceModel         = new Resource();
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

        // Get all pillars
        $allPillars = $this->pillarModel->findAll();
        
        // Filter pillars based on privacy and login status
        $isLoggedIn = ClientAuth::isLoggedIn();
        $pillars = [];
        
        foreach ($allPillars as $pillar) {
            // Convert to array if object
            if (is_object($pillar)) {
                $pillar = (array) $pillar;
            }
            
            // If pillar is private and user is not logged in, skip it
            if (isset($pillar['is_private']) && $pillar['is_private'] == 1 && !$isLoggedIn) {
                continue;
            }
            
            $pillars[] = $pillar;
        }

        $data = [
            'title'       => $title,
            'pillars'     => $pillars,
            'description' => $description,
            'activeView'  => 'all',
            'isLoggedIn'  => $isLoggedIn
        ];

        return view('frontendV2/ksp/pages/pillars/landing/index', $data);
    }

    /**
     * Display individual pillar view
     */
    public function pillarView($slug)
    {
        $isLoggedIn = ClientAuth::isLoggedIn();
        
        // Get all pillars for navigation (filtered by privacy)
        $allPillars = $this->pillarModel->findAll();
        $pillars = [];
        
        foreach ($allPillars as $pillar) {
            // Convert to array if object
            if (is_object($pillar)) {
                $pillar = (array) $pillar;
            }
            
            // If pillar is private and user is not logged in, skip it
            if (isset($pillar['is_private']) && $pillar['is_private'] == 1 && !$isLoggedIn) {
                continue;
            }
            
            $pillars[] = $pillar;
        }
        
        // Find the specific pillar by slug (check all pillars, not just filtered)
        $activePillar = null;
        foreach ($allPillars as $pillar) {
            // Convert to array if object
            if (is_object($pillar)) {
                $pillar = (array) $pillar;
            }
            
            if ($pillar['slug'] === $slug) {
                $activePillar = $pillar;
                break;
            }
        }

        // If pillar not found, redirect to main pillars page
        if (!$activePillar) {
            return redirect()->to(base_url('ksp/pillars'));
        }

        // Check if pillar is private and user is not logged in
        if (isset($activePillar['is_private']) && $activePillar['is_private'] == 1 && !$isLoggedIn) {
            // Store the current URL for redirect after login
            session()->set('redirect_url', current_url());
            
            // Redirect to login with a message
            return redirect()->to(base_url('ksp/login'))
                ->with('error', 'Please log in to access this private pillar.');
        }

        $title = "KEWASNET - " . $activePillar['title'] . " Pillar";
        $description = $activePillar['description'] ?? "Explore the " . $activePillar['title'] . " pillar of KEWASNET's water and sanitation work.";

        $data = [
            'title'        => $title,
            'pillars'      => $pillars,
            'description'  => $description,
            'activeView'   => $slug,
            'activePillar' => $activePillar,
            'isLoggedIn'   => $isLoggedIn
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

        // Get all pillars and filter by privacy
        $isLoggedIn = ClientAuth::isLoggedIn();
        $allPillars = $this->pillarModel->findAll();
        $pillars = [];
        
        foreach ($allPillars as $pillar) {
            // Convert to array if object
            if (is_object($pillar)) {
                $pillar = (array) $pillar;
            }
            
            // If pillar is private and user is not logged in, skip it
            if (isset($pillar['is_private']) && $pillar['is_private'] == 1 && !$isLoggedIn) {
                continue;
            }
            
            $pillars[] = $pillar;
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'pillars' => $pillars,
            'isLoggedIn' => $isLoggedIn
        ];

        return view('frontendV2/ksp/pages/pillars/articles/landing', $data);
    }

    /**
     * Display articles for a specific pillar with pagination and filters
     */
    public function pillarArticles($slug)
    {
        try {
            $isLoggedIn = ClientAuth::isLoggedIn();
            
            // Get pillar by slug
            $pillar = $this->pillarArticlesService->getPillarBySlug($slug);
            
            if (!$pillar) {
                throw new \Exception('Pillar not found');
            }
            
            // Convert to array if object
            if (is_object($pillar)) {
                $pillar = (array) $pillar;
            }
            
            // Check if pillar is private and user is not logged in
            if (isset($pillar['is_private']) && $pillar['is_private'] == 1 && !$isLoggedIn) {
                // Store the current URL for redirect after login
                session()->set('redirect_url', current_url());
                
                // Redirect to login with a message
                return redirect()->to(base_url('ksp/login'))
                    ->with('error', 'Please log in to access articles for this private pillar.');
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
            
            // Log for debugging
            log_message('debug', 'Categories fetched for pillar ' . $pillar['id'] . ': ' . count($categories));
            
            // Get document types dynamically
            $documentTypes = $this->pillarArticlesService->getDocumentTypes();
            
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
            
            // Check bookmark status for each resource (if user is logged in)
            $bookmarkedResources = [];
            if ($isLoggedIn && !empty($resources['data'])) {
                $userId = session()->get('id') ?? session()->get('user_id');
                if ($userId) {
                    $userBookmarkModel = new \App\Models\UserBookmark();
                    foreach ($resources['data'] as $resource) {
                        $resourceId = is_array($resource) ? $resource['id'] : $resource->id;
                        $bookmarkedResources[$resourceId] = $userBookmarkModel->isBookmarked($userId, $resourceId);
                    }
                }
            }
            
            $data = [
                'title'             => "KEWASNET - {$pillar['title']} Articles",
                'description'       => $pillar['meta_description'] ?? "Explore resources and articles for {$pillar['title']} pillar",
                'pillar'            => $pillar,
                'resources'         => $resources['data'] ?? [],
                'bookmarkedResources' => $bookmarkedResources,
                'isLoggedIn'        => $isLoggedIn,
                'categories'        => $categories ?? [],
                'documentTypes'     => $documentTypes ?? [],
                'totalResources'    => $statistics['totalResources'] ?? 0,
                'totalCategories'   => $statistics['totalCategories'] ?? 0,
                'totalContributors' => $statistics['totalContributors'] ?? 0,
                'currentPage'       => $resources['pagination']['current_page'] ?? 1,
                'totalPages'        => $resources['pagination']['last_page'] ?? 1,
                'perPage'           => $resources['pagination']['per_page'] ?? $perPage,
                'totalItems'        => $resources['pagination']['total'] ?? 0,
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
                $pillar = $this->pillarModel->where('id', $resource['pillar_id'])->first();
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
            
            // Convert to array if still object
            if (is_object($pillar)) {
                $pillar = (array) $pillar;
            }
            
            // Check if pillar is private and user is not logged in
            $isLoggedIn = ClientAuth::isLoggedIn();
            if (isset($pillar['is_private']) && $pillar['is_private'] == 1 && !$isLoggedIn) {
                // Store the current URL for redirect after login
                session()->set('redirect_url', current_url());
                
                // Redirect to login with a message
                return redirect()->to(base_url('ksp/login'))
                    ->with('error', 'Please log in to access this article from a private pillar.');
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

            // Check if resource is bookmarked (if user is logged in)
            $isBookmarked = false;
            if ($isLoggedIn) {
                $userId = session()->get('id') ?? session()->get('user_id');
                if ($userId) {
                    $userBookmarkModel = new \App\Models\UserBookmark();
                    $isBookmarked = $userBookmarkModel->isBookmarked($userId, $resource['id']);
                }
            }

            // Track view using session-based tracking (similar to news-details)
            $userId = session()->get('id') ?? session()->get('user_id');
            
            // Check if this user/session has already viewed this resource
            $viewedResources = session()->get('viewed_resources') ?? [];
            
            if (!in_array($resource['id'], $viewedResources)) {
                // Mark as viewed in session
                $viewedResources[] = $resource['id'];
                session()->set('viewed_resources', $viewedResources);
                
                // Increment the resource view count
                $this->pillarArticlesService->incrementViewCount($resource['id']);
                
                log_message('info', "Resource {$resource['id']} view incremented for " . ($userId ?? 'guest'));
            } else {
                log_message('info', "Resource {$resource['id']} already viewed in this session");
            }
            
            // Check if article is draft and user is not logged in
            $isLoggedIn = ClientAuth::isLoggedIn();
            if (isset($resource['is_published']) && $resource['is_published'] == 0 && !$isLoggedIn) {
                // Store the current URL for redirect after login
                session()->set('redirect_url', current_url());
                
                // Redirect to login with a message
                return redirect()->to(base_url('ksp/login'))
                    ->with('error', 'This article is currently in draft mode. Please log in to view it.');
            }
            
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
                'isBookmarked'      => $isBookmarked,
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
        
        // Send notifications after successful comment
        if (isset($result['success']) && $result['success'] === true) {
            try {
                $resource = $this->resourceModel->find($resourceId);
                if ($resource) {
                    $notificationService = new \App\Services\NotificationService();
                    $userModel = model('UserModel');
                    $userId = session()->get('id') ?? session()->get('user_id');
                    $commenterUser = $userModel->find($userId);
                    $commenterName = $commenterUser ? ($commenterUser['first_name'] . ' ' . $commenterUser['last_name']) : 'Someone';

                    $articleTitle = $resource['title'] ?? 'Article';
                    $articleSlug = $resource['slug'] ?? '';

                    // Notify resource author if it's not the user commenting on their own article
                    if ($resource['created_by'] != $userId) {
                        $notificationService->notifyResourceComment($resource['created_by'], $articleTitle, $commenterName, $articleSlug);
                    }

                    // If this is a reply to a comment, notify the parent comment author
                    if (!empty($parentId)) {
                        $commentModel = model('ResourceComment');
                        $parentComment = $commentModel->find($parentId);
                        if ($parentComment && $parentComment['user_id'] != $userId && $parentComment['user_id'] != $resource['created_by']) {
                            // Only notify if it's not the resource author and not the commenter themselves
                            $notificationService->notifyResourceCommentReply($parentComment['user_id'], $commenterName, $articleSlug);
                        }
                    }
                }
            } catch (\Exception $notificationError) {
                log_message('error', "Error sending resource comment notifications: " . $notificationError->getMessage());
                // Don't fail comment if notification fails
            }
        }
        
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
        
        // Send notification after successful vote
        if (isset($result['success']) && $result['success'] === true) {
            try {
                $resource = $this->resourceModel->find($resourceId);
                if ($resource) {
                    $userId = session()->get('id') ?? session()->get('user_id');
                    
                    // Notify resource author if it's not the user voting on their own article
                    if ($resource['created_by'] != $userId) {
                        $notificationService = new \App\Services\NotificationService();
                        $userModel = model('UserModel');
                        $voterUser = $userModel->find($userId);
                        $voterName = $voterUser ? ($voterUser['first_name'] . ' ' . $voterUser['last_name']) : 'Someone';

                        $articleTitle = $resource['title'] ?? 'Article';
                        $articleSlug = $resource['slug'] ?? '';

                        $notificationService->notifyResourceHelpful($resource['created_by'], $articleTitle, $voterName, $articleSlug);
                    }
                }
            } catch (\Exception $notificationError) {
                log_message('error', "Error sending resource helpful notification: " . $notificationError->getMessage());
                // Don't fail vote if notification fails
            }
        }
        
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

    /**
     * Download resource attachment
     * Similar to FilesController::downloadAttachment but with resource-specific logic
     */
    public function downloadAttachment()
    {
        // Get the entire URI path after the route (exactly like FilesController)
        $uri = service('uri');
        $segmentCount = $uri->getTotalSegments();
        $pathParts = [];
        
        // Start from segment 4 (ksp = 1, attachments = 2, download = 3, path starts at 4)
        for ($i = 4; $i <= $segmentCount; $i++) {
            $pathParts[] = $uri->getSegment($i);
        }
        
        $filePath = implode('/', $pathParts);
        
        log_message('debug', 'PillarController downloadAttachment - File path: ' . $filePath);
        
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        log_message('debug', 'PillarController downloadAttachment - Full file path: ' . $fullPath);

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        // Find attachment record to get original name (exactly like FilesController)
        $fileAttachmentModel = new \App\Models\FileAttachment();
        $attachment = $fileAttachmentModel->where('file_path', $filePath)->first();
        
        // Convert to array if object (FileAttachment returns arrays)
        if ($attachment && is_object($attachment)) {
            $attachment = (array) $attachment;
        }
        
        $originalName = $attachment ? ($attachment['original_name'] ?? basename($filePath)) : basename($filePath);
        
        // If it's a resource attachment, handle additional logic
        if ($attachment && isset($attachment['attachable_type']) && $attachment['attachable_type'] === 'resources') {
            // Get the resource
            $resource = $this->resourceModel->find($attachment['attachable_id']);
            
            // Convert to array if object
            if ($resource && is_object($resource)) {
                $resource = (array) $resource;
            }
            
            if ($resource) {
                // Check if resource is published (or draft and user is logged in)
                $isLoggedIn = ClientAuth::isLoggedIn();
                if (isset($resource['is_published']) && $resource['is_published'] == 0 && !$isLoggedIn) {
                    return redirect()->to(base_url('ksp/login'))
                        ->with('error', 'Please log in to download this resource.');
                }
                
                // Increment resource download count
                $this->pillarArticlesService->incrementDownloadCount($resource['id']);
            }
        } else if ($attachment && isset($attachment['attachable_type']) && $attachment['attachable_type'] !== 'resources') {
            // For non-resource attachments (discussions, replies), pass to DiscussionController
            log_message('debug', 'Passing non-resource attachment to DiscussionController');
            $discussionController = new \App\Controllers\FrontendV2\DiscussionController();
            return $discussionController->downloadAttachment();
        }

        // Increment download count (like FilesController)
        if ($attachment) {
            $fileAttachmentModel->incrementDownloadCount($attachment['id']);
        }

        return $this->response->download($fullPath, null)->setFileName($originalName);
    }

    /**
     * View resource attachment (for previewing documents)
     * Similar to FilesController::viewAttachment but with resource-specific logic
     */
    public function viewAttachment()
    {
        // Get the entire URI path after the route (exactly like FilesController)
        $uri = service('uri');
        $segmentCount = $uri->getTotalSegments();
        $pathParts = [];
        
        // Start from segment 4 (ksp = 1, attachments = 2, view = 3, path starts at 4)
        for ($i = 4; $i <= $segmentCount; $i++) {
            $pathParts[] = $uri->getSegment($i);
        }
        
        $filePath = implode('/', $pathParts);
        
        log_message('debug', 'PillarController viewAttachment - File path: ' . $filePath);
        
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        log_message('debug', 'PillarController viewAttachment - Full file path: ' . $fullPath);

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        // Find attachment record
        $fileAttachmentModel = new \App\Models\FileAttachment();
        $attachment = $fileAttachmentModel->where('file_path', $filePath)->first();
        
        // Convert to array if object (FileAttachment returns arrays)
        if ($attachment && is_object($attachment)) {
            $attachment = (array) $attachment;
        }
        
        // If it's a resource attachment, check access
        if ($attachment && isset($attachment['attachable_type']) && $attachment['attachable_type'] === 'resources') {
            // Get the resource
            $resource = $this->resourceModel->find($attachment['attachable_id']);
            
            // Convert to array if object
            if ($resource && is_object($resource)) {
                $resource = (array) $resource;
            }
            
            if ($resource) {
                // Check if resource is published (or draft and user is logged in)
                $isLoggedIn = ClientAuth::isLoggedIn();
                if (isset($resource['is_published']) && $resource['is_published'] == 0 && !$isLoggedIn) {
                    return redirect()->to(base_url('ksp/login'))
                        ->with('error', 'Please log in to view this resource.');
                }
            }
        } else if ($attachment && isset($attachment['attachable_type']) && $attachment['attachable_type'] !== 'resources') {
            // For non-resource attachments (discussions, replies), pass to DiscussionController
            log_message('debug', 'Passing non-resource attachment to DiscussionController');
            $discussionController = new \App\Controllers\FrontendV2\DiscussionController();
            return $discussionController->viewAttachment();
        }

        // Get file mime type and return (exactly like FilesController)
        $file = new \CodeIgniter\Files\File($fullPath);
        $mime = $file->getMimeType();

        return $this->response->setContentType($mime)
                            ->setBody(file_get_contents($fullPath));
    }

    /**
     * Toggle bookmark for a resource article
     */
    public function toggleBookmark()
    {
        log_message('debug', 'toggleBookmark called. Is AJAX: ' . ($this->request->isAJAX() ? 'yes' : 'no'));
        log_message('debug', 'Request method: ' . $this->request->getMethod());
        log_message('debug', 'Request URI: ' . $this->request->getUri()->getPath());
        
        if (!$this->request->isAJAX()) {
            log_message('warning', 'toggleBookmark called but not AJAX request');
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Check if user is logged in
        $userId = session()->get('id') ?? session()->get('user_id');
        log_message('debug', 'User ID from session: ' . ($userId ?? 'null'));
        
        if (!$userId) {
            log_message('warning', 'toggleBookmark called but user not logged in');
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Please log in to bookmark articles',
                'redirect' => base_url('ksp/login')
            ]);
        }

        $resourceId = $this->request->getPost('resource_id');
        log_message('debug', 'Resource ID from POST: ' . ($resourceId ?? 'null'));
        
        if (!$resourceId) {
            log_message('error', 'toggleBookmark called without resource_id');
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Resource ID is required'
            ]);
        }

        $userBookmarkModel = new \App\Models\UserBookmark();
        
        // Check if already bookmarked
        $isBookmarked = $userBookmarkModel->isBookmarked($userId, $resourceId);

        if ($isBookmarked) {
            // Remove bookmark
            $deleted = $userBookmarkModel->removeBookmark($userId, $resourceId);
            if ($deleted) {
                log_message('info', "Bookmark removed: User {$userId}, Resource {$resourceId}");
                return $this->response->setJSON([
                    'success' => true,
                    'bookmarked' => false,
                    'message' => 'Bookmark removed'
                ]);
            } else {
                log_message('error', "Failed to remove bookmark: User {$userId}, Resource {$resourceId}");
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to remove bookmark'
                ]);
            }
        } else {
            // Add bookmark
            $insertId = $userBookmarkModel->addBookmark($userId, $resourceId);
            if ($insertId) {
                log_message('info', "Bookmark added: User {$userId}, Resource {$resourceId}, Insert ID: {$insertId}");
                return $this->response->setJSON([
                    'success' => true,
                    'bookmarked' => true,
                    'message' => 'Article bookmarked'
                ]);
            } else {
                // Check for validation errors
                $errors = $userBookmarkModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Failed to bookmark article';
                log_message('error', "Failed to add bookmark: User {$userId}, Resource {$resourceId}. Errors: " . json_encode($errors));
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $errors
                ]);
            }
        }
    }

    /**
     * Publish article (remove from draft)
     */
    public function publishArticle()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Check if user is logged in
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Please log in to publish articles',
                'redirect' => base_url('ksp/login')
            ]);
        }

        $resourceId = $this->request->getPost('resource_id');
        
        if (!$resourceId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Resource ID is required'
            ]);
        }

        // Get the resource
        $resource = $this->resourceModel->find($resourceId);
        
        if (!$resource) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Article not found'
            ]);
        }

        // Convert to array if object
        if (is_object($resource)) {
            $resource = (array) $resource;
        }

        // Check if user is the author
        if (isset($resource['created_by']) && $resource['created_by'] != $userId) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You do not have permission to publish this article'
            ]);
        }

        // Check if already published
        if (isset($resource['is_published']) && $resource['is_published'] == 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Article is already published'
            ]);
        }

        // Publish the article
        $updated = $this->resourceModel->update($resourceId, ['is_published' => 1]);
        
        if ($updated) {
            log_message('info', "Article published: User {$userId}, Resource {$resourceId}");
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Article published successfully'
            ]);
        } else {
            log_message('error', "Failed to publish article: User {$userId}, Resource {$resourceId}");
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to publish article'
            ]);
        }
    }
}
