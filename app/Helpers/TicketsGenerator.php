<?php

namespace App\Helpers;

class TicketsGenerator
{
   public static function ticket($eventId, $prefix = "K", $length = 6)
   {
      $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = "$prefix-";
      $suffixString = "$eventId";
      for ($i = 0; $i < $length; $i++) {
         $randomString .= $characters[rand(0, $charactersLength - 1)];
         if ($i % 3 == 0 && $i != 0) {
            $randomString .= '-';
         }
      }
      return $randomString.$suffixString;
   }
}
