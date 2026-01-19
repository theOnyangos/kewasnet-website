<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\CourseEnrollmentModel;
use App\Models\EventModel;
use App\Models\EventBookingModel;
use App\Models\DocumentResource;
use App\Models\BlogPost;
use App\Models\Discussion;
use App\Models\Program;
use App\Models\Pillar;
use App\Models\Faq;
use App\Models\SitemapModel;
use App\Libraries\ClientAuth;

class AIContextService
{
    protected $userModel;
    protected $courseModel;
    protected $enrollmentModel;
    protected $eventModel;
    protected $bookingModel;
    protected $resourceModel;
    protected $blogModel;
    protected $discussionModel;
    protected $programModel;
    protected $pillarModel;
    protected $faqModel;
    protected $sitemapModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new CourseEnrollmentModel();
        $this->eventModel = new EventModel();
        $this->bookingModel = new EventBookingModel();
        $this->resourceModel = new DocumentResource();
        $this->blogModel = new BlogPost();
        $this->discussionModel = new Discussion();
        $this->programModel = new Program();
        $this->pillarModel = new Pillar();
        $this->faqModel = new Faq();
        $this->sitemapModel = new SitemapModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get user-specific context
     */
    public function getUserContext($userId): array
    {
        $context = [];
        
        // Get user info
        $user = $this->userModel->find($userId);
        if ($user) {
            $context['user'] = [
                'name' => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
                'email' => $user['email'] ?? '',
                'status' => $user['status'] ?? 'active',
            ];
        }

        // Get course enrollments
        $enrollments = $this->enrollmentModel
            ->where('user_id', $userId)
            ->findAll();
        
        if (!empty($enrollments)) {
            $courseIds = array_column($enrollments, 'course_id');
            $courses = $this->courseModel
                ->whereIn('id', $courseIds)
                ->findAll();
            
            $context['enrolled_courses'] = array_map(function($course) {
                return [
                    'id' => $course['id'],
                    'title' => $course['title'] ?? '',
                    'slug' => $course['slug'] ?? '',
                    'level' => $course['level'] ?? '',
                ];
            }, $courses);
        }

        // Get event bookings
        $bookings = $this->bookingModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        if (!empty($bookings)) {
            $eventIds = array_column($bookings, 'event_id');
            $events = $this->eventModel
                ->whereIn('id', $eventIds)
                ->findAll();
            
            $context['booked_events'] = array_map(function($event) {
                return [
                    'id' => $event['id'],
                    'title' => $event['title'] ?? '',
                    'slug' => $event['slug'] ?? '',
                    'start_date' => $event['start_date'] ?? '',
                ];
            }, $events);
        }

        return $context;
    }

