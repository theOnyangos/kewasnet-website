<?php

namespace App\Services;

use App\Models\SitemapModel;
use App\Models\BlogPost;
use App\Models\Resource;
use App\Models\Pillar;
use App\Models\Program;
use App\Models\JobOpportunityModel;
use App\Models\FileAttachment;

class SitemapService
{
    protected $sitemapModel;
    protected $blogModel;
    protected $resourceModel;
    protected $pillarModel;
    protected $programModel;
    protected $jobModel;
    protected $fileAttachmentModel;

    public function __construct()
    {
        $this->sitemapModel        = new SitemapModel();
        $this->blogModel           = new BlogPost();
        $this->resourceModel       = new Resource();
        $this->pillarModel         = new Pillar();
        $this->programModel        = new Program();
        $this->jobModel            = new JobOpportunityModel();
        $this->fileAttachmentModel = new FileAttachment();
    }

    /**
     * Generate and store sitemap data
     */
    public function generateSitemap($contentTypes = null)
    {
        try {
            // Default content types if none provided
            if ($contentTypes === null) {
                $contentTypes = ['static', 'blog', 'resources', 'pillars', 'programs', 'jobs'];
            }

            $urlsData = [];
            $currentDate = date('Y-m-d H:i:s');

            // Static pages
            if (in_array('static', $contentTypes)) {
                $staticPages = $this->getStaticPages();
                foreach ($staticPages as $page) {
                    $urlsData[] = [
                        'url'           => $page['url'],
                        'title'         => $page['title'],
                        'description'   => $page['description'],
                        'category'      => $page['category'],
                        'changefreq'    => $page['changefreq'],
                        'priority'      => $page['priority'],
                        'last_modified' => $currentDate,
                        'is_active'     => 1
                    ];
                }
            }

            // Blog posts (News)
            if (in_array('blog', $contentTypes)) {
                $blogs = $this->getBlogPosts();
                foreach ($blogs as $blog) {
                    $blog = is_array($blog) ? (object) $blog : $blog;
                    $urlsData[] = [
                        'url'           => 'news-details/' . $blog->slug,
                        'title'         => $blog->title,
                        'description'   => substr(strip_tags($blog->content ?? $blog->summary ?? ''), 0, 155),
                        'category'      => 'News & Updates',
                        'changefreq'    => 'weekly',
                        'priority'      => '0.7',
                        'last_modified' => $blog->updated_at ?: $blog->created_at,
                        'is_active'     => 1
                    ];
                }
            }

            /** 
             * Resources don't have individual detail pages,
             * they're handled as downloadable files from the main resources page
             * So we skip generating individual resource URLs
             */
            if (in_array('resources', $contentTypes)) {
                // Resources are handled as a static page (already included in static pages)
                // Individual resources don't have public detail pages, they are downloadable files
                log_message('info', 'Resources content type selected but skipped - no individual detail pages exist');
            }

            // Pillars (WASH Topics) - Generate URLs for individual pillar categories
            // and optionally for individual articles within pillars
            if (in_array('pillars', $contentTypes)) {
                $pillars = $this->getPillars();
                foreach ($pillars as $pillar) {
                    $pillar = is_array($pillar) ? (object) $pillar : $pillar;
                    
                    // Add the pillar category page (updated route)
                    $urlsData[] = [
                        'url'           => 'ksp/pillars/' . $pillar->slug,
                        'title'         => $pillar->title,
                        'description'   => substr(strip_tags($pillar->description ?? $pillar->content ?? ''), 0, 155),
                        'category'      => 'WASH Topics',
                        'changefreq'    => 'weekly',
                        'priority'      => '0.8',
                        'last_modified' => $pillar->updated_at ?: $pillar->created_at,
                        'is_active'     => 1
                    ];
                    
                    // Also add individual articles/resources within this pillar
                    try {
                        $pillarResources = $this->getResourcesForPillar($pillar->id ?? $pillar->id);
                        foreach ($pillarResources as $resource) {
                            $resource = is_array($resource) ? (object) $resource : $resource;
                            
                            // Add the resource article page
                            if (!empty($resource->slug)) {
                                $urlsData[] = [
                                    'url'           => 'ksp/pillar-article/' . $resource->slug,
                                    'title'         => $resource->title,
                                    'description'   => substr(strip_tags($resource->description ?? $resource->content ?? ''), 0, 155),
                                    'category'      => 'WASH Topics - Articles',
                                    'changefreq'    => 'monthly',
                                    'priority'      => '0.7',
                                    'last_modified' => $resource->updated_at ?: $resource->created_at,
                                    'is_active'     => 1
                                ];
                            }
                            
                            // Add resource attachment downloads
                            try {
                                $attachments = $this->getResourceAttachments($resource->id);
                                foreach ($attachments as $attachment) {
                                    $attachment = is_array($attachment) ? (object) $attachment : $attachment;
                                    
                                    // Create a descriptive title for the attachment
                                    $attachmentTitle = $resource->title . ' - ' . ($attachment->original_name ?? 'Download');
                                    
                                    // Get file extension for better categorization
                                    $fileExt = strtoupper(pathinfo($attachment->original_name ?? '', PATHINFO_EXTENSION));
                                    $category = 'WASH Topics - Downloads';
                                    if ($fileExt) {
                                        $category .= ' (' . $fileExt . ')';
                                    }
                                    
                                    $urlsData[] = [
                                        'url'           => 'client/download/download-attachment/' . $attachment->file_path,
                                        'title'         => $attachmentTitle,
                                        'description'   => 'Download ' . ($attachment->original_name ?? 'attachment') . ' from ' . $resource->title,
                                        'category'      => $category,
                                        'changefreq'    => 'yearly',
                                        'priority'      => '0.6',
                                        'last_modified' => $attachment->updated_at ?: $attachment->created_at,
                                        'is_active'     => 1
                                    ];
                                }
                            } catch (\Exception $e) {
                                log_message('error', 'Failed to fetch attachments for resource ' . ($resource->slug ?? 'unknown') . ': ' . $e->getMessage());
                            }
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to fetch resources for pillar ' . ($pillar->slug ?? 'unknown') . ': ' . $e->getMessage());
                    }
                }
            }

            // Programs
            if (in_array('programs', $contentTypes)) {
                $programs = $this->getPrograms();
                foreach ($programs as $program) {
                    $program = is_array($program) ? (object) $program : $program;
                    $urlsData[] = [
                        'url'           => 'programs/' . $program->slug,
                        'title'         => $program->title,
                        'description'   => substr(strip_tags($program->description ?? $program->content ?? ''), 0, 155),
                        'category'      => 'Our Programs',
                        'changefreq'    => 'monthly',
                        'priority'      => '0.8',
                        'last_modified' => $program->updated_at ?: $program->created_at,
                        'is_active'     => 1
                    ];
                }
            }

            // Job opportunities
            if (in_array('jobs', $contentTypes)) {
                $jobs = $this->getJobs();
                foreach ($jobs as $job) {
                    $job = is_array($job) ? (object) $job : $job;
                    $urlsData[] = [
                        'url'           => 'opportunities/' . $job->slug,
                        'title'         => $job->title,
                        'description'   => substr(strip_tags($job->description ?? $job->summary ?? ''), 0, 155),
                        'category'      => 'Careers',
                        'changefreq'    => 'weekly',
                        'priority'      => '0.6',
                        'last_modified' => $job->updated_at ?: $job->created_at,
                        'is_active'     => 1
                    ];
                }
            }

            // Store in database
            $result = $this->sitemapModel->bulkUpdateOrCreate($urlsData);

            if ($result) {
                // Deactivate URLs that are no longer valid
                $activeUrls = array_column($urlsData, 'url');
                $this->sitemapModel->deactivateUrlsNotInList($activeUrls);
                
                // Generate physical XML file
                $this->generateXmlFile();
                
                return [
                    'status'    => 'success',
                    'success'   => true, // For backward compatibility with FrontendV2 controller
                    'message'   => 'Sitemap generated successfully',
                    'count'     => count($urlsData)
                ];
            } else {
                return [
                    'status'    => 'error',
                    'success'   => false, // For backward compatibility with FrontendV2 controller
                    'message'   => 'Failed to store sitemap data'
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'Sitemap generation failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'success' => false, // For backward compatibility with FrontendV2 controller
                'message' => 'An error occurred while generating sitemap: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get static pages configuration
     */
    private function getStaticPages()
    {
        return [
            [
                'url' => '',
                'title' => 'KEWASNET - Kenya Water and Sanitation Network',
                'description' => 'KEWASNET is a network of civil society organizations working on water, sanitation and hygiene (WASH) issues in Kenya.',
                'category' => 'Main Pages',
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'url' => 'about',
                'title' => 'About KEWASNET',
                'description' => 'Learn about KEWASNET, our mission, vision, and work in water, sanitation and hygiene.',
                'category' => 'Main Pages',
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'url' => 'services',
                'title' => 'Our Services',
                'description' => 'Explore the services offered by KEWASNET to improve water and sanitation in Kenya.',
                'category' => 'Main Pages',
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'url' => 'climate-change',
                'title' => 'Climate Change',
                'description' => 'Understanding the impact of climate change on water and sanitation systems.',
                'category' => 'WASH Topics',
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ],
            [
                'url' => 'water-management',
                'title' => 'Water Management',
                'description' => 'Sustainable water management practices and solutions.',
                'category' => 'WASH Topics',
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ],
            [
                'url' => 'sanitation',
                'title' => 'Sanitation',
                'description' => 'Improving sanitation facilities and practices across Kenya.',
                'category' => 'WASH Topics',
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ],
            [
                'url' => 'projects',
                'title' => 'Our Projects',
                'description' => 'Explore KEWASNET\'s current and completed projects.',
                'category' => 'Resources',
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            [
                'url' => 'news',
                'title' => 'News & Updates',
                'description' => 'Stay updated with the latest news and developments in WASH sector.',
                'category' => 'Resources',
                'changefreq' => 'daily',
                'priority' => '0.7'
            ],
            [
                'url' => 'blog',
                'title' => 'KEWASNET Blog',
                'description' => 'Read insights, stories, and updates from KEWASNET team and partners.',
                'category' => 'Resources',
                'changefreq' => 'daily',
                'priority' => '0.7'
            ],
            [
                'url' => 'contact',
                'title' => 'Contact Us',
                'description' => 'Get in touch with KEWASNET team.',
                'category' => 'Main Pages',
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            [
                'url' => 'faq',
                'title' => 'Frequently Asked Questions',
                'description' => 'Find answers to common questions about KEWASNET services and WASH topics.',
                'category' => 'Resources',
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'url' => 'best-practices',
                'title' => 'Best Practices',
                'description' => 'Learn about best practices in water, sanitation, and hygiene implementation.',
                'category' => 'Resources',
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'url' => 'policy-briefs',
                'title' => 'Policy Briefs',
                'description' => 'Access comprehensive policy briefs on WASH sector development and governance.',
                'category' => 'Resources',
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'url' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'description' => 'KEWASNET privacy policy and data protection information.',
                'category' => 'Legal Pages',
                'changefreq' => 'yearly',
                'priority' => '0.4'
            ],
            [
                'url' => 'terms-of-service',
                'title' => 'Terms of Service',
                'description' => 'Terms and conditions for using KEWASNET services.',
                'category' => 'Legal Pages',
                'changefreq' => 'yearly',
                'priority' => '0.4'
            ],
            [
                'url' => 'ksp/pillars',
                'title' => 'Our Pillars',
                'description' => 'Explore the key pillars of KEWASNET\'s work in the WASH sector.',
                'category' => 'Main Pages',
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ]
        ];
    }

    /**
     * Get blog posts for sitemap
     */
    private function getBlogPosts()
    {
        try {
            return $this->blogModel->orderBy('updated_at', 'DESC')
                                  ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch blog posts for sitemap: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get resources for sitemap
     */
    private function getResources()
    {
        try {
            return $this->resourceModel->orderBy('updated_at', 'DESC')
                                     ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch resources for sitemap: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get pillars for sitemap
     */
    private function getPillars()
    {
        try {
            return $this->pillarModel->orderBy('updated_at', 'DESC')
                                   ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch pillars for sitemap: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get job opportunities for sitemap
     */
    private function getJobs()
    {
        try {
            return $this->jobModel->orderBy('updated_at', 'DESC')
                                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch jobs for sitemap: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get programs for sitemap
     */
    private function getPrograms()
    {
        try {
            return $this->programModel->where('is_active', 1)
                                     ->orderBy('sort_order', 'ASC')
                                     ->orderBy('updated_at', 'DESC')
                                     ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch programs for sitemap: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get resources for a specific pillar
     */
    private function getResourcesForPillar($pillarId)
    {
        try {
            return $this->resourceModel->where('pillar_id', $pillarId)
                                      ->where('is_published', 1)
                                      ->orderBy('updated_at', 'DESC')
                                      ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch resources for pillar ' . $pillarId . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get file attachments for a specific resource
     */
    private function getResourceAttachments($resourceId)
    {
        try {
            return $this->fileAttachmentModel
                        ->where('attachable_type', 'resources')
                        ->where('attachable_id', $resourceId)
                        ->where('is_image', 0) // Only get non-image files (documents, PDFs, etc.)
                        ->orderBy('created_at', 'DESC')
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch attachments for resource ' . $resourceId . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get sitemap statistics
     */
    public function getStatistics()
    {
        return $this->sitemapModel->getStatistics();
    }

    /**
     * Get sitemap status and statistics for admin panel
     */
    public function getSitemapStatus()
    {
        try {
            $statistics = $this->getStatistics();
            $lastGeneration = $this->sitemapModel->getLastGeneration();
            $xmlPath = WRITEPATH . 'sitemap.xml';
            
            return [
                'status' => 'success',
                'data' => [
                    'statistics' => $statistics,
                    'last_generation' => $lastGeneration,
                    'xml_exists' => file_exists($xmlPath),
                    'xml_size' => file_exists($xmlPath) ? filesize($xmlPath) : 0,
                    'xml_path' => $xmlPath
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Failed to get sitemap status: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Save sitemap configuration
     */
    public function saveSitemapConfig($config)
    {
        try {
            // Store configuration in settings or dedicated config table
            $settingsModel = model('App\\Models\\SettingsModel');
            
            foreach ($config as $key => $value) {
                $settingsModel->setSetting('sitemap_' . $key, $value);
            }
            
            return [
                'status' => 'success',
                'message' => 'Sitemap configuration saved successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Failed to save sitemap config: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get sitemap configuration
     */
    public function getSitemapConfig()
    {
        try {
            $settingsModel = model('App\\Models\\SettingsModel');
            
            $config = [
                'auto_generate'         => $settingsModel->getSetting('sitemap_auto_generate', 0),
                'include_images'        => $settingsModel->getSetting('sitemap_include_images', 1),
                'max_urls'              => $settingsModel->getSetting('sitemap_max_urls', 50000),
                'priority_default'      => $settingsModel->getSetting('sitemap_priority_default', 0.5),
                'changefreq_default'    => $settingsModel->getSetting('sitemap_changefreq_default', 'monthly'),
                'exclude_patterns'      => $settingsModel->getSetting('sitemap_exclude_patterns', ''),
                'content_types'         => $settingsModel->getSetting('sitemap_content_types', ['static', 'blog', 'resources', 'pillars', 'programs', 'jobs'])
            ];
            
            return [
                'status' => 'success',
                'data' => $config
            ];
        } catch (\Exception $e) {
            log_message('error', 'Failed to get sitemap config: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Generate physical XML sitemap file
     */
    private function generateXmlFile(): bool
    {
        try {
            $urls = $this->sitemapModel->getActiveUrlsForXml();
            
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
            
            foreach ($urls as $url) {
                $urlElement = $xml->addChild('url');
                $urlElement->addChild('loc', base_url($url->url));
                $urlElement->addChild('lastmod', date('Y-m-d', strtotime($url->last_modified)));
                $urlElement->addChild('changefreq', $url->changefreq);
                $urlElement->addChild('priority', $url->priority);
            }
            
            $xmlPath = WRITEPATH . 'sitemap.xml';
            return file_put_contents($xmlPath, $xml->asXML()) !== false;
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to generate XML file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Public method to generate XML sitemap file
     */
    public function generateXmlSitemap(): array
    {
        try {
            $success = $this->generateXmlFile();
            
            if ($success) {
                return [
                    'status' => 'success',
                    'success' => true,
                    'message' => 'XML sitemap generated successfully',
                    'file_path' => WRITEPATH . 'sitemap.xml'
                ];
            } else {
                return [
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Failed to generate XML sitemap file'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to generate XML sitemap: ' . $e->getMessage());
            return [
                'status' => 'error',
                'success' => false,
                'message' => 'An error occurred while generating XML sitemap: ' . $e->getMessage()
            ];
        }
    }
}
