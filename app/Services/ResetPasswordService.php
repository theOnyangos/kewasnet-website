<?php

namespace App\Services;

use Carbon\Carbon;
use Config\Services;
use App\Libraries\Hash;
use App\Libraries\Mailer;
use App\Models\UserModel;
use App\Models\PasswordResetToken;
use App\Services\AuthService as EmailService;

class ResetPasswordService
{
    protected static $tokenLength = 6;
    protected static $tokenExpiryMinutes = 30;

    /**
     * Send a password reset email with token
     *
     * @param string $email
     * @return bool
     */
    public static function sendResetPasswordEmail(string $email): bool
    {
        $user = self::getUserByEmail($email);
        if (!$user) {
            return false;
        }

        $resetCode = self::generateResetToken();
        $tokenExpiryDate = Carbon::now()->addMinutes(self::$tokenExpiryMinutes);

        self::storeResetToken($email, $resetCode, $tokenExpiryDate);

        return self::sendResetEmail($user, $resetCode);
    }

    /**
     * Update user password
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function updatePassword(string $email, string $password): bool
    {
        try {
            $user = self::getUserByEmail($email);
            if (!$user) {
                return false;
            }

            return self::updateUserPassword($email, $password);
        } catch (\Exception $e) {
            log_message('error', 'Password update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return array|null
     */
    protected static function getUserByEmail(string $email): ?array
    {
        return (new UserModel())->where('email', $email)->first();
    }

    /**
     * Generate a random reset token
     *
     * @return int
     */
    protected static function generateResetToken(): int
    {
        return random_int(10 ** (self::$tokenLength - 1), (10 ** self::$tokenLength) - 1);
    }

    /**
     * Store reset token in database
     *
     * @param string $email
     * @param int $token
     * @param Carbon $expiryDate
     * @return void
     */
    protected static function storeResetToken(string $email, int $token, Carbon $expiryDate): void
    {
        $passwordResetToken = new PasswordResetToken();

        // Delete any existing tokens for this email
        $passwordResetToken->where('email', $email)->delete();

        // Insert new token
        $passwordResetToken->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiryDate->toDateTimeString(),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Send password reset email
     *
     * @param array $user
     * @param int $resetCode
     * @return bool
     */
    protected static function sendResetEmail(array $user, int $resetCode): bool
    {
        $view = Services::renderer();
        $social = (new EmailService())->getSocialMediaInfo();

        $message = $view->setData([
            'token' => $resetCode,
            'userName' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email'],
            'social_links' => $social
        ])->render('backend/emails/password_reset_template');

        $mailer = new Mailer();
        return $mailer->send(
            $user['email'],
            'Reset your password',
            $message,
            env('EMAIL_FROM_ADDRESS'),
            env('EMAIL_FROM_NAME')
        );
    }

    /**
     * Update user password in database
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    protected static function updateUserPassword(string $email, string $password): bool
    {
        $db = \Config\Database::connect();;
        $builder = $db->table('users');

        if (!$builder) {
            log_message('error', 'Database table "users" not found');
            return false;
        }

        $updateData = [
            'password' => Hash::make($password),
            'updated_at' => Carbon::now()->toDateTimeString()
        ];

        return $builder->where('email', $email)
            ->update($updateData);
    }
}