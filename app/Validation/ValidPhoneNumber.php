<?php

namespace App\Validation;

class ValidPhoneNumber
{
    // This method checks if the phone number is valid
    public function valid_phone_number(string $str, string $fields, array $data): bool
    {
        // Check if the phone number is valid
        if (preg_match('/^(?:254|\+254|0)?(7(?:(?:[129876534][0-9])|(?:0[0-8])|(4[0-1]))[0-9]{6})$/', $str)) {
            return true;
        }

        return false;
    }
}
