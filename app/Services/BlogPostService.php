<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\BlogPost;
use App\Models\BlogPostTag;
use App\Models\FileAttachment;
use CodeIgniter\Validation\Validation;
use App\Exceptions\ValidationException;

class BlogPostService
{
    protected $validation;
    protected $blogPostModel;
    protected $blogPostTagModel;
    protected $fileAttachmentModel;

    public function __construct()
    {
        $this->blogPostModel        = new BlogPost();
        $this->validation           = \Config\Services::validation();
        $this->blogPostTagModel     = new BlogPostTag();
        $this->fileAttachmentModel  = new FileAttachment();
    }

    public function createBlogPost(array $formData, array $files): string
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Track uploaded files for potential rollback
        $uploadedFiles = [];
        
        try {
            // Set validation rules
            $rules = [
                'title'         => 'required|min_length[3]|max_length[255]',
                'slug'          => 'required|min_length[3]|max_length[255]|is_unique[blog_posts.slug]',
                'category'      => 'required|string|is_not_unique[blog_categories.id]',
                'excerpt'       => 'permit_empty|max_length[1000]',
                'content'       => 'required',
                'status'        => 'required|in_list[draft,published,archived]',
            ];

            $messages = [
                'title' => [
                    'required' => 'Title is required',
                    'min_length' => 'Title must be at least 3 characters long',
                    'max_length' => 'Title cannot exceed 255 characters'
                ],
                'slug' => [
                    'required' => 'Slug is required',
                    'min_length' => 'Slug must be at least 3 characters long',
                    'max_length' => 'Slug cannot exceed 255 characters',
                    'is_unique' => 'Slug must be unique'
                ],
                'category' => [
                    'required' => 'Category is required',
                    'string' => 'Category must be a valid selection',
                    'is_not_unique' => 'Category must exist. The selected category is not in the system'
                ],
                'excerpt' => [
                    'max_length' => 'Excerpt cannot exceed 1000 characters'
                ],
                'content' => [
                    'required' => 'Content is required'
                ],
                'status' => [
                    'required' => 'Status is required',
                    'in_list' => 'Status must be draft, published, or archived'
                ]
            ];

            $this->validation->setRules($rules, $messages);
        
            if (!$this->validation->run($formData)) {
                throw new ValidationException($this->validation->getErrors());
            }

            // Process featured image and additional images
            $featuredImage = null;
            if (!empty($files['featured_image'])) {
                $featuredImageResult = $this->processFeaturedImage($files);
                $featuredImage = $featuredImageResult['data'];
                $uploadedFiles = array_merge($uploadedFiles, $featuredImageResult['files']);
            }

            $additionalImages = [];
            if (!empty($files['additional_images'])) {
                $additionalImagesResult = $this->processImages($files);
                $additionalImages = $additionalImagesResult['data'];
                $uploadedFiles = array_merge($uploadedFiles, $additionalImagesResult['files']);
            }

            // Generate UUID for blog post
            $blogPostId = \Ramsey\Uuid\Uuid::uuid4()->toString();

            // Determine status and published_at
            $status = $formData['status'] ?? 'draft';
            $publishedAt = null;

            // Set published_at if status is published
            if ($status === 'published') {
                $publishedAt = !empty($formData['published_at']) && $formData['published_at'] !== ''
                    ? $formData['published_at']
                    : Carbon::now()->toDateTimeString();
            }

            $insertData = [
                'id'                => $blogPostId,
                'title'             => $formData['title'],
                'slug'              => $formData['slug'],
                'user_id'           => session()->get('id'),
                'category_id'       => $formData['category'],
                'excerpt'           => $formData['excerpt'] ?? '',
                'content'           => $formData['content'],
                'featured_image'    => $featuredImage['file_path'] ?? null,
                'meta_title'        => $formData['meta_title'] ?? null,
                'meta_description'  => $formData['meta_description'] ?? null,
                'meta_keywords'     => $formData['meta_keywords'] ?? null,
                'status'            => $status,
                'reading_time'      => (int)($formData['reading_time'] ?? 5),
                'is_featured'       => isset($formData['is_featured']) ? 1 : 0,
                'published_at'      => $publishedAt,
                'created_at'        => Carbon::now()->toDateTimeString(),
                'updated_at'        => Carbon::now()->toDateTimeString()
            ];

            // Insert blog post
            $result = $this->blogPostModel->insert($insertData);

            if (!$result) {
                throw new \RuntimeException('Failed to create blog post due to database error.');
            }

            // Handle tags if they exist
            if (!empty($formData['tags'])) {
                $tags = is_string($formData['tags']) ? json_decode($formData['tags'], true) : $formData['tags'];
                if (is_array($tags) && !empty($tags)) {
                    $this->createBlogPostTags($tags, $blogPostId);
                }
            }

            // Store additional images as file attachments
            if (!empty($additionalImages)) {
                foreach ($additionalImages as $image) {
                    $this->fileAttachmentModel->insert([
                        'attachable_type' => 'blog_posts',
                        'attachable_id' => $blogPostId,
                        'original_name' => $image['original_name'] ?? $image['file_name'],
                        'file_name' => $image['file_name'],
                        'file_path' => $image['file_path'],
                        'file_type' => $image['mime_type'],
                        'mime_type' => $image['mime_type'],
                        'file_size' => $image['file_size']
                    ]);
                }
            }

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                // Transaction failed, rollback uploaded files
                $this->rollbackUploadedFiles($uploadedFiles);
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }

