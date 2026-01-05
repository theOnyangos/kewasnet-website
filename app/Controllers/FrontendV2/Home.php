<?php

namespace App\Controllers\FrontendV2;

use App\Models\BlogPost;
use App\Services\HomeService;    
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PartnerModel as Partner;
use App\Models\DocumentResource;
use App\Models\EventModel;

class Home extends BaseController
{
    protected $partnerModel;
    protected $blogPostModel;
    protected $homeService;
    protected $documentResourceModel;
    protected $eventModel;

    public function __construct()
    {
        $this->partnerModel  = new Partner();
        $this->blogPostModel = new BlogPost();
        $this->homeService   = new HomeService();
        $this->documentResourceModel = new DocumentResource();
        $this->eventModel = new EventModel();
    }

    // Index method to load the landing page
    public function index()
    {
        $partners   = $this->partnerModel->getAllPartners();
        $blogPosts  = $this->blogPostModel->getFeaturedBlogPosts(3);
        $stats      = $this->homeService->getKnowledgeHubStats();
        
        // Get recent published events (upcoming events, limit 3)
        $recentEvents = $this->eventModel
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->where('start_date >=', date('Y-m-d'))
            ->orderBy('start_date', 'ASC')
            ->limit(3)
            ->findAll();

        // Format blog post image URLs
        foreach ($blogPosts as $post) {
            if (!empty($post->featured_image)) {
                $imagePath = $post->featured_image;
                
                // Remove any leading/trailing whitespace
                $imagePath = trim($imagePath);
                
                // If it's already a full URL, extract just the path part
                if (strpos($imagePath, 'http') === 0) {
                    $parsedUrl = parse_url($imagePath);
                    if (isset($parsedUrl['path'])) {
                        $imagePath = ltrim($parsedUrl['path'], '/');
                    }
                }
                
                // Remove any leading slashes
                $imagePath = ltrim($imagePath, '/');
                
                // Always rebuild the URL with current base_url
                $post->featured_image = base_url($imagePath);
            } else {
                // Set default image if none exists
                $post->featured_image = base_url('assets/images/default-blog.jpg');
            }
        }
        
        // Format event image URLs
        foreach ($recentEvents as $event) {
            if (!empty($event['image_url'])) {
                $imagePath = $event['image_url'];
                $imagePath = trim($imagePath);
                
                if (strpos($imagePath, 'http') === 0) {
                    $parsedUrl = parse_url($imagePath);
                    if (isset($parsedUrl['path'])) {
                        $imagePath = ltrim($parsedUrl['path'], '/');
                    }
                }
                
                $imagePath = ltrim($imagePath, '/');
                $event['image_url'] = base_url($imagePath);
            } else {
                $event['image_url'] = base_url('assets/images/default-blog.jpg');
            }
        }

        $data = [
            'title'                 => 'KEWASNET - Kenya Water and Sanitation Network',
            'description'           => 'The Kenya Water and Sanitation Civil Society Network is the National Network of Water Civil Society Organizations in Kenya.',
            'newsletterTitle'       => 'Stay informed',
            'newsletterDescription' => 'Subscribe to our newsletter and never miss important updates from the WASH sector',
            'partners'              => $partners,
            'blogs'                 => $blogPosts,
            'events'                => $recentEvents,
            'stats'                 => $stats,
        ];

        return view('frontendV2/website/pages/landing/index', $data);
    }

    // Method to load the about us page
    public function aboutUs()
    {
        $title          = "About Us - KEWASNET";
        $description    = "Learn about KEWASNET, our mission, vision, and values.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/about/index', $data);
    }

    // Method to load the programs page
    public function programs()
    {
        $title          = "Programs - KEWASNET";
        $description    = "Explore our comprehensive programs focused on sustainable water, sanitation, and hygiene solutions across Kenya.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/programs/index', $data);
    }