    /**
     * Search content based on query
     */
    public function getContentContext(string $query, int $limit = 5): array
    {
        $context = [
            'courses' => [],
            'events' => [],
            'resources' => [],
            'blog_posts' => [],
            'discussions' => [],
            'programs' => [],
            'pillars' => [],
            'faqs' => [],
            'sitemap' => [],
            'kb_sources' => [],
            'static_pages' => [],
            'feedback_channels' => [],
        ];

        // Search courses
        $courses = $this->courseModel
            ->groupStart()
                ->like('title', $query)
                ->orLike('summary', $query)
                ->orLike('description', $query)
            ->groupEnd()
            ->where('status', 'published')
            ->limit($limit)
            ->findAll();
        
        $context['courses'] = array_map(function($course) {
            return [
                'id' => $course['id'],
                'title' => $course['title'] ?? '',
                'slug' => $course['slug'] ?? '',
                'summary' => substr($course['summary'] ?? '', 0, 200),
                'level' => $course['level'] ?? '',
                'price' => $course['price'] ?? 0,
            ];
        }, $courses);

        // Search events
        $events = $this->eventModel
            ->groupStart()
                ->like('title', $query)
                ->orLike('description', $query)
            ->groupEnd()
            ->where('status', 'published')
            ->limit($limit)
            ->findAll();
        
        $context['events'] = array_map(function($event) {
            return [
                'id' => $event['id'],
                'title' => $event['title'] ?? '',
                'slug' => $event['slug'] ?? '',
                'start_date' => $event['start_date'] ?? '',
                'venue' => $event['venue'] ?? '',
            ];
        }, $events);

        // Search resources
        $resources = $this->resourceModel
            ->groupStart()
                ->like('title', $query)
                ->orLike('description', $query)
            ->groupEnd()
            ->where('is_published', 1)
            ->limit($limit)
            ->findAll();
        
        $context['resources'] = array_map(function($resource) {
            return [
                'id' => $resource['id'],
                'title' => $resource['title'] ?? '',
                'slug' => $resource['slug'] ?? '',
                'description' => substr($resource['description'] ?? '', 0, 200),
            ];
        }, $resources);

        // Search blog posts
        $blogPosts = $this->blogModel
            ->groupStart()
                ->like('title', $query)
                ->orLike('excerpt', $query)
            ->groupEnd()
            ->where('status', 'published')
            ->limit($limit)
            ->findAll();
        
        $context['blog_posts'] = array_map(function($post) {
            return [
                'id' => $post->id ?? '',
                'title' => $post->title ?? '',
                'slug' => $post->slug ?? '',
                'excerpt' => substr($post->excerpt ?? '', 0, 200),
            ];
        }, $blogPosts);

        // Search discussions
        $discussions = $this->discussionModel
            ->groupStart()
                ->like('title', $query)
                ->orLike('content', $query)
            ->groupEnd()
            ->limit($limit)
            ->findAll();
        
        $context['discussions'] = array_map(function($discussion) {
            $discussionData = is_array($discussion) ? $discussion : (array) $discussion;
            return [
                'id' => $discussionData['id'] ?? '',
                'title' => $discussionData['title'] ?? '',
                'slug' => $discussionData['slug'] ?? '',
            ];
        }, $discussions);

        // Search programs
        try {
            $programs = $this->programModel
                ->groupStart()
                    ->like('title', $query)
                    ->orLike('description', $query)
                ->groupEnd()
                ->limit($limit)
                ->findAll();
            
            $context['programs'] = array_map(function($program) {
                return [
                    'id' => $program['id'] ?? '',
                    'title' => $program['title'] ?? '',
                    'slug' => $program['slug'] ?? '',
                ];
            }, $programs);
        } catch (\Exception $e) {
            // Program model might not exist or table might not be available
            $context['programs'] = [];
        }

        // Search pillars
        try {
            $pillars = $this->pillarModel
                ->groupStart()
                    ->like('title', $query)
                    ->orLike('description', $query)
                    ->orLike('content', $query)
                ->groupEnd()
                ->where('is_active', 1)
                ->limit($limit)
                ->findAll();
            
            $context['pillars'] = array_map(function($pillar) {
                $pillarData = is_array($pillar) ? $pillar : (array)$pillar;
                return [
                    'id' => $pillarData['id'] ?? '',
                    'title' => $pillarData['title'] ?? '',
                    'slug' => $pillarData['slug'] ?? '',
                    'description' => substr($pillarData['description'] ?? '', 0, 200),
                ];
            }, $pillars);
        } catch (\Exception $e) {
            log_message('debug', 'Pillar search failed: ' . $e->getMessage());
            $context['pillars'] = [];
        }

        // Search FAQs
        try {
            $faqs = $this->faqModel
                ->groupStart()
                    ->like('question', $query)
                    ->orLike('answer', $query)
                    ->orLike('tags', $query)
                ->groupEnd()
                ->where('status', 'active')
                ->where('searchable', 1)
                ->limit($limit)
                ->findAll();
            
            $context['faqs'] = array_map(function($faq) {
                $faqData = is_array($faq) ? $faq : (array)$faq;
                return [
                    'id' => $faqData['id'] ?? '',
                    'question' => $faqData['question'] ?? '',
                    'answer' => substr($faqData['answer'] ?? '', 0, 200),
                    'category' => $faqData['category'] ?? '',
                ];
            }, $faqs);
        } catch (\Exception $e) {
            log_message('debug', 'FAQ search failed: ' . $e->getMessage());
            $context['faqs'] = [];
        }

        // Search sitemap
        try {
            $sitemaps = $this->sitemapModel
                ->groupStart()
                    ->like('title', $query)
                    ->orLike('description', $query)
                    ->orLike('url', $query)
                ->groupEnd()
                ->where('is_active', 1)
                ->limit($limit)
                ->findAll();
            
            $context['sitemap'] = array_map(function($sitemap) {
                $sitemapData = is_object($sitemap) ? (array)$sitemap : $sitemap;
                return [
                    'id' => $sitemapData['id'] ?? '',
                    'title' => $sitemapData['title'] ?? '',
                    'url' => $sitemapData['url'] ?? '',
                    'description' => substr($sitemapData['description'] ?? '', 0, 200),
                    'category' => $sitemapData['category'] ?? '',
                ];
            }, $sitemaps);
        } catch (\Exception $e) {
            log_message('debug', 'Sitemap search failed: ' . $e->getMessage());
            $context['sitemap'] = [];
        }

        // Search AI knowledge base (admin-managed sources)
        $context['kb_sources'] = $this->getKnowledgeBaseContext($query, $limit);

        // Add static page references
        $context['static_pages'] = [
            'about' => [
                'name' => 'About Us',
                'url' => base_url('about'),
                'description' => 'Learn about KEWASNET, our mission, vision, and organization',
            ],
            'contact' => [
                'name' => 'Contact Us',
                'url' => base_url('contact-us'),
                'description' => 'Get in touch with KEWASNET team',
            ],
            'privacy' => [
                'name' => 'Privacy and Policies',
                'url' => base_url('privacy-and-policies'),
                'description' => 'Privacy policies and data protection information',
            ],
            'terms' => [
                'name' => 'Terms of Service',
                'url' => base_url('terms-of-service'),
                'description' => 'Terms of service and user agreements',
            ],
            'help' => [
                'name' => 'Help Center',
                'url' => base_url('help-center'),
                'description' => 'Help center with guides and support resources',
            ],
            'support' => [
                'name' => 'Support',
                'url' => base_url('contact-us'),
                'description' => 'Contact support for assistance',
            ],
        ];

        // Add feedback channels information
        $context['feedback_channels'] = [
            'feedback' => [
                'name' => 'General Feedback',
                'url' => base_url('contact-us'),
                'description' => 'Submit general feedback through the contact form',
            ],
            'suggestions' => [
                'name' => 'Suggestions',
                'url' => base_url('contact-us'),
                'description' => 'Share your suggestions and ideas through the contact form',
            ],
            'complaints' => [
                'name' => 'Complaints',
                'url' => base_url('contact-us'),
                'description' => 'Submit complaints through the contact form',
            ],
            'grievances' => [
                'name' => 'Grievances',
                'url' => base_url('contact-us'),
                'description' => 'Report grievances through the contact form',
            ],
        ];

        return $context;
    }

