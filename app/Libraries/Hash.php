<?php

namespace App\Libraries;

class Hash {
    
    public function __construct() {
        // Add your initialization code here
    }

    public static function make($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verify($password, $hash) {
        if (password_verify($password, $hash)) {
            return true;
        } else {
            return false;
        }
    }
}