    // Method to load the resources page
    public function resources()
    {
        $resource = [];
        $title          = "Resources - KEWASNET";
        $description    = "Access a wide range of resources including reports, publications, and tools to support water and sanitation initiatives.";

        // Load the correct models
        $documentResourceModel = model('App\Models\DocumentResource');
        $resourceCategoryModel = model('App\Models\DocumentResourceCategory');
        $fileAttachmentModel = model('App\Models\FileAttachment');
        
        // Get featured document resources with attachments
        $featuredResources = $documentResourceModel->getFeaturedResourcesWithAttachments(6);
        
        // Get document categories for filtering
        $documentCategories = $resourceCategoryModel->getActiveCategories();
        
        // Add resource count to each category
        foreach ($documentCategories as &$category) {
            $category['resource_count'] = $documentResourceModel
                ->where('category_id', $category['id'])
                ->where('is_published', 1)
                ->countAllResults();
        }

        // Get document attachments
        foreach ($featuredResources as &$resource) {
            $resource['attachments'] = $fileAttachmentModel
                ->where('attachable_id', $resource['id'])
                ->where('attachable_type', 'document_resources')
                ->orderBy('created_at', 'ASC')
                ->findAll();
        }
        
        // Get total published resource count
        $totalResources = $documentResourceModel->where('is_published', 1)->countAllResults();
        
        // Get recent resources count by month for stats
        $recentResourcesCount = $documentResourceModel
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->where('is_published', 1)
            ->countAllResults();

        $data = [
            'title'                 => $title,
            'resource'              => $resource,
            'description'           => $description,
            'featuredResources'     => $featuredResources,
            'documentCategories'    => $documentCategories,
            'totalResources'        => $totalResources,
            'recentResourcesCount'  => $recentResourcesCount,
            'newsletterTitle'       => 'Stay updated with our resources',
            'newsletterDescription' => 'Subscribe to our newsletter for the latest resources, reports, and publications from KEWASNET.',
        ];

        return view('frontendV2/website/pages/resources/index', $data);
    }

    // AJAX method to search resources (updated for new structure)
    public function searchResources()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $categoryId = $this->request->getGet('category_id');
        $search     = $this->request->getGet('search');
        $page       = (int)$this->request->getGet('page') ?: 1;
        $perPage    = 10;

        $documentResourceModel = model('App\Models\DocumentResource');
        
        // Build the query
        $builder = $documentResourceModel->builder()
            ->select('*')
            ->where('is_published', 1);

