<?php

// application/Libraries/Paystack.php

namespace App\Libraries;

use GuzzleHttp\Client;
use App\Models\PaystackSetting;

class Paystack {
    
    protected $client;

    public function __construct() {
        // Get the Paystack settings from the database
        $paystackSetting = new PaystackSetting();
        $setting = $paystackSetting->where('status', 1)->first();

        $this->client = new Client([
            'base_uri' => $setting['payment_url'] . '/transaction/initialize',
            'headers' => [
                'Authorization' => 'Bearer '.$setting['secret_key'],
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function verifyTransaction($reference) {
        $response = $this->client->get("transaction/verify/{$reference}");
        return json_decode($response->getBody(), true);
    }
}
