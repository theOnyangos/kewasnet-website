<?php

namespace App\Services;

use RuntimeException;
use App\Models\FileAttachment;
use CodeIgniter\Database\Exceptions\DatabaseException;

class OpportunityService
{
    protected $db;
    protected $model;
    protected $fileAttachmentModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->model = model('App\Models\JobOpportunityModel');
        $this->fileAttachmentModel = new FileAttachment();
        
        log_message('debug', 'OpportunityService: Initialized with database and models');
    }

    /**
     * Create a new job opportunity with document upload and transaction support
     * 
     * @param array $postData
     * @param mixed $files
     * @return array
     * @throws RuntimeException
     * @throws DatabaseException
     */
    public function createOpportunity(array $postData, $files = null): array
    {
        $uploadedAttachments = [];
        $insertId = null;

        log_message('debug', 'OpportunityService::createOpportunity called with data: ' . json_encode($postData));
        
        // Better logging for files
        if ($files) {
            if (is_array($files)) {
                log_message('debug', 'OpportunityService: Files array received with keys: ' . json_encode(array_keys($files)));
                foreach ($files as $key => $file) {
                    if (is_object($file)) {
                        log_message('debug', 'OpportunityService: File ' . $key . ' is object of type: ' . get_class($file));
                    } else {
                        log_message('debug', 'OpportunityService: File ' . $key . ' is: ' . gettype($file));
                    }
                }
            } else {
                log_message('debug', 'OpportunityService: Files received as: ' . gettype($files) . (is_object($files) ? ' of type ' . get_class($files) : ''));
            }
        } else {
            log_message('debug', 'OpportunityService: No files received');
        }

        // Start transaction
        $this->db->transStart();
        log_message('debug', 'OpportunityService: Database transaction started');

        try {
            $documentPath = null;

            // Prepare the data
            $data = $this->prepareOpportunityData($postData, $documentPath);
            log_message('debug', 'OpportunityService: Prepared opportunity data: ' . json_encode($data));

            // Save to database
            if (!$this->model->save($data)) {
                $errors = $this->model->errors();
                log_message('error', 'OpportunityService: Failed to save opportunity data. Errors: ' . json_encode($errors));
                throw new DatabaseException('Failed to save job opportunity: ' . json_encode($errors));
            }

            // Get the inserted ID
            $insertId = $this->model->getInsertID();
            log_message('debug', 'OpportunityService: Opportunity saved with ID: ' . $insertId);

            // Handle resource file attachments - store in writable directory
            if ($files && is_array($files) && isset($files['document']) && $files['document']->isValid()) {
                log_message('debug', 'OpportunityService: Processing document file upload for opportunity ID: ' . $insertId);
                
                $fileUploadResult = $this->processResourceFiles($insertId, $files['document']);
                log_message('debug', 'OpportunityService: File upload result: ' . json_encode($fileUploadResult));

                if (!$fileUploadResult['success']) {
                    log_message('error', 'OpportunityService: File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                    throw new \RuntimeException('File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                }
                
                $uploadedAttachments = $fileUploadResult['uploaded_files'];
                log_message('info', 'OpportunityService: Successfully uploaded ' . count($uploadedAttachments) . ' files');
            } else {
                log_message('debug', 'OpportunityService: No valid document file to upload');
                if ($files && !is_array($files)) {
                    log_message('warning', 'OpportunityService: Files parameter is not an array: ' . gettype($files));
                }
            }

            // Check transaction status
            if ($this->db->transStatus() === false) {
                log_message('error', 'OpportunityService: Database transaction failed');
                $this->rollbackUploadedFiles($uploadedAttachments);
                throw new \RuntimeException('Database transaction failed');
            }

            // Complete transaction if everything succeeded
            $this->db->transComplete();
            log_message('debug', 'OpportunityService: Database transaction completed successfully');

            $result = [
                'status' => 'success',
                'message' => 'Job opportunity created successfully',
                'data' => [
                    'id' => $insertId,
                    'document_url' => $documentPath ? base_url('uploads/' . $documentPath) : null,
                    'attachments_count' => count($uploadedAttachments)
                ]
            ];

            log_message('info', 'OpportunityService: Successfully created opportunity with ID: ' . $insertId);
            return $result;

        } catch (\Exception $e) {
            // Rollback transaction on any error
            $this->db->transRollback();
            log_message('error', 'OpportunityService: Transaction rolled back due to error: ' . $e->getMessage());
            
            // Clean up uploaded files if they exist
            if (!empty($uploadedAttachments)) {
                log_message('debug', 'OpportunityService: Cleaning up ' . count($uploadedAttachments) . ' uploaded files');
                $this->rollbackUploadedFiles($uploadedAttachments);
            }

            log_message('error', 'OpportunityService: Error in createOpportunity: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            throw $e;
        }
    }

    /**
     * Update an existing job opportunity
     * 
     * @param int $id
     * @param array $postData
     * @param mixed $file
     * @return array
     * @throws RuntimeException
     * @throws DatabaseException
     */
    public function updateOpportunity(int $id, array $postData, $file = null): array
    {
        log_message('debug', 'OpportunityService::updateOpportunity called for ID: ' . $id);
        
        // Get existing opportunity
        $existingOpportunity = $this->model->find($id);
        if (!$existingOpportunity) {
            return [
                'success' => false,
                'message' => 'Opportunity not found'
            ];
        }

        $uploadedAttachments = [];
        $oldDocumentPath = $existingOpportunity['document_url'];

        // Start transaction
        $this->db->transStart();
        log_message('debug', 'OpportunityService: Database transaction started for update');

        try {
            $documentPath = $oldDocumentPath; // Keep old path by default

            // Handle new file upload if provided
            if ($file && $file->isValid() && !$file->hasMoved()) {
                log_message('debug', 'OpportunityService: Processing new document file upload');
                
                $fileUploadResult = $this->processResourceFiles($id, $file);
                log_message('debug', 'OpportunityService: File upload result: ' . json_encode($fileUploadResult));

                if (!$fileUploadResult['success']) {
                    log_message('error', 'OpportunityService: File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                    throw new \RuntimeException('File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                }
                
                $uploadedAttachments = $fileUploadResult['uploaded_files'];
                if (!empty($uploadedAttachments)) {
                    $documentPath = $uploadedAttachments[0];  // Use first uploaded file
                    
                    // Delete old document if it exists
                    if ($oldDocumentPath) {
                        $this->deleteOldDocument($oldDocumentPath);
                    }
                }
                log_message('info', 'OpportunityService: Successfully uploaded new document');
            }

            // Prepare the data
            $data = $this->prepareOpportunityData($postData, $documentPath);
            $data['id'] = $id; // Ensure ID is set for update
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            log_message('debug', 'OpportunityService: Prepared opportunity update data: ' . json_encode($data));

            // Update in database
            if (!$this->model->save($data)) {
                $errors = $this->model->errors();
                log_message('error', 'OpportunityService: Failed to update opportunity. Errors: ' . json_encode($errors));
                throw new DatabaseException('Failed to update job opportunity: ' . json_encode($errors));
            }

            log_message('debug', 'OpportunityService: Opportunity updated successfully');

            // Check transaction status
            if ($this->db->transStatus() === false) {
                log_message('error', 'OpportunityService: Database transaction failed');
                $this->rollbackUploadedFiles($uploadedAttachments);
                throw new \RuntimeException('Database transaction failed');
            }

            // Complete transaction
            $this->db->transComplete();
            log_message('debug', 'OpportunityService: Database transaction completed successfully');

            $result = [
                'success' => true,
                'message' => 'Job opportunity updated successfully',
                'data' => [
                    'id' => $id,
                    'document_url' => $documentPath ? base_url('uploads/' . $documentPath) : null
                ]
            ];

            log_message('info', 'OpportunityService: Successfully updated opportunity with ID: ' . $id);
            return $result;

        } catch (\Exception $e) {
            // Rollback transaction on any error
            $this->db->transRollback();
            log_message('error', 'OpportunityService: Transaction rolled back due to error: ' . $e->getMessage());
            
            // Clean up newly uploaded files if they exist
            if (!empty($uploadedAttachments)) {
                log_message('debug', 'OpportunityService: Cleaning up ' . count($uploadedAttachments) . ' newly uploaded files');
                $this->rollbackUploadedFiles($uploadedAttachments);
            }

            log_message('error', 'OpportunityService: Error in updateOpportunity: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to update opportunity: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a job opportunity
     * 
     * @param string $id
     * @return array
     */
    public function deleteOpportunity(string $id): array
    {
        log_message('debug', 'OpportunityService::deleteOpportunity called for ID: ' . $id);
        
        try {
            // Get existing opportunity to get document path
            $opportunity = $this->model->find($id);
            if (!$opportunity) {
                return [
                    'success' => false,
                    'message' => 'Opportunity not found'
                ];
            }

            // Start transaction
            $this->db->transStart();
            log_message('debug', 'OpportunityService: Database transaction started for deletion');

            // Delete from database (soft delete if using deleted_at)
            if (!$this->model->delete($id)) {
                throw new \RuntimeException('Failed to delete opportunity from database');
            }

            log_message('debug', 'OpportunityService: Opportunity deleted from database');

            // Delete associated document file if exists
            if (!empty($opportunity['document_url'])) {
                $this->deleteOldDocument($opportunity['document_url']);
            }

            // Check transaction status
            if ($this->db->transStatus() === false) {
                log_message('error', 'OpportunityService: Database transaction failed');
                throw new \RuntimeException('Database transaction failed');
            }

            // Complete transaction
            $this->db->transComplete();
            log_message('debug', 'OpportunityService: Database transaction completed successfully');

            log_message('info', 'OpportunityService: Successfully deleted opportunity with ID: ' . $id);
            
            return [
                'success' => true,
                'message' => 'Job opportunity deleted successfully'
            ];

        } catch (\Exception $e) {
            // Rollback transaction on any error
            $this->db->transRollback();
            log_message('error', 'OpportunityService: Transaction rolled back due to error: ' . $e->getMessage());
            log_message('error', 'OpportunityService: Error in deleteOpportunity: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to delete opportunity: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete old document file
     * 
     * @param string $documentPath
     * @return void
     */
    protected function deleteOldDocument(string $documentPath): void
    {
        try {
            $fullPath = WRITEPATH . 'uploads/' . $documentPath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
                log_message('debug', 'OpportunityService: Deleted old document: ' . $documentPath);
            }
        } catch (\Exception $e) {
            log_message('error', 'OpportunityService: Failed to delete old document: ' . $e->getMessage());
        }
    }

    /**
     * Prepare opportunity data from request
     * 
     * @param array $postData
     * @param string|null $documentPath
     * @return array
     */
    protected function prepareOpportunityData(array $postData, ?string $documentPath): array
    {
        log_message('debug', 'OpportunityService: Preparing opportunity data from post data');
        log_message('debug', 'OpportunityService: Raw postData keys: ' . json_encode(array_keys($postData)));
        log_message('debug', 'OpportunityService: Scope value in postData: ' . ($postData['scope'] ?? 'NOT SET'));
        log_message('debug', 'OpportunityService: Application deadline in postData: ' . ($postData['application_deadline'] ?? 'NOT SET'));
        
        // Process benefits array
        $benefits = null;
        if (isset($postData['benefits']) && is_array($postData['benefits'])) {
            $benefits = array_filter($postData['benefits'], function($benefit) {
                return !empty(trim($benefit));
            });
            $benefits = !empty($benefits) ? json_encode(array_values($benefits)) : null;
            log_message('debug', 'OpportunityService: Processed benefits: ' . ($benefits ?? 'none'));
        }

        // Process requirements array
        $requirements = null;
        if (isset($postData['requirements']) && is_array($postData['requirements'])) {
            $requirements = array_filter($postData['requirements'], function($requirement) {
                return !empty(trim($requirement));
            });
            $requirements = !empty($requirements) ? json_encode(array_values($requirements)) : null;
            log_message('debug', 'OpportunityService: Processed requirements: ' . ($requirements ?? 'none'));
        }

        // Process application deadline - convert from various formats to Y-m-d
        $applicationDeadline = null;
        if (isset($postData['application_deadline']) && !empty(trim($postData['application_deadline']))) {
            $dateString = trim($postData['application_deadline']);
            log_message('debug', 'OpportunityService: Raw application_deadline received: ' . $dateString);
            
            try {
                // Try to parse the date from various formats
                $date = \DateTime::createFromFormat('d-M-Y', $dateString); // 12-Dec-1993 format
                if (!$date) {
                    $date = \DateTime::createFromFormat('Y-m-d', $dateString); // 2025-10-10 format
                }
                if (!$date) {
                    $date = new \DateTime($dateString); // Let PHP try to parse it
                }
                
                if ($date) {
                    $applicationDeadline = $date->format('Y-m-d');
                    log_message('debug', 'OpportunityService: Converted application_deadline to: ' . $applicationDeadline);
                } else {
                    log_message('warning', 'OpportunityService: Could not parse application_deadline: ' . $dateString);
                }
            } catch (\Exception $e) {
                log_message('error', 'OpportunityService: Error parsing application_deadline: ' . $e->getMessage());
            }
        }

        // Generate a unique slug from the title
        $slug = null;
        if (isset($postData['title']) && !empty(trim($postData['title']))) {
            $slug = $this->generateUniqueSlug(trim($postData['title']));
            log_message('debug', 'OpportunityService: Generated slug: ' . $slug);
        }

        // Process scope field - it's a JSON field in the database
        $scope = null;
        if (isset($postData['scope']) && !empty(trim($postData['scope']))) {
            $scopeValue = trim($postData['scope']);
            // Remove common empty HTML patterns from WYSIWYG editors
            $strippedScope = strip_tags($scopeValue);
            $strippedScope = trim($strippedScope);
            
            // Only set scope if there's actual content, store as JSON string
            if (!empty($strippedScope)) {
                $scope = json_encode(['content' => $scopeValue]);
                log_message('debug', 'OpportunityService: Scope has content, encoding as JSON');
            } else {
                log_message('debug', 'OpportunityService: Scope is empty, setting to null');
            }
        } else {
            log_message('debug', 'OpportunityService: No scope provided, setting to null');
        }

        $preparedData = [
            'title'                 => $postData['title'] ?? null,
            'slug'                  => $slug,
            'description'           => $postData['description'] ?? null,
            'opportunity_type'      => $postData['opportunity_type'] ?? null,
            'location'              => $postData['location'] ?? null,
            'is_remote'             => isset($postData['is_remote']) && $postData['is_remote'] == '1' ? 1 : 0,
            'salary_min'            => !empty($postData['salary_min']) ? (float)$postData['salary_min'] : null,
            'salary_max'            => !empty($postData['salary_max']) ? (float)$postData['salary_max'] : null,
            'salary_currency'       => $postData['salary_currency'] ?? 'USD',
            'application_deadline'  => $applicationDeadline,
            'status'                => $postData['status'] ?? 'draft',
            'company'               => $postData['company'] ?? null,
            'benefits'              => $benefits,
            'scope'                 => $scope,
            'requirements'          => $requirements,
            'contract_duration'     => $postData['contract_duration'] ?? null,
            'hours_per_week'        => !empty($postData['hours_per_week']) ? (int)$postData['hours_per_week'] : null,
            'document_url'          => $documentPath,
        ];

        log_message('debug', 'OpportunityService: Prepared opportunity data array: ' . json_encode($preparedData, JSON_PRETTY_PRINT));
        log_message('debug', 'OpportunityService: Data preparation completed. Title: ' . ($preparedData['title'] ?? 'none'));
        return $preparedData;
    }

    protected function generateUniqueSlug(string $title): string
    {
        // Convert title to slug format
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = trim($slug, '-');
        
        // Ensure slug is not empty
        if (empty($slug)) {
            $slug = 'job-opportunity';
        }
        
        // Check if slug exists and make it unique
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->model->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        log_message('debug', 'OpportunityService: Final unique slug: ' . $slug);
        return $slug;
    }

    protected function processResourceFiles(string $resourceId, $file)
    {
        $errors = [];
        $uploadedFiles = [];

        log_message('debug', 'OpportunityService: Processing file upload for resource ID: ' . $resourceId);
        log_message('debug', 'OpportunityService: File info - Name: ' . $file->getClientName() . ', Size: ' . $file->getSize() . ' bytes');

        try {
            if ($file->isValid() && !$file->hasMoved()) {
                log_message('debug', 'OpportunityService: File is valid and not moved yet');
                
                // Generate unique filename and path
                $newName        = $file->getRandomName();
                $uploadPath     = 'job_opportunities/' . date('Y-m-d');
                $fullUploadPath = WRITEPATH . 'uploads/' . $uploadPath;

                log_message('debug', 'OpportunityService: Generated filename: ' . $newName);
                log_message('debug', 'OpportunityService: Upload path: ' . $fullUploadPath);

                // Validate file type and size
                $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
                $maxSize = 50 * 1024 * 1024; // 50MB
                
                $fileExtension = strtolower($file->getClientExtension());
                $fileSize = $file->getSize();
                $mimeType = $file->getClientMimeType();
                
                log_message('debug', 'OpportunityService: File validation - Extension: ' . $fileExtension . ', Size: ' . $fileSize . ', MIME: ' . $mimeType);
                
                if (!in_array($fileExtension, $allowedTypes)) {
                    log_message('error', 'OpportunityService: Invalid file type: ' . $fileExtension);
                    throw new \RuntimeException('Invalid file type. Allowed: ' . implode(', ', $allowedTypes));
                }
                
                if ($fileSize > $maxSize) {
                    log_message('error', 'OpportunityService: File too large: ' . $fileSize . ' bytes (max: ' . $maxSize . ')');
                    throw new \RuntimeException('File too large. Maximum size: 50MB');
                }

                // Create directory if it doesn't exist
                if (!is_dir($fullUploadPath)) {
                    log_message('debug', 'OpportunityService: Creating upload directory: ' . $fullUploadPath);
                    if (!mkdir($fullUploadPath, 0755, true)) {
                        log_message('error', 'OpportunityService: Failed to create upload directory: ' . $fullUploadPath);
                        throw new \RuntimeException('Failed to create upload directory');
                    }
                }
                
                // Move file to upload directory
                if ($file->move($fullUploadPath, $newName)) {
                    $uploadedFilePath = $fullUploadPath . '/' . $newName;
                    $uploadedFiles[] = $uploadedFilePath;
                    
                    log_message('info', 'OpportunityService: File uploaded successfully to: ' . $uploadedFilePath);
                    
                    // Determine if file is an image
                    $isImage = strpos($mimeType, 'image/') === 0;
                    
                    // Save attachment record
                    $attachmentData = [
                        'attachable_id'     => $resourceId,
                        'attachable_type'   => 'job_opportunity',
                        'original_name'     => $file->getClientName(),
                        'file_name'         => $newName,
                        'file_path'         => $uploadPath . '/' . $newName,
                        'file_type'         => $fileExtension,
                        'file_size'         => $fileSize,
                        'mime_type'         => $mimeType,
                        'is_image'          => $isImage ? 1 : 0,
                        'download_count'    => 0,
                        'created_at'        => date('Y-m-d H:i:s')
                    ];
                    
                    log_message('debug', 'OpportunityService: Saving file attachment data: ' . json_encode($attachmentData));
                    
                    if (!$this->fileAttachmentModel->save($attachmentData)) {
                        $attachmentErrors = $this->fileAttachmentModel->errors();
                        log_message('error', 'OpportunityService: Failed to save file attachment: ' . json_encode($attachmentErrors));
                        throw new \RuntimeException('Failed to save file attachment: ' . json_encode($attachmentErrors));
                    }
                    
                    $attachmentId = $this->fileAttachmentModel->getInsertID();
                    log_message('info', 'OpportunityService: File attachment saved with ID: ' . $attachmentId);
                    
                    return [
                        'success' => true,
                        'uploaded_files' => $uploadedFiles,
                        'attachment_id' => $attachmentId
                    ];
                } else {
                    log_message('error', 'OpportunityService: Failed to move uploaded file: ' . $file->getClientName());
                    throw new \RuntimeException('Failed to move uploaded file: ' . $file->getClientName());
                }
            } else {
                $errorMessage = $file->getErrorString();
                log_message('error', 'OpportunityService: File upload error: ' . $errorMessage);
                $errors[] = 'File upload error: ' . $errorMessage;
            }
        } catch (\Exception $e) {
            log_message('error', 'OpportunityService: Exception in processResourceFiles: ' . $e->getMessage());
            $errors[] = $e->getMessage();
        }

        return [
            'success'        => false,
            'errors'         => $errors,
            'uploaded_files' => $uploadedFiles
        ];
    }

    protected function rollbackUploadedFiles(array $filePaths): void
    {
        log_message('debug', 'OpportunityService: Rolling back ' . count($filePaths) . ' uploaded files');
        
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    log_message('info', 'OpportunityService: Successfully rolled back uploaded file: ' . $filePath);
                } else {
                    log_message('error', 'OpportunityService: Failed to delete file during rollback: ' . $filePath);
                }
            } else {
                log_message('warning', 'OpportunityService: File not found during rollback: ' . $filePath);
            }
        }
        
        if (count($filePaths) > 0) {
            log_message('info', 'OpportunityService: File rollback completed for ' . count($filePaths) . ' files');
        }
    }

    /**
     * Validate opportunity data before processing
     * 
     * @param array $data
     * @return array
     */
    public function validateOpportunityData(array $data): array
    {
        log_message('debug', 'OpportunityService: Validating opportunity data');
        
        $errors = [];
        
        // Required fields validation
        $requiredFields = ['title', 'description', 'opportunity_type'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                log_message('warning', 'OpportunityService: Missing required field: ' . $field);
            }
        }
        
        // Salary validation
        if (!empty($data['salary_min']) && !empty($data['salary_max'])) {
            if ((float)$data['salary_min'] > (float)$data['salary_max']) {
                $errors[] = 'Minimum salary cannot be greater than maximum salary';
                log_message('warning', 'OpportunityService: Invalid salary range - min: ' . $data['salary_min'] . ', max: ' . $data['salary_max']);
            }
        }
        
        // Deadline validation
        if (!empty($data['application_deadline'])) {
            $deadline = strtotime($data['application_deadline']);
            if ($deadline === false) {
                $errors[] = 'Invalid application deadline format';
                log_message('warning', 'OpportunityService: Invalid deadline format: ' . $data['application_deadline']);
            } elseif ($deadline < time()) {
                $errors[] = 'Application deadline cannot be in the past';
                log_message('warning', 'OpportunityService: Deadline in the past: ' . $data['application_deadline']);
            }
        }
        
        $isValid = empty($errors);
        log_message('debug', 'OpportunityService: Data validation ' . ($isValid ? 'passed' : 'failed') . '. Errors: ' . count($errors));
        
        return [
            'valid' => $isValid,
            'errors' => $errors
        ];
    }

    /**
     * Get upload statistics
     * 
     * @return array
     */
    public function getUploadStats(): array
    {
        $uploadDir = WRITEPATH . 'uploads/job_opportunities/';
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'directories' => 0
        ];
        
        if (is_dir($uploadDir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($uploadDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $stats['total_files']++;
                    $stats['total_size'] += $file->getSize();
                }
            }
        }
        
        log_message('debug', 'OpportunityService: Upload stats - Files: ' . $stats['total_files'] . ', Size: ' . $stats['total_size'] . ' bytes');
        return $stats;
    }
}