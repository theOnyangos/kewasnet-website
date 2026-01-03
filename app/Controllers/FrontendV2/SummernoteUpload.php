<?php

namespace App\Controllers\FrontendV2;

use CodeIgniter\Files\File;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SummernoteUpload extends BaseController
{
    public function uploadImage()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(405)->setJSON([
                'error' => 'Method not allowed'
            ]);

        $file = $this->request->getFile('file');
        
        if (!$file || !$file->isValid()) return $this->response->setStatusCode(400)->setJSON([
                'error' => $file->getErrorString() ?? 'No file uploaded'
            ]);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Only JPG, PNG, GIF, and WebP images are allowed'
            ]);

        // Validate file size (max 2MB)
        if ($file->getSize() > 2097152) return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Image must be less than 2MB'
            ]);

        // Generate a unique filename
        $newName = $file->getRandomName();
        $uploadPath = FCPATH . 'uploads/blog_images';

        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Move the file to the uploads directory
        if (!$file->hasMoved()) {
            $file->move($uploadPath, $newName);
        }

        // Return the URL for Summernote
        return $this->response->setJSON([
            'url' => base_url("uploads/blog_images/{$newName}")
        ]);
    }

    public function deleteImage()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(405)->setJSON([
                'error' => 'Method not allowed'
            ]);

        $imageUrl = $this->request->getPost('src');
        $basePath = base_url('uploads/blog_images/');
        
        if (strpos($imageUrl, $basePath) !== 0) return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Invalid image URL'
            ]);

        $filename = str_replace($basePath, '', $imageUrl);
        $filePath = FCPATH . 'uploads/blog_images/' . $filename;

        // Delete the file if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
            return $this->response->setJSON([
                'success' => true
            ]);
        }

        return $this->response->setStatusCode(404)->setJSON([
            'error' => 'Image not found'
        ]);
    }
}
