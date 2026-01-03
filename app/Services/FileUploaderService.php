<?php

namespace App\Services;

class FileUploaderService
{
    /**
     * Upload job document
     * 
     * @param mixed $file
     * @return string|null
     * @throws RuntimeException
     */
    public function uploadJobDocument($file): ?string
    {
        if (!$file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('Invalid file upload');
        }

        $newName = $file->getRandomName();
        $uploadPath = WRITEPATH . 'uploads/job_documents';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if (!$file->move($uploadPath, $newName)) {
            throw new RuntimeException('Failed to move uploaded file');
        }

        return 'job_documents/' . $newName;
    }

    /**
     * Clean up uploaded file if exists
     * 
     * @param string $filePath
     * @return bool
     */
    public function cleanUpUploadedFile(string $filePath): bool
    {
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}