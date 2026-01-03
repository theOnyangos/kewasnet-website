<?php

namespace App\Libraries;

class GetSalute 
{
    // This method get's salute depending on the time of the day
    public static function salute() {
        $hour = date('H');
        if ($hour >= 0 && $hour < 12) {
            return "Good morning";
        } else if ($hour >= 12 && $hour < 16) {
            return "Good afternoon";
        } else if ($hour >= 16 && $hour < 24) {
            return "Good evening";
        }
    }
}