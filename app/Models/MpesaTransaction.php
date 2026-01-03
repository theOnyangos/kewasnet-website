<?php

namespace App\Models;

use CodeIgniter\Model;

class MpesaTransaction extends Model
{
    protected $table            = 'mpesa_transactions';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'transaction_date',
        'account_reference',
        'response_description',
        'merchant_request_id',
        'checkout_request_id',
        'customer_message',
        'mpesa_receipt_number',
        'result_description',
        'amount',
        'transaction_type',
        'trans_id',
        'business_short_code',
        'bill_ref_number',
        'org_account_balance',
        'MSISDN',
    ];
}
