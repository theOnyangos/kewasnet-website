<?php

namespace App\Helpers;

class SlugHelper
{
   public static function createSlug($title)
   {
      helper('url');

      $slug = url_title(strtolower($title), '-', true);
      return $slug;
   }
}
