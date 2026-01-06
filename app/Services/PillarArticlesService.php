<?php

namespace App\Services;

use App\Models\Pillar;
use App\Models\Resource;
use App\Models\Contributor;
use App\Models\DocumentType;
use App\Models\ResourceCategory;
use App\Models\ResourceContributor;
use CodeIgniter\Database\ConnectionInterface;
use App\Models\FileAttachment;

class PillarArticlesService
{
    protected $db;
    protected $pillarModel;
    protected $resourceModel;
    protected $contributorModel;
    protected $documentTypeModel;
    protected $fileAttachmentModel;
    protected $resourceCategoryModel;
    protected $resourceContributorModel;

    public function __construct()
    {
        $this->pillarModel              = new Pillar();
        $this->resourceModel            = new Resource();
        $this->documentTypeModel        = new DocumentType();
        $this->resourceCategoryModel    = new ResourceCategory();
        $this->contributorModel         = new Contributor();
        $this->resourceContributorModel = new ResourceContributor();
        $this->db                       = \Config\Database::connect();
        $this->fileAttachmentModel      = new FileAttachment();
    }

    /**
     * Get pillar by slug
     */
    public function getPillarBySlug(string $slug): ?array
    {
        try {
            $pillar = $this->pillarModel->where('slug', $slug)->first();
            
            if (!$pillar) {
                return null;
            }
            // Convert to array if it's an object
            if (is_object($pillar)) {
                // Handle both Entity objects and stdClass objects
                if (method_exists($pillar, 'toArray')) {
                    return $pillar->toArray();
                } else {
                    // Convert stdClass to array
                    return (array) $pillar;
                }
            }
            return $pillar;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching pillar by slug: ' . $e->getMessage());
            return null;
        }
    }

