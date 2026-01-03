<?php

namespace App\Models;

use CodeIgniter\Model;

class SmsSetting extends Model
{
    protected $table            = 'sms_settings';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'api_endpoint',
        'api_key',
        'short_code',
        'token',
        'partner_id',
        'created_at',
    ];
}
