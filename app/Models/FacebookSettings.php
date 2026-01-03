<?php

namespace App\Models;

use CodeIgniter\Model;

class FacebookSettings extends Model
{
    protected $table            = 'facebook_settings';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'page_id',
        'page_access_token',
        'app_id',
        'app_secret',
        'verification_token',
        'webhook_url',
        'created_at',
        'updated_at'
    ];
}