        /**
     * Get resources for a specific pillar with pagination and filters
     */
    public function getResourcesForPillar(string $pillarId, array $filters = [], int $page = 1, int $perPage = 6): array
    {
        try {
            // Build query using query builder for better flexibility
            $builder = $this->db->table('resources r')
                ->select('r.*, 
                    rc.name as category_name,
                    dt.name as document_type_name,
                    dt.color as document_type_color,
                    p.title as pillar_name')
                ->join('resource_categories rc', 'r.category_id = rc.id', 'left')
                ->join('document_types dt', 'r.document_type_id = dt.id', 'left')
                ->join('pillars p', 'r.pillar_id = p.id', 'left')
                ->where('r.pillar_id', $pillarId)
                ->where('r.is_published', 1)
                ->where('r.deleted_at IS NULL', null, false);
            
            // Apply category filter
            if (!empty($filters['category']) && $filters['category'] !== 'all') {
                $builder->where('r.category_id', $filters['category']);
            }
            
            // Apply document type filter
            if (!empty($filters['document_type']) && $filters['document_type'] !== 'all') {
                $builder->where('r.document_type_id', $filters['document_type']);
            }
            
            // Apply search filter
            if (!empty($filters['search'])) {
                $builder->groupStart()
                    ->like('r.title', $filters['search'])
                    ->orLike('r.description', $filters['search'])
                    ->groupEnd();
            }
            
            // Get total count before pagination
            $total = $builder->countAllResults(false);
            
            // Apply sorting
            $sort = $filters['sort'] ?? 'newest';
            switch ($sort) {
                case 'oldest':
                    $builder->orderBy('r.created_at', 'ASC');
                    break;
                case 'title':
                    $builder->orderBy('r.title', 'ASC');
                    break;
                case 'downloads':
                    // Order by download count (we'll need to join with file_attachments or use a subquery)
                    $builder->orderBy('r.download_count', 'DESC');
                    break;
                case 'newest':
                default:
                    $builder->orderBy('r.created_at', 'DESC');
                    break;
            }
            
            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
            
            $resources = $builder->get()->getResultArray();
            
            // Add file attachments data to each resource
            if (!empty($resources)) {
                foreach ($resources as &$resource) {
                    try {
                        $attachmentData = $this->fileAttachmentModel->getAttachmentsForResource($resource['id']);
                        $resource['total_downloads'] = $attachmentData['total_downloads'] ?? 0;
                        $resource['total_file_size'] = $attachmentData['total_file_size'] ?? 0;
                        
                        // Get file type from first attachment if available
                        if (!empty($attachmentData['attachments']) && is_array($attachmentData['attachments'])) {
                            $firstAttachment = $attachmentData['attachments'][0];
                            $resource['file_type'] = $firstAttachment['file_type'] ?? $resource['file_type'] ?? 'Document';
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Error fetching attachments for resource ' . $resource['id'] . ': ' . $e->getMessage());
                        $resource['total_downloads'] = 0;
                        $resource['total_file_size'] = 0;
                    }
                }
                unset($resource); // Unset the reference
            }
            
            return [
                'data' => $resources ?? [],
                'pagination' => [
                    'total'         => $total,
                    'per_page'      => $perPage,
                    'current_page'  => $page,
                    'last_page'     => ceil($total / $perPage) ?: 1,
                    'from'          => $total > 0 ? $offset + 1 : 0,
                    'to'            => min($offset + $perPage, $total)
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching resources for pillar: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [
                'data' => [],
                'pagination' => [
                    'total'         => 0,
                    'per_page'      => $perPage,
                    'current_page'  => $page,
                    'last_page'     => 1,
                    'from'          => 0,
                    'to'            => 0
                ]
            ];
        }
    }

    /**
     * Get categories for a pillar with resource counts
     */
    public function getCategoriesForPillar(string $pillarId): array
    {
        try {
            // Validate pillar ID
            if (empty($pillarId)) {
                log_message('error', 'Empty pillar ID provided to getCategoriesForPillar');
                return [];
            }
            
            // Use query builder directly to query resource_categories table
            $query = $this->db->table('resource_categories')
                ->select('resource_categories.id, resource_categories.pillar_id, resource_categories.name, resource_categories.slug, resource_categories.description, resource_categories.created_at, resource_categories.updated_at')
                ->where('resource_categories.pillar_id', $pillarId)
                ->orderBy('resource_categories.name', 'ASC');
            
            $categories = $query->get()->getResultArray();
            
            if (empty($categories)) {
                log_message('debug', 'No categories found in resource_categories table for pillar ID: ' . $pillarId);
                // Try to verify if pillar exists
                $pillarExists = $this->db->table('pillars')
                    ->where('id', $pillarId)
                    ->countAllResults();
                log_message('debug', 'Pillar exists check: ' . ($pillarExists > 0 ? 'Yes' : 'No'));
                return [];
            }
            
            log_message('debug', 'Found ' . count($categories) . ' categories in resource_categories table for pillar ID: ' . $pillarId);
            
            // Add resource counts to each category
            foreach ($categories as &$category) {
                try {
                    // Count published resources for this category
                    $resourceCount = $this->db->table('resources')
                        ->where('category_id', $category['id'])
                        ->where('is_published', 1)
                        ->where('deleted_at IS NULL', null, false)
                        ->countAllResults();
                    
                    $category['resource_count'] = (int)$resourceCount;
                } catch (\Exception $e) {
                    log_message('error', 'Error counting resources for category ' . ($category['id'] ?? 'unknown') . ': ' . $e->getMessage());
                    $category['resource_count'] = 0;
                }
            }
            unset($category); // Unset reference
            
            log_message('debug', 'Successfully processed ' . count($categories) . ' categories with resource counts');
            return $categories;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching categories from resource_categories table for pillar ' . $pillarId . ': ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Get all document types formatted for filters
     */
    public function getDocumentTypes(): array
    {
        try {
            $documentTypes = $this->documentTypeModel->orderBy('name', 'ASC')->findAll() ?? [];
            
            // Format for view
            return array_map(function($docType) {
                $docTypeArray = is_object($docType) ? (array)$docType : $docType;
                return [
                    'value' => $docTypeArray['id'],
                    'label' => $docTypeArray['name'],
                    'color' => $docTypeArray['color'] ?? '#6B7280'
                ];
            }, $documentTypes);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching document types: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get statistics for a pillar
     */
    public function getPillarStatistics(string $pillarId): array
    {
        try {
            $stats = [
                'totalResources' => $this->countResourcesForPillar($pillarId),
                'totalCategories' => $this->countCategoriesForPillar($pillarId),
                'totalContributors' => $this->countContributorsForPillar($pillarId)
            ];

            return $stats;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching pillar statistics: ' . $e->getMessage());
            return [
                'totalResources' => 0,
                'totalCategories' => 0,
                'totalContributors' => 0
            ];
        }
    }

    /**
     * Build resource count query with filters
     */
    private function buildResourceCountQuery(string $pillarId, array $filters)
    {
        $query = $this->db->table('resources')
            ->where('pillar_id', $pillarId)
            ->where('is_published', 1);

        return $this->applyFiltersToQuery($query, $filters);
    }

    /**
     * Build main resource query with joins, filters, and pagination
     */
    private function buildResourceQuery(string $pillarId, array $filters, int $page, int $perPage)
    {
        // Temporarily simplify query to isolate the collation issue
        $query = $this->db->table('resources r')
            ->select('r.id, r.title, r.slug, r.description, r.image_url, r.file_url, 
                      r.file_type, r.created_at, r.document_type_id, r.category_id')
            ->where('r.pillar_id', $pillarId)
            ->where('r.is_published', 1);

        $query = $this->applyFiltersToQuery($query, $filters);

        return $query
            ->orderBy('r.created_at', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage);
    }

    /**
     * Apply filters to a query
     */
    private function applyFiltersToQuery($query, array $filters)
    {
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['document_type']) && $filters['document_type'] !== 'all') {
            $query->where('document_type_id', $filters['document_type']);
        }

        if (!empty($filters['search'])) {
            $query->groupStart()
                ->like('title', $filters['search'])
                ->orLike('description', $filters['search'])
                ->groupEnd();
        }

        return $query;
    }

    /**
     * Add contributors to resources (limit to first 3 resources and 2 contributors each)
     */
    private function addContributorsToResources(array &$resources): void
    {
        if (empty($resources)) {
            return;
        }

        // Only process first 3 resources to save memory
        $limitedResources = array_slice($resources, 0, 3);
        
        foreach ($limitedResources as $index => $resource) {
            try {
                $contributorQuery = $this->db->table('resource_contributors rc')
                    ->select('c.name, rc.role')
                    ->join('contributors c', 'c.id = rc.contributor_id')
                    ->where('rc.resource_id', $resource->id)
                    ->limit(2);
                
                $resource->contributors = $contributorQuery->get()->getResult();
                
                if ($index >= 2) break; // Only process first 3 resources
            } catch (\Exception $e) {
                log_message('error', 'Error fetching contributors for resource ' . $resource->id . ': ' . $e->getMessage());
                $resource->contributors = [];
            }
        }
        
        // Set empty contributors for remaining resources
        for ($i = 3; $i < count($resources); $i++) {
            $resources[$i]->contributors = [];
        }
    }

    /**
     * Count resources for a pillar
     */
    private function countResourcesForPillar(string $pillarId): int
    {
        try {
            return $this->db->table('resources')
                ->where('pillar_id', $pillarId)
                ->where('is_published', 1)
                ->where('deleted_at IS NULL', null, false)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error counting resources: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count categories for a pillar
     */
    private function countCategoriesForPillar(string $pillarId): int
    {
        try {
            return $this->db->table('resource_categories')
                ->where('pillar_id', $pillarId)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'Error counting categories: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count unique contributors for a pillar
     */
    private function countContributorsForPillar(string $pillarId): int
    {
        try {
            // Use a subquery approach to avoid collation issues in joins
            $resourceIds = $this->db->table('resources')
                ->select('id')
                ->where('pillar_id', $pillarId)
                ->where('is_published', 1)
                ->get()
                ->getResult();
            
            if (empty($resourceIds)) {
                return 0;
            }
            
            $resourceIdList = array_column($resourceIds, 'id');
            
            return $this->db->table('resource_contributors')
                ->select('contributor_id')
                ->whereIn('resource_id', $resourceIdList)
                ->distinct()
                ->countAllResults();
                
        } catch (\Exception $e) {
            log_message('error', 'Error counting contributors: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Parse filters from request
     */
    public function parseFilters(array $requestData): array
    {
        return [
            'category' => $requestData['category'] ?? null,
            'document_type' => $requestData['document_type'] ?? null,
            'search' => $requestData['search'] ?? null
        ];
    }

    /**
     * Get default view data structure
     */
    public function getDefaultViewData(string $slug): array
    {
        return [
            'title'             => 'KEWASNET - Pillar Articles',
            'description'       => 'Pillar articles page',
            'pillar'            => ['title' => 'Unknown Pillar', 'slug' => $slug, 'description' => 'No description available', 'id' => ''],
            'resources'         => [],
            'categories'        => [],
            'documentTypes'     => [],
            'totalResources'    => 0,
            'totalCategories'   => 0,
            'totalContributors' => 0,
            'currentPage'       => 1,
            'totalPages'        => 1,
            'perPage'           => 6,
            'totalItems'        => 0,
            'filters' => [
                'category' => null,
                'document_type' => null,
                'search' => null,
                'sort' => 'newest'
            ]
        ];
    }

    /**
     * Validate and sanitize pagination parameters
     */
    public function validatePaginationParams(int $page, int $perPage = 6): array
    {
        $page = max(1, $page); // Ensure page is at least 1
        $perPage = max(1, min(50, $perPage)); // Limit between 1 and 50

        return ['page' => $page, 'perPage' => $perPage];
    }

    /**
     * Get resource by slug for detailed view with enhanced data
     */
    public function getResourceBySlug(string $slug): ?array
    {
        try {            
            // Validate input
            if (empty($slug) || strlen($slug) > 255) {
                log_message('error', 'Invalid slug provided: ' . $slug);
                return null;
            }
            
            // Get resource with category and pillar information using raw SQL to handle collation
            $sql = "SELECT r.*, 
                           rc.name as category_name, 
                           rc.slug as category_slug,
                           p.title as pillar_title,
                           p.slug as pillar_slug
                    FROM resources r
                    LEFT JOIN resource_categories rc ON r.category_id COLLATE utf8mb4_general_ci = rc.id COLLATE utf8mb4_general_ci
                    LEFT JOIN pillars p ON r.pillar_id COLLATE utf8mb4_general_ci = p.id COLLATE utf8mb4_general_ci
                    WHERE r.slug = ? 
                    AND r.is_published = 1 
                    AND r.deleted_at IS NULL";
            
            $resource = $this->db->query($sql, [$slug])->getRowArray();
            
            if (!$resource) {
                return null;
            }

            // Get file attachments for this resource
            $attachmentData = $this->fileAttachmentModel->getAttachmentsForResource($resource['id']);
            
            $resource['attachments']     = $attachmentData['attachments'] ?? [];
            $resource['total_downloads'] = $attachmentData['total_downloads'] ?? 0;
            // Get contributors for this resource (limit to prevent memory issues)
            $contributors = $this->getContributorsForResource($resource['id']);
            $resource['contributors'] = array_slice($contributors, 0, 10); // Limit to 10 contributors max
            
            // Get category information if available
            if (!empty($resource['category_id'])) {
                $category = $this->resourceCategoryModel->find($resource['category_id']);
                if (is_object($category)) {
                    // Handle both Entity objects and stdClass objects
                    if (method_exists($category, 'toArray')) {
                        $resource['category'] = $category->toArray();
                    } else {
                        // Convert stdClass to array
                        $resource['category'] = (array) $category;
                    }
                } else {
                    $resource['category'] = $category;
                }
            }
            
            log_message('debug', "PillarArticlesService: Returning resource successfully");
            return $resource;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching resource by slug: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Get contributors for a specific resource
     */
    public function getContributorsForResource(string $resourceId): array
    {
        try {
            // Validate resource ID
            if (empty($resourceId)) {
                return [];
            }
            
            return $this->db->table('resource_contributors rc')
                ->select('c.id, c.name, c.organization, rc.role')
                ->join('contributors c', 'c.id = rc.contributor_id')
                ->where('rc.resource_id', $resourceId)
                ->limit(10) // Prevent memory issues with too many contributors
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching contributors for resource: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Increment download count for a resource
     */
    public function incrementDownloadCount(string $resourceId): bool
    {
        try {
            return $this->resourceModel->set('download_count', 'download_count + 1', false)
                ->where('id', $resourceId)
                ->update();
        } catch (\Exception $e) {
            log_message('error', 'Error incrementing download count: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment view count for a resource
     */
    public function incrementViewCount(string $resourceId): bool
    {
        try {
            return $this->resourceModel->set('view_count', 'view_count + 1', false)
                ->where('id', $resourceId)
                ->update();
        } catch (\Exception $e) {
            log_message('error', 'Error incrementing view count: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle resource download - increment count and return file info
     */
    public function handleResourceDownload(string $resourceId): ?array
    {
        try {
            $resource = $this->resourceModel->find($resourceId);
            
            if (!$resource || empty($resource['file_url'])) {
                return null;
            }
            
            // Increment download count
            $this->incrementDownloadCount($resourceId);
            
            return [
                'file_url'  => $resource['file_url'],
                'file_name' => $resource['title'] . '.' . strtolower($resource['file_type']),
                'file_type' => $resource['file_type'],
                'file_size' => $resource['file_size']
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error handling resource download: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get comprehensive statistics for a resource
     */
    public function getResourceStatistics(string $resourceId): array
    {
        try {
            $resource = $this->db->table('resources')
                ->select('view_count, download_count, file_size, file_type, publication_year, is_featured, created_at')
                ->where('id', $resourceId)
                ->where('is_published', 1)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if (!$resource) {
                return [];
            }

            // Calculate some additional metrics
            $daysSincePublication = 0;
            if (!empty($resource['created_at'])) {
                $created = new \DateTime($resource['created_at']);
                $now = new \DateTime();
                $daysSincePublication = $now->diff($created)->days;
            }

            $dailyAvgViews = $daysSincePublication > 0 ? round($resource['view_count'] / $daysSincePublication, 2) : 0;
            $downloadRate = $resource['view_count'] > 0 ? round(($resource['download_count'] / $resource['view_count']) * 100, 1) : 0;

            return [
                'view_count'                => (int) $resource['view_count'],
                'download_count'            => (int) $resource['download_count'],
                'file_size'                 => $resource['file_size'],
                'file_type'                 => $resource['file_type'],
                'publication_year'          => $resource['publication_year'],
                'is_featured'               => (bool) $resource['is_featured'],
                'days_since_publication'    => $daysSincePublication,
                'daily_avg_views'           => $dailyAvgViews,
                'download_rate_percent'     => $downloadRate,
                'created_at'                => $resource['created_at']
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching resource statistics: ' . $e->getMessage());
            return [];
        }
    }
}