        // Apply filters
        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('title', $search)
                    ->orLike('description', $search)
                    ->groupEnd();
        }

        // Get total count for pagination
        $totalResources = $builder->countAllResults(false);

        // Get paginated results
        $resources = $builder->orderBy('created_at', 'DESC')
                            ->limit($perPage, ($page - 1) * $perPage)
                            ->get()
                            ->getResultArray();

        // Add file attachments to each resource
        $fileAttachmentModel = model('App\Models\FileAttachment');
        foreach ($resources as &$resource) {
            $resource['attachments'] = $fileAttachmentModel
                ->where('attachable_id', $resource['id'])
                ->where('attachable_type', 'document_resources')
                ->orderBy('created_at', 'ASC')
                ->findAll();
            
            // Calculate total downloads from all attachments
            $resource['total_downloads'] = array_sum(array_column($resource['attachments'], 'download_count'));
        }

        $totalPages = ceil($totalResources / $perPage);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'resources' => $resources,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total' => $totalResources,
                    'per_page' => $perPage,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ]
        ]);
    }

    // AJAX method to increment view count for document resource
    public function incrementViewCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $resourceId = $this->request->getPost('resource_id');
        
        if (!$resourceId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Resource ID is required']);
        }

        $success = $this->documentResourceModel->incrementViewCount($resourceId);

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'View count updated' : 'Failed to update view count'
        ]);
    }

    // AJAX method to get resources by category
    public function getResourcesByCategory()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $categoryId = $this->request->getGet('category_id');
        $documentType = $this->request->getGet('document_type');
        $search = $this->request->getGet('search');
        $page = (int)$this->request->getGet('page') ?: 1;
        $perPage = 10;

        $resourceModel = model('App\Models\ResourceModel');
        
        // Build search criteria
        $criteria = [];
        
        // Handle static category filtering
        if ($categoryId && in_array($categoryId, ['policy', 'training', 'research', 'guidelines', 'tools'])) {
            $criteria['category_type'] = $categoryId;
        }
        
        if ($documentType) {
            $criteria['document_type_id'] = $documentType;
        }

        // Get resources with pagination
        $resources = $resourceModel->getFilteredResourcesByType($criteria, $search, $perPage, $page);
        // Add relations to resources
        $resources = $resourceModel->getResourcesWithRelations($resources);
        $totalResources = $resourceModel->getPublishedResourcesCountByType($criteria, $search);
        
        $totalPages = ceil($totalResources / $perPage);

        return $this->response->setJSON([
            'success'     => true,
            'resources'   => $resources,
            'pagination'  => [
                'current_page'    => $page,
                'total_pages'     => $totalPages,
                'total_resources' => $totalResources,
                'per_page'        => $perPage
            ]
        ]);
    }

    // Method to load the opportunities page
    public function opportunities()
    {
        $title          = "Opportunities - KEWASNET";
        $description    = "Explore various opportunities to collaborate, partner, and contribute to our mission of improving water and sanitation in Kenya.";

        // Load the JobOpportunityModel
        $jobOpportunityModel = new \App\Models\JobOpportunityModel();
        
        // Fetch published job opportunities ordered by creation date (newest first)
        $opportunities = $jobOpportunityModel
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Group opportunities by type for better display
        $groupedOpportunities = [
            'full-time'  => [],
            'part-time'  => [],
            'contract'   => [],
            'internship' => [],
            'freelance'  => []
        ];

        foreach ($opportunities as $opportunity) {
            $type = $opportunity['opportunity_type'];
            if (isset($groupedOpportunities[$type])) {
                $groupedOpportunities[$type][] = $opportunity;
            }
        }

        $data = [
            'title'                 => $title,
            'description'           => $description,
            'newsletterTitle'       => 'Stay updated on new opportunities',
            'newsletterDescription' => 'Subscribe to our newsletter for the latest job postings, grants, and partnership opportunities.',
            'opportunities'         => $opportunities,
            'groupedOpportunities'  => $groupedOpportunities,
            'totalOpportunities'    => count($opportunities)
        ];

        return view('frontendV2/website/pages/opportunities/index', $data);
    }

    // Method to load the news page
    public function news()
    {
        // Load models
        $blogPostModel = new \App\Models\BlogPost();
        $blogCategoryModel = new \App\Models\BlogPostCategory();
        $blogTagModel = new \App\Models\BlogTag();
        
        $title                 = "News & Events - KEWASNET";
        $description           = "Stay updated with the latest news, events, and activities from KEWASNET and the broader water and sanitation sector in Kenya.";
        $newsletterTitle       = "Stay informed";
        $newsletterDescription = "Subscribe to our newsletter and never miss important updates from the WASH sector.";

        // Get blogs data with pagination
        $perPage = 12;
        $page = $this->request->getVar('page') ?? 1;
        
        // Get search and filter parameters
        $search = $this->request->getVar('search');
        $category = $this->request->getVar('category');
        $tag = $this->request->getVar('tag');
        $dateFilter = $this->request->getVar('date');
        
        // Build the query for blog posts
        $builder = $blogPostModel->builder()
            ->select('blog_posts.*, 
                     blog_categories.name as category_name,
                     blog_categories.slug as category_slug,
                     COALESCE(blog_posts.views, 0) as views')
            ->join('blog_categories', 'blog_categories.id = blog_posts.category_id', 'left')
            ->where('blog_posts.status', 'published')
            ->orderBy('blog_posts.published_at', 'DESC');
        
        // Apply search filter
        if ($search) {
            $builder->groupStart()
                    ->like('blog_posts.title', $search)
                    ->orLike('blog_posts.excerpt', $search)
                    ->orLike('blog_posts.content', $search)
                    ->groupEnd();
        }
        
        // Apply category filter
        if ($category) {
            $builder->where('blog_categories.slug', $category);
        }
        
        // Apply date filter
        if ($dateFilter) {
            switch ($dateFilter) {
                case 'last_7_days':
                    $builder->where('blog_posts.published_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
                    break;
                case 'last_30_days':
                    $builder->where('blog_posts.published_at >=', date('Y-m-d H:i:s', strtotime('-30 days')));
                    break;
                case 'last_6_months':
                    $builder->where('blog_posts.published_at >=', date('Y-m-d H:i:s', strtotime('-6 months')));
                    break;
                case 'last_year':
                    $builder->where('blog_posts.published_at >=', date('Y-m-d H:i:s', strtotime('-1 year')));
                    break;
            }
        }
        
        // Get total count for pagination
        $totalPosts = $builder->countAllResults(false);
        
        // Get paginated posts
        $blogPosts = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResult();

        // Format blog posts image paths
        foreach ($blogPosts as $post) {
            if (!empty($post->featured_image)) {
                $imagePath = $post->featured_image;
                if (strpos($imagePath, 'http') === 0) {
                    $parsedUrl = parse_url($imagePath);
                    if (isset($parsedUrl['path'])) {
                        $imagePath = ltrim($parsedUrl['path'], '/');
                    }
                }
                $post->featured_image = base_url($imagePath);
            }
        }

        // Get featured posts
        $featuredPosts = $blogPostModel->getFeaturedPosts(2);

        // Format featured posts image paths
        foreach ($featuredPosts as $post) {
            if (is_object($post) && !empty($post->featured_image)) {
                $imagePath = $post->featured_image;
                if (strpos($imagePath, 'http') === 0) {
                    $parsedUrl = parse_url($imagePath);
                    if (isset($parsedUrl['path'])) {
                        $imagePath = ltrim($parsedUrl['path'], '/');
                    }
                }
                $post->featured_image = base_url($imagePath);
            }
        }

        // Get categories for filter
        $categories = $blogCategoryModel->getActiveCategories();
        
        // Get popular tags for filter
        $popularTags = $blogTagModel->getPopularTags(8);
        
        // Calculate pagination
        $totalPages = ceil($totalPosts / $perPage);
        
        $data = [
            'title'                 => $title,
            'description'           => $description,
            'newsletterTitle'       => $newsletterTitle,
            'newsletterDescription' => $newsletterDescription,
            'blogPosts'            => $blogPosts,
            'featuredPosts'        => $featuredPosts,
            'categories'           => $categories,
            'popularTags'          => $popularTags,
            'currentPage'          => (int)$page,
            'totalPages'           => $totalPages,
            'totalPosts'           => $totalPosts,
            'perPage'              => $perPage,
            'search'               => $search,
            'selectedCategory'     => $category,
            'selectedTag'          => $tag,
            'selectedDate'         => $dateFilter
        ];

        return view('frontendV2/website/pages/news/index', $data);
    }

    // Method to load the news details page
    public function newsDetails($slug)
    {
        // Load models
        $blogPostModel = new \App\Models\BlogPost();
        $blogPostViewModel = new \App\Models\BlogPostView();
        $blogCategoryModel = new \App\Models\BlogPostCategory();
        $blogTagModel = new \App\Models\BlogTag();
        
        // Fetch the blog post by slug with related data
        $blogPost = $blogPostModel->builder()
            ->select('blog_posts.*, 
                     blog_categories.name as category_name,
                     blog_categories.slug as category_slug,
                     blog_categories.description as category_description,
                     system_users.first_name, system_users.last_name, system_users.email as author_email,
                     COALESCE(blog_posts.views, 0) as views')
            ->join('blog_categories', 'blog_categories.id = blog_posts.category_id', 'left')
            ->join('system_users', 'system_users.id = blog_posts.user_id', 'left')
            ->where('blog_posts.slug', $slug)
            ->where('blog_posts.status', 'published')
            ->get()
            ->getRow();
        
        // If blog post not found, show 404
        if (!$blogPost) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Blog post with slug '{$slug}' not found");
        }
        
        // Track view using session-based tracking (same as discussions)
        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Check if this user/session has already viewed this blog post
        $viewedBlogPosts = session()->get('viewed_blog_posts') ?? [];
        
        if (!in_array($blogPost->id, $viewedBlogPosts)) {
            // Mark as viewed in session
            $viewedBlogPosts[] = $blogPost->id;
            session()->set('viewed_blog_posts', $viewedBlogPosts);
            
            // Record the view in tracking table
            $ipAddress = $this->request->getIPAddress();
            $userAgent = $this->request->getUserAgent()->getAgentString();
            
            $blogPostViewModel->recordView(
                $blogPost->id,
                $ipAddress,
                $userAgent,
                $userId
            );
            
            // Increment the blog post view count
            $blogPostModel->incrementViews($blogPost->id);
            $blogPost->views = ($blogPost->views ?? 0) + 1;
            
            log_message('info', "Blog post {$blogPost->id} view incremented for " . ($userId ?? 'guest'));
        } else {
            log_message('info', "Blog post {$blogPost->id} already viewed in this session");
        }
        
        // Get related posts (same category, excluding current post)
        $relatedPosts = $blogPostModel->builder()
            ->select('blog_posts.*, blog_categories.name as category_name')
            ->join('blog_categories', 'blog_categories.id = blog_posts.category_id', 'left')
            ->where('blog_posts.category_id', $blogPost->category_id)
            ->where('blog_posts.id !=', $blogPost->id)
            ->where('blog_posts.status', 'published')
            ->orderBy('blog_posts.published_at', 'DESC')
            ->limit(3)
            ->get()
            ->getResult();

        // Format related posts image paths
        foreach ($relatedPosts as $post) {
            if (!empty($post->featured_image)) {
                $imagePath = $post->featured_image;
                if (strpos($imagePath, 'http') === 0) {
                    $parsedUrl = parse_url($imagePath);
                    if (isset($parsedUrl['path'])) {
                        $imagePath = ltrim($parsedUrl['path'], '/');
                    }
                }
                $post->featured_image = base_url($imagePath);
            }
        }

        // Get recent posts for sidebar
        $recentPosts = $blogPostModel->getRecentPosts(5);

        // Format recent posts image paths
        foreach ($recentPosts as $post) {
            if (is_object($post) && !empty($post->featured_image)) {
                $imagePath = $post->featured_image;
                if (strpos($imagePath, 'http') === 0) {
                    $parsedUrl = parse_url($imagePath);
                    if (isset($parsedUrl['path'])) {
                        $imagePath = ltrim($parsedUrl['path'], '/');
                    }
                }
                $post->featured_image = base_url($imagePath);
            }
        }
        
        // Get popular tags
        $popularTags = $blogTagModel->getPopularTags(10);
        
        // Get categories for sidebar
        $categories = $blogCategoryModel->getActiveCategories();
        
        // Prepare author name
        $authorName = trim(($blogPost->first_name ?? '') . ' ' . ($blogPost->last_name ?? ''));
        if (empty($authorName)) {
            $authorName = 'KEWASNET Team';
        }
        
        // Calculate reading time if not set
        if (empty($blogPost->reading_time)) {
            $wordCount = str_word_count(strip_tags($blogPost->content));
            $blogPost->reading_time = max(1, ceil($wordCount / 200)); // 200 words per minute
        }

        // Format featured image path
        if (!empty($blogPost->featured_image)) {
            $featuredImagePath = $blogPost->featured_image;

            // If it's already a full URL, extract just the path part
            if (strpos($featuredImagePath, 'http') === 0) {
                $parsedUrl = parse_url($featuredImagePath);
                if (isset($parsedUrl['path'])) {
                    $featuredImagePath = ltrim($parsedUrl['path'], '/');
                }
            }

            // Always rebuild the URL with current base_url
            $blogPost->featured_image = base_url($featuredImagePath);
        }

        // Get additional images from file_attachments table
        $fileAttachmentModel = new \App\Models\FileAttachment();
        $additionalImages = $fileAttachmentModel->where('attachable_type', 'blog_posts')
                                               ->where('attachable_id', $blogPost->id)
                                               ->orderBy('created_at', 'ASC')
                                               ->findAll();

        // Format additional image paths
        $formattedAdditionalImages = [];
        foreach ($additionalImages as $image) {
            $imageArray = is_object($image) ? json_decode(json_encode($image), true) : $image;

            if (!empty($imageArray['file_path'])) {
                $filePath = $imageArray['file_path'];

                // If it's already a full URL, extract just the path part
                if (strpos($filePath, 'http') === 0) {
                    $parsedUrl = parse_url($filePath);
                    if (isset($parsedUrl['path'])) {
                        $filePath = ltrim($parsedUrl['path'], '/');
                    }
                }

                // Always rebuild the URL with current base_url
                $imageArray['file_path'] = base_url($filePath);
                $formattedAdditionalImages[] = $imageArray;
            }
        }

        $additionalImages = $formattedAdditionalImages;
        
        $title = $blogPost->meta_title ?: $blogPost->title . ' - KEWASNET';
        $description = $blogPost->meta_description ?: $blogPost->excerpt ?: substr(strip_tags($blogPost->content), 0, 160);
        
        $data = [
            'title' => $title,
            'description' => $description,
            'keywords' => $blogPost->meta_keywords ?? '',
            'blogPost' => $blogPost,
            'authorName' => $authorName,
            'relatedPosts' => $relatedPosts,
            'recentPosts' => $recentPosts,
            'popularTags' => $popularTags,
            'categories' => $categories,
            'additionalImages' => $additionalImages,
            'newsletterTitle' => 'Stay updated with our latest insights',
            'newsletterDescription' => 'Subscribe to our newsletter for the latest news and insights from the WASH sector.',
        ];

        return view('frontendV2/website/pages/news/details', $data);
    }

    // Method to load the contact us page
    public function contactUs()
    {
        $title          = "Contact Us - KEWASNET";
        $description    = "Get in touch with KEWASNET for inquiries, feedback, or support regarding water and sanitation initiatives.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/contact-us/index', $data);
    }

    public function privacyAndPolicies()
    {
        $title          = "Privacy and Policies - KEWASNET";
        $description    = "Learn about KEWASNET's privacy policies and terms of use.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/policies/privacy_and_policies', $data);
    }

    public function googlePrivacy()
    {
        $title          = "Google Privacy Policy - KEWASNET";
        $description    = "Learn about KEWASNET's use of Google services and related privacy policies.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/policies/google_privacy', $data);
    }

    public function termsOfService()
    {
        $title          = "Terms of Service - KEWASNET";
        $description    = "Learn about KEWASNET's terms of service and user agreements.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/policies/terms_of_service', $data);
    }

    public function cookies()
    {
        $title          = "Cookies Policy - KEWASNET";
        $description    = "Learn about KEWASNET's use of cookies and tracking technologies.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/policies/cookies_policies', $data);
    }

    public function sitemap()
    {
        $title          = "Sitemap - KEWASNET";
        $description    = "Explore the sitemap of KEWASNET to find the information you need.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/policies/sitemap', $data);
    }

    public function policyBriefs()
    {
        $title          = "Policy Briefs - KEWASNET";
        $description    = "Explore KEWASNET's policy briefs for insights and recommendations.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/policy_briefs/index', $data);
    }

    public function bestPractices()
    {
        $title          = "Best Practices - KEWASNET";
        $description    = "Explore KEWASNET's best practices for effective water and sanitation solutions.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/best_practices/index', $data);
    }

    public function helpCenter()
    {
        $title          = "Help Center - KEWASNET";
        $description    = "Find answers to common questions and get support from KEWASNET.";

        $data = [
            'title'         => $title,
            'description'   => $description
        ];

        return view('frontendV2/website/pages/help_center/index', $data);
    }
}
