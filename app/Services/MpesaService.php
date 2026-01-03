<?php

namespace App\Services;

use App\Models\OnlinePayment;
use App\Models\UserModel;

class MpesaService 
{
    // Check payment successful
    public static function isPaymentSuccess($checkoutRequestID)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('online_payments');

        // Get payment
        $payment = (new OnlinePayment())->where('checkout_request_id', $checkoutRequestID)->first();

        if ($payment) {
            // Get payment status
            $paymentStatus = $payment['customer_message'];

            if ($paymentStatus == 'paid') {
                return true;
            }
        }
        return false;
    }

    public static function updatePaymentDetails($response)
    {
        $ResultCode        = $response["Body"]["stkCallback"]["ResultCode"];
		$merchantRequestID = $response["Body"]["stkCallback"]["MerchantRequestID"];
		$checkoutRequestID = $response["Body"]["stkCallback"]["CheckoutRequestID"];
		$resultDescription = $response["Body"]["stkCallback"]["ResultDesc"];
		$amount            = $response["Body"]["stkCallback"]["CallbackMetadata"]["Item"][0]["Value"];
		$mpesaReciptNumber = $response["Body"]["stkCallback"]["CallbackMetadata"]["Item"][1]["Value"];
		$transactionDate   = $response["Body"]["stkCallback"]["CallbackMetadata"]["Item"][3]["Value"];
		$phoneNumber       = $response["Body"]["stkCallback"]["CallbackMetadata"]["Item"][4]["Value"];

        if ($phone = self::getCustomerPhoneNumber($checkoutRequestID)) {
            if ($ResultCode == 0) {
                $response = self::updateUsersPaymentDetails('paid', $checkoutRequestID, $resultDescription, $transactionDate, $merchantRequestID, $amount, $mpesaReciptNumber, $phoneNumber);
                return $response;
            } else {
                $response = self::updateUsersPaymentDetails('failed', $checkoutRequestID, $resultDescription, $transactionDate);
                return $response;
            }
        } else {
            $response = array(
                'status' => 'failed',
                'Message' => 'Error processing payment',
                'CheckoutRequestID' => $checkoutRequestID
            );
            return $response;
        }
    }

    public static function getCustomerPhoneNumber($checkoutRequestID)
    {
        // Get payment
        $payment = (new OnlinePayment())->where('checkout_request_id', $checkoutRequestID)->first();

        if ($payment) {
            // Get phone number
            $customerPhoneNumber = $payment['phone_number'];
            return $customerPhoneNumber;
        }

        return false;
    }

    // Update users payment details with data from callback
    public static function updateUsersPaymentDetails($payment_status, $checkoutRequestID, $resultDescription, $transactionDate, $merchantRequestID="", $amount="", $mpesaReciptNumber="", $phoneNumber="")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('online_payments');

        if (!empty($merchantRequestID) && !empty($mpesaReciptNumber)) {  
			$payloadData = array(
				'customer_message'    => $payment_status,
				'merchant_request_id' => $merchantRequestID,
				'checkout_request_id' => $checkoutRequestID,
				'result_description'  => $resultDescription,
				'amount'              => $amount,
				'mpesa_recipt_number' => $mpesaReciptNumber,
				'transaction_date'    => $transactionDate // date('Y-m-d H:i:s')
			);

            // Update payment
            $builder->where('checkout_request_id', $checkoutRequestID);
            $builder->update($payloadData);

            $response = array(
			    'status' => 'paid',
			    'Message' => 'Wallet Credited Successfully',
			    'CheckoutRequestID' => $checkoutRequestID
			);
            return $response;
		} else {
			$payloadData = array(
				'customer_message'    => $payment_status,
				'checkout_request_id' => $checkoutRequestID,
				'result_description'  => $resultDescription,
				'amount'              => $amount,
				'transaction_date'    => $transactionDate
			);
			
            // Update payment
            $builder->where('checkout_request_id', $checkoutRequestID);
            $builder->update($payloadData);

            $response = array(
                'status' => 'failed',
                'Message' => 'Error processing payment',
                'CheckoutRequestID' => $checkoutRequestID
            );
            return $response;
		}
    }

    // Checks and updates the wallet balance
    public static function updateWalletBalance($phone, $amount)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('clients');

        // Get client
        $client = (new UserModel())->where('phone', $phone)->first();

        if ($client) {
            // Get current wallet balance
            $currentWalletBalance = $client['wallet_balance'];

            // Calculate new wallet balance
            $newWalletBalance = $currentWalletBalance + $amount;

            // Update wallet balance
            $builder->where('phone', $phone);
            $builder->update([
                'wallet_balance' => $newWalletBalance,
            ]);
            return true;
        }
        return false;
    }

    // Saves confirmation data to database (Payload from safaricom)
    public static function saveConfirmationData($payload)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('offline_payments');

        if (!empty($payload)) {
            $payloadData = array(
                'TransactionType' => $payload['TransactionType'],
                'TransID' => $payload['TransID'],
                'TransTime' => $payload['TransTime'],
                'TransAmount' => $payload['TransAmount'],
                'BusinessShortCode' => $payload['BusinessShortCode'],
                'BillRefNumber' => $payload['BillRefNumber'],
                'InvoiceNumber' => $payload['InvoiceNumber'],
                'OrgAccountBalance' => $payload['OrgAccountBalance'],
                'ThirdPartyTransID' => $payload['ThirdPartyTransID'],
                'MSISDN' => $payload['MSISDN'],
                'FirstName' => $payload['FirstName'],
                'MiddleName' => $payload['MiddleName'],
                'LastName' => $payload['LastName'],
            );
    
            // Save confirmation data
            $builder->insert($payloadData);
    
            // Calculate wallet balance
            self::updateWalletBalance($payload['BillRefNumber'], $payload['TransAmount']);
    
            $resultArray=[
                "ResultDesc"=>"Confirmation Service request accepted successfully",
                "ResultCode"=>"0"
            ];
            
            header('Content-Type: application/json');
            echo json_encode($resultArray);
        } else {
            $resultArray=[
                "ResultDesc"=>"Confirmation Service request not accepted",
                "ResultCode"=>"1"
            ];
            
            header('Content-Type: application/json');
            echo json_encode($resultArray);
        }
    }
}