            return $blogPostId;
            
        } catch (ValidationException $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();
            $this->rollbackUploadedFiles($uploadedFiles);
            
            log_message('error', 'Blog post validation failed: ' . json_encode($e->getErrors()));
            // Re-throw ValidationException to preserve validation details
            throw $e;
        } catch (\Exception $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();
            $this->rollbackUploadedFiles($uploadedFiles);
            
            log_message('error', 'Blog post creation failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to create blog post: ' . $e->getMessage());
        }
    }

    public function updateBlogPost(string $id, array $formData, array $files): string
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $uploadedFiles = [];

        try {
            // Get existing blog post
            $existingBlogPost = $this->blogPostModel->find($id);
            if (!$existingBlogPost) {
                throw new \RuntimeException('Blog post not found');
            }

            // Convert to array if it's an object
            if (is_object($existingBlogPost)) {
                $existingBlogPost = (array) $existingBlogPost;
            }

            // Handle featured image removal
            $removeFeaturedImage = false;
            if (!empty($formData['remove_featured_image']) && $formData['remove_featured_image'] === '1') {
                $removeFeaturedImage = true;

                // Delete existing featured image file
                if (!empty($existingBlogPost['featured_image'])) {
                    $this->deleteImageFile($existingBlogPost['featured_image']);
                }
            }

            // Process featured image if uploaded
            $featuredImage = null;
            if (!empty($files['featured_image'])) {
                $featuredImageResult = $this->processFeaturedImage($files);
                $featuredImage = $featuredImageResult['data'];
                $uploadedFiles = array_merge($uploadedFiles, $featuredImageResult['files']);

                // Delete old featured image file if exists (being replaced)
                if (!empty($existingBlogPost['featured_image'])) {
                    $this->deleteImageFile($existingBlogPost['featured_image']);
                }
            }

            // Handle deleted images
            $deletedImages = [];
            if (!empty($formData['deleted_images'])) {
                $deletedImages = is_string($formData['deleted_images'])
                    ? json_decode($formData['deleted_images'], true)
                    : $formData['deleted_images'];

                if (is_array($deletedImages) && !empty($deletedImages)) {
                    foreach ($deletedImages as $imageId) {
                        // Get the attachment to delete the physical file
                        $attachment = $this->fileAttachmentModel->find($imageId);

                        if ($attachment) {
                            // Convert to array if it's an object
                            if (is_object($attachment)) {
                                $attachment = json_decode(json_encode($attachment), true);
                            }

                            // Delete physical file using helper function
                            if (!empty($attachment['file_path'])) {
                                $this->deleteImageFile($attachment['file_path']);
                            }

                            // Delete from database
                            $this->fileAttachmentModel->delete($imageId);
                            log_message('info', 'Deleted file attachment: ' . $imageId);
                        }
                    }
                }
            }

            // Process additional images if uploaded
            $additionalImages = [];
            if (!empty($files['additional_images'])) {
                $additionalImagesResult = $this->processImages($files);
                $additionalImages = $additionalImagesResult['data'];
                $uploadedFiles = array_merge($uploadedFiles, $additionalImagesResult['files']);
            }

            // Determine status and published_at
            $status = $formData['status'] ?? $existingBlogPost['status'];
            $publishedAt = $existingBlogPost['published_at'];

            // Set published_at if status is published
            if ($status === 'published') {
                if (empty($publishedAt) || $publishedAt === '0000-00-00 00:00:00') {
                    $publishedAt = !empty($formData['published_at']) && $formData['published_at'] !== ''
                        ? $formData['published_at']
                        : Carbon::now()->toDateTimeString();
                }
            }

            $updateData = [
                'title'             => $formData['title'],
                'slug'              => $formData['slug'],
                'category_id'       => $formData['category'],
                'excerpt'           => $formData['excerpt'] ?? '',
                'content'           => $formData['content'],
                'meta_title'        => $formData['meta_title'] ?? null,
                'meta_description'  => $formData['meta_description'] ?? null,
                'meta_keywords'     => $formData['meta_keywords'] ?? null,
                'status'            => $status,
                'reading_time'      => (int)($formData['reading_time'] ?? 5),
                'is_featured'       => isset($formData['is_featured']) ? 1 : 0,
                'published_at'      => $publishedAt,
                'updated_at'        => Carbon::now()->toDateTimeString()
            ];

            // Update featured image if uploaded
            if ($featuredImage) {
                $updateData['featured_image'] = $featuredImage['file_path'];
            } elseif ($removeFeaturedImage) {
                // Set featured image to null if marked for removal
                $updateData['featured_image'] = null;
            }

            // Update blog post
            $result = $this->blogPostModel->update($id, $updateData);

            if (!$result) {
                throw new \RuntimeException('Failed to update blog post due to database error.');
            }

            // Delete existing tags
            $this->blogPostTagModel->where('post_id', $id)->delete();

            // Handle tags if they exist
            if (!empty($formData['tags'])) {
                $tags = is_string($formData['tags']) ? json_decode($formData['tags'], true) : $formData['tags'];
                if (is_array($tags) && !empty($tags)) {
                    $this->createBlogPostTags($tags, $id);
                }
            }

            // Store new additional images as file attachments
            if (!empty($additionalImages)) {
                foreach ($additionalImages as $image) {
                    $this->fileAttachmentModel->insert([
                        'attachable_type' => 'blog_posts',
                        'attachable_id' => $id,
                        'original_name' => $image['original_name'] ?? $image['file_name'],
                        'file_name' => $image['file_name'],
                        'file_path' => $image['file_path'],
                        'file_type' => $image['mime_type'],
                        'mime_type' => $image['mime_type'],
                        'file_size' => $image['file_size']
                    ]);
                }
            }

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                // Transaction failed, rollback uploaded files
                $this->rollbackUploadedFiles($uploadedFiles);
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }

            return $id;

        } catch (ValidationException $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();
            $this->rollbackUploadedFiles($uploadedFiles);

            log_message('error', 'Blog post validation failed: ' . json_encode($e->getErrors()));
            // Re-throw ValidationException to preserve validation details
            throw $e;
        } catch (\Exception $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();
            $this->rollbackUploadedFiles($uploadedFiles);

            log_message('error', 'Blog post update failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update blog post: ' . $e->getMessage());
        }
    }

    protected function createBlogPostTags(array $tags, string $blogPostId): void
    {
        $blogTagModel = new \App\Models\BlogTag();

        foreach ($tags as $tagName) {
            if (!empty($tagName)) {
                // Check if tag name is actually an ID (UUID format)
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $tagName)) {
                    // It's already a tag ID
                    $tagId = $tagName;
                } else {
                    // It's a tag name, find or create the tag
                    $slug = $this->createSlug($tagName);
                    $existingTag = $blogTagModel->where('slug', $slug)->first();

                    if ($existingTag) {
                        $tagId = $existingTag['id'];
                    } else {
                        // Create new tag
                        $tagId = \Ramsey\Uuid\Uuid::uuid4()->toString();
                        $blogTagModel->insert([
                            'id' => $tagId,
                            'name' => $tagName,
                            'slug' => $slug
                        ]);
                    }
                }

                // Create the post-tag relationship
                $this->blogPostTagModel->insert([
                    'post_id' => $blogPostId,
                    'tag_id' => $tagId
                ]);
            }
        }
    }

    protected function createSlug($name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        return $slug;
    }

    protected function processImages(array $files): array
    {
        $images = [];
        $uploadedFiles = [];
        
        if (!empty($files['additional_images'])) {
            foreach ($files['additional_images'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    // Generate a new filename
                    $newName = $file->getRandomName();
                    $uploadPath = FCPATH . 'uploads/blog_images';
                    $fullPath = $uploadPath . '/' . $newName;

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    // Move file to upload directory
                    if (!$file->hasMoved()) {
                        $file->move($uploadPath, $newName);
                        $uploadedFiles[] = $fullPath; // Track uploaded file path
                    }

                    // Determine if file is an image
                    $mimeType = $file->getClientMimeType();
                    $isImage = strpos($mimeType, 'image/') === 0;

                    if ($isImage) {
                        $images[] = [
                            'original_name' => $file->getClientName(),
                            'file_name' => $newName,
                            'file_path' => "uploads/blog_images/{$newName}",
                            'mime_type' => $mimeType,
                            'file_size' => $file->getSize(),
                        ];
                    }
                }
            }
        }

        return [
            'data' => $images,
            'files' => $uploadedFiles
        ];
    }

    protected function processFeaturedImage(array $files): ?array
    {
        $uploadedFiles = [];
        
        if (!empty($files['featured_image'])) {
            $file = is_array($files['featured_image']) ? $files['featured_image'][0] : $files['featured_image'];

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $uploadPath = FCPATH . 'uploads/blog_images/featured';
                $fullPath = $uploadPath . '/' . $newName;

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                if (!$file->hasMoved()) {
                    $file->move($uploadPath, $newName);
                    $uploadedFiles[] = $fullPath; // Track uploaded file path
                }

                $mimeType = $file->getClientMimeType();
                $isImage = strpos($mimeType, 'image/') === 0;

                if ($isImage) {
                    return [
                        'data' => [
                            'file_name' => $newName,
                            'file_path' => "uploads/blog_images/featured/{$newName}",
                            'mime_type' => $mimeType,
                            'file_size' => $file->getSize(),
                        ],
                        'files' => $uploadedFiles
                    ];
                }
            }
        }

        return [
            'data' => null,
            'files' => $uploadedFiles
        ];
    }

    protected function rollbackUploadedFiles(array $filePaths): void
    {
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
                log_message('info', 'Rolled back uploaded file: ' . $filePath);
            }
        }
    }

    /**
     * Delete an image file from the server
     * Handles both full URLs and relative paths
     *
     * @param string $imagePath The image path (can be full URL or relative path)
     * @return bool True if file was deleted or doesn't exist, false on error
     */
    protected function deleteImageFile(string $imagePath): bool
    {
        if (empty($imagePath)) {
            return true;
        }

        // If it's a full URL, extract the path
        if (strpos($imagePath, 'http') === 0) {
            $parsedUrl = parse_url($imagePath);
            if (isset($parsedUrl['path'])) {
                // Remove leading slash and use as relative path
                $imagePath = ltrim($parsedUrl['path'], '/');
            }
        }

        // Build full file path
        $fullPath = FCPATH . $imagePath;

        // Delete the file if it exists
        if (file_exists($fullPath)) {
            $deleted = @unlink($fullPath);
            if ($deleted) {
                log_message('info', 'Deleted image file: ' . $imagePath);
                return true;
            } else {
                log_message('error', 'Failed to delete image file: ' . $imagePath);
                return false;
            }
        }

        // File doesn't exist, which is fine
        log_message('debug', 'Image file does not exist (already deleted?): ' . $imagePath);
        return true;
    }
}