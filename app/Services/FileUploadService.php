<?php

namespace App\Services;

use App\Helpers\SlugHelper;

class FileUploadService
{
   // This method uploads files
   public function uploadFile($file, $title, $filePath, $pathUrl, $currentImage = null)
   {
        try {
            if (!is_dir($pathUrl)) {
                mkdir($pathUrl, 0755, true);
            }
            
            if ($currentImage != null) {
                $fileName = basename(parse_url($currentImage, PHP_URL_PATH));
                unlink(FCPATH . $filePath . $fileName);
            }

            $extension = $file->getClientExtension();

            $fileName = time() . '-' . SlugHelper::createSlug($title) . '.' . $extension;

            $uploadPath = FCPATH . $filePath;

            $file->move($uploadPath, $fileName);
            
            $filePathToSave = base_url() . $pathUrl . $fileName;

            return $filePathToSave;

        } catch (\Throwable $error) {
            return $error->getMessage();
        }
   }

   // This method uploads multiple files
    public function uploadMultipleFiles($files, $title, $filePath, $pathUrl, $currentImages = null)
    {
          try {
                if (!is_dir($pathUrl)) {
                    mkdir($pathUrl, 0755, true);
                }

                $filePaths = [];
                foreach ($files as $file) {

                 if ($currentImages != null) {
                    $fileName = basename(parse_url($currentImages, PHP_URL_PATH));
                    unlink(FCPATH . $filePath . $fileName);
                 }
    
                 $extension = $file->getClientExtension();
    
                 $fileName = time() . '-' . SlugHelper::createSlug($title) . '.' . $extension;
    
                 $uploadPath = FCPATH . $filePath;
    
                 $file->move($uploadPath, $fileName);
                 
                 $filePathToSave = base_url() . $pathUrl . $fileName;

                 $fileData = [
                    'file_name' => $fileName,
                    'file_path' => $filePathToSave,
                    'file_mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                 ];
    
                 array_push($filePaths, $fileData);
                }
    
                return $filePaths;
    
          } catch (\Throwable $error) {
                return $error->getMessage();
          }
    }

    // This method uploads a video file
    public function uploadVideoFile($file, $title, $filePath, $pathUrl, $currentVideo = null)
    {
        try {
            if (!is_dir($pathUrl)) {
                mkdir($pathUrl, 0755, true);
            }
            
            if ($currentVideo != null) {
                $fileName = basename(parse_url($currentVideo, PHP_URL_PATH));
                unlink(FCPATH . $filePath . $fileName);
            }

            $extension = $file->getClientExtension();

            $fileName = time() . '-' . SlugHelper::createSlug($title) . '.' . $extension;

            $uploadPath = FCPATH . $filePath;

            $file->move($uploadPath, $fileName);
            
            $filePathToSave = base_url() . $pathUrl . $fileName;

            return $filePathToSave;

        } catch (\Throwable $error) {
            return $error->getMessage();
        }
    }
}
