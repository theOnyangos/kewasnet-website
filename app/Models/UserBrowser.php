<?php

namespace App\Models;

use CodeIgniter\Model;

class UserBrowser extends Model
{
    protected $table            = 'user_browsers';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'browser',
        'platform',
        'ip_address',
        'login_type',
        'login_time',
    ];
}
