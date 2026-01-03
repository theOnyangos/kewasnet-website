<?php

use CodeIgniter\HTTP\Files\UploadedFile;

if (!function_exists('uploadFile')) {
    /**
     * Uploads a file and saves its path to the database.
     *
     * @param UploadedFile $file
     * @param string $uploadPath
     * @return array
     */
    function uploadFile(UploadedFile $file, $uploadPath = 'uploads')
    {
        $publicPath = FCPATH . $uploadPath;

        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($publicPath, $newName);

            $fullFilePath = base_url($uploadPath . '/' . $newName);

            return [
                'status' => 'success',
                'status_code' => 200,
                'message' => 'File uploaded successfully',
                'file_name' => $newName,
                'file_path' => $fullFilePath,
            ];
        }

        return [
            'status' => 'error',
            'status_code' => 400,
            'file_name' => $file->getName(),
            'message' => 'File upload failed',
        ];
    }
}

if (!function_exists('updateFile')) {
    /**
     * Updates an existing file by replacing it with a new one.
     *
     * @param string $currentFilePath
     * @param UploadedFile $newFile
     * @param string $uploadPath
     * @return array
     */
    function updateFile($currentFilePath, UploadedFile $newFile, $uploadPath = 'uploads')
    {
        // Delete the existing file
        $existingFilePath = str_replace(base_url(), FCPATH, $currentFilePath);
        if (file_exists($existingFilePath)) {
            unlink($existingFilePath);
        }

        // Upload the new file
        return uploadFile($newFile, $uploadPath);
    }
}

if (!function_exists('uploadMultipleFiles')) {
    /**
     * Uploads multiple files and returns their paths.
     *
     * @param array $files
     * @param string $uploadPath
     * @return array
     */
    function uploadMultipleFiles(array $files, $uploadPath = 'uploads')
    {
        $results = [];
        foreach ($files as $file) {
            $uploadResult = uploadFile($file, $uploadPath);

            if ($uploadResult['status'] === 'error') :
                // log error message
                log_message("error", "Failed to upload image {$uploadResult['file_name']}. Message: ". $uploadResult['message']);

                // Client Error
                return $uploadResult;
            endif;

            $results[] = $uploadResult['file_path'];
        }
        return $results;
    }
    // Example:
    // $files = $this->request->getFiles();
    // $uploadResults = uploadMultipleFiles($files['files']);
}
