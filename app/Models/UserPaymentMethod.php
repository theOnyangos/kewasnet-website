<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPaymentMethod extends Model
{
    protected $table            = 'user_payment_methods';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'payment_method_id',
        'account_number',
        'account_name',
        'account_reference',
        'account_phone_number',
        'cvv',
        'expiry_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
