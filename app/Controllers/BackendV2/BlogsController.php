<?php

namespace App\Controllers\BackendV2;

use App\Models\BlogPost;
use App\Models\BlogPostCategory;
use App\Models\BlogPostTag;
use App\Models\BlogNewsletter;
use App\Models\BlogTag;
use App\Services\BlogPostService;
use App\Services\DataTableService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BlogsController extends BaseController
{
    protected const PAGE_TITLE = "Manage Blogs - KEWASNET";
    protected const BLOGS_PAGE_TITLE = "Blog Management";

    protected $blogPostModel;
    protected $blogCategoryModel;
    protected $blogPostTagModel;
    protected $blogNewsletterModel;
    protected $blogTagModel;
    protected $dataTableService;
    protected $blogPostService;

    public function __construct()
    {
        $this->blogPostModel = new BlogPost();
        $this->blogCategoryModel = new BlogPostCategory();
        $this->blogPostTagModel = new BlogPostTag();
        $this->blogNewsletterModel = new BlogNewsletter();
        $this->blogTagModel = new BlogTag();
        $this->dataTableService = new DataTableService();
        $this->blogPostService = new BlogPostService();
    }

    protected const CREATE_BLOGS_PAGE_TITLE = "Create New Blog Post";
    protected const CREATE_BLOGS_DASH_TITLE = "Create New Blog Post - Dashboard";

    public function index()
    {
        // Get real blog stats from the models
        $blogStats = $this->blogPostModel->getBlogStats();
        $newsletterStats = $this->blogNewsletterModel->getNewsletterStats();

        $combinedStats = [
            'total_posts'       => $blogStats->total_posts,
            'published_posts'   => $blogStats->published_posts,
            'draft_posts'       => $blogStats->draft_posts,
            'total_categories'  => $blogStats->total_categories,
            'total_views'       => $blogStats->total_views,
            'total_comments'    => $blogStats->total_comments,
            'newsletters_sent'  => $newsletterStats->total_campaigns,
            'subscribers'       => $newsletterStats->total_subscribers
        ];

        return view('backendV2/pages/blogs/index', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => self::BLOGS_PAGE_TITLE,
            'blogStats' => $combinedStats
        ]);
    }

    public function blogs()
    {
        // Get blog stats from BlogPost model
        $blogStats = $this->blogPostModel->getBlogStats();
        
        // Get newsletter stats
        $newsletterStats = $this->blogNewsletterModel->getNewsletterStats();
        
        // Combine stats for the view
        $stats = [
            'total_posts' => $blogStats->total_posts,
            'published_posts' => $blogStats->published_posts,
            'draft_posts' => $blogStats->draft_posts,
            'total_categories' => $blogStats->total_categories,
            'total_views' => $blogStats->total_views,
            'total_comments' => $blogStats->total_comments,
            'newsletters_sent' => $newsletterStats->total_campaigns ?? 0,
            'subscribers' => $newsletterStats->total_subscribers ?? 0
        ];
        
        return view('backendV2/pages/blogs/blogs', [
            'title' => 'Blog Posts - KEWASNET',
            'dashboardTitle' => 'Blog Posts Management',
            'blogStats' => $stats
        ]);
    }

    public function newsletters(): string
    {
        // Get real statistics data for newsletter subscriptions
        $newsletterStats = $this->blogNewsletterModel->getNewsletterStats();
        
        $data['statistics'] = [
            'total_newsletters' => $newsletterStats->total_newsletters, // Total subscriptions
            'total_subscribers' => $newsletterStats->total_subscribers, // Active subscriptions
            'inactive_subscribers' => $newsletterStats->inactive_subscribers,
            'recent_subscriptions' => $newsletterStats->recent_subscriptions,
            'total_campaigns' => $newsletterStats->total_campaigns,
            'avg_open_rate' => $newsletterStats->avg_open_rate,
            'avg_click_rate' => $newsletterStats->avg_click_rate,
            'recent_campaigns' => $newsletterStats->recent_campaigns
        ];

        $data['title'] = 'Newsletter Subscriptions - KEWASNET';
        $data['dashboardTitle'] = 'Newsletter Subscription Management';

        return view('backendV2/pages/blogs/newsletters', $data);
    }

    /**
     * API endpoint for blog posts DataTable
     */
    public function getBlogPosts()
    {
        $columns = ['id', 'title', 'category_name', 'author_name', 'status', 'views', 'comments_count', 'created_at'];

        return $this->dataTableService->handle(
            $this->blogPostModel,
            $columns,
            'getBlogsTable',
            'countBlogPosts',
        );
    }

    /**
     * API endpoint for blog categories DataTable
     */
    public function getBlogCategories()
    {
        $columns = ['id', 'name', 'description', 'post_count', 'created_at'];

        return $this->dataTableService->handle(
            $this->blogCategoryModel,
            $columns,
            'getCategoriesTable',
            'countCategories',
        );
    }

    /**
     * Create a new blog category
     */
    public function createCategory()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $data = $this->request->getPost();

            // Validation rules
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]|is_unique[blog_categories.name]',
                'slug' => 'required|min_length[3]|max_length[255]|is_unique[blog_categories.slug]|alpha_dash',
                'description' => 'permit_empty|max_length[500]',
                'is_active' => 'permit_empty|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Generate UUID for the category
            $categoryData = [
                'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'is_active' => isset($data['is_active']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insert the category
            $this->blogCategoryModel->insert($categoryData);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Blog category created successfully!',
                'data' => $categoryData
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            log_message('error', 'Category creation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create category: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a single category by ID
     */
    public function getCategory($id)
    {
        try {
            $category = $this->blogCategoryModel->find($id);

            if (!$category) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Category not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get category error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve category'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing blog category
     */
    public function updateCategory($id)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $data = $this->request->getPost();

            // Check if category exists
            $category = $this->blogCategoryModel->find($id);
            if (!$category) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Category not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Validation rules
            $rules = [
                'name' => "required|min_length[3]|max_length[255]|is_unique[blog_categories.name,id,{$id}]",
                'slug' => "required|min_length[3]|max_length[255]|is_unique[blog_categories.slug,id,{$id}]|alpha_dash",
                'description' => 'permit_empty|max_length[500]',
                'is_active' => 'permit_empty|in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Prepare update data
            $updateData = [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'is_active' => isset($data['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->blogCategoryModel->update($id, $updateData);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Category updated successfully!',
                'data' => array_merge(['id' => $id], $updateData)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Category update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update category: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a blog category
     */
    public function deleteCategory($id)
    {
        try {
            $category = $this->blogCategoryModel->find($id);
            
            if (!$category) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Category not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if category has posts
            $postCount = $this->blogPostModel
                ->where('category_id', $id)
                ->countAllResults();

            if ($postCount > 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Cannot delete category. It has {$postCount} blog post(s) associated with it."
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            // Soft delete the category
            $this->blogCategoryModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Category deleted successfully!'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Category deletion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API endpoint for newsletters DataTable (Newsletter Subscriptions)
     */
    public function getNewsletters()
    {
        $columns = ['id', 'email', 'is_active', 'created_at', 'updated_at'];

        return $this->dataTableService->handle(
            $this->blogNewsletterModel,
            $columns,
            'getNewslettersTable',
            'countNewsletters',
        );
    }

    public function create()
    {
        $blogPostCategories = $this->blogCategoryModel->findAll();
        $blogTags = $this->blogTagModel->select('id, name')->findAll();

        if (empty($blogPostCategories) && empty($blogTags)) {
            return redirect()->back()->with('error', 'No blog categories or tags found.');
        }

        return view('backendV2/pages/blogs/create', [
            'title' => self::CREATE_BLOGS_PAGE_TITLE,
            'dashboardTitle' => self::CREATE_BLOGS_DASH_TITLE,
            'categories' => $blogPostCategories,
            'tags' => $blogTags
        ]);
    }

    public function handleCreateBlog()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $blogData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Debug log
            log_message('info', 'Blog creation data: ' . json_encode($blogData));
            log_message('info', 'Blog creation files: ' . json_encode(array_keys($files)));

            $blogPost = $this->blogPostService->createBlogPost($blogData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Blog post created successfully!',
                'data' => [],
                'redirect_url' => site_url('auth/blogs/blogs')
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
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

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('auth/blogs/blogs'))->with('error', 'Blog post ID is required');
        }

        $blogPost = $this->blogPostModel->find($id);

        if (!$blogPost) {
            return redirect()->to(base_url('auth/blogs/blogs'))->with('error', 'Blog post not found');
        }

        // Convert to array if it's an object (properly handle all properties)
        if (is_object($blogPost)) {
            $blogPost = json_decode(json_encode($blogPost), true);
        }

        // Debug log to check featured image
        log_message('debug', 'Blog Post Featured Image (original): ' . ($blogPost['featured_image'] ?? 'NULL'));

        // Format featured image path for display
        if (!empty($blogPost['featured_image'])) {
            $featuredImagePath = $blogPost['featured_image'];

            // If it's already a full URL, extract just the path part
            if (strpos($featuredImagePath, 'http') === 0) {
                // Parse the URL and get the path
                $parsedUrl = parse_url($featuredImagePath);
                if (isset($parsedUrl['path'])) {
                    // Remove leading slash to make it work with base_url()
                    $featuredImagePath = ltrim($parsedUrl['path'], '/');
                }
            }

            // Always rebuild the URL with current base_url to ensure it's correct
            $blogPost['featured_image'] = base_url($featuredImagePath);
            log_message('debug', 'Blog Post Featured Image (formatted): ' . $blogPost['featured_image']);
        } else {
            log_message('debug', 'Featured image is empty or null');
        }

        // Get categories
        $blogPostCategories = $this->blogCategoryModel->where('deleted_at', null)->findAll();

        // Get tags
        $blogTags = $this->blogTagModel->findAll();

        // Get blog post tags
        $postTags = $this->blogPostTagModel->where('post_id', $id)->findAll();
        $selectedTags = array_column($postTags, 'tag_id');

        // Get existing additional images (file attachments)
        $fileAttachmentModel = new \App\Models\FileAttachment();
        $additionalImages = $fileAttachmentModel->where('attachable_type', 'blog_posts')
                                               ->where('attachable_id', $id)
                                               ->findAll();

        // Convert additional images to array and format paths
        $formattedAdditionalImages = [];
        foreach ($additionalImages as $image) {
            // Properly convert object to array
            $imageArray = is_object($image) ? json_decode(json_encode($image), true) : $image;

            // Format file path for display
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
                $imageArray['file_path_display'] = base_url($filePath);
            } else {
                $imageArray['file_path_display'] = '';
            }

            $formattedAdditionalImages[] = $imageArray;
        }

        $data = [
            'title' => 'Edit Blog Post - KEWASNET',
            'dashboardTitle' => 'Edit Blog Post',
            'blogPost' => $blogPost,
            'categories' => $blogPostCategories,
            'tags' => $blogTags,
            'selectedTags' => $selectedTags,
            'postTags' => $postTags,
            'additionalImages' => $formattedAdditionalImages
        ];

        return view('backendV2/pages/blogs/edit', $data);
    }

    public function handleEdit($id = null)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        if (!$id) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_BAD_REQUEST,
                'Blog post ID is required'
            );
        }

        try {
            // Check if blog post exists
            $blogPost = $this->blogPostModel->find($id);
            if (!$blogPost) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Blog post not found'
                );
            }

            $blogData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Debug log
            log_message('info', 'Blog update data: ' . json_encode($blogData));
            log_message('info', 'Blog update files: ' . json_encode(array_keys($files)));

            $updatedBlogPost = $this->blogPostService->updateBlogPost($id, $blogData, $files);

            $response = [
                'status' => 'success',
                'message' => 'Blog post updated successfully!',
                'data' => [],
                'redirect_url' => site_url('auth/blogs/blogs')
            ];

            return $this->response->setJSON($response)
                                 ->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            log_message('error', 'Blog update error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update blog post: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function toggleStatus($id = null)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        if (!$id) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_BAD_REQUEST,
                'Blog post ID is required'
            );
        }

        try {
            $status = $this->request->getPost('status');

            if (!in_array($status, ['draft', 'published', 'archived'])) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_BAD_REQUEST,
                    'Invalid status value'
                );
            }

            // Update blog post status
            $blogPost = $this->blogPostModel->find($id);

            if (!$blogPost) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Blog post not found'
                );
            }

            $updateData = ['status' => $status];

            // If publishing, set published_at (first time or update if invalid)
            if ($status === 'published') {
                // Check if published_at is empty or has invalid date
                if (empty($blogPost->published_at) ||
                    $blogPost->published_at === '0000-00-00 00:00:00' ||
                    strtotime($blogPost->published_at) === false) {
                    $updateData['published_at'] = date('Y-m-d H:i:s');
                }
            }

            $this->blogPostModel->update($id, $updateData);

            $message = $status === 'published' ? 'Blog post published successfully' : 'Blog post status updated successfully';

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                $message
            );

        } catch (\Exception $e) {
            log_message('error', 'Blog status toggle error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update blog post status: ' . $e->getMessage()
            );
        }
    }

    /**
     * Delete a blog post (soft delete)
     */
    public function delete($id = null)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        if (!$id) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_BAD_REQUEST,
                'Blog post ID is required'
            );
        }

        try {
            $blogPost = $this->blogPostModel->find($id);

            if (!$blogPost) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Blog post not found'
                );
            }

            // Soft delete the blog post
            $this->blogPostModel->delete($id);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Blog post deleted successfully'
            );

        } catch (\Exception $e) {
            log_message('error', 'Blog post deletion error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to delete blog post: ' . $e->getMessage()
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

    /**
     * Subscribe to newsletter (Frontend endpoint)
     */
    public function subscribeNewsletter()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ])->setStatusCode(400);
        }

        $email = $this->request->getPost('email');

        // Validation
        if (empty($email)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email address is required.'
            ])->setStatusCode(422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ])->setStatusCode(422);
        }

        try {
            $result = $this->blogNewsletterModel->subscribe($email);
            
            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'email' => $email,
                        'subscribed_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message']
                ])->setStatusCode(409);
            }
        } catch (\Exception $e) {
            log_message('error', 'Newsletter subscription error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while processing your subscription. Please try again later.'
            ])->setStatusCode(500);
        }
    }

    protected function isValidAjaxRequest(): bool
    {
        // Just check if it's an AJAX request
        // CSRF protection is handled by CodeIgniter's framework-level CSRF filter
        return $this->request->isAJAX();
    }

    // Reactivate newsletter subscription
    public function reactivateSubscription($id)
    {
        try {
            $newsletterModel = new \App\Models\BlogNewsletter();

            $result = $newsletterModel->update($id, ['is_active' => true]);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Subscription reactivated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to reactivate subscription'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Reactivate subscription error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while reactivating subscription'
            ])->setStatusCode(500);
        }
    }

    // Unsubscribe user from newsletter
    public function unsubscribeUser($id)
    {
        try {
            $newsletterModel = new \App\Models\BlogNewsletter();

            $result = $newsletterModel->update($id, ['is_active' => false]);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User unsubscribed successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to unsubscribe user'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Unsubscribe user error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while unsubscribing user'
            ])->setStatusCode(500);
        }
    }

    // Delete newsletter subscriber permanently
    public function deleteSubscriber($id)
    {
        try {
            $newsletterModel = new \App\Models\BlogNewsletter();

            $result = $newsletterModel->delete($id);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Subscriber deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete subscriber'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Delete subscriber error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting subscriber'
            ])->setStatusCode(500);
        }
    }

    /**
     * Duplicate a blog post
     */
    public function duplicate($id)
    {
        try {
            $blog = $this->blogPostModel->find($id);

            if (!$blog) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Blog post not found'
                ])->setStatusCode(404);
            }

            // Prepare duplicated blog data
            $duplicateData = [
                'title' => $blog->title . ' (Copy)',
                'slug' => $blog->slug . '-copy-' . time(),
                'content' => $blog->content,
                'excerpt' => $blog->excerpt,
                'featured_image' => $blog->featured_image,
                'status' => 'draft', // Always create as draft
                'user_id' => session()->get('id'),
                'category_id' => $blog->category_id,
                'meta_title' => $blog->meta_title,
                'meta_description' => $blog->meta_description,
                'meta_keywords' => $blog->meta_keywords,
                'views' => 0, // Reset views
                'published_at' => null // Reset published date
            ];

            // Create the duplicate
            $newBlogId = $this->blogPostModel->insert($duplicateData);

            if ($newBlogId) {
                // Copy tags if they exist
                $tags = $this->blogPostTagModel->where('post_id', $id)->findAll();
                foreach ($tags as $tag) {
                    $this->blogPostTagModel->insert([
                        'post_id' => $newBlogId,
                        'tag_id' => $tag['tag_id']
                    ]);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Blog post duplicated successfully',
                    'data' => ['id' => $newBlogId]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to duplicate blog post'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Duplicate blog error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while duplicating blog post: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * View deleted blog posts (Admin only - role_id = 1)
     */
    public function deleted()
    {
        // Check if user has admin role
        if (session()->get('role_id') != 1) {
            return redirect()->to(base_url('auth/blogs'))->with('error', 'Access denied. Admin privileges required.');
        }

        // Get blog stats
        $blogStats = $this->blogPostModel->getBlogStats();
        
        // Get newsletter stats
        $newsletterStats = $this->blogNewsletterModel->getNewsletterStats();
        
        // Combine stats for the view
        $stats = [
            'total_posts' => $blogStats->total_posts,
            'published_posts' => $blogStats->published_posts,
            'draft_posts' => $blogStats->draft_posts,
            'total_categories' => $blogStats->total_categories,
            'total_views' => $blogStats->total_views,
            'total_comments' => $blogStats->total_comments,
            'newsletters_sent' => $newsletterStats->total_campaigns ?? 0,
            'subscribers' => $newsletterStats->total_subscribers ?? 0
        ];

        return view('backendV2/pages/blogs/deleted', [
            'title' => 'Deleted Blog Posts - KEWASNET',
            'dashboardTitle' => 'Deleted Blog Posts',
            'blogStats' => $stats
        ]);
    }

    /**
     * Get deleted blog posts for DataTable (Admin only)
     */
    public function getDeletedPosts()
    {
        // Check if user has admin role
        if (session()->get('role_id') != 1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }

        $columns = ['id', 'title', 'category_name', 'author_name', 'status', 'deleted_at'];

        return $this->dataTableService->handle(
            $this->blogPostModel,
            $columns,
            'getDeletedBlogsTable',
            'countDeletedBlogPosts',
        );
    }

    /**
     * Restore a deleted blog post (Admin only)
     */
    public function restore($id)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        // Check if user has admin role
        if (session()->get('role_id') != 1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied. Admin privileges required.'
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }

        try {
            // Find the deleted blog post using withDeleted()
            $blogPost = $this->blogPostModel->onlyDeleted()->find($id);

            if (!$blogPost) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Deleted blog post not found'
                );
            }

            // Restore the blog post by setting deleted_at to null
            $db = \Config\Database::connect();
            $builder = $db->table('blog_posts');
            $builder->where('id', $id);
            $builder->update(['deleted_at' => null]);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Blog post restored successfully'
            );

        } catch (\Exception $e) {
            log_message('error', 'Blog post restoration error: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to restore blog post: ' . $e->getMessage()
            );
        }
    }

    /**
     * Permanently delete a blog post (admin only)
     */
    public function permanentDelete($id = null)
    {
        // Verify AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ])->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        // Verify admin access
        if (session()->get('role_id') != 1) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_FORBIDDEN,
                'Access denied. Admin privileges required.'
            );
        }

        if (!$id) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_BAD_REQUEST,
                'Blog post ID is required'
            );
        }

        try {
            // Find the deleted blog post
            $blogPost = $this->blogPostModel->onlyDeleted()->find($id);
            
            if (!$blogPost) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Blog post not found'
                );
            }

            // Permanently delete the blog post (force delete)
            $db = \Config\Database::connect();
            $db->table('blog_posts')
                ->where('id', $id)
                ->delete();

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Blog post permanently deleted'
            );

        } catch (\Exception $e) {
            log_message('error', 'Error permanently deleting blog post: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to permanently delete blog post: ' . $e->getMessage()
            );
        }
    }
}
