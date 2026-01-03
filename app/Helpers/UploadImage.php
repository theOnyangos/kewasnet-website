<?php

namespace App\Helpers;

class UploadImage
{
   public static function upload($file, $folder = "blogImages")
   {
      $extension = $file->getClientExtension();
      $name = $file->getBasename();

      // Generate a custom name for the file (you can adjust this as needed)
      $fileName = time() . '' . $name . '.' . $extension;

      // Set the path to the uploads folder
      $uploadPath = ROOTPATH . "public/uploads/$folder/";

      // Check if the file already exists
      if (file_exists($uploadPath . $fileName)) {
         // If the file exists, remove it
         unlink($uploadPath . $fileName);
      }

      // Move the file to the uploads folder
      if ($file->move($uploadPath, $fileName)) {
         // Image path
         $imagePath = base_url() . "uploads/$folder/" . $fileName;

         return $imagePath;
      }

      return false;
   }
}
