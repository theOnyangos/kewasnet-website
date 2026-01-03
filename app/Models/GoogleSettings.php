<?php

namespace App\Models;

use CodeIgniter\Model;

class GoogleSettings extends Model
{
    protected $table            = 'google_settings';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'client_id',
        'client_secret',
        'redirect_uri',
        'application_name',
        'created_at',
        'updated_at',
    ];
}
