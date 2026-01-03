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
            // Use raw query to handle collation issues
            $sql = "SELECT resources.*, resource_categories.name as category_name 
                    FROM resources 
                    LEFT JOIN resource_categories ON resources.category_id COLLATE utf8mb4_general_ci = resource_categories.id COLLATE utf8mb4_general_ci
                    WHERE resource_categories.pillar_id COLLATE utf8mb4_general_ci = ? 
                    AND resources.deleted_at IS NULL";
            
            $params = [$pillarId];
            
            // Apply filters
            if (!empty($filters['category'])) {
                $sql .= " AND resource_categories.id COLLATE utf8mb4_general_ci = ?";
                $params[] = $filters['category'];
            }
            
            if (!empty($filters['search'])) {
                $sql .= " AND (resources.title LIKE ? OR resources.description LIKE ?)";
                $params[] = '%' . $filters['search'] . '%';
                $params[] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($filters['document_type'])) {
                $sql .= " AND resources.document_type = ?";
                $params[] = $filters['document_type'];
            }
            
            // Apply sorting
            $sort = $filters['sort'] ?? 'latest';
            switch ($sort) {
                case 'title':
                    $sql .= " ORDER BY resources.title ASC";
                    break;
                case 'type':
                    $sql .= " ORDER BY resources.document_type ASC";
                    break;
                case 'latest':
                default:
                    $sql .= " ORDER BY resources.created_at DESC";
                    break;
            }
            
            // Get total count for pagination
            $countSql = "SELECT COUNT(*) as total 
                        FROM resources 
                        LEFT JOIN resource_categories ON resources.category_id COLLATE utf8mb4_general_ci = resource_categories.id COLLATE utf8mb4_general_ci
                        WHERE resource_categories.pillar_id COLLATE utf8mb4_general_ci = ? 
                        AND resources.deleted_at IS NULL";
            
            $countParams = [$pillarId];
            
            // Apply the same filters to count query
            if (!empty($filters['category'])) {
                $countSql .= " AND resource_categories.id COLLATE utf8mb4_general_ci = ?";
                $countParams[] = $filters['category'];
            }
            
            if (!empty($filters['search'])) {
                $countSql .= " AND (resources.title LIKE ? OR resources.description LIKE ?)";
                $countParams[] = '%' . $filters['search'] . '%';
                $countParams[] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($filters['document_type'])) {
                $countSql .= " AND resources.document_type = ?";
                $countParams[] = $filters['document_type'];
            }
            
            $countResult = $this->db->query($countSql, $countParams)->getRow();
            $total = $countResult->total ?? 0;
            
            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;
            
            $resources = $this->db->query($sql, $params)->getResultArray();
            
            // Add file attachments to each resource
            if (!empty($resources)) {
                foreach ($resources as &$resource) {
                    $attachmentData = $this->fileAttachmentModel->getAttachmentsForResource($resource['id']);
                    $resource['total_downloads'] = $attachmentData['total_downloads'] ?? 0;
                    $resource['total_file_size'] = $attachmentData['total_file_size'] ?? 0;
                }
                unset($resource); // Unset the reference
            }
            
            return [
                'data' => $resources ?? [],
                'pagination'        => [
                    'total'         => $total,
                    'per_page'      => $perPage,
                    'current_page'  => $page,
                    'last_page'     => ceil($total / $perPage),
                    'from'          => $offset + 1,
                    'to'            => min($offset + $perPage, $total)
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching resources for pillar: ' . $e->getMessage());
            return [
                'data' => [],
                'pagination'        => [
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
            $categories = $this->resourceCategoryModel
                ->where('pillar_id', $pillarId)
                ->findAll() ?? [];
            
            // Convert objects to arrays and add resource counts
            return array_map(function($category) {
                $categoryArray = is_object($category) ? (array)$category : $category;
                
                // Get resource count for this category
                try {
                    $resourceCount = $this->db->table('resources')
                        ->where('category_id', $categoryArray['id'])
                        ->where('is_published', 1)
                        ->where('deleted_at IS NULL')
                        ->countAllResults();
                    
                    $categoryArray['resource_count'] = $resourceCount;
                } catch (\Exception $e) {
                    log_message('error', 'Error counting resources for category ' . $categoryArray['id'] . ': ' . $e->getMessage());
                    $categoryArray['resource_count'] = 0;
                }
                
                return $categoryArray;
            }, $categories);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching categories for pillar: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all document types
     */
    public function getDocumentTypes(): array
    {
        try {
            return $this->documentTypeModel->findAll() ?? [];
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
            'pillar'            => ['title' => 'Unknown Pillar', 'slug' => $slug, 'description' => 'No description available'],
            'resources'         => [],
            'categories'        => [],
            'documentTypes'     => [],
            'totalResources'    => 0,
            'totalCategories'   => 0,
            'totalContributors' => 0,
            'currentPage'       => 1,
            'totalPages'        => 0,
            'perPage'           => 6,
            'totalItems'        => 0,
            'filters' => [
                'category' => null,
                'document_type' => null,
                'search' => null
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
