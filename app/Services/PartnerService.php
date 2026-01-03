<?php

namespace App\Services;

use App\Exceptions\ValidationException;

class PartnerService
{
    protected $db;
    protected $validation;
    protected $partnerModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->partnerModel = new \App\Models\PartnerModel();
        $this->validation = \Config\Services::validation();
    }

    public function handlePartnerCreation(array $formData, $file): array
    {
        $rules = [
            'partner_name'      => 'required|max_length[255]',
            'website_url'       => 'permit_empty|valid_url',
            'partnership_type'  => 'required|in_list[strategic_partner,implementation_partner,funding_partner,technical_partner]',
        ];

        $messages = [
            'partner_name' => [
                'required' => 'Partner name is required.',
                'max_length' => 'Partner name cannot exceed 255 characters.'
            ],
            'website_url' => [
                'valid_url' => 'Website URL must be a valid URL.'
            ],
            'partnership_type' => [
                'required' => 'Partnership type is required.',
                'in_list' => 'Invalid partnership type selected.'
            ],
        ];

        $this->validation->setRules($rules, $messages);

        if (!$this->validation->run($formData)) {
            throw new ValidationException($this->validation->getErrors());
        }

        $validatedData = $this->validation->getValidated();

        log_message('info', 'Validated data: ' . print_r($validatedData, true));
        log_message('info', 'File object: ' . print_r($file, true));

        try {
            // Start transaction
            $this->db->transStart();

            $filePath = null;
            if ($file !== null && $file->isValid() && !$file->hasMoved()) {
                $uploadedFile = $this->uploadImage($file);

                log_message('info', 'Uploaded file response: ' . print_r($uploadedFile, true));

                if ($uploadedFile['status'] === 'error') {
                    throw new \RuntimeException('Image upload failed: ' . $uploadedFile['message']);
                }

                $filePath = $uploadedFile['data'];
            }

            $insertData = [
                'partner_name'      => $validatedData['partner_name'],
                'partnership_type'  => $validatedData['partnership_type'],
                'partner_url'       => $validatedData['website_url'] ?? null,
            ];

            // Only add partner_logo if a file was uploaded
            if ($filePath) {
                $insertData['partner_logo'] = $filePath;
            }

            log_message('info', 'Inserting partner data: ' . print_r($insertData, true));

            // Create partner
            $partnerId = $this->partnerModel->insert($insertData);

            // Get validation errors if insert failed
            if ($partnerId === false) {
                $errors = $this->partnerModel->errors();
                log_message('error', 'Partner insert failed. Validation errors: ' . print_r($errors, true));
                throw new \RuntimeException('Failed to insert partner record: ' . implode(', ', $errors));
            }

            log_message('info', 'Partner created successfully with ID: ' . $partnerId);

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                if ($filePath) {
                    $this->rollbackImageUpload($filePath);
                }
                throw new \RuntimeException('Database transaction failed');
            }

            return $insertData;
        } catch (\Exception $e) {
            // Rollback transaction on any error
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }

            // Rollback image upload if necessary
            if (isset($filePath)) {
                $this->rollbackImageUpload($filePath);
            }

            throw $e;
        }
    }

    public function handlePartnerUpdate(string $id, array $formData, $file): array
    {
        $rules = [
            'partner_name'      => 'required|max_length[255]',
            'website_url'       => 'permit_empty|valid_url',
            'partnership_type'  => 'required|in_list[strategic_partner,implementation_partner,funding_partner,technical_partner]',
        ];

        $messages = [
            'partner_name' => [
                'required' => 'Partner name is required.',
                'max_length' => 'Partner name cannot exceed 255 characters.'
            ],
            'website_url' => [
                'valid_url' => 'Website URL must be a valid URL.'
            ],
            'partnership_type' => [
                'required' => 'Partnership type is required.',
                'in_list' => 'Invalid partnership type selected.'
            ],
        ];

        $this->validation->setRules($rules, $messages);

        if (!$this->validation->run($formData)) {
            throw new ValidationException($this->validation->getErrors());
        }

        $validatedData = $this->validation->getValidated();

        log_message('info', 'Validated data: ' . print_r($validatedData, true));
        log_message('info', 'File object: ' . print_r($file, true));

        try {
            // Start transaction
            $this->db->transStart();

            // Get existing partner
            $existingPartner = $this->partnerModel->find($id);
            if (!$existingPartner) {
                throw new \RuntimeException('Partner not found');
            }

            $oldLogoPath = $existingPartner['partner_logo'] ?? null;
            $newLogoUploaded = false;

            // Handle logo removal
            if (isset($formData['remove_logo']) && $formData['remove_logo'] == '1') {
                if ($oldLogoPath) {
                    $this->rollbackImageUpload($oldLogoPath);
                }
            }

            // Handle new logo upload
            $newLogoPath = null;
            if ($file !== null && $file->isValid() && !$file->hasMoved()) {
                // Delete old logo if exists and we're uploading a new one
                if ($oldLogoPath) {
                    $this->rollbackImageUpload($oldLogoPath);
                }

                $uploadedFile = $this->uploadImage($file);

                log_message('info', 'Uploaded file response: ' . print_r($uploadedFile, true));

                if ($uploadedFile['status'] === 'error') {
                    throw new \RuntimeException('Image upload failed: ' . $uploadedFile['message']);
                }

                $newLogoPath = $uploadedFile['data'];
                $newLogoUploaded = true;
            }

            $updateData = [
                'partner_name'      => $validatedData['partner_name'],
                'partnership_type'  => $validatedData['partnership_type'],
                'partner_url'       => $validatedData['website_url'] ?? null,
            ];

            // Update partner_logo field
            if (isset($formData['remove_logo']) && $formData['remove_logo'] == '1') {
                // Only set to null if we're not uploading a new logo
                if (!$newLogoUploaded) {
                    $updateData['partner_logo'] = null;
                }
            }

            // If a new logo was uploaded, update the field
            if ($newLogoUploaded) {
                $updateData['partner_logo'] = $newLogoPath;
            }

            log_message('info', 'Updating partner data: ' . print_r($updateData, true));

            // Update partner
            $result = $this->partnerModel->update($id, $updateData);

            if ($result === false) {
                $errors = $this->partnerModel->errors();
                log_message('error', 'Partner update failed. Validation errors: ' . print_r($errors, true));
                throw new \RuntimeException('Failed to update partner record: ' . implode(', ', $errors));
            }

            log_message('info', 'Partner updated successfully with ID: ' . $id);

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                if (isset($uploadedFile) && isset($uploadedFile['data'])) {
                    $this->rollbackImageUpload($uploadedFile['data']);
                }
                throw new \RuntimeException('Database transaction failed');
            }

            return $updateData;
        } catch (\Exception $e) {
            // Rollback transaction on any error
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }

            // Rollback new image upload if necessary
            if (isset($uploadedFile) && isset($uploadedFile['data'])) {
                $this->rollbackImageUpload($uploadedFile['data']);
            }

            throw $e;
        }
    }

    public function handleLogoRemoval(string $id): array
    {
        try {
            // Start transaction
            $this->db->transStart();

            // Get existing partner
            $existingPartner = $this->partnerModel->find($id);
            if (!$existingPartner) {
                throw new \RuntimeException('Partner not found');
            }

            $oldLogoPath = $existingPartner['partner_logo'] ?? null;

            // Delete logo file if exists
            if ($oldLogoPath) {
                $this->rollbackImageUpload($oldLogoPath);
            }

            // Update database to set partner_logo to NULL
            $result = $this->partnerModel->update($id, [
                'partner_logo' => null
            ]);

            if ($result === false) {
                $errors = $this->partnerModel->errors();
                log_message('error', 'Partner logo removal failed. Validation errors: ' . print_r($errors, true));
                throw new \RuntimeException('Failed to remove partner logo: ' . implode(', ', $errors));
            }

            log_message('info', 'Partner logo removed successfully for ID: ' . $id);

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }

            return [
                'id' => $id,
                'partner_logo' => null
            ];
        } catch (\Exception $e) {
            // Rollback transaction on any error
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }

            throw $e;
        }
    }

    public function handlePartnerDeletion(string $id): array
    {
        try {
            // Start transaction
            $this->db->transStart();

            // Get existing partner
            $existingPartner = $this->partnerModel->find($id);
            if (!$existingPartner) {
                throw new \RuntimeException('Partner not found');
            }

            $oldLogoPath = $existingPartner['partner_logo'] ?? null;

            // Delete logo file if exists
            if ($oldLogoPath) {
                $this->rollbackImageUpload($oldLogoPath);
            }

            // Delete partner from database
            $result = $this->partnerModel->delete($id);

            if ($result === false) {
                log_message('error', 'Partner deletion failed for ID: ' . $id);
                throw new \RuntimeException('Failed to delete partner record');
            }

            log_message('info', 'Partner deleted successfully for ID: ' . $id);

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }

            return [
                'id' => $id,
                'deleted' => true
            ];
        } catch (\Exception $e) {
            // Rollback transaction on any error
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }

            throw $e;
        }
    }

    private function rollbackImageUpload(string $fileUrl): void
    {
        // Convert URL path to absolute file path
        // fileUrl format: 'uploads/partners/partner_xxx.jpg'
        $filePath = FCPATH . $fileUrl;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    private function uploadImage($file): array
    {
        if (!$file->isValid()) {
            return [
                'status'  => 'error',
                'message' => 'File upload error: ' . $file->getErrorString(),
                'data'    => null
            ];
        }

        $uploadPath = FCPATH . 'uploads/partners/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $newName = uniqid('partner_') . '.' . $file->getExtension();

        // Move the file
        if ($file->move($uploadPath, $newName)) {
            // Store the relative file path URL
            $fileUrl = 'uploads/partners/' . $newName;

            return [
                'status'  => 'success',
                'message' => 'File uploaded successfully.',
                'data'    => $fileUrl
            ];
        } else {
            return [
                'status'  => 'error',
                'message' => 'File upload failed.',
                'data'    => null
            ];
        }
    }
}