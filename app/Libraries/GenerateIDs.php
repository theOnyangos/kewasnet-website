<?php

namespace App\Libraries;

use App\Models\UserModel;
use App\Models\EventTicket;
use Carbon\Carbon;

class GenerateIDs 
{
    public static function randomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = 'ID';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }

    public static function generateToken(): string
    {
        // Generate a random token using a secure method
        $token = bin2hex(random_bytes(16)); // 32 characters long
        return $token;
    }

    public static function verifyToken($token): string
    {
        // Here you would typically check the token against a database or cache
        // For this example, we'll just return the token if it's valid
        if (strlen($token) === 32) { // Example validation check
            return $token;
        } else {
            throw new \Exception('Invalid token');
        }
    }

    public static function generateEmployeeNumber(): string
    {
        $prefix = 'ID';
        $startingNumber = 10001;

        $userModel = new UserModel();

        try {
            // Fetch the maximum employee number from the database
            $lastUser = $userModel->selectMax('employee_number')
                                ->first();

            if ($lastUser && $lastUser['employee_number']) {
                $lastEmployeeNumber = $lastUser['employee_number'];
                $lastEmployeeNumber = substr($lastEmployeeNumber, strlen($prefix));
                $lastEmployeeNumber = intval($lastEmployeeNumber);
                $newEmployeeNumber = $lastEmployeeNumber + 1;
            } else {
                $newEmployeeNumber = $startingNumber;
            }

            return $prefix . $newEmployeeNumber;
        } catch (\Exception $e) {
            // Handle the exception (log, throw, or return a default value)
            return 'Error generating employee_number'. $e->getMessage();
        }
    }

    public static function generateTicketCode(): string
    {
        $prefix = 'TCKT';
        $startingNumber = 100000001;
        $ticketDate = date('Ymd');

        try {
            $db = \Config\Database::connect();
            
            $query = $db->table('event_tickets')
                        ->select('ticket_code')
                        ->where('ticket_code IS NOT NULL', null, false)
                        ->orderBy('id', 'DESC')
                        ->get();

            $lastTicket = $query->getRow();

            if ($lastTicket) {
                $lastTicketCode = explode('-', $lastTicket->ticket_code);
                $lastTicketCode = $lastTicketCode[1];
                $newTicketCode = $lastTicketCode + 1;
            } else {
                $newTicketCode = $startingNumber;
            }

            return $prefix .'-'. $newTicketCode .'-'. $ticketDate;
        } catch (\Exception $e) {
            return 'Error generating ticket_code'. $e->getMessage();
        }
    }

    public static function generateStrongPassword(): string {
        // Define character sets for password generation
        $uppercaseChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
        $numberChars = '0123456789';
        $specialChars = '!@#$%&;:,.?';

        // Combine character sets
        $allChars = $uppercaseChars . $lowercaseChars . $numberChars . $specialChars;

        // Initialize the password string
        $password = '';

        // Generate the password
        for ($i = 0; $i < 12; $i++) {
            // Randomly select a character from the combined character set
            $randomIndex = rand(0, strlen($allChars) - 1);
            $password .= $allChars[$randomIndex];
        }

        // Check if the generated password meets the validation criteria
        if (!preg_match('@[A-Z]@', $password) || 
            !preg_match('@[a-z]@', $password) || 
            !preg_match('@[0-9]@', $password) || 
            strlen($password) < 8) {
            
            return self::generateStrongPassword();
        }

        return $password;
    }

    function generateJobApplicationReferenceNumber($prefix = 'KJARN_') {
        $randomNumber = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        
        $currentDate = date('dmY');
        
        $referenceNumber = $prefix . $currentDate . $randomNumber;
        
        return $referenceNumber;
    }

}