    /**
     * Retrieve relevant AI knowledge base chunks for a query.
     * Uses SQL LIKE search (fast/simple). Can be upgraded to embeddings later.
     */
    public function getKnowledgeBaseContext(string $query, int $limit = 5): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        try {
            $builder = $this->db->table('ai_kb_chunks');
            $builder->select([
                'ai_kb_chunks.id as chunk_id',
                'ai_kb_chunks.source_id',
                'ai_kb_chunks.chunk_index',
                'ai_kb_chunks.content',
                'ai_kb_sources.type as source_type',
                'ai_kb_sources.title as source_title',
                'ai_kb_sources.source_url',
                'ai_kb_sources.file_path',
                'ai_kb_sources.updated_at as source_updated_at',
            ]);
            $builder->join('ai_kb_sources', 'ai_kb_sources.id = ai_kb_chunks.source_id');
            $builder->where('ai_kb_sources.status', 'active');

            $builder->groupStart();
            $builder->like('ai_kb_chunks.content', $query);
            $builder->orLike('ai_kb_sources.title', $query);

            $tokens = preg_split('/\s+/', $query) ?: [];
            $tokens = array_values(array_filter(array_map('trim', $tokens), fn ($t) => mb_strlen($t) >= 4));
            $tokens = array_slice($tokens, 0, 6);

            foreach ($tokens as $t) {
                $builder->orLike('ai_kb_chunks.content', $t);
                $builder->orLike('ai_kb_sources.title', $t);
            }
            $builder->groupEnd();

            $builder->orderBy('ai_kb_sources.updated_at', 'DESC');
            $builder->orderBy('ai_kb_chunks.chunk_index', 'ASC');
            $builder->limit($limit);

            $rows = $builder->get()->getResultArray();

            return array_map(function ($r) {
                $content = (string) ($r['content'] ?? '');
                return [
                    'chunk_id' => $r['chunk_id'] ?? '',
                    'source_id' => $r['source_id'] ?? '',
                    'chunk_index' => (int) ($r['chunk_index'] ?? 0),
                    'content' => $content,
                    'citation' => [
                        'title' => $r['source_title'] ?? '',
                        'type' => $r['source_type'] ?? '',
                        'url' => $r['source_url'] ?? null,
                        'file_path' => $r['file_path'] ?? null,
                    ],
                    'snippet' => mb_substr(trim($content), 0, 240),
                ];
            }, $rows);
        } catch (\Exception $e) {
            log_message('debug', 'AI KB search failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get admin-specific context
     */
    public function getAdminContext($userId, string $query = ''): array
    {
        $context = [];
        
        // Get user info
        $user = $this->userModel->find($userId);
        if ($user) {
            $context['admin'] = [
                'name' => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
                'email' => $user['email'] ?? '',
            ];
        }

        // High-level system stats (broad coverage, no secrets)
        $context['statistics'] = [
            'total_courses' => $this->courseModel->countAllResults(),
            'published_courses' => $this->courseModel->where('status', 'published')->countAllResults(),
            'total_events' => $this->eventModel->countAllResults(),
            'upcoming_events' => $this->eventModel->where('start_date >=', date('Y-m-d'))->countAllResults(),
            'total_resources' => $this->resourceModel->countAllResults(),
            'published_resources' => $this->resourceModel->where('is_published', 1)->countAllResults(),
            'total_blog_posts' => $this->blogModel->where('status', 'published')->countAllResults(),
        ];

        // Additional stats from other tables if available
        $extraTables = [
            'system_users',
            'users',
            'courses',
            'events',
            'resources',
            'blog_posts',
            'blog_newsletters',
            'newsletter_subscriptions',
            'forum_members',
            'forums',
            'discussions',
            'notifications',
            'email_queue',
            'partners',
            'programs',
            'pillars',
            'faqs',
            'ai_conversations',
            'ai_messages',
            'ai_kb_sources',
        ];

        $context['system_counts'] = [];
        foreach ($extraTables as $t) {
            try {
                if ($this->db->tableExists($t)) {
                    $context['system_counts'][$t] = $this->db->table($t)->countAllResults();
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        // Admin query-time search across key tables (filtered columns to avoid secrets)
        $query = trim($query);
        if ($query !== '') {
            $context['admin_search'] = $this->searchDatabase($query, [
                'system_users',
                'users',
                'courses',
                'events',
                'resources',
                'blog_posts',
                'blog_newsletters',
                'forums',
                'forum_members',
                'discussions',
                'notifications',
                'email_queue',
                'partners',
                'programs',
                'pillars',
                'faqs',
                'ai_kb_sources',
            ]);
        }

        return $context;
    }

    /**
     * Generic database search
     */
    public function searchDatabase(string $query, array $tables = []): array
    {
        $results = [];
        
        // If no tables specified, search common tables
        if (empty($tables)) {
            $tables = ['courses', 'events', 'resources', 'blog_posts'];
        }

        foreach ($tables as $table) {
            try {
                $builder = $this->db->table($table);
                
                // Get column names for the table
                $columns = $this->db->getFieldNames($table);
                $searchableColumns = array_filter($columns, function($col) {
                    $colLower = strtolower((string) $col);

                    // Always exclude core technical columns
                    $excludeExact = ['id', 'created_at', 'updated_at', 'deleted_at'];
                    if (in_array($colLower, $excludeExact, true)) {
                        return false;
                    }

                    // Exclude sensitive/security columns
                    $sensitiveNeedles = [
                        'password', 'passwd', 'pwd',
                        'token', 'secret', 'api_key', 'apikey', 'access_key',
                        'private_key', 'signature',
                        'hash', 'salt',
                        'otp', '2fa', 'mfa',
                        'remember', 'reset',
                        'session', 'cookie',
                    ];
                    foreach ($sensitiveNeedles as $needle) {
                        if (strpos($colLower, $needle) !== false) {
                            return false;
                        }
                    }

                    return true;
                });

                $builder->groupStart();
                foreach ($searchableColumns as $col) {
                    $builder->orLike($col, $query);
                }
                $builder->groupEnd();
                
                $results[$table] = $builder->limit(5)->get()->getResultArray();
            } catch (\Exception $e) {
                // Table might not exist, skip
                log_message('debug', 'Table search failed for ' . $table . ': ' . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Format context for AI prompts
     */
    public function formatContextForPrompt(array $context): string
    {
        $formatted = [];
        
        if (isset($context['user'])) {
            $formatted[] = "User Information:";
            $formatted[] = "- Name: " . ($context['user']['name'] ?? 'N/A');
            $formatted[] = "- Email: " . ($context['user']['email'] ?? 'N/A');
            $formatted[] = "";
        }

        if (isset($context['enrolled_courses']) && !empty($context['enrolled_courses'])) {
            $formatted[] = "User's Enrolled Courses:";
            foreach ($context['enrolled_courses'] as $course) {
                $formatted[] = "- " . $course['title'] . " (Level: " . $course['level'] . ")";
            }
            $formatted[] = "";
        }

        if (isset($context['booked_events']) && !empty($context['booked_events'])) {
            $formatted[] = "User's Booked Events:";
            foreach ($context['booked_events'] as $event) {
                $formatted[] = "- " . $event['title'] . " (Date: " . $event['start_date'] . ")";
            }
            $formatted[] = "";
        }

        if (isset($context['courses']) && !empty($context['courses'])) {
            $formatted[] = "Available Courses:";
            foreach ($context['courses'] as $course) {
                $formatted[] = "- " . $course['title'] . ($course['price'] > 0 ? " (Price: $" . $course['price'] . ")" : " (Free)");
            }
            $formatted[] = "";
        }

        if (isset($context['events']) && !empty($context['events'])) {
            $formatted[] = "Upcoming Events:";
            foreach ($context['events'] as $event) {
                $formatted[] = "- " . $event['title'] . " (Date: " . $event['start_date'] . ", Venue: " . $event['venue'] . ")";
            }
            $formatted[] = "";
        }

        if (isset($context['resources']) && !empty($context['resources'])) {
            $formatted[] = "Available Resources:";
            foreach ($context['resources'] as $resource) {
                $formatted[] = "- " . $resource['title'];
            }
            $formatted[] = "";
        }

        if (isset($context['blog_posts']) && !empty($context['blog_posts'])) {
            $formatted[] = "Blog Posts:";
            foreach ($context['blog_posts'] as $post) {
                $formatted[] = "- " . $post['title'];
            }
            $formatted[] = "";
        }

        if (isset($context['discussions']) && !empty($context['discussions'])) {
            $formatted[] = "Discussions:";
            foreach ($context['discussions'] as $discussion) {
                $formatted[] = "- " . $discussion['title'];
            }
            $formatted[] = "";
        }

        if (isset($context['programs']) && !empty($context['programs'])) {
            $formatted[] = "Programs:";
            foreach ($context['programs'] as $program) {
                $formatted[] = "- " . $program['title'];
            }
            $formatted[] = "";
        }

        if (isset($context['pillars']) && !empty($context['pillars'])) {
            $formatted[] = "Pillars:";
            foreach ($context['pillars'] as $pillar) {
                $formatted[] = "- " . $pillar['title'] . ($pillar['description'] ? ": " . $pillar['description'] : '');
            }
            $formatted[] = "";
        }

        if (isset($context['faqs']) && !empty($context['faqs'])) {
            $formatted[] = "Frequently Asked Questions:";
            foreach ($context['faqs'] as $faq) {
                $formatted[] = "- Q: " . $faq['question'];
                $formatted[] = "  A: " . $faq['answer'];
            }
            $formatted[] = "";
        }

        if (isset($context['sitemap']) && !empty($context['sitemap'])) {
            $formatted[] = "Sitemap Pages:";
            foreach ($context['sitemap'] as $sitemap) {
                $formatted[] = "- " . $sitemap['title'] . " (" . $sitemap['url'] . ")";
            }
            $formatted[] = "";
        }

        if (isset($context['kb_sources']) && !empty($context['kb_sources'])) {
            $formatted[] = "Knowledge Base Sources (admin-managed):";
            foreach ($context['kb_sources'] as $kb) {
                $title = $kb['citation']['title'] ?? 'Source';
                $ref = $kb['citation']['url'] ?? ($kb['citation']['file_path'] ?? '');
                $formatted[] = "- Source: " . $title . ($ref ? " (" . $ref . ")" : '');
                $formatted[] = "  Content: " . ($kb['snippet'] ?? '');
            }
            $formatted[] = "";
        }

        if (isset($context['static_pages']) && !empty($context['static_pages'])) {
            $formatted[] = "Available Pages:";
            foreach ($context['static_pages'] as $key => $page) {
                $formatted[] = "- " . $page['name'] . ": " . $page['url'] . " - " . $page['description'];
            }
            $formatted[] = "";
        }

        if (isset($context['feedback_channels']) && !empty($context['feedback_channels'])) {
            $formatted[] = "Feedback and Support Channels:";
            foreach ($context['feedback_channels'] as $key => $channel) {
                $formatted[] = "- " . $channel['name'] . ": " . $channel['url'] . " - " . $channel['description'];
            }
            $formatted[] = "";
        }

        if (isset($context['statistics'])) {
            $formatted[] = "System Statistics:";
            foreach ($context['statistics'] as $key => $value) {
                $formatted[] = "- " . str_replace('_', ' ', ucwords($key, '_')) . ": " . $value;
            }
            $formatted[] = "";
        }

        if (isset($context['system_counts']) && !empty($context['system_counts'])) {
            $formatted[] = "System Counts (tables):";
            foreach ($context['system_counts'] as $table => $count) {
                $formatted[] = "- " . $table . ": " . $count;
            }
            $formatted[] = "";
        }

        if (isset($context['admin_search']) && !empty($context['admin_search'])) {
            $formatted[] = "Admin Search Results (filtered):";
            foreach ($context['admin_search'] as $table => $rows) {
                if (empty($rows)) {
                    continue;
                }
                $formatted[] = "- Table: " . $table;
                // Keep prompt small: include only first 2 rows per table
                $rows = array_slice($rows, 0, 2);
                foreach ($rows as $r) {
                    // include a compact JSON-ish preview
                    $formatted[] = "  Row: " . substr(json_encode($r), 0, 400);
                }
            }
            $formatted[] = "";
        }

        return implode("\n", $formatted);
    }
}
