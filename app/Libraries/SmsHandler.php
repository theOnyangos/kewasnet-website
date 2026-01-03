<?php

namespace App\Libraries;

use App\Models\SmsSetting;
use GuzzleHttp\Client;

class SmsHandler
{
    // Handle sending messages
    public static function send($to, $message)
    {
        $smsSettings = self::getSmsSettings();
        $client = new Client();

        $response = $client->request('POST', $smsSettings['api_endpoint'], [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'mobile' => $to,
                'message' => $message,
                'shortcode' => $smsSettings['short_code'] ?? '',
                'partnerID' => $smsSettings['partner_id'],
                'apikey' => $smsSettings['api_key'],
            ],
        ]);

        $response = json_decode($response->getBody(), true);

        if ($response) {
            return true;
        } else {
            return $response['message'];
        }
    }

    // Get SMS settings from the database
    private static function getSmsSettings()
    {
        $smsSettings = (new SmsSetting())->find(1);
        return $smsSettings;
    }
}
