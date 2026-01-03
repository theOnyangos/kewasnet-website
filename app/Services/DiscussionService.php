<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Discussion;
use App\Models\FileAttachment;
use App\Models\ForumModerator;
use CodeIgniter\Validation\Validation;
use App\Exceptions\ValidationException;

class DiscussionService
{
    protected $db;
    protected $userId;
    protected $validation;
    protected $discussionModel;
    protected $fileAttachmentModel;
    protected $forumModeratorModel;
    protected $pivotTable = 'discussion_tag_pivot'; // Not complete

    public function __construct()
    {
        $this->discussionModel      = new Discussion();
        $this->validation           = \Config\Services::validation();
        $this->db                   = \Config\Database::connect();
        $this->fileAttachmentModel  = new FileAttachment();
        $this->forumModeratorModel  = new ForumModerator();
        $this->userId               = session()->get('id') ? session()->get('id') : session()->get('user_id'); // Admin ID or User ID (Moderator)
    }

    public function createDiscussion(string $forumId, array $forumData, array $files)
    {
        if (!$this->checkUserPermissions($this->userId, $forumId)) {
            throw new \RuntimeException('You do not have permission to create a discussion in this forum');
        }

        $rules = [
            'forum_id' => 'required|is_not_unique[forums.id]',
            'title'    => 'required|min_length[5]|max_length[255]',
            'slug'     => 'required|max_length[500]|is_unique[discussions.slug,id,{id}]',
            'content'  => 'required|min_length[20]',
            'status'   => 'permit_empty|in_list[active,hidden,reported,deleted]',
            'tags'     => 'permit_empty|validateTags',
            'attachments[]' => 'permit_empty|uploaded[attachments.*]|max_size[attachments.*,5120]|ext_in[attachments.*,pdf,doc,docx,txt,xls,xlsx,ppt,pptx,csv]'
        ];

        $messages = [
            'forum_id' => [
                'required' => 'Forum ID is required',
                'is_not_unique' => 'Forum ID does not exist'
            ],
            'title' => [
                'required'   => 'Discussion title is required',
                'min_length' => 'Title must be at least 5 characters long',
                'max_length' => 'Title cannot exceed 255 characters'
            ],
            'slug' => [
                'required'   => 'Slug is required',
                'max_length' => 'Slug cannot exceed 500 characters',
                'is_unique'  => 'This slug is already in use'
            ],
            'content' => [
                'required' => 'Discussion content is required',
                'min_length' => 'Content must be at least 20 characters long'
            ],
            'tags' => [
                'permit_empty'  => 'Tags are optional',
            ],
            'attachments[]' => [
                'permit_empty' => 'Attachments are optional',
                'uploaded'     => 'You must upload a file',
                'max_size'     => 'File size must not exceed 5MB',
                'ext_in'       => 'Invalid file type. Allowed types: pdf, doc, docx, txt, xls, xlsx, ppt, pptx, csv'
            ]
        ];

        $this->validation->setRules($rules, $messages);
        
        if (!$this->validation->run($forumData)) {
            throw new ValidationException($this->validation->getErrors());
        }

        try {            
            $insertData = [
                'forum_id' => $forumId,
                'title'    => $forumData['title'],
                'slug'     => $forumData['slug'],
                'content'  => $forumData['content'],
                'tags'     => $forumData['tags'] ?? [],
                'status'   => $forumData['status'] ?? 'active',
            ];

            $discussionId = $this->discussionModel->createDiscussion($insertData);

            if (!$discussionId) {
                throw new \RuntimeException('Failed to create discussion');
            }

            // Process file attachments
            $this->processAttachments($discussionId, $files);

            return $discussionId;

        } catch (\Exception $e) {
            log_message('error', 'Discussion creation failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to create discussion due to database error: ' . $e->getMessage());
        }
    }

