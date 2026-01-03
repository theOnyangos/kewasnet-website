<?php

namespace App\Services;

use App\Models\Pillar;
use App\Models\Resource;
use App\Models\Contributor;
use App\Models\DocumentType;
use App\Models\FileAttachment;
use App\Models\ResourceCategory;
use App\Models\ResourceContributor;
use CodeIgniter\Validation\Validation;
use App\Exceptions\ValidationException;

class PillarService
{
    protected $validation;
    protected $pillarModel;
    protected $resourceModel;
    protected $contributorModel;
    protected $resourceCategoryModel;
    protected $resourceContributorModel;
    protected $documentTypeModel;
    protected $fileAttachmentModel;

    public function __construct()
    {
        $this->pillarModel              = new Pillar();
        $this->resourceModel            = new Resource();
        $this->contributorModel         = new Contributor();
        $this->resourceContributorModel = new ResourceContributor();
        $this->validation               = \Config\Services::validation();
        $this->resourceCategoryModel    = new ResourceCategory();
        $this->documentTypeModel        = new DocumentType();
        $this->fileAttachmentModel      = new FileAttachment();
    }

    public function createPillar(array $formData, array $files): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Track uploaded files for potential rollback
        $uploadedFiles = [];
        
        try {
            // Set validation rules based on Pillar model
            $rules = [
                'title'         => 'required|max_length[100]',
                'slug'          => 'required|max_length[50]|is_unique[pillars.slug]',
                'icon'          => 'permit_empty|max_length[255]',
                'description'   => 'permit_empty|max_length[1000]',
                'content'       => 'permit_empty',
                'button_text'   => 'permit_empty|max_length[100]',
                'button_link'   => 'permit_empty|valid_url_strict',
                'is_private'    => 'permit_empty|in_list[0,1]',
                'is_active'     => 'permit_empty|in_list[0,1]',
                'meta_title'    => 'permit_empty|max_length[255]',
                'meta_description' => 'permit_empty|max_length[500]',
            ];

            $messages = [
                'title' => [
                    'required' => 'Title is required',
                    'max_length' => 'Title cannot exceed 100 characters'
                ],
                'slug' => [
                    'required' => 'Slug is required',
                    'max_length' => 'Slug cannot exceed 50 characters',
                    'is_unique' => 'Slug must be unique'
                ],
                'icon' => [
                    'max_length' => 'Icon class cannot exceed 255 characters'
                ],
                'description' => [
                    'max_length' => 'Description cannot exceed 1000 characters'
                ],
                'button_text' => [
                    'max_length' => 'Button text cannot exceed 100 characters'
                ],
                'button_link' => [
                    'valid_url_strict' => 'Button link must be a valid URL'
                ],
                'is_private' => [
                    'in_list' => 'Privacy setting must be 0 (public) or 1 (private)'
                ],
                'is_active' => [
                    'in_list' => 'Status must be 0 (inactive) or 1 (active)'
                ],
                'meta_title' => [
                    'max_length' => 'Meta title cannot exceed 255 characters'
                ],
                'meta_description' => [
                    'max_length' => 'Meta description cannot exceed 500 characters'
                ]
            ];

            $this->validation->setRules($rules, $messages);
        
            if (!$this->validation->run($formData)) {
                $validationErrors = $this->validation->getErrors();
                log_message('error', 'Pillar validation errors: ' . json_encode($validationErrors));
                throw new ValidationException($validationErrors);
            }

            // Process pillar image
            $imagePath = null;
            if (!empty($files['pillar_image'])) {
                $imageResult = $this->processPillarImage($files);
                $imagePath = $imageResult['data'];
                $uploadedFiles = array_merge($uploadedFiles, $imageResult['files']);
            }

            $insertData = [
                'user_id'           => session()->get('id'), // Fallback to admin user
                'title'             => $formData['title'],
                'slug'              => $formData['slug'],
                'icon'              => $formData['icon'] ?? null,
                'description'       => $formData['description'] ?? '',
                'content'           => $formData['content'] ?? '',
                'image_path'        => $imagePath,
                'button_text'       => $formData['button_text'] ?? 'Explore Resources',
                'button_link'       => $formData['button_link'] ?? null,
                'is_private'        => isset($formData['is_private']) ? (int)$formData['is_private'] : 0,
                'is_active'         => isset($formData['is_active']) ? (int)$formData['is_active'] : 1,
                'meta_title'        => $formData['meta_title'] ?? null,
                'meta_description'  => $formData['meta_description'] ?? null
            ];

            // Debug log the data being inserted
            log_message('debug', 'Pillar insert data: ' . json_encode($insertData, JSON_PRETTY_PRINT));

            // Insert the pillar data into the database
            $insertId = $this->pillarModel->createPillar($insertData);

            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                // Transaction failed, rollback uploaded files
                $this->rollbackUploadedFiles($uploadedFiles);
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }

