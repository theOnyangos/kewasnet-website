<?php

namespace App\Validation;

class ValidateFullName
{
    // This method checks if both names are provided
    public function validateFullName($str)
    {
        $pattern = '/^[a-zA-Z]+ [a-zA-Z]+$/';
        if (preg_match($pattern, $str)) {
            return true;
        } else {
            return false;
        }
    }
}
