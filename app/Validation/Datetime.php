<?php

namespace App\Validation;

class Datetime
{
    public function datetime_check($str)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $str);

        if ($dateTime instanceof \DateTime && $dateTime->format('Y-m-d H:i:s') === $str) {
            return true;
        }

        return false;
    }
}
