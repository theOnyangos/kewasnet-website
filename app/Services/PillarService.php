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

    public function updatePillar(string $id, array $formData, array $files): array
    {
        // Normalize the ID (trim whitespace, convert to lowercase for comparison)
        $id = trim($id);
        
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Track uploaded files for potential rollback
        $uploadedFiles = [];
        
        try {
            // Log the ID being searched
            log_message('info', 'Searching for pillar with ID: ' . $id . ' (length: ' . strlen($id) . ')');
            
            // Try multiple methods to find the pillar
            $existingPillar = null;
            
            // Method 1: Using where()->first()
            $existingPillar = $this->pillarModel->where('id', $id)->first();
            log_message('info', 'Method 1 (where->first): ' . ($existingPillar ? 'Found' : 'Not found'));
            
            // Method 2: Using find() if first method failed
            if (!$existingPillar) {
                $existingPillar = $this->pillarModel->find($id);
                log_message('info', 'Method 2 (find): ' . ($existingPillar ? 'Found' : 'Not found'));
            }
            
            // Method 3: Using builder directly if both failed
            if (!$existingPillar) {
                $builder = $this->pillarModel->builder();
                $result = $builder->where('id', $id)->get()->getRowArray();
                if ($result) {
                    $existingPillar = $result;
                    log_message('info', 'Method 3 (builder): Found');
                } else {
                    log_message('info', 'Method 3 (builder): Not found');
                }
            }
            
            if (!$existingPillar) {
                log_message('error', 'Pillar not found with any method - ID: ' . $id);
                
                // Try to find with deleted records to see if it was soft-deleted
                $deletedPillar = $this->pillarModel->withDeleted()->where('id', $id)->first();
                if ($deletedPillar) {
                    log_message('error', 'Pillar found but is soft-deleted');
                    throw new \RuntimeException('Pillar was deleted and cannot be updated');
                }
                
                // Check if any pillar exists with similar ID (for debugging)
                $allPillars = $this->pillarModel->findAll();
                log_message('info', 'Total pillars in database: ' . count($allPillars));
                if (count($allPillars) > 0) {
                    log_message('info', 'Sample pillar IDs: ' . json_encode(array_slice(array_column($allPillars, 'id'), 0, 3)));
                }
                
                // Additional check: verify the ID format is valid UUID
                if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
                    throw new \RuntimeException('Invalid pillar ID format: ' . $id);
                }
                
                throw new \RuntimeException('Pillar not found with ID: ' . $id);
            }
            
            // Convert to array if it's an object
            if (is_object($existingPillar)) {
                $existingPillar = (array) $existingPillar;
            }
            
            log_message('info', 'Pillar found for update - ID: ' . $id . ', Title: ' . ($existingPillar['title'] ?? 'N/A'));

            // Set validation rules for update (slug uniqueness check excludes current pillar)
            $rules = [
                'title'         => 'required|max_length[100]',
                'slug'          => 'required|max_length[50]|is_unique[pillars.slug,id,' . $id . ']',
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

            // Build update data - start with all fields except image_path
            $updateData = [
                'title'             => $formData['title'],
                'slug'              => $formData['slug'],
                'icon'              => $formData['icon'] ?? null,
                'description'       => $formData['description'] ?? '',
                'content'           => $formData['content'] ?? '',
                'button_text'       => $formData['button_text'] ?? 'Explore Resources',
                'button_link'       => $formData['button_link'] ?? null,
                'is_private'        => isset($formData['is_private']) ? (int)$formData['is_private'] : 0,
                'is_active'         => isset($formData['is_active']) ? (int)$formData['is_active'] : 1,
                'meta_title'        => $formData['meta_title'] ?? null,
                'meta_description'  => $formData['meta_description'] ?? null
            ];

            // Only update image_path if a new image is uploaded
            if (!empty($files['pillar_image']) && isset($files['pillar_image']) && $files['pillar_image']->isValid()) {
                $oldImagePath = $existingPillar['image_path'] ?? null;
                
                // Delete old image if exists
                if ($oldImagePath) {
                    // Handle different image path formats
                    $oldFilePath = null;
                    if (strpos($oldImagePath, 'http') === 0 || strpos($oldImagePath, '//') === 0) {
                        // Full URL - extract path
                        $parsedUrl = parse_url($oldImagePath);
                        $path = $parsedUrl['path'] ?? '';
                        if ($path && strpos($path, '/') === 0) {
                            $oldFilePath = FCPATH . ltrim($path, '/');
                        }
                    } elseif (strpos($oldImagePath, '/') === 0) {
                        // Absolute path
                        $oldFilePath = FCPATH . ltrim($oldImagePath, '/');
                    } else {
                        // Relative path
                        $oldFilePath = FCPATH . $oldImagePath;
                    }
                    
                    if ($oldFilePath && file_exists($oldFilePath) && is_file($oldFilePath)) {
                        @unlink($oldFilePath);
                        log_message('info', 'Deleted old pillar image: ' . $oldFilePath);
                    }
                }
                
                // Process and upload new image
                $imageResult = $this->processPillarImage($files);
                if (!empty($imageResult['data'])) {
                    $updateData['image_path'] = $imageResult['data'];
                    $uploadedFiles = array_merge($uploadedFiles, $imageResult['files']);
                    log_message('info', 'New pillar image uploaded: ' . $imageResult['data']);
                }
            }
            // If no new image is uploaded, image_path is not included in updateData, so it won't be changed

            // Debug log the data being updated
            log_message('debug', 'Pillar update data: ' . json_encode($updateData, JSON_PRETTY_PRINT));

            // Update the pillar data in the database
            $updateResult = $this->pillarModel->update($id, $updateData);
            
            if (!$updateResult) {
                throw new \RuntimeException('Failed to update pillar in database');
            }

            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                // Transaction failed, rollback uploaded files
                $this->rollbackUploadedFiles($uploadedFiles);
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }

            // Retrieve the updated pillar - use builder directly to avoid any caching issues
            $db = \Config\Database::connect();
            $builder = $db->table('pillars');
            $result = $builder->where('id', $id)->get()->getRowArray();
            
            if ($result && is_array($result)) {
                $updatedPillar = $result;
                log_message('info', 'Pillar retrieved using direct DB query - ID: ' . $id);
            } else {
                // Try with model
                $updatedPillar = $this->pillarModel->where('id', $id)->first();
                
                // Convert to array if it's an object
                if (is_object($updatedPillar)) {
                    $updatedPillar = (array) $updatedPillar;
                }
                
                if (!$updatedPillar || !is_array($updatedPillar)) {
                    log_message('warning', 'Could not retrieve updated pillar from database, using merged data');
                    // Merge existing pillar data with update data as fallback
                    $updatedPillar = array_merge($existingPillar, $updateData);
                    $updatedPillar['id'] = $id; // Ensure ID is set
                } else {
                    log_message('info', 'Pillar retrieved using model - ID: ' . $id);
                }
            }
            
            log_message('info', 'Pillar updated successfully - ID: ' . $id . ', Title: ' . ($updatedPillar['title'] ?? 'N/A'));
            return $updatedPillar;
            
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
            
            log_message('error', 'Pillar update failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update pillar: ' . $e->getMessage());
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

    public function updateResourceCategory(string $id, array $formData): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Find the existing category
            $existingCategory = $this->resourceCategoryModel->where('id', $id)->first();
            
            if (!$existingCategory) {
                throw new \RuntimeException('Resource category not found with ID: ' . $id);
            }

            // Convert to array if object
            if (is_object($existingCategory)) {
                $existingCategory = (array) $existingCategory;
            }

            // Set validation rules for resource category update
            $rules = [
                'name'          => 'required|max_length[255]',
                'pillar_id'     => 'required|is_not_unique[pillars.id]',
                'slug'          => 'required|max_length[100]|is_unique[resource_categories.slug,id,' . $id . ']',
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

            // Prepare data for update
            $updateData = [
                'name'        => $formData['name'],
                'slug'        => $formData['slug'],
                'pillar_id'   => $formData['pillar_id'] ?? null,
                'description' => $formData['description'] ?? '',
            ];

            // Skip model validation since we've already validated in the service
            // Update the resource category in the database
            $result = $this->resourceCategoryModel->skipValidation(true)->update($id, $updateData);

            if (!$result) {
                // Get the last error from the model
                $errors = $this->resourceCategoryModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                log_message('error', 'Resource category update failed: ' . $errorMessage);
                throw new \RuntimeException('Failed to update resource category in database: ' . $errorMessage);
            }

            // Retrieve the updated category
            $updatedCategory = $this->resourceCategoryModel->where('id', $id)->first();
            
            if (is_object($updatedCategory)) {
                $updatedCategory = (array) $updatedCategory;
            }

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }

            return [
                'status'  => 'success',
                'message' => 'Resource category updated successfully.',
                'data'    => $updatedCategory
            ];
        } catch (ValidationException $e) {
            $db->transRollback();
            throw $e;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating resource category: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update resource category: ' . $e->getMessage());
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
                'slug'   => 'required|max_length[100]|is_unique[document_types.slug]',
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

    public function updateDocumentType(string $id, array $formData): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Find the existing document type
            $existingType = $this->documentTypeModel->where('id', $id)->first();
            
            if (!$existingType) {
                throw new \RuntimeException('Document type not found with ID: ' . $id);
            }

            // Convert to array if object
            if (is_object($existingType)) {
                $existingType = (array) $existingType;
            }

            // Set validation rules for document type update
            $rules = [
                'name'          => 'required|max_length[255]',
                'slug'          => 'required|max_length[100]|is_unique[document_types.slug,id,' . $id . ']',
                'color'         => 'required|max_length[20]',
            ];

            $messages = [
                'name' => [
                    'required'      => 'Document type name is required.',
                    'max_length'    => 'Document type name cannot exceed 255 characters.',
                ],
                'slug' => [
                    'required'      => 'URL slug is required.',
                    'max_length'    => 'URL slug cannot exceed 100 characters.',
                    'is_unique'     => 'URL slug must be unique.',
                ],
                'color' => [
                    'required'      => 'Color is required.',
                    'max_length'    => 'Color cannot exceed 20 characters.',
                ],
            ];

            $this->validation->setRules($rules, $messages);

            if (!$this->validation->run($formData)) {
                $validationErrors = $this->validation->getErrors();
                throw new ValidationException($validationErrors);
            }

            // Prepare data for update
            $updateData = [
                'name'        => $formData['name'],
                'slug'        => $formData['slug'],
                'color'      => $formData['color'] ?? null,
            ];

            // Skip model validation since we've already validated in the service
            // Update the document type in the database
            $result = $this->documentTypeModel->skipValidation(true)->update($id, $updateData);

            if (!$result) {
                // Get the last error from the model
                $errors = $this->documentTypeModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                log_message('error', 'Document type update failed: ' . $errorMessage);
                throw new \RuntimeException('Failed to update document type in database: ' . $errorMessage);
            }

            // Retrieve the updated document type
            $updatedType = $this->documentTypeModel->where('id', $id)->first();
            
            if (is_object($updatedType)) {
                $updatedType = (array) $updatedType;
            }

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
            }

            return [
                'status'  => 'success',
                'message' => 'Document type updated successfully.',
                'data'    => $updatedType
            ];
        } catch (ValidationException $e) {
            $db->transRollback();
            throw $e;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating document type: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update document type: ' . $e->getMessage());
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
            // Can be single file or array of files
            if (isset($files['resource_file'])) {
                $resourceFiles = $files['resource_file'];
                $allUploadedFiles = [];
                
                // Handle both single file and array of files
                if (is_array($resourceFiles)) {
                    foreach ($resourceFiles as $file) {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $fileUploadResult = $this->processResourceFiles($resourceId, $file);
                            
                            if (!$fileUploadResult['success']) {
                                // Rollback already uploaded files
                                $this->rollbackUploadedFiles($allUploadedFiles);
                                throw new \RuntimeException('File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                            }
                            
                            if (!empty($fileUploadResult['uploaded_files'])) {
                                $allUploadedFiles = array_merge($allUploadedFiles, $fileUploadResult['uploaded_files']);
                            }
                        }
                    }
                } else {
                    // Single file
                    if ($resourceFiles->isValid() && !$resourceFiles->hasMoved()) {
                        $fileUploadResult = $this->processResourceFiles($resourceId, $resourceFiles);
                        
                        if (!$fileUploadResult['success']) {
                            throw new \RuntimeException('File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                        }
                        
                        $allUploadedFiles = $fileUploadResult['uploaded_files'] ?? [];
                    }
                }
                
                $uploadedAttachments = $allUploadedFiles;
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

    public function getContributorsForArticle(string $articleId): array
    {
        try {
            $contributors = $this->db->table('resource_contributors rc')
                ->select('c.id, c.name, c.organization, c.email, rc.role')
                ->join('contributors c', 'c.id = rc.contributor_id')
                ->where('rc.resource_id', $articleId)
                ->get()
                ->getResultArray();
            
            return $contributors ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching contributors for article: ' . $e->getMessage());
            return [];
        }
    }

    public function updatePillarArticle(string $articleId, array $formData, array $files = [], array $filesToDelete = []): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Track uploaded files for potential rollback
        $uploadedFiles = [];
        $uploadedAttachments = [];
        
        try {
            // Find the article
            $article = $this->resourceModel->where('id', $articleId)->first();
            
            if (!$article) {
                throw new \RuntimeException('Article not found');
            }
            
            // Convert to array if object
            if (is_object($article)) {
                if (method_exists($article, 'toArray')) {
                    $article = $article->toArray();
                } else {
                    $article = (array) $article;
                }
            }
            
            // Set validation rules for resource/article update
            $rules = [
                'pillar_id'         => 'required|is_not_unique[pillars.id]',
                'document_type_id'  => 'required|is_not_unique[document_types.id]',
                'category_id'       => 'permit_empty|is_not_unique[resource_categories.id]',
                'title'             => 'required|max_length[255]',
                'slug'              => 'required|max_length[100]|is_unique[resources.slug,id,' . $articleId . ']',
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

            // Handle image upload (featured image) - only if new image is provided
            $imagePath = $article['image_url'] ?? null;
            if (isset($files['image_file']) && $files['image_file']->isValid()) {
                // Delete old image if exists
                if (!empty($article['image_url'])) {
                    $oldImagePath = str_replace(base_url(), '', $article['image_url']);
                    if (file_exists(FCPATH . $oldImagePath)) {
                        @unlink(FCPATH . $oldImagePath);
                    }
                }
                
                $imageUpload = $this->handleResourceImageUpload($files['image_file']);
                if ($imageUpload['data']) {
                    $imagePath      = base_url($imageUpload['data']);
                    $uploadedFiles  = array_merge($uploadedFiles, $imageUpload['files']);
                }
            }

            // Prepare data for update
            $updateData = [
                'pillar_id'         => $validatedData['pillar_id'],
                'category_id'       => $validatedData['category_id'] ?? null,
                'document_type_id'  => $validatedData['document_type_id'],
                'title'             => $validatedData['title'],
                'slug'              => $validatedData['slug'],
                'description'       => $validatedData['description'] ?? null,
                'content'           => $validatedData['content'],
                'image_url'         => $imagePath,
                'publication_year'  => $validatedData['publication_year'] ?? $article['publication_year'] ?? date('Y'),
                'is_featured'       => $validatedData['is_featured'] ?? $article['is_featured'] ?? 0,
                'is_published'      => $validatedData['is_published'] ?? $article['is_published'] ?? 1,
                'meta_title'        => $validatedData['meta_title'] ?? null,
                'meta_description'  => $validatedData['meta_description'] ?? null,
            ];
            
            // Skip model validation since we're handling it in the service
            $this->resourceModel->skipValidation(true);
            
            // Update the resource
            $result = $this->resourceModel->update($articleId, $updateData);
            
            if (!$result) {
                $errors = $this->resourceModel->errors();
                throw new \RuntimeException('Failed to update resource: ' . json_encode($errors));
            }

            // Handle file deletions
            if (!empty($filesToDelete)) {
                foreach ($filesToDelete as $fileId) {
                    $attachment = $this->fileAttachmentModel->find($fileId);
                    if ($attachment) {
                        // Delete physical file
                        if (!empty($attachment['file_path'])) {
                            $filePath = WRITEPATH . $attachment['file_path'];
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                        }
                        // Delete attachment record
                        $this->fileAttachmentModel->delete($fileId);
                    }
                }
            }

            // Handle new resource file attachments
            if (isset($files['resource_file'])) {
                $resourceFiles = $files['resource_file'];
                $allUploadedFiles = [];
                
                // Handle both single file and array of files
                if (is_array($resourceFiles)) {
                    foreach ($resourceFiles as $file) {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $fileUploadResult = $this->processResourceFiles($articleId, $file);
                            
                            if (!$fileUploadResult['success']) {
                                $this->rollbackUploadedFiles($allUploadedFiles);
                                throw new \RuntimeException('File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                            }
                            
                            if (!empty($fileUploadResult['uploaded_files'])) {
                                $allUploadedFiles = array_merge($allUploadedFiles, $fileUploadResult['uploaded_files']);
                            }
                        }
                    }
                } else {
                    // Single file
                    if ($resourceFiles->isValid() && !$resourceFiles->hasMoved()) {
                        $fileUploadResult = $this->processResourceFiles($articleId, $resourceFiles);
                        
                        if (!$fileUploadResult['success']) {
                            throw new \RuntimeException('File upload failed: ' . implode(', ', $fileUploadResult['errors']));
                        }
                        
                        $allUploadedFiles = $fileUploadResult['uploaded_files'] ?? [];
                    }
                }
                
                $uploadedAttachments = $allUploadedFiles;
            }

            // Handle contributors - delete existing and recreate
            $this->resourceContributorModel
                ->where('resource_id', $articleId)
                ->delete();
            
            if (!empty($formData['contributors'])) {
                $this->handleContributors($articleId, $formData['contributors']);
            }

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                $this->rollbackUploadedFiles($uploadedFiles);
                $this->rollbackUploadedFiles($uploadedAttachments);
                throw new \RuntimeException('Database transaction failed');
            }

            // Get the updated resource
            $updatedArticle = $this->resourceModel->where('id', $articleId)->first();
            
            if (is_object($updatedArticle)) {
                if (method_exists($updatedArticle, 'toArray')) {
                    $updatedArticle = $updatedArticle->toArray();
                } else {
                    $updatedArticle = (array) $updatedArticle;
                }
            }
            
            return $updatedArticle ?? $article;

        } catch (\Exception $e) {
            $db->transRollback();
            
            // Clean up any uploaded files if transaction failed
            $this->rollbackUploadedFiles($uploadedFiles);
            $this->rollbackUploadedFiles($uploadedAttachments);
            
            log_message('error', 'Pillar article update failed: ' . $e->getMessage());
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
                    $fullFilePath = $fullUploadPath . '/' . $newName;
                    $uploadedFiles[] = $fullFilePath;
                    
                    // Determine if file is an image
                    $mimeType = $file->getClientMimeType();
                    $isImage = strpos($mimeType, 'image/') === 0;
                    
                    // Save attachment record (let model handle timestamps)
                    $attachmentData = [
                        'attachable_id'     => $resourceId,
                        'attachable_type'   => 'resources',
                        'original_name'     => $file->getClientName(),
                        'file_name'         => $newName,
                        'file_path'         => $uploadPath . '/' . $newName,
                        'file_type'         => $mimeType,
                        'file_size'         => $file->getSize(),
                        'mime_type'         => $mimeType,
                        'is_image'          => $isImage ? 1 : 0
                    ];
                    
                    // Skip validation to avoid conflicts
                    $this->fileAttachmentModel->skipValidation(true);
                    
                    if (!$this->fileAttachmentModel->insert($attachmentData)) {
                        // Rollback the uploaded file
                        @unlink($fullFilePath);
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

    public function removePillarImage(string $id): bool
    {
        try {
            // Find the pillar
            $pillar = $this->pillarModel->where('id', $id)->first();
            
            if (!$pillar) {
                throw new \RuntimeException('Pillar not found');
            }

            // Convert to array if object
            if (is_object($pillar)) {
                $pillar = (array) $pillar;
            }

            $imagePath = $pillar['image_path'] ?? null;

            if (empty($imagePath)) {
                // No image to remove, but still update the database
                $this->pillarModel->update($id, ['image_path' => null]);
                return true;
            }

            // Determine the file path
            $filePath = null;
            
            // If it's a full URL, extract the path
            if (strpos($imagePath, 'http') === 0 || strpos($imagePath, '//') === 0) {
                // Extract path from URL
                $parsedUrl = parse_url($imagePath);
                $filePath = $parsedUrl['path'] ?? null;
                if ($filePath && strpos($filePath, '/') === 0) {
                    $filePath = FCPATH . ltrim($filePath, '/');
                }
            } elseif (strpos($imagePath, '/') === 0) {
                // Absolute path
                $filePath = FCPATH . ltrim($imagePath, '/');
            } else {
                // Relative path
                $filePath = FCPATH . $imagePath;
            }

            // Delete the physical file if it exists
            if ($filePath && file_exists($filePath) && is_file($filePath)) {
                if (unlink($filePath)) {
                    log_message('info', 'Deleted pillar image file: ' . $filePath);
                } else {
                    log_message('warning', 'Failed to delete pillar image file: ' . $filePath);
                }
            } else {
                log_message('info', 'Pillar image file not found (may have been deleted already): ' . $filePath);
            }

            // Update the database to remove the image path
            $updateResult = $this->pillarModel->update($id, ['image_path' => null]);

            if (!$updateResult) {
                throw new \RuntimeException('Failed to update pillar in database');
            }

            log_message('info', 'Pillar image removed successfully - ID: ' . $id);
            return true;

        } catch (\Exception $e) {
            log_message('error', 'Failed to remove pillar image: ' . $e->getMessage());
            throw new \RuntimeException('Failed to remove pillar image: ' . $e->getMessage());
        }
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
