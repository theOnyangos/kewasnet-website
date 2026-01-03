<?php

namespace App\Services;

use App\Models\Reply;
use App\Models\Discussion;
use CodeIgniter\Files\File;
use App\Models\FileAttachment;
use CodeIgniter\HTTP\ResponseInterface;
use App\Exceptions\ValidationException;

class ClientDiscussionService
{
    protected $response;
    protected $validation;
    protected $replyModel;
    protected $discussionModel;
    protected $fileAttachmentModel;

    public function __construct()
    {
        $this->replyModel           = new Reply();
        $this->discussionModel      = new Discussion();
        $this->fileAttachmentModel  = new FileAttachment();
        $this->response             = service('response');
        $this->validation           = \Config\Services::validation();
    }

    /**
     * Get a discussion by its slug.
     */
    public function getDiscussionBySlug(string $slug)
    {
        $discussion = $this->discussionModel->where('slug', $slug)->first();

        if ($discussion) {
            $attachment = $this->fileAttachmentModel->where('attachable_id', $discussion->id)->first();
            if ($attachment) {
                $discussion->original_name = $attachment['original_name'];
                $discussion->file_size = $attachment['file_size'];
                $discussion->download_url = base_url("ksp/discussion/download-attachment/" . urlencode($attachment['file_path']));
                $discussion->view_url = base_url("ksp/discussion/view-attachment/" . $attachment['file_path']); 
            } else {
                $discussion->original_name = null;
                $discussion->file_size = null;
                $discussion->download_url = null;
                $discussion->view_url = null;
            }
        }

        return $discussion;
    }

    /**
    * Get replies for a discussion along with user information.
    */
    public function getRepliesWithUserInfo(string $discussionId): array
    {
        return $this->replyModel->getRepliesWithUserInfo($discussionId);
    }

    /**
     * Get author information for a discussion.
     */
    public function getDiscussionAuthorInformation(string $discussionId)
    {
        return $this->discussionModel->getDiscussionAuthorInformation($discussionId);
    }

    /**
     * Increment the view count for a discussion.
     */
    public function incrementViews(string $discussionId): bool
    {
        return $this->discussionModel->incrementViews($discussionId);
    }