    public function getDiscussionsByForumId(string $forumId, array $options = [])
    {
        // Validate the forum ID
        $this->validation->setRule('forum_id', 'Forum ID', 'required|is_not_unique[forums.id]');
        
        if (!$this->validation->run(['forum_id' => $forumId])) {
            throw new ValidationException($this->validation->getErrors());
        }

        // Set default options
        $defaultOptions = [
            'page' => 1,
            'per_page' => 10,
            'order_by' => 'created_at',
            'order_dir' => 'DESC',
            'status' => 'active',
            'with_attachments' => true,
            'cache_ttl' => 300, // 5 minutes cache by default
            'force_refresh' => false, // Bypass cache if needed
        ];
        
        $options = array_merge($defaultOptions, $options);

        // Generate a unique cache key based on all parameters
        $cacheKey = $this->generateCacheKey($forumId, $options);

        // Check cache if not forcing refresh
        if (!$options['force_refresh'] && $cached = cache($cacheKey)) {
            return $cached;
        }

        try {
            // Start database transaction for consistency
            $this->db->transStart();

            // Get discussions from the model
            $builder = $this->discussionModel
                ->where('forum_id', $forumId)
                ->where('status', $options['status'])
                ->orderBy($options['order_by'], $options['order_dir']);

            // Apply search filter if provided
            if (!empty($options['search'])) {
                $builder->groupStart()
                    ->like('title', $options['search'])
                    ->orLike('content', $options['search'])
                    ->groupEnd();
            }

            $discussions = $builder->paginate($options['per_page'], 'default', $options['page']);

            $attachments = [];
            if ($options['with_attachments'] && !empty($discussions)) {
                // Get discussion IDs for attachment query
                $discussionIds = array_column($discussions, 'id');

                // Get attachments for these discussions
                $attachments = $this->fileAttachmentModel
                    ->where('attachable_type', 'discussion')
                    ->whereIn('attachable_id', $discussionIds)
                    ->findAll();

                // Organize attachments by discussion ID
                $attachmentsByDiscussion = [];
                foreach ($attachments as $attachment) {
                    $attachmentsByDiscussion[$attachment['attachable_id']][] = $attachment;
                }

                // Merge attachments with discussions
                foreach ($discussions as &$discussion) {
                    $discussion->attachments = $attachmentsByDiscussion[$discussion->id] ?? [];
                }
            }

            $this->db->transComplete();

            $result = [
                'discussions' => $discussions,
                'pager' => $this->discussionModel->pager,
                'meta' => [
                    'total_attachments' => count($attachments),
                    'cache_key' => $cacheKey,
                    'cached_at' => date('Y-m-d H:i:s')
                ]
            ];

            // Save to cache
            cache()->save($cacheKey, $result, $options['cache_ttl']);

            return $result;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Failed to fetch discussions: ' . $e->getMessage());
            throw new \RuntimeException('Failed to fetch discussions: ' . $e->getMessage());
        }
    }

    public function invalidateForumCache(string $forumId): void
    {
        $cache = cache();
        $prefix = "forum_discussions:{$forumId}:";

        // Get all cache keys for this forum
        $keys = $cache->getCacheKeys(); // Note: This might need adapter-specific implementation

        foreach ($keys as $key) {
            if (strpos($key, $prefix) === 0) {
                $cache->delete($key);
            }
        }
    }

    protected function generateCacheKey(string $forumId, array $options): string
    {
        // Remove cache-specific options from the key generation
        $keyOptions = $options;
        unset($keyOptions['cache_ttl'], $keyOptions['force_refresh']);

        // Create a hash of the options to ensure consistent key length
        $optionsHash = md5(json_encode($keyOptions));

        return "forum_discussions:{$forumId}:{$optionsHash}";
    }

