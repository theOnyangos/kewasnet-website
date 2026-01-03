<?php

namespace App\Libraries;

use App\Models\Payment;
use App\Models\MpesaSettings;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Libraries\SmsHandler;
use App\Services\MpesaService;
use App\Models\OnlinePayment;

class MpesaApi {

    const registerURLsEndpoint = "https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl";
    const stkPushEndpoint = "https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query";
    const access_token_url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    const transactionDesc = "Adding money to wallet";
    private $confirmationUrl;
    private $validationUrl;
    private $callBackUrl;
    private $mpesaSettings;

    public function __construct()
    {
        $this->mpesaSettings = self::getMpesaSettings();

        $this->confirmationUrl = $this->mpesaSettings['consumer_endpoint'] . "payment/confirmation";
        $this->validationUrl = $this->mpesaSettings['consumer_endpoint'] . "payment/validation";
        $this->callBackUrl = $this->mpesaSettings['consumer_endpoint'] . "callback_response";
    }

    // Receive payments made offline using paybill number and phone number
    public static function offlinePaymentConfirmation($data)
    {
        $response = json_decode($data, true);
        return MpesaService::saveConfirmationData($response);
    }

    // Offline validation from Safaricom API
    public static function offlinePaymentValidation($data)
    {
        $response = json_decode($data, true);

        $phoneNumber = "0".substr($response['BillRefNumber'], -9);

        // Check if phone number is registered in the database
        $clientUser = new Client();
        $client = $clientUser->where('phone_number', $phoneNumber)->first();

        if ($client) {
            // Success Accepted
            return json_encode([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]);
        } else {
            // Failed
            return json_encode([
                'ResultCode' => 'C2B00012',
                'ResultDesc' => 'Rejected'
            ]);
        }
    }

    // Get STK callback after payment has been processed from safaricom
    public static function getSTKCallback($data)
    {
        $callbackData = json_decode($data);

        return MpesaService::updatePaymentDetails($callbackData);
    }

    // Initiate stk push
    public function initiateSTKPush($phone, $amount)
    {
        $mpesaSettings = self::getMpesaSettings();
        $access_token = self::generateAccessToken();
        $url = "https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query";
        $phone = substr_replace($phone, "254", 0, 1);

        $payloadData = array(
            'BusinessShortCode' => $mpesaSettings['business_short_code'],
            'Password' => base64_encode($mpesaSettings['business_short_code'] . $mpesaSettings['pass_key'] . date("YmdHis")),
            'Timestamp' => date("YmdHis"),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $mpesaSettings['business_short_code'],
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callBackUrl,
            'AccountReference' => $mpesaSettings['account_reference'],
            'TransactionDesc' => "Adding money to wallet"
        );

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
            ],
            'json' => $payloadData
        ]);


        $response = json_decode((string) $response->getBody(), true);

        if ($response['ResponseCode'] == 0) {
            $merchantRequestID = $response['MerchantRequestID'];
            $checkoutRequestID = $response['CheckoutRequestID'];
            $customerMessage = $response['CustomerMessage'];

			$paymentData = array(
				'phone_number' => $phone,
				'amount' => $amount,
				'account_reference' => $mpesaSettings['account_reference'],
				'response_description' => $customerMessage,
				'merchant_request_id' => $merchantRequestID,
				'checkout_request_id' => $checkoutRequestID,
				'customer_message' => "Requested",
				'created_at' => Carbon::now(),
			);

            $payment = new OnlinePayment();
            $payment->insert($paymentData);

            return [
                'success' => true,
                'checkoutRequestId' => $response['CheckoutRequestID'],
                'responseCode' => $response['ResponseCode'],
                'message' => $response['Message'],
            ];
        } else {
            return [
                'success' => false,
                'responseCode' => $response['ResponseCode'],
                'message' => $response['Message'],
            ];
        }
    }

    // Get mpesa Access token
    public static function generateAccessToken()
    {
        $mpesaSettings = self::getMpesaSettings();
        $consumer_key = $mpesaSettings['consumer_key'];
        $consumer_secret = $mpesaSettings['consumer_secret'];
        $credentials = base64_encode($consumer_key . ":" . $consumer_secret);
        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ]);

        $access_token = json_decode($response->getBody())->access_token;
        return $access_token;
    }
    
    // Register callback urls
    public function registerUrl()
    {
        $mpesaSettings = self::getMpesaSettings();
        $access_token = self::generateAccessToken();
        $url = self::registerURLsEndpoint;

        $payloadData = array(
			'ShortCode' => $mpesaSettings['mpesa_shortcode'],
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $this->confirmationUrl,
            'ValidationURL' => $this->validationUrl
		);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
            ],
            'json' => $payloadData
        ]);

        return $response->getBody();
    }

    // Get mpesa settings
    public static function getMpesaSettings()
    {
        $mpesaSettings = new MpesaSettings();
        $mpesaSettings = $mpesaSettings->first();

        return $mpesaSettings;
    }

    // initiateB2C
    public function initiateB2C($phone, $amount)
    {
        $mpesaSettings = self::getMpesaSettings();
        $access_token = self::generateAccessToken();
        $url = "https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest";
        $phone = substr_replace($phone, "254", 0, 1);

        $payloadData = array(
            'InitiatorName' => $mpesaSettings['initiator_name'],
            'SecurityCredential' => $mpesaSettings['security_credential'],
            'CommandID' => 'BusinessPayment',
            'Amount' => $amount,
            'PartyA' => $mpesaSettings['business_short_code'],
            'PartyB' => $phone,
            'Remarks' => 'Payment',
            'QueueTimeOutURL' => $mpesaSettings['consumer_endpoint'] . "b2c/timeout",
            'ResultURL' => $mpesaSettings['consumer_endpoint'] . "b2c/result",
            'Occasion' => 'Payment'
        );

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
            ],
            'json' => $payloadData
        ]);

        $response = json_decode((string) $response->getBody(), true);

        if ($response['ResponseCode'] == 0) {
            $merchantRequestID = $response['MerchantRequestID'];
            $checkoutRequestID = $response['CheckoutRequestID'];
            $customerMessage = $response['CustomerMessage'];

            $paymentData = array(
                'phone_number' => $phone,
                'amount' => $amount,
                'account_reference' => $mpesaSettings['account_reference'],
                'response_description' => $customerMessage,
                'merchant_request_id' => $merchantRequestID,
                'checkout_request_id' => $checkoutRequestID,
                'customer_message' => "Requested",
                'created_at' => Carbon::now(),
            );

            $payment = new OnlinePayment();
            $payment->insert($paymentData);

            return [
                'success' => true,
                'checkoutRequestId' => $response['CheckoutRequestID'],
                'responseCode' => $response['ResponseCode'],
                'message' => $response['Message'],
            ];
        } else {
            return [
                'success' => false,
                'responseCode' => $response['ResponseCode'],
                'message' => $response['Message'],
            ];
        }
    }
}