    /**
     * Add a reply to a discussion.
     */
    public function addReply(string $discussionId, string $content, ?string $parentId = null, array $files = []): array
    {
        // Validate discussion exists and is not locked
        $discussion = $this->discussionModel->find($discussionId);

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        if (!$discussion) {
            return [
                'success' => false,
                'message' => 'Discussion not found'
            ];
        }

        if ($discussion->is_locked) {
            return [
                'success' => false,
                'message' => 'This discussion is locked'
            ];
        }

        // Validate content
        if (empty($content) || strlen(trim($content)) < 10) {
            return [
                'success' => false,
                'message' => 'Reply must be at least 10 characters long'
            ];
        }

        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'You must be logged in to reply'
            ];
        }

        $uploadedAttachments = [];
        
        try {
            // Create reply
            $replyData = [
                'discussion_id' => $discussionId,
                'user_id'       => $userId,
                'content'       => trim($content),
                'parent_id'     => !empty($parentId) ? $parentId : null,
            ];

            if ($this->replyModel->insert($replyData)) {
                $replyId = $this->replyModel->getInsertID();

                // Update discussion reply count and last reply info
                $this->discussionModel->incrementReplies($discussionId, $userId);

                // Handle file attachments - make it more robust
                if (!empty($files)) {
                    // Check if we have the expected attachments array
                    $attachmentsToProcess = [];
                    
                    if (isset($files['attachments']) && is_array($files['attachments'])) {
                        $attachmentsToProcess = $files['attachments'];
                    } else {
                        // Try to find any uploaded files
                        foreach ($files as $fileArray) {
                            if (is_array($fileArray)) {
                                $attachmentsToProcess = array_merge($attachmentsToProcess, $fileArray);
                            }
                        }
                    }
                    
                    // Filter out empty files
                    $attachmentsToProcess = array_filter($attachmentsToProcess, function($file) {
                        // Check if $file is a valid uploaded file object
                        if (!is_object($file) || !method_exists($file, 'getError')) {
                            return false;
                        }
                        return $file->getError() !== UPLOAD_ERR_NO_FILE;
                    });
                    
                    if (!empty($attachmentsToProcess)) {
                        $uploadResult = $this->handleFileUploads($attachmentsToProcess, $replyId, $userId);
                        
                        if (isset($uploadResult['success']) && !$uploadResult['success']) {
                            throw new \Exception($uploadResult['message']);
                        }
                        
                        $uploadedAttachments = $uploadResult;
                    }
                }

                $db->transComplete();

                return [
                    'success'     => true,
                    'message'     => 'Reply added successfully' . (!empty($uploadedAttachments) ? ' with ' . count($uploadedAttachments) . ' attachment(s)' : ''),
                    'reply_id'    => $replyId,
                    'attachments' => $uploadedAttachments
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to add reply'
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            
            // Clean up any uploaded files if transaction failed
            if (!empty($uploadedAttachments)) {
                foreach ($uploadedAttachments as $attachment) {
                    if (isset($attachment['file_path'])) {
                        $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

     /**
     * Handle file uploads and create attachment records
     */
    protected function handleFileUploads(array $files, string $replyId, string $userId): array
    {
        $maxSize             = 5 * 1024 * 1024; // 5MB
        $maxFiles            = 5;
        $uploadedAttachments = [];
        $errors              = [];

        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv'
        ];

        // Debug: log the files structure
        log_message('debug', 'Files in handleFileUploads: ' . json_encode(array_map(function($file) {
            return [
                'name'          => $file->getName(),
                'client_name'   => $file->getClientName(),
                'size'          => $file->getSize(),
                'valid'         => $file->isValid(),
                'error'         => $file->getError(),
                'error_string'  => $file->getErrorString()
            ];
        }, $files)));

        if (count($files) > $maxFiles) {
            return [
                'success' => false,
                'message' => "Maximum {$maxFiles} files allowed"
            ];
        }

        foreach ($files as $file) {
            // Skip empty files or files with upload errors
            if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                continue; // Skip empty file inputs
            }

            if (!$file->isValid()) {
                $errors[] = 'Invalid file uploaded: ' . $file->getErrorString();
                continue;
            }

            // Validate file size
            if ($file->getSize() > $maxSize) {
                $errors[] = "File '{$file->getClientName()}' exceeds 5MB limit";
                continue;
            }

            // Validate file type
            $clientMime = $file->getClientMimeType();
            if (!in_array($clientMime, $allowedTypes)) {
                $errors[] = "File type not supported: {$file->getClientName()}";
                continue;
            }

            // Generate unique filename and path
            $newName        = $file->getRandomName();
            $uploadPath     = 'replies/' . date('Y-m-d');
            $fullUploadPath = WRITEPATH . 'uploads/' . $uploadPath;

            // Create directory if it doesn't exist
            if (!is_dir($fullUploadPath)) {
                mkdir($fullUploadPath, 0755, true);
            }

            // Move file to upload directory
            if ($file->move($fullUploadPath, $newName)) {
                // Create attachment record
                $attachmentData = [
                    'attachable_type'   => 'reply',
                    'attachable_id'     => $replyId,
                    'user_id'           => $userId,
                    'original_name'     => $file->getClientName(),
                    'file_name'         => $newName,
                    'file_path'         => $uploadPath . '/' . $newName,
                    'file_type'         => $file->getClientExtension(),
                    'file_size'         => $file->getSize(),
                    'mime_type'         => $file->getClientMimeType(),
                    'download_count'    => 0,
                ];

                try {
                    if ($this->fileAttachmentModel->insert($attachmentData)) {
                        $attachmentId = $this->fileAttachmentModel->getInsertID();
                        
                        $uploadedAttachments[] = [
                            'id'            => $attachmentId,
                            'original_name' => $file->getClientName(),
                            'file_name'     => $newName,
                            'file_path'     => $uploadPath . '/' . $newName,
                            'file_type'     => $file->getClientExtension(),
                            'file_size'     => $file->getSize(),
                            'mime_type'     => $file->getClientMimeType(),
                            'is_image'      => strpos($file->getClientMimeType(), 'image/') === 0,
                            'download_url'  => $this->getAttachmentDownloadUrl($uploadPath . '/' . $newName),
                            'view_url'      => $this->getAttachmentViewUrl($uploadPath . '/' . $newName)
                        ];
                    } else {
                        unlink($fullUploadPath . '/' . $newName);
                        $errors[] = "Failed to save attachment: {$file->getClientName()}";
                    }
                } catch (\Exception $e) {
                    unlink($fullUploadPath . '/' . $newName);
                    $errors[] = "Database error: {$file->getClientName()}";
                }
            } else {
                $errors[] = "Failed to upload: {$file->getClientName()}";
            }
        }

        if (!empty($errors)) {
            foreach ($uploadedAttachments as $attachment) {
                $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            return [
                'success' => false,
                'message' => implode(', ', $errors)
            ];
        }

        return $uploadedAttachments;
    }

    /**
     * Get attachment download URL
     */
    protected function getAttachmentDownloadUrl(string $filePath): string
    {
        return base_url("ksp/discussion/download-attachment/" . urlencode($filePath));
    }

    /**
     * Get attachment view URL (for images)
     */
    protected function getAttachmentViewUrl(string $filePath): string
    {
        return base_url("writable/uploads/" . $filePath);
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(string $filePath)
    {
        $filePath = urldecode($filePath);

        log_message('debug', 'Downloading file: ' . $filePath);

        $fullPath = WRITEPATH . 'uploads/' . $filePath;

        log_message('debug', 'Full file path: ' . $fullPath);

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new PageNotFoundException('File not found');
        }

        // Find attachment record to get original name
        $attachment = $this->fileAttachmentModel->where('file_path', $filePath)->first();
        $originalName = $attachment ? $attachment['original_name'] : basename($filePath);

        // Increment download count
        if ($attachment) {
            $this->fileAttachmentModel->incrementDownloadCount($attachment['id']);
        }

        return $this->response->download($fullPath, null)->setFileName($originalName);
    }

    /**
     * View attachment (for images)
     */
    public function viewAttachment(string $filePath)
    {
        $filePath = urldecode($filePath);
        $fullPath = WRITEPATH . 'uploads/' . $filePath;

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new PageNotFoundException('File not found');
        }

        $file = new File($fullPath);
        $mime = $file->getMimeType();

        return $this->response->setContentType($mime)->setBody(file_get_contents($fullPath));
    }

    /**
     * Get attachments for a reply
     */
    public function getReplyAttachments(string $replyId)
    {
        $attachments = $this->fileAttachmentModel->where('attachable_type', 'reply')
                                               ->where('attachable_id', $replyId)
                                               ->findAll();

        return array_map(function($attachment) {
            return [
                'id'            => $attachment['id'],
                'original_name' => $attachment['original_name'],
                'file_name'     => $attachment['file_name'],
                'file_type'     => $attachment['file_type'],
                'file_size'     => $attachment['file_size'],
                'is_image'      => $attachment['is_image'],
                'download_url'  => $this->getAttachmentDownloadUrl($attachment['file_path']),
                'view_url'      => $this->getAttachmentViewUrl($attachment['file_path']),
                'created_at'    => $attachment['created_at']
            ];
        }, $attachments);
    }

    /**
     * Create a new discussion
     */
    public function createDiscussion(array $data, array $files = [])
    {
        $userId = session()->get('id');
        
        // Authorization check: Only moderators and admins can create discussions
        if (!empty($data['forum_id'])) {
            $forumModeratorModel = new \App\Models\ForumModerator();
            $isModerator = $forumModeratorModel->isModerator($data['forum_id'], $userId);
            $isAdmin = \App\Libraries\CIAuth::isAdmin();
            
            if (!$isModerator && !$isAdmin) {
                throw new \RuntimeException('Only moderators and administrators can create discussions in this forum');
            }
        }
        
        $rules = [
            'title'     => 'required|min_length[5]|max_length[255]',
            'slug'      => 'required|is_unique[discussions.slug]',
            'content'   => 'required|min_length[20]|max_length[1000]',
            'forum_id'  => 'required|is_not_unique[forums.id]'
        ];

        $messages = [
            'title'     => [
                'required' => 'Title is required',
                'min_length' => 'Title must be at least 5 characters long',
                'max_length' => 'Title cannot exceed 255 characters'
            ],
            'slug'      => [
                'required' => 'Slug is required',
                'is_unique' => 'Slug must be unique'
            ],
            'content'   => [
                'required' => 'Content is required',
                'min_length' => 'Content must be at least 20 characters long',
                'max_length' => 'Content cannot exceed 1000 characters'
            ],
            'forum_id'  => [
                'required' => 'Forum ID is required',
                'is_not_unique' => 'Invalid forum selected'
            ]
        ];

        $this->validation->setRules($rules, $messages);
        
        if (!$this->validation->run($data)) {
            throw new ValidationException($this->validation->getErrors());
        }
        $data['user_id'] = $userId;

        // Prepare discussion data
        $discussionData = [
            'title'      => $data['title'],
            'slug'       => $data['slug'],
            'tags'       => json_encode($data['tags']), // store array
            'content'    => $data['content'],
            'forum_id'   => $data['forum_id'],
            'user_id'    => $data['user_id'],
            'tags'       => !empty($data['tags']) ? json_encode(explode(',', $data['tags'])) : null,
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Start transaction
        $db = db_connect();
        $db->transStart();
        
        try {
            // Insert discussion
            $discussionId = $this->discussionModel->insert($discussionData);
            
            if (!$discussionId) {
                throw new \Exception('Failed to create discussion');
            }

            // Handle file attachments - make it more robust
            if (!empty($files)) {
                // Check if we have the expected attachments array
                $attachmentsToProcess = [];
                
                if (isset($files['attachments']) && is_array($files['attachments'])) {
                    $attachmentsToProcess = $files['attachments'];
                } else {
                    // Try to find any uploaded files
                    foreach ($files as $fileArray) {
                        if (is_array($fileArray)) {
                            $attachmentsToProcess = array_merge($attachmentsToProcess, $fileArray);
                        }
                    }
                }
                
                // Filter out empty files
                $attachmentsToProcess = array_filter($attachmentsToProcess, function($file) {
                    return $file->getError() !== UPLOAD_ERR_NO_FILE;
                });
                
                if (!empty($attachmentsToProcess)) {
                    $uploadResult = $this->handleAttachments($attachmentsToProcess, $discussionId, $userId);
                    
                    if (isset($uploadResult['success']) && !$uploadResult['success']) {
                        throw new \Exception($uploadResult['message']);
                    }
                    
                    $uploadedAttachments = $uploadResult;
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Get the created discussion
            $discussion = $this->discussionModel->find($discussionId);
            
            return [
                'discussion'    => $discussion,
            ];
            
        } catch (\Exception $e) {
            $db->transRollback();

            // Clean up any uploaded files if transaction failed
            if (!empty($uploadedAttachments)) {
                foreach ($uploadedAttachments as $attachment) {
                    if (isset($attachment['file_path'])) {
                        $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }

            throw $e->getMessage();
        }
    }

    /**
     * Handle file attachments for a discussion
     */
    protected function handleAttachments(array $files, string $discussionId, string $userId)
    {
        $maxSize             = 5 * 1024 * 1024; // 5MB
        $maxFiles            = 5;
        $uploadedAttachments = [];
        $errors              = [];

        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        if (count($files) > $maxFiles) {
            return [
                'success' => false,
                'message' => "Maximum {$maxFiles} files allowed"
            ];
        }

        foreach ($files as $file) {
            if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if (!$file->isValid() || $file->hasMoved()) {
                $errors[] = 'Invalid file uploaded: ' . $file->getErrorString();
                continue;
            }
            // Validate file size
            if ($file->getSize() > $maxSize) {
                $errors[] = "File '{$file->getClientName()}' exceeds 5MB limit";
                continue;
            }
            // Validate file type
            $clientMime = $file->getClientMimeType();
            if (!in_array($clientMime, $allowedTypes)) {
                $errors[] = "File type not supported: {$file->getClientName()}";
                continue;
            }
            // Generate unique filename and path
            $newName        = $file->getRandomName();
            $uploadPath     = 'discussions/' . date('Y/m');
            $fullUploadPath = WRITEPATH . 'uploads/' . $uploadPath;
            if (!is_dir($fullUploadPath)) {
                mkdir($fullUploadPath, 0755, true);
            }
            if ($file->move($fullUploadPath, $newName)) {
                $attachmentData = [
                    'attachable_type'   => 'discussion',
                    'attachable_id'     => $discussionId,
                    'user_id'           => $userId,
                    'original_name'     => $file->getClientName(),
                    'file_name'         => $newName,
                    'file_path'         => $uploadPath . '/' . $newName,
                    'file_type'         => $file->getClientExtension(),
                    'file_size'         => $file->getSize(),
                    'mime_type'         => $file->getClientMimeType(),
                    'download_count'    => 0,
                ];
                try {
                    if ($this->fileAttachmentModel->insert($attachmentData)) {
                        $attachmentId = $this->fileAttachmentModel->getInsertID();
                        $uploadedAttachments[] = [
                            'id'            => $attachmentId,
                            'original_name' => $file->getClientName(),
                            'file_name'     => $newName,
                            'file_path'     => $uploadPath . '/' . $newName,
                            'file_type'     => $file->getClientExtension(),
                            'file_size'     => $file->getSize(),
                            'mime_type'     => $file->getClientMimeType(),
                            'is_image'      => strpos($file->getClientMimeType(), 'image/') === 0,
                            'download_url'  => $this->getAttachmentDownloadUrl($uploadPath . '/' . $newName),
                            'view_url'      => $this->getAttachmentViewUrl($uploadPath . '/' . $newName)
                        ];
                    } else {
                        unlink($fullUploadPath . '/' . $newName);
                        $errors[] = "Failed to save attachment: {$file->getClientName()}";
                    }
                } catch (\Exception $e) {
                    unlink($fullUploadPath . '/' . $newName);
                    $errors[] = "Database error: {$file->getClientName()}";
                }
            } else {
                $errors[] = "Failed to upload: {$file->getClientName()}";
            }
        }

        if (!empty($errors)) {
            foreach ($uploadedAttachments as $attachment) {
                $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return [
                'success' => false,
                'message' => implode(', ', $errors)
            ];
        }

        return $uploadedAttachments;
    }
}