    protected function processAttachments($discussionId, $files)
    {
        if (!empty($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    // Generate a new filename
                    $newName = $file->getRandomName();
                    
                    // Move file to upload directory
                    $file->move(WRITEPATH . 'uploads/discussions', $newName);
                    
                    // Determine if file is an image
                    $mimeType = $file->getClientMimeType();
                    $isImage = strpos($mimeType, 'image/') === 0;
                    
                    // Save attachment record - store path without 'uploads/' prefix
                    $this->fileAttachmentModel->save([
                        'attachable_id' => $discussionId,
                        'attachable_type' => 'discussion',
                        'original_name' => $file->getClientName(),
                        'file_name' => $newName,
                        'file_path' => 'discussions/' . $newName,
                        'file_type' => $mimeType,
                        'file_size' => $file->getSize(),
                        'mime_type' => $mimeType,
                        'is_image' => $isImage,
                    ]);
                }
            }
        }
    }

    protected function checkUserPermissions($userId, $forumId): bool
    {
        // Check if user is authenticated
        if (!$userId) {
            return false;
        }

        // Check if the forum exists and is active
        $forum = model('Forum')->find($forumId);
        
        if (!$forum || !$forum->is_active) {
            return false;
        }
        
        // Check if user is blocked from the forum
        $forumMemberModel = new \App\Models\ForumMember();
        $membership = $forumMemberModel->where('forum_id', $forumId)
                                      ->where('user_id', $userId)
                                      ->first();
        
        if ($membership && $membership->is_blocked == 1) {
            return false;
        }

        // Allow if user is a moderator of the forum or any authenticated user
        return $this->isUserModerator($userId, $forumId) || $userId !== null;
    }

    protected function isUserModerator($userId, $forumId): bool
    {
        return $this->forumModeratorModel->isModerator($forumId, $userId);
    }
    
    public function updateDiscussion(string $discussionId, array $discussionData, array $files = [])
    {
        $discussionModel = model('Discussion');
        $discussion = $discussionModel->find($discussionId);
        
        if (!$discussion) {
            throw new \RuntimeException('Discussion not found');
        }
        
        // Check permissions
        if (!$this->checkUserPermissions($this->userId, $discussion->forum_id)) {
            throw new \RuntimeException('You do not have permission to update this discussion');
        }
        
        $rules = [
            'title'    => 'required|min_length[5]|max_length[255]',
            'slug'     => 'required|max_length[500]',
            'content'  => 'required|min_length[20]',
            'tags'     => 'permit_empty',
        ];
        
        $messages = [
            'title' => [
                'required'   => 'Discussion title is required',
                'min_length' => 'Title must be at least 5 characters long',
                'max_length' => 'Title cannot exceed 255 characters'
            ],
            'slug' => [
                'required'   => 'Slug is required',
                'max_length' => 'Slug cannot exceed 500 characters'
            ],
            'content' => [
                'required' => 'Discussion content is required',
                'min_length' => 'Content must be at least 20 characters long'
            ]
        ];
        
        $this->validation->setRules($rules, $messages);
        
        if (!$this->validation->run($discussionData)) {
            throw new ValidationException($this->validation->getErrors());
        }
        
        try {
            // Prepare update data with only basic fields
            $updateData = [
                'title'   => $discussionData['title'],
                'slug'    => $discussionData['slug'],
                'content' => $discussionData['content']
            ];
            
            // Update basic fields first
            $discussionModel->update($discussionId, $updateData);
            
            // Handle tags separately using direct query to avoid JSON encoding issues
            if (isset($discussionData['tags'])) {
                $tags = $discussionData['tags'];
                // If it's a JSON string, decode it first
                if (is_string($tags)) {
                    $decodedTags = json_decode($tags, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedTags)) {
                        $tags = $decodedTags;
                    } else {
                        $tags = [];
                    }
                }
                
                // Encode to JSON string for database
                $tagsJson = json_encode(is_array($tags) ? $tags : []);
                
                // Update tags field directly
                $db = \Config\Database::connect();
                $db->table('discussions')
                   ->where('id', $discussionId)
                   ->update(['tags' => $tagsJson]);
            }
            
            // Process new attachments
            if (!empty($files['attachments'])) {
                $this->processAttachments($discussionId, $files);
            }
            
            return $discussionModel->find($discussionId);
            
        } catch (\Exception $e) {
            log_message('error', 'Discussion update failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update discussion: ' . $e->getMessage());
        }
    }
}