<?php

namespace App\Services;

use App\Models\SocialLink;
use App\Models\PartnerModel;
use App\Models\YoutubeLinkModel;
use App\Models\Faq;
use CodeIgniter\Validation\Validation;
use App\Exceptions\ValidationException;

class SettingsService
{
    protected $validation;
    protected $socialLinkModel;
    protected $youtubeLinkModel;
    protected $partnerModel;
    protected $faqModel;
    protected $db;

    public function __construct()
    {
        $this->socialLinkModel = new SocialLink();
        $this->youtubeLinkModel = new YoutubeLinkModel();
        $this->partnerModel = new PartnerModel();
        $this->faqModel = new Faq();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
    }

    // ==================== SOCIAL MEDIA METHODS ====================

    /**
     * Get all social media settings
     * 
     * @return array
     */
    public function getSocialMediaSettings(): array
    {
        $socialMediaSettings = $this->socialLinkModel->findAll();

        if (!empty($socialMediaSettings)) {
            return [
                'success' => true,
                'data' => $socialMediaSettings[0]
            ];
        }

        return [
            'success' => false,
            'message' => 'No social media settings found.'
        ];
    }

    /**
     * Save or update social media settings
     * 
     * @param array $data
     * @return array
     */
    public function saveSocialMediaSettings(array $data): array
    {
        try {
            // Validate required fields and data
            if (empty($data)) {
                return [
                    'success' => false,
                    'message' => 'No data provided.'
                ];
            }

            // Validate data before processing
            $validationErrors = $this->validateSocialMediaData($data);
            if (!empty($validationErrors)) {
                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validationErrors
                ];
            }

            // Remove empty values to avoid saving empty strings
            $data = array_filter($data, function($value) {
                return !empty(trim($value));
            });

            // Check if we're updating an existing record
            $socialId = $data['id'] ?? null;
            unset($data['id']); // Remove UUID from data array as it's not an allowed field

            if ($socialId) {
                return $this->updateSocialMediaRecord($socialId, $data);
            } else {
                return $this->createOrUpdateSocialMediaRecord($data);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error saving social media settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while saving social media settings. Please try again.'
            ];
        }
    }

    /**
     * Update existing social media record
     * 
     * @param string $socialId
     * @param array $data
     * @return array
     */
    private function updateSocialMediaRecord(string $socialId, array $data): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $existingRecord = $this->socialLinkModel->find($socialId);
            if (!$existingRecord) {
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Social media settings record not found.'
                ];
            }

            $updateResult = $this->socialLinkModel->update($socialId, $data);
            if ($updateResult === false || $this->socialLinkModel->errors()) {
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Failed to update social media settings.',
                    'errors' => $this->socialLinkModel->errors()
                ];
            }

            // Complete the transaction
            $db->transComplete();
            
            // Check if transaction was successful
            if ($db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Transaction failed while updating social media settings'
                ];
            }

            return [
                'success' => true,
                'message' => 'Social media settings updated successfully.',
                'data' => ['id' => $socialId]
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Social media update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while updating social media settings'
            ];
        }
    }

    /**
     * Create new or update existing social media record
     * 
     * @param array $data
     * @return array
     */
    private function createOrUpdateSocialMediaRecord(array $data): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $existingSettings = $this->socialLinkModel->first();
            
            if ($existingSettings) {
                // Update the existing single record instead of creating a new one
                $updateResult = $this->socialLinkModel->update($existingSettings['id'], $data);
                if ($updateResult === false || $this->socialLinkModel->errors()) {
                    $db->transRollback();
                    return [
                        'success' => false,
                        'message' => 'Failed to update social media settings.',
                        'errors' => $this->socialLinkModel->errors()
                    ];
                }

                // Complete the transaction
                $db->transComplete();
                
                // Check if transaction was successful
                if ($db->transStatus() === false) {
                    return [
                        'success' => false,
                        'message' => 'Transaction failed while updating social media settings'
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Social media settings updated successfully.',
                    'data' => ['id' => $existingSettings['id']]
                ];
            } else {
                // Create the first record
                $insertResult = $this->socialLinkModel->insert($data);
                if ($insertResult === false || $this->socialLinkModel->errors()) {
                    $db->transRollback();
                    return [
                        'success' => false,
                        'message' => 'Failed to create social media settings.',
                        'errors' => $this->socialLinkModel->errors()
                    ];
                }

                $newId = $this->socialLinkModel->insertID();
                
                // Complete the transaction
                $db->transComplete();
                
                // Check if transaction was successful
                if ($db->transStatus() === false) {
                    return [
                        'success' => false,
                        'message' => 'Transaction failed while creating social media settings'
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Social media settings created successfully.',
                    'data' => ['uuid' => $newId]
                ];
            }

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Social media create/update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while saving social media settings'
            ];
        }
    }

    /**
     * Validate social media data
     * 
     * @param array $data
     * @return array
     */
    private function validateSocialMediaData(array $data): array
    {
        $errors = [];
        $urlFields = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'website'];

        // Validate URLs
        foreach ($urlFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                if (!filter_var($data[$field], FILTER_VALIDATE_URL)) {
                    $errors[] = ucfirst($field) . ' must be a valid URL.';
                }
            }
        }

        // Validate WhatsApp phone number
        if (isset($data['whatsapp']) && !empty($data['whatsapp'])) {
            if (!preg_match('/^\+[1-9]\d{1,14}$/', $data['whatsapp'])) {
                $errors[] = 'WhatsApp must be a valid international phone number (e.g., +254700000000).';
            }
        }

        return $errors;
    }

    // ==================== YOUTUBE METHODS ====================

    /**
     * Get YouTube links for DataTables
     * 
     * @return mixed
     */
    public function getYouTubeLinksForDataTables()
    {
        $dataTableService = new \App\Services\DataTableService();
        $columns = ['id', 'title', 'link', 'description', 'created_at'];

        return $dataTableService->handle(
            $this->youtubeLinkModel,
            $columns,
            'getVideoLinks',
            'countAllVideoLinks',
        );
    }

    /**
     * Get Partners for DataTables
     *
     * @return mixed
     */
    public function getPartnersForDataTables()
    {
        $dataTableService = new \App\Services\DataTableService();
        $columns = ['id', 'partner_name', 'partner_logo', 'partner_url', 'partnership_type', 'description', 'created_at'];

        return $dataTableService->handle(
            $this->partnerModel,
            $columns,
            'getPartners',
            'countAllPartners',
        );
    }

    /**
     * Save Partner (create or update)
     * 
     * @param array $data
     * @return array
     */
    public function savePartner(array $data): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        $uploadedLogoPath = null; // Track uploaded file for cleanup on error
        
        try {
            // Get form data
            $partnerName     = trim($data['partner_name'] ?? '');
            $partnershipType = trim($data['partnership_type'] ?? '');
            $partnerUrl      = trim($data['partner_url'] ?? '');
            $description     = trim($data['description'] ?? '');
            $id              = $data['id'] ?? null;

            // Validation
            $validationResult = $this->validatePartnerData([
                'partner_name'     => $partnerName,
                'partnership_type' => $partnershipType,
                'partner_url'      => $partnerUrl
            ]);
            
            if (!$validationResult['isValid']) {
                $db->transRollback();
                return [
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validationResult['errors']
                ];
            }

            $saveData = [
                'partner_name'     => $partnerName,
                'partnership_type' => $partnershipType,
                'partner_url'      => $partnerUrl,
                'description'      => $description
            ];

            // Handle logo upload
            if (isset($data['partner_logo'])) {
                $logoResult = $this->handlePartnerLogoUpload($data['partner_logo'], $id);
                if ($logoResult['status'] === 'error') {
                    $db->transRollback();
                    return $logoResult;
                }
                if ($logoResult['logoPath']) {
                    $uploadedLogoPath = $logoResult['logoPath']; // Track for cleanup
                    $saveData['partner_logo'] = base_url($logoResult['logoPath']);
                }
            }

            if ($id) {
                // Update existing partner
                $updateResult = $this->partnerModel->update($id, $saveData);
                if ($updateResult === false || $this->partnerModel->errors()) {
                    $db->transRollback();
                    // Clean up uploaded file if database update failed
                    if ($uploadedLogoPath) {
                        $this->deletePartnerLogo($uploadedLogoPath);
                    }
                    return [
                        'status'  => 'error',
                        'message' => 'Failed to update partner',
                        'errors'  => $this->partnerModel->errors()
                    ];
                }
                $message = 'Partner updated successfully';
            } else {
                // Create new partner
                $insertResult = $this->partnerModel->insert($saveData);
                if ($insertResult === false || $this->partnerModel->errors()) {
                    $db->transRollback();
                    // Clean up uploaded file if database insert failed
                    if ($uploadedLogoPath) {
                        $this->deletePartnerLogo($uploadedLogoPath);
                    }
                    return [
                        'status' => 'error',
                        'message' => 'Failed to save partner',
                        'errors' => $this->partnerModel->errors()
                    ];
                }
                $message = 'Partner added successfully';
            }

            // Complete the transaction
            $db->transComplete();
            
            // Check if transaction was successful
            if ($db->transStatus() === false) {
                // Clean up uploaded file if transaction failed
                if ($uploadedLogoPath) {
                    $this->deletePartnerLogo($uploadedLogoPath);
                }
                return [
                    'status' => 'error',
                    'message' => 'Transaction failed while saving partner'
                ];
            }

            return [
                'status' => 'success',
                'message' => $message
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            // Clean up uploaded file if any exception occurred
            if ($uploadedLogoPath) {
                $this->deletePartnerLogo($uploadedLogoPath);
            }
            log_message('error', 'Partner save error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while saving the partner'
            ];
        }
    }

    /**
     * Get single Partner by ID
     * 
     * @param int $id
     * @return array
     */
    public function getPartnerById(int $id): array
    {
        $partner = $this->partnerModel->find($id);

        if (!$partner) {
            return [
                'status' => 'error',
                'message' => 'Partner not found'
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'id'               => $partner['id'],
                'partner_name'     => $partner['partner_name'],
                'partnership_type' => $partner['partnership_type'],
                'partner_url'      => $partner['partner_url'],
                'partner_logo'     => $partner['partner_logo'],
                'description'      => $partner['description'],
                'created_at'       => $partner['created_at']
            ]
        ];
    }

    /**
     * Delete Partner
     * 
     * @param int $id
     * @return array
     */
    public function deletePartner(int $id): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Check if partner exists
            $existingPartner = $this->partnerModel->find($id);
            if (!$existingPartner) {
                $db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'Partner not found'
                ];
            }

            // Delete the logo file if it exists
            if (!empty($existingPartner['partner_logo'])) {
                $this->deletePartnerLogo($existingPartner['partner_logo']);
            }

            // Use where clause to ensure safe deletion
            $deleteResult = $this->partnerModel->where('id', $id)->delete();
            
            // Check if deletion was successful
            if ($deleteResult === false || $this->partnerModel->errors()) {
                $db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete partner',
                    'errors' => $this->partnerModel->errors()
                ];
            }

            // Complete the transaction
            $db->transComplete();
            
            // Check if transaction was successful
            if ($db->transStatus() === false) {
                return [
                    'status' => 'error',
                    'message' => 'Transaction failed while deleting partner'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Partner deleted successfully'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Partner delete error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while deleting the partner'
            ];
        }
    }

    /**
     * Validate Partner data
     * 
     * @param array $data
     * @return array
     */
    private function validatePartnerData(array $data): array
    {
        $errors = [];
        $isValid = true;

        // Validate partner name
        if (empty($data['partner_name'])) {
            $errors['partner_name'] = 'Partner name is required';
            $isValid = false;
        } elseif (strlen($data['partner_name']) < 2) {
            $errors['partner_name'] = 'Partner name must be at least 2 characters long';
            $isValid = false;
        }

        // Validate partnership type
        if (empty($data['partnership_type'])) {
            $errors['partnership_type'] = 'Partnership type is required';
            $isValid = false;
        } else {
            $validTypes = ['sponsor', 'strategic_partner', 'supporter', 'donor'];
            if (!in_array($data['partnership_type'], $validTypes)) {
                $errors['partnership_type'] = 'Invalid partnership type';
                $isValid = false;
            }
        }

        // Validate partner URL (if provided)
        if (!empty($data['partner_url'])) {
            if (!filter_var($data['partner_url'], FILTER_VALIDATE_URL)) {
                $errors['partner_url'] = 'Please enter a valid URL';
                $isValid = false;
            }
        }

        return [
            'isValid' => $isValid,
            'errors' => $errors
        ];
    }

    /**
     * Handle Partner Logo Upload
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param int|null $partnerId
     * @return array
     */
    private function handlePartnerLogoUpload($file, $partnerId = null): array
    {
        if (!$file || !$file->isValid()) {
            return ['status' => 'success', 'logoPath' => null];
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return [
                'status' => 'error',
                'message' => 'Invalid file type. Only JPG, PNG, GIF, and SVG files are allowed.'
            ];
        }

        // Validate file size (2MB max)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return [
                'status' => 'error',
                'message' => 'File size too large. Maximum size is 2MB.'
            ];
        }

        try {
            // Create upload directory if it doesn't exist
            $uploadPath = FCPATH . 'uploads/partners/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete old logo if updating
            if ($partnerId) {
                $existingPartner = $this->partnerModel->find($partnerId);
                if ($existingPartner && !empty($existingPartner['partner_logo'])) {
                    $this->deletePartnerLogo($existingPartner['partner_logo']);
                }
            }

            // Generate unique filename
            $extension = $file->getClientExtension();
            $fileName = 'partner_' . uniqid() . '_' . time() . '.' . $extension;
            
            // Move file to upload directory
            if ($file->move($uploadPath, $fileName)) {
                $logoPath = 'uploads/partners/' . $fileName;
                return [
                    'status' => 'success',
                    'logoPath' => $logoPath
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to upload logo file'
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'Partner logo upload error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred during file upload'
            ];
        }
    }

    /**
     * Delete Partner Logo File
     * 
     * @param string $logoPath
     * @return void
     */
    private function deletePartnerLogo(string $logoPath): void
    {
        try {
            $fullPath = FCPATH . $logoPath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete partner logo: ' . $e->getMessage());
        }
    }

    /**
     * Save YouTube link (create or update)
     * 
     * @param array $data
     * @return array
     */
    public function saveYouTubeLink(array $data): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get form data
            $description = trim($data['description'] ?? '');
            $title = trim($data['title'] ?? '');
            $url = trim($data['link'] ?? '');
            $id = $data['id'] ?? null;

            // Validation
            $validationResult = $this->validateYouTubeData(['title' => $title, 'link' => $url]);
            if (!$validationResult['isValid']) {
                $db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validationResult['errors']
                ];
            }

            // Additional YouTube URL validation
            if (!$this->isValidYouTubeUrl($url)) {
                $db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'Please enter a valid YouTube URL'
                ];
            }

            $saveData = [
                'description' => $description,
                'title'       => $title,
                'link'        => $url
            ];

            if ($id) {
                // Update existing link
                $updateResult = $this->youtubeLinkModel->update($id, $saveData);
                if ($updateResult === false || $this->youtubeLinkModel->errors()) {
                    $db->transRollback();
                    return [
                        'status' => 'error',
                        'message' => 'Failed to update YouTube link',
                        'errors' => $this->youtubeLinkModel->errors()
                    ];
                }
                $message = 'YouTube link updated successfully';
            } else {
                // Create new link
                $insertResult = $this->youtubeLinkModel->insert($saveData);
                if ($insertResult === false || $this->youtubeLinkModel->errors()) {
                    $db->transRollback();
                    return [
                        'status' => 'error',
                        'message' => 'Failed to save YouTube link',
                        'errors' => $this->youtubeLinkModel->errors()
                    ];
                }
                $message = 'YouTube link added successfully';
            }

            // Complete the transaction
            $db->transComplete();
            
            // Check if transaction was successful
            if ($db->transStatus() === false) {
                return [
                    'status' => 'error',
                    'message' => 'Transaction failed while saving YouTube link'
                ];
            }

            return [
                'status' => 'success',
                'message' => $message
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'YouTube link save error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while saving the link'
            ];
        }
    }

    /**
     * Get single YouTube link by ID
     * 
     * @param int $id
     * @return array
     */
    public function getYouTubeLinkById(int $id): array
    {
        $link = $this->youtubeLinkModel->find($id);

        if (!$link) {
            return [
                'status' => 'error',
                'message' => 'YouTube link not found'
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'id'          => $link['id'],
                'title'       => $link['title'],
                'url'         => $link['link'], // Map 'link' field to 'url' for frontend
                'description' => $link['description'] ?? '',
                'created_at'  => $link['created_at']
            ]
        ];
    }

    /**
     * Delete YouTube link by ID
     * 
     * @param int $id
     * @return array
     */
    public function deleteYouTubeLink(int $id): array
    {
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Check if link exists
            $existingLink = $this->youtubeLinkModel->find($id);
            if (!$existingLink) {
                $db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'YouTube link not found'
                ];
            }

            // Use where clause to ensure safe deletion
            $deleteResult = $this->youtubeLinkModel->where('id', $id)->delete();
            if ($deleteResult === false || $this->youtubeLinkModel->errors()) {
                $db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete YouTube link',
                    'errors' => $this->youtubeLinkModel->errors()
                ];
            }

            // Complete the transaction
            $db->transComplete();
            
            // Check if transaction was successful
            if ($db->transStatus() === false) {
                return [
                    'status' => 'error',
                    'message' => 'Transaction failed while deleting YouTube link'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'YouTube link deleted successfully'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'YouTube link delete error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while deleting the link'
            ];
        }
    }

    /**
     * Validate YouTube data
     * 
     * @param array $data
     * @return array
     */
    private function validateYouTubeData(array $data): array
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'link'  => 'required|valid_url|max_length[500]'
        ];

        $messages = [
            'title' => [
                'required'   => 'Title is required',
                'max_length' => 'Title cannot exceed 255 characters'
            ],
            'link' => [
                'required'   => 'YouTube URL is required',
                'valid_url'  => 'Please enter a valid YouTube URL',
                'max_length' => 'URL cannot exceed 500 characters'
            ]
        ];

        $this->validation->setRules($rules, $messages);

        if ($this->validation->run($data)) {
            return [
                'isValid' => true,
                'errors'  => []
            ];
        }

        return [
            'isValid' => false,
            'errors' => $this->validation->getErrors()
        ];
    }

    /**
     * Validate YouTube URL format
     * 
     * @param string $url
     * @return bool
     */
    private function isValidYouTubeUrl(string $url): bool
    {
        $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w-]+(&[\w=]*)?$/';
        return preg_match($pattern, $url);
    }

    /**
     * Process Image Upload
     * 
     * 
     */
    protected function processImages(array $files): array
    {
        $images = [];
        $uploadedFiles = [];
        
        if (!empty($files['partner_logo'])) {
            foreach ($files['partner_logo'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    // Generate a new filename
                    $newName = $file->getRandomName();
                    $uploadPath = FCPATH . 'uploads/partner_images';
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
                            'file_name' => $newName,
                            'file_path' => base_url("uploads/partner_images/{$newName}"),
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

    // ==================== FAQ METHODS ====================

    /**
     * Get FAQs for DataTables
     */
    public function getFaqsForDataTables()
    {
        $request = request();
        
        // DataTables parameters
        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? '';
        $orderColumn = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';
        
        $columns = ['question', 'category', 'status', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        // Base query
        $builder = $this->faqModel->builder();
        
        // Search functionality
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('question', $searchValue)
                    ->orLike('answer', $searchValue)
                    ->orLike('category', $searchValue)
                    ->orLike('tags', $searchValue)
                    ->groupEnd();
        }
        
        // Total records count
        $totalRecords = $this->faqModel->countAllResults(false);
        
        // Filtered records count
        $filteredRecords = $builder->countAllResults(false);
        
        // Get paginated data
        $faqs = $builder->orderBy($orderBy, $orderDir)
                       ->limit($length, $start)
                       ->get()
                       ->getResultArray();
        
        return response()->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $faqs
        ]);
    }

    /**
     * Save FAQ (create or update)
     */
    public function saveFaq(array $data): array
    {
        $this->db->transStart();

        try {
            // Extract FAQ data
            $faqData = [
                'question'      => trim($data['question'] ?? ''),
                'answer'        => trim($data['answer'] ?? ''),
                'category'      => trim($data['category'] ?? ''),
                'status'        => trim($data['status'] ?? 'active'),
                'display_order' => (int)($data['display_order'] ?? 0),
                'tags'          => trim($data['tags'] ?? ''),
                'is_featured'   => $data['is_featured'] ?? 0,
                'searchable'    => $data['searchable'] ?? 1
            ];

            // Validate FAQ data
            $validation = $this->validateFaqData($faqData);
            if (!$validation['isValid']) {
                $this->db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation['errors']
                ];
            }

            $isUpdate = !empty($data['id']);

            if ($isUpdate) {
                // Update existing FAQ
                $faqId = $data['id'];
                
                // Check if FAQ exists
                $existingFaq = $this->faqModel->find($faqId);
                if (!$existingFaq) {
                    $this->db->transRollback();
                    return [
                        'status' => 'error',
                        'message' => 'FAQ not found'
                    ];
                }

                // Update FAQ
                if (!$this->faqModel->update($faqId, $faqData)) {
                    $this->db->transRollback();
                    return [
                        'status' => 'error',
                        'message' => 'Failed to update FAQ',
                        'errors' => $this->faqModel->errors()
                    ];
                }

                $message = 'FAQ updated successfully';
            } else {
                // Create new FAQ
                $faqId = $this->faqModel->insert($faqData);
                if (!$faqId) {
                    $this->db->transRollback();
                    return [
                        'status' => 'error',
                        'message' => 'Failed to save FAQ',
                        'errors' => $this->faqModel->errors()
                    ];
                }

                $message = 'FAQ created successfully';
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'status' => 'error',
                    'message' => 'Transaction failed'
                ];
            }

            return [
                'status' => 'success',
                'message' => $message,
                'data' => ['id' => $isUpdate ? $data['id'] : $faqId]
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'FAQ save error: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => 'An error occurred while saving the FAQ'
            ];
        }
    }

    /**
     * Get FAQ by ID
     */
    public function getFaqById($id): array
    {
        try {
            $faq = $this->faqModel->find($id);
            
            if (!$faq) {
                return [
                    'status' => 'error',
                    'message' => 'FAQ not found'
                ];
            }

            return [
                'status' => 'success',
                'data' => $faq
            ];

        } catch (\Exception $e) {
            log_message('error', 'Get FAQ error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while retrieving the FAQ'
            ];
        }
    }

    /**
     * Delete FAQ
     */
    public function deleteFaq($id): array
    {
        $this->db->transStart();
        
        try {
            // Check if FAQ exists
            $existingFaq = $this->faqModel->find($id);
            if (!$existingFaq) {
                $this->db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'FAQ not found'
                ];
            }

            // Use where clause to ensure safe deletion
            $deleteResult = $this->faqModel->where('id', $id)->delete();
            
            if ($deleteResult === false || $this->faqModel->errors()) {
                $this->db->transRollback();
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete FAQ',
                    'errors' => $this->faqModel->errors()
                ];
            }

            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return [
                    'status' => 'error',
                    'message' => 'Transaction failed while deleting FAQ'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'FAQ deleted successfully'
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'FAQ delete error: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => 'An error occurred while deleting the FAQ'
            ];
        }
    }

    /**
     * Validate FAQ data
     */
    private function validateFaqData(array $data): array
    {
        $errors = [];
        $isValid = true;

        // Validate question
        if (empty($data['question'])) {
            $errors['question'] = 'Question is required';
            $isValid = false;
        } elseif (strlen($data['question']) < 10) {
            $errors['question'] = 'Question must be at least 10 characters long';
            $isValid = false;
        } elseif (strlen($data['question']) > 255) {
            $errors['question'] = 'Question cannot exceed 255 characters';
            $isValid = false;
        }

        // Validate answer
        if (empty($data['answer'])) {
            $errors['answer'] = 'Answer is required';
            $isValid = false;
        } elseif (strlen($data['answer']) < 10) {
            $errors['answer'] = 'Answer must be at least 10 characters long';
            $isValid = false;
        }

        // Validate category
        if (empty($data['category'])) {
            $errors['category'] = 'Category is required';
            $isValid = false;
        } else {
            $validCategories = ['general', 'programs', 'membership', 'events', 'donations', 'technical'];
            if (!in_array($data['category'], $validCategories)) {
                $errors['category'] = 'Invalid category selected';
                $isValid = false;
            }
        }

        // Validate status
        if (!empty($data['status'])) {
            $validStatuses = ['active', 'draft', 'archived'];
            if (!in_array($data['status'], $validStatuses)) {
                $errors['status'] = 'Invalid status selected';
                $isValid = false;
            }
        }

        return [
            'isValid' => $isValid,
            'errors' => $errors
        ];
    }
}