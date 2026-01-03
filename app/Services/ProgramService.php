<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Program;
use CodeIgniter\Validation\Validation;
use App\Exceptions\ValidationException;

class ProgramService
{
    protected Validation $validation;
    protected Program $programModel;

    public function __construct()
    {
        $this->validation = \Config\Services::validation();
        $this->programModel = new Program();
        helper(['url', 'filesystem']);
    }

    public function createProgram(array $formData, array $files): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $this->validateProgramData($formData);

        $program = $this->prepareProgram($formData);

        // Handle file uploads - check both possible structures
        $imageFile = null;
        if (isset($files['image_url'])) {
            $imageFile = $files['image_url'];
        }
        
        // Log file structure for debugging
        log_message('info', 'Files structure in createProgram: ' . json_encode(array_keys($files)));
        
        if ($imageFile && is_object($imageFile) && $imageFile->isValid() && !$imageFile->hasMoved()) {
            try {
                $program['image_url'] = $this->handleImageUpload($imageFile);
                log_message('info', 'Image uploaded successfully: ' . $program['image_url']);
            } catch (\Exception $e) {
                log_message('error', 'Image upload failed: ' . $e->getMessage());
                // Continue without image
            }
        } else {
            log_message('info', 'No valid image file found');
        }

        // Set default icon if not provided
        if (empty($program['icon_svg'])) {
            $program['icon_svg'] = $this->getDefaultIcon();
        }

        // Generate UUID
        $program['id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();

        // Save the program to the database
        $this->programModel->insert($program);

        // Complete transaction
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
        }

        return $program;
    }

    public function updateProgram(string $id, array $formData, array $files): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Get existing program
        $existingProgram = $this->programModel->find($id);
        if (!$existingProgram) {
            throw new \RuntimeException('Program not found');
        }

        // Convert to array if it's an object
        if (is_object($existingProgram)) {
            $existingProgram = (array) $existingProgram;
        }

        // Validate with modified rules for update (slug uniqueness check should exclude current record)
        $this->validateProgramDataForUpdate($formData, $id);

        $program = $this->prepareProgram($formData);

        // Handle file uploads
        if (!empty($files['image_url']) && $files['image_url']->isValid()) {
            // Delete old image if exists
            if (!empty($existingProgram['image_url'])) {
                $oldImagePath = FCPATH . $existingProgram['image_url'];
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            $program['image_url'] = $this->handleImageUpload($files['image_url']);
        } else {
            // Keep existing image
            $program['image_url'] = $existingProgram['image_url'] ?? null;
        }

        // Use existing icon if not provided
        if (empty($program['icon_svg'])) {
            $program['icon_svg'] = $existingProgram['icon_svg'] ?? $this->getDefaultIcon();
        }

        // Update timestamps
        $program['updated_at'] = date('Y-m-d H:i:s');
        unset($program['created_at']); // Don't update created_at

        // Update the program in the database
        $this->programModel->update($id, $program);

        // Complete transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('Database transaction failed. All changes have been rolled back.');
        }

        // Return updated program
        $program['id'] = $id;
        $program['slug'] = $program['slug'] ?? $existingProgram['slug'];

        return $program;
    }

    protected function validateProgramDataForUpdate(array $data, string $id): void
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]|max_length[500]',
            'content' => 'required|min_length[50]',
            'slug' => "permit_empty|min_length[3]|max_length[255]|is_unique[programs.slug,id,{$id}]",
            'icon_svg' => 'permit_empty',
            'background_color' => 'permit_empty|max_length[7]',
            'sort_order' => 'permit_empty|integer',
            'is_active' => 'permit_empty|in_list[0,1]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'meta_title' => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty|max_length[160]',
            'meta_keywords' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validation->setRules($rules)->run($data)) {
            throw new ValidationException($this->validation->getErrors());
        }
    }

    protected function validateProgramData(array $data): void
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]|max_length[500]',
            'content' => 'required|min_length[50]',
            'slug' => 'permit_empty|min_length[3]|max_length[255]|is_unique[programs.slug]',
            'icon_svg' => 'permit_empty',
            'background_color' => 'permit_empty|max_length[7]',
            'sort_order' => 'permit_empty|integer',
            'is_active' => 'permit_empty|in_list[0,1]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'meta_title' => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty|max_length[160]',
            'meta_keywords' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validation->setRules($rules)->run($data)) {
            throw new ValidationException($this->validation->getErrors());
        }
    }

    protected function prepareProgram(array $formData): array
    {
        $program = [
            'title' => $formData['title'],
            'description' => $formData['description'],
            'content' => $formData['content'],
            'slug' => $this->generateSlug($formData),
            'icon_svg' => $formData['icon_svg'] ?? '',
            'background_color' => $formData['background_color'] ?? 'bg-Secondary',
            'sort_order' => (int)($formData['sort_order'] ?? 0),
            'is_active' => (int)($formData['is_active'] ?? 1),
            'is_featured' => isset($formData['is_featured']) ? 1 : 0,
            'meta_title' => $formData['meta_title'] ?? '',
            'meta_description' => $formData['meta_description'] ?? '',
            'meta_keywords' => $formData['meta_keywords'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $program;
    }

    protected function generateSlug(array $formData): string
    {
        if (!empty($formData['slug'])) {
            return $formData['slug'];
        }

        return url_title($formData['title'], '-', true);
    }

    protected function handleImageUpload($imageFile): string
    {
        if (!$imageFile->isValid() || $imageFile->hasMoved()) {
            throw new \Exception('Invalid image file');
        }

        // Check file size (2MB max)
        if ($imageFile->getSize() > 2048 * 1024) {
            throw new \Exception('Image file is too large. Maximum size is 2MB.');
        }

        // Validate image type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($imageFile->getMimeType(), $allowedTypes)) {
            throw new \Exception('Invalid image type. Only JPEG, PNG, and GIF are allowed.');
        }

        $uploadPath = FCPATH . 'uploads/programs/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $imageFile->getRandomName();
        $imageFile->move($uploadPath, $newName);

        return 'uploads/programs/' . $newName;
    }

    protected function getDefaultIcon(): string
    {
        return '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>';
    }
}