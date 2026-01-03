<?php

namespace App\Libraries;

class VideoUploader
{

   /**
    * Upload a video file
    *
    * @param object $file
    * @param string $path
    * @return string
    */
   public function upload($file, $path = 'courses')
   {
      $extension = $file->getClientExtension();
      $name = $file->getBasename();

      // Generate a custom name for the file (you can adjust this as needed)
      $fileName = time() . '' . $name . '.' . $extension;

      // Set the path to the uploads folder
      $uploadPath = ROOTPATH . 'public/uploads/' . $path . '/';

      // Check if the file already exists
      if (file_exists($uploadPath . $fileName)) {
         // If the file exists, remove it
         unlink($uploadPath . $fileName);
      }

      // Move the file to the uploads folder
      if ($file->move($uploadPath, $fileName)) {
         // Image path
         $videoPath = base_url() . 'public/uploads/' . $path . '/' . $fileName;

         return $videoPath;
      }

      return false;
   }
}