            return $this->pillarModel->find($insertId);
            
        } catch (ValidationException $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();
            $this->rollbackUploadedFiles($uploadedFiles);
            
            log_message('error', 'Pillar validation failed: ' . json_encode($e->getErrors()));
            // Re-throw ValidationException to preserve validation details
            throw $e;
        } catch (\Exception $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();
            $this->rollbackUploadedFiles($uploadedFiles);
            
            log_message('error', 'Pillar creation failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to create pillar: ' . $e->getMessage());
        }
    }

    public function createResourceCategory(array $formData): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Set validation rules for resource category
            $rules = [
                'name'          => 'required|max_length[255]',
                'pillar_id'    => 'required|is_not_unique[pillars.id]',
                'slug'          => 'required|max_length[100]|is_unique[resource_categories.slug]',
                'description'   => 'permit_empty|max_length[1000]',
            ];

            $messages = [
                'name' => [
                    'required'      => 'Category name is required.',
                    'max_length'    => 'Category name cannot exceed 255 characters.',
                ],
                'slug' => [
                    'required'      => 'URL slug is required.',
                    'max_length'    => 'URL slug cannot exceed 100 characters.',
                    'is_unique'     => 'URL slug must be unique.',
                ],
                'description' => [
                    'max_length'    => 'Description cannot exceed 1000 characters.',
                ],
                'pillar_id' => [
                    'required'      => 'Pillar is required.',
                    'is_not_unique' => 'Selected pillar does not exist.',
                ],
            ];

            $this->validation->setRules($rules, $messages);

            if (!$this->validation->run($formData)) {
                $validationErrors = $this->validation->getErrors();
                throw new ValidationException($validationErrors);
            }

            // Prepare data for insertion
            $insertData = [
                'name'        => $formData['name'],
                'slug'        => $formData['slug'],
                'pillar_id'   => $formData['pillar_id'] ?? null,
                'description' => $formData['description'] ?? '',
            ];

            // Insert the resource category into the database
            $insertId = $this->resourceCategoryModel->createResourceCategory($insertData);

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }

            return $this->resourceCategoryModel->find($insertId);

        } catch (ValidationException $e) {
            // Rollback transaction
            $db->transRollback();

            log_message('error', 'Resource category validation failed: ' . json_encode($e->getErrors()));
            // Re-throw ValidationException to preserve validation details
            throw $e;
        } catch (\Exception $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();

            log_message('error', 'Resource category creation failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to create resource category: ' . $e->getMessage());
        }
    }

    public function deleteResourceCategory(string $id): bool
    {
        return $this->resourceCategoryModel->delete($id); // TODO: there is more to handle before delete.
    }

    public function createDocumentType(array $formData): array
    {
        try {
            // Start database transaction
            $db = \Config\Database::connect();
            $db->transStart();
    
            // Set validation Rules for document type
            $rules = [
                'name'   => 'required|max_length[255]',
                'slug'   => 'required|max_length[100]|is_unique[resource_categories.slug]',
                'color'  => 'required',
            ];
    
            $messages = [
                'name' => [
                    'required'   => 'Category name is required.',
                    'max_length' => 'Category name cannot exceed 255 characters.',
                ],
                'slug' => [
                    'required'   => 'URL slug is required.',
                    'max_length' => 'URL slug cannot exceed 100 characters.',
                    'is_unique'  => 'URL slug must be unique.',
                ],
                'color' => [
                    'required'   => 'Color is required.',
                ],
            ];
    
            $this->validation->setRules($rules, $messages);
    
            if (!$this->validation->run($formData)) {
                $validationErrors = $this->validation->getErrors();
                throw new ValidationException($validationErrors);
            }
    
            // Prepare data for insertion
            $insertData = [
                'name'  => $formData['name'],
                'slug'  => $formData['slug'],
                'color' => $formData['color'] ?? null,
            ];
    
            $insertId = $this->documentTypeModel->createDocumentType($insertData);
    
            // Complete transaction
            $db->transComplete();
    
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }
    
            return $this->documentTypeModel->find($insertId);
        } catch (ValidationException $e) {
            // Rollback transaction
            $db->transRollback();

            log_message('error', 'Document type validation failed: ' . json_encode($e->getErrors()));
            // Re-throw ValidationException to preserve validation details
            throw $e;
        } catch (\Exception $e) {
            // Rollback transaction and uploaded files
            $db->transRollback();

            log_message('error', 'Document type creation failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to create document type: ' . $e->getMessage());
        }
    }

    public function deleteDocumentType(string $id): bool
    {
        return $this->documentTypeModel->delete($id); // TODO: there is more to handle before delete.
    }

    public function createPillarArticle(array $formData, array $files, array $contributors = []): object
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Track uploaded files for potential rollback
        $uploadedFiles = [];
        $uploadedAttachments = [];
        
        try {
            // Set validation rules for resource/article
            $rules = [
                'pillar_id'         => 'required|is_not_unique[pillars.id]',
                'document_type_id'  => 'required|is_not_unique[document_types.id]',
                'category_id'       => 'permit_empty|is_not_unique[resource_categories.id]',
                'title'             => 'required|max_length[255]',
                'slug'              => 'required|max_length[100]|is_unique[resources.slug]',
                'description'       => 'permit_empty|max_length[1000]',
                'content'           => 'required',
                'publication_year'  => 'permit_empty|integer|greater_than[1900]|less_than_equal_to[' . (date('Y') + 1) . ']',
                'is_featured'       => 'permit_empty|in_list[0,1]',
                'is_published'      => 'permit_empty|in_list[0,1]',
                'meta_title'        => 'permit_empty|max_length[60]',
                'meta_description'  => 'permit_empty|max_length[160]',
            ];

            $messages = [
                'pillar_id' => [
                    'required'      => 'Please select a pillar',
                    'is_not_unique' => 'Selected pillar does not exist'
                ],
                'document_type_id' => [
                    'required'      => 'Please select a document type',
                    'is_not_unique' => 'Selected document type does not exist'
                ],
                'title' => [
                    'required'      => 'Title is required',
                    'max_length'    => 'Title cannot exceed 255 characters'
                ],
                'slug' => [
                    'required'      => 'URL slug is required',
                    'max_length'    => 'URL slug cannot exceed 100 characters',
                    'is_unique'     => 'This URL slug is already taken'
                ],
                'content' => [
                    'required'      => 'Article content is required'
                ]
            ];

            $this->validation->setRules($rules, $messages);

            if (!$this->validation->run($formData)) {
                throw new ValidationException($this->validation->getErrors());
            }

            $validatedData = $this->validation->getValidated();

            // Handle image upload (featured image) - store in public directory
            $imagePath = null;
            if (isset($files['image_file']) && $files['image_file']->isValid()) {
                $imageUpload = $this->handleResourceImageUpload($files['image_file']);
                if ($imageUpload['data']) {
                    $imagePath      = $imageUpload['data'];
                    $uploadedFiles  = array_merge($uploadedFiles, $imageUpload['files']);
                }
            }

            // Prepare data for insertion
            $insertData = [
                'pillar_id'         => $validatedData['pillar_id'],
                'category_id'       => $validatedData['category_id'] ?? null,
                'document_type_id'  => $validatedData['document_type_id'],
                'title'             => $validatedData['title'],
                'slug'              => $validatedData['slug'],
                'description'       => $validatedData['description'] ?? null,
                'content'           => $validatedData['content'],
                'image_url'         => base_url($imagePath),
                'file_url'          => null,
                'file_size'         => null,
                'file_type'         => null,
                'publication_year'  => $validatedData['publication_year'] ?? date('Y'),
                'is_featured'       => $validatedData['is_featured'] ?? 0,
                'is_published'      => $validatedData['is_published'] ?? 1,
                'meta_title'        => $validatedData['meta_title'] ?? null,
                'meta_description'  => $validatedData['meta_description'] ?? null,
                'created_by'        => session()->get('id') ?? session()->get('user_id'),
                'download_count'    => 0,
                'view_count'        => 0,
            ];
            
            // Insert the resource
            $result = $this->resourceModel->insert($insertData);
            
            if (!$result) {
                $errors = $this->resourceModel->errors();
                throw new \RuntimeException('Failed to create resource: ' . json_encode($errors));
            }

            // Get the inserted ID
            $resourceId = $this->resourceModel->getInsertID();
            
            if (!$resourceId) {
                throw new \RuntimeException('Failed to get resource ID after insertion');
            }

            // Handle resource file attachments - store in writable directory
            if (isset($files['resource_file']) && $files['resource_file']->isValid()) {
                $fileUploadResult = $this->processResourceFiles($resourceId, $files['resource_file']);
                
                if (!$fileUploadResult['success']) {
                    throw new \RuntimeException('File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                }
                
                $uploadedAttachments = $fileUploadResult['uploaded_files'];
            }

            // Handle contributors
            if (!empty($contributors)) {
                $this->handleContributors($resourceId, $contributors);
            }

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                $this->rollbackUploadedFiles($uploadedFiles);
                $this->rollbackUploadedFiles($uploadedAttachments);
                throw new \RuntimeException('Database transaction failed');
            }

            // Get the created resource
            $resource = $this->resourceModel->find($resourceId);            
            return $resource;

        } catch (\Exception $e) {
            $db->transRollback();
            
            // Clean up any uploaded files if transaction failed
            $this->rollbackUploadedFiles($uploadedFiles);
            $this->rollbackUploadedFiles($uploadedAttachments);
            
            log_message('error', 'Pillar article creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deletePillarArticle(string $articleId): bool
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // First, get the article to check existence and get file paths
            $article = $this->resourceModel->find($articleId);
            
            if (!$article) {
                throw new \RuntimeException('Article not found');
            }
            
            // Get all file attachments for this article
            $attachments = $this->fileAttachmentModel
                ->where('attachable_id', $articleId)
                ->where('attachable_type', 'resources')
                ->findAll();
            
            $filesToDelete = [];
            
            // Delete file attachment records and collect file paths for deletion
            foreach ($attachments as $attachment) {
                // Store file path for later deletion
                if (!empty($attachment['file_path'])) {
                    $filesToDelete[] = WRITEPATH . $attachment['file_path'];
                }
                
                // Delete the attachment record
                if (!$this->fileAttachmentModel->delete($attachment['id'])) {
                    throw new \RuntimeException('Failed to delete file attachment record');
                }
            }
            
            // Delete resource-contributor relationships
            $this->resourceContributorModel
                ->where('resource_id', $articleId)
                ->delete();
            
            // Delete the main article record
            $deleteResult = $this->resourceModel->delete($articleId);
            
            if (!$deleteResult) {
                throw new \RuntimeException('Failed to delete article record');
            }
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }
            
            // Delete physical files after successful transaction
            $this->deletePhysicalFiles($filesToDelete);
            
            log_message('info', 'Pillar article deleted successfully: ' . $articleId);
            return true;
            
        } catch (\Exception $e) {
            // Rollback transaction
            $db->transRollback();
            
            log_message('error', 'Failed to delete pillar article ' . $articleId . ': ' . $e->getMessage());
            throw new \RuntimeException('Failed to delete article: ' . $e->getMessage());
        }
    }

    protected function deletePhysicalFiles(array $filePaths): void
    {
        foreach ($filePaths as $filePath) {
            try {
                if (file_exists($filePath) && is_file($filePath)) {
                    if (unlink($filePath)) {
                        log_message('info', 'Deleted file: ' . $filePath);
                    } else {
                        log_message('warning', 'Failed to delete file: ' . $filePath);
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Error deleting file ' . $filePath . ': ' . $e->getMessage());
            }
        }
    }

    protected function processResourceFiles(string $resourceId, $file): array
    {
        $errors = [];
        $uploadedFiles = [];

        try {
            if ($file->isValid() && !$file->hasMoved()) {
                // Generate unique filename and path
                $newName        = $file->getRandomName();
                $uploadPath     = 'resources/' . date('Y-m-d');
                $fullUploadPath = WRITEPATH . 'uploads/' . $uploadPath;

                // Validate file type and size
                $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
                $maxSize = 50 * 1024 * 1024; // 50MB
                
                $fileExtension = strtolower($file->getClientExtension());
                if (!in_array($fileExtension, $allowedTypes)) {
                    throw new \RuntimeException('Invalid file type. Allowed: ' . implode(', ', $allowedTypes));
                }
                
                if ($file->getSize() > $maxSize) {
                    throw new \RuntimeException('File too large. Maximum size: 50MB');
                }

                // Create directory if it doesn't exist
                if (!is_dir($fullUploadPath)) {
                    mkdir($fullUploadPath, 0755, true);
                }
                
                // Move file to upload directory
                if ($file->move($fullUploadPath, $newName)) {
                    $uploadedFiles[] = $fullUploadPath . '/' . $newName;
                    
                    // Determine if file is an image
                    $mimeType = $file->getClientMimeType();
                    $isImage = strpos($mimeType, 'image/') === 0;
                    
                    // Save attachment record
                    $attachmentData = [
                        'attachable_id'     => $resourceId,
                        'attachable_type'   => 'resources',
                        'original_name'     => $file->getClientName(),
                        'file_name'         => $newName,
                        'file_path'         => $uploadPath . '/' . $newName,
                        'file_type'         => $mimeType,
                        'file_size'         => $file->getSize(),
                        'mime_type'         => $mimeType,
                        'is_image'          => $isImage ? 1 : 0,
                        'created_at'        => date('Y-m-d H:i:s')
                    ];
                    
                    if (!$this->fileAttachmentModel->insert($attachmentData)) {
                        throw new \RuntimeException('Failed to save file attachment: ' . json_encode($this->fileAttachmentModel->errors()));
                    }
                    
                    return [
                        'success' => true,
                        'uploaded_files' => $uploadedFiles
                    ];
                } else {
                    throw new \RuntimeException('Failed to move uploaded file: ' . $file->getClientName());
                }
            } else {
                $errors[] = 'File upload error: ' . $file->getErrorString();
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        return [
            'success'        => false,
            'errors'         => $errors,
            'uploaded_files' => $uploadedFiles
        ];
    }

    protected function handleResourceImageUpload($file): array
    {
        $uploadedFiles = [];
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            try {
                // Store images in public directory for better performance
                $uploadPath = FCPATH . 'uploads/resources/images/' . date('Y-m-d');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Generate unique filename
                $newName  = uniqid() . '_' . time() . '.' . $file->getClientExtension();
                $fullPath = $uploadPath . '/' . $newName;
                
                // Validate file type and size
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $maxSize      = 10 * 1024 * 1024; // 10MB
                
                $fileExtension = strtolower($file->getClientExtension());
                if (!in_array($fileExtension, $allowedTypes)) {
                    throw new \RuntimeException('Invalid image file type. Allowed: ' . implode(', ', $allowedTypes));
                }
                
                if ($file->getSize() > $maxSize) {
                    throw new \RuntimeException('Image file too large. Maximum size: 10MB');
                }

                // Move file to public directory
                if ($file->move($uploadPath, $newName)) {
                    $uploadedFiles[] = $fullPath;
                    log_message('info', 'Resource image uploaded: ' . $newName);
                    
                    // Return relative path for database storage
                    $relativePath = 'uploads/resources/images/' . date('Y-m-d') . '/' . $newName;
                    
                    return [
                        'data'  => $relativePath,
                        'files' => $uploadedFiles
                    ];
                }
            } catch (\Exception $e) {
                log_message('error', 'Image upload failed: ' . $e->getMessage());
                throw $e;
            }
        }

        return [
            'data' => null,
            'files' => $uploadedFiles
        ];
    }

    protected function processPillarImage(array $files): array
    {
        $uploadedFiles = [];
        
        if (!empty($files['pillar_image'])) {
            $file = is_array($files['pillar_image']) ? $files['pillar_image'][0] : $files['pillar_image'];

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $uploadPath = FCPATH . 'uploads/pillars';
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
                        'data' => base_url("uploads/pillars/{$newName}"), // Relative path for database storage
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

    protected function handleContributors(string $resourceId, array $contributors): void
    {
        foreach ($contributors as $contributorData) {
            // Skip empty contributor entries
            if (empty($contributorData['name']) && empty($contributorData['email'])) {
                continue;
            }
            
            // Check if contributor already exists by email or name
            $existingContributor = null;
            if (!empty($contributorData['email'])) {
                $existingContributor = $this->contributorModel->where('email', $contributorData['email'])->first();
                
                // FIX: Check if existingContributor is not null before accessing array elements
                if ($existingContributor) {
                    log_message('debug', 'Existing contributor found by email: ' . $existingContributor['email']);
                } else {
                    log_message('debug', 'No existing contributor found by email: ' . $contributorData['email']);
                }
            }
            
            if (!$existingContributor && !empty($contributorData['name'])) {
                $existingContributor = $this->contributorModel->where('name', $contributorData['name'])->first();
                
                // Optional: Add similar debug logging for name search
                if ($existingContributor) {
                    log_message('debug', 'Existing contributor found by name: ' . $existingContributor['name']);
                }
            }

            $contributorId = null;
            
            if ($existingContributor) {
                $contributorId = $existingContributor['id'];
                log_message('debug', 'Using existing contributor: ' . $contributorId);
            } else {
                // Create new contributor
                $newContributorData = [
                    'name'          => $contributorData['name'] ?? 'Anonymous',
                    'organization'  => $contributorData['organization'] ?? null,
                    'email'         => $contributorData['email'] ?? null,
                    'bio'           => null,
                    'photo_url'     => null,
                ];

                $contributorResult = $this->contributorModel->insert($newContributorData);
                
                if (!$contributorResult) {
                    log_message('error', 'Failed to create contributor: ' . json_encode($this->contributorModel->errors()));
                    continue; // Skip this contributor but don't fail the whole process
                }

                $contributorId = $this->contributorModel->getInsertID();
                log_message('debug', 'Created new contributor: ' . $contributorId);
            }

            // Link resource and contributor
            if ($contributorId) {
                $linkData = [
                    'resource_id'    => $resourceId,
                    'contributor_id' => $contributorId,
                    'role'           => 'author', // Default role
                    'created_at'     => date('Y-m-d H:i:s')
                ];

                $linkResult = $this->resourceContributorModel->insert($linkData);
                
                if (!$linkResult) {
                    log_message('error', 'Failed to link resource and contributor: ' . json_encode($this->resourceContributorModel->errors()));
                }
            }
        }
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
}
