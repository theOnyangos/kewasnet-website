<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\FileAttachment;
use App\Models\DocumentResource;
use App\Models\ResourceCategory;
use CodeIgniter\HTTP\Files\UploadedFile;

class ResourceService
{
    protected $db;
    protected $documentResourceModel;
    protected $resourceModel;
    protected $categoryModel;
    protected $fileAttachmentModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->documentResourceModel = new DocumentResource();
        $this->resourceModel = new Resource();
        $this->categoryModel = new ResourceCategory();
        $this->fileAttachmentModel = new FileAttachment();
    }

    // This method gets the client resources HTML
    public function getClientResourcesHTML($resourceData)
    {
        if (empty($resourceData)) {
            return '<div class="text-center text-gray-500">No resources found</div>';
        }

        $resourceHTML = '<div class="grid grid-cols-1 md:grid-cols-2 gap-10">';

        // Loop through the resources
        foreach ($resourceData as $resource) {
            $resourceHTML .= '<div class="mb-3 border border-borderColor p-3 rounded scroll-box">
                                <div class="pb-3 flex justify-between items-center">
                                    <h2 class="text-lg font-semibold roboto">'. $resource['name'] .'</h2>

                                    <!-- Total Documents -->
                                    <div class="">
                                    <p class="text-gray-700">Total Documents: '. count($resource['resources']) .'</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 h-[300px] overflow-y-scroll scroll-content">';

            // Generate resource cards
            $resourceHTML .= $this->createResourcesArray($resource['resources']);

            $resourceHTML .= '</div>
                            </div>';
        }

        $resourceHTML .= '</div>';

        return $resourceHTML;
    }

    // This function create a resources array and returns it
    public function createResourcesArray($resources)
    {
        $resourcesArray = '';

        // Loop through the resources
        foreach ($resources as $resource) {
            $resourcesArray .= '
                <div class="resource-card bg-white p-4 rounded-md cursor-pointer hover:bg-gray-100">
                    <!-- Document Information -->
                    <div class="">
                        <div class="border-b border-borderColor pb-2 mb-2 flex gap-2 items-center">
                        <div class="mb-2">
                            <img src="'. $this->checkFileMimeType($resource['file_type']) .'" alt="Document type image" class="w-[50px] rounded">
                        </div>
                        <p class="text-gray-700 font-semibold">'. $resource['file_name'] .'</p>
                        </div>

                        <div class="w-full flex justify-between items-center">
                            <!-- Download Document Button -->
                            <button type="button" onclick="handleDownloadResource(this, '.htmlspecialchars(json_encode($resource)).')" download class="hidden py-2 px-8 rounded-md bg-gradient-to-r from-primary to-secondary text-white md:flex gap-2 items-center">
                                <ion-icon name="cloud-download-outline"></ion-icon>
                                Download
                            </button>

                            <a href="'. $resource['file_path'] .'" download="'. $resource['file_path'] .'" class="md:hidden py-2 px-8 rounded-md bg-gradient-to-r from-primary to-secondary text-white flex gap-2 items-center">
                                <ion-icon name="cloud-download-outline"></ion-icon>
                                Download
                            </a>

                            <!-- File size -->
                            <p class="text-gray-700">'. $this->bytesToMB($resource['file_size']) .'MB</p>
                        </div>
                    </div>
                </div>';
        }

        return $resourcesArray;
    }

    // This method checks for file types
    public function checkFileMimeType($fileType)
    {
        if ($fileType == "application/pdf") {
            return base_url("pdf.jpg");
        } 
        
        // return for word documents only
        if ($fileType == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $fileType == "application/msword") {
            return base_url("doc.jpg");
        }

        // return for xlsx only
        if ($fileType == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            return base_url("xls.jpg");
        }

        // return for pptx only
        if ($fileType == "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
            return base_url("pptx.jpg");
        }

        // return for odt file
        if ($fileType == "application/vnd.oasis.opendocument.text") {
            return base_url("odt.jpg");
        }
    }

    // This method converts bytes to megabytes
    function bytesToMB($bytes) {
        return round($bytes / (1024 * 1024), 2);
    }

    /**
     * Create a new resource with file upload
     *
     * @param array $formData
     * @param array $files
     * @return array
     */
    public function createNewResource(array $formData, array $files): array
    {
        $uploadedAttachments = [];
        $resourceId = null;

        log_message('debug', 'ResourceService::createNewResource called with data: ' . json_encode($formData));
        
        // Log file information
        if ($files && isset($files['resource_filename'])) {
            $file = $files['resource_filename'];
            log_message('debug', 'ResourceService: File received - Name: ' . $file->getClientName() . ', Size: ' . $file->getSize() . ' bytes');
        } else {
            log_message('debug', 'ResourceService: No file received');
        }

        // Start transaction
        $this->db->transStart();
        log_message('debug', 'ResourceService: Database transaction started');

        try {
            // Validate required fields
            if (empty($formData['resource_title']) || empty($formData['category_id'])) {
                throw new \RuntimeException('Resource title and category are required');
            }

            // Generate UUID for the resource (since DocumentResource uses UUIDs)
            $resourceId = \Ramsey\Uuid\Uuid::uuid4()->toString();
            
            // Get default document type (or first available)
            $defaultDocumentType = $this->db->table('document_types')
                ->orderBy('id', 'ASC')
                ->limit(1)
                ->get()
                ->getRow();
            
            if (!$defaultDocumentType) {
                throw new \RuntimeException('No document types available. Please create document types first.');
            }
            
            // Prepare data for DocumentResource model
            $resourceData = [
                'id'                    => $resourceId, // Explicitly set the UUID
                'title'                 => $formData['resource_title'],
                'description'           => $formData['resource_description'] ?? $formData['resource_title'],
                'category_id'           => $formData['category_id'],
                'document_type_id'      => $formData['document_type_id'] ?? $defaultDocumentType->id,
                'is_published'          => isset($formData['is_published']) && $formData['is_published'] ? 1 : 0,
                'is_featured'           => isset($formData['is_featured']) && $formData['is_featured'] ? 1 : 0,
                'view_count'            => 0
            ];

            log_message('debug', 'ResourceService: Prepared resource data with UUID: ' . json_encode($resourceData));

            // Save resource to database first using DocumentResource model
            if (!$this->documentResourceModel->save($resourceData)) {
                $errors = $this->documentResourceModel->errors();
                log_message('error', 'ResourceService: Failed to save resource data. Errors: ' . json_encode($errors));
                throw new \RuntimeException('Failed to save resource: ' . json_encode($errors));
            }

            log_message('debug', 'ResourceService: Resource saved with ID: ' . $resourceId);

            // Handle file upload after resource is saved
            if ($files && isset($files['resource_filename'])) {
                log_message('debug', 'ResourceService: Processing file uploads for resource ID: ' . $resourceId);
                
                $uploadedFileCount = 0;
                $uploadErrors = [];
                
                // Handle multiple files
                foreach ($files['resource_filename'] as $file) {
                    if ($file->isValid()) {
                        log_message('debug', 'ResourceService: Processing file: ' . $file->getClientName());
                        
                        $fileUploadResult = $this->processResourceFiles($resourceId, $file);
                        
                        if ($fileUploadResult['success']) {
                            $uploadedAttachments = array_merge($uploadedAttachments, $fileUploadResult['uploaded_files']);
                            $uploadedFileCount++;
                            log_message('info', 'ResourceService: Successfully uploaded file: ' . $file->getClientName());
                        } else {
                            $uploadErrors[] = $file->getClientName() . ': ' . implode(', ', $fileUploadResult['errors']);
                            log_message('error', 'ResourceService: File upload failed for ' . $file->getClientName());
                        }
                    }
                }
                
                // If any files failed, throw error
                if (!empty($uploadErrors)) {
                    log_message('error', 'ResourceService: Some files failed to upload: ' . implode('; ', $uploadErrors));
                    throw new \RuntimeException('Some files failed to upload: ' . implode('; ', $uploadErrors));
                }
                
                log_message('info', "ResourceService: Successfully uploaded {$uploadedFileCount} file(s)");
            } else {
                log_message('debug', 'ResourceService: No files to upload');
            }

            // Check transaction status
            if ($this->db->transStatus() === false) {
                log_message('error', 'ResourceService: Database transaction failed');
                $this->rollbackUploadedFiles($uploadedAttachments);
                throw new \RuntimeException('Database transaction failed');
            }

            // Complete transaction
            $this->db->transComplete();
            log_message('debug', 'ResourceService: Database transaction completed successfully');

            $result = [
                'status' => 'success',
                'message' => 'Resource created successfully',
                'data' => [
                    'resource_id' => $resourceId,
                    'title' => $resourceData['title'],
                    'file_uploaded' => !empty($uploadedAttachments)
                ]
            ];

            log_message('info', 'ResourceService: Successfully created resource with ID: ' . $resourceId);
            return $result;

        } catch (\Exception $e) {
            // Rollback transaction on any error
            $this->db->transRollback();
            log_message('error', 'ResourceService: Transaction rolled back due to error: ' . $e->getMessage());
            
            // Clean up uploaded files if they exist
            if (!empty($uploadedAttachments)) {
                log_message('debug', 'ResourceService: Cleaning up uploaded files');
                $this->rollbackUploadedFiles($uploadedAttachments);
            }

            log_message('error', 'ResourceService: Exception creating resource: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => 'An error occurred while creating the resource: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Process resource file upload and save to FileAttachment table
     *
     * @param string $resourceId
     * @param $file
     * @return array
     */
    protected function processResourceFiles(string $resourceId, $file): array
    {
        $errors = [];
        $uploadedFiles = [];

        log_message('debug', 'ResourceService: Processing file upload for resource ID: ' . $resourceId);
        log_message('debug', 'ResourceService: File info - Name: ' . $file->getClientName() . ', Size: ' . $file->getSize() . ' bytes');

        try {
            if ($file->isValid() && !$file->hasMoved()) {
                log_message('debug', 'ResourceService: File is valid and not moved yet');
                
                // Generate unique filename and path
                $newName = $file->getRandomName();
                $uploadPath = 'document_resources/' . date('Y-m-d');
                $fullUploadPath = WRITEPATH . 'uploads/' . $uploadPath;

                log_message('debug', 'ResourceService: Generated filename: ' . $newName);
                log_message('debug', 'ResourceService: Upload path: ' . $fullUploadPath);

                // Validate file type and size
                $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
                $maxSize = 20 * 1024 * 1024; // 20MB
                
                $fileExtension = strtolower($file->getClientExtension());
                $fileSize = $file->getSize();
                $mimeType = $file->getClientMimeType();
                
                log_message('debug', 'ResourceService: File validation - Extension: ' . $fileExtension . ', Size: ' . $fileSize . ', MIME: ' . $mimeType);
                
                if (!in_array($fileExtension, $allowedTypes)) {
                    log_message('error', 'ResourceService: Invalid file type: ' . $fileExtension);
                    throw new \RuntimeException('Invalid file type. Allowed: ' . implode(', ', $allowedTypes));
                }
                
                if ($fileSize > $maxSize) {
                    log_message('error', 'ResourceService: File too large: ' . $fileSize . ' bytes (max: ' . $maxSize . ')');
                    throw new \RuntimeException('File too large. Maximum size: 20MB');
                }

                // Create directory if it doesn't exist
                if (!is_dir($fullUploadPath)) {
                    log_message('debug', 'ResourceService: Creating upload directory: ' . $fullUploadPath);
                    if (!mkdir($fullUploadPath, 0755, true)) {
                        log_message('error', 'ResourceService: Failed to create upload directory: ' . $fullUploadPath);
                        throw new \RuntimeException('Failed to create upload directory');
                    }
                }
                
                // Move file to upload directory
                if ($file->move($fullUploadPath, $newName)) {
                    $uploadedFilePath = $fullUploadPath . '/' . $newName;
                    $uploadedFiles[] = $uploadedFilePath;
                    
                    log_message('info', 'ResourceService: File uploaded successfully to: ' . $uploadedFilePath);
                    
                    // Save attachment record to FileAttachment table
                    $attachmentData = [
                        'attachable_id'     => $resourceId,
                        'attachable_type'   => 'document_resources',
                        'original_name'     => $file->getClientName(),
                        'file_name'         => $newName,
                        'file_path'         => $uploadPath . '/' . $newName,
                        'file_type'         => $fileExtension,
                        'file_size'         => $fileSize,
                        'mime_type'         => $mimeType,
                        'is_image'          => 0, // Resources are typically documents, not images
                        'download_count'    => 0,
                        'created_at'        => date('Y-m-d H:i:s')
                    ];
                    
                    log_message('debug', 'ResourceService: Saving file attachment data: ' . json_encode($attachmentData));
                    
                    if (!$this->fileAttachmentModel->save($attachmentData)) {
                        $attachmentErrors = $this->fileAttachmentModel->errors();
                        log_message('error', 'ResourceService: Failed to save file attachment: ' . json_encode($attachmentErrors));
                        throw new \RuntimeException('Failed to save file attachment: ' . json_encode($attachmentErrors));
                    }
                    
                    $attachmentId = $this->fileAttachmentModel->getInsertID();
                    log_message('info', 'ResourceService: File attachment saved with ID: ' . $attachmentId);
                    
                    return [
                        'success' => true,
                        'uploaded_files' => $uploadedFiles,
                        'attachment_id' => $attachmentId
                    ];
                } else {
                    log_message('error', 'ResourceService: Failed to move uploaded file: ' . $file->getClientName());
                    throw new \RuntimeException('Failed to move uploaded file: ' . $file->getClientName());
                }
            } else {
                $errorMessage = $file->getErrorString();
                log_message('error', 'ResourceService: File upload error: ' . $errorMessage);
                $errors[] = 'File upload error: ' . $errorMessage;
            }
        } catch (\Exception $e) {
            log_message('error', 'ResourceService: Exception in processResourceFiles: ' . $e->getMessage());
            $errors[] = $e->getMessage();
        }

        return [
            'success'        => false,
            'errors'         => $errors,
            'uploaded_files' => $uploadedFiles
        ];
    }

    /**
     * Rollback uploaded files on transaction failure
     *
     * @param array $filePaths
     * @return void
     */
    protected function rollbackUploadedFiles(array $filePaths): void
    {
        log_message('debug', 'ResourceService: Rolling back ' . count($filePaths) . ' uploaded files');
        
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    log_message('info', 'ResourceService: Successfully rolled back uploaded file: ' . $filePath);
                } else {
                    log_message('error', 'ResourceService: Failed to delete file during rollback: ' . $filePath);
                }
            } else {
                log_message('warning', 'ResourceService: File not found during rollback: ' . $filePath);
            }
        }
        
        if (count($filePaths) > 0) {
            log_message('info', 'ResourceService: File rollback completed for ' . count($filePaths) . ' files');
        }
    }

    /**
     * Create a new resource category
     *
     * @param array $formData
     * @return array
     */
    public function createResourceCategory(array $formData): array
    {
        try {
            // Validate required fields
            if (empty($formData['resource_category_name'])) {
                return [
                    'status' => 'error',
                    'message' => 'Category name is required',
                    'data' => null
                ];
            }

            // Prepare data for insertion
            $categoryData = [
                'name' => $formData['resource_category_name'],
                'description' => $formData['resource_category_description'] ?? null,
                'slug' => url_title($formData['resource_category_name'], '-', true),
                'pillar_id' => null // Set to null if no pillar association needed
            ];

            // Create category record
            $categoryId = $this->categoryModel->insert($categoryData);

            if ($categoryId) {
                log_message('info', 'ResourceService: Category created successfully with ID: ' . $categoryId);
                
                return [
                    'status' => 'success',
                    'message' => 'Resource category created successfully',
                    'data' => [
                        'id' => $categoryId,
                        'name' => $categoryData['name'],
                        'description' => $categoryData['description']
                    ]
                ];
            } else {
                $errors = $this->categoryModel->errors();
                log_message('error', 'ResourceService: Failed to create category: ' . json_encode($errors));
                
                return [
                    'status' => 'error',
                    'message' => 'Failed to create resource category',
                    'data' => null,
                    'errors' => $errors
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'ResourceService: Exception creating category: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => 'An error occurred while creating the category: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update an existing resource
     *
     * @param string $resourceId
     * @param array $formData
     * @param array $files Optional array of files to upload
     * @return array
     */
    public function updateResource(string $resourceId, array $formData, array $files = []): array
    {
        $uploadedAttachments = [];

        log_message('debug', 'ResourceService::updateResource called for ID: ' . $resourceId);
        
        // Start transaction
        $this->db->transStart();
        log_message('debug', 'ResourceService: Database transaction started for update');

        try {
            // Check if resource exists
            $existingResource = $this->documentResourceModel->find($resourceId);
            if (!$existingResource) {
                throw new \RuntimeException('Resource not found');
            }

            // Prepare update data
            $updateData = [
                'title' => $formData['resource_name'],
                'description' => $formData['resource_description'] ?? $formData['resource_name'],
                'category_id' => $formData['category_id'],
                'is_featured' => isset($formData['is_featured']) && $formData['is_featured'] ? 1 : 0,
            ];

            log_message('debug', 'ResourceService: Prepared update data: ' . json_encode($updateData));

            // Update resource
            if (!$this->documentResourceModel->update($resourceId, $updateData)) {
                $errors = $this->documentResourceModel->errors();
                log_message('error', 'ResourceService: Failed to update resource. Errors: ' . json_encode($errors));
                throw new \RuntimeException('Failed to update resource: ' . json_encode($errors));
            }

            log_message('debug', 'ResourceService: Resource updated successfully');

            // Log file debugging information
            if (!empty($files)) {
                log_message('debug', 'ResourceService: Received ' . count($files) . ' file(s) for upload');
                foreach ($files as $index => $file) {
                    log_message('debug', "ResourceService: File {$index} - isValid: " . ($file->isValid() ? 'true' : 'false') . ', Size: ' . $file->getSize() . ', Name: ' . $file->getName());
                }
            } else {
                log_message('debug', 'ResourceService: No files received');
            }

            // Handle file removal if requested (remove specific files marked for deletion)
            if (isset($formData['files_to_remove']) && !empty($formData['files_to_remove'])) {
                $filesToRemove = explode(',', $formData['files_to_remove']);
                log_message('debug', 'ResourceService: Removing ' . count($filesToRemove) . ' file(s) for resource ID: ' . $resourceId);
                
                $attachmentModel = model('App\Models\FileAttachment');
                
                foreach ($filesToRemove as $attachmentId) {
                    $attachmentId = trim($attachmentId);
                    if (empty($attachmentId)) continue;
                    
                    $attachment = $attachmentModel->find($attachmentId);
                    
                    if ($attachment && $attachment['attachable_id'] == $resourceId && $attachment['attachable_type'] == 'document_resources') {
                        // Delete physical file
                        $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
                        if (file_exists($filePath)) {
                            if (unlink($filePath)) {
                                log_message('debug', 'ResourceService: Deleted file: ' . $filePath);
                            } else {
                                log_message('error', 'ResourceService: Failed to delete file: ' . $filePath);
                            }
                        }
                        
                        // Delete database record
                        if ($attachmentModel->delete($attachmentId)) {
                            log_message('info', 'ResourceService: Successfully removed attachment ID: ' . $attachmentId);
                        } else {
                            log_message('error', 'ResourceService: Failed to delete attachment record ID: ' . $attachmentId);
                        }
                    }
                }
            }

            // Handle new file upload if provided
            if (!empty($files)) {
                log_message('debug', 'ResourceService: Processing ' . count($files) . ' file upload(s) for resource ID: ' . $resourceId);
                
                $uploadedFileCount = 0;
                $uploadErrors = [];
                
                foreach ($files as $file) {
                    if ($file && $file->isValid() && !$file->hasMoved() && $file->getSize() > 0) {
                        log_message('debug', 'ResourceService: Uploading file: ' . $file->getClientName() . ', Size: ' . $file->getSize() . ' bytes');
                        
                        // Upload new file
                        $fileUploadResult = $this->processResourceFiles($resourceId, $file);
                        log_message('debug', 'ResourceService: File upload result: ' . json_encode($fileUploadResult));

                        if ($fileUploadResult['success']) {
                            $uploadedAttachments = array_merge($uploadedAttachments, $fileUploadResult['uploaded_files']);
                            $uploadedFileCount++;
                            log_message('info', 'ResourceService: Successfully uploaded file: ' . $file->getClientName());
                        } else {
                            $uploadErrors[] = $file->getClientName() . ': ' . implode(', ', $fileUploadResult['errors']);
                            log_message('error', 'ResourceService: Failed to upload ' . $file->getClientName());
                        }
                    }
                }
                
                if (!empty($uploadErrors)) {
                    log_message('error', 'ResourceService: Some files failed: ' . implode('; ', $uploadErrors));
                    throw new \RuntimeException('Some files failed to upload: ' . implode('; ', $uploadErrors));
                }
                
                log_message('info', "ResourceService: Successfully uploaded {$uploadedFileCount} file(s)");
            }
            
            // Check transaction status
            if ($this->db->transStatus() === false) {
                log_message('error', 'ResourceService: Database transaction failed');
                $this->rollbackUploadedFiles($uploadedAttachments);
                throw new \RuntimeException('Database transaction failed');
            }

            // Complete transaction
            $this->db->transComplete();
            log_message('debug', 'ResourceService: Database transaction completed successfully');

            $result = [
                'status' => 'success',
                'message' => 'Resource updated successfully',
                'data' => [
                    'resource_id' => $resourceId,
                    'title' => $updateData['title'],
                    'file_updated' => !empty($uploadedAttachments)
                ]
            ];

            log_message('info', 'ResourceService: Successfully updated resource with ID: ' . $resourceId);
            return $result;

        } catch (\Exception $e) {
            // Rollback transaction on any error
            $this->db->transRollback();
            log_message('error', 'ResourceService: Transaction rolled back due to error: ' . $e->getMessage());
            
            // Clean up uploaded files if they exist
            if (!empty($uploadedAttachments)) {
                log_message('debug', 'ResourceService: Cleaning up uploaded files');
                $this->rollbackUploadedFiles($uploadedAttachments);
            }

            log_message('error', 'ResourceService: Exception updating resource: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => 'An error occurred while updating the resource